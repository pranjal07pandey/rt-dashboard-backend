<?php

namespace App\Http\Controllers\InvoiceManager;

use App\AssignedInvoice;
use App\Company;
use App\Docket;
use App\Email_Client;
use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentInvoice;
use App\EmailSentInvoiceDescription;
use App\EmailSentInvoiceImage;
use App\EmailSentInvoicePaymentDetail;
use App\EmailSentInvoiceValue;
use App\EmailUser;
use App\Employee;
use App\FolderItem;
use App\Invoice;
use App\InvoiceField;
use App\InvoiceSetting;
use App\Mail\EmailInvoice;
use App\SentDocketInvoice;
use App\SentDocketRecipientApproval;
use App\SentDockets;
use App\SentEInvoiceAttachedEDocket;
use App\SentEmailDocketInvoice;
use App\SentInvoice;
use App\SentInvoiceAttachedDocket;
use App\SentInvoiceDescription;
use App\SentInvoiceImageValue;
use App\SentInvoicePaymentDetail;
use App\SentInvoiceValue;
use App\Services\CompanyService;
use App\User;
use App\UserNotification;
use PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Validator;
use Illuminate\Support\Facades\File;
use App\Support\Collection;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class SentInvoiceController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            if(Session::get('company_id')==''){
                if(Employee::where('user_id', Auth::user()->id)->count()!=0):
                    $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
                    Session::put('adminType',2);
                else :
                    $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
                    Session::put('adminType',1);
                endif;
                Session::put('company_id',$companyId);
            }
            if(!checkProfileComplete()){
                return redirect()->route('companyProfile');
            }

            $status     =   checkSubscription();
            switch ($status){
                case 'noSubscription':
                    return redirect('dashboard/company/profile/selectSubscription');
                    break;

                case 'subscriptionCancel':
                    return redirect()->route('Company.Subscription.Continue');
                    break;

                case 'past_due':
                    break;

                case 'card_declined':
                    return redirect()->route('Company.Subscription.CardDeclined');
                    break;

                case 'canceled':
                    //return redirect()->route('Company.Subscription.Canceled');
                    break;
                default:
                    break;
            }
            return $next($request);
        });
    }


    public function index(CompanyService $companyService){
        $company            =   Company::with('invoices')->findOrfail(Session::get('company_id'));
        if(count($company->invoices)==0){
            flash('Please create an invoice template from "Invoice Manager >> Invoice Templates" before creating an invoice.', 'warning');
            return redirect()->route('invoices.allInvoices');
        }
        $invoiceTemplates   =   $companyService->assignedInvoiceTemplate($company);
        return view('dashboard/company/invoiceManager/create/index',compact('invoiceTemplates','company'));
    }

    public function recipient(Request $request){
        if ($request->type == 1)
            $company    =   Company::with('employees.userInfo')->findOrfail(Session::get('company_id'));
        elseif($request->type == 2)
            $company    =   Company::with('emailClients.emailUser')->findOrfail(Session::get('company_id'));

        return view('dashboard/company/invoiceManager/create/partials/recipients/list',compact('company','request'));
    }

    public function dockets(Request $request){
        if($request->type==1){
            return $this->invoiceableDocket($request);
        }else{
            return $this->invoiceableEmailDocket($request);
        }
    }

    public function invoiceableDocket(Request $request){
        $admin      =   Employee::where('company_id',Session::get('company_id'))->where('is_admin',1)->where('employed',1)->pluck('user_id')->toArray();
        $admin[]    =   Company::where('id',Session::get('company_id'))->first()->user_id;

        if(in_array(Auth::user()->id,$admin)){
            $sentDocketQueryTemp    =   SentDockets::with('recipientInfo','docketInfo','senderUserInfo')->where('sender_company_id',Session::get('company_id'))->where('invoiceable',1)->orderBy('id','desc')->get();
        }else{
            $sentDocketQueryTemp    =   SentDockets::with('recipientInfo','docketInfo','senderUserInfo')->where('user_id',Auth::user()->id)->where('invoiceable',1)->orderBy('id','desc')->get();
        }
        $dockets     =    array();
        $advanceFilterData =    array();
        foreach($sentDocketQueryTemp as $sentDocket){
            $advanceFilterData['dockets'][]   =   $sentDocket->docketInfo;
            if ($sentDocket->recipientInfo->count() == 1){
                if ($sentDocket->recipientInfo->first()->user_id == $request->recipient) {
                    $dockets[]   =   $sentDocket;
                    $advanceFilterData['amount'][]  =   $sentDocket->invoiceAmount();
                    $totalSentDocketID[]    =   $sentDocket->id;
                }
            }else if($sentDocket->recipientInfo->count()>=2){
                $tempSentDocketRecipient    =   $sentDocket->recipientInfo->pluck('user_id')->toArray();
                if ($this->array_equal($tempSentDocketRecipient,array(Auth::user()->id,$request->recipient))) {
                    $dockets[]   =   $sentDocket;
                    $advanceFilterData['amount'][]  =   $sentDocket->invoiceAmount();
                    $totalSentDocketID[]    =   $sentDocket->id;
                }
            }
        }
        if(array_key_exists('dockets', $advanceFilterData)){
            $advanceFilterData['dockets']   =   array_unique($advanceFilterData['dockets']);
        }
        $dockets    =   new Collection($dockets);
        return view('dashboard/company/invoiceManager/create/partials/attachDockets/dockets', compact('dockets','request', 'advanceFilterData'))->render();
    }

    public function invoice(Request $request){
        $selectedInvoiceable= $request->selectedInvoiceable;
        $type = $request->type;

        $invoiceableDocket = $this->getinvoiceableDocket($selectedInvoiceable,$type);

        $invoiceDetail = Invoice::find($request->id);
        if($invoiceDetail == null){
            $invoiceTemplate    =    Invoice::where('company_id',Session::get('company_id'))->pluck('id')->toArray();
            $invoiceAssignTemplate = AssignedInvoice::whereIn('invoice_id',$invoiceTemplate)->get();
            $invoiceTemplateResult = $invoiceAssignTemplate->unique('invoice_id');
            $company = Company::where('id',Session::get('company_id'))->first();
            $employee = $company->employees;
            $emailRecepients = array();
            $emailClient          =   Email_Client::where("company_id",Session::get('company_id'))->get();
            foreach ($emailClient as $emailClients ){
                $emailRecepients[] = array(
                    'id'=>$emailClients->emailUser->id,
                    'name'=>$emailClients->emailUser->email,

                );
            }
            return view('dashboard/company/sentInvoice/index',compact('invoiceTemplateResult','company','employee','emailRecepients'));
        }else{
            $invoice = InvoiceField::where('invoice_id',$request->id)->orderBy('order','asc')->get();
            return view('dashboard/company/sentInvoice/invoiceTemplate', compact('invoice','invoiceableDocket','invoiceDetail'));
        }

    }

    public function send(Request $request){
        $validator  =   Validator::make(Input::all(),['templateId' =>     'required', 'recipientId'  =>  'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $isDocketAttached = 0;
            if ($request->invoiceableDocketId != null){
                $invoiceableDocketId = explode( ',', $request->invoiceableDocketId);
                if (count($invoiceableDocketId)>0){
                    $isDocketAttached = 1;
                }
            }
            $date = Carbon::now()->format('M-d-Y');
            if ($request->isemail == 1){
                $recipientIdData =  $request->recipientId;
                $template = $request->templateId;
                $invDoc = $request->invoiceableDocketId;

                //check employee
                $company = Company::where('id',Session::get('company_id'))->first();
                $employee = $company->employees;
                $dataUserId = [];
                foreach ($employee as $row){
                    $dataUserId[] = $row->user_id;
                }
                array_push($dataUserId,$company->user_id);
                if (!in_array($recipientIdData,$dataUserId)){
                    return response()->json(array('status' => false,'message' => "Employee Not Found"));
                }
                //check invoiceId
                if (Invoice::where('id',$template)->where('company_id',Session::get('company_id'))->count() == 0){
                    return response()->json(array('status' => false,'message' => "Invoice Id Doesnt match"));
                }
                //check invoiceableDocket
                if ($invDoc != null){
                    $invoiceableDocket = explode( ',', $invDoc);
                    $totalSentDocketID  =   array();
                    $admin  =   array();
                    $admin    =   Employee::where('company_id',Session::get('company_id'))->where('is_admin',1)->where('employed',1)->pluck('user_id')->toArray();
                    $admin[]   =   Company::where('id',Session::get('company_id'))->first()->user_id;

                    if(in_array(Auth::user()->id,$admin)){
                        $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',Session::get('company_id'))->where('invoiceable',1)->orderBy('id','desc')->get();
                    }else{
                        $sentDocketQueryTemp              =   SentDockets::where('user_id',Auth::user()->id)->where('invoiceable',1)->orderBy('id','desc')->get();
                    }
                    foreach($sentDocketQueryTemp as $sentDocket){
                        if ($sentDocket->recipientInfo->count() == 1){
                            if ($sentDocket->recipientInfo->first()->user_id == $recipientIdData) {
                                $totalSentDocketID[] = $sentDocket->id;
                            }
                        }else if($sentDocket->recipientInfo->count()>=2){
                            $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
                            if ($this->array_equal($tempSentDocketRecipient,array(Auth::user()->id,$recipientIdData))) {
                                $totalSentDocketID[]    =   $sentDocket->id;
                            }
                        }
                    }
                    foreach ($invoiceableDocket as $invoiceableDockets){
                        if (!in_array($invoiceableDockets,$totalSentDocketID)){
                            return response()->json(array('status' => false,'message' => "Invoiceable Docket Id Doesnt match"));
                        }
                    }
                }

                $invoice    =   Invoice::where('id',$request->templateId)->first();
                $companyInvoice=Company::where('id',  Session::get('company_id'))->first();
                $invoiceuserFullname= User::where('id', Auth::user()->id)->first();
                $sentInvoice =     new SentInvoice();
                $sentInvoice->user_id    =    Auth::user()->id;
                $sentInvoice->abn                =      $companyInvoice->abn;
                $sentInvoice->company_name       =      $companyInvoice->name;
                $sentInvoice->company_address    =      $companyInvoice->address;
                $sentInvoice->company_logo    =      $companyInvoice->logo;
                $sentInvoice->sender_name        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $sentInvoice->invoice_id  =   $request->templateId;
                $sentInvoice->user_invoice_count = 0;
                $sentInvoice->receiver_user_id   =   $request->recipientId;
                if(Employee::where('user_id', $request->recipientId)->count()!=0):
                    $companyId = Employee::where('user_id', $request->recipientId)->first()->company_id;
                else :
                    $companyId   =   Company::where('user_id', $request->recipientId)->first()->id;
                endif;
                $sentInvoice->receiver_company_id         =   $companyId;
                $sentInvoice->company_id	=  Session::get('company_id');
                $sentInvoice->status             =   0;
                $sentInvoice->amount        =   0;
                $sentInvoice->isDocketAttached  =  $isDocketAttached;
                if($invoice->gst==1){
                    $sentInvoice->gst           =   $invoice->gst_value;
                }
                if($companyInvoice->number_system == 1){
                    if (SentInvoice::where('company_id', Session::get('company_id'))->count()== 0){
                        $sentInvoice->company_invoice_id = 1;
                    }else{
                        $companyDocketId =  SentInvoice::where('company_id', Session::get('company_id'))->pluck('company_invoice_id')->toArray();;
                        $sentInvoice->company_invoice_id = max($companyDocketId) + 1;
                    }
                }else{
                    $sentInvoice->company_invoice_id  = 0;
                }



                $sentInvoice->save();


                $findUserInvoiceCount = SentInvoice::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                $findUserEmailInvoiceCount =EmailSentInvoice::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                if(max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount)) == 0){
                    $uniquemax = 0;
                    $sentInvoice->user_invoice_count = $uniquemax+1;
                    $employeeData = Employee::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
                    if($employeeData->count() == 0){
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                    }else{
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                    $sentInvoice->update();
                }else{
                    $uniquemax = max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount));
                    $sentInvoice->user_invoice_count = $uniquemax+1;
                    $employeeData = Employee::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
                    if($employeeData->count() == 0){
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                    }else{
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                    $sentInvoice->update();
                }



                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =    InvoiceSetting::where('company_id',Session::get('company_id'));
                if($invoiceSettingQuery->count()==1){
                    $invoiceSetting =   $invoiceSettingQuery->first();
                    $sentInvoicePaymentDetails  =    new SentInvoicePaymentDetail();
                    $sentInvoicePaymentDetails->sent_invoice_id =   $sentInvoice->id;
                    $sentInvoicePaymentDetails->company_id      =   $sentInvoice->company_id;
                    $sentInvoicePaymentDetails->bank_name       =   $invoiceSetting->bank_name;
                    $sentInvoicePaymentDetails->account_name	=   $invoiceSetting->account_name;
                    $sentInvoicePaymentDetails->bsb_number      =   $invoiceSetting->bsb_number;
                    $sentInvoicePaymentDetails->account_number  =   $invoiceSetting->account_number;
                    $sentInvoicePaymentDetails->instruction     =   $invoiceSetting->instruction;
                    $sentInvoicePaymentDetails->additional_information  =   $invoiceSetting->additional_information;
                    $sentInvoicePaymentDetails->save();
                }

                if($isDocketAttached==1){
                    foreach($invoiceableDocketId as $rowId) {
                        $attachedDocket     =   new SentInvoiceAttachedDocket();
                        $attachedDocket->sent_invoice_id    =   $sentInvoice->id;
                        $attachedDocket->sent_docket_id     =   $rowId;
                        $attachedDocket->save();
                    }
                }

                if (@$invoice->docketFolderAssign!=null){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id = $invoice->docketFolderAssign->folder_id;
                    $folderItem->ref_id = $sentInvoice->id;
                    $folderItem->type = 2;
                    $folderItem->user_id = Auth::user()->id;
                    $folderItem->status = 0;
                    $folderItem->company_id = Session::get('company_id');
                    if ($folderItem->save()){
                        SentInvoice::where('id',$sentInvoice->id)->update(['folder_status'=>1]);
                    }
                }


                //invoice filed query
                $invoiceFieldsQuery   =  InvoiceField::where('invoice_id',$request->templateId)->orderBy('order','asc')->get();
                $signatureSN=1;
                foreach ($invoiceFieldsQuery as $row){
                    if($row->invoice_field_category_id==9){
                        $invoiceFieldValue   =   new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "signature";
                        $invoiceFieldValue->save();
                        if (Input::has('formFieldSignature'.$row->id)){
                            $imageField =   'formFieldSignature'.$row->id;
                            $image     =   Input::file($imageField);
                            if (count($image)>0){
                                foreach ($image as $images){
                                    if($images->isValid()) {
                                        $imageValue     =    new SentInvoiceImageValue();
                                        $imageValue->sent_invoice_value_id    =  $invoiceFieldValue->id;
                                        // $ext = $images->getClientOriginalExtension();
                                        // $filename = basename("signature.".Auth::user()->id.$signatureSN) . time() . ".png";
                                        $dest = 'files/'.$date.'/invoice/signature';
                                        // $images->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$images);
                                        $imageValue->save();
                                    }
                                    $signatureSN++;
                                }
                            }

                        }
                    }elseif($row->invoice_field_category_id==5){
                        $invoiceFieldValue   =   new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "image";
                        $invoiceFieldValue->save();
                        if (Input::has('formFieldImage'.$row->id)){
                            $imageField =   'formFieldImage'.$row->id;
                            $image     =   Input::file($imageField);
                            if (count($image)>0){
                                foreach ($image as $images){
                                    if($images->isValid()) {
                                        $imageValue     =    new SentInvoiceImageValue();
                                        $imageValue->sent_invoice_value_id    =  $invoiceFieldValue->id;
                                        // $ext = $images->getClientOriginalExtension();
                                        // $filename = basename($images->getClientOriginalName(), '.' . $images->getClientOriginalExtension()) . time() . "." . $ext;
                                        $dest = 'files/'.$date.'/invoice/image';
                                        // $images->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$images);
                                        $imageValue->save();
                                    }
                                }
                            }

                        }
                    } else if($row->invoice_field_category_id==12){
                        $invoiceFieldValue = new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id = $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id = $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value =  $row->label;
                        $invoiceFieldValue->save();
                    }
                    // else {
                    //     $invoiceFieldValue = new SentInvoiceValue();
                    //     $invoiceFieldValue->sent_invoice_id = $sentInvoice->id;
                    //     $invoiceFieldValue->invoice_field_id = $row->id;
                    //     $invoiceFieldValue->label  =   $row->label;
                    //     $invoiceFieldValue->value = Input::get('formField' . $row->id == "")? "N/a" : Input::get('formField' . $row->id);
                    //     $invoiceFieldValue->save();
                    // }
                    empty($invoiceFieldValue);
                }

                //invoice description query
                if(Input::get('invoiceDescriptionCount')!=0){
                    $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                    for($i = 0; $i <$totalInvoiceDescription;$i++){
                        if ( Input::get('invoiceDescriptionValue'.$i) != null){
                            $invoiceDescription     =    new SentInvoiceDescription();
                            $invoiceDescription->sent_invoice_id    =   $sentInvoice->id;
                            $invoiceDescription->description   =   Input::get('invoiceDescriptionValue'.$i);
                            $invoiceDescription->amount        =   Input::get('invoiceDescriptionAmount'.$i);
                            $invoiceDescription->save();
                            empty($invoiceDescription);


                            $emailTotal=0;
                            foreach ($invoiceDescription->where('sent_invoice_id',$sentInvoice->id)->get() as $item){
                                $emailTotal += $item->amount;
                            }
                            $invoiceAmount  =    0;
                            if($isDocketAttached==1){
                                $invoiceAmountQuery    =    SentDocketInvoice::whereIn('sent_docket_id',$invoiceableDocketId)->where('type',2)->get();
                                foreach($invoiceAmountQuery as $amount){
                                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                                }
                            }
                            $totalAmount = $emailTotal+$invoiceAmount;
                            $sentInvoice->amount = $totalAmount;
                            $sentInvoice->save();
                        }

                    }

                }


                $sentInvoiceReceiverInfo    =    User::where('id',$request->recipientId)->first();
                $userNotification   =   new UserNotification();
                $userNotification->sender_user_id   =   Auth::user()->id;
                $userNotification->receiver_user_id =   $sentInvoiceReceiverInfo->id;
                $userNotification->type     =   4;
                $userNotification->title    =   'New Invoice';
                $userNotification->message  =   "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name;
                $userNotification->key      =   $sentInvoice->id;
                $userNotification->status   =   0;
                $userNotification->save();
                if($sentInvoiceReceiverInfo->device_type == 2){
                    sendiOSNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));

                }else if($sentInvoiceReceiverInfo->device_type == 1){
                    sendAndroidNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));
                }


                return response()->json(['status'=>true ,'data'=>'invoiceManager/allInvoice']);
            }
            else if ($request->isemail == 2){

                $recipientIdData =  $request->emailrecipientId;
                $template = $request->templateId;
                $invDoc = $request->invoiceableDocketId;
                //check email employee
                $emailRecepients = [];
                $emailClient          =   Email_Client::where("company_id",Session::get('company_id'))->get();
                foreach ($emailClient as $emailClients ){
                    $emailRecepients[] = $emailClients->emailUser->id;
                }
                if (!in_array($recipientIdData,$emailRecepients)){
                    return response()->json(array('status' => false,'message' => "Email Employee Not Found"));
                }

                //check invoiceId
                if (Invoice::where('id',$template)->where('company_id',Session::get('company_id'))->count() == 0){
                    return response()->json(array('status' => false,'message' => "Invoice Id Doesnt match"));
                }

                if ($invDoc != null){
                    $invoiceableDocket = explode( ',', $invDoc);
                    $emailSentDocket = EmailSentDocket::where('user_id',Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
                    $arrays = array();
                    foreach ($emailSentDocket as $row){
                        foreach ($row->recipientInfo as $items){
                            if ($items->email_user_id == $recipientIdData){
                                $arrays[]=$items->email_sent_docket_id;
                            }
                        }
                    }



                    foreach ($invoiceableDocket as $invoiceableDockets){
                        if (!in_array($invoiceableDockets,$arrays)){
                            return response()->json(array('status' => false,'message' => "Invoiceable Email Docket Id Doesnt match"));
                        }
                    }


                }


                $invoice    =   Invoice::where('id',$request->templateId)->first();
                $companyInvoice=Company::where('id',Session::get('company_id'))->first();
                $invoiceuserFullname= User::where('id',Auth::user()->id)->first();
                $sentInvoice =     new EmailSentInvoice();
                $sentInvoice->abn                =      $companyInvoice->abn;
                $sentInvoice->company_name       =      $companyInvoice->name;
                $sentInvoice->company_address    =      $companyInvoice->address;
                $sentInvoice->company_logo    =      $companyInvoice->logo;
                $sentInvoice->template_title    =   $invoice->title;
                $sentInvoice->sender_name        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $sentInvoice->invoice_id  =   $request->templateId;
                $sentInvoice->user_id    =   Auth::user()->id;
                $sentInvoice->company_id	=   Session::get('company_id');
                $sentInvoice->receiver_user_id   =   $request->emailrecipientId;

                $emailClient = Email_Client::where('company_id', Session::get('company_id'))->where('email_user_id', $request->emailrecipientId)->first();
                $sentInvoice->receiver_full_name = $emailClient->full_name;
                $sentInvoice->receiver_company_name = $emailClient->company_name;
                $sentInvoice->receiver_company_address = $emailClient->company_address;


                $totalSum = 0;
                $totalInvoiceDescription1    =     Input::get('invoiceDescriptionCount');
                for($i = 0; $i <$totalInvoiceDescription1;$i++){
                    $totalSum +=   Input::get('invoiceDescriptionAmount'.$i);
                }

                $sentInvoice->amount =$totalSum;
                if($invoice->gst==1){
                    $sentInvoice->gst           =   $invoice->gst_value;
                }
                $sentInvoice->isDocketAttached  =   $isDocketAttached;
                $sentInvoice->hashKey           =   $this->generateRandomString();
                $sentInvoice->status            =   0;


                if($companyInvoice->number_system == 1){
                    if (EmailSentInvoice::where('company_id', Session::get('company_id'))->count()== 0){
                        $sentInvoice->company_invoice_id = 1;
                    }else{
                        $companyDocketId =  EmailSentInvoice::where('company_id', Session::get('company_id'))->pluck('company_invoice_id')->toArray();
                        $sentInvoice->company_invoice_id = max($companyDocketId) + 1;
                    }
                }else{
                    $sentInvoice->company_invoice_id  = 0;
                }

                $sentInvoice->save();

                if($companyInvoice->number_system == 1){
                    if($invoice->hide_prefix == 1){
                        $sentInvoice->formatted_id = $sentInvoice->company_id."-".$sentInvoice->company_invoice_id;
                    }else{
                        $sentInvoice->formatted_id = "rt-".$sentInvoice->company_id."-einv-".$sentInvoice->company_invoice_id;
                    }
                    $sentInvoice->update();
                }else{
                    $findUserInvoiceCount = SentInvoice::where('user_id',Auth::user()->id)->where('company_id', Session::get('company_id'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount =EmailSentInvoice::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                    if(max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount)) == 0){
                        $uniquemax = 0;
                        $sentInvoice->user_invoice_count = $uniquemax+1;
                        $employeeData = Employee::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
                        if($employeeData->count() == 0){
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id = $invoice->id."-1-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                            }
                        }else{
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id = $invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }
                        }
                        $sentInvoice->update();
                    }else{
                        $uniquemax = max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount));
                        $sentInvoice->user_invoice_count = $uniquemax+1;
                        $employeeData = Employee::where('user_id', Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
                        if($employeeData->count() == 0){
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id = $invoice->id."-1-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                            }
                        }else{
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id = $invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }
                        }
                        $sentInvoice->update();
                    }
                }

                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =    InvoiceSetting::where('company_id',Session::get('company_id'));
                if($invoiceSettingQuery->count()==1){
                    $invoiceSetting =   $invoiceSettingQuery->first();

                    $sentInvoicePaymentDetails                          =    new EmailSentInvoicePaymentDetail();
                    $sentInvoicePaymentDetails->email_sent_invoice_id   =   $sentInvoice->id;
                    $sentInvoicePaymentDetails->company_id              =   $sentInvoice->company_id;
                    $sentInvoicePaymentDetails->bank_name               =   $invoiceSetting->bank_name;
                    $sentInvoicePaymentDetails->account_name	        =   $invoiceSetting->account_name;
                    $sentInvoicePaymentDetails->bsb_number              =   $invoiceSetting->bsb_number;
                    $sentInvoicePaymentDetails->account_number          =   $invoiceSetting->account_number;
                    $sentInvoicePaymentDetails->instruction             =   $invoiceSetting->instruction;
                    $sentInvoicePaymentDetails->additional_information  =   $invoiceSetting->additional_information;
                    $sentInvoicePaymentDetails->save();
                }

                if($isDocketAttached==1){
                    foreach($invoiceableDocketId as $rowId) {
                        $attachedEmailDocket     =   new SentEInvoiceAttachedEDocket();
                        $attachedEmailDocket->sent_email_invoice_id    =   $sentInvoice->id;
                        $attachedEmailDocket->sent_email_docket_id     =   $rowId;
                        $attachedEmailDocket->save();
                    }
                }

                //invoice filed query
                $invoiceFieldsQuery   =  InvoiceField::where('invoice_id',$request->templateId)->orderBy('order','asc')->get();
                foreach ($invoiceFieldsQuery as $row){

                    if($row->invoice_field_category_id==9){
                        $invoiceFieldValue   =   new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "signature";
                        $invoiceFieldValue->save();
                        if (Input::has('formFieldSignature'.$row->id)){
                            $imageField =   'formFieldSignature'.$row->id;
                            $image     =   Input::file($imageField);
                            if (count($image)>0){
                                foreach ($image as $images){
                                    if($images->isValid()) {
                                        $imageValue     =    new EmailSentInvoiceImage();
                                        $imageValue->email_sent_invoice_value_id    =  $invoiceFieldValue->id;
                                        // $ext = $images->getClientOriginalExtension();
                                        // $filename = basename($images->getClientOriginalName(), '.' . $images->getClientOriginalExtension()) . time() . "." . $ext;
                                        $dest = 'files/'.$date.'/invoice/signature/email';
                                        // $images->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$images);
                                        $imageValue->save();
                                    }
                                }
                            }

                        }
                    }elseif($row->invoice_field_category_id==5){
                        $invoiceFieldValue   =   new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "image";
                        $invoiceFieldValue->save();
                        if (Input::has('formFieldImage'.$row->id)){
                            $imageField =   'formFieldImage'.$row->id;
                            $image     =   Input::file($imageField);
                            if (count($image)>0){
                                foreach ($image as $images){
                                    if($images->isValid()) {
                                        $imageValue     =    new EmailSentInvoiceImage();
                                        $imageValue->email_sent_invoice_value_id    =  $invoiceFieldValue->id;
                                        // $ext = $images->getClientOriginalExtension();
                                        // $filename = basename($images->getClientOriginalName(), '.' . $images->getClientOriginalExtension()) . time() . "." . $ext;
                                        $dest = 'files/'.$date.'/invoice/image/email';
                                        // $images->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$images);
                                        $imageValue->save();
                                    }
                                }
                            }

                        }
                    } else if($row->invoice_field_category_id==12){
                        $invoiceFieldValue = new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id = $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id = $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value =  $row->label;
                        $invoiceFieldValue->save();
                    }else {
                        $invoiceFieldValue = new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id = $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id = $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value = (Input::get('formField' . $row->id) == "")? "N/a" : Input::get('formField' . $row->id);
                        $invoiceFieldValue->save();
                    }
                    empty($invoiceFieldValue);
                }

                //invoice description query
                if(Input::get('invoiceDescriptionCount')!=0){
                    $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');

                    for($i = 0; $i <$totalInvoiceDescription;$i++){
                        $description = Input::get('invoiceDescriptionValue'.$i);
                        if ($description != null){
                            $amount = Input::get('invoiceDescriptionAmount'.$i);
                            $invoiceDescription     =    new EmailSentInvoiceDescription();
                            $invoiceDescription->email_sent_invoice_id    =   $sentInvoice->id;

                            $invoiceDescription->description   =  $description ;
                            $invoiceDescription->amount        =   $amount;
                            $invoiceDescription->save();
                            empty($invoiceDescription);
                        }

                    }
                }

                if (@$invoice->docketFolderAssign!=null){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id = $invoice->docketFolderAssign->folder_id;
                    $folderItem->ref_id = $sentInvoice->id;
                    $folderItem->type = 4;
                    $folderItem->user_id = Auth::user()->id;
                    $folderItem->status = 0;
                    $folderItem->company_id = Session::get('company_id');
                    if ($folderItem->save()){
                        EmailSentInvoice::where('id',$sentInvoice->id)->update(['folder_status'=>1]);
                    }
                }
                Mail::to($sentInvoice->receiverInfo->email)->send(new EmailInvoice($sentInvoice, $sentInvoice->receiverInfo, 'You’ve got an invoice'));
                // Mail::send('emails.invoice.emailInvoice', $data, function ($message) use($invoice,$sentInvoice,$invoiceuser) {
                //     $message->from("info@recordtimeapp.com.au",$invoice->companyInfo->name );
                //     $message->replyTo($invoiceuser->email,@$invoiceuser->first_name.' '.@$invoiceuser->last_name);
                //     $message->to($sentInvoice->receiverInfo->email)->subject($invoice->title);
                // });

                return response()->json(['status'=>true ,'data'=>'invoiceManager/allInvoice']);

            }
        endif;
    }

    public function array_equal($a, $b) {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }

    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }


    public function getinvoiceableDocket($selectedInvoiceable,$type){
        if($type == 1){
            $invoiceableDockets =   array();
            if ($selectedInvoiceable != null){
                $sentDocketQuery    =    SentDockets::whereIn('id',$selectedInvoiceable);
                if($sentDocketQuery->count()>0) {
                    $resultQuery = $sentDocketQuery->orderBy('created_at', 'desc')->get();

                    foreach ($resultQuery as $result) {
                        if ($result->sender_company_id == Session::get('company_id')):
                            $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                            $company = $result->senderCompanyInfo->name;
                            $senderImage = $result->senderUserInfo->image;
                            $recipientsQuery = $result->recipientInfo;
                            $recipientData = "";
                            foreach ($recipientsQuery as $recipient) {
                                if ($recipient->id == $recipientsQuery->first()->id)
                                    $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                                else
                                    $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                            }

                            $totalRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $result->id)->count();
                            $totalRecipientApproved = SentDocketRecipientApproval::where('sent_docket_id', $result->id)->where('status', 1)->count();

                            //check is approval
                            $isApproval = 0;
                            $isApproved = 0;

                            if (SentDocketRecipientApproval::where('sent_docket_id', $result->id)->where('user_id', Auth::user()->id)->count() == 1) {
                                $isApproval = 1;

                                //check is approved
                                if (SentDocketRecipientApproval::where('sent_docket_id', $result->id)->where('user_id', Auth::user()->id)->where('status', 1)->count() == 1) {
                                    $isApproved = 1;
                                }
                            }


                            if ($totalRecipientApproved == $totalRecipientApprovals) {
                                $approvalText = "Approved";

                            } else {
                                $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

                            }


                            $invoiceDescription = array();
                            $invoiceDescriptionQuery = SentDocketInvoice::where('sent_docket_id', $result->id)->where('type', 1)->get();
                            foreach ($invoiceDescriptionQuery as $description) {
                                $invoiceDescription[] = array('label' => $description->sentDocketValueInfo->label, 'value' => $description->sentDocketValueInfo->value);
                            }

                            $invoiceAmount = 0;
                            $invoiceAmountQuery = SentDocketInvoice::where('sent_docket_id', $result->id)->where('type', 2)->get();
                            foreach ($invoiceAmountQuery as $amount) {
                                $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                if (is_numeric($unitRate[0]["value"])) {
                                    $unitRate1 = $unitRate[0]["value"];
                                } else {
                                    $unitRate1 = 0;
                                }
                                if (is_numeric($unitRate[1]["value"])) {
                                    $unitRate2 = $unitRate[1]["value"];
                                } else {
                                    $unitRate2 = 0;
                                }
                                $invoiceAmount = $invoiceAmount + $unitRate1 * $unitRate2;
                            }
                            //                if($invoiceAmount != 0) {


                            $invoiceableDockets[] = array('id' => $result->id,
                                'companyDocketId' => 'rt-' . $result->sender_company_id . '-doc-' . $result->company_docket_id,
                                'user_id' => $result->user_id,
                                'docketTemplateId' => $result->docketInfo->id,
                                'docketName' => $result->docketInfo->title,
                                'sender' => $userName,
                                'company' => $company,
                                'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                                'invoiceDescription' => $invoiceDescription,
                                'invoiceAmount' => sprintf('%0.2f', $invoiceAmount),
                                'status' => $approvalText,
                                'senderImage' => AmazoneBucket::url() . $senderImage,
                                'recipient' => $recipientData,
                                'isApproval' => $isApproval,
                                'isApproved' => $isApproved,
                            );


                            //                }
                            empty($invoiceDescription);
                            empty($invoiceAmount);
                        endif;
                    }

                }
                $getInvoicealeList =  (new Collection($invoiceableDockets));
            }else{
                $getInvoicealeList =  (new Collection());
            }
        }elseif($type == 2){

            if ($selectedInvoiceable != null){
                $matchEmailDocket = EmailSentDocket::whereIn('id',$selectedInvoiceable);
                $invoiceableEmailDockets =   array();
                if($matchEmailDocket->count()>0) {
                    $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
                    foreach ($resultQuery as $result) {
                        if ($result->company_id == Session::get('company_id')):
                            $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                            $company = $result->senderCompanyInfo->name;
                            $senderImage = $result->senderCompanyInfo->userInfo->image;
                            $recipientName  =    "";
                            foreach($result->recipientInfo as $recipient) {
                                if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                                    $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                                }else{
                                    $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                                }
                                if ($result->recipientInfo->count() > 1)
                                    if ($result->recipientInfo->last()->id != $recipient->id){
                                        $recipientName  = $recipientName.", ";
                                    }

                            }
                            //approval text
                            $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                            $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
                            // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                            if ($totalRecipientApproved == $totalRecipientApprovals ){
                                $approvalText               =  "Approved";
                            }else{
                                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                            }


                            $invoiceDescription     =    array();
                            $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
                            foreach($invoiceDescriptionQuery as $description){
                                $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                            }
                            $invoiceAmount  =    0;
                            $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                            foreach($invoiceAmountQuery as $amount){
                                $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                                $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                            }
                            //                if($invoiceAmount != 0) {
                            $invoiceableEmailDockets[] = array('id' => $result->id,
                                'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                                'user_id' => $result->user_id,
                                'docketName' => $result->docketInfo->title,
                                'docketTemplateId' => $result->docketInfo->id,
                                'sender' => $userName,
                                'company' => $company,
                                'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                                'invoiceDescription' => $invoiceDescription,
                                'invoiceAmount' => $invoiceAmount,
                                'status' => $approvalText,
                                'recipient'=>$recipientName,
                                "isApproved"    => $result->status,
                                'senderImage'=> AmazoneBucket::url() . $senderImage,
                            );
                            //                }
                            empty($invoiceDescription);
                            empty($invoiceAmount);
                        endif;
                    }

                }
                $getInvoicealeList =  (new Collection($invoiceableEmailDockets));

            }else{
                $getInvoicealeList =  (new Collection());
            }

        }

        return  $getInvoicealeList;
    }

    function generateRandomString($length = 60) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }


    public function invoiceableEmailDocket(Request $request){

        $emailSentDocket = EmailSentDocket::where('user_id',Auth::user()->id)->where('company_id', Session::get('company_id'))->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $request->recipient){
                    $arrays[]=$items->email_sent_docket_id;
                }
            }
        }
        $matchEmailDocket = EmailSentDocket::whereIn('id',$arrays);
        $invoiceableEmailDockets =   array();
        $record_time_user = $request->userId;
        $docketDetail = array();
        $amounts = array();
        $rangeandDocketTemplate = array();
        if($matchEmailDocket->count()>0) {
            $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
            foreach ($resultQuery as $result) {
                if ($result->company_id == Session::get('company_id')):
                    $docketDetail[] = array(
                        'id'=>$result->docketInfo->id,
                        'title' => $result->docketInfo->title
                    );
                    $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                    $company = $result->senderCompanyInfo->name;
                    $senderImage = $result->senderCompanyInfo->userInfo->image;
                    $recipientName  =    "";
                    foreach($result->recipientInfo as $recipient) {
                        if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                            $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                        }else{
                            $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                        }
                        if ($result->recipientInfo->count() > 1)
                            if ($result->recipientInfo->last()->id != $recipient->id){
                                $recipientName  = $recipientName.", ";
                            }

                    }
                    //approval text
                    $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                    $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
                    // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                    if ($totalRecipientApproved == $totalRecipientApprovals ){
                        $approvalText               =  "Approved";
                    }else{
                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                    }


                    $invoiceDescription     =    array();
                    $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
                    foreach($invoiceDescriptionQuery as $description){
                        $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                    }
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    $amounts[] = $invoiceAmount;
                    $uniqueDocketName = $this->unique_multidim_array($docketDetail,'id');
                    $doc = array();
                    foreach ($uniqueDocketName as $uniqueDocketNames){
                        $doc[] = array(
                            'id'=>$uniqueDocketNames['id'],
                            'title' => $uniqueDocketNames['title']
                        );
                    }

                    $rangeandDocketTemplate['docket_template'] = $doc;
                    $rangeandDocketTemplate['range'] = array(
                        'min'=>min($amounts),
                        'max'=>max($amounts));

                    //                if($invoiceAmount != 0) {
                    $invoiceableEmailDockets[] = array('id' => $result->id,
                        'companyDocketId'=>$result->formatted_id,
                        'user_id' => $result->user_id,
                        'docketName' => $result->docketInfo->title,
                        'docketTemplateId' => $result->docketInfo->id,
                        'sender' => $userName,
                        'company' => $company,
                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                        'invoiceDescription' => $invoiceDescription,
                        'invoiceAmount' => $invoiceAmount,
                        'status' => $approvalText,
                        'recipient'=>$recipientName,
                        "isApproved"    => $result->status,
                        'senderImage'=> AmazoneBucket::url() . $senderImage,


                    );
                    //                }
                    empty($invoiceDescription);
                    empty($invoiceAmount);
                endif;
            }

        }

        $getInvoicealeList =  (new Collection($invoiceableEmailDockets));
        return view('dashboard/company/sentInvoice/invoiceableEmailDocket', compact('getInvoicealeList','rangeandDocketTemplate','record_time_user'));
    }



    public  function filterInvoiceableDocket(Request $request){
        $min_amount = explode(",",$request->range)[0];
        $max_amount = explode(",",$request->range)[1];
        $userId = $request->record_time_user;
        $totalSentDocketID  =   array();

        //get company superadmin, admins user id
        $admin  =   array();
        $admin    =   Employee::where('company_id',Session::get('company_id'))->where('is_admin',1)->where('employed',1)->pluck('user_id')->toArray();
        $admin[]   =   Company::where('id',Session::get('company_id'))->first()->user_id;

        if(in_array(Auth::user()->id,$admin)){
            $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',Session::get('company_id'))->where('invoiceable',1)->orderBy('id','desc')->get();
            // dd($sentDocketQueryTemp->pluck('id'));
        }else{
            $sentDocketQueryTemp              =   SentDockets::where('user_id',Session::get('company_id'))->where('invoiceable',1)->orderBy('id','desc')->get();
        }
        foreach($sentDocketQueryTemp as $sentDocket){
            if ($sentDocket->recipientInfo->count() == 1){
                // echo $sentDocket->id."<br/>";
                if ($sentDocket->recipientInfo->first()->user_id == $userId) {
                    $totalSentDocketID[] = $sentDocket->id;
                }
            }else if($sentDocket->recipientInfo->count()>=2){
                //get all recipients by sent dockets id
                $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
                if ($this->array_equal($tempSentDocketRecipient,array(Auth::user()->id,$userId))) {
                    $totalSentDocketID[]    =   $sentDocket->id;
                }
            }
        }
        $sentDocketQuery    =    SentDockets::whereIn('id',$totalSentDocketID);
        if(Input::has("to")){
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }



        if(Input::has("docketTempalte_id")) {
            if ($request->docketTempalte_id && $request->docketTempalte_id != null) {
                $sentDocketQuery->whereIn('docket_id', $request->docketTempalte_id);
            }
        }


        if($request->docket_id && $request->docket_id != null){
            $sentDocketQuery->where('id',$request->docket_id);
        }


        $rangeValue = array();
        if ($min_amount!= '' && $min_amount != ''){
            $range = array();
            foreach ($sentDocketQuery->get()  as $sentDocketQuerys  ){
                if ($sentDocketQuerys->sender_company_id == Session::get('company_id')){
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$sentDocketQuerys->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        if(is_numeric($unitRate[0]["value"])){
                            $unitRate1= $unitRate[0]["value"];
                        }else{
                            $unitRate1=0;
                        }
                        if(is_numeric($unitRate[1]["value"])){
                            $unitRate2= $unitRate[1]["value"];
                        }else{
                            $unitRate2= 0;
                        }
                        $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
//                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    $range[] =
                        array(
                            'docket_id'=>$sentDocketQuerys->docketInfo->id,
                            'amount'=> $invoiceAmount

                        );
                }

            }


            $rangeData = new Collection($range);


            if ($min_amount != '' && $max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $min_amount && $rangeDatas['amount'] <= $max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }

        }


        $filterData    =   $sentDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableDockets =   array();
        foreach ($filterData as $result){
            if ($result->sender_company_id == Session::get('company_id')):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $senderImage = $result->senderUserInfo->image;
                $company = $result->senderCompanyInfo->name;
                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                }
                //approval text
                $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;

                if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',Auth::user()->id)->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',Auth::user()->id)->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }
                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }
                $invoiceDescription     =    array();
                $invoiceDescriptionQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',1)->get();
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentDocketValueInfo->label,'value' => $description->sentDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    if(is_numeric($unitRate[0]["value"])){
                        $unitRate1= $unitRate[0]["value"];
                    }else{
                        $unitRate1=0;
                    }
                    if(is_numeric($unitRate[1]["value"])){
                        $unitRate2= $unitRate[1]["value"];
                    }else{
                        $unitRate2= 0;
                    }
                    $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                }

