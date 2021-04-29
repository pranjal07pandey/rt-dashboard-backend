<?php

namespace App\Http\Controllers\Api;

use App\AssignedInvoice;
use App\Client;
use App\Company;
use App\CompanyXero;
use App\DocumentTheme;
use App\Email_Client;
use App\EmailSentInvoice;
use App\EmailSentInvoiceDescription;
use App\EmailSentInvoiceImage;
use App\EmailSentInvoicePaymentDetail;
use App\EmailSentInvoiceValue;
use App\Employee;
use App\FolderItem;
use App\Invoice;
use App\InvoiceField;
use App\InvoiceSetting;
use App\InvoiceXeroSetting;
use App\Mail\EmailInvoice;
use App\SentDocketInvoice;
use App\SentEInvoiceAttachedEDocket;
use App\SentInvoice;
use App\SentInvoiceAttachedDocket;
use App\SentInvoiceDescription;
use App\SentInvoiceImageValue;
use App\SentInvoicePaymentDetail;
use App\SentInvoiceValue;
use App\SentInvoiceXero;
use App\SentXeroInvoiceSetting;
use App\SubscriptionLog;
use App\SynXeroContact;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use XeroPHP\Application\PrivateApplication;
use Validator;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class InvoiceController extends Controller
{

    public function getInvoiceTemplateList(Request $request){
//        $invoiceTemplate     =    Invoice::select('id','title')->where('company_id',$request->header('companyId'))->orderBy('id','desc')->get();
        $invoiceTemplateQuery    =    AssignedInvoice::where('user_id',$request->header('userId'))->get();
        $invoiceTemplate    =   array();
        foreach ($invoiceTemplateQuery as $row){
            $invoiceTemplate[]     =    array('id'   =>  $row->invoice_id, 'title'    =>  $row->invoiceInfo->title);
        }
        return response()->json(array('status' => true, 'invoiceTemplate' => $invoiceTemplate));
    }

    public function getInvoiceTemplateDetailsById(Request $request, $invoiceId){
        $invoiceQuery     =   Invoice::where('id',$invoiceId);
        if($invoiceQuery->count()>0){
            if($invoiceQuery->first()->company_id==$request->header('companyId')){
                $invoice    =   $invoiceQuery->first();
                $invoiceFieldQuery    =   InvoiceField::where('invoice_id',$invoice->id)->orderBy('order','asc')->get();
                $invoiceFields   =   array();

                foreach ($invoiceFieldQuery as $row){
                    $subField   =   array();
                    if($row->invoice_field_category_id == 9) {
                        $invoiceFields[] = array('id' => $row->id,
                            'invoice_field_category_id' => $row->invoice_field_category_id,
                            'invoice_field_category' => $row->fieldCategoryInfo->title,
                            'label' => $row->label,
                            'order' => $row->order,
                            'subField'  => $subField);

                    }
                    elseif ($row->invoice_field_category_id == 1){
                        $imageHeader[] = array('id' => $row->id,
                            'invoice_field_category_id' => $row->invoice_field_category_id,
                            'invoice_field_category' => $row->fieldCategoryInfo->title,
                            'label' => $row->label,
                            'order' => $row->order,
                            'subField'  => $subField);
                    }
                    elseif ($row->invoice_field_category_id == 2){
                        $imageHeader[] = array('id' => $row->id,
                            'invoice_field_category_id' => $row->invoice_field_category_id,
                            'invoice_field_category' => $row->fieldCategoryInfo->title,
                            'label' => $row->label,
                            'order' => $row->order,
                            'subField'  => $subField);
                    }
                    elseif ($row->invoice_field_category_id == 5){
                        $imageHeader[] = array('id' => $row->id,
                            'invoice_field_category_id' => $row->invoice_field_category_id,
                            'invoice_field_category' => $row->fieldCategoryInfo->title,
                            'label' => $row->label,
                            'order' => $row->order,
                            'subField'  => $subField);
                    }
                    elseif ($row->invoice_field_category_id == 12){
                        $imageHeader[] = array('id' => $row->id,
                            'invoice_field_category_id' => $row->invoice_field_category_id,
                            'invoice_field_category' => $row->fieldCategoryInfo->title,
                            'label' => $row->label,
                            'order' => $row->order,
                            'subField'  => $subField);
                    }


                }
                if(@$imageHeader ){
                    foreach ($imageHeader as $row){
                        $invoiceFields[] =   $row;
                    }

                }

                return response()->json(array('status' => true,
                    'invoice' => array('id'         =>  $invoice->id,
                        'title'     =>  $invoice->title,
                        'subTitle'  =>  $invoice->subTitle,
                        'gst'       =>  $invoice->gst,
                        'gst_label' =>  $invoice->gst_label,
                        'gst_value' =>  $invoice->gst_value),
                    'invoice_field'  => $invoiceFields));
            }else{
                return response()->json(array("status" => false,"message"=>'Invalid Request!'));
            }

        }else {
            return response()->json(array("status" => true,"message"=>'Invoice not found!'));
        }
    }

    public function saveSentInvoice(Request $request){
        //cehck if subscription was free count remaining docket left
        $company    =    Company::where('id',$request->header('companyId'))->first();
        if($company->id!=1) {
            if ($company->trial_period == 3) {
                //get last subscription created date
                $subscriptionLogQuery = SubscriptionLog::where('company_id', $company->id);
                if ($subscriptionLogQuery->count() > 0) {
                    $lastUpdatedSubscription = $subscriptionLogQuery->orderBy('id', 'desc')->first();
                    $monthDay = Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
                    $now = Carbon::now();
                    $currentMonthStart = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay);
                    $currentMonthEnd = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay)->addDay(30);
                } else {
                    $currentMonthStart = new Carbon('first day of this month');
                    $currentMonthEnd = new Carbon('last day of this month');
                }

                $sentInvoices = SentInvoice::where('company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
                $emailInvoices = EmailSentInvoice::where('company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();

                $totalMonthInvoices = $sentInvoices + $emailInvoices;
                if ($totalMonthInvoices >= 1) {
                    return response()->json(array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 1 invoice per month.'));
                }
            }
        }
        $validator  =   Validator::make(Input::all(),['receiver_user_id' =>     'required',
            'invoice_id'  =>  'required',
            'isDocketAttached'  =>  'required']);

        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $date = Carbon::now()->format('d-M-Y');
            if($request->emailTemplateFlag=="true"):

                $invoice    =   Invoice::where('id',$request->invoice_id)->first();
                $companyInvoice=Company::where('id',$request->header('companyId'))->first();
                $invoiceuserFullname= User::where('id',$request->header('userId'))->first();
                $sentInvoice =     new EmailSentInvoice();
                $sentInvoice->invoice_id  =   $request->invoice_id;
                $sentInvoice->template_title    =   $invoice->title;
                $sentInvoice->abn                =      $companyInvoice->abn;
                $sentInvoice->company_name       =      $companyInvoice->name;
                $sentInvoice->company_address    =      $companyInvoice->address;
                $sentInvoice->company_logo = $companyInvoice->logo;
                $sentInvoice->sender_name        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $sentInvoice->user_id    =   $request->header('userId');
                $sentInvoice->theme_document_id = $invoice->theme_document_id;
                $sentInvoice->company_id	=   $request->header('companyId');
                $sentInvoice->receiver_user_id   =   $request->receiver_user_id;
                $sentInvoice->syn   =   $invoice->syn_xero;

                if($companyInvoice->number_system == 1){
                    if (EmailSentInvoice::where('company_id',$request->header('companyId'))->count()== 0){
                        $sentInvoice->company_invoice_id = 1;
                    }else{
                        $companyDocketId =  EmailSentInvoice::where('company_id',$request->header('companyId'))->pluck('company_invoice_id')->toArray();
                        $sentInvoice->company_invoice_id = max($companyDocketId) + 1;
                    }
                }else{
                    $sentInvoice->company_invoice_id  = 0;
                }

//                $sentInvoice->template_title=$invoice->title;

//                //receiver info optional
//                $sentInvoice->receiver_full_name            =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
//                $sentInvoice->receiver_company_name         =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
//                $sentInvoice->receiver_company_address      =   ($request->has('receiverCompanyAddress'))?$request->receiverCompanyAddress:"";


                if($request->has('receiverFullName') && $request->input('receiverFullName')!=""){
                    $sentInvoice->receiver_full_name            =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                    $sentInvoice->receiver_company_name         =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    $sentInvoice->receiver_company_address      =   ($request->has('receiverCompanyAddress'))?$request->receiverCompanyAddress:"";
                }else{
                    $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $request->receiver_user_id)->first();
                    $sentInvoice->receiver_full_name = $emailClient->full_name;
                    $sentInvoice->receiver_company_name = $emailClient->company_name;
                    $sentInvoice->receiver_company_address = $emailClient->company_address;
                }

//
//                $totalSum = 0;
//                $totalInvoiceDescription1    =     Input::get('invoiceDescriptionCount');
//                for($i = 0; $i <$totalInvoiceDescription1;$i++){
//                    $totalSum +=   Input::get('invoiceDescriptionAmount'.$i);
//                }
                $sentInvoice->amount =0;

                if($invoice->gst==1){
                    $sentInvoice->gst           =   $invoice->gst_value;
                }
                $sentInvoice->isDocketAttached  =   $request->isDocketAttached;
                $sentInvoice->hashKey           =   $this->generateRandomString();
                $sentInvoice->status            =   0;
                $sentInvoice->save();

                if($companyInvoice->number_system == 1){
                    if($invoice->hide_prefix ==1){
                        $sentInvoice->formatted_id = $sentInvoice->company_id."-".$sentInvoice->company_invoice_id;
                        $sentInvoice->update();
                    }else{
                        $sentInvoice->formatted_id = "rt-".$sentInvoice->company_id."-einv-".$sentInvoice->company_invoice_id;
                        $sentInvoice->update();
                    }
                }else{
                    $findUserInvoiceCount = SentInvoice::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount =EmailSentInvoice::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('invoice_id',$invoice->id)->pluck('user_invoice_count')->toArray();
                    if(max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount)) == 0){
                        $uniquemax = 0;
                        $sentInvoice->user_invoice_count = $uniquemax+1;
                        $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                        if($employeeData->count() == 0){
                            if($invoice->hide_prefix ==1){
                                $sentInvoice->formatted_id = $invoice->id."-1-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);

                            }
                        }else{
                            if($invoice->hide_prefix ==1){
                                $sentInvoice->formatted_id =$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }
                        }
                        $sentInvoice->update();
                    }else{
                        $uniquemax = max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount));
                        $sentInvoice->user_invoice_count = $uniquemax+1;
                        $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                        if($employeeData->count() == 0){
                            if($invoice->hide_prefix ==1){
                                $sentInvoice->formatted_id = $invoice->id."-1-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                            }
                        }else{
                            if($invoice->hide_prefix ==1){
                                $sentInvoice->formatted_id = $invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }else{
                                $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                            }
                        }
                        $sentInvoice->update();
                    }
                }





                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =    InvoiceSetting::where('company_id',$request->header('companyId'));
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

                if($request->isDocketAttached==1){
                    $docketsId = Input::get('dockets');
                    foreach($docketsId as $rowId) {
                        $attachedEmailDocket     =   new SentEInvoiceAttachedEDocket();
                        $attachedEmailDocket->sent_email_invoice_id    =   $sentInvoice->id;
                        $attachedEmailDocket->sent_email_docket_id     =   $rowId;
                        $attachedEmailDocket->save();
                    }
                }

                //invoice filed query
                $invoiceFieldsQuery   =  InvoiceField::where('invoice_id',$request->invoice_id)->orderBy('order','asc')->get();
                foreach ($invoiceFieldsQuery as $row){
                    if($row->invoice_field_category_id==9){
                        $invoiceFieldValue   =   new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "signature";
                        $invoiceFieldValue->save();

                        $totalImages    =     Input::get('formFieldSignature'.$row->id.'count');
                        for($i = 0; $i <$totalImages;$i++){
                            $imageField =   'formFieldSignature'.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValue     =    new EmailSentInvoiceImage();
                                    $imageValue->email_sent_invoice_value_id    =  $invoiceFieldValue->id;

                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/signature/email';
                                    // $image->move($dest, $filename);
                                    // $imageValue->value    =    $dest . '/' . $filename;
                                    $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                    $imageValue->save();
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

                        $totalImages    =     Input::get('formFieldImage'.$row->id.'count');
                        for($i = 0; $i <$totalImages;$i++){
                            $imageField =   'formFieldImage'.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValue     =    new EmailSentInvoiceImage();
                                    $imageValue->email_sent_invoice_value_id    =  $invoiceFieldValue->id;

                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/image/email';
                                    // $image->move($dest, $filename);
                                    // $imageValue->value    =    $dest . '/' . $filename;
                                    $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                    $imageValue->save();
                                }
                            }
                        }
                    }
                    else {
                        $invoiceFieldValue = new EmailSentInvoiceValue();
                        $invoiceFieldValue->email_sent_invoice_id = $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id = $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value = Input::get('formField' . $row->id == "")? "N/a" : Input::get('formField' . $row->id);
                        $invoiceFieldValue->save();
                    }

                    empty($invoiceFieldValue);
                }

                //invoice description query
