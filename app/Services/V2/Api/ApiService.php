<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\MessageDisplay;
use App\Helpers\V2\StaticValue;
use App\Http\Resources\V2\Docket\EmailSentDocketResource;
use App\Http\Resources\V2\Email\EmailClientResource;
use App\Http\Resources\V2\Invoice\InvoiceConversationChatResource;
use App\Http\Resources\V2\Invoice\InvoiceFilterDocketResource;
use App\Services\V2\ConstructorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use overint\MailgunValidator;
use ReceiptValidator\iTunes\Validator as iTunesValidator;
use App\Support\Collection;
use App\Notifications\AppOpen;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Services\V2\Api\UserService;
use App\Helpers\V2\AmazoneBucket;

class ApiService extends ConstructorService {

    public function companyDockets(){
        //find requested user is super admin or not
        $authUser = auth()->user()->id;
        $authCompany = auth()->user()->companyInfo->id;
        $user   =  $this->userRepository->getDataWhere([['id',$authUser]])->first();
        $allDocketTemplates    =  $this->docketRepository->getDataWhere([['company_id',$authCompany]])->select('id','title')->orderBy('id','desc')->get();
        $activeDocket          =   array();

        if($user->user_type == 2){
            foreach ($allDocketTemplates as $docketTemplate){
                if($this->sentDocketsRepository->getDataWhere([['docket_id',$docketTemplate["id"]]])->count()>0){
                    $activeDocket[] =   $docketTemplate;
                }
            }
        }else{
            foreach ($allDocketTemplates as $docketTemplate){
                if($this->assignedDocketRepository->getDataWhere([['docket_id',$docketTemplate["id"]],['user_id',$authUser]])->count()>0){
                    $activeDocket[] =   $docketTemplate;
                }
            }
        }
        $docketTemplate     =  $this->docketRepository->getDataWhere([['company_id',$authCompany]])->select('id','title')->orderBy('id','desc')->get();
        return $activeDocket;
    }