//                if (!$min_amount && !$max_amount){
//                    $invoiceableDockets[] = array('id' => $result->id,
//                        'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
//                        'user_id' => $result->user_id,
//                        'docketName' => $result->docketInfo->title,
//                        'docketTemplateId' => $result->docketInfo->id,
//                        'sender' => $userName,
//                        'company' => $company,
//                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
//                        'invoiceDescription' => $invoiceDescription,
//                        'invoiceAmount' => $invoiceAmount,
//                        'recipient'=>$recipientData,
//                        'senderImage'=>asset($senderImage),
//                        'status' => $approvalText,
//                        'isApproval'=>$isApproval,
//                        'isApproved'=>$isApproved,
//                    );
//                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $invoiceableDockets[] = array('id' => $result->id,
                            'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
                            'user_id' => $result->user_id,
                            'docketName' => $result->docketInfo->title,
                            'docketTemplateId' => $result->docketInfo->id,
                            'sender' => $userName,
                            'company' => $company,
                            'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                            'invoiceDescription' => $invoiceDescription,
                            'invoiceAmount' => $invoiceAmount,
                            'recipient'=>$recipientData,
                            'senderImage'=> AmazoneBucket::url() . $senderImage,
                            'status' => $approvalText,
                            'isApproval'=>$isApproval,
                            'isApproved'=>$isApproved,
                        );
                    }