//                $totalAmount= 0;
                if(Input::get('invoiceDescriptionCount')!=0){
                    $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                    for($i = 0; $i <$totalInvoiceDescription;$i++){
                        $invoiceDescription     =    new EmailSentInvoiceDescription();
                        $invoiceDescription->email_sent_invoice_id    =   $sentInvoice->id;
                        $invoiceDescription->description   =   Input::get('invoiceDescriptionValue'.$i);
                        $invoiceDescription->amount        =   Input::get('invoiceDescriptionAmount'.$i);
                        $invoiceDescription->save();
                        empty($invoiceDescription);
                    }
                    $total=0;
                    foreach ($invoiceDescription->where('email_sent_invoice_id',$sentInvoice->id)->get() as $item){
                        $total += $item->amount;
                    }
                    $sentInvoice->amount = $total;
                    $sentInvoice->save();
                }


                if ($invoice->docketFolderAssign->count()!=0){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id = $invoice->docketFolderAssign->folder_id;
                    $folderItem->ref_id = $sentInvoice->id;
                    $folderItem->type = 4;
                    $folderItem->user_id = $request->header('userId');
                    $folderItem->status = 0;
                    $folderItem->company_id = $request->header('companyId');
                    if ($folderItem->save()){
                        EmailSentInvoice::where('id',$sentInvoice->id)->update(['folder_status'=>1]);
                    }
                }


                //for emailing only
                $data['sentInvoice']    =   $sentInvoice;
                $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$sentInvoice->id)->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }
                $data['sentInvoiceValue']    =   $sentInvoiceValue;
                $invoiceSetting =   array();



                //check invoice payment info
                if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->count()==1){
                    $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->first();
                }


                $data['invoiceSetting'] =   $invoiceSetting;
                $data['invoiceDescription']     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$sentInvoice->id)->get();



                $document_name  = "emailed-invoice-".$sentInvoice->id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
                $document_path   =   'files/pdf/emailedInvoiceForward/'.str_replace('.', '',$document_name).'.pdf';
                if(!AmazoneBucket::fileExist($document_path)){

                    $invoice     =     $sentInvoice;
                    $companyDetails =   Company::where('id',$invoice->company_id)->first();

                    $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$invoice->id)->get();
                    $invoiceSetting =   array();
                    //check invoice payment info
                    if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$invoice->id)->count()==1){
                        $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$invoice->id)->first();
                    }

                    $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                    $output = $pdf->output();
                    $path = storage_path($document_path);
                    file_put_contents($path, $output);

                }
                $data["downloadLink"]   =   AmazoneBucket::url() . 'storage/'.$document_path;
                $invoiceuser= User::where('id',$request->header('userId'))->first();