    public function filterDocket($request){
        $authUser = auth()->user()->id;
        if ($request->emailFlag == "true"){
            $sentemailDocketsQuery =  $this->emailSentDocketRepository->getDataWhere([['user_id',$authUser]])->with('recipientInfo');
            if ($request->date_type){
                if($request->date_type == "2"){
                    if($request->from){
                        $sentemailDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                    }
                    if($request->to){
                        $sentemailDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                    }
                }
            }

            if ($request->date_type){
                if($request->date_type == "1"){
                    if ($request->from) {
                        $sentDocketsQuery = $this->emailSentDocketDateFilter($request->from,$sentemailDocketsQuery,'from');
                    }
                    
                    if ($request->to) {
                        $sentemailDocketsQuery = $this->emailSentDocketDateFilter($request->to,$sentDocketsQuery,'to');
                    }
                }
            }

            if($request->docketTemplateId && $request->docketTemplateId != ""){
                $this->emailSentDocketRepository->getDataWhere([['user_id',$authUser]])->whereIn('docket_id',$request->docketTemplateId);
            }
            if($request->docketId && $request->docketId != ""){
                $this->emailSentDocketRepository->getDataWhere([['user_id',$authUser]])->where('company_docket_id',$request->docketId);
            }

            $sentEmailDockets     =   $this->emailSentDocketRepository->getDataWhere([['user_id',$authUser]])->get();

            $dockets        =   array();
            foreach($sentEmailDockets as $result){
                $userId  = 	$result->user_id;
                $userName  =   $result->sender_name;
                $company    =   $result->company_name;
                $docketStatus   =   "Sent";
                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   @$recipient->emailUserInfo->email;
                    else
                        $recipientData  =   $recipientData.", ".@$recipient->emailUserInfo->email;
                }
                    //approval text
                $emailSentDocketRecipientData = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$result->id],['approval',1]]);
                $totalRecipientApprovals    =   $emailSentDocketRecipientData->count();
                $totalRecipientApproved     =   $emailSentDocketRecipientData->where('status',1)->count();
                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }
                $companyDocketId = 'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id;
                $dockets[]   = new EmailSentDocketResource($result,$userId,$userName,$company,$recipientData,$approvalText,$docketStatus,$companyDocketId);
                // $dockets[]   =   array('id' => $result->id,
                //     'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                //     'user_id'   =>  $userId,
                //     'docketName' => $result->docketInfo->title,
                //     'docketId' => $result->docketInfo->id,
                //     'sender' => $userName,
                //     'profile' => asset($result->senderUserInfo->image),
                //     'company'   =>  $company,
                //     'recipient' => $recipientData,
                //     'recipients' => $recipientData,
                //     'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                //     'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                //     'approvalText'  =>  $approvalText,
                //     'isApproved'    =>  $result->status,
                //     'status'    => $docketStatus);
            }
        }else{
            $receivedSentDocketIds  = $this->sentDocketRecipientRepository->getDataWhere([['user_id',$authUser]])->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
            $sentDocketIds          = $this->sentDocketsRepository->getDataWhere([['user_id',$authUser]])->pluck('id')->toArray();
            if(count($receivedSentDocketIds)!=0 && count($sentDocketIds) != 0){
                $totalSentDocketIds     =   array_unique(array_merge($sentDocketIds, $receivedSentDocketIds));
            }else if(count($receivedSentDocketIds)!=0){
                $totalSentDocketIds =  $sentDocketIds;
            }else{
                $totalSentDocketIds =  $receivedSentDocketIds;
            }
            $sentDocketsQuery =  $this->sentDocketsRepository->getModel()->query();
            if ($request->date_type){
                if($request->date_type == "2"){
                    if($request->from ){
                        $sentDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                    }
                    if($request->to ){
                        $sentDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                    }
                }
            }

            if($request->date_type) {
                if ($request->date_type == "1") {
                    if ($request->from) {
                        $sentDocketsQuery = $this->sentDocketDateFilter($request->from,$sentDocketsQuery,'from');
                    }
                    if ($request->to) {
                        $sentDocketsQuery = $this->sentDocketDateFilter($request->to,$sentDocketsQuery,'to');
                    }
                }
            }

            if($request->docketTemplateId && $request->docketTemplateId != ""){
                $sentDocketsQuery->whereIn('docket_id',$request->docketTemplateId);
            }
            if($request->docketId && $request->docketId != ""){
                $sentDocketsQuery->where('company_docket_id',$request->docketId);
            }

            $sentDocketsQuery->whereIn('id',$totalSentDocketIds);
            $sentDockets     =   $sentDocketsQuery->with('senderUserInfo','senderCompanyInfo','recipientInfo.userInfo')->get();

            $dockets        =   array();
            foreach($sentDockets as $result){
                $userId  = 	$result->user_id;
                $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;

                if ($result->status == 3){
                    $docketStatus = "Rejected";
                }else{
                    if($result->is_cancel == 1){
                        $docketStatus = "Cancelled";
                    }else{
                        if($result->user_id == $authUser){
                            $docketStatus   =   "Sent";
                        } else {
                            $docketStatus   =   "Received";
                        }
                    }
                }

                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   @$recipient->userInfo->first_name." ".@$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".@$recipient->userInfo->first_name." ".@$recipient->userInfo->last_name;
                }

                //approval text
                $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]]);
                $totalRecipientApprovals    =   $sentDocketRecipientApprovalData->count();
                $totalRecipientApproved     =   $sentDocketRecipientApprovalData->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->where('user_id',$authUser)->count()==1){
                    $isApproval             =   1;
                    //check is approved
                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->where('user_id',$authUser)->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }

                //canreject
                $canRejectDocket = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->where('user_id',$authUser);
                $canReject = 0;
                $isReject = 0;
                if($canRejectDocket->count() > 0 ){
                    if ($canRejectDocket->first()->status == 0){
                        if ($result->status == 0) {
                            $canReject = 1;
                        }else{
                            $canReject = 0;
                        }
                    }else{
                        $canReject = 0;
                    }
                    if ($result->status == 3){
                        $isReject = 1;
                    }else{
                        $isReject = 0;
                    }

                }

                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                $companyDocketId = 'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id;
                $dockets[]   = new InvoiceFilterDocketResource($result,'docketFilter',$userName,$company,null,null,$recipientData,$result->senderUserInfo->image,
                                                                $approvalText,$isApproval,$isApproved,$companyDocketId,$docketStatus,$canReject,$isReject);
                
                // $dockets[]   =   array('id' => $result->id,
                //     'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
                //     'user_id'   =>  $userId,
                //     'docketName' => $result->docketInfo->title,
                //     'docketId' => $result->docketInfo->id,
                //     'sender' => $userName,
                //     'profile' => asset($result->senderUserInfo->image),
                //     'company'   =>  $company,
                //     'recipient' => $recipientData,
                //     'recipients' => $recipientData,
                //     'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                //     'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                //     'approvalText'  =>  $approvalText,
                //     'isApproval'    =>  $isApproval,
                //     'isApproved'    =>  $isApproved,
                //     'canReject'=>$canReject,
                //     'isReject' => $isReject,
                //     'status'    => $docketStatus);
            }
        }
        //        conversation sorting according to dateAdded
        $dockets = FunctionUtils::conversationArrayDateSorting($dockets);
        // $size = count($dockets);
        // for($i = 0; $i<$size; $i++){
        //     for ($j=0; $j<$size-1-$i; $j++) {
        //         if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
        //             $tempArray   =    $dockets[$j+1];
        //             $dockets[$j+1] = $dockets[$j];
        //             $dockets[$j]  =   $tempArray;
        //         }
        //     }
        // }
        return response()->json(['dockets' => $dockets],200);
    }

    public function filterDocument($request){
        //get total company employee ids
        $authCompany = auth()->user()->companyInfo;
        $employeeIds    =   $this->employeeRepository->getDataWhere([['company_id',$authCompany->id]])->pluck('user_id');
        $employeeIds[]  =   $authCompany->user_id;

        if($request->document_type == 1){
            $dockets = $this->filterDocumentIncludes($this->sentDocketsRepository,$request,$employeeIds);
            return response()->json(['dockets' => $dockets],200);
        }elseif($request->document_type == 2){
            $invoices = $this->filterDocumentIncludes($this->sentInvoiceRepository,$request,$employeeIds);
            return response()->json(['invoices' => $invoices],200);
        }

    }

    public function emailUserList(){
        if(!request()->headers->has('companyId')){
            if(isset(auth()->user()->id)){
                $userId = auth()->user()->id;
                $companyId = auth()->user()->companyInfo->id;
            }
        }else{
            $companyId = request()->header('companyId');
            $userId = request()->header('userId');
        }

        $emailClient = $this->emailClientRepository->getDataWhere([["company_id",$companyId]])->select('id','email_user_id','full_name','company_name','company_address')
                                                    ->with('emailUser')->orderBy('id','desc')->get();
        $emailClients = array();
        if (count($emailClient) > 0){
            foreach ($emailClient as $row){
                $emailClients[] = new EmailClientResource($row);
                // $emailClients[] =   array('id'=> $row->id,
                //     'email_user_id'=>$row->emailUser->id,
                //     'email'          => $row->emailUser->email,
                //     'full_name'=> $row->full_name,
                //     'company_name'         =>  $row->company_name,
                //     'company_address'  => $row->company_address,
                // );
            }
        }
        return $emailClients;
    }

    public function saveEmailClient($request){
        try {
            $authCompany = auth()->user()->companyInfo;
            // $validator = new MailgunValidator(StaticValue::MailgunPubKey());
            // if ($validator->validate($request->email) != "false" && $validator->validate($request->email) != null) {
                $emailUser = $this->emailUserRepository->getDataWhere([['email', $request->email]])->first();
                if ($emailUser != null) {
                    $emailClientDb = $this->emailClientRepository->getDataWhere([['email_user_id', $emailUser->id],['company_id',$authCompany->id]])->first();
                    if ($emailClientDb == null):
                        $addEmailClientRequest = new Request();
                        $addEmailClientRequest['full_name'] = $request->full_name;
                        if ($request->has('company_name') == ""){
                            $addEmailClientRequest['company_name'] = "";
                        }else{
                            $addEmailClientRequest['company_name'] = $request->company_name;
                        }
                        if ($request->has('company_address') == ""){
                            $addEmailClientRequest['company_address'] = "";
                        }else{
                            $addEmailClientRequest['company_address'] = $request->company_address;
                        }
                        $addEmailClientRequest['company_id'] = $authCompany->id;
                        $addEmailClientRequest['email_user_id'] = $emailUser->id;
                        $emailClient= $this->emailClientRepository->insertAndUpdate($addEmailClientRequest);
                        return response()->json(["message" => MessageDisplay::EmailClientAdded, 'email_user_id' => $emailUser->id],200);
                    else:
                        return response()->json(["message" => 'This email is already added on your Custom Clients as user '.$emailClientDb->full_name ],500);
                    endif;
                } else {
                    $emailUserRequest = new Request();
                    $emailUserRequest['email'] = $request->email;
                    $emailUserRequest['name'] = "";
                    $emailUserRequest['company_name'] = "";
                    $emailClient = $this->emailUserRepository->insertAndUpdate($emailUserRequest);
                
                    $addEmailClientRequest = new Request();
                    $addEmailClientRequest['full_name'] = $request->full_name;
                    if ($request->has('company_name') == ""){
                        $addEmailClientRequest['company_name'] = "";
                    }else{
                        $addEmailClientRequest['company_name'] = $request->company_name;
                    }
                    if ($request->has('company_address') == ""){
                        $addEmailClientRequest['company_address'] = "";
                    }else{
                        $addEmailClientRequest['company_address'] = $request->company_address;
                    }

                    $addEmailClientRequest['company_id'] = $authCompany->id;
                    $addEmailClientRequest['email_user_id'] = $emailClient->id;
                    $this->emailClientRepository->insertAndUpdate($addEmailClientRequest);
                    return response()->json(["message" => MessageDisplay::EmailClientAdded, 'email_user_id' => $emailClient->id],200);
                }
            // }else{
            //     return response()->json(["message" => MessageDisplay::InvalidEmail],500);
            // }
        } catch (\Exception $ex) {
            return response()->json(["message" => MessageDisplay::ClientAddFail],500);
        }
    }

    public function getInvoiceableEmailDocketList($key){
        $authUserId = auth()->user()->id;
        $authCompany = auth()->user()->companyInfo;
        $emailSentDocket = $this->emailSentDocketRepository->getDataWhere([['user_id',$authUserId],['company_id',$authCompany->id]])->with('recipientInfo')->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $key){
                  $arrays[] = $items->email_sent_docket_id;
                }
            }
        }
        $matchEmailDocket = $this->emailSentDocketRepository->getDataWhereIn('id',$arrays)->orderBy('created_at', 'desc')
                            ->with('senderUserInfo','senderCompanyInfo.userInfo','recipientInfo')->get();
        $invoiceableEmailDockets =   array();
        if(count($matchEmailDocket) > 0) {
            foreach ($matchEmailDocket as $result) {
                if ($result->company_id == $authCompany->id):
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
                    $emailSentDocketRecipientData = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$result->id],['approval',1]]);
                    $totalRecipientApprovals    =   $emailSentDocketRecipientData->count();
                    $totalRecipientApproved     =   $emailSentDocketRecipientData->where('status',1)->count();

                    if ($totalRecipientApproved == $totalRecipientApprovals ){
                        $approvalText               =  "Approved";
                    }else{
                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                    }
                    $invoiceDescription     =    array();
                    $invoiceDescriptionQuery    =    $this->sentEmailDocketInvoiceRepository->getDataWhere([['email_sent_docket_id',$result->id]])->with('sentEmailDocketValueInfo')->where('type',1)->get();
                    foreach($invoiceDescriptionQuery as $description){
                        $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                    }
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    $this->sentEmailDocketInvoiceRepository->getDataWhere([['email_sent_docket_id',$result->id]])->with('sentEmailDocketValueInfo.sentDocketUnitRateValue')->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }

                    $isApproval = $totalRecipientApprovals;
                    $invoiceableEmailDockets[] = new InvoiceFilterDocketResource($result,'InvoiceEmailDocket',$userName,$company,$invoiceDescription,$invoiceAmount,
                                                $recipientName,$senderImage,$approvalText,$isApproval,$result->status);
                    // $invoiceableEmailDockets[] = array('id' => $result->id,
                    //     'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                    //     'user_id' => $result->user_id,
                    //     'docketName' => $result->docketInfo->title,
                    //     'docketTemplateId' => $result->docketInfo->id,
                    //     'sender' => $userName,
                    //     'company' => $company,
                    //     'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                    //     'invoiceDescription' => $invoiceDescription,
                    //     'invoiceAmount' => $invoiceAmount,
                    //     'status' => $approvalText,
                    //     'recipient'=>$recipientName,
                    //     "isApproved"    => $result->status,
                    //     'senderImage'=>asset($senderImage),
                    // );
                    empty($invoiceDescription);
                    empty($invoiceAmount);
                endif;
            }

        }
        return $invoiceableEmailDockets;
    }

    public function approveDocketByEmail($request,$id,$hashKey){
        $sentDocketQuery     =   $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$id],['hashKey',$hashKey]]);
        $sentDockets = $this->sentDocketsRepository->getDataWhere([['id',$id]])->with('senderUserInfo','recipientInfo.userInfo')->first();
        if($sentDocketQuery->count()==1){
            if ($sentDockets->docketApprovalType == 0){
                $sentDocket =   $sentDocketQuery->first();
                if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                    $sentDocket->hashKey = '';
                    $sentDocket->status     =   1;
                    $sentDocket->save();

                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$id],['hashKey','!=','']])->count()==0){
                        $this->sentDocketsRepository->getDataWhere([['id',$id]])->update(['status'=>1]);
                    }

                    if($sentDockets->senderUserInfo->device_type == 2){
                        $this->firebaseApi->sendiOSNotification($sentDockets->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                    }else if($sentDockets->senderUserInfo->device_type == 1){
                        $this->firebaseApi->sendAndroidNotification($sentDockets->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                    }
                    $userNotificationRequest   =    new Request();
                    $userNotificationRequest['sender_user_id']   =    $sentDocket->user_id;
                    $userNotificationRequest['receiver_user_id'] =   $sentDockets->user_id;
                    $userNotificationRequest['type']     =   3;
                    $userNotificationRequest['title']    =   'Docket Approved';
                    $userNotificationRequest['message']  =   $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket.";
                    $userNotificationRequest['key']      =   $id;
                    $userNotificationRequest['status']   =   0;
                    $this->userNotificationRepository->insertAndUpdate();
                    $message    =   MessageDisplay::DocketApproved;
                    return view('errors.errorPage', compact('message'));
                }
            }else{
                $sentDocket =   $sentDocketQuery->first();
                $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$id],['type',2]])->get();
                $docketFields   = $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDockets->id]])->get();
                $sentDocketRecepients = array();
                foreach ($sentDockets->recipientInfo as $sentDocketRecepient){
                    if ($sentDocketRecepient->userInfo->employeeInfo){
                        $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
                    }else if ($sentDocketRecepient->userInfo->companyInfo){
                        $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
                    }
                    $sentDocketRecepients[]=array(
                        'name'=>$sentDocketRecepient->userInfo->first_name." ".$sentDocketRecepient->userInfo->last_name,
                        'company_name'=> $companyNameRecipent,
                    );
                }
                $datass= (new Collection($sentDocketRecepients))->sortBy('company_name');
                $receiverDetail = array();
                foreach ($datass as $datas){
                    $receiverDetail[$datas['company_name']][]= $datas['name'];
                }
                return view('errors.sentDocket', compact('sentDockets','sentDocket','docketTimer','docketFields','receiverDetail'));
            }
        }
        $message    =  MessageDisplay::LinkExpired;
        return view('errors.errorPage', compact('message'));
    }

    public function approvedDocketSignature(Request $request){
        $sentDocketQuery     =  $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$request->sentDocketId],['hashKey',$request->hashKey]]);
        if($sentDocketQuery->count() == 1){
            $sentDocket =   $sentDocketQuery->first();
            if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                $sentDocket->hashKey = '';
                $sentDocket->status     =   1;
                $sentDocket->approval_time = Carbon::now()->toDateTimeString();
                $sentDocket->name = $request->name;
                $image = $request->signature;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'files/docket/images/signature'.time().'.'.'png';
                \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                $sentDocket->signature = $imageName;
                $sentDocket->save();
                $sentDocketsQuery = $this->sentDocketSrepository->getDataWhere([['id',$request->sentDocketId]]);
                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$request->sentDocketId],['hashKey','!=','']])->count()==0){
                    $sentDocketsQuery->update(['status'=>1]);
                }
                $sentDocketss    =    $sentDocketsQuery->first();
                if($sentDocketss->senderUserInfo->device_type == 2){
                    $this->firebaseApi->sendiOSNotification($sentDocketss->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                }else if($sentDocketss->senderUserInfo->device_type == 1){
                    $this->firebaseApi->sendAndroidNotification($sentDocketss->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                }
                $userNotificationRequest   =    new Request();
                $userNotificationRequest['sender_user_id']   =    $sentDocket->user_id;
                $userNotificationRequest['receiver_user_id'] =   $sentDocketss->user_id;
                $userNotificationRequest['type']     =   3;
                $userNotificationRequest['title']    =   'Docket Approved';
                $userNotificationRequest['message']  =   $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket.";
                $userNotificationRequest['key']      =   $request->sentDocketId;
                $userNotificationRequest['status']   =   0;
                $this->userNotificationRepository->insertAndUpdate($userNotificationRequest);
                return response()->json(['message' => MessageDisplay::DocketApproved],200);
            }else {
                return response()->json(['message' => MessageDisplay::LinkExpired],500);
            }
        }else{
            $message    =   MessageDisplay::LinkExpired;
            return view('errors.errorPage', compact('message'));
        }
    }

    public  function sentDocketReject($request){
        try {
            DB::beginTransaction();
            $sentDocketId = Input::get('sent_docket_id');
            $explanation = Input::get('explanation');
            $authUser = auth()->user()->id;
            if ($this->sentDocketsRepository->getDataWhere([['id', $sentDocketId],['status',3]])->count() == 0){
                $sentDocketRecipientApprovalQuery = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $sentDocketId],['user_id', $authUser],['status', 0]])->first();
                if ($sentDocketRecipientApprovalQuery != null){
                    $sentDocketRecipientApprovalRequest     = new Request();
                    $sentDocketRecipientApprovalRequest['sent_docket_recipient_approval_id'] = $sentDocketRecipientApprovalQuery->id;
                    $sentDocketRecipientApprovalRequest['status']     =   3;
                    $sentDocketRecipientApprovalRequest['approval_time'] = Carbon::now()->toDateTimeString();
                    $this->sentDocketRecipientApprovalRepository->insertAndUpdate($sentDocketRecipientApprovalRequest);

                    $sentDocketExplanationRequest = new Request();
                    $sentDocketExplanationRequest['sent_docket_id'] =  $sentDocketId;
                    $sentDocketExplanationRequest['explanation'] =  $explanation;
                    $sentDocketExplanationRequest['user_id'] =  $authUser;
                    $this->sentDocketRejectRepository->insertAndUpdate($sentDocketExplanationRequest);

                    $this->sentDocketsRepository->getDataWhere([['id', $sentDocketId]])->update(['status'=> 3]);
                    $sentDocket = $this->sentDocketsRepository->getDataWhere([['id', $sentDocketId]])->first();
                    $companyAdminUser = $sentDocket->senderCompanyInfo->userInfo;

                    $this->commonNotification($sentDocketId,$authUser,$sentDocket);

                    if ($sentDocket->user_id != $companyAdminUser->id){
                        $this->commonNotification($request->docket_id,$authUser,$sentDocket);
                    }
                    
                    DB::commit();
                    return response()->json(['message' => MessageDisplay::DocketRejected],200);
                }
            }
            DB::rollback();
            return response()->json(['message' => MessageDisplay::DocketExist],500);
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(['message' => MessageDisplay::DocketExist],500);
        }
    }

    public function receiptValidator($request){
        $authCompany = auth()->user()->companyInfo;
        $authUserId = auth()->user()->id;
        $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
        $receiptBase64Data = $request->purchase_token;
        try {
            $response = $validator->setReceiptData($receiptBase64Data)->validate();
            $sharedSecret = StaticValue::ITuneSharedSecret(); // Generated in iTunes Connect's In-App Purchase menu
            $response = $validator->setSharedSecret($sharedSecret)->setReceiptData($receiptBase64Data)->validate();
        } catch (Exception $e) {
            return response()->json(array('status' => false,'message' => 'got error'. $e->getMessage()));
        }
        if ($response->isValid()) {
            $data = array();
            foreach ($response->getPurchases() as $purchase) {
               $data[] = $purchase->getRawResponse();
            }
            if ($this->appleSubscriptionRepository->getDataWhere([['company_id',$authCompany]])->count() != 0){
                $this->appleSubscriptionRepository->getDataWhere([['company_id',$authCompany]])->delete();
            }
            foreach ($data as $datas){
                $applepurchaseRequest = new Request();
                $applepurchaseRequest['product_id'] = $datas['product_id'];
                $applepurchaseRequest['company_id'] =  $authCompany;
                $applepurchaseRequest['transaction_id'] = $datas['transaction_id'];
                $applepurchaseRequest['purchase_date'] = $datas['purchase_date'];
                $applepurchaseRequest['expiry_date'] = $datas['expires_date'];
                $this->appleSubscriptionRepository->insertAndUpdate($applepurchaseRequest);
            }
            $this->companyRepository->getDataWhere([['id',$authCompany]])->update(['trial_period'=>4]);
            $appleSubcriptionCount = $this->companyRepository->getDataWhere([['user_id',$authUserId],['trial_period',4]])->count();
            $timestampCheck = 1;
            if ($appleSubcriptionCount != 0){
                $appleSubscriptions = $this->appleSubscriptionRepository->getDataWhere([['company_id',$authCompany]])->get()->last();
                $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($appleSubscriptions->expiry_date)->format('Y-m-d H:i:s'), 'UTC');
                $timestamp->setTimezone('Australia/Canberra');
                $now = Carbon::now();
                $timestampCheck = 0;
                if ($timestamp->gt($now)){
                    $timestampCheck = 1;
                }
            }
            if($timestampCheck == 1){
                $companyAdmin = $this->companyRepository->getDataWhere([['user_id',$authUserId]])->count();
                $company_admin = ($companyAdmin == 0) ? false : true;

                $stripeSubcription = $this->companyRepository->getDataWhere([['user_id',$authUserId],['trial_period',2]])->count();
                if ( $stripeSubcription == 0){
                    $stripe_subcription = false;
                }else{
                    $stripe_subcription = true;
                }

                $appleSubcription = $appleSubcriptionCount;
                $apple_subcription = ($appleSubcription == 0) ? false : true ;
                return response()->json(['company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription],200);
            }
        } else {
            return response()->json(['message' =>'Receipt is not valid. Receipt result code = ' . $response->getResultCode()],500);
        }
    }

    public function subscriptionStatus(){
        $authUserId = auth()->user()->id;
        $appleSubscriptions = $this->appleSubscriptionRepository->getDataWhere([['company_id',auth()->user()->companyInfo->id]])->get()->last();
        $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($appleSubscriptions->expiry_date)->format('Y-m-d H:i:s'), 'UTC');
        $timestamp->setTimezone('Australia/Canberra');

        $now = Carbon::now();
        if ($timestamp->gt($now) || $timestamp->lt($now)){
            $companyAdmin = $this->companyRepository->getDataWhere([['user_id',$authUserId]])->count();
            $company_admin = ($companyAdmin == 0) ? false : true;

            $stripeSubcription = $this->companyRepository->getDataWhere([['user_id',$authUserId],['trial_period',2]])->count();
            if ( $stripeSubcription == 0){
                $stripe_subcription = false;
            }else{
                $stripe_subcription = true;
            }

            $appleSubcription = $this->companyRepository->getDataWhere([['user_id',$authUserId],['trial_period',4]])->count();
            $apple_subcription = ($appleSubcription == 0) ? false : true ;

            return response()->json(['company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription],200);
        }
    }

    public function myPermission(){
        $authUserId = auth()->user()->id;
        $company = auth()->user()->companyInfo;
        if ($authUserId == $company->user_id){
            $userType = "superAdmin";
        }else{
            $userType = "employee";
        }
        $myPermission = array();
        if ($userType == "superAdmin"){
            $myPermission = $this->myPermissionCheck($company);
        }elseif ($userType == "employee"){
            $employee = $this->employeeRepository->getDataWhere([['user_id',$authUserId],['company_id',$company->id]])->first();
            $myPermission = $this->myPermissionCheck($employee);
        }

        $free_user = ($company->trial_period== 3 ) ? 0 : 1;
        $subscriptionLogQuery    =  $this->subscriptionLogRepository->getDataWhere([['company_id',$company->id]]);
        if($subscriptionLogQuery->count()>0){
            $lastUpdatedSubscription    =    $subscriptionLogQuery->orderBy('id','desc')->first();
            $monthDay   =    Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
            $now    =   Carbon::now();
            $currentMonthStart  =   Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay);
            if($now->gte($currentMonthStart)){
                $currentMonthEnd = Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->addDay(30);
            }else{
                $currentMonthEnd =   $currentMonthStart;
                $currentMonthStart =      Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->subDays(30);
            }
        }else{
            $currentMonthStart = new Carbon('first day of this month');
            $currentMonthEnd = new Carbon('last day of this month');
        }

        $sentDockets    = $this->sentDocketsRepository->getDataWhere([['sender_company_id',$company->id]])->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
        $emailDockets   = $this->emailSentDocketRepository->getDataWhere([['company_id',$company->id]])->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();

        $sentInvoice = $this->sentInvoiceRepository->getDataWhere([['company_id',$company->id]])->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
        $emailInvoice = $this->emailSentInvoiceRepository->getDataWhere([['company_id',$company->id]])->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();

        $totalMonthDockets  =   $sentDockets + $emailDockets;
        $totalMonthInvoices  =   $sentInvoice + $emailInvoice;

        if ($totalMonthDockets >= 5){
            $docket_limit = 0;
        }else{
            $docket_limit = 5-$totalMonthDockets;
        }

        if ($totalMonthInvoices>=1){
            $invoice_limit = 0;
        }else{
            $invoice_limit = 1-$totalMonthInvoices;
        }
        $user   = auth()->user();
        $user->slackChannel('rt-app-reopen')->notify(new AppOpen($user));
        return response()->json(['free_user'=>$free_user,'docket_limit'=>$docket_limit,'invoice_limit'=>$invoice_limit,'permission'=>$myPermission],200);
    }

    public function saveGridPrefiller($request){
        $parentId = $request->parentId;
        if($request->isdependent == 1){
            $data = array();
            $gridField = $this->docketFieldGridRepository->getDataWhere([['docket_field_id',$request->docketFieldId],['id', $request->gridFieldId]])->get()->first();
            $index = $this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$gridField->docket_prefiller_id]])->pluck('index')->toArray();
            if(count($index) > 0){
                if (max($index) >= $request->index){
                    $updateParentId = "";
                    $valueCount = Input::get('lastIndex');
                    for ($i = $request->index; $i <= $valueCount; $i++ ){
                        $valueData = Input::get('value_'.$i);
                        $docketPrefillerRequest = new Request();
                        $docketPrefillerRequest['docket_prefiller_id'] = $gridField->docket_prefiller_id;
                        $docketPrefillerRequest['label'] = $valueData;
                        if($i == $request->index){
                            $docketPrefillerRequest['index'] = $request->index;
                            $docketPrefillerRequest['root_id'] = $request->parentId;
                        }else{
                            $docketPrefillerRequest['index'] = $i;
                            $docketPrefillerRequest['root_id'] = $updateParentId;
                        }
                        $docketPrefiller = $this->docketPrefillerValueRepository->insertAndUpdate($docketPrefillerRequest);
                        $data[] = array(
                            'id'=>$docketPrefiller->id,
                            'value'=>$docketPrefiller->label,
                            'root_id'=>intval($docketPrefiller->root_id),
                            'index'=>  intval($docketPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketPrefiller->id;
                    }
                    $datas = FunctionUtils::buildAutoPrefillerTreeArrayList($data,$parentId,$parentId,$gridField);
                    return response()->json(['prefiller' => $datas],200);
                }
            }
        }else if($request->isdependent == 0){
            $data = array();
            $gridField = $this->docketFieldGridRepository->getDataWhere([['docket_field_id',$request->docketFieldId],['id', $request->gridFieldId]])->get()->first();
            $index = $gridField->gridFieldPreFiller->pluck('index')->toArray();
            if($index > 0){
                if (max($index) >= $request->index){
                    $updateParentId = "";
                    $valueCount = Input::get('lastIndex');
                    for ($i = $request->index; $i <= $valueCount; $i++ ){
                        $valueData = Input::get('value_'.$i);
                        $docketGridPrefillerRequest = new Request();
                        $docketGridPrefillerRequest['docket_field_grid_id'] = $request->gridFieldId;
                        $docketGridPrefillerRequest['value'] = $valueData;
                        if($i == $request->index){
                            $docketGridPrefillerRequest['index'] = $request->index;
                            $docketGridPrefillerRequest['root_id'] = $request->parentId;
                        }else{
                            $docketGridPrefillerRequest['index'] = $i;
                            $docketGridPrefillerRequest['root_id'] = $updateParentId;
                        }
                        $docketGridPrefiller = $this->docketGridPrefillerRepository->insertAndUpdate($docketGridPrefillerRequest);
                        $data[] = array(
                            'id'=>$docketGridPrefiller->id,
                            'value'=>$docketGridPrefiller->value,
                            'root_id'=>intval($docketGridPrefiller->root_id),
                            'index'=> intval($docketGridPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketGridPrefiller->id;
                    }

                    $datas = FunctionUtils::buildAutoPrefillerTreeArrayList($data,$parentId,$parentId,$gridField);
                    return response()->json(['prefiller' => $datas],200);
                }
            }
        }
        return response()->json(['message' => MessageDisplay::InvalidData],500);
    }

    public function savePrefiller($request){
        $parentId = $request->parentId;
        if($request->isdependent == 1){
            $data = array();
            $docketField = $this->docketFieldRepository->getDataWhere([['id', $request->docketFieldId]])->get()->first();
            if (max($this->docketPrefillerValueRepository->getDataWhere([['docket_prefiller_id',$docketField->docket_prefiller_id]])->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                    $valueData = Input::get('value_'.$i);
                    $docketPrefillerRequest = new Request();
                    $docketPrefillerRequest['docket_prefiller_id'] = $docketField->docket_prefiller_id;
                    $docketPrefillerRequest['label'] = $valueData;
                    if($i == $request->index){
                        $docketPrefillerRequest['index'] = $request->index;
                        $docketPrefillerRequest['root_id'] = $request->parentId;
                    }else{
                        $docketPrefillerRequest['index'] = $i;
                        $docketPrefillerRequest['root_id'] = $updateParentId;
                    }
                    $docketPrefiller = $this->docketPrefillerValueRepository->insertAndUpdate($docketPrefillerRequest);
                    $data[]   =  array(
                        'id'=> $docketPrefiller->id,
                        'value'=> $docketPrefiller->label,
                        'index'=>$docketPrefiller->index,
                        'root_id'=> intval($docketPrefiller->root_id),
                    );
                    $updateParentId = $docketPrefiller->id;
                }
                $datas = FunctionUtils::buildTreeArrayList($data,$parentId,$parentId);
                return response()->json(['prefiller' => $datas],200);
            }
        }else if($request->isdependent == 0){
            $data = array();
            $docketField = $this->docketFieldRepository->getDataWhere([['id', $request->docketFieldId]])->get()->first();
            if (max($docketField->docketPreFiller->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                    $valueData = Input::get('value_'.$i);
                    $docketPrefillerRequest = new Request();
                    $docketPrefillerRequest['docket_prefiller_id'] = $docketField->docket_prefiller_id;
                    $docketPrefillerRequest['label'] = $valueData;
                    if($i == $request->index){
                        $docketPrefillerRequest['index'] = $request->index;
                        $docketPrefillerRequest['root_id'] = $request->parentId;
                    }else{
                        $docketPrefillerRequest['index'] = $i;
                        $docketPrefillerRequest['root_id'] = $updateParentId;
                    }
                    $docketGridPrefiller = $this->docketFiledPreFillerRepository->insertAndUpdate($docketPrefillerRequest);
                    $data[]   =  array(
                        'id'=> $docketGridPrefiller->id,
                        'value'=> $docketGridPrefiller->value,
                        'index'=>$docketGridPrefiller->index,
                        'root_id'=> intval($docketGridPrefiller->root_id),
                    );
                    $updateParentId = $docketGridPrefiller->id;
                }
                $datas = FunctionUtils::buildTreeArrayList($data,$parentId,$parentId);
                return response()->json(['prefiller' => $datas],200);
            }
        }
        return response()->json(['message' => MessageDisplay::InvalidData],500);
    }

    public  function deleteDraft($request){
        $this->docketDraftRepository->getDataWhere([['user_id',auth()->user()->id]])->whereIn('id', $request->docket_draft_id)->delete();
    }

    public function nextDocketId($request){
        if($request->type == "DOCKET"){
            $authCompany = auth()->user()->companyInfo;
            $authUser = auth()->user();
            $docket = $this->docketRepository->getDataWhere([['id',$request->template_id],['company_id',$authCompany->id]])->get()->first();
            if($docket != null){
                $company = $authCompany;
                if($company->number_system == 1){
                    return response()->json(array('status' => false, 'message'=>'Invalid Data'));
                }else{
                    if($docket->is_docket_number == 1){
                        $sentDocket = "";
                        $findUserDocketCount = $this->sentDocketsRepository->getDataWhere([['user_id', $authUser->id],['sender_company_id', $authCompany->id],['docket_id',$request->template_id]])->pluck('user_docket_count')->toArray();
                        $findUserEmailDocketCount = $this->emailSentDocketRepository->getDataWhere([['user_id', $authUser->id],['company_id', $authCompany->id],['docket_id',$request->template_id]])->pluck('user_docket_count')->toArray();
                        $mergeData =array_merge($findUserDocketCount,$findUserEmailDocketCount);
                        if(count($mergeData) == 0){
                            $maxData = 0;
                        }else{
                            $maxData=  max($mergeData);
                        }

                        if($maxData == 0){
                            $uniquemax = 0;
                        }else{
                            $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                        }
                        $employeeData = $this->employeeRepository->getDataWhere([['user_id', $authUser->id],['company_id', $authCompany->id]])->get();
                        if($employeeData->count() == 0){
                            if($docket->hide_prefix == 1){
                                $sentDocket = $docket->id."-1-".($uniquemax+1);
                            }else{
                                $sentDocket = "RT-".$docket->prefix."-".$docket->id."-1-".($uniquemax+1);
                            }
                        }else{
                            if($docket->hide_prefix == 1){
                                $sentDocket = $docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }else{
                                $sentDocket = "RT-".$docket->prefix."-".$docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                            }
                        }
                        return response()->json(['data'=>$sentDocket],200);
                    }else{
                        return response()->json(['message'=> MessageDisplay::NumberSystemCheck],500);
                    }
                }
            }
        }
        return response()->json(['message'=> MessageDisplay::InvalidData],500);
    }

    public function updateDocketAprovalMethod($request){
        $this->docketRepository->getDataWhere([['id',$request->docket_id],['company_id',auth()->user()->companyInfo->id]])->update(['docketApprovalType'=>$request->docket_approval_type]);
    }

    public function logout(){
        Auth::logout();
        $this->userRepository->getDataWhere([['id',auth()->user()->id]])->update(array('deviceToken'=>''));
    }



    

    function emailSentDocketDateFilter($date,$sentemailDocketsQuery,$type){
        return $this->commonDocketDateFilter($date,$sentemailDocketsQuery,$type,'emailSentDocketValue');
    }

    function sentDocketDateFilter($date,$sentemailDocketsQuery,$type){
        return $this->commonDocketDateFilter($date,$sentemailDocketsQuery,$type,'sentDocketsValue');
    }

    function commonDocketDateFilter($date,$sentemailDocketsQuery,$type,$docketType){
        $carbonDate = Carbon::parse($date);
        unset($tempSentDocket);
        $tempSentDocket = array();
        foreach ($sentemailDocketsQuery->get() as $row) {
            $flag = false;
            $docketTemplate = $row->docketInfo();
            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');
            if($docketType == 'sentDocketsValue'){
                $getAllSentDocketDateFieldsValues = $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id', $row->id]])->whereIn('docket_field_id', $docketFieldsIds)->get();
            }else{
                $getAllSentDocketDateFieldsValues = $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id', $row->id]])->whereIn('docket_field_id', $docketFieldsIds)->get();
            }
            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                try{
                    Carbon::parse($rowValue->value);
                    if ($rowValue->value != "" && $rowValue->value != "null") {
                        if (($type == 'from') ? $carbonDate->lte(Carbon::parse($rowValue->value)) : $carbonDate->gte(Carbon::parse($rowValue->value)))
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
        return ($docketType == 'sentDocketsValue') ? $this->sentDocketsRepository->getDataWhereIn('id', $tempSentDocket) : $this->emailSentDocketRepository->getDataWhereIn('id', $tempSentDocket);
    }

    function filterDocumentIncludes($repository,$request,$employeeIds){
        $authUser = auth()->user()->id;
        if($request->document_type == 1){
            $receivedSentDocketIds  =  $this->sentDocketRecipientRepository->getDataWhereIn('user_id',$employeeIds)->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
        }
        $sentDockeOrInvoiceIds =  $repository->getDataWhereIn('user_id',$employeeIds)->pluck('id')->toArray();

        if($request->document_type == 1){
            if(count($receivedSentDocketIds)!=0 && count($sentDockeOrInvoiceIds) != 0){
                $totalSentDocketOrInvoiceIds     =   array_unique(array_merge($sentDockeOrInvoiceIds, $receivedSentDocketIds));
            }else if(count($receivedSentDocketIds)!=0){
                $totalSentDocketOrInvoiceIds =  $sentDockeOrInvoiceIds;
            }else{
                $totalSentDocketOrInvoiceIds =  $receivedSentDocketIds;
            }
        }else if($request->document_type == 2){
            if(count($sentDockeOrInvoiceIds) != 0){
                $totalSentDocketOrInvoiceIds = $sentDockeOrInvoiceIds;
            }    
        }
        
        $sentInvoicesOrDocketQuery = $repository->getModel()->query();

        if ($request->from){
            $sentInvoicesOrDocketQuery->whereDate('created_at', '>=', $request->from);
        }

        if ($request->to){
            $sentInvoicesOrDocketQuery->whereDate('created_at', '<=', $request->to);
        }

        $sentInvoicesOrDocketQuery->whereIn('id',$totalSentDocketOrInvoiceIds);

        $sentInvoicesOrDocket     =   $sentInvoicesOrDocketQuery->get();

        if($request->search != ""){

            $matchedIDArray = array();

            $searchKey = $request->search;

            //check docket id
            $matchedIDArray = $repository->getDataWhere([['id', 'like', '%' . $searchKey . '%']])->whereIn('id', $totalSentDocketOrInvoiceIds)->pluck('id')->toArray();
            if (count($matchedIDArray) > 0) {
                $totalSentInvoiceIds = array_merge(array_diff($totalSentDocketOrInvoiceIds, $matchedIDArray), array_diff($matchedIDArray, $totalSentDocketOrInvoiceIds));
            }

            //check docket info(sender name, sender company name , receiver name, company name //
            $sentInvoiceOrDocketQuery = $repository->getDataWhereIn('id', $totalSentInvoiceIds)->get();

            foreach ($sentInvoiceOrDocketQuery as $row){
                $senderName=$row->receiverUserInfo->first_name." ".$row->receiverUserInfo->last_name;
                $senderCompanyName  =   $row->senderCompanyInfo->name;
                if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }

                if($request->document_type == 1){
                    //receiver info
                    $receiversName  =   "";
                    if($row->recipientInfo){
                        $sn = 1;
                        foreach($row->recipientInfo as $recipient):
                            $receiversName  =   $receiversName.@$recipient->userInfo->first_name." ". @$recipient->userInfo->last_name;
                            if($sn!=$row->recipientInfo->count()):
                                $receiversName  =   $receiversName.", ";
                            endif;
                            $sn++;
                        endforeach;

                    }
                    //for receivers company name
                    $recipientIds   =   $row->recipientInfo->pluck('user_id');
                    $companyEmployeeQuery   =  $this->employeeRepository->getDataWhereIn('user_id',$recipientIds)->pluck('company_id');
                    $empCompany    =   $this->companyRepository->getDataWhereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                    $adminCompanyQuery   =    $this->companyRepository->getDataWhereIn('user_id',$recipientIds)->pluck('id')->toArray();
                    $company    =   $this->companyRepository->getDataWhereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

                    if(preg_match("/".$searchKey."/i",$receiversName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }

                    if(preg_match("/".$searchKey."/i",$row->docketInfo->title)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }
                }else if($request->document_type == 2){
                    $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
                    $senderCompanyName  =   $row->senderCompanyInfo->name;
                    if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }
                    if(preg_match("/".$searchKey."/i",$row->invoiceInfo->title)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }  
                }

                if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                    $matchedIDArray[]   =   $row->id;
                    continue;
                }

                if($request->document_type == 1){
                    if($row->sentDocketValue){
                        foreach ($row->sentDocketValue as $rowValue){
                            if($rowValue->docketFieldInfo->docket_filed_category_id!=5 && $rowValue->docketFieldInfo->docket_filed_category_id!=7 && $rowValue->docketFieldInfo->docket_filed_category_id!=8 && $rowValue->docketFieldInfo->docket_filed_category_id!=9 &&
                                $rowValue->docketFieldInfo->docket_filed_category_id!=12 && $rowValue->docketFieldInfo->docket_filed_category_id!=13 && $rowValue->docketFieldInfo->docket_filed_category_id!=14){

                                if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                                    $matchedIDArray[]   =   $row->id;
                                }
                            }
                        }
                    }
                }
            }

            $sentInvoicesOrDocket     =    $repository->getDataWhereIn('id',$matchedIDArray)->get();
        }


        $docketsOrInvoice = array();

        foreach($sentInvoicesOrDocket as $result){
            $userId  = 	$result->user_id;
            $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
            $profile    =    AmazoneBucket::url() . $result->senderUserInfo->image;
            $company    =   $result->senderCompanyInfo->name;

            if($result->user_id == $authUser){
                if($request->document_type == 2){
                    $userId  = 	$result->receiver_user_id;
                    $userName  =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $profile    =    AmazoneBucket::url() . $result->receiverUserInfo->image;
                    $company    =   $result->receiverCompanyInfo->name;
                }

                if($result->status==0):
                    $invoiceOrDocketStatus   =   "Sent";
                endif;
            } else {
                if($result->status==0):
                    $invoiceOrDocketStatus   =   "Received";
                endif;
            }

            if($result->status==1)
                $invoiceOrDocketStatus ="Approved";

            if($request->document_type == 1){
                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->userInfo != null){
                        if($recipient->id == $recipientsQuery->first()->id)
                            $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                        else
                            $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    }
                }

                //approval text
                $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]]);
                $totalRecipientApprovals    =   $sentDocketRecipientApprovalData->count();
                $totalRecipientApproved     =   $sentDocketRecipientApprovalData->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->where('user_id',$authUser)->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$result->id]])->where('user_id',$authUser)->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }


                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                $docketsOrInvoice[]   = new InvoiceFilterDocketResource($result,'filterDocument',$userName,$company,null,null,$recipientData,$result->senderUserInfo->image,
                                            $approvalText,$isApproval,$isApproved,null,$invoiceOrDocketStatus);
                // $docketsOrInvoice[]   =   array('id' => $result->id,
                //     'user_id'   =>  $userId,
                //     'docketName' => $result->docketInfo->title,
                //     'sender' => $userName,
                //     'profile' => asset($result->senderUserInfo->image),
                //     'company'   =>  $company,
                //     'recipient' => $recipientData,
                //     'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                //     'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                //     'isApproved'    =>  $result->status,
                //     'approvalText'  =>  $approvalText,
                //     'isApproval'    =>  $isApproval,
                //     'isApproved'    =>  $isApproved,
                //     'status'    => $invoiceOrDocketStatus);
            }else if($request->document_type == 2){
                $docketsOrInvoice[]   = new InvoiceConversationChatResource($result,$userName,$company,$invoiceOrDocketStatus,$profile,'2');
            //     $docketsOrInvoice[]   =   array('id' => $result->id,
            //         'user_id'   =>  $userId,
            //         'invoiceName' => $result->invoiceInfo->title,
            //         'sender' => $userName,
            //         'profile' => $profile,
            //         'company'   =>  $company,
            //         'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
            //         'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y h:i:s'),
            //         'status'    => $invoiceOrDocketStatus);
            }
        }

        //conversation sorting according to dateAdded
        $docketsOrInvoice = FunctionUtils::conversationArrayDateSorting($docketsOrInvoice);
        // $size = count($docketsOrInvoice);
        // for($i = 0; $i<$size; $i++){
        //     for ($j=0; $j<$size-1-$i; $j++) {
        //         if (strtotime($docketsOrInvoice[$j+1]["dateSorting"]) > strtotime($docketsOrInvoice[$j]["dateSorting"])) {
        //             $tempArray   =    $docketsOrInvoice[$j+1];
        //             $docketsOrInvoice[$j+1] = $docketsOrInvoice[$j];
        //             $docketsOrInvoice[$j]  =   $tempArray;
        //         }
        //     }
        // }

        return $docketsOrInvoice;
    }

    function commonNotification($sentDocketId,$authUser,$sentDocket){
        $userNotificationRequest   =   new Request();
        $userNotificationRequest['sender_user_id']   =    $authUser;
        $userNotificationRequest['receiver_user_id'] = $sentDocket->user_id;
        $userNotificationRequest['type']     =   3;
        $userNotificationRequest['title']    =   'Docket Rejected';
        $userNotificationRequest['message']  =   "Your Docket has been rejected by". auth()->user()->first_name.' '.auth()->user()->last_name;
        $userNotificationRequest['key']      =   $sentDocketId;
        $userNotificationRequest['status']   =   0;
        $userNotification = $this->userNotificationRepository->insertAndUpdate($userNotificationRequest);

        if ($sentDocket->senderUserInfo->deviceToken != ""){
            if ($sentDocket->senderUserInfo->device_type == 2)
            {
                $this->firebaseApi->sendiOSNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message );
            }
            if ($sentDocket->senderUserInfo->device_type == 1)
            {
                $this->firebaseApi->sendAndroidNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
            }
        }
    }

    function myPermissionCheck($userType){
        if ($userType->can_docket== 1) {
            $myPermission[] = array('id' => 1, 'name' => 'Docket');
        }
        if ($userType->can_invoice== 1) {
            $myPermission[] = array('id' => 2, 'name' => 'Invoice');
        }
        if ($userType->can_timer== 1) {
            $myPermission[] = array('id' => 3, 'name' => 'Timer');
        }
        return $myPermission;
    }

    function uploadFiles($request){
        $number = 0;
        $date = Carbon::now()->format('d-M-Y');
        $arrayImage = [];
        if($request->has('docket_field_category_id')){
            if($request->docket_field_category_id == 5){
                if($request->has('file')){
                    $images = $request->file;
                    foreach ($images as  $keys => $img){
                        // $ext = $img->getClientOriginalExtension();
                        // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                        $dest = 'files/draft/images/'.$date;
                        // if(!is_dir($dest)){
                        //     mkdir($dest);
                        // }
                        // $img->move($dest, $filename);
                        // array_push($arrayImage, asset($dest . '/' . $filename));

                        array_push($arrayImage, AmazoneBucket::url() . FunctionUtils::imageUpload($dest,$img));
                        $number++;
                    }
                }
            }
        }
        else if($request->has('signature_image')){
            $images = $request->signature_image;  // your base64 encoded
            foreach ($images as  $key => $img){
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $name = 'signature'.time()."-".$number.'.'.'png';
                $path = 'files/docket/images/';
                $imageName = $path.$name;
                if(!is_dir($path)){
                    mkdir($path);
                }
                // \File::put(public_path(''). '/' . $imageName, base64_decode($img));
                // $temp['url'] =  asset($imageName);
                $temp['url'] =  AmazoneBucket::url() . FunctionUtils::imageUpload($imageName,base64_decode($img),$number,true);;
                $temp['name'] = $request->signature_name[$key];
                $temp['signature_unique_count'] = $request->signature_unique_count[$key];
                
                array_push($arrayImage, $temp);
                
                $number++;
            }
        }
        else if($request->has('sketchpad')){
            $images = $request->sketchpad;  // your base64 encoded
            foreach ($images as  $keys => $img){
                $img = str_replace('data:image/png;base64,', '', $img);
                $img = str_replace(' ', '+', $img);
                $name = 'sketchpad'.time()."-".$number.'.'.'png';
                $urlPath = 'files/docket/images/';
                $imageName = $urlPath.$name;
                if(!is_dir($urlPath)){
                    mkdir($urlPath);
                }
                // \File::put(public_path(''). '/' . $imageName, base64_decode($img));
                // array_push($arrayImage, asset($imageName));

                array_push($arrayImage, AmazoneBucket::url() . FunctionUtils::imageUpload($imageName,base64_decode($img),$number,true));
                $number++;
            }
        }
        else if($request->has('explanation_file')){
            $images = $request->explanation_file;  // your base64 encoded
            foreach ($images as  $key => $img){
                // $ext = $img->getClientOriginalExtension();
                // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                $dest = 'files/draft/images/'.$date;
                // if(!is_dir($dest)){
                //     mkdir($dest);
                // }
                // $img->move($dest, $filename);
                // array_push($arrayImage, asset($dest . '/' . $filename));

                array_push($arrayImage, AmazoneBucket::url() . FunctionUtils::imageUpload($dest,$img));
                $number++;
            }
        }
        else if($request->has('image_file')){
            $images = $request->image_file;
            foreach ($images as  $keys => $img){
                // $ext = $img->getClientOriginalExtension();
                // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                $dest = 'files/draft/images/'.$date;
                // if(!is_dir($dest)){
                //     mkdir($dest);
                // }
                // $img->move($dest, $filename);
                // array_push($arrayImage, asset($dest . '/' . $filename));

                array_push($arrayImage, AmazoneBucket::url() . FunctionUtils::imageUpload($dest,$img));
                $number++;
            }
        }
       return response()->json($arrayImage);
    }

    public function taskManagement($request){
        if(!$request->headers->has('companyId')){
            if(isset(auth()->user()->id)){
                $request->headers->set('userId', auth()->user()->id);
                $request->headers->set('companyId', auth()->user()->companyInfo->id);
            }
        }
        $employeesQuery   =  $this->employeeRepository->getDataWhere([['company_id',$request->header('companyId')]])->get();
        $companyAdmin   =   $this->companyRepository->getDataWhere([['id',$request->header('companyId')]])->first();
        $employees[]  = array('id'    =>  $companyAdmin->user_id, 'name'  =>  $companyAdmin->userInfo->first_name. " ".$companyAdmin->userInfo->last_name);
        foreach ($employeesQuery as $row) {
            if(@$row->userInfo->isActive == 1) {
                $employees[] = array( 'id' => $row->user_id, 'name' => $row->userInfo->first_name . " " . $row->userInfo->last_name);
            }
        }

        $templates = $this->docketRepository->getDataWhere([["company_id", $request->header('companyId')],['is_archive',0]])->select('id','title')->orderBy('title', 'asc')->get();

        $employeesID = $this->employeeRepository->getDataWhere([['company_id', $request->header('companyId')]])->pluck('user_id');
        $employeesID->push($companyAdmin->user_id);

        $machines = $this->machineRepository->getDataWhere([['company_id',$request->header('companyId')]])->get();
        $assignDocketUserConnectionData = $this->assignDocketUserConnectionRepository->getDataWhere([['user_id',$request->header('userId')]])->groupBy('assign_docket_id')->pluck('assign_docket_id');
        $assignDocketUserFrom = $this->assignDocketUserRepository->getDataWhere([
                        [DB::raw('DATE_FORMAT(from_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->from_date)->format('Y-m-d')],
                        [DB::raw('DATE_FORMAT(from_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->to_date)->format('Y-m-d')]
                    ])->whereIn('id',$assignDocketUserConnectionData)->with('assignDocketUserConnection')->get();
        $assignDocketUserTo = $this->assignDocketUserRepository->getDataWhere([
                        [DB::raw('DATE_FORMAT(to_date,"%Y-%m-%d")'), ">=",Carbon::parse($request->from_date)->format('Y-m-d')],
                        [DB::raw('DATE_FORMAT(to_date,"%Y-%m-%d")'), "<=",Carbon::parse($request->to_date)->format('Y-m-d')]
                    ])->whereIn('id',$assignDocketUserConnectionData)->with('assignDocketUserConnection')->get();
        $assignDocketUser = $assignDocketUserFrom->merge($assignDocketUserTo);
        $calender_data = collect();
        foreach($assignDocketUser as $value){
            $temp = [];
            $temp['id'] = $value->id;
            $temp['calendarId'] = '1';
            $temp['title'] = $value->name;
            $temp['category'] = 'time';
            $temp['start'] = $value->from_date;
            $temp['end'] = $value->to_date;
            $temp['bgColor'] = $value->bgcolor;
            $temp['body'] = 'body';
            $temp['state'] = $value->status;
            $machineDetail = [];
            $userDetail = [];
            foreach($value->assignDocketUserConnection as $dataValue){
                if($dataValue->machine){
                    $tempMachine['id'] =  $dataValue->machine->id;
                    $tempMachine['name'] =  $dataValue->machine->name;
                    $tempMachine['image'] =  $dataValue->machine->image;
                    array_push($machineDetail,$tempMachine);
                }
                if($dataValue->user){
                    $tempUser['id'] =  $dataValue->user->id;
                    $tempUser['name'] =  $dataValue->user->first_name . ' ' . $dataValue->user->last_name;
                    $tempUser['image'] =  $dataValue->user->image;
                    array_push($userDetail,$tempUser);
                }
            }
            $machineList = $value->assignDocketUserConnection->groupBy('machine_id')->keys()->filter(function ($value, $key) {
                if($value){
                    return $value;
                }
            })->toArray();
            
            $employeeList = $value->assignDocketUserConnection->groupBy('user_id')->keys()->filter(function ($value, $key) {
                if($value){
                    return $value;
                }
            })->toArray();
            $docketTemplate = $value->assignDocketUserConnection->groupBy('docket_id')->keys()->filter(function ($value, $key) {
                if($value){
                    return $value;
                }
            })->toArray();
            $temp['raw'] = [
                'comment' => $value->comment,
                'machineList' => array_values($machineList),
                'employeeList' => array_values($employeeList),
                'docketTemplate' => array_values($docketTemplate),
                'machineDetail' => array_map("unserialize", array_unique(array_map("serialize", $machineDetail))),
                'userDetail' => array_map("unserialize", array_unique(array_map("serialize", $userDetail)))
            ];
            $calender_data->push($temp);
        }

        return response()->json(['employees' => $employees, 'templates' => $templates, 'machines' => $machines, 'calender_data' => $calender_data],200);
    }

    public function taskManagementById($request,$docket_id,$employee,$docketTemplate){
        if(auth()->user() != null){
            $user_type = auth()->user()->user_type;
            $user_id = auth()->user()->id;
            $companyId = auth()->user()->companyInfo->id;
        }else{
            $user_type = $this->userRepository->getDataWhere([['id',$request->header('userId')]])->first()->user_type;
            $user_id = $request->header('userId');
            $companyId = $request->header('companyId');
        }
        if($user_type == 2){
            $docketTemplate = $this->docketRepository->getDataWhere([["company_id", $companyId],['is_archive',0]])->orderBy('title', 'asc')->get();
        }else{
            $docketTemplate = $docketTemplate;
        }
        $emailClients = $this->emailUserList();
        $employeeList = $employee;
        $docketDraftAssign = $this->docketDraftsAssignRepository->getDataWhere([['assign_docket_user_id',$request->assign_docket_id],
                            ['docket_id',$docket_id],['user_id',$user_id]])->first();
        $fromAssign = 'true';
        if($docketDraftAssign){
            $docketDraft = $this->docketDraftRepository->getDataWhere([['id',$docketDraftAssign->docket_draft_id]])->first();
            if($docketDraft){
                return response()->json(['docketTemplate' => $docketTemplate, 'emailClients' => $emailClients, 'employeeList' => $employeeList, 'docketDraft' => $docketDraft,
                                'fromAssign' => $fromAssign],200);
            }
        }else{
            $docketDraft = null;
        }
       
        $templateId = $docket_id;
        $temp['docket_template_id'] = $docket_id;
        $temp['assign_docket_id'] = $request->assign_docket_id;
        $temp['employeeList'] = $request->employeeList;
        $temp['machineList'] = $request->machineList;
        $data = json_encode($temp);
        return response()->json(['docketTemplate' => $docketTemplate, 'emailClients' => $emailClients, 'employeeList' => $employeeList, 'docketDraft' => $docketDraft,
                                'templateId' => $templateId, 'data' => $data],200);
    }

    public function taskStatusManagement($request){
        $this->assignDocketUserRepository->getDataWhere([['id',$request->assign_docket_id]])->update(['status' => $request->status]);
    }
}