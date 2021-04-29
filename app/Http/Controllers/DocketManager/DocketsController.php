<?php

namespace App\Http\Controllers\DocketManager;

use App\Client;
use App\Company;
use App\Docket;
use App\DocketDraft;
use App\DocketField;
use App\DocketFieldGridLabel;
use App\DocketFieldGridValue;
use App\DocketLabel;
use App\EmailSentDocket;
use App\EmailSentDocketImageValue;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketTallyUnitRateVal;
use App\EmailSentDocManualTimer;
use App\EmailSentDocManualTimerBrk;
use App\EmailSnetDocketUnitRateValue;
use App\Employee;
use App\Folder;
use App\FolderItem;
use App\Invoice_Label;
use App\SendDocketImageValue;
use App\SentDcoketTimerAttachment;
use App\SentDocketAttachment;
use App\SentDocketInvoiceDetail;
use App\SentDocketLabel;
use App\SentDocketManualTimer;
use App\SentDocketManualTimerBreak;
use App\SentDocketRecipientApproval;
use App\SentDocketReject;
use App\SentDocketsValue;
use App\EmailSentDocketValue;
use App\SentDocketRecipient;
use App\SentDockets;
use App\SentDocketTallyUnitRateVal;
use App\SentDocketTimesheet;
use App\SentDocketUnitRateValue;
use App\SentDocValYesNoValue;
use App\SentEmailAttachment;
use App\SentEmailDocketLabel;
use App\SentEmailDocValYesNoValue;
use App\Services\ClientService;
use App\Services\CompanyService;
use App\Support\Collection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\V2\Api\ApiService;
use App\Services\V2\Api\DocketService;
use App\Services\V2\Api\UserService;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use App\Helpers\V2\MessageDisplay;
use App\Http\Controllers\APIController;
use App\User;
use App\DocketDraftsAssign;