//                if($request->header('companyId')==1) {
                    Mail::to($sentInvoice->receiverInfo->email)->send(new EmailInvoice($sentInvoice, $sentInvoice->receiverInfo, 'You’ve got an invoice'));
//                }else{
//                    Mail::send('emails.invoice.emailInvoice', $data, function ($message) use ($invoice, $sentInvoice, $invoiceuser) {
//                        $message->from("info@recordtimeapp.com.au", $invoice->companyInfo->name);
//                        $message->replyTo($invoiceuser->email, @$invoiceuser->first_name . ' ' . @$invoiceuser->last_name);
//                        $message->to($sentInvoice->receiverInfo->email)->subject($invoice->title);
//                    });
//                }

                return response()->json(array('status' => true, 'message' => 'Invoice successfully sent to '.$sentInvoice->receiverInfo->email));
            else:
                $invoice    =   Invoice::where('id',$request->invoice_id)->first();
                $companyInvoice=Company::where('id',$request->header('companyId'))->first();
                $invoiceuserFullname= User::where('id',$request->header('userId'))->first();
                $sentInvoice =     new SentInvoice();
                $sentInvoice->user_id    =   $request->header('userId');
                $sentInvoice->abn                =      $companyInvoice->abn;
                $sentInvoice->company_name       =      $companyInvoice->name;
                $sentInvoice->company_address    =      $companyInvoice->address;
                $sentInvoice->company_logo       =      $companyInvoice->logo;
                $sentInvoice->sender_name        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $sentInvoice->invoice_id  =   $request->invoice_id;
                $sentInvoice->theme_document_id = $invoice->theme_document_id;
                $sentInvoice->receiver_user_id   =   $request->receiver_user_id;
                $sentInvoice->syn   =   $invoice->syn_xero;

                if($companyInvoice->number_system == 1){
                    if (SentInvoice::where('company_id',$request->header('companyId'))->count()== 0){
                        $sentInvoice->company_invoice_id = 1;
                    }else{
                        $companyDocketId =  SentInvoice::where('company_id',$request->header('companyId'))->pluck('company_invoice_id')->toArray();
                        $sentInvoice->company_invoice_id = max($companyDocketId) + 1;
                    }
                }else{
                    $sentInvoice->company_invoice_id =0;
                }

