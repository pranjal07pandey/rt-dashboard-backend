<?php

namespace App\Http\Controllers\InvoiceManager;

use App\Client;
use App\Company;
use App\DocketLabel;
use App\EmailSentInvoice;
use App\Folder;
use App\Invoice;
use App\Invoice_Label;
use App\SentInvoice;
use App\Services\ClientService;
use App\Services\CompanyService;
use App\Support\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class InvoicesController extends Controller
{
    public function allInvoices(Request $request, CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('userInfo','invoiceLabels','docketLabels','employees.userInfo')->findOrfail(Session::get('company_id'));
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $invoices           =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();

        $searchKey = Input::get('search');
        if($request->items == ""){ $items= 10; }
        else{ $items = $request->items; }

        if(Input::get('data')){
            $allSentInvoices    =   $companyService->allSentInvoices($company)->where('folder_status',0);
            $emailSentInvoices    =   $companyService->emailSentInvoices($company)->where('folder_status',0);
            $merged= $allSentInvoices->concat($emailSentInvoices);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchAllInvoice',compact('searchKey','items','result'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.allInvoice',compact('company','clients','invoices','companyFolder','searchKey','items','result'));
            }
        }
        if(Input::get('search')) {
            $possibleSentInvoices   =   $companyService->allSentInvoices($company)->where('folder_status',0);
            $possibleEmailInvoices  =   $companyService->emailSentInvoices($company)->where('folder_status',0);
            $filteredInvoices       =   $this->searchSentInvoices($searchKey, $possibleSentInvoices);
            $filteredEmailInvoices  =   $this->searchEmailSentInvoices($searchKey, $possibleEmailInvoices);

            $merged         =   array_merge($filteredInvoices,$filteredEmailInvoices);
            $result         =   (new Collection($merged))->sortByDesc('created_at')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchAllInvoice',compact('searchKey','items','result'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.allInvoice',compact('company','clients','invoices','companyFolder','searchKey','items','result'));
            }
        }

        $allSentInvoices    =   $companyService->allSentInvoices($company)->where('folder_status',0);
        $emailSentInvoices    =   $companyService->emailSentInvoices($company)->where('folder_status',0);
        $merged= $allSentInvoices->concat($emailSentInvoices);
        $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

        return view('dashboard.company.invoiceManager.invoices.allInvoice',compact('company','invoices','clients','items','result','companyFolder'));
    }

    public function sentInvoices(Request $request, CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('userInfo','invoiceLabels','docketLabels','employees.userInfo')->findOrfail(Session::get('company_id'));
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $invoices           =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();

        $searchKey = Input::get('search');
        if($request->items == ""){ $items= 10; }
        else{ $items = $request->items; }

        if(Input::get('data')){
            $sentInvoice    =   $companyService->sentInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);
            if($request->ajax()){
                return view('dashboard.company.invoiceManager.invoices.searchSentInvoice',compact('sentInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.index',compact('company', 'sentInvoice','invoices','clients','searchKey','items','companyFolder'));
            }
        }
        if(Input::get('search')) {
            $possibleSentInvoices   =   $companyService->sentInvoices($company)->where('folder_status',0);
            $filteredInvoices       =   $this->searchSentInvoices($searchKey, $possibleSentInvoices);

            $sentInvoice    =   (new Collection($filteredInvoices))->sortByDesc('created_at')->paginate($items);
            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchSentInvoice',compact('sentInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.index',compact('company','sentInvoice','invoices','clients','searchKey','items','companyFolder'));
            }
        }

        $sentInvoice    =   $companyService->sentInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);

        return view('dashboard.company.invoiceManager.invoices.index',compact('company','sentInvoice','invoices','clients','items','companyFolder'));
    }

    public function receivedInvoices(Request $request, CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('userInfo','invoiceLabels','docketLabels','employees.userInfo')->findOrfail(Session::get('company_id'));
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $invoices           =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();

        $searchKey = Input::get('search');
        if($request->items == ""){ $items= 10; }
        else{ $items = $request->items; }

        if(Input::get('data')){
            $receivedInvoice  =   $companyService->receivedInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchReceivedInvoice',compact('receivedInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.receivedInvoices',compact('company','receivedInvoice','invoices','clients','searchKey','items','companyFolder'));
            }
        }

        if(Input::get('search')) {
            $possibleSentInvoices   =   $companyService->receivedInvoices($company)->where('folder_status',0);
            $filteredInvoices       =   $this->searchSentInvoices($searchKey, $possibleSentInvoices);
            $receivedInvoice    =   (new Collection($filteredInvoices))->sortByDesc('created_at')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchReceivedInvoice',compact('receivedInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.receivedInvoices',compact('company','receivedInvoice','invoices','clients','searchKey','items','companyFolder'));
            }
        }

        $receivedInvoice  =   $companyService->receivedInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);
        return view('dashboard.company.invoiceManager.invoices.receivedInvoices',compact('company', 'receivedInvoice','invoices','clients','items','companyFolder'));
    }

    public function emailedInvoices(Request $request,CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('userInfo','invoiceLabels','docketLabels','employees.userInfo')->findOrfail(Session::get('company_id'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();

        $searchKey = Input::get('search');
        if($request->items == ""){ $items= 10; }
        else{ $items = $request->items; }

        if(Input::get('data')){
            $emailedInvoice    =   $companyService->emailSentInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);
            if($request->ajax()) {
                return view('dashboard.company.invoiceManager.invoices.searchEmailInvoice',compact('emailedInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.emailedInvoices',compact('emailedInvoice','searchKey','items', 'totalInvoiceLabel','companyFolder','folder','sentDocketLabel'));
            }
        }

        if(Input::get('search')) {
            $possibleEmailInvoices  =   $companyService->emailSentInvoices($company)->where('folder_status',0);
            $filteredEmailInvoices  =   $this->searchEmailSentInvoices($searchKey, $possibleEmailInvoices);
            $emailedInvoice = (new Collection($filteredEmailInvoices))->sortByDesc('created_at')->paginate($items);
            if($request->ajax()){
                return view('dashboard.company.invoiceManager.invoices.searchEmailInvoice',compact('emailedInvoice','searchKey','items'));
            }else{
                return view('dashboard.company.invoiceManager.invoices.emailedInvoices',compact('company','emailedInvoice','searchKey','items','companyFolder'));
            }
        }
        $emailedInvoice    =   $companyService->emailSentInvoices($company)->where('folder_status',0)->sortByDesc('created_at')->paginate($items);

        return view('dashboard.company.invoiceManager.invoices.emailedInvoices',compact('company','emailedInvoice','items','companyFolder', 'searchKey'));
    }

    public function searchSentInvoices($searchKey, $possibleSentInvoices){
        $filteredInvoices   =   array();
        foreach ($possibleSentInvoices as $sentInvoice){
            if(preg_match("/" . $searchKey . "/i", $sentInvoice->company_invoice_id)){ $filteredInvoices[] = $sentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$sentInvoice->invoiceInfo->title)){ $filteredInvoices[] = $sentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",Carbon::parse($sentInvoice->created_at)->format('d-M-Y'))){ $filteredInvoices[] = $sentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$sentInvoice->sender_name)){ $filteredInvoices[] = $sentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$sentInvoice->sender_name)){ $filteredInvoices[] = $sentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$sentInvoice->company_name)){ $filteredInvoices[] = $sentInvoice; continue; }

            $receiverName   =   $sentInvoice->receiverUserInfo->first_name." ".$sentInvoice->receiverUserInfo->last_name;
            $receiverCompanyName  =   $sentInvoice->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                $filteredInvoices[] = $sentInvoice; continue;
            }
        }
        return $filteredInvoices;
    }

    public function searchEmailSentInvoices($searchKey, $possibleEmailInvoices){
        $filteredEmailInvoices  =   array();
        foreach ($possibleEmailInvoices as $emailSentInvoice){
            if(preg_match("/" . $searchKey . "/i", $emailSentInvoice->company_invoice_id)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",Carbon::parse($emailSentInvoice->created_at)->format('d-M-Y'))){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",Carbon::parse($emailSentInvoice->created_at)->format('d M'))){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",Carbon::parse($emailSentInvoice->created_at)->format('F'))){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->template_title)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }

            if(preg_match("/".$searchKey."/i",$emailSentInvoice->sender_name)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->company_name)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->company_address)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }

            if(preg_match("/".$searchKey."/i",$emailSentInvoice->receiverInfo->email)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->receiver_full_name)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->receiver_company_name)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }
            if(preg_match("/".$searchKey."/i",$emailSentInvoice->receiver_company_address)){ $filteredEmailInvoices[] = $emailSentInvoice; continue; }

            if ($emailSentInvoice->invoiceDescription){
                foreach ($emailSentInvoice->invoiceDescription as $invoiceDescription){
                    if(preg_match("/".$searchKey."/i",$invoiceDescription->description)){ $filteredEmailInvoices[] = $emailSentInvoice; break; }
                    if(preg_match("/".$searchKey."/i",$invoiceDescription->amount)){ $filteredEmailInvoices[] = $emailSentInvoice; break; }
                }
            }
        }
        return $filteredEmailInvoices;
    }
}
