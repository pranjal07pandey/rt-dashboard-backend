<?php

namespace App\Http\Controllers\ClientManagement;

use App\Client;
use App\ClientRequest;
use App\Company;
use App\Services\ClientService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Helpers\V2\AmazoneBucket;

class ClientManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $companyId =    getCompanyId();
            Session::put('company_id',$companyId);
            if(!checkProfileComplete()){
                return redirect()->route('companyProfile');
            }
            return $next($request);
        });
    }

    public function index(ClientService $clientService){
        $company        =   Company::with('clientRequest','unapprovedClientRequest','emailClients')->findOrFail(Session::get('company_id'));
        $clients        =   $clientService->clients($company);
        return view('dashboard.company.clientManagement.index', compact('company','clients'));
    }

    public function search($key){
        if(strlen(str_replace(' ', '', $key))>0) {
            $array = array();
            $query = Company::where('id', '!=', Session::get('company_id'))->where(function ($query) use ($key) {
                return $query->where('abn', 'like', '%' . $key . '%')
                    ->orWhere('name', 'like', '%' . $key . '%')
                    ->orWhere('address', 'like', '%' . $key . '%');
            })->get();

            echo "<ul>";
            foreach ($query as $row):
                if(AmazoneBucket::fileExist($row->logo)) $logo   =   AmazoneBucket::url() . $row->logo;
                else $logo   =   "https://dummyimage.com/100x100/f0f0f0/999999.jpg&text=Company+Logo";

                echo '<li>
                            <div class="companyProfileImage pull-left">
                                <img src="'.$logo.'">
                            </div>
                            <div class="companyDetails pull-left">
                                <strong>' . $row->name . '</strong><br/>
                                <span> ABN: ' . $row->abn . '</span><br/>
                                <span> Location: ' . $row->address . '</span>
                            </div>';

                $is_client  =   Client::where(function($query) use ($row) {
                    return $query->where('company_id',$row->id)->where('requested_company_id',Session::get('company_id'));
                })->orWhere(function($query) use ($row){
                    return $query->where('company_id',Session::get('company_id'))->where('requested_company_id',$row->id);
                })->count();

                $is_requested_client = ClientRequest::where(function($query) use ($row) {
                    return $query->where('company_id',Session::get('company_id'))->where('requested_company_id',$row->id);
                })->count();

                if($is_client){
                    echo '<button type="button" class="pull-right btn btn-secondary btn-sm btn-raised" ><i class="fa fa-plus"></i> ADD CLIENT</button>';
                }else{
                    if ($is_requested_client){
                        echo '<button class="pull-right btn btn-secondary btn-sm btn-raised" ><i class="fa fa-check"></i> REQUESTED</button>';
                    }else{
                        echo '<button class="pull-right btn btn-success  btn-sm btn-raised clientRequested" userId="'.$row->id.'" id="userRequested'.$row->id.'" ><i class="fa fa-plus"></i> ADD CLIENT</button>';
                    }
                }
                echo '<div class="clearfix"></div></li>';
            endforeach;
            echo "</ul>";
        }
    }

    public function request(Request $request){
        $id = $request->id;
        $is_client  =   Client::where(function($query) use ($id) {
            return $query->where('company_id',$id)->where('requested_company_id',Session::get('company_id'));
        })->orWhere(function($query) use ($id){
            return $query->where('company_id',Session::get('company_id'))->where('requested_company_id',$id);
        })->count();

        if($is_client==0):
            $is_requested  =   ClientRequest::where(function($query) use ($id) {
                return $query->where('company_id',$id)->where('requested_company_id',Session::get('company_id'));
            })->orWhere(function($query) use ($id){
                return $query->where('company_id',Session::get('company_id'))->where('requested_company_id',$id);
            })->count();
            if($is_requested==0):
                $client_request                         =    new ClientRequest();
                $client_request->user_id                =   Auth::user()->id;
                $client_request->company_id             =   Session::get('company_id');
                $client_request->requested_company_id   =   $id;
                $client_request->status                 =   0;
                $client_request->save();

                $receiverCompanyInfo = Company::where('id', $id)->first();
                $userInfo = User::where('id',$receiverCompanyInfo->user_id)->first();

                if ($userInfo->deviceToken != ""){
                    if ($userInfo->device_type == 2)
                        sendiOSNotification($userInfo->deviceToken, 'Client Request', Company::where('id',Session::get('company_id'))->first()->name." has made a client request, please visit the backend to approve or disapprove", array());

                    if ($userInfo->device_type == 1)
                        sendAndroidNotification($userInfo->deviceToken, "Client Request", Company::where('id',Session::get('company_id'))->first()->name." has made a client request, please visit the backend to approve or disapprove", array());
                }

                // Sending email
                $data['receiveremail'] = $userInfo->email;
                $data['senderCompanyName'] = Company::where('id',Session::get('company_id'))->first()->name;
                Mail::send('emails.clientrequest.index', $data, function ($message) use ($userInfo) {
                    $message->from("info@recordtimeapp.com.au", "Record Time");
                    $message->to($userInfo->email)->subject("Client Request");
                });
                return response()->json(['status'=>true ,'message'=>'Client request sent successfully.','id'=>'userRequested'.$id]);
            else :
                return response()->json(['status'=>false ,'message'=>'Client request already requested.']);
            endif;
        else :
            return response()->json(['status'=>false ,'message'=>'Already added in your client list!']);
        endif;
        return redirect()->back();
    }
    public function destroy(Request $request){
        $client     =    Client::with('requestedCompanyInfo','companyInfo')->where('id',$request->id)->firstOrFail();
        if(Session::get('company_id')==$client->company_id || Session::get('company_id')==$client->requested_company_id){
            if(Session::get('company_id')==$client->company_id){ $clientName = $client->requestedCompanyInfo->name;}
            else{ $clientName     =  $client->companyInfo->name;}
            $client->delete();

            flash($clientName.' removed from your client list.','success');
            return redirect()->route('clientManagement.index');
        } else{
            flash('Invalid Request!','warning');
            return redirect()->route('clientManagement.index');
        }
    }
}
