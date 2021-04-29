<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\MessageDisplay;
use App\Http\Resources\V2\Docket\SearchDocketResource;
use App\Http\Resources\V2\Invoice\InvoiceConversationChatResource;
use App\Http\Resources\V2\Invoice\InvoiceTempleteResource;
use App\Http\Resources\V2\Invoice\InvoiceTimelineResource;
use App\Mail\EmailInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class InvoiceService extends ConstructorService {
    

    public function getInvoiceTemplateList($request){
        if(!$request->headers->has('userId')){
            if(isset(auth()->user()->id)){
                $request->headers->set('userId', auth()->user()->id);
            }
        }
        $invoiceTemplateQuery    =  $this->assignedInvoiceRepository->getDataWhere([['user_id',$request->header('userId')]])->get();
        $invoiceTemplate    =   array();
        foreach ($invoiceTemplateQuery as $row){
            $invoiceTemplate[]     =    array('id'   =>  $row->invoice_id, 'title'    =>  $row->invoiceInfo->title);
        }
        return $invoiceTemplate;
    }

    public function getInvoiceTemplateDetailsById($request,$invoiceId){
        if(!$request->headers->has('userId')){
            if(isset(auth()->user()->id)){
                $request->headers->set('userId', auth()->user()->id);
                $request->headers->set('companyId', auth()->user()->companyInfo->id);
            }
        }
        $invoiceQuery     =  $this->invoiceRepository->getDataWhere([['id',$invoiceId]]);
        if($invoiceQuery->count() > 0){
            if($invoiceQuery->first()->company_id == $request->header('companyId')){
                $invoice    =   $invoiceQuery->first();
                $invoiceFieldQuery    = $this->invoiceFieldRepository->getDataWhere([['invoice_id',$invoice->id]])->orderBy('order','asc')->get();
                $invoiceFields   =   array();

                foreach ($invoiceFieldQuery as $row){
                    $subField   =   array();
                    $fields = array('id' => $row->id,
                                    'invoice_field_category_id' => $row->invoice_field_category_id,
                                    'invoice_field_category' => $row->fieldCategoryInfo->title,
                                    'label' => $row->label,
                                    'order' => $row->order,
                                    'subField'  => $subField);

                    if($row->invoice_field_category_id == 9) {
                        $invoiceFields[] = $fields;
                    }
                    elseif ($row->invoice_field_category_id == 1 || $row->invoice_field_category_id == 2 ||
                            $row->invoice_field_category_id == 5 || $row->invoice_field_category_id == 12){
                        $imageHeader[] = $fields;
                    }
                }
                if(@$imageHeader ){
                    foreach ($imageHeader as $row){
                        $invoiceFields[] =   $row;
                    }

                }
                $data = new InvoiceTempleteResource($invoice,$invoiceFields);
                return response()->json($data,200);
            }else{
                return response()->json(array("status" => false,"message"=>MessageDisplay::InvalidRequest));
            }
        }else {
            return response()->json(array("status" => false,"message"=>MessageDisplay::InvoiceNotFound));
        }
    }

    public function saveSentInvoice($request){
        try {
            DB::beginTransaction();
            //cehck if subscription was free count remaining docket left
            $company    =  auth()->user()->companyInfo;
            $companyId = $company->id;
            if($companyId != 1) {
                if ($company->trial_period == 3) {
                    //get last subscription created date
                    $subscriptionLogQuery = $this->subscriptionLogRepository->getDataWhere([['company_id', $company->id]]);
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

                    $sentInvoices = $this->sentInvoiceRepository->getDataWhere([['company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
                    $emailInvoices = $this->emailSentInvoiceRepository->getDataWhere([['company_id', $company->id]])->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();

                    $totalMonthInvoices = $sentInvoices + $emailInvoices;
                    if ($totalMonthInvoices >= 1) {
                        return response()->json(array('status' => true, 'message' => MessageDisplay::SubscriptionUpgrade));
                    }
                }
            }
        
            $date = Carbon::now()->format('d-M-Y');
            $invoice    =  $this->invoiceRepository->getDataWhere([['id',$request->invoice_id]])->first();
            $companyInvoice = $this->companyRepository->getDataWhere([['id',$companyId]])->first();
            $invoiceuserFullname = auth()->user();

            if($request->emailTemplateFlag == "true"):
                $emailSentInvoiceRequest =     new Request();
                $emailSentInvoiceRequest['invoice_id']  =   $request->invoice_id;
                $emailSentInvoiceRequest['template_title']    =   $invoice->title;
                $emailSentInvoiceRequest['abn']                =      $companyInvoice->abn;
                $emailSentInvoiceRequest['company_name']       =      $companyInvoice->name;
                $emailSentInvoiceRequest['company_address']    =      $companyInvoice->address;
                $emailSentInvoiceRequest['company_logo'] = $companyInvoice->logo;
                $emailSentInvoiceRequest['sender_name']        =      $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $emailSentInvoiceRequest['user_id']    =   auth()->user()->id;
                $emailSentInvoiceRequest['theme_document_id'] = $invoice->theme_document_id;
                $emailSentInvoiceRequest['company_id']	=   $companyId;
                $emailSentInvoiceRequest['receiver_user_id']   =   $request->receiver_user_id;
                $emailSentInvoiceRequest['syn']   =   $invoice->syn_xero;
                $emailSentInvoiceRequest['amount'] = 0;
                if($companyInvoice->number_system == 1){
                    $emailSentInvoiceList = $this->emailSentInvoiceRepository->getDataWhere([['company_id',$companyId]])->select('company_invoice_id')->get();
                    if (count($emailSentInvoiceList) == 0){
                        $emailSentInvoiceRequest['company_invoice_id'] = 1;
                    }else{
                        $companyDocketId =  $emailSentInvoiceList->pluck('company_invoice_id')->toArray();
                        $emailSentInvoiceRequest['company_invoice_id'] = max($companyDocketId) + 1;
                    }
                }else{
                    $emailSentInvoiceRequest['company_invoice_id']  = 0;
                }
                if($request->has('receiverFullName') && $request->input('receiverFullName')!=""){
                    $emailSentInvoiceRequest['receiver_full_name']            =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                    $emailSentInvoiceRequest['receiver_company_name']         =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    $emailSentInvoiceRequest['receiver_company_address']      =   ($request->has('receiverCompanyAddress'))?$request->receiverCompanyAddress:"";
                }else{
                    $emailClient = $this->emailClientRepository->getDataWhere([['company_id', $companyId],['email_user_id', $request->receiver_user_id]])->first();
                    $emailSentInvoiceRequest['receiver_full_name'] = $emailClient->full_name;
                    $emailSentInvoiceRequest['receiver_company_name'] = $emailClient->company_name;
                    $emailSentInvoiceRequest['receiver_company_address'] = $emailClient->company_address;
                }
                if($invoice->gst == 1){
                    $emailSentInvoiceRequest['gst']           =   $invoice->gst_value;
                }
                $emailSentInvoiceRequest['isDocketAttached']  =   $request->isDocketAttached;
                $emailSentInvoiceRequest['hashKey']           =   $this->generateRandomString();
                $emailSentInvoiceRequest['status']            =   0;

                $sentInvoice = $this->emailSentInvoiceRepository->insertAndUpdate($emailSentInvoiceRequest);

                if($companyInvoice->number_system == 1){
                    if($invoice->hide_prefix ==1){
                        $sentInvoice->formatted_id = $sentInvoice->company_id."-".$sentInvoice->company_invoice_id;
                    }else{
                        $sentInvoice->formatted_id = "rt-".$sentInvoice->company_id."-einv-".$sentInvoice->company_invoice_id;
                    }
                    $sentInvoice->update();
                }else{
                    $findUserInvoiceCount = $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId],['invoice_id',$invoice->id]])->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount = $this->emailSentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId],['invoice_id',$invoice->id]])->pluck('user_invoice_count')->toArray();
                    if(max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount)) == 0){
                        $uniquemax = 0;
                    }else{
                        $uniquemax = max(array_merge($findUserInvoiceCount,$findUserEmailInvoiceCount));
                    }
                    $sentInvoice->user_invoice_count = $uniquemax+1;
                    $employeeData = $this->employeeRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId]])->get();
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

                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =   $this->invoiceSettingRepository->getDataWhere([['company_id',$companyId]]);
                if($invoiceSettingQuery->count()==1){
                    $invoiceSetting =   $invoiceSettingQuery->first();

                    $sentInvoicePaymentDetailsRequest                            =    new Request();
                    $sentInvoicePaymentDetailsRequest['email_sent_invoice_id']   =   $sentInvoice->id;
                    $sentInvoicePaymentDetailsRequest['company_id']              =   $sentInvoice->company_id;
                    $sentInvoicePaymentDetailsRequest['bank_name']               =   $invoiceSetting->bank_name;
                    $sentInvoicePaymentDetailsRequest['account_name']	         =   $invoiceSetting->account_name;
                    $sentInvoicePaymentDetailsRequest['bsb_number']              =   $invoiceSetting->bsb_number;
                    $sentInvoicePaymentDetailsRequest['account_number']          =   $invoiceSetting->account_number;
                    $sentInvoicePaymentDetailsRequest['instruction']             =   $invoiceSetting->instruction;
                    $sentInvoicePaymentDetailsRequest['additional_information']  =   $invoiceSetting->additional_information;
                    $sentInvoicePaymentDetails = $this->emailSentInvoicePaymentDetailRepository->insertAndUpdate($sentInvoicePaymentDetailsRequest);
                }

                if($request->isDocketAttached==1){
                    $docketsId = Input::get('dockets');
                    foreach($docketsId as $rowId) {
                        $attachedEmailDocketRequest     =   new Request();
                        $attachedEmailDocketRequest['sent_email_invoice_id']    =   $sentInvoice->id;
                        $attachedEmailDocketRequest['sent_email_docket_id']     =   $rowId;
                        $attachedEmailDocket = $this->sentEInvoiceAttachedEDocketRepository->insertAndUpdate($attachedEmailDocketRequest);
                    }
                }

                //invoice filed query
                $invoiceFieldsQuery   = $this->invoiceFieldRepository->getDataWhere([['invoice_id',$request->invoice_id]])->orderBy('order','asc')->get();
                foreach ($invoiceFieldsQuery as $row){
                    $invoiceFieldValueRequest   =   new Request();
                    $invoiceFieldValueRequest['email_sent_invoice_id']   =   $sentInvoice->id;
                    $invoiceFieldValueRequest['invoice_field_id']  =   $row->id;
                    $invoiceFieldValueRequest['label']  =   $row->label;
                    if($row->invoice_field_category_id == 9){
                        $invoiceFieldValueRequest['value']  =   "signature";
                    }elseif($row->invoice_field_category_id == 5){
                        $invoiceFieldValueRequest['value']  =   "image";
                    }else {
                        $invoiceFieldValueRequest['value'] = Input::get('formField' . $row->id == "")? "N/a" : Input::get('formField' . $row->id);
                    }

                    $invoiceFieldValue = $this->emailSentInvoiceValueRepository->insertAndUpdate($invoiceFieldValueRequest);
                    
                    if($row->invoice_field_category_id == 9 && $row->invoice_field_category_id == 5){
                        if($row->invoice_field_category_id == 9){
                            $formField = 'Signature';
                        }else{
                            $formField = 'Image';
                        }
                        $totalImages    =     Input::get('formField'.$formField.$row->id.'count');
                        for($i = 0; $i <$totalImages;$i++){
                            $imageField =   'formField'.$formField.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValueRequest     =    new Request();
                                    $imageValueRequest['email_sent_invoice_value_id']    =  $invoiceFieldValue->id;

                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/'.strtolower($formField).'/email';
                                    // $image->move($dest, $filename);
                                    // $imageValueRequest['value']    =    $dest . '/' . $filename;

                                    $imageValueRequest['value'] = FunctionUtils::imageUpload($dest,$image);
                                    $imageValue = $this->emailSentInvoiceImageRepository->insertAndUpdate($imageValueRequest);
                                }
                            }
                        }
                    }
                }

                //invoice description query
                if(Input::get('invoiceDescriptionCount')!=0){
                    $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                    for($i = 0; $i <$totalInvoiceDescription;$i++){
                        $invoiceDescriptionRequest     =    new Request();
                        $invoiceDescriptionRequest->email_sent_invoice_id    =   $sentInvoice->id;
                        $invoiceDescriptionRequest->description   =   Input::get('invoiceDescriptionRequestValue'.$i);
                        $invoiceDescriptionRequest->amount        =   Input::get('invoiceDescriptionAmount'.$i);
                        $this->emailSentInvoiceDescriptionRepository->insertAndUpdate($invoiceDescriptionRequest);
                    }
                    $total=0;
                    foreach ($this->emailSentInvoiceDescriptionRepository->where('email_sent_invoice_id',$sentInvoice->id)->get() as $item){
                        $total += $item->amount;
                    }
                    $sentInvoice->amount = $total;
                    $sentInvoice->save();
                }


                if ($invoice->docketFolderAssign != null){
                    $folderItemRequest = new Request();
                    $folderItemRequest['folder_id'] = $invoice->docketFolderAssign->folder_id;
                    $folderItemRequest['ref_id'] = $sentInvoice->id;
                    $folderItemRequest['type'] = 4;
                    $folderItemRequest['user_id'] = auth()->user()->id;
                    $folderItemRequest['status'] = 0;
                    $folderItemRequest['company_id'] = $companyId;
                    $folderItem = $this->folderItemRepository->insertAndUpdate($folderItemRequest);
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
                    $invoiceSetting =   $emailSentInvoicePaymentDetail;
                }

                $data['invoiceSetting'] =   $invoiceSetting;
                $data['invoiceDescription']     =  $this->emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$sentInvoice->id]])->get();

                $document_name  = "emailed-invoice-".$sentInvoice->id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
                $document_path   =   'files/pdf/emailedInvoiceForward/'.str_replace('.', '',$document_name).'.pdf';
                if(!AmazoneBucket::fileExist($document_path)){

                    $invoice     =     $sentInvoice;
                    $companyDetails =  $this->companyRepository->getDataWhere([['id',$invoice->company_id]])->first();

                    $invoiceDescription     =   $this->emailSentInvoiceDescription->getDataWhere([['email_sent_invoice_id',$invoice->id]])->get();
                    $invoiceSetting =   array();
                    //check invoice payment info
                    $emailSentInvoicePaymentDetail = $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->first();
                    if($emailSentInvoicePaymentDetail != null){
                        $invoiceSetting =  $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->first();
                    }

                    $pdf = \PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                    $output = $pdf->output();
                    $path = storage_path($document_path);
                    file_put_contents($path, $output);

                }
                $data["downloadLink"]   =   asset('storage/'.$document_path);
                Mail::to($sentInvoice->receiverInfo->email)->send(new EmailInvoice($sentInvoice, $sentInvoice->receiverInfo, 'You’ve got an invoice'));

                return response()->json(array('status' => true, 'message' => 'Invoice successfully sent to '.$sentInvoice->receiverInfo->email));
            else:
                $sentInvoiceRequest                       =     new Request();
                $sentInvoiceRequest['user_id']            =     auth()->user()->id;
                $sentInvoiceRequest['abn']                =     $companyInvoice->abn;
                $sentInvoiceRequest['company_name']       =     $companyInvoice->name;
                $sentInvoiceRequest['company_address']    =     $companyInvoice->address;
                $sentInvoiceRequest['company_logo']       =     $companyInvoice->logo;
                $sentInvoiceRequest['sender_name']        =     $invoiceuserFullname->first_name.' '.$invoiceuserFullname->last_name;
                $sentInvoiceRequest['invoice_id']         =     $request->invoice_id;
                $sentInvoiceRequest['theme_document_id']  =     $invoice->theme_document_id;
                $sentInvoiceRequest['receiver_user_id']   =     $request->receiver_user_id;

                if($companyInvoice->number_system == 1){
                    $sentInvoice = $this->sentInvoiceRepository->getDataWhere([['company_id',$companyId]])->select('company_invoice_id')->get();
                    if (count($sentInvoice) == 0){
                        $sentInvoiceRequest['company_invoice_id'] = 1;
                    }else{
                        $companyDocketId =  $sentInvoice->pluck('company_invoice_id')->toArray();
                        $sentInvoiceRequest['company_invoice_id'] = max($companyDocketId) + 1;
                    }
                }else{
                    $sentInvoiceRequest['company_invoice_id'] =0;
                }
                $employeeData = $this->employeeRepository->getDataWhere([['user_id', $request->receiver_user_id]])->first();
                if($employeeData != null):
                    $companyId = $employeeData->company_id;
                else :
                    @$companyId   = $this->companyRepository->getDataWhere([['user_id', $request->receiver_user_id]])->first()->id;
                endif;

                $sentInvoiceRequest['receiver_company_id']         =   $companyId;
                $sentInvoiceRequest['company_id']	=   $companyId;
                $sentInvoiceRequest['status']             =   0;
                $sentInvoiceRequest['amount']        =   0;
                $sentInvoiceRequest['isDocketAttached']  =   $request->isDocketAttached;

                if($invoice->gst==1){
                    $sentInvoiceRequest['gst']           =   $invoice->gst_value;
                }
                $sentInvoice = $this->sentInvoiceRepository->insertAndUpdate($sentInvoiceRequest);

                if($companyInvoice->number_system == 1){
                    if ($invoice->hide_prefix == 1){
                        $sentInvoice->formatted_id = $sentInvoice->company_id."-".$sentInvoice->company_invoice_id;
                    }else{
                        $sentInvoice->formatted_id = "rt-".$sentInvoice->company_id."-inv-".$sentInvoice->company_invoice_id;
                    }
                    $sentInvoice->update();
                }else {
                    $findUserInvoiceCount = $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId],['invoice_id', $invoice->id]])->pluck('user_invoice_count')->toArray();
                    $findUserEmailInvoiceCount = $this->emailSentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId],['invoice_id', $invoice->id]])->pluck('user_invoice_count')->toArray();
                    if (max(array_merge($findUserInvoiceCount, $findUserEmailInvoiceCount)) == 0) {
                        $uniquemax = 0;
                    } else {
                        $uniquemax = max(array_merge($findUserInvoiceCount, $findUserEmailInvoiceCount));
                    }
                    $sentInvoice->user_invoice_count = $uniquemax + 1;
                    $employeeData = $this->employeeRepository->getDataWhere([['user_id', auth()->user()->id],['company_id', $companyId]])->get();
                    if ($employeeData->count() == 0) {
                        if ($invoice->hide_prefix == 1){
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

                //check invoice payment info and save it into sent invoice payment details table
                $invoiceSettingQuery     =  $this->invoiceSettingRepository->getDataWhere([['company_id',$companyId]]);
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
                if($request->isDocketAttached==1){
                    $docketsId = Input::get('dockets');
                    foreach($docketsId as $rowId) {
                        $attachedDocketRequest     =   new Request();
                        $attachedDocketRequest['sent_invoice_id']    =   $sentInvoice->id;
                        $attachedDocketRequest['sent_docket_id']     =   $rowId;
                        $this->sentInvoiceAttachedDocketRepository->insertAndUpdate($attachedDocketRequest);
                    }
                }
                if ($invoice->docketFolderAssign != null){
                    $folderItemRequest = new Request();
                    $folderItemRequest['folder_id'] = $invoice->docketFolderAssign->folder_id;
                    $folderItemRequest['ref_id'] = $sentInvoice->id;
                    $folderItemRequest['type'] = 2;
                    $folderItemRequest['user_id'] = auth()->user()->id;
                    $folderItemRequest['status'] = 0;
                    $folderItemRequest['company_id'] = $companyId;
                    $this->folderItemRepository->insertAndUpdate($folderItemRequest);
                    $this->sentInvoiceRepository->getDataWhere([['id',$sentInvoice->id]])->update(['folder_status'=>1]);
                }

                //invoice filed query
                $invoiceFieldsQuery   = $this->invoiceFieldRepository->getDataWhere([['invoice_id',$request->invoice_id]])->orderBy('order','asc')->get();
                foreach ($invoiceFieldsQuery as $row){
                    $invoiceFieldValueRequest   =   new Request();
                    $invoiceFieldValueRequest['sent_invoice_id']   =   $sentInvoice->id;
                    $invoiceFieldValueRequest['invoice_field_id']  =   $row->id;
                    $invoiceFieldValueRequest['label']  =   $row->label;
                    if($row->invoice_field_category_id == 9){
                        $invoiceFieldValueRequest['value']            =   "signature";
                    } elseif($row->invoice_field_category_id == 5){
                        $invoiceFieldValueRequest['value']            =   "image";
                    }else {
                        $invoiceFieldValueRequest['value'] = ($request->has('formField'.$row->id))?Input::get('formField' . $row->id):"";
                    }

                    $invoiceFieldValue = $this->sentInvoiceValueRepository->insertAndUpdate($invoiceFieldValueRequest);
                    if($row->invoice_field_category_id == 9 || $row->invoice_field_category_id == 5){
                        if($row->invoice_field_category_id == 9){
                            $formField = 'Signature';
                        }elseif($row->invoice_field_category_id == 5){
                            $formField = 'Image';
                        }
                        $totalImages    =     Input::get('formField'.$formField.$row->id.'count');
                        for($i = 0; $i <$totalImages; $i++){
                            $imageField =   'formField'.$formField.$row->id.'Id'.$i;
                            $image              =   Input::file($imageField);
                            if($request->hasFile($imageField)) {
                                if ($image->isValid()) {
                                    $imageValueRequest     =    new Request();
                                    $imageValueRequest['sent_invoice_value_id']    =  $invoiceFieldValue->id;
    
                                    // $ext = $image->getClientOriginalExtension();
                                    // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                    $dest = 'files/'+$date+'/invoice/'.strtolower($formField);
                                    // $image->move($dest, $filename);
                                    // $imageValueRequest['value']    =    $dest . '/' . $filename;

                                    $imageValueRequest['value'] = FunctionUtils::imageUpload($dest,$image);
                                    $this->sentInvoiceImageValueRepository->insertAndUpdate($imageValueRequest);
                                }
                            }
                        }
                    }
                }

                //invoice description query
                $totalInvoiceDescription    =     Input::get('invoiceDescriptionCount');
                for($i = 0; $i < $totalInvoiceDescription;$i++){
                    $invoiceDescriptionRequest     =    new Request();
                    $invoiceDescriptionRequest['sent_invoice_id']    =   $sentInvoice->id;
                    $invoiceDescriptionRequest['description']   =   Input::get('invoiceDescriptionValue'.$i);
                    $invoiceDescriptionRequest['amount']        =   Input::get('invoiceDescriptionAmount'.$i);
                    $this->sentInvoiceDescriptionRepository->insertAndUpdate($invoiceDescriptionRequest);
                }
                $total=0;
                foreach ($this->sentInvoiceDescriptionRepository->getDataWhere([['sent_invoice_id',$sentInvoice->id]])->get() as $item){
                    $total += $item->amount;
                }

                $invoiceAmount  =    0;
                $invoiceAmountQuery    =  $this->sentDocketInvoiceRepository->getDataWhere([['type',2]])->whereIn('sent_docket_id',Input::get('dockets'))->get();
                if(count($invoiceAmountQuery) > 0){
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    $totalAmount = $total+$invoiceAmount;
                    $sentInvoice->amount = $totalAmount;
                    $sentInvoice->save();
                }

                $sentInvoiceReceiverInfo   =   $this->userRepository->getDataWhere([['id',$request->receiver_user_id]])->first();
                $userNotificationRequest   =   new Request();
                $userNotificationRequest['sender_user_id']   =   auth()->user()->id;
                $userNotificationRequest['receiver_user_id'] =   $sentInvoiceReceiverInfo->id;
                $userNotificationRequest['type']     =   2;
                $userNotificationRequest['title']    =   '';
                $userNotificationRequest['message']  =   $request->message;
                $userNotificationRequest['key']      =   $sentInvoice->id;
                $userNotificationRequest['status']   =   0;
                $this->userNotificationRepository->insertAndUpdate($userNotificationRequest);

                if($sentInvoiceReceiverInfo->device_type == 2){
                    $this->firebaseApi->sendiOSNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));

                }else if($sentInvoiceReceiverInfo->device_type == 1){
                    $this->firebaseApi->sendAndroidNotification($sentInvoiceReceiverInfo->deviceToken,"New Invoice", "You have received an invoice from ".$sentInvoice->senderUserInfo->first_name." ".$sentInvoice->senderUserInfo->last_name,array('type'=>4));
                }
            endif;  //check email template flag end
            DB::commit();
            return response()->json(['message' => 'Invoice successfully sent to '.$sentInvoice->receiverUserInfo->first_name." ".$sentInvoice->receiverUserInfo->last_name],200);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['error'=>MessageDisplay::ERROR],500);
        }
    }

    public function getLatestInvoiceHome($request){
        $conversationArray   =   array();
        $sentInvoiceQuery    =  $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id]])
            ->orWhere('receiver_user_id', auth()->user()->id)
            ->with('senderUserInfo','senderCompanyInfo','receiverUserInfo');

        if($sentInvoiceQuery->count() > 0){
            foreach ($sentInvoiceQuery->orderBy('created_at','desc')->take(10)->get() as $result) {
                $userId = $result->user_id;
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $profile = AmazoneBucket::url() . $result->senderUserInfo->image;
                $company = $result->senderCompanyInfo->name;

                if ($result->user_id == auth()->user()->id) {
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

                $receiver  = $result->receiverUserInfo->first_name." ".$result->receiverUserInfo->last_name;
                $conversationArray[] = new SearchDocketResource($result,'invoice',$userId,$userName,$profile,$company,$invoiceStatus,null,null,null,null,$receiver,'companyInvoiceId');
            }
        }

        return $this->conversationArrayDateSorting($conversationArray);
    }
  
    public function getLatestInvoiceList($request){
        $conversationArray      =   array();
        $companyId = auth()->user()->companyInfo->id;
        $added_company_idQuery         =  $this->clientRepository->getDataWhere([["company_id",$companyId]])->orWhere('requested_company_id',$companyId)->get();
        $added_company_id   =   array();
        $added_company_id[] =   $companyId;
        foreach ($added_company_idQuery as $row){
            if($row->company_id==$companyId){
                $added_company_id[] =   $row->requested_company_id;
            }else {
                $added_company_id[] =   $row->company_id;
            }
        }


        $employeeId     =   array();
        $employeeIdQuery  = $this->employeeRepository->getDataWhereIn('company_id',$added_company_id)->get();
        foreach ($employeeIdQuery as $row){
            $employeeId[]   =   $row->user_id;
        }

        $employeeUserIds = $this->companyRepository->getDataWhereIn('id',$added_company_id)->pluck('user_id');
        foreach ($employeeUserIds as $employeeUserId) {
            $employeeId[] 	= $employeeUserId;
        }

        foreach ($employeeId as $userId) {
            $sentInvoiceQuery = $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['receiver_user_id', $userId]])
                                                            ->orWhere([['receiver_user_id', auth()->user()->id],['user_id', $userId]])
                                                            ->with('receiverUserInfo','receiverCompanyInfo','senderUserInfo','senderCompanyInfo')
                                                            ->orderBy('created_at','desc')->first();
            if($sentInvoiceQuery != null){
                $result = $sentInvoiceQuery;
                $profile = "";
                if($result->user_id == auth()->user()->id){
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
                    $profile    =  AmazoneBucket::url() . $result->senderUserInfo->image;
                    $company    =   $result->senderCompanyInfo->name;

                    if($result->status==0):
                        $invoiceStatus   =   "Received";
                    endif;
                }

                if($result->status==1)
                    $invoiceStatus ="Approved";

                $receiver  = $result->receiverUserInfo->first_name." ".$result->receiverUserInfo->last_name;
                $conversationArray[] = new SearchDocketResource($result,'invoice',$userId,$userName,$profile,$company,$invoiceStatus,null,null,null,null,$receiver);
            }
        }

        return $this->conversationArrayDateSorting($conversationArray);
    }

    public function getConversationInvoiceChatByUserId($request,$userId){
        $conversationArray = [];
        $companyId = auth()->user()->companyInfo->id;
        $sentInvoiceQuery = $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['receiver_user_id', $userId]])
                                                            ->orWhere([['receiver_user_id', auth()->user()->id],['user_id', $userId]])
                                                            ->with('senderUserInfo','senderCompanyInfo','receiverUserInfo','receiverCompanyInfo')
                                                            ->orderBy('created_at','desc')->get();
        if(count($sentInvoiceQuery) > 0){
            $resultQuery = $sentInvoiceQuery;
            foreach ($resultQuery as $result){
                if($result->company_id == $companyId):
                    $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $company    =   $result->senderCompanyInfo->name;
                    if($result->status==0):
                        if($result->receiver_user_id == auth()->user()->id){
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
                $conversationArray[] = new InvoiceConversationChatResource($result,$userName,$company,$invoiceStatus);
            }
        }
        return $conversationArray;
    }

    public function getInvoiceDetailsById($request,$id){
        $sentInvoice     =  $this->sentInvoiceRepository->getDataWhere([['id',$id]]);
        $companyId = auth()->user()->companyInfo->id;
        if($sentInvoice->count() == 1):
            //check docket associated with user or not
            $companyId  =    $companyId;
            if($this->sentInvoiceRepository->getDataWhere([['id',$id],['company_id',$companyId]])->orWhere('receiver_company_id',$companyId)->count()>0){
                $sentInvoiceValueQuery    =  $this->sentInvoiceValueRepository->getDataWhere([['sent_invoice_id',$id]])->get();
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
                $invoice     =     $sentInvoice->first();
                $data = array();
                $data['full_name']= $invoice->sender_name;
                $data['company_name']= $invoice->company_name;
                $data['address']= $invoice->company_address;
                $companyDetails =  $this->companyRepository->getDataWhere([['id',$invoice->company_id]])->first();

                $invoiceDescription     =   $this->sentInvoiceDescriptionRepository->getDataWhere([['sent_invoice_id',$invoice->id]])->get();
                $invoiceSetting =   array();
                //check invoice payment info
                $sentInvoicePaymentDetail = $this->sentInvoicePaymentDetailRepository->getDataWhere([['sent_invoice_id',$id]])->first();
                if($sentInvoicePaymentDetail != null){
                    $invoiceSetting =   $sentInvoicePaymentDetail;
                }

                $userNotificationQuery  = $this->userNotificationRepository->getDataWhere([['type',4],['receiver_user_id',auth()->user()->id],['key',$id]]);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        $userNotificationQuery->update(['status'=>1]);
                    }
                }
                if ($sentInvoice->first()->theme_document_id == 0){
                    return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                }else{
                    $documentTheme = $this->documentThemeRepository->getDataWhere([['id', $sentInvoice->first()->theme_document_id]]);
                    if($documentTheme->count()==0){
                        return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.preview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                    }else{
                        $theme = $documentTheme->first();
                        return response()->json(array('status' => true, 'invoice' => view('dashboard/company/themes/'.$theme->slug.'/mobile',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
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

    public function getEmailInvoiceDetailsById($request,$id){
        $companyId = auth()->user()->companyInfo->id;
        $sentInvoice     =  $this->emailSentInvoiceRepository->getDataWhere([['id',$id]]);
        if($sentInvoice->count()==1):
            //check docket associated with user or not
            $companyId  =    $companyId;
            if($this->emailSentInvoiceRepository->getDataWhere([['id',$id],['company_id',$companyId]])->count()>0){
                $sentInvoiceValueQuery    = $this->emailSentInvoiceValueRepository->getDataWhere([['email_sent_invoice_id',$id]])->get();
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

                $invoice     =     $sentInvoice->first();
                $data = array();
                $data['full_name']= $invoice->receiverInfo->email;
                $data['company_name']= $invoice->receiver_company_name;
                $data['address']= $invoice->receiver_company_address;
                $companyDetails =  $this->companyRepository->getDataWhere([['id',$invoice->company_id]])->first();


                $invoiceDescription     =   $this->emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->get();
                $invoiceSetting =   array();
                //check invoice payment info
                $emailSentInvoicePaymentDetail = $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$id]])->first();
                if($emailSentInvoicePaymentDetail != null){
                    $invoiceSetting =   $emailSentInvoicePaymentDetail;
                }
                if ($sentInvoice->first()->theme_document_id == 0){
                    return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.emailInvoicePreview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                }else{
                    $documentTheme = $this->documentThemeRepository->getDataWhere([['id', $sentInvoice->first()->theme_document_id]])->first();
                    if($documentTheme == null){
                        return response()->json(array('status' => true, 'invoice' => view('dashboard.company.invoiceManager.emailInvoicePreview',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','data'))->render()));
                    }else{
                        $theme = $documentTheme;
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

    public function getInvoiceTimelineByUserId($request,$id){
        $sentInvoiceQuery = $this->sentInvoiceRepository->getDataWhere([['user_id', auth()->user()->id],['receiver_user_id', $id]])
                                                            ->orWhere([['receiver_user_id', auth()->user()->id],['user_id', $id]])
                                                            ->with('senderUserInfo','senderCompanyInfo','receiverUserInfo','invoiceInfo');
        
        $sentInvoiceId  =    $sentInvoiceQuery->pluck('id');
        $sentInvoiceDates   =    $sentInvoiceQuery->where('created_at', '<=',Carbon::now())->groupBy('date')->groupBy('date')->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))->toArray();
        $conversationArray = array();
        foreach ($sentInvoiceDates as $sentInvoiceDate) {
            $dateWiseQuery  =    $this->sentInvoiceRepository->getDataWhereIn('id',$sentInvoiceId)->whereDate('created_at',$sentInvoiceDate)->orderBy('created_at','desc')->get();
            $invoices =   array();
            foreach ($dateWiseQuery as $result){
                $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;
                if($result->status==0):
                    if($result->receiver_user_id == auth()->user()->id){
                        $invoiceStatus   =   "Received";
                    }else{
                        $invoiceStatus   =   "Sent";
                    }
                endif;
                if($result->status==1)
                    $invoiceStatus ="Approved";


                $invoices[] = new InvoiceTimelineResource($result,$userName,$company,$invoiceStatus);
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentInvoiceDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentInvoiceDate['date'])->format('l')), 'invoices'   =>   $invoices);
            unset($invoices);
        }
        return $conversationArray;
    }

    // conversation sorting according to dateAdded
    public function conversationArrayDateSorting($conversationArray){
        return FunctionUtils::conversationArrayDateSorting($conversationArray);
    }
}