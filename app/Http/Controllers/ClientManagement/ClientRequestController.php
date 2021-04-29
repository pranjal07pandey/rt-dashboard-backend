<?php

namespace App\Http\Controllers\ClientManagement;

use App\Client;
use App\ClientRequest;
use App\Company;
use App\Email_Client;
use App\Services\ClientService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ClientRequestController extends Controller
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
        $company        =   Company::with('clientRequest','unapprovedClientRequest','emailClients')->findOrFail(Session::get('company_id'));
        $clients        =   $clientService->clients($company);
        return view('dashboard.company.clientManagement.clientRequest.index',compact('company','clients'));
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function unapproved(ClientService $clientService){
        $company        =   Company::with('clientRequest','unapprovedClientRequest.requestedCompanyInfo','emailClients')->findOrFail(Session::get('company_id'));
        $clients        =   $clientService->clients($company);

        return view('dashboard.company.clientManagement.unapprovedClient.index',compact('company','clients'));
    }

    public function action($action, $id){
        $clientRequest  =   ClientRequest::findOrFail($id);
        if($clientRequest->company_id!=Session::get('company_id') && $clientRequest->requested_company_id!=Session::get('company_id')){
            flash('Invalid action ! Please try with valid action.','warning');
            return redirect()->route('clients.request');
        }

        switch ($action){
            case 'cancel':
                flash('Client request has been removed successfully.','success');
                break;
            case 'reject':
                flash('Client request has been rejected successfully.','success');
                break;
            case 'accept':
                if($clientRequest->requested_company_id!=Session::get('company_id')){
                    flash('Invalid action ! Please try with valid action.','warning');
                    return redirect()->route('clients.request');
                }
                $client                         =   new Client();
                $client->user_id                =   $clientRequest->user_id;
                $client->company_id             =   $clientRequest->company_id;
                $client->requested_company_id   =   $clientRequest->requested_company_id;
                $client->accepted_user_id       =   Auth::user()->id;
                $client->save();

                flash('"'. $clientRequest->companyInfo->name.'" client request accepted.','success');
                break;

            default:
                flash('Invalid action ! Please try with valid action.','warning');
                break;
        }
        $clientRequest->delete();
        return redirect()->route('clients.request');
    }
}