class DocketsController extends Controller
{
    protected $docketService,$apiService,$userService;
    public function __construct(DocketService $docketService,ApiService $apiService,UserService $userService){
        $this->docketService = $docketService;
        $this->apiService = $apiService;
        $this->userService = $userService;
    }
    public function allDockets(Request $request, CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));

        $dockets            =   $company->dockets()->orderBy('id','desc')->get();
        $sentInvoiceLabel   =   $company->invoiceLabels()->orderBy('id','desc')->get();
        $searchKey = "";

        if ($request->items == ""){$items= 10;}
        else{ $items = $request->items; }

        if (Input::get('data')){
            $searchKey= Input::get('search');

            $allSentDockets     =   $companyService->allSentDockets($company)->where('folder_status',0);
            $emailSentDocket    =   $companyService->emailSentDockets($company)->where('folder_status',0);

            $merged= $allSentDockets->concat($emailSentDocket);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchAllDockets',compact('company','dockets','clients','searchKey','items','result','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.dockets.all',compact('company','dockets','clients','searchKey','items','result','companyFolder','sentInvoiceLabel'));
            }
        }

        if (Input::get('search')){
            $searchKey= Input::get('search');

            $allSentDockets     =   $companyService->allSentDockets($company)->where('folder_status',0);
            $emailSentDocket    =   $companyService->emailSentDockets($company)->where('folder_status',0);

            $possibleSentDocketsID  =   $allSentDockets->pluck('id')->toArray();
            $possibleEmailSentDocketsID =   $emailSentDocket->pluck('id')->toArray();
            $matchedIDArray     =   array();
            $matchIDArrayEmail  = array();

            $matchedIDArray     =   SentDockets::where('formatted_id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
            $matchIDArrayEmail  =   EmailSentDocket::where('formatted_id','like','%'.$searchKey.'%')->whereIn('id',$possibleEmailSentDocketsID)->pluck('id')->toArray();
            if(count($matchedIDArray)>0)
                $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));

            if(count($matchIDArrayEmail)>0)
                $possibleEmailSentDocketsID  =   array_merge(array_diff($possibleEmailSentDocketsID,$matchIDArrayEmail),array_diff($matchIDArrayEmail,$possibleEmailSentDocketsID));

            $docketQuery        =    SentDockets::with('recipientInfo')->whereIn('id',$possibleSentDocketsID)->get();
            $emailDocketQuery   =    EmailSentDocket::whereIn('id',$possibleEmailSentDocketsID)->with('senderUserInfo','senderCompanyInfo','docketInfo','sentDocketValue.docketFieldInfo')->get();

            $filteredSentDocIDs =   $this->searchSentDocket($searchKey, $docketQuery);
            $matchedIDArray         =   array_merge($matchedIDArray, $filteredSentDocIDs);

            $filteredEmailSentIDs   =   $this->searchEmailDocket($searchKey, $emailDocketQuery);
            $matchIDArrayEmail =   array_merge($matchIDArrayEmail,$filteredEmailSentIDs);

            $sentDockets    =   SentDockets::whereIn('id',$matchedIDArray)->with('senderUserInfo','senderCompanyInfo','recipientInfo.userInfo','docketInfo','sentDocketValue.docketFieldInfo')->orderBy('created_at','desc')->get();
            $docketsEmail    =   EmailSentDocket::whereIn('id',$matchIDArrayEmail)->with('senderUserInfo','senderCompanyInfo','docketInfo','sentDocketValue.docketFieldInfo')->orderBy('created_at','desc')->get();

            $merged= $sentDockets->concat($docketsEmail);
            $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);
            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchAllDockets',compact('company','sentDockets','searchKey','items','result','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.dockets.all',compact('company','sentDockets','dockets','clients','searchKey','items','result','companyFolder','sentInvoiceLabel'));
            }
        }

        $allSentDockets     =   $companyService->allSentDockets($company)->where('folder_status',0);
        $emailSentDocket    =   $companyService->emailSentDockets($company)->where('folder_status',0);

        $merged= $allSentDockets->concat($emailSentDocket);
        $result = (new Collection($merged))->sortByDesc('created_at')->paginate($items);

        return view('dashboard.company.docketManager.dockets.all',compact('company','dockets','clients','items','result','companyFolder','sentInvoiceLabel'));
    }

    public function sentDockets(Request $request,  CompanyService $companyService,ClientService $clientService){
        $company            =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        $dockets            =   $company->dockets()->orderBy('id','desc')->get();
        $sentInvoiceLabel   =   $company->invoiceLabels()->orderBy('id','desc')->get();

        if($request->items == ""){ $items= 10;}
        else{ $items = $request->items; }

        if(Input::get('data')){
            $searchKey = Input::get('search');
            $sentDockets    =   $companyService->sentDockets($company)->where('folder_status',0)->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchSentDocket',compact('company','sentDockets','searchKey','items','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.docket',compact('company','sentDockets','dockets','clients','searchKey','items','companyFolder','sentInvoiceLabel'));
            }
        }
        if(Input::get('search')) {
            $searchKey = Input::get('search');
            $possibleSentDocketsID    =   SentDockets::select('id')->where('sender_company_id',Session::get('company_id'))->pluck('id')->toArray();

            //check docket id
            $matchedIDArray =   SentDockets::where('formatted_id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
            if(count($matchedIDArray)>0){
                $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
            }

            //check docket info(sender name, sender company name , receiver name, company name //
            $sentDocketQuery    =    SentDockets::whereIn('id',$possibleSentDocketsID)->get();
            $filteredSentDocIDs =   $this->searchSentDocket($searchKey, $sentDocketQuery);
            $matchedIDArray         =   array_merge($matchedIDArray, $filteredSentDocIDs);
            $sentDockets    =   SentDockets::whereIn('id',$matchedIDArray)->where('folder_status',0)->orderBy('created_at','desc')->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchSentDocket',compact('company','sentDockets','searchKey','items','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.docket',compact('company','sentDockets','dockets','clients','searchKey','items','companyFolder','sentInvoiceLabel'));
            }
        }


        $sentDockets    =   $companyService->sentDockets($company)->where('folder_status',0)->paginate($items);
        return view('dashboard.company.docketManager.docket',compact('company','sentDockets','dockets','clients','items','companyFolder','sentInvoiceLabel'));
    }

    public function receivedDockets(Request $request, CompanyService $companyService,ClientService $clientService){
        $company            =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $sentInvoiceLabel   =   Invoice_Label::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->get();
        $dockets            =   $company->dockets()->orderBy('id','desc')->get();
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));

        if($request->items == ""){ $items= 10;}
        else{ $items = $request->items; }

        if (Input::get('data')){
            $searchKey = Input::get('search');
            $sentDockets        =   $companyService->receivedDockets($company)->where('folder_status',0)->paginate($items);

            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchReceviedDocket',compact('company','sentDockets','searchKey','items','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.dockets.received',compact('company','sentDockets','dockets','clients','totalCompany','searchKey','items','companyFolder','sentInvoiceLabel'));
            }
        }

        if (Input::get('search')){
            $searchKey = Input::get('search');
            $possibleSentDocketsID        =   $companyService->receivedDockets($company)->pluck('id')->toArray();

            $filteredSentDockets    =   array();
            $matchedIDArray     =   array();

            //check docket id
            $matchedIDArray =   SentDockets::where('formatted_id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
            if(count($matchedIDArray)>0){
                $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
            }

            $receiverDocketQuery    =    SentDockets::whereIn('id',$possibleSentDocketsID)->get();
            $filteredSentDocIDs =   $this->searchSentDocket($searchKey, $receiverDocketQuery);
            $matchedIDArray         =   array_merge($matchedIDArray, $filteredSentDocIDs);

            $sentDockets    =   SentDockets::whereIn('id',$matchedIDArray)->where('folder_status',0)->orderBy('created_at','desc')->paginate($items);
            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchReceviedDocket',compact('sentDockets','searchKey','items','companyFolder','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.dockets.received',compact('sentDockets','dockets','clients','totalCompany','searchKey','items','companyFolder','sentInvoiceLabel'));
            }
        }

        $sentDockets        =   $companyService->receivedDockets($company)->where('folder_status',0)->paginate($items);

        return view('dashboard.company.docketManager.dockets.received',compact('company','companyFolder','sentDockets','dockets','clients','items','sentInvoiceLabel'));
    }

    public function emailedDockets(Request $request, CompanyService $companyService, ClientService $clientService){
        $company            =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $sentInvoiceLabel   =   $company->invoiceLabels()->orderBy('id','desc')->get();
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));

        if($request->items == ""){ $items= 10;}
        else{ $items = $request->items; }

        if (Input::get('data')){
            $searchKey = Input::get('search');
            $dockets    =   $companyService->emailSentDockets($company)->where('folder_status',0)->paginate($items);
            $docketusedbyemail =  EmailSentDocket::with('docketInfo')->select('docket_id')->whereIn('id',$companyService->emailSentDockets($company)->pluck('id')->toArray())->groupBy('docket_id')->get();
            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchEmailedDocket',compact('company','clients','dockets','companyFolder', 'searchKey','docketusedbyemail','items','clients','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.docket.emailedDockets',compact('company','clients', 'dockets','companyFolder','totalCompany','docketsName','sentDockets','docket','docketusedbyemail','employes','company','searchKey','items','sentInvoiceLabel'));
            }
        }

        if(Input::get('search')) {
            $searchKey = Input::get('search');
            $possibleSentDocketsID    =   EmailSentDocket::where('company_id',Session::get('company_id'))->orderBy('created_at','desc')->pluck('id')->toArray();

            //check docket id
            $matchedIDArray =   EmailSentDocket::where('formatted_id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
            if(count($matchedIDArray)>0){
                $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
            }

            //check docket info(sender name, sender company name , receiver name, company name //
            $sentDocketQuery    =    EmailSentDocket::whereIn('id',$possibleSentDocketsID)->get();

            $filteredEmailSentIDs   =   $this->searchEmailDocket($searchKey, $sentDocketQuery);
            $matchedIDArray =   array_merge($matchedIDArray,$filteredEmailSentIDs);

            $dockets    =   EmailSentDocket::whereIn('id',$matchedIDArray)->orderBy('created_at','desc')->where('folder_status',0)->paginate($items);
            $docketusedbyemail =  EmailSentDocket::with('docketInfo')->select('docket_id')->whereIn('id',$matchedIDArray)->groupBy('docket_id')->get();

            if($request->ajax()) {
                return view('dashboard.company.docketManager.docket.searchEmailedDocket',compact('searchKey','dockets','items','companyFolder','clients','sentInvoiceLabel'));
            }else{
                return view('dashboard.company.docketManager.docket.emailedDockets',compact('dockets','docketusedbyemail','company','searchKey','items','companyFolder','sentInvoiceLabel'));
            }
        }

        $dockets    =   $companyService->emailSentDockets($company)->where('folder_status',0)->paginate($items);
        $docketusedbyemail =  EmailSentDocket::with('docketInfo')->select('docket_id')->whereIn('id',$companyService->emailSentDockets($company)->pluck('id')->toArray())->groupBy('docket_id')->get();

        return view('dashboard.company.docketManager.docket.emailedDockets',compact('company','dockets','docketusedbyemail','companyFolder','sentInvoiceLabel', 'items'));
    }

    public function searchSentDocket($searchKey, $sentDockets){
        $matchedIDArray  =   array();
        foreach ($sentDockets as $row) {
            if(preg_match("/" . $searchKey . "/i", $row->sender_name) || preg_match("/" . $searchKey . "/i", $row->company_name)) { $matchedIDArray[] = $row->id; continue; }
            if(preg_match("/" . $searchKey . "/i", $row->template_title)){ $matchedIDArray[] = $row->id; continue; }
            if(preg_match("/" . $searchKey . "/i", Carbon::parse($row->created_at)->format('d-M-Y'))) { $matchedIDArray[] = $row->id; continue; }
            if(preg_match("/" . $searchKey . "/i", $row->formatted_id)) { $matchedIDArray[] = $row->id; continue; }

            if ($row->recipientInfo) {
                foreach ($row->recipientInfo as $recipient):
                    $receiversName = @$recipient->userInfo->first_name . " " . @$recipient->userInfo->last_name;
                    if (preg_match("/" . $searchKey . "/i", $receiversName)) {
                        $matchedIDArray[] = $row->id;
                        break;
                    }

                    $companyName = "";
                    $employeeQuery = Employee::where('user_id', $recipient->user_id)->get();
                    if($employeeQuery->count() > 0){ $companyName = $employeeQuery->first()->companyInfo->name;}
                    else{
                        $companyQuery = Company::where('user_id', $recipient->user_id)->get();
                        if ($companyQuery->count() > 0)
                            $companyName = $companyQuery->first()->name;
                    }
                    if (preg_match("/" . $searchKey . "/i", $companyName)) {
                        $matchedIDArray[] = $row->id;
                        break;
                    }
                endforeach;
            }

            //  for docket field value
            if($row->sentDocketValue) {
                foreach ($row->sentDocketValue as $rowValue) {
                    if (@$rowValue->docketFieldInfo->docket_field_category_id != 5 && @$rowValue->docketFieldInfo->docket_field_category_id != 7 && @$rowValue->docketFieldInfo->docket_field_category_id != 8 && @$rowValue->docketFieldInfo->docket_field_category_id != 9 &&
                        @$rowValue->docketFieldInfo->docket_field_category_id != 12 && @$rowValue->docketFieldInfo->docket_field_category_id != 13 && @$rowValue->docketFieldInfo->docket_field_category_id != 14 && @$rowValue->docketFieldInfo->docket_field_category_id != 22
                    ) {
                        if (preg_match("/" . $searchKey . "/i", $rowValue->value)) {
                            $matchedIDArray[] = $row->id;
                            break;
                        }
                    }
                }
            }
        }
        return $matchedIDArray;
    }
    public function searchEmailDocket($searchKey, $emailSentDockets){
        $matchIDArrayEmail  =   array();
        foreach($emailSentDockets as $row){
            if((preg_match("/".$searchKey."/i",$row->sender_name) || preg_match("/".$searchKey."/i",$row->company_name))){ $matchIDArrayEmail[]   =   $row->id; continue; }
            if(preg_match("/".$searchKey."/i",$row->template_title)){ $matchIDArrayEmail[]   =   $row->id; continue; }
            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) { $matchIDArrayEmail[]   =   $row->id; continue; }
            if(preg_match('/('.$searchKey.')/',  $row->formatted_id)){  $matchIDArrayEmail[]   =   $row->id; continue; }

            //for receivers Email Company name Company address Company full name
            foreach($row->recipientInfo as $recipient){
                if(preg_match("/".$searchKey."/i",$recipient->emailUserInfo->email)){ $matchIDArrayEmail[]   =   $row->id; break; }
                if(preg_match("/".$searchKey."/i",$recipient->receiver_full_name)){ $matchIDArrayEmail[]   =   $row->id; break; }
                if(preg_match("/".$searchKey."/i",$recipient->receiver_company_name)){ $matchIDArrayEmail[]   =   $row->id; break; }
                if (preg_match("/".$searchKey."/i",$recipient->receiver_company_address)){ $matchIDArrayEmail[]   =   $row->id; break; }
            }

            //for docket field value
            if($row->sentDocketValue){
                foreach ($row->sentDocketValue as $rowValue){
                    if(@$rowValue->docketFieldInfo->docket_field_category_id!=5 && @$rowValue->docketFieldInfo->docket_field_category_id!=7 && @$rowValue->docketFieldInfo->docket_field_category_id!=8 && @$rowValue->docketFieldInfo->docket_field_category_id!=9 &&
                        @$rowValue->docketFieldInfo->docket_field_category_id!=12 && @$rowValue->docketFieldInfo->docket_field_category_id!=13 && @$rowValue->docketFieldInfo->docket_field_category_id!=14 && @$rowValue->docketFieldInfo->docket_field_category_id!=22){
                        if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                            $matchIDArrayEmail[]   =   $row->id;
                            break;
                        }
                    }
                }
            }
        }
        return $matchIDArrayEmail;
    }

    public function filterDocket(Request $request, CompanyService $companyService, ClientService $clientService){
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $docketTemplate = null;
        $company    =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $clients    =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        if($request->items == ""){$items= 10;}
        else{ $items = $request->items; }


        $sentDocketsQuery =  SentDockets::query();
        $sentEmailDocketsQuery =  EmailSentDocket::query();
        if($request->invoiceable){
            $sentDocketsQuery->where('invoiceable',$request->invoiceable);
            $sentEmailDocketsQuery->where('invoiceable',$request->invoiceable);
        }
        if($request->docketTemplateId){
            $sentDocketsQuery->where('docket_id',$request->docketTemplateId);
            $sentEmailDocketsQuery->where('docket_id',$request->docketTemplateId);
        }
        if($request->docketFieldValue){

            foreach ($request->docketFieldValue as $key => $requestData){
                $docketData = DocketField::where('id',$key)->first()->toArray();
                $filterArray[] = array(
                    'docket_field'=>$docketData,
                    'value'=>$requestData
                );

            }

            $matchId =array();
            $emailMatchArray = array();
            foreach ($request->docketFieldValue as $fieldId=>$fieldValues){
                if($fieldValues != null){
                    foreach ($sentDocketsQuery->get() as $ite){
                        foreach ($ite->sentDocketValue as $sentDV){
                            if($sentDV->docket_field_id == $fieldId){
                                if($sentDV->docketFieldInfo->docket_field_category_id ==7){
                                    if($sentDV->sentDocketUnitRateValue){
                                        foreach($sentDV->sentDocketUnitRateValue as $sentDURValue){
                                            if(preg_match("/{$fieldValues}/i", $sentDURValue->value)){
                                                $matchId[] = @$sentDURValue->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }elseif($sentDV->docketFieldInfo->docket_field_category_id == 23){
                                    if($sentDV->sentDocketTallyableUnitRateValue){
                                        foreach($sentDV->sentDocketTallyableUnitRateValue as $sentDTURValue){
                                            if(preg_match("/{$fieldValues}/i", $sentDTURValue->value)){
                                                $matchId[] = @$sentDTURValue->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }elseif ($sentDV->docketFieldInfo->docket_field_category_id == 18){
                                    if($sentDV->SentDocValYesNoValueInfo){
                                        foreach($sentDV->SentDocValYesNoValueInfo as $SentDVYNValueInfo){
                                            if(preg_match("/{$fieldValues}/i", $SentDVYNValueInfo->value)){
                                                $matchId[] = @$SentDVYNValueInfo->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }elseif ($sentDV->docketFieldInfo->docket_field_category_id == 20){
                                    if($sentDV->sentDocketManualTimer){
                                        foreach($sentDV->sentDocketManualTimer as $sentDMTimer){
                                            if(preg_match("/{$fieldValues}/i", $sentDMTimer->value)){
                                                $matchId[] = @$sentDMTimer->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    if($sentDV->sentDocketManualTimerBreak){
                                        foreach($sentDV->sentDocketManualTimerBreak as $sentDMTBreak){
                                            if( preg_match("/{$fieldValues}/i", $sentDMTBreak->reason)){
                                                $matchId[] = @$sentDMTBreak->sentDocketValue->sent_docket_id;
                                            }
                                            if( preg_match("/{$fieldValues}/i", $sentDMTBreak->value)){
                                                $matchId[] = @$sentDMTBreak->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }elseif ($sentDV->docketFieldInfo->docket_field_category_id  == 22){
                                    if($sentDV->sentDocketFieldGridValues){
                                        foreach($sentDV->sentDocketFieldGridValues as $sentDFGValues){
                                            if( preg_match("/{$fieldValues}/i", $sentDFGValues->value)){
                                                $matchId[] = @$sentDFGValues->docket_id;
                                            }
                                        }
                                    }
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }else{
                                    $matchId[] = @$sentDV->where('sent_docket_id',$ite->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->sent_docket_id;
                                }
                            }


                        }
                    }
                    foreach ($sentEmailDocketsQuery->get() as $row){
                        foreach ($row->sentDocketValue as $sentEDValue){
                            if($sentEDValue->docket_field_id == $fieldId){

                                if($sentEDValue->docketFieldInfo->docket_field_category_id ==7){
                                    if($sentEDValue->sentDocketUnitRateValue){
                                        foreach($sentEDValue->sentDocketUnitRateValue as $sentDURValue){
                                            if(preg_match("/{$fieldValues}/i", $sentDURValue->value)){
                                                $emailMatchArray[] = @$sentDURValue->sentDocketValue->sent_docket_id;
                                            }
                                        }
                                    }
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;

                                }elseif($sentEDValue->docketFieldInfo->docket_field_category_id == 23){
                                    if($sentEDValue->sentDocketTallyableUnitRateValue){
                                        foreach($sentEDValue->sentDocketTallyableUnitRateValue as $sentDTURValue){
                                            if(preg_match("/{$fieldValues}/i", $sentDTURValue->value)){
                                                $emailMatchArray[] = @$sentDTURValue->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;
                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id == 18){

                                    if($sentEDValue->SentEmailDocValYesNoValueInfo){
                                        foreach($sentEDValue->SentEmailDocValYesNoValueInfo as $SentEDVYNVInfo){
                                            if(preg_match("/{$fieldValues}/i", $SentEDVYNVInfo->value)){
                                                $emailMatchArray[] = @$SentEDVYNVInfo->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;
                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id == 20){
                                    if($sentEDValue->emailSentDocManualTimer){
                                        foreach($sentEDValue->emailSentDocManualTimer as $emailSDMTimer){
                                            if(preg_match("/{$fieldValues}/i", $emailSDMTimer->value)){
                                                $emailMatchArray[] = @$emailSDMTimer->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }

                                    if($sentEDValue->emailSentDocManualTimerBrk){
                                        foreach($sentEDValue->emailSentDocManualTimerBrk as $emailSDMTBrk){
                                            if( preg_match("/{$fieldValues}/i", $emailSDMTBrk->reason)){
                                                $emailMatchArray[] = @$emailSDMTBrk->sentDocketValue->email_sent_docket_id;
                                            }
                                            if( preg_match("/{$fieldValues}/i", $emailSDMTBrk->value)){
                                                $emailMatchArray[] = @$emailSDMTBrk->sentDocketValue->email_sent_docket_id;
                                            }
                                        }
                                    }
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;

                                }elseif ($sentEDValue->docketFieldInfo->docket_field_category_id  == 22){

                                    if($sentEDValue->sentDocketFieldGridValues){
                                        foreach($sentEDValue->sentDocketFieldGridValues as $sentDFGValues){
                                            if( preg_match("/{$fieldValues}/i", $sentDFGValues->value)){
                                                $emailMatchArray[] = @$sentDFGValues->docket_id;
                                            }
                                        }
                                    }

                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;
                                }
                                else{
                                    $emailMatchArray[] = @$sentEDValue->where('email_sent_docket_id',$row->id)->where('docket_field_id',$fieldId)->where('value','like', '%' . $fieldValues . '%')->first()->email_sent_docket_id;
                                }
                            }
                        }
                    }
                }
            }
            if(count($matchId) != 0){
                $sentDocketsQuery->whereIn('id',array_unique($matchId));
            }
            if(count($emailMatchArray) != 0){
                $sentEmailDocketsQuery->whereIn('id',$emailMatchArray);
            }


        }


        if($request->docketId){
            $sentDocketsQuery->where('formatted_id','like', '%' . $request->docketId . '%');
            $sentEmailDocketsQuery->where('formatted_id','like', '%' . $request->docketId . '%');

        }
        if($request->date){
            if($request->date==1){
                if($request->from){
                    $sentDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                    $sentEmailDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                }

                if($request->to){
                    $sentDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                    $sentEmailDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                }
            }
        }

        if($request->type=="sent"){
            $possibleSentDockets    =   $companyService->sentDockets($company)->pluck('id')->toArray();
            $emailSentDocketsIds    =   array(0);
            if($request->company){
                $companyInput               =   Company::find($request->company);
                $totalCompanyEmployeeID     =   $companyInput->getAllCompanyUserIds();

                if($request->employee){ $totalCompanyEmployeeID  =    array((int)$request->employee); }

                if($request->company==Session::get('company_id')){
                    $possibleSentDockets    =    SentDockets::whereIn('id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->get()->pluck('id')->toArray();
                }
                else{
                    $possibleSentDockets    =    SentDocketRecipient::whereIn('sent_docket_id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('sent_docket_id')->toArray();
                }

            }

            //check docket filed date value
            if($request->date) {
                if ($request->date == 2) {
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                try{
                                    Carbon::parse($rowValue->value);
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }catch(\Exception $e) {
                                    break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);


                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);

                    }
                }
            }
            $sentDocketsQuery->whereIn('id',$possibleSentDockets);
            $sentEmailDocketsQuery->whereIn('id',$emailSentDocketsIds);
        }
        elseif($request->type=="received"){
            $possibleSentDockets    =   $companyService->receivedDockets($company)->pluck('id')->toArray();
            $emailSentDocketsIds    =   array(0);

            if($request->company){
                $companyInput               =   Company::find($request->company);
                $totalCompanyEmployeeID     =   $companyInput->getAllCompanyUserIds();

                if($request->employee){ $totalCompanyEmployeeID  =    array((int)$request->employee); }

                $possibleSentDockets    =    SentDocketRecipient::whereIn('sent_docket_id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('sent_docket_id')->toArray();
                $sentDocketsQuery = SentDockets::whereIn('id', $possibleSentDockets);
            }

            //check docket filed date value
            if($request->date) {
                if ($request->date == 2) {
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                try{
                                    Carbon::parse($rowValue->value);
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }catch(\Exception $e) {
                                    break;
                                }

                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);


                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);

                    }
                }
            }
            $sentEmailDocketsQuery->whereIn('id',$emailSentDocketsIds);

        }
        elseif($request->type=="all"){
            $possibleSentDockets    =   $companyService->allSentDockets($company)->pluck('id')->toArray();
            $emailSentDocketsIds    =   $companyService->emailSentDockets($company)->pluck('id')->toArray();

            if($request->company){
                $companyInput               =   Company::find($request->company);
                $totalCompanyEmployeeID     =   $companyInput->getAllCompanyUserIds();

                if($request->employee){ $totalCompanyEmployeeID  =    array((int)$request->employee); }

                if($request->company==Session::get('company_id')){
                    if($request->employee){
                        $companyEmailSentDocketId   =   EmailSentDocket::where('user_id',$request->employee)->pluck('id')->toArray();
                        $emailSentDocketsIds        =   $companyEmailSentDocketId;
                    }
                }else{ $emailSentDocketsIds    =   array(0); }

                $companySentDocketId           =    SentDockets::whereIn('id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('id')->toArray();
                $companySentDocketRecipient    =    SentDocketRecipient::whereIn('sent_docket_id',$possibleSentDockets)->whereIn('user_id',$totalCompanyEmployeeID)->pluck('sent_docket_id')->toArray();
                $possibleSentDockets            =   array_unique(array_merge($companySentDocketId,$companySentDocketRecipient));
            }

            if($request->date) {
                if ($request->date == 2) {
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();

                        foreach (SentDockets::whereIn('id',$possibleSentDockets)->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if($rowValue->value!="N/a"){
                                    try{
                                        Carbon::parse($rowValue->value);
                                        if ($rowValue->value != "" && $rowValue->value != "null") {
                                            if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                                $flag = true;
                                        }
                                        if ($flag == true)
                                            break;
                                    }catch(\Exception $e) {
                                        break;
                                    }
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }


                        unset($possibleSentDockets);
                        $possibleSentDockets = $tempSentDocket;



                        unset($tempSentEmailDocket);
                        $tempSentEmailDocket = array();

                        foreach (EmailSentDocket::whereIn('id',$emailSentDocketsIds)->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if($rowValue->value!="N/a"){
                                    try{
                                        Carbon::parse($rowValue->value);
                                        if ($rowValue->value != "" && $rowValue->value != "null") {
                                            if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                                $flag = true;
                                        }
                                        if ($flag == true)
                                            break;
                                    }catch(\Exception $e) {
                                        break;
                                    }
                                }
                            }

                            if ($flag == true) {
                                $tempSentEmailDocket[] = $row->id;
                            }
                        }


                        unset($emailSentDocketsIds);
                        $emailSentDocketsIds = $tempSentEmailDocket;
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);
                        unset($tempSentDocket);
                        $tempSentDocket = array();

                        foreach (SentDockets::whereIn('id',$possibleSentDockets)->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if($rowValue->value!="N/a"){
                                    if ($rowValue->value != "" && $rowValue->value != "null") {

                                        if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($possibleSentDockets);
                        $possibleSentDockets = $tempSentDocket;

                        unset($tempSentEmailDocket);
                        $tempSentEmailDocket = array();

                        foreach (EmailSentDocket::whereIn('id',$emailSentDocketsIds)->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if($rowValue->value!="N/a"){
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentEmailDocket[] = $row->id;
                            }
                        }
                        unset($emailSentDocketsIds);
                        $emailSentDocketsIds = $tempSentEmailDocket;

                    }
                }
            }


            $sentDocketsQuery->whereIn('id',$possibleSentDockets);
            $sentEmailDocketsQuery->whereIn('id',$emailSentDocketsIds);
        }
        else{
            $sentDocketsQuery->where('sender_company_id', Session::get('company_id'))->orderBy('created_at', 'desc');
        }
        $emailSentDocket    =   $sentEmailDocketsQuery->with('senderUserInfo','senderCompanyInfo','docketInfo','sentDocketValue.docketFieldInfo')->get();
        $sentDocketResult   =   $sentDocketsQuery->with('senderUserInfo','senderCompanyInfo','recipientInfo.userInfo','docketInfo','sentDocketValue.docketFieldInfo')->get();
        $merged             =   $emailSentDocket->concat($sentDocketResult);
        $sentDockets        =   (new Collection($merged))->sortByDesc('created_at')->paginate($items);
        return view('dashboard.company.docketManager.dockets.advancedFilter.index',compact('company','sentDockets','request','clients','items','filterArray'));
    }

    public function createDockets(Request $request){
        if(auth()->user() != null){
            $user_type = auth()->user()->user_type;
        }else{
            $user_type = User::where('id',$request->header('userId'))->first()->user_type;
        }
        if($user_type == 2){
            $docketTemplate = Docket::where("company_id", Session::get('company_id'))->where('is_archive',0)->orderBy('title', 'asc')->get();
        }else{
            $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        }
        // $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        $emailClients = $this->apiService->emailUserList();
        $employeeList = $this->userService->getEmployeeList($request);
        // $docketDraft = DocketDraft::where([['user_id',auth()->user()->id],['is_draft',1]])->orderBy('id','desc')->first();
        // if($docketDraft){
        //     return view('dashboard.company.docketManager.docket.edit',compact('docketTemplate','emailClients','employeeList','docketDraft'));
        // }
        $templateId = null;
        $data = 'empty';
        return view('dashboard.company.docketManager.docket.create',compact('docketTemplate','emailClients','employeeList','templateId','data'));
    }

    public function docketdraftUser($draft_id,Request $request){
        if(auth()->user() != null){
            $user_type = auth()->user()->user_type;
        }else{
            $user_type = User::where('id',$request->header('userId'))->first()->user_type;
        }
        if($user_type == 2){
            $docketTemplate = Docket::where("company_id", Session::get('company_id'))->where('is_archive',0)->orderBy('title', 'asc')->get();
        }else{
            $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        }
        $emailClients = $this->apiService->emailUserList();
        $employeeList = $this->userService->getEmployeeList($request);
        $docketDraft = DocketDraft::where([['user_id',auth()->user()->id],['id',$draft_id]])
                                    ->orWhere([['user_id',auth()->user()->id],['docket_id',$draft_id]])
                                    ->first();
        $data = 'empty';
        $fromAssign = 'false';
        if($docketDraft){
            return view('dashboard.company.docketManager.docket.edit',compact('docketTemplate','emailClients','employeeList','docketDraft','fromAssign'));
        }
        $templateId = $draft_id;
        return view('dashboard.company.docketManager.docket.create',compact('docketTemplate','emailClients','employeeList','docketDraft','templateId','data'));
    }

    public function assignDocketdraftUser($draft_id,Request $request){
        if(auth()->user() != null){
            $user_type = auth()->user()->user_type;
        }else{
            $user_type = User::where('id',$request->header('userId'))->first()->user_type;
        }
        if($user_type == 2){
            $docketTemplate = Docket::where("company_id", Session::get('company_id'))->where('is_archive',0)->orderBy('title', 'asc')->get();
        }else{
            $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        }
        $emailClients = $this->apiService->emailUserList();
        $employeeList = $this->userService->getEmployeeList($request);
        $docketDraftAssign = DocketDraftsAssign::where([['assign_docket_user_id',$request->assign_docket_id],['docket_id',$draft_id]])->first();
        $fromAssign = 'true';
        if($docketDraftAssign){
            $docketDraft = DocketDraft::where([['id',$docketDraftAssign->docket_draft_id]])->first();
            if($docketDraft){
                return view('dashboard.company.docketManager.docket.edit',compact('docketTemplate','emailClients','employeeList','docketDraft','fromAssign'));
            }
        }else{
            $docketDraft = null;
        }
       
        $templateId = $draft_id;
        $temp['docket_template_id'] = $draft_id;
        $temp['assign_docket_id'] = $request->assign_docket_id;
        $temp['employeeList'] = $request->employeeList;
        $temp['machineList'] = $request->machineList;
        $data = json_encode($temp);
        return view('dashboard.company.docketManager.docket.create',compact('docketTemplate','emailClients','employeeList','docketDraft','templateId','data'));
    }

    public function apiAssignDocketdraftUser($draft_id,Request $request){
        if(auth()->user() != null){
            $user_type = auth()->user()->user_type;
            $user_id = auth()->user()->id;
            $company_id = auth()->user()->companyInfo->id;
        }else{
            $user_type = User::where('id',$request->header('userId'))->first()->user_type;
            $user_id = $request->header('userId');
            $company_id = $request->header('companyId');
        }
        if($user_type == 2){
            $docketTemplate = Docket::where("company_id", $company_id)->where('is_archive',0)->orderBy('title', 'asc')->get();
        }else{
            $docketTemplate = $this->docketService->getAssignedDocketTemplateByUserId($request);
        }
        $emailClients = $this->apiService->emailUserList();
        $employeeList = $this->userService->getEmployeeList($request);
        $docketDraftAssign = DocketDraftsAssign::where([['assign_docket_user_id',$request->assign_docket_id],['docket_id',$draft_id]])->first();
        $fromAssign = 'true';
        if($docketDraftAssign){
            $docketDraft = DocketDraft::where([['id',$docketDraftAssign->docket_draft_id]])->first();
            if($docketDraft){
                return response()->json(['docketTemplate' => $docketTemplate, 'emailClients' => $emailClients, 'employeeList' => $employeeList, 
                   'docketDraft' => $docketDraft, 'fromAssign' => $fromAssign ]);
            }
        }else{
            $docketDraft = null;
        }
       
        $templateId = $draft_id;
        $temp['docket_template_id'] = $draft_id;
        $temp['assign_docket_id'] = $request->assign_docket_id;
        $temp['employeeList'] = $request->employeeList;
        $temp['machineList'] = $request->machineList;
        $data = json_encode($temp);
        return response()->json(['docketTemplate' => $docketTemplate, 'emailClients' => $emailClients, 'employeeList' => $employeeList, 
                   'docketDraft' => $docketDraft, 'templateId' => $templateId, 'data' => $data ]);
    }

    public function docketDraft(Request $request,ClientService $clientService){
        $company            =   Company::with('dockets','invoices','docketLabels','invoiceLabels','employees.userInfo','userInfo')->findOrfail(Session::get('company_id'));
        $companyFolder      =   $company->folders()->with('folderItems')->where('root_id',0)->orderBy('name','asc')->get();
        $clients            =   $clientService->clients($company,array('requestedCompanyInfo.employees.userInfo','companyInfo.employees.userInfo'));
        $userId[] = $company->user_id;
        $allEmployeeId = array_merge($company->employees->pluck("user_id")->toArray(),$userId);
        $docketDrafts = DocketDraft::whereIn('user_id',$allEmployeeId)->where('is_admin',0)->orderBy('id','desc')->with('userInfo')->get();
        return view('dashboard.company.docketManager.dockets.draft.index', compact('company','companyFolder','clients','docketDrafts'));
    }

    public function docketDraftSave(Request $request){
        try{
            $docketDraftDb = DocketDraft::where('id',$request->docketDraftId)->first();
            $request = new Request();
            $request['id'] = $docketDraftDb->id;
            $request['user_id'] = $docketDraftDb->user_id;
            $request['docket_id'] = $docketDraftDb->docket_id;
            $request['data'] = $docketDraftDb->value;
            $request['created_at'] = $docketDraftDb->created_at;
            $request['updated_at'] = $docketDraftDb->updated_at;
            $request->headers->set('companyId', auth()->user()->companyInfo->id);
            $request->headers->set('userId', auth()->user()->id);
            $apiController = new APIController();
            $response = $apiController->v1SaveSentDefaultDocket($request);
            if(!$response->getData()->status){
                toastr()->error(MessageDisplay::ERROR);
                return redirect()->back();
            }
            toastr()->success(MessageDisplay::DocketDraftSave);
            return redirect()->route('dockets.docketDraft');
        }catch(\Exception $ex){
            toastr()->error(MessageDisplay::ERROR);
            return redirect()->back();
        }
    }
}