//                $sentInvoice->template_title=$invoice->title;

                if(Employee::where('user_id', $request->receiver_user_id)->count()!=0):
                    $companyId = Employee::where('user_id', $request->receiver_user_id)->first()->company_id;
                else :
                    @$companyId   =   Company::where('user_id', $request->receiver_user_id)->first()->id;
                endif;

                $sentInvoice->receiver_company_id         =   $companyId;
                $sentInvoice->company_id	=   $request->header('companyId');
                $sentInvoice->status             =   0;
                $sentInvoice->amount        =   0;
                $sentInvoice->isDocketAttached  =   $request->isDocketAttached;

                if($invoice->gst==1){
                    $sentInvoice->gst           =   $invoice->gst_value;
                }

                $sentInvoice->save();

                if($companyInvoice->number_system == 1){
                    if ($invoice->hide_prefix == 1){
                        $sentInvoice->formatted_id = $sentInvoice->company_id."-".$sentInvoice->company_invoice_id;
                        $sentInvoice->update();
                    }else{
                        $sentInvoice->formatted_id = "rt-".$sentInvoice->company_id."-inv-".$sentInvoice->company_invoice_id;
                        $sentInvoice->update();
                    }
                }else {




                    $findUserInvoiceCount = SentInvoice::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('invoice_id', $invoice->id)->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount = EmailSentInvoice::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('invoice_id', $invoice->id)->pluck('user_invoice_count')->toArray();
                    if (max(array_merge($findUserInvoiceCount, $findUserEmailInvoiceCount)) == 0) {
                        $uniquemax = 0;
                        $sentInvoice->user_invoice_count = $uniquemax + 1;
                        $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                        if ($employeeData->count() == 0) {
                            if ($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id =  $invoice->id . "-1-" . ($uniquemax + 1);
                            }else{
                                $sentInvoice->formatted_id = "RT-" . $invoice->prefix . "-" . $invoice->id . "-1-" . ($uniquemax + 1);
                            }
                        } else {
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id =  $invoice->id . "-" . $employeeData->first()->sn . "-" . ($uniquemax + 1);
                            }else{
                                $sentInvoice->formatted_id = "RT-" . $invoice->prefix . "-" . $invoice->id . "-" . $employeeData->first()->sn . "-" . ($uniquemax + 1);
                            }
                        }
                        $sentInvoice->update();
                    } else {
                        $uniquemax = max(array_merge($findUserInvoiceCount, $findUserEmailInvoiceCount));
                        $sentInvoice->user_invoice_count = $uniquemax + 1;
                        $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                        if ($employeeData->count() == 0) {
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id =  $invoice->id . "-1-" . ($uniquemax + 1);
                            }else{
                                $sentInvoice->formatted_id = "RT-" . $invoice->prefix . "-" . $invoice->id . "-1-" . ($uniquemax + 1);
                            }
                        } else {
                            if($invoice->hide_prefix == 1){
                                $sentInvoice->formatted_id = $invoice->id . "-" . $employeeData->first()->sn . "-" . ($uniquemax + 1);

                            }else{
                                $sentInvoice->formatted_id = "RT-" . $invoice->prefix . "-" . $invoice->id . "-" . $employeeData->first()->sn . "-" . ($uniquemax + 1);

                            }
                        }
                        $sentInvoice->update();
                    }
                }

                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =    InvoiceSetting::where('company_id',$request->header('companyId'));
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
                if($request->isDocketAttached==1){
                    $docketsId = Input::get('dockets');
                    foreach($docketsId as $rowId) {
                        $attachedDocket     =   new SentInvoiceAttachedDocket();
                        $attachedDocket->sent_invoice_id    =   $sentInvoice->id;
                        $attachedDocket->sent_docket_id     =   $rowId;
                        $attachedDocket->save();
                    }
                }

                if ($invoice->docketFolderAssign->count()!=0){
                    $folderItem = new FolderItem();
                    $folderItem->folder_id = $invoice->docketFolderAssign->folder_id;
                    $folderItem->ref_id = $sentInvoice->id;
                    $folderItem->type = 2;
                    $folderItem->user_id = $request->header('userId');
                    $folderItem->status = 0;
                    $folderItem->company_id = $request->header('companyId');
                    if ($folderItem->save()){
                        SentInvoice::where('id',$sentInvoice->id)->update(['folder_status'=>1]);
                    }
                }
                //invoice filed query
                $invoiceFieldsQuery   =  InvoiceField::where('invoice_id',$request->invoice_id)->orderBy('order','asc')->get();
                foreach ($invoiceFieldsQuery as $row){
                    if($row->invoice_field_category_id==9){
                        $invoiceFieldValue   =   new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "signature";
                        $invoiceFieldValue->save();

                        $totalImages    =     Input::get('formFieldSignature'.$row->id.'count');
                        for($i = 0; $i <$totalImages;$i++){
                            $imageField =   'formFieldSignature'.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValue     =    new SentInvoiceImageValue();
                                    $imageValue->sent_invoice_value_id    =  $invoiceFieldValue->id;

                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/signature';
                                    // $image->move($dest, $filename);
                                    // $imageValue->value    =    $dest . '/' . $filename;
                                    $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                    $imageValue->save();
                                }
                            }
                        }
                    } elseif($row->invoice_field_category_id==5){
                        $invoiceFieldValue   =   new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id   =   $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id  =   $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value            =   "image";
                        $invoiceFieldValue->save();
                        $totalImages    =     Input::get('formFieldImage'.$row->id.'count');
                        for($i = 0; $i <$totalImages;$i++){
                            $imageField =   'formFieldImage'.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValue     =    new SentInvoiceImageValue();
                                    $imageValue->sent_invoice_value_id    =  $invoiceFieldValue->id;
                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/image';
                                    // $image->move($dest, $filename);
                                    // $imageValue->value    =    $dest . '/' . $filename;
                                    $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                    $imageValue->save();
                                }
                            }
                        }
                    }
                    else {
                        $invoiceFieldValue = new SentInvoiceValue();
                        $invoiceFieldValue->sent_invoice_id = $sentInvoice->id;
                        $invoiceFieldValue->invoice_field_id = $row->id;
                        $invoiceFieldValue->label  =   $row->label;
                        $invoiceFieldValue->value = ($request->has('formField'.$row->id))?Input::get('formField' . $row->id):"";

                        $invoiceFieldValue->save();
                    }

                    empty($invoiceFieldValue);
                }

                //invoice description query
                $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                for($i = 0; $i < $totalInvoiceDescription;$i++){
                    $invoiceDescription     =    new SentInvoiceDescription();
                    $invoiceDescription->sent_invoice_id    =   $sentInvoice->id;
                    $invoiceDescription->description   =   Input::get('invoiceDescriptionValue'.$i);
                    $invoiceDescription->amount        =   Input::get('invoiceDescriptionAmount'.$i);
                    $invoiceDescription->save();
                    empty($invoiceDescription);
                }
                $total=0;
                foreach ($invoiceDescription->where('sent_invoice_id',$sentInvoice->id)->get() as $item){
                    $total += $item->amount;
                }


                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentDocketInvoice::whereIn('sent_docket_id',Input::get('dockets'))->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                }
                $totalAmount = $total+$invoiceAmount;
                $sentInvoice->amount = $totalAmount;
                $sentInvoice->save();