//                }
                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;

        }

        $getInvoicealeList =  (new Collection($invoiceableDockets));
        return view('dashboard/company/sentInvoice/filterInvoiceableDocket', compact('getInvoicealeList'));

    }


    public function filterInvoiceableEmailDocket(Request $request){
        $userId = $request->record_time_user;
        $totalSentDocketID  =   array();
        $min_amount = explode(",",$request->range)[0];
        $max_amount = explode(",",$request->range)[1];

        $userId = $request->record_time_user;
        $emailSentDocket = EmailSentDocket::where('user_id',Auth::user()->id)->where('company_id',Session::get('company_id'))->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $userId){
                    $arrays[]=$items->email_sent_docket_id;
                }
            }
        }

        $sentEmailDocketQuery = EmailSentDocket::whereIn('id',$arrays);
        if(Input::has("to")){
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentEmailDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }

        if(Input::has("to")) {
            if ($request->docketTempalte_id && $request->docketTempalte_id != null) {
                $sentEmailDocketQuery->whereIn('docket_id', $request->docketTempalte_id);
            }
        }

        if($request->docket_id && $request->docket_id != null){
            $sentEmailDocketQuery->where('id',$request->docket_id);
        }


        $rangeValue = array();
        if ($min_amount!= '' && $min_amount != ''){
            $range = array();
            foreach ($sentEmailDocketQuery->get()  as $sentDocketQuerys  ){
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$sentDocketQuerys->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];

                }
                $range[] =
                    array(
                        'docket_id'=>$sentDocketQuerys->docketInfo->id,
                        'amount'=> $invoiceAmount

                    );

            }
            $rangeData = new Collection($range);
            if ($min_amount != '' && $max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $min_amount && $rangeDatas['amount'] <= $max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }
        }

        $filterData    =   $sentEmailDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableEmailDockets =   array();
        foreach ($filterData as $result) {
            if ($result->company_id == Session::get('company_id')):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $company = $result->senderCompanyInfo->name;
                $senderImage = $result->senderCompanyInfo->userInfo->image;
                $recipientName  =    "";
                foreach($result->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($result->recipientInfo->count() > 1)
                        if ($result->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }

                }
                //approval text
                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
                // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }


                $invoiceDescription     =    array();
                $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                }

