<?php

namespace App\Http\Controllers\ClientManagement;

use App\Company;
use App\Email_Client;
use App\EmailUser;
use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use overint\MailgunValidator;

class EmailClientController extends Controller
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ClientService $clientService)
    {
        $company        =   Company::with('clientRequest','unapprovedClientRequest','emailClients.emailUser')->findOrFail(Session::get('company_id'));
        $clients        =   $clientService->clients($company);

        return view('dashboard.company.clientManagement.email-clients.index', compact('company','clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, ClientService $clientService)
    {
        $this->validate($request,['email'   => 'required|email','full_name'=>'required']);
        $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
        if ($validator->validate($request->email) == false){
            flash('Invalid Email address.', 'warning');
            return redirect()->back();
        }

        $emailUser = EmailUser::where('email', $request->email)->get();
        if($emailUser->count()==0) {
            $emailUser = new EmailUser();
            $emailUser->email = $request->email;
            $emailUser->name = "";
            $emailUser->company_name = "";
            $emailUser->save();
        }else{ $emailUser   =   $emailUser->first(); }

        $company        =   Company::findOrFail(Session::get('company_id'));
        $emailClient    =    $clientService->emailClient($company, $emailUser);
        if($clientService->emailClient($company, $emailUser)!=null){
            flash($emailUser->email.' is already added on your Custom Clients as user '.$emailClient->full_name , 'warning');
            return redirect()->back();
        }

        $emailClient                    =   new Email_Client();
        $emailClient->full_name         =   $request->full_name;
        $emailClient->company_name      =   ($request->company_name=="")?"":$request->company_name;
        $emailClient->company_address   =   ($request->company_address=="")?"":$request->company_address;
        $emailClient->company_id        =   $company->id;
        $emailClient->email_user_id     =   $emailUser->id;
//        $emailClient->syn_user          =   0;
        $emailClient->save();

        flash('Email client added successfully.', 'success');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $this->validate($request,['id'   => 'required','full_name'=>'required']);

        $emailClient    =  Email_Client::findOrFail($request->id);
        if($emailClient->company_id!=Session::get('company_id')){
            flash('Invalid Action.','warning');
        }

        $emailClient->full_name         =   $request->full_name;
        $emailClient->company_name      =   ($request->company_name == "")? " " :$request->company_name;
        $emailClient->company_address   =   ($request->company_address == "")? " " :$request->company_address;
        $emailClient->save();

        flash('Client info updated successfully', 'success');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request,['id'   => 'required']);
        $emailClient   =    Email_Client::findOrFail($request->id);
        if($emailClient->company_id!=Session::get('company_id')){
            flash('Invalid Action.','warning');
        }else{
            flash($emailClient->emailUser->email.' deleted successfully.','success');
            $emailClient->delete();
        }
        return redirect()->back();
    }
}