//                dd($invoice->invoiceXeroSetting->first()->xero_syn_invoice);

                if (CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->count()==1){

                    if ($invoice->syn_xero==1) {
                        $invoiceXeroSetting = new SentInvoiceXero();
                        $invoiceXeroSetting->sent_invoice_id = $sentInvoice->id;
                        $invoiceXeroSetting->company_xero_id = CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->first()->id;
                        $invoiceXeroSetting->xero_invoice_id = 0;
                        if($invoiceXeroSetting->save()){
                            foreach ($invoice->invoiceXeroSetting->first()->xeroInvoiceValue as $items) {
                                if ($items->xero_field_id == 1) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id =$invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();
                                }
                                if ($items->xero_field_id == 2) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id = $invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();
                                }
                                if ($items->xero_field_id == 3) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id = $invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();
                                }
                                if ($items->xero_field_id == 4) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id = $invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();
                                }
                                if ($items->xero_field_id == 5) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id =$invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();

                                }
                                if ($items->xero_field_id == 6) {
                                    $xeroInvoiceValue = new SentXeroInvoiceSetting();
                                    $xeroInvoiceValue->xero_field_id = $items->xero_field_id;
                                    $xeroInvoiceValue->sent_invoice_xero_id = $invoiceXeroSetting->id;
                                    $xeroInvoiceValue->value = $items->value;
                                    $xeroInvoiceValue->save();
                                }
                            }

                        }
                    }

                    $xeroCompanId= CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->first()->id;

                    if(InvoiceXeroSetting::where('invoice_id',$invoice->id)->where('company_xero_id',$xeroCompanId)->first()->xero_syn_invoice==1) {
                        $senderCompany = Company::where('id', $request->header('companyId'))->first();
                        $employessender = Employee::where('company_id', $request->header('companyId'))->pluck('user_id')->toArray();
                        $adminsender = Company::where('id', $request->header('companyId'))->pluck('user_id')->toArray();
                        $totalCompany = array_merge($employessender, $adminsender);
                        if (!in_array($request->receiver_user_id, $totalCompany)) {
                            $reciverType = "company";
                        } else {

                            if (Employee::where('user_id', $request->receiver_user_id)->where('is_admin', 1)->count() == 1) {
                                $reciverType = 'admin';
                            } elseif (Employee::where('user_id', $request->receiver_user_id)->where('employed', 1)->count() == 1) {
                                $reciverType = 'employee';
                            } elseif ($request->receiver_user_id == $senderCompany->user_id) {
                                $reciverType = 'admin';
                            }
                        }

                        if ($request->header('userId') == $senderCompany->user_id) {
                            $senderType = "admin";
                        } elseif (Employee::where('user_id', $request->header('userId'))->where('is_admin', 1)->count() == 1) {
                            $senderType = 'admin';
                        } elseif (Employee::where('user_id', $request->header('userId'))->where('employed', 1)->count() == 1) {
                            $senderType = 'employee';
                        }

                        if ($reciverType == "company") {
                            //from company to user
                            $company = CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->first();
                            $config = [
                                'oauth' => [
                                    'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                                    'consumer_key' => $company->consumer_key,
                                    'consumer_secret' => $company->consumer_secret,
                                    'rsa_private_key' => 'file://' . $company->rsa_private_key,
                                ],
                            ];
                            $xero = new PrivateApplication($config);
                            $invoiceDetail = SentInvoice::where('id', $sentInvoice->id)->first();
                            if (SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                                $synXeroData = SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->first();
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );

                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }
                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }else{
                                $Sentinv = SentInvoice:: where('id', $sentInvoice->id)->first();
                                $user = User::where('id', $Sentinv->receiver_user_id)->first();
                                $employeDetail = array();
                                $employeDetail[] = array(
                                    'id' => $user->id,
                                    'email' => $user->email,
                                    'first_name' => $user->first_name,
                                    'last_name' => $user->last_name,
                                    'contact_name' => $user->last_name . " " . $user->last_name
                                );

                                $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                                $employees->setContactID($this->getRandNum())
                                    ->setClean()
                                    ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                                    ->setFirstName($employeDetail[0]['first_name'])
                                    ->setLastName($employeDetail[0]['last_name'])
                                    ->setEmailAddress($employeDetail[0]['email']);
                                if ($employees->save()) {
                                    if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", $request->header('companyId'))->count() == 0) {
                                        $addXeroContact = new SynXeroContact();
                                        $addXeroContact->contact_name = $employees->Name;
                                        $addXeroContact->first_name = $employees->FirstName;
                                        $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                        $addXeroContact->email = $employees->EmailAddress;
                                        $addXeroContact->xero_contact_id = $employees->ContactId;
                                        $addXeroContact->company_xero_id = $company->id;
                                        $addXeroContact->company_id = $request->header('companyId');
                                        $addXeroContact->save();
                                    }
                                }
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );

                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }

                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }

                        } elseif ($senderType == 'admin' && $reciverType == 'employee') {
                            //company to user
                            $company = CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->first();
                            $config = [
                                'oauth' => [
                                    'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                                    'consumer_key' => $company->consumer_key,
                                    'consumer_secret' => $company->consumer_secret,
                                    'rsa_private_key' => 'file://' . $company->rsa_private_key,
                                ],
                            ];

                            $xero = new PrivateApplication($config);
                            $invoiceDetail = SentInvoice::where('id', $sentInvoice->id)->first();
                            if (SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                                $synXeroData = SynXeroContact::where('email', $invoiceDetail->receiverUserInfo->email)->where('company_xero_id', $company->id)->first();
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );

                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }

                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }else{
                                $Sentinv = SentInvoice:: where('id', $sentInvoice->id)->first();
                                $user = User::where('id', $Sentinv->receiver_user_id)->first();
                                $employeDetail = array();
                                $employeDetail[] = array(
                                    'id' => $user->id,
                                    'email' => $user->email,
                                    'first_name' => $user->first_name,
                                    'last_name' => $user->last_name,
                                    'contact_name' => $user->last_name . " " . $user->last_name
                                );

                                $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                                $employees->setContactID($this->getRandNum())
                                    ->setClean()
                                    ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                                    ->setFirstName($employeDetail[0]['first_name'])
                                    ->setLastName($employeDetail[0]['last_name'])
                                    ->setEmailAddress($employeDetail[0]['email']);
                                if ($employees->save()) {
                                    if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", $request->header('companyId'))->count() == 0) {
                                        $addXeroContact = new SynXeroContact();
                                        $addXeroContact->contact_name = $employees->Name;
                                        $addXeroContact->first_name = $employees->FirstName;
                                        $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                        $addXeroContact->email = $employees->EmailAddress;
                                        $addXeroContact->xero_contact_id = $employees->ContactId;
                                        $addXeroContact->company_xero_id = $company->id;
                                        $addXeroContact->company_id = $request->header('companyId');
                                        $addXeroContact->save();
                                    }
                                }
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );

                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCREC")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setDiscountRate($sendXeroSetting[4]['value']);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }

                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }
                        } elseif ($senderType == 'employee' && $reciverType == 'admin') {
                            //user to Employee
                            $company = CompanyXero::where('company_id', $request->header('companyId'))->where('status', 1)->first();
                            $config = [
                                'oauth' => [
                                    'callback' => 'https://www.recordtimeapp.com.au/rtBeta/dashboard/company/xero/Invoices',
                                    'consumer_key' => $company->consumer_key,
                                    'consumer_secret' => $company->consumer_secret,
                                    'rsa_private_key' => 'file://' . $company->rsa_private_key,
                                ],
                            ];

                            $xero = new PrivateApplication($config);

                            $invoiceDetail = SentInvoice::where('id', $sentInvoice->id)->first();
                            if (SynXeroContact::where('email', $invoiceDetail->senderUserInfo->email)->where('company_xero_id', $company->id)->count() != 0) {
                                $synXeroData = SynXeroContact::where('email', $invoiceDetail->senderUserInfo->email)->where('company_xero_id', $company->id)->first();
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );
                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $synXeroData->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCPAY")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }

                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            } else {
                                $Sentinv = SentInvoice:: where('id', $sentInvoice->id)->first();
                                $user = User::where('id', $Sentinv->user_id)->first();
                                $employeDetail = array();
                                $employeDetail[] = array(
                                    'id' => $user->id,
                                    'email' => $user->email,
                                    'first_name' => $user->first_name,
                                    'last_name' => $user->last_name,
                                    'contact_name' => $user->last_name . " " . $user->last_name
                                );

                                $employees = new \XeroPHP\Models\Accounting\Contact($xero);
                                $employees->setContactID($this->getRandNum())
                                    ->setClean()
                                    ->setName($employeDetail[0]['first_name'] . ' ' . $employeDetail[0]['last_name'])
                                    ->setFirstName($employeDetail[0]['first_name'])
                                    ->setLastName($employeDetail[0]['last_name'])
                                    ->setEmailAddress($employeDetail[0]['email']);
                                if ($employees->save()) {
                                    if (SynXeroContact::where("email", $employees->EmailAddress)->where("company_id", $request->header('companyId'))->count() == 0) {
                                        $addXeroContact = new SynXeroContact();
                                        $addXeroContact->contact_name = $employees->Name;
                                        $addXeroContact->first_name = $employees->FirstName;
                                        $addXeroContact->last_name = ($employees->LastName == "") ? " " : $employees->LastName;
                                        $addXeroContact->email = $employees->EmailAddress;
                                        $addXeroContact->xero_contact_id = $employees->ContactId;
                                        $addXeroContact->company_xero_id = $company->id;
                                        $addXeroContact->company_id = $request->header('companyId');
                                        $addXeroContact->save();
                                    }
                                }


                                // $synXeroData=SynXeroContact::where('email',$employees->EmailAddress)->where('company_id',Session::get('company_id'))->first();
                                $sendXeroSetting = array();
                                foreach ($invoiceDetail->sentInvoiceXero->first()->sentXeroInvoiceSettingInfo as $data) {
                                    $sendXeroSetting[] = array(
                                        'id' => $data->id,
                                        'xero_field_id' => $data->xero_field_id,
                                        'value' => $data->value,
                                    );
                                }
                                $contacts = $xero->loadByGUID('Accounting\\Contact', $addXeroContact->xero_contact_id);
                                $invoice = new \XeroPHP\Models\Accounting\Invoice($xero);
                                $invoice->setType("ACCPAY")->setContact($contacts)->setReference($sentInvoice->id)->setDueDate(\DateTime::createFromFormat('Y-m-d', Carbon::parse($invoiceDetail->created_at)->addDay($sendXeroSetting[1]['value'])->format('Y-m-d')))->setLineAmountType($sendXeroSetting[0]['value']);
                                if ($invoiceDetail->invoiceDescription->count() != 0) {
                                    foreach ($invoiceDetail->invoiceDescription as $detail) {
                                        $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                        $xeroLineItem->setQuantity(1);
                                        $xeroLineItem->setDescription($detail->description);
                                        $xeroLineItem->setUnitAmount($detail->amount);
                                        $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                        $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                        $invoice->addLineItem($xeroLineItem);
                                        $invoice->save();
                                    }
                                }
                                if ($invoiceDetail->attachedDocketsInfo->count() != 0) {
                                    foreach ($invoiceDetail->attachedDocketsInfo as $invoiceDocket) {
                                        $invoiceAmountQuery = \App\SentDocketInvoice::where('sent_docket_id', $invoiceDocket->docketInfo->id)->where('type', 2)->get();
                                        $invoiceAmount = 0;
                                        foreach ($invoiceAmountQuery as $amount) {
                                            $unitRate = $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                            $xeroLineItem = new \XeroPHP\Models\Accounting\Invoice\LineItem($xero);
                                            $xeroLineItem->setQuantity(1);
                                            $xeroLineItem->setDescription($invoiceDocket->docketInfo->docketInfo->title . ' ' . '/' . ' ' . 'Doc' . $invoiceDocket->docketInfo->id);
                                            $xeroLineItem->setUnitAmount($invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"]);
                                            $xeroLineItem->setAccountCode(explode('-', $sendXeroSetting[2]['value'])[0]);
                                            $xeroLineItem->setTaxType(explode('-', $sendXeroSetting[3]['value'])[0]);
                                            $invoice->addLineItem($xeroLineItem);
                                            $invoice->save();
                                        }

                                    }
                                }
                                SentInvoiceXero::where('id', $invoiceXeroSetting->id)->update(['xero_invoice_id' => $invoice->InvoiceID]);
                            }


                        }
                    }


                }

                $sentInvoiceReceiverInfo    =    User::where('id',$request->receiver_user_id)->first();
                $userNotification   =   new UserNotification();
                $userNotification->sender_user_id   =   $request->header('userId');
                $userNotification->receiver_user_id =   $sentInvoiceReceiverInfo->id;
                $userNotification->type     =   2;
                $userNotification->title    =   '';
                $userNotification->message  =   $request->message;
                $userNotification->key      =   $sentInvoice->id;
                $userNotification->status   =   0;
                $userNotification->save();

                if($sentInvoiceReceiverInfo->device_type == 2){
                    sendiOSNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));

                }else if($sentInvoiceReceiverInfo->device_type == 1){
                    sendAndroidNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));
                }
            endif;  //check email template flag end

            return response()->json(array('status' => true, 'message' => 'Invoice successfully sent to '.$sentInvoice->receiverUserInfo->first_name." ".$sentInvoice->receiverUserInfo->last_name));
        endif;
    }

    public function getRandNum()
    {
        $randNum = strval(rand(1000,100000));
        return $randNum;
    }

    public function getLatestInvoiceHome(Request $request){
        $conversationArray   =   array();
        $sentInvoiceQuery    =   SentInvoice::where('user_id', $request->header('userId'))
            ->orWhere('receiver_user_id', $request->header('userId'));

        if($sentInvoiceQuery->count()>0){
            foreach ($sentInvoiceQuery->orderBy('created_at','desc')->take(10)->get() as $result) {
                $userId = $result->user_id;
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $profile = AmazoneBucket::url() . $result->senderUserInfo->image;
                $company = $result->senderCompanyInfo->name;

                if ($result->user_id == $request->header('userId')) {
                    if ($result->status == 0):
                        $invoiceStatus = "Sent";
                    endif;
                } else {
                    if ($result->status == 0):
                        $invoiceStatus = "Received";
                    endif;
                }

                if ($result->status == 1)
                    $invoiceStatus = "Approved";

                $conversationArray[] = array('id' => $result->id,
                    'companyInvoiceId'=>$result->formatted_id,
                    'user_id' => $userId,
                    'invoiceName' => $result->invoiceInfo->title,
                    'sender' => $userName,
                    'profile' => $profile,
                    'company' => $company,
                    'receiver'  => $result->receiverUserInfo->first_name." ".$result->receiverUserInfo->last_name,
                    'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' => Carbon::parse($result->created_at)->format('d-M-Y h:i:s'),
                    'status' => $invoiceStatus);
            }
        }

//        conversation sorting according to dateAdded
        $size = count($conversationArray);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($conversationArray[$j+1]["dateSorting"]) > strtotime($conversationArray[$j]["dateSorting"])) {
                    $tempArray   =    $conversationArray[$j+1];
                    $conversationArray[$j+1] = $conversationArray[$j];
                    $conversationArray[$j]  =   $tempArray;
                }
            }
        }

        return response()->json(array('status' => true, 'invoices' =>$conversationArray));
    }

    public function getLatestInvoiceList(Request $request){
        $conversationArray      =   array();

        $added_company_idQuery         =   Client::where("company_id",$request->header('companyId'))->orWhere('requested_company_id',$request->header('companyId'))->get();
        $added_company_id   =   array();
        $added_company_id[] =   $request->header('companyId');
        foreach ($added_company_idQuery as $row){
            if($row->company_id==$request->header('companyId')){
                $added_company_id[] =   $row->requested_company_id;
            }else {
                $added_company_id[] =   $row->company_id;
            }
        }


        $employeeId     =   array();
        $employeeIdQuery  =   Employee::whereIn('company_id',$added_company_id)->get();
        foreach ($employeeIdQuery as $row){
            $employeeId[]   =   $row->user_id;
        }
        //add other company's superadmin
        foreach ($added_company_id as $row) {
            $employeeId[] 	=	Company::where('id',$row)->first()->user_id;
        }


        foreach ($employeeId as $userId) {
            $sentInvoiceQuery    =    SentInvoice::where(function($query) use ($request,$userId){
                return $query->where('user_id', $request->header('userId'))
                    ->where('receiver_user_id', $userId);
            })->orWhere(function($query) use($request,$userId) {
                return $query->where('receiver_user_id', $request->header('userId'))
                    ->where('user_id', $userId);
            });

            if($sentInvoiceQuery->count()>0){
                $result = $sentInvoiceQuery->orderBy('created_at','desc')->first();


                $profile = "";
                if($result->user_id==$request->header('userId')){

                    $userId  = 	$result->receiver_user_id;
                    $userName  =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $profile    =    AmazoneBucket::url() . $result->receiverUserInfo->image;
                    $company    =   $result->receiverCompanyInfo->name;

                    if($result->status==0):
                        $invoiceStatus   =   "Sent";
                    endif;
                } else {
                    $userId  = 	$result->user_id;
                    $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $profile    =    AmazoneBucket::url() . $result->senderUserInfo->image;
                    $company    =   $result->senderCompanyInfo->name;

                    if($result->status==0):
                        $invoiceStatus   =   "Received";
                    endif;
                }

                if($result->status==1)
                    $invoiceStatus ="Approved";

                $conversationArray[]   =   array('id' => $result->id,
                    'user_id'   =>  $userId,
                    'invoiceName' => $result->invoiceInfo->title,
                    'sender' => $userName,
                    'profile' => $profile,
                    'company'   =>  $company,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y h:i:s'),
                    'status'    => $invoiceStatus);
            }
        }

//        conversation sorting according to dateAdded
        $size = count($conversationArray);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($conversationArray[$j+1]["dateSorting"]) > strtotime($conversationArray[$j]["dateSorting"])) {
                    $tempArray   =    $conversationArray[$j+1];
                    $conversationArray[$j+1] = $conversationArray[$j];
                    $conversationArray[$j]  =   $tempArray;
                }
            }
        }

        return response()->json(array('status' => true, 'invoices' =>$conversationArray));
    }

    public function getConversationInvoiceChatByUserId(Request $request, $userId){
        $sentInvoiceQuery    =    SentInvoice::where(function($query) use ($request, $userId){
            return $query->where('user_id', $request->header('userId'))
                ->where('receiver_user_id', $userId);
        })->orWhere(function($query) use($userId, $request) {
            return $query->where('receiver_user_id', $request->header('userId'))
                ->where('user_id', $userId);
        });

        if($sentInvoiceQuery->count()>0){
            $resultQuery = $sentInvoiceQuery->orderBy('created_at','desc')->get();

            foreach ($resultQuery as $result){
                if($result->company_id==$request->header('companyId')):
                    $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $company    =   $result->senderCompanyInfo->name;
                    if($result->status==0):
                        if($result->receiver_user_id==$request->header('userId')){
                            $invoiceStatus   =   "Received";
                        }else{
                            $invoiceStatus   =   "Sent";
                        }
                    endif;
                else :
                    $userName   =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $company    =   $result->receiverCompanyInfo->name;
                    if($result->status==0):
                        $invoiceStatus   =   "Received";
                    endif;
                endif;
                if($result->status==1)
                    $invoiceStatus ="Approved";

                $conversationArray[]   =   array('id' => $result->id,
                    'user_id'   =>  $result->user_id,
                    'invoiceName' => $result->invoiceInfo->title,
                    'sender' => $userName,
                    'company'   =>  $company,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $invoiceStatus);
            }
        }
        return response()->json(array('status' => true, 'invoices' =>$conversationArray));
    }

    public function getInvoiceDetailsById(Request $request, $id){

        $sentInvoice     =   SentInvoice::where('id',$id);
        if($sentInvoice->count()==1):
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');
            if($sentInvoice->where('company_id',$companyId)->orWhere('receiver_company_id',$companyId)->count()>0){
                $sentInvoiceValueQuery    =    SentInvoiceValue::where('sent_invoice_id',$id)->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }
                $invoice     =     SentInvoice::where('id',$id)->first();
                $data = array();
                $data['full_name']= $invoice->sender_name;
                $data['company_name']= $invoice->company_name;
                $data['address']= $invoice->company_address;
                $companyDetails =   Company::where('id',$invoice->company_id)->first();


                $invoiceDescription     =    SentInvoiceDescription::where('sent_invoice_id',$invoice->id)->get();


//                $invoiceAttachedDockets     =    SentInvoiceAttachedDocket::where("sent_invoice_id",$id)->get();/

                $invoiceSetting =   array();
                //check invoice payment info
                if(SentInvoicePaymentDetail::where('sent_invoice_id',$id)->count()==1){
                    $invoiceSetting =   SentInvoicePaymentDetail::where('sent_invoice_id',$id)->first();
                }

                $userNotificationQuery  =   UserNotification::where('type',4)->where('receiver_user_id',$request->header('userId'))->where('key',$id);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        UserNotification::where('type',4)->where('receiver_user_id',$request->header('userId'))->where('key',$id)->update(['status'=>1]);
                    }
                }
                if (SentInvoice::where('id',$id)->first()->theme_document_id == 0){
                    // return view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                    return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                }else{
                    if(DocumentTheme::where('id', SentInvoice::where('id',$id)->first()->theme_document_id)->count()==0){
                        return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                    }else{
                        $theme = DocumentTheme::where('id', SentInvoice::where('id',$id)->first()->theme_document_id)->first();
                        //  return view('dashboard/company/themes/'.$theme->slug.'/mobile',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                        return response()->json(array('status' => true, 'invoice' => view('dashboard/company/themes/'.$theme->slug.'/mobile',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                    }

                }

                // return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'))->render()));
            }
            else {
                echo "not authorized";
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;

    }

    public function getEmailInvoiceDetailsById(Request $request, $id){

        $sentInvoice     =   EmailSentInvoice::where('id',$id);
        if($sentInvoice->count()==1):
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');
            if($sentInvoice->where('company_id',$companyId)->count()>0){
                $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$id)->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }

                $invoice     =     EmailSentInvoice::where('id',$id)->first();
                $data = array();
                $data['full_name']= $invoice->receiverInfo->email;
                $data['company_name']= $invoice->receiver_company_name;
                $data['address']= $invoice->receiver_company_address;
                $companyDetails =   Company::where('id',$invoice->company_id)->first();


                $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$invoice->id)->get();

                $invoiceSetting =   array();
                //check invoice payment info
                if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->count()==1){
                    $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->first();
                }
                if (SentInvoice::where('id',$id)->first()->theme_document_id == 0){
                    return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.emailInvoicePreview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                }else{
                    if(DocumentTheme::where('id', SentInvoice::where('id',$id)->first()->theme_document_id)->count()==0){
                        return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.emailInvoicePreview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                    }else{
                        $theme = DocumentTheme::where('id', SentInvoice::where('id',$id)->first()->theme_document_id)->first();
                        //  return view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));

                        //email recipients
                        $recipients     =   array('email' => $sentInvoice->first()->receiverInfo->email,
                            'shareableLink' => url('invoice/emailed',array($sentInvoice->first()->encryptedID(),$sentInvoice->first()->receiverInfo->encryptedID())));

                        return response()->json(array('status' => true,
                                                        'invoice' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render(),
                                                'recipient' => $recipients));
                    }
                }
            }
            else {
                echo "not authorized";
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;

    }

    public function getInvoiceTimelineByUserId(Request $request,$id){
        $sentInvoiceQuery    =    SentInvoice::where(function($query) use ($request, $id){
            return $query->where('user_id', $request->header('userId'))
                ->where('receiver_user_id', $id);
        })->orWhere(function($query) use($id, $request) {
            return $query->where('receiver_user_id', $request->header('userId'))
                ->where('user_id', $id);
        });
        $sentInvoiceId  =    $sentInvoiceQuery->pluck('id');
        $sentInvoiceDates   =    $sentInvoiceQuery->where('created_at', '<=',Carbon::now())->groupBy('date')->groupBy('date')->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))->toArray();
        $conversationArray = array();
        foreach ($sentInvoiceDates as $sentInvoiceDate) {
            $sentInvoiceArray    =   array();
            $dateWiseQuery  =    SentInvoice::whereIn('id',$sentInvoiceId)->whereDate('created_at',$sentInvoiceDate)->orderBy('created_at','desc')->get();
            $invoices =   array();
            foreach ($dateWiseQuery as $result){
                // if($result->company_id==$request->header('companyId')):
                $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;
                if($result->status==0):
                    if($result->receiver_user_id==$request->header('userId')){
                        $invoiceStatus   =   "Received";
                    }else{
                        $invoiceStatus   =   "Sent";
                    }
                endif;
                // else :
                //     $userName   =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                //     $company    =   $result->receiverCompanyInfo->name;
                //     if($result->status==0):
                //         $invoiceStatus   =   "Received";
                //     endif;
                // endif;
                if($result->status==1)
                    $invoiceStatus ="Approved";

                $invoices[]   =   array('id' => $result->id,
                    'companyInvoiceId'=>'rt-'.$result->company_id.'-inv-'.$result->company_invoice_id,
                    'user_id'   =>  $result->user_id,
                    'invoiceName' => $result->invoiceInfo->title,
                    'sender' => $userName,
                    'recipients'    =>  $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name,
                    'profile'=> AmazoneBucket::url() . $result->senderUserInfo->image,
                    'company'   =>  $company,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $invoiceStatus);
            }

            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentInvoiceDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentInvoiceDate['date'])->format('l')), 'invoices'   =>   $invoices);
            unset($invoices);
        }
        return response()->json(array('status' => true, 'timeline' => $conversationArray));
    }

}