//                if (!Input::has('min_amount') && !Input::has('max_amount')){
//                    $invoiceableEmailDockets[] = array('id' => $result->id,
//                        'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
//                        'user_id' => $result->user_id,
//                        'docketName' => $result->docketInfo->title,
//                        'docketTemplateId' => $result->docketInfo->id,
//                        'sender' => $userName,
//                        'company' => $company,
//                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
//                        'invoiceDescription' => $invoiceDescription,
//                        'invoiceAmount' => $invoiceAmount,
//                        'status' => $approvalText,
//                        'recipient'=>$recipientName,
//                        "isApproved"    => $result->status,
//                        'senderImage'=>asset($senderImage),
//                    );
//                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $invoiceableEmailDockets[] = array('id' => $result->id,
                            'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                            'user_id' => $result->user_id,
                            'docketName' => $result->docketInfo->title,
                            'docketTemplateId' => $result->docketInfo->id,
                            'sender' => $userName,
                            'company' => $company,
                            'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                            'invoiceDescription' => $invoiceDescription,
                            'invoiceAmount' => $invoiceAmount,
                            'status' => $approvalText,
                            'recipient'=>$recipientName,
                            "isApproved"    => $result->status,
                            'senderImage'=> AmazoneBucket::url() . $senderImage,
                        );
                    }
//                }


                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;
        }
        $getInvoicealeList =  (new Collection($invoiceableEmailDockets));

        return view('dashboard/company/sentInvoice/filterInvoiceableEmailDocket', compact('getInvoicealeList'));

    }
}