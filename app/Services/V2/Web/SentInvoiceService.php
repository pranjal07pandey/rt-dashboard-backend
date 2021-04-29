<?php
namespace App\Services\V2\Web;

use App\Jobs\SentInvoiceJob;
use App\Services\V2\ConstructorService;
use Validator;
use Illuminate\Support\Facades\Input;
use PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Helpers\V2\FunctionUtils;
use Illuminate\Http\Request;
use App\Helpers\V2\MessageDisplay;
use App\Jobs\SentInvoicePdfSaveJob;

class SentInvoiceService extends ConstructorService {

    public function send($request){
        $validator  =   Validator::make(Input::all(),['templateId' =>     'required', 'recipientId'  =>  'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            try{
                DB::beginTransaction();
                $isDocketAttached = 0;
                if ($request->invoiceableDocketId != null){
                    $invoiceableDocketId = explode( ',', $request->invoiceableDocketId);
                    if (count($invoiceableDocketId)>0){
                        $isDocketAttached = 1;
                    }
                }
                $date = Carbon::now()->format('M-d-Y');
                $company = auth()->user()->companyInfo;
                $authUser = auth()->user();

                if ($request->isemail == 1){
                    $recipientIdData =  $request->recipientId;
                    $template = $request->templateId;
                    $invDoc = $request->invoiceableDocketId;

                    //check employee
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
                    if ($this->invoiceRepository->getDataWhere([['id',$template],['company_id',$company->id]])->count() == 0){
                        return response()->json(array('status' => false,'message' => "Invoice Id Doesnt match"));
                    }
                    //check invoiceableDocket
                    if ($invDoc != null){
                        $invoiceableDocket = explode( ',', $invDoc);
                        $totalSentDocketID  =   array();
                        $admin  =   array();
                        $admin    = $this->employeeRepository->getDataWhere([['company_id',$company->id],['is_admin',1],['employed',1]])->pluck('user_id')->toArray();
                        $admin[]   =   $company->user_id;

                        if(in_array($authUser->id,$admin)){
                            $sentDocketQueryTemp    =  $this->sentDocketsRepository->getDataWhere([['sender_company_id',$company->id],['invoiceable',1]])->orderBy('id','desc')->get();
                        }else{
                            $sentDocketQueryTemp    = $this->sentDocketsRepository->getDataWhere([['user_id',$authUser->id],['invoiceable',1]])->orderBy('id','desc')->get();
                        }
                        foreach($sentDocketQueryTemp as $sentDocket){
                            if ($sentDocket->recipientInfo->count() == 1){
                                if ($sentDocket->recipientInfo->first()->user_id == $recipientIdData) {
                                    $totalSentDocketID[] = $sentDocket->id;
                                }
                            }else if($sentDocket->recipientInfo->count()>=2){
                                $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
                                if (FunctionUtils::array_equal($tempSentDocketRecipient,array($authUser->id,$recipientIdData))) {
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

                    $invoice    = $this->invoiceRepository->getDataWhere([['id',$request->templateId]])->first();
                    $companyInvoice = $company;
                    $invoiceuserFullname = auth()->user();
                    $sentInvoiceRequest =     new Request();
                    $sentInvoiceRequest['user_id']    =    $invoiceuserFullname->id;
                    $sentInvoiceRequest['abn']                =      $companyInvoice->abn;
                    $sentInvoiceRequest['company_name']       =      $companyInvoice->name;
                    $sentInvoiceRequest['company_address']    =      $companyInvoice->address;
                    $sentInvoiceRequest['company_logo']    =      $companyInvoice->logo;
                    $sentInvoiceRequest['sender_name']        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                    $sentInvoiceRequest['invoice_id']  =   $request->templateId;
                    $sentInvoiceRequest['user_invoice_count'] = 0;
                    $sentInvoiceRequest['receiver_user_id']   =   $request->recipientId;
                    $recipientEmployeeList = $this->employeeRepository->getDataWhere([['user_id', $request->recipientId]])->first();
                    if($recipientEmployeeList != null):
                        $companyId = $recipientEmployeeList->company_id;
                    else :
                        $companyId   = $this->companyRepository->getDataWhere([['user_id', $request->recipientId]])->first()->id;
                    endif;
                    $sentInvoiceRequest['receiver_company_id']         =   $companyId;
                    $sentInvoiceRequest['company_id']	=  $company->id;
                    $sentInvoiceRequest['status']             =   0;
                    $sentInvoiceRequest['amount']        =   0;
                    $sentInvoiceRequest['isDocketAttached']  =  $isDocketAttached;
                    if($invoice->gst==1){
                        $sentInvoiceRequest['gst']           =   $invoice->gst_value;
                    }
                    if($companyInvoice->number_system == 1){
                        $sentInvoiceDb = $this->sentInvoiceRepository->getDataWhere([['company_id', $company->id]])->pluck('company_invoice_id');
                        if ($sentInvoiceDb->count()== 0){
                            $sentInvoiceRequest['company_invoice_id'] = 1;
                        }else{
                            $companyDocketId =  $sentInvoiceDb->toArray();;
                            $sentInvoiceRequest['company_invoice_id'] = max($companyDocketId) + 1;
                        }
                    }else{
                        $sentInvoiceRequest['company_invoice_id']  = 0;
                    }
                    $sentInvoice = $this->sentInvoiceRepository->insertAndUpdate($sentInvoiceRequest);


                    $findUserInvoiceCount = $this->sentInvoiceRepository->getDataWhere([['user_id', $authUser->id],['company_id', $company->id],['invoice_id',$invoice->id]])->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount = $this->emailSentInvoiceRepository->getDataWhere([['user_id', $authUser->id],['company_id', $company->id],['invoice_id',$invoice->id]])->pluck('user_invoice_count')->toArray();
                    if(max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount)) == 0){
                        $uniquemax = 0;
                    }else{
                        $uniquemax = max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount));
                    }

                    $sentInvoice->user_invoice_count = $uniquemax+1;
                    $employeeData = $this->employeeRepository->getDataWhere([['user_id', $authUser->id],['company_id', $company->id]])->get();
                    if($employeeData->count() == 0){
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-1-".($uniquemax+1);
                    }else{
                        $sentInvoice->formatted_id = "RT-".$invoice->prefix."-".$invoice->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                    $sentInvoice->update();



                    //check invoice payment info and save it into sent invoice payment details table
                    $invoiceSettingQuery     =  $this->invoiceSettingRepository->getDataWhere([['company_id',$company->id]]);
                    if($invoiceSettingQuery->count()==1){
                        $invoiceSetting =   $invoiceSettingQuery->first();
                        $sentInvoicePaymentDetailsRequest  =    new Request();
                        $sentInvoicePaymentDetailsRequest['sent_invoice_id'] =   $sentInvoice->id;
                        $sentInvoicePaymentDetailsRequest['company_id']      =   $sentInvoice->company_id;
                        $sentInvoicePaymentDetailsRequest['bank_name']       =   $invoiceSetting->bank_name;
                        $sentInvoicePaymentDetailsRequest['account_name']	=   $invoiceSetting->account_name;
                        $sentInvoicePaymentDetailsRequest['bsb_number']      =   $invoiceSetting->bsb_number;
                        $sentInvoicePaymentDetailsRequest['account_number']  =   $invoiceSetting->account_number;
                        $sentInvoicePaymentDetailsRequest['instruction']     =   $invoiceSetting->instruction;
                        $sentInvoicePaymentDetailsRequest['additional_information']  =   $invoiceSetting->additional_information;
                        $this->sentInvoicePaymentDetailRepository->insertAndUpdate($sentInvoicePaymentDetailsRequest);
                    }

                    if($isDocketAttached==1){
                        foreach($invoiceableDocketId as $rowId) {
                            $attachedDocketRequest     =   new Request();
                            $attachedDocketRequest['sent_invoice_id']    =   $sentInvoice->id;
                            $attachedDocketRequest['sent_docket_id']     =   $rowId;
                            $this->sentInvoiceAttachedDocketRepository->insertAndUpdate($attachedDocketRequest);
                        }
                    }

                    if (@$invoice->docketFolderAssign!=null){
                        $folderItemRequest = new Request();
                        $folderItemRequest['folder_id'] = $invoice->docketFolderAssign->folder_id;
                        $folderItemRequest['ref_id'] = $sentInvoice->id;
                        $folderItemRequest['type'] = 2;
                        $folderItemRequest['user_id'] = $authUser->id;
                        $folderItemRequest['status'] = 0;
                        $folderItemRequest['company_id'] = $company->id;
                        $this->folderItemRepository->insertAndUpdate($folderItemRequest);

                        $this->sentInvoiceRepository->getDataWhere([['id',$sentInvoice->id]])->update(['folder_status'=>1]);
                    }


                    //invoice filed query
                    $invoiceFieldsQuery   = $this->invoiceFieldRepository->getDataWhere([['invoice_id',$request->templateId]])->orderBy('order','asc')->get();
                    $signatureSN=1;
                    foreach ($invoiceFieldsQuery as $row){
                        $invoiceFieldValueRequest   =   new Request();
                        $invoiceFieldValueRequest['sent_invoice_id']   =   $sentInvoice->id;
                        $invoiceFieldValueRequest['invoice_field_id']  =   $row->id;
                        $invoiceFieldValueRequest['label']  =   $row->label;
                        if($row->invoice_field_category_id==9){
                            
                            $invoiceFieldValueRequest['value']            =   "signature";
                            
                        }elseif($row->invoice_field_category_id==5){
                            $invoiceFieldValueRequest['value']            =   "image";
                            
                        } else if($row->invoice_field_category_id==12){
                            $invoiceFieldValueRequest['value'] =  $row->label;
                        }
                        $invoiceFieldValue = $this->sentInvoiceValueRepository->insertAndUpdate($invoiceFieldValueRequest);

                        if($row->invoice_field_category_id==9 || $row->invoice_field_category_id==5){
                            if($row->invoice_field_category_id==9){
                                $signatureSN = $this->sentInvoiceImageValue($row,$invoiceFieldValue->id,$date,$authUser,'formFieldSignature','signature',$signatureSN);
                            }else{
                                $this->sentInvoiceImageValue($row,$invoiceFieldValue->id,$date,$authUser,'formFieldImage','image',$signatureSN);
                            }
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
                                $invoiceDescriptionRequest     =    new Request();
                                $invoiceDescriptionRequest['sent_invoice_id']    =   $sentInvoice->id;
                                $invoiceDescriptionRequest['description']   =   Input::get('invoiceDescriptionValue'.$i);
                                $invoiceDescriptionRequest['amount']        =   Input::get('invoiceDescriptionAmount'.$i);

                                $invoiceDescription = $this->sentInvoiceDescriptionRepository->insertAndUpdate($invoiceDescriptionRequest);

                                $emailTotal=0;
                                foreach ($this->sentInvoiceDescriptionRepository->getDataWhere([['sent_invoice_id',$sentInvoice->id]])->get() as $item){
                                    $emailTotal += $item->amount;
                                }
                                $invoiceAmount  =    0;
                                if($isDocketAttached==1){
                                    $invoiceAmountQuery    =   $this->sentDocketInvoiceRepository->getDataWhere([['type',2]])->whereIn('sent_docket_id',$invoiceableDocketId)->get();
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

                    $sentInvoiceReceiverInfo    =  $this->userRepository->getDataWhere([['id',$request->recipientId]])->first();
                    $userNotificationRequest   =   new Request();
                    $userNotificationRequest['sender_user_id']   =   $authUser->id;
                    $userNotificationRequest['receiver_user_id'] =   $sentInvoiceReceiverInfo->id;
                    $userNotificationRequest['type']     =   4;
                    $userNotificationRequest['title']    =   'New Invoice';
                    $userNotificationRequest['message']  =   "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name;
                    $userNotificationRequest['key']      =   $sentInvoice->id;
                    $userNotificationRequest['status']   =   0;
                    $userNotification = $this->userNotificationRepository->insertAndUpdate($userNotificationRequest);
                    if($sentInvoiceReceiverInfo->device_type == 2){
                        $this->firebaseApi->sendiOSNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));

                    }else if($sentInvoiceReceiverInfo->device_type == 1){
                        $this->firebaseApi->sendAndroidNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));
                    }

                    DB::commit();
                    return response()->json(['status'=>true ,'data'=>'invoiceManager/allInvoice']);
                }
                else if ($request->isemail == 2){

                    $recipientIdData =  $request->emailrecipientId;
                    $template = $request->templateId;
                    $invDoc = $request->invoiceableDocketId;
                    //check email employee
                    $emailRecepients = [];
                    $emailClient          = $this->emailClientRepository->getDataWhere([["company_id",$company->id]])->get();
                    foreach ($emailClient as $emailClients ){
                        $emailRecepients[] = $emailClients->emailUser->id;
                    }
                    if (!in_array($recipientIdData,$emailRecepients)){
                        return response()->json(array('status' => false,'message' => "Email Employee Not Found"));
                    }

                    //check invoiceId
                    if ($this->invoiceRepository->getDataWhere([['id',$template],['company_id',$company->id]])->count() == 0){
                        return response()->json(array('status' => false,'message' => "Invoice Id Doesnt match"));
                    }

                    if ($invDoc != null){
                        $invoiceableDocket = explode( ',', $invDoc);
                        $emailSentDocket = $this->emailSentDocketRepository->getDataWhere([['user_id',$authUser->id],['company_id', $company->id]])->get();
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


                    $invoice    = $this->invoiceRepository->getDataWhere([['id',$request->templateId]])->first();
                    $companyInvoice = $company;
                    $invoiceuserFullname = $authUser;
                    $sentInvoiceRequest =     new Request();
                    $sentInvoiceRequest['abn']                =      $companyInvoice->abn;
                    $sentInvoiceRequest['company_name']       =      $companyInvoice->name;
                    $sentInvoiceRequest['company_address']    =      $companyInvoice->address;
                    $sentInvoiceRequest['company_logo']    =      $companyInvoice->logo;
                    $sentInvoiceRequest['template_title']    =   $invoice->title;
                    $sentInvoiceRequest['sender_name']        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                    $sentInvoiceRequest['invoice_id']  =   $request->templateId;
                    $sentInvoiceRequest['user_id']    =   $authUser->id;
                    $sentInvoiceRequest['company_id']	=   $company->id;
                    $sentInvoiceRequest['receiver_user_id']   =   $request->emailrecipientId;

                    $emailClient = $this->emailClientRepository->getDataWhere([['company_id', $company->id],['email_user_id', $request->emailrecipientId]])->first();
                    $sentInvoiceRequest['receiver_full_name'] = $emailClient->full_name;
                    $sentInvoiceRequest['receiver_company_name'] = $emailClient->company_name;
                    $sentInvoiceRequest['receiver_company_address'] = $emailClient->company_address;


                    $totalSum = 0;
                    $totalInvoiceDescription1    =     Input::get('invoiceDescriptionCount');
                    for($i = 0; $i <$totalInvoiceDescription1;$i++){
                        $totalSum +=   Input::get('invoiceDescriptionAmount'.$i);
                    }

                    $sentInvoiceRequest['amount'] =$totalSum;
                    if($invoice->gst==1){
                        $sentInvoiceRequest['gst']           =   $invoice->gst_value;
                    }
                    $sentInvoiceRequest['isDocketAttached']  =   $isDocketAttached;
                    $sentInvoiceRequest['hashKey']           =   FunctionUtils::generateRandomString();
                    $sentInvoiceRequest['status']            =   0;


                    if($companyInvoice->number_system == 1){
                        $emailSentInvoiceDb = $this->emailSentInvoiceRepository->getDataWhere([['company_id',$company->id]])->pluck('company_invoice_id');
                        if ($emailSentInvoiceDb->count()== 0){
                            $sentInvoiceRequest['company_invoice_id'] = 1;
                        }else{
                            $companyDocketId =  $emailSentInvoiceDb->toArray();
                            $sentInvoiceRequest['company_invoice_id'] = max($companyDocketId) + 1;
                        }
                    }else{
                        $sentInvoiceRequest['company_invoice_id']  = 0;
                    }

                    $sentInvoice = $this->emailSentInvoiceRepository->insertAndUpdate($sentInvoiceRequest);

                    //check invoice payment info and save it into sent invoice payment details table
                    $invoiceSettingQuery     =  $this->invoiceSettingRepository->getDataWhere([['company_id',$company->id]]);
                    if($invoiceSettingQuery->count()==1){
                        $invoiceSetting =   $invoiceSettingQuery->first();

                        $sentInvoicePaymentDetailsRequest                          =    new Request();
                        $sentInvoicePaymentDetailsRequest['email_sent_invoice_id']   =   $sentInvoice->id;
                        $sentInvoicePaymentDetailsRequest['company_id']              =   $sentInvoice->company_id;
                        $sentInvoicePaymentDetailsRequest['bank_name']               =   $invoiceSetting->bank_name;
                        $sentInvoicePaymentDetailsRequest['account_name']	        =   $invoiceSetting->account_name;
                        $sentInvoicePaymentDetailsRequest['bsb_number']              =   $invoiceSetting->bsb_number;
                        $sentInvoicePaymentDetailsRequest['account_number']          =   $invoiceSetting->account_number;
                        $sentInvoicePaymentDetailsRequest['instruction']             =   $invoiceSetting->instruction;
                        $sentInvoicePaymentDetailsRequest['additional_information']  =   $invoiceSetting->additional_information;
                        $sentInvoicePaymentDetails = $this->emailSentInvoicePaymentDetailRepository->insertAndUpdate($sentInvoicePaymentDetailsRequest);
                    }

                    if($isDocketAttached==1){
                        foreach($invoiceableDocketId as $rowId) {
                            $attachedEmailDocketRequest     =   new Request();
                            $attachedEmailDocketRequest['sent_email_invoice_id']    =   $sentInvoice->id;
                            $attachedEmailDocketRequest['sent_email_docket_id']     =   $rowId;
                            $attachedEmailDocket = $this->sentEInvoiceAttachedEDocketRepository->insertAndUpdate($attachedEmailDocketRequest);
                        }
                    }

                    //invoice filed query
                    $invoiceFieldsQuery   =  $this->invoiceFieldRepository->getDataWhere([['invoice_id',$request->templateId]])->orderBy('order','asc')->get();
                    foreach ($invoiceFieldsQuery as $row){

                        $invoiceFieldValueRequest   =   new Request();
                        $invoiceFieldValueRequest['email_sent_invoice_id']   =   $sentInvoice->id;
                        $invoiceFieldValueRequest['invoice_field_id']  =   $row->id;
                        $invoiceFieldValueRequest['label']  =   $row->label;
                        if($row->invoice_field_category_id==9){
                            $invoiceFieldValueRequest['value']            =   "signature";
                        }elseif($row->invoice_field_category_id==5){
                            $invoiceFieldValueRequest['value']            =   "image";
                        } else if($row->invoice_field_category_id==12){
                            $invoiceFieldValueRequest['value'] =  $row->label;
                        }else {
                            $invoiceFieldValueRequest['value'] = (Input::get('formField' . $row->id) == "") ? "N/a" : Input::get('formField' . $row->id);
                        }
                        $invoiceFieldValue = $this->emailSentInvoiceValueRepository->insertAndUpdate($invoiceFieldValueRequest);
                        if($row->invoice_field_category_id==9 || $row->invoice_field_category_id==5){
                            if($row->invoice_field_category_id==9){
                                $this->sentEmailInvoiceImageValue($row,$invoiceFieldValue->id,$date,'formFieldSignature','signature');
                            }else{
                                $this->sentEmailInvoiceImageValue($row,$invoiceFieldValue->id,$date,'formFieldImage','image');
                            }
                        }
                    }
                    //invoice description query
                    if(Input::get('invoiceDescriptionCount')!=0){
                        $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                        for($i = 0; $i <$totalInvoiceDescription;$i++){
                            $description = Input::get('invoiceDescriptionValue'.$i);
                            if ($description != null){
                                $amount = Input::get('invoiceDescriptionAmount'.$i);
                                $invoiceDescriptionRequest     =    new Request();
                                $invoiceDescriptionRequest['email_sent_invoice_id']    =   $sentInvoice->id;
                                $invoiceDescriptionRequest['description']   =  $description ;
                                $invoiceDescriptionRequest['amount']        =   $amount;
                                $this->emailSentInvoiceDescriptionRepository->insertAndUpdate($invoiceDescriptionRequest);
                            }
                        }
                    }

                    if (@$invoice->docketFolderAssign!=null){
                        $folderItemRequest = new Request();
                        $folderItemRequest['folder_id'] = $invoice->docketFolderAssign->folder_id;
                        $folderItemRequest['ref_id'] = $sentInvoice->id;
                        $folderItemRequest['type'] = 4;
                        $folderItemRequest['user_id'] = $authUser->id;
                        $folderItemRequest['status'] = 0;
                        $folderItemRequest['company_id'] = $company->id;
                        $this->folderItemRepository->insertAndUpdate($folderItemRequest);
                        $this->emailSentInvoiceRepository->getDataWhere([['id',$sentInvoice->id]])->update(['folder_status'=>1]);
                    }

                    //for emailing only
                    $data['sentInvoice']    =   $sentInvoice;
                    $sentInvoiceValueQuery    =  $this->emailSentInvoiceValueRepository->getDataWhere([['email_sent_invoice_id',$sentInvoice->id]])->get();
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
                    $emailSentInvoicePaymentDetail = $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$sentInvoice->id]])->first();
                    if($emailSentInvoicePaymentDetail != null){
                        $invoiceSetting =  $emailSentInvoicePaymentDetail ;
                    }
                    $data['invoiceSetting'] =   $invoiceSetting;
                    $data['invoiceDescription']     =  $this->emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$sentInvoice->id]])->get();

                    $document_name  =  "emailed-invoice-".$sentInvoice->id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
                    $document_path   =   'files/pdf/emailedInvoiceForward/'.str_replace('.', '',$document_name).'.pdf';
                    if(!AmazoneBucket::fileExist($document_path)){
                        $sendInvoiceData['sentInvoice'] = $sentInvoice;
                        $sendInvoiceData['sentInvoiceValue'] = $sentInvoiceValue;
                        $sendInvoiceData['document_path'] = $document_path;
                        
                        dispatch((new SentInvoicePdfSaveJob($sendInvoiceData))->delay(Carbon::now()->addSecond(5)));
                        // $invoice     =     $sentInvoice;
                        // $companyDetails =  $this->companyRepository->getDataWhere([['id',$invoice->company_id]])->first();
                        // $invoiceDescription     =  $this->emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->get();
                        // $invoiceSetting =   array();
                        // //check invoice payment info
                        // $emailSentInvoicePaymentDetailDoc = $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->first();
                        // if($emailSentInvoicePaymentDetailDoc != null){
                        //     $invoiceSetting =  $emailSentInvoicePaymentDetailDoc;
                        // }

                        // $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                        // $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                        // $output = $pdf->output();
                        // $path = storage_path($document_path);
                        // file_put_contents($path, $output);
                    }
                    $data["downloadLink"]   =   asset('storage/'.$document_path);

                    $invoice    =  $this->invoiceRepository->getDataWhere([['id',$request->templateId]])->first();
                    // Mail::to($sentInvoice->receiverInfo->email)->send(new EmailInvoice($sentInvoice, $sentInvoice->receiverInfo, 'You’ve got an invoice'));
                    $mailData['sentInvoice'] = $sentInvoice;
                    $mailData['receiverInfo'] = $sentInvoice->receiverInfo;
                    $mailData['subject'] = 'You’ve got an invoice';
                    dispatch((new SentInvoiceJob($mailData))->delay(Carbon::now()->addSecond(10)));

                    DB::commit();
                    return response()->json(['status'=>true ,'data'=>'invoiceManager/allInvoice']);

                }
            }catch(\Exception $ex){
                dd($ex);
                DB::rollback();
                return response()->json(['status'=>false ,'data'=>'Error.']);
            }
        endif;
    }

    function sentInvoiceImageValue($row,$invoiceFieldValueId,$date,$authUser,$formName,$type,$signatureSN = null){
        if (Input::has($formName.$row->id)){
            $imageField =   $formName.$row->id;
            $image     =   Input::file($imageField);
            if (count($image)>0){
                foreach ($image as $images){
                    if($images->isValid()) {
                        $imageValueRequest     =    new Request();
                        $imageValueRequest['sent_invoice_value_id']    =  $invoiceFieldValueId;
                        // $ext = $images->getClientOriginalExtension();
                        // if($type == 'signature'){
                        //     $filename = basename("".$type.".".$authUser->id.$signatureSN) . time() . ".png";
                        // }else{
                        //     $filename = basename($images->getClientOriginalName(), '.' . $images->getClientOriginalExtension()) . time() . "." . $ext;
                        // }
                        
                        $dest = 'files/'.$date.'/invoice/'.$type.'';
                        // $urlPath = $dest .'/'. $filename;
                        // $images->move($dest, $filename);
                        // $imageValueRequest['value'] = $dest . '/' . $filename;

                        $imageValueRequest['value'] = FunctionUtils::imageUpload($dest,$images);
                        $this->sentInvoiceImageValueRepository->insertAndUpdate($imageValueRequest);
                    }
                    if($type == 'signature'){
                        return $signatureSN++;
                    }
                }
            }
        }
    }


    function sentEmailInvoiceImageValue($row,$invoiceFieldValueId,$date,$formName,$type){
        if (Input::has($formName.$row->id)){
            $imageField =   $formName.$row->id;
            $image     =   Input::file($imageField);
            if (count($image)>0){
                foreach ($image as $images){
                    if($images->isValid()) {
                        $imageValueRequest     =    new Request();
                        $imageValueRequest['sent_invoice_value_id']    =  $invoiceFieldValueId;
                        // $ext = $images->getClientOriginalExtension();
                        // $filename = basename($images->getClientOriginalName(), '.' . $images->getClientOriginalExtension()) . time() . "." . $ext;
                        $dest = 'files/'.$date.'/invoice/'.$type.'/email';
                        // $urlPath = $dest .'/'. $filename;
                        // $images->move($dest, $filename);
                        // $imageValueRequest['value'] = $dest . '/' . $filename;

                        $imageValueRequest['value'] = FunctionUtils::imageUpload($dest,$images);
                        $this->sentInvoiceImageValueRepository->insertAndUpdate($imageValueRequest);
                    }
                }
            }
        }
    }
}