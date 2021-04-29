<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\MessageDisplay;
use App\Helpers\V2\StaticValue;
use App\Http\Resources\V2\Docket\DocketEmailConversationResource;
use App\Http\Resources\V2\Docket\SearchDocketResource;
use App\Http\Resources\V2\Docket\SentDocketResource;
use App\Http\Resources\V2\Invoice\InvoiceFilterDocketResource;
use App\Http\Resources\V2\Invoice\InvoiceTimelineResource;
use App\Services\V2\ConstructorService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use overint\MailgunValidator;
use App\Helpers\V2\AmazoneBucket;

class EmailConversationService extends ConstructorService {

    public function getLatestConversationList($request){
        $conversationArray      =   array();
        $uniqueRecipients       =   array();
        //get all the sent docket associate with logged in user
        $sentDocketRecipientsQueryID    =  $this->sentDocketRecipientRepository->getDataWhere([['user_id',auth()->user()->id]])->orderBy('id','desc')->get()->pluck('sent_docket_id')->toArray();
        $sentDocketQueryID              =  $this->sentDocketsRepository->getDataWhere([['user_id',auth()->user()->id]])->orderBy('id','desc')->get()->pluck('id')->toArray();
        $totalSentDocketID              =  array_unique(array_merge($sentDocketRecipientsQueryID, $sentDocketQueryID));
        $filteredSentDocketsID          =  array();

        if(count($totalSentDocketID) > 0) {
            $sentDocketsQuery = $this->sentDocketsRepository->getDataWhereIn('id', $totalSentDocketID)->orderBy('id', 'desc')->get();

            foreach ($sentDocketsQuery as $sentDocket) {
                if (count($uniqueRecipients) == 0) {
                    $uniqueRecipients[] = array_unique(array_merge($sentDocket->recipientInfo->pluck('user_id')->toArray(), array($sentDocket->user_id)));
                    $filteredSentDocketsID[] = $sentDocket->id;
                } else {
                    $tempRecipients = array_unique(array_merge($sentDocket->recipientInfo->pluck('user_id')->toArray(), array($sentDocket->user_id)));
                    //check old uniqueRecipients

                    $flag = true;
                    for ($i = 0; $i < count($uniqueRecipients); $i++) {
                        if (FunctionUtils::array_equal($uniqueRecipients[$i], $tempRecipients)) {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        $filteredSentDocketsID[] = $sentDocket->id;
                        $uniqueRecipients[] = $tempRecipients;
                    }
                }
            }

            $filteredSentDocketsQuery = $this->sentDocketsRepository->getDataWhereIn('id', $filteredSentDocketsID)->with('recipientInfo')->orderBy('created_at', 'desc')->get();
            foreach ($filteredSentDocketsQuery as $row) {
                if ($row->recipientInfo->count()) {
                    $conversationName = "";
                    $recipientsArray = [];
                    if ($row->recipientInfo->count() > 1) {
                        $tempRecipientIds = array_values(array_unique(array_merge($row->recipientInfo->pluck('user_id')->toArray(), array($row->user_id))));
                        $sn = 1;
                        foreach ($tempRecipientIds as $recipient) {
                            $recipientData = $this->userRepository->getDataWhere([['id',$recipient]])->first();
                            if($recipientData != null){
                                if ($sn == 1) {
                                    $conversationName = $recipientData->first_name;
                                } else {
                                    $conversationName = $conversationName . ", " . $recipientData->first_name;
                                }
                            }
                            $sn++;
                        }
                        $recipientsArray["sentDocket" . $row->id] = $tempRecipientIds;
                        $conversationProfile = asset("assets/dashboard/images/multipleRecipient2.png");
                    } else {
                        if ($row->user_id == auth()->user()->id) {
                            $userData = @$this->userRepository->getDataWhere([['id',$row->recipientInfo->first()->user_id]])->first();
                            if($userData != null){
                                $conversationName = @$userData->first_name . " " . @$userData->last_name;
                                $conversationProfile = AmazoneBucket::url() . $userData->image;
                                $recipientsArray["sentDocket" . $row->id] = array($userData->id);
                            }
                        } else {
                            $userData = @$this->userRepository->getDataWhere([['id',$row->user_id]])->first();
                            $conversationName = $userData->first_name . " " . $userData->last_name;
                            $conversationProfile = AmazoneBucket::url() . $userData->image;
                            $recipientsArray["sentDocket" . $row->id] = array($row->user_id);
                        }
                    }
                    $companyIds = array();
                    $companies = "";
                    foreach ($recipientsArray["sentDocket" . $row->id] as $userId) {
                        //find company admin
                        $companyData = $this->companyRepository->getDataWhere([['user_id', $userId]])->first();
                        if ($companyData != null) {
                            $companyIds[] = $companyData->id;
                        } else {
                            $companyIds[] = $this->employeeRepository->getDataWhere([['user_id', $userId]])->first()->company_id;
                        }
                    }
                    $sn = 1;
                    foreach (array_unique($companyIds) as $companyId) {
                        $companyData = $this->companyRepository->getDataWhere([['id', $companyId]])->first();
                        if ($sn == 1) {
                            $companies = $companyData->name;
                        } else {
                            $companies = $companies . ", " . $companyData->name;
                        }
                        $sn++;
                    }

                    //check approved or not /.if status == 1 approved
                    if($row->status == 1){
                        $status    =    "Approved";
                    }elseif($row->user_id==auth()->user()->id){
                        $status     =   "Sent";
                    }else{
                        $status     =   "Received";
                    }

                    $lastDocket = array('docketName' => $row->docketInfo->title, 'sender' => $row->senderUserInfo->first_name,'status' => $status);
                    $conversation = array('id' => $row->id,
                        'name' => $conversationName,
                        'dateAdded' => Carbon::parse($row->created_at)->format('d-M-Y'),
                        'profile' => $conversationProfile,
                        'companies' => $companies,
                        'recipients' => $recipientsArray["sentDocket" . $row->id]);
                    $conversationArray[] = array('conversation' => $conversation, 'lastDocket' => $lastDocket);
                }
            }
        }
        return $conversationArray;
    }

    public function getLatestEmailInvoiceConversationList($request){
        $sentEmailInvoiceReceiverId     = $this->emailSentInvoiceRepository->getDataWhere([['user_id',auth()->user()->id]])->select('receiver_user_id')->orderBy('created_at','desc')->distinct()->get();
        $conversationArray  =   array();

        foreach ($sentEmailInvoiceReceiverId as $row){
            $sentEmailInvoiceQuery  =   $this->emailSentInvoiceRepository->getDataWhere([['receiver_user_id',$row->receiver_user_id],['user_id',auth()->user()->id]])->orderBy('created_at','desc')->first();
            if($sentEmailInvoiceQuery->status==1)
                $invoiceStatus ="Approved";
            else
                $invoiceStatus   =   "Sent";

            $conversationArray[]   = new SearchDocketResource($sentEmailInvoiceQuery,'emailInvoice',null,null,null,null,$invoiceStatus);
        }
        $conversationArray = FunctionUtils::conversationArrayDateSorting($conversationArray);
        return $conversationArray;
    }

    public function getLatestEmailConversationList(Request $request){
        $company = auth()->user()->companyInfo;
        if($company->id != 1){
            $conversationArray  =   array();
            $uniqueRecipients   =   array();
            $filteredEmailSentDocketsID  =   array();
            $emailSentDocketQuery    =   $this->emailSentDocketRepository->getDataWhere([['user_id',auth()->user()->id]])->with('recipientInfo')->orderBy('created_at','desc')->get();

            foreach ($emailSentDocketQuery as $emailSentDocket) {
                if(count($uniqueRecipients) == 0){
                    $uniqueRecipients[] =   $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $recipientsArray["emailSentDocket" . $emailSentDocket->id] = $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $filteredEmailSentDocketsID[] =   $emailSentDocket->id;
                }else{
                    $tempRecipients     =    $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $flag   =    true;
                    for($i = 0; $i<count($uniqueRecipients); $i++){
                        if(FunctionUtils::array_equal($uniqueRecipients[$i],$tempRecipients)){
                            $flag   =    false;
                        }
                    }
                    if($flag){
                        $filteredEmailSentDocketsID[]   =   $emailSentDocket->id;
                        $uniqueRecipients[]             =   $tempRecipients;
                        $recipientsArray["emailSentDocket" . $emailSentDocket->id] = $tempRecipients;
                    }
                }
            }

            $filteredEmailSentDocketsQuery  =  $this->emailSentDocketRepository->getDataWhereIn('id',$filteredEmailSentDocketsID)->orderBy('created_at','desc')->get();
            foreach ($filteredEmailSentDocketsQuery as $row) {
                if($row->status==1)
                    $docketStatus ="Approved";
                else
                    $docketStatus   =   "Sent";

                $recipientName =   "";
                foreach($row->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($row->recipientInfo->count() > 1)
                        if ($row->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }
                }
                $profile    =    "";
                if($row->recipientInfo->count()>0){
                    $profile    =   asset("assets/dashboard/images/multipleRecipient2.png");
                }else{
                    $profile    =   AmazoneBucket::url() . $row->senderUserInfo->image;
                }
                $userName = $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
                $conversationArray[]   = new SearchDocketResource($row,'Emaildocket',$row->user_id,$userName,$profile,$row->senderCompanyInfo->name,$docketStatus,
                            $recipientsArray["emailSentDocket" . $row->id],null,null,null,null,null,$recipientName);
            }//foreach end
        }else{
            $sentEmailDocketReceiverId    =  $this->emailSentDocketRepository->getDataWhere([['user_id',auth()->user()->id]])->select('receiver_user_id')->orderBy('created_at','desc')->distinct()->get();
            $conversationArray  =   array();

            foreach ($sentEmailDocketReceiverId as $row){
                $sentEmailDocketQuery   =   $this->emailSentDocketRepository->getDataWhere([['receiver_user_id',$row->receiver_user_id],['user_id',auth()->user()->id]])->orderBy('created_at','desc')->first();
                if($sentEmailDocketQuery->status==1)
                    $docketStatus ="Approved";
                else
                    $docketStatus   =   "Sent";

                $conversationArray[]   =  new DocketEmailConversationResource($sentEmailDocketQuery,'emailConversation',$docketStatus);
            }
        }
        $conversationArray = FunctionUtils::conversationArrayDateSorting($conversationArray);
        return $conversationArray;
    }

    public function getLatestEmailDocketHome($request){
        $conversationArray  =   array();
        $sentEmailDocketQuery   =  $this->emailSentDocketRepository->getDataWhere([['user_id',auth()->user()->id]])->take(10)->orderBy('created_at','desc')->get();
        foreach ($sentEmailDocketQuery as $result) {
                $docketStatus = "Sent";

            $sender     =    "";
            $recipientName  =    "";
            foreach($result->recipientInfo as $recipient) {
                $sender = $sender . "" . $recipient->emailUserInfo->email;

                if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                    $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                }else{
                    $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                }
                if ($result->recipientInfo->count() > 1)
                    if ($result->recipientInfo->last()->id != $recipient->id){
                        $sender = $sender . ", ";
                        $recipientName  = $recipientName.", ";
                    }

            }

            //approval text
            $emailSentDocketRecipient = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$result->id],['approval',1]])->get();
            $totalRecipientApprovals    =   count($emailSentDocketRecipient);
            $totalRecipientApproved     =   count($emailSentDocketRecipient->where('status',1));

            if ($totalRecipientApproved == $totalRecipientApprovals ){
                $approvalText               =  "Approved";
            }else{
                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
            }

            $senderUser                 =   auth()->user();
            $company                    =   auth()->user()->companyInfo;
            if($company->id==1){
                $sender   = $senderUser->first_name." ".$senderUser->last_name;
            }
            $conversationArray[] = new InvoiceFilterDocketResource($result,'emailConversation',$sender,$company->name,null,null,$recipientName,
                                    $senderUser->image,$approvalText,null,$result->status,$result->formatted_id);
        }
        $conversationArray = FunctionUtils::conversationArrayDateSorting($conversationArray);
        return $conversationArray;
    }

    public function getLatestEmailInvoiceHome($request){
        $conversationArray  =   array();
        $sentEmailInvoiceQuery  = $this->emailSentInvoiceRepository->getDataWhere([['user_id',auth()->user()->id]])->take(10)->orderBy('created_at','desc')->get();
        foreach ($sentEmailInvoiceQuery as $result) {
            if ($result->status == 1)
                $invoiceStatus = "Approved";
            else
                $invoiceStatus = "Sent";
            $userName = $result->senderUserInfo->first_name.' '.$result->senderUserInfo->last_name;
            $profile = AmazoneBucket::url() . $result->senderUserInfo->image;
            $companyName   = $result->senderCompanyInfo->name;
            $conversationArray[] = new SearchDocketResource($result,'invoice',$result->receiver_user_id,$userName,$profile,$companyName,
                                    $invoiceStatus,null,null,null,null,null,'companyInvoiceId');
        }
        $conversationArray = FunctionUtils::conversationArrayDateSorting($conversationArray);
        return $conversationArray;
    }

    public function getConversationChatByUserId($userId){
        $userId = auth()->user()->id;
        $companyData = auth()->user()->companyInfo;
        $sentDocketQuery    =  $this->sentDocketsRepository->getDataWhere([['user_id', $userId],['receiver_user_id', $userId]])
                                                            ->orWhere([['receiver_user_id', $userId],['user_id', $userId]])
                                                            ->with('receiverUserInfo','companyInfo','senderCompanyInfo','senderUserInfo','docketInfo');
        if($sentDocketQuery->count()>0){
            $resultQuery = $sentDocketQuery->orderBy('created_at','desc')->get();
            foreach ($resultQuery as $result){
                if($result->company_id == $companyData->id):
                    $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $company    =   $result->senderCompanyInfo->name;
                    if($result->status==0):
                        if($result->receiver_user_id==$userId){
                            $docketStatus   =   "Received";
                        }else{
                            $docketStatus   =   "Sent";
                        }
                    endif;
                else :
                    $userName   =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $company    =   $result->companyInfo->name;
                    if($result->status==0):
                        $docketStatus   =   "Sent";
                    endif;
                endif;
                if($result->status==1)
                    $docketStatus ="Approved";

                $conversationArray[]   = new DocketEmailConversationResource($result,$docketStatus,$userName,$company);
            }
        }
        return $conversationArray;
    }

    public function getEmailConversationChatByUserId($userId){
        $authUserId = auth()->user()->id;
        $sentDocketQuery    =  $this->emailSentDocketRepository->getDataWhere([['user_id', $authUserId],['receiver_user_id', $userId]]);
        $conversationArray  =   array();
        if($sentDocketQuery->count()>0){
            $resultQuery = $sentDocketQuery->orderBy('created_at','desc')->get();
            foreach ($resultQuery as $result){
                if($result->status==0)
                    $docketStatus   =   "Sent";
                if($result->status==1)
                    $docketStatus ="Approved";

                $conversationArray[]   = new DocketEmailConversationResource($result,$docketStatus);
            }
        }
        return $conversationArray;
    }

    public function getTimelineChatByRecipients(){
        $userId = auth()->user()->id;
        $conversationArray      =   array();
        $totalSentDocketID      =   array();
        $recipientsId = array_map('intval',Input::get('recipientId'));
        $recipientsWithUserId 	=	array_unique(array_merge($recipientsId,array((int)$userId)));

        //check sent docket
        foreach($recipientsWithUserId as $recipientId) {
            $sentDocketQuery              =  $this->sentDocketsRepository->getDataWhere([['user_id',$recipientId]])->orderBy('id','desc')->get();
            foreach($sentDocketQuery as $sentDocket){
                if($sentDocket->recipientInfo->count()>0):
                    //get all recipients by sent dockets id
                    $tempSentDocketRecipient    =    array_unique(array_merge(array((int)$recipientId), $sentDocket->recipientInfo->pluck('user_id')->toArray()));
                    if (FunctionUtils::array_equal($tempSentDocketRecipient,$recipientsWithUserId)) {
                        $totalSentDocketID[]    =   $sentDocket->id;
                    }
                endif;
            }
        }
        $sentDocketsDates    =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketID)->where('created_at', '<=',Carbon::now())->get(array(DB::raw('Date(created_at) as date')))->toArray();

        foreach ($sentDocketsDates as $sentDocketsDate){
            $sentDocketArray    =   array();
            $dateWiseQuery  =  $this->sentDocketsRepository->getDataWhereIn('id',$totalSentDocketID)->whereDate('created_at',$sentDocketsDate)->with('recipientInfo.userInfo')->orderBy('created_at','desc')->get();
            foreach ($dateWiseQuery as $dateWise){
                //check approved or not /.if status == 1 approved
                if ($dateWise->status == 3){
                    $status = "Rejected";
                }else{
                    if($dateWise->is_cancel== 1){
                        $status     =   "Cancelled";
                    }else{
                        if($dateWise->user_id==$userId){
                            $status     =   "Sent";
                        }else{
                            $status     =   "Received";
                        }
                    }
                }

                //approval text
                $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$dateWise->id]]);
                $totalRecipientApprovals    =   $sentDocketRecipientApprovalData->count();
                $totalRecipientApproved     =   $sentDocketRecipientApprovalData->where('status',1)->count();

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$dateWise->id],['user_id',$userId]])->count()==1){
                    $isApproval             =   1;
                    //check is approved
                    if($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$dateWise->id],['user_id',$userId]])->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }

                foreach ($dateWiseQuery as $dateWiseQuerys) {
                    $recipientsQuery = $dateWiseQuerys->recipientInfo;
                    $recipientData = "";
                    foreach ($recipientsQuery as $recipient) {
                        if ($recipient->id == $recipientsQuery->first()->id)
                            $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                        else
                            $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                    }
                }

                //canreject
                $canRejectDocket = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id',$dateWise->id],['user_id',$userId]]);
                $canReject = 0;
                $isReject = 0;
                if($canRejectDocket->count() > 0 ){
                    if ($canRejectDocket->first()->status == 0){
                        if ($dateWise->status == 0) {
                            $canReject = 1;
                        }else{
                            $canReject = 0;
                        }
                    }else{
                        $canReject = 0;
                    }


                    if ($dateWise->status == 3){
                        $isReject = 1;
                    }else{
                        $isReject = 0;
                    }

                }

                if (($dateWise->is_cancel && $dateWise->user_id == $userId) || !$dateWise->is_cancel){
                    $sentDocketArray[]  = new SentDocketResource($dateWise,'sentDocket',$recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status);
                }
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentDocketsDate['date'])->format('l')), 'sentDockets'   =>   $sentDocketArray);
            unset($sentDocketArray);
        }
        return $conversationArray;
    }

    public function getEmailTimelineByRecipients(){
        $conversationsArray      =   array();
        $totalSentDocketID      =   array();
        $recipientsId = array_map('intval',Input::get('recipientId'));
        $totalSentDocketID  =   array();
        //check sent docket
        $sentEmailDocketQuery              =  $this->emailSentDocketRepository->getDataWhere([['user_id',auth()->user()->id]])->with('recipientInfo.emailUserInfo')->orderBy('created_at','desc')->get();
        foreach($sentEmailDocketQuery as $sentEmailDocket){
            if($sentEmailDocket->recipientInfo->count()>0):
                if (FunctionUtils::array_equal($sentEmailDocket->recipientInfo->pluck('email_user_id')->toArray(),$recipientsId)) {
                    $totalSentDocketID[]    =   $sentEmailDocket->id;
                }
            endif;
        }
        $emailSentDocketsDates    =    $this->emailSentDocketRepository->getDataWhereIn('id',$totalSentDocketID)->where('created_at', '<=',Carbon::now())->get(array(DB::raw('Date(created_at) as date')))->toArray();
        foreach ($emailSentDocketsDates as $sentDocketsDate){
            $dateWiseQuery  =    $this->emailSentDocketRepository->getDataWhereIn('id',$totalSentDocketID)->whereDate('created_at',$sentDocketsDate)->orderBy('created_at','desc')->get();
            $conversationArray      =   array();
            foreach ($dateWiseQuery as $dateWise){
                //check approved or not /.if status == 1 approved
                $status     =   "Sent";
                $emailSentDocketRecipientData = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$dateWise->id],['approval',1]]);
                $totalRecipientApprovals    =  $emailSentDocketRecipientData->count();
                $totalRecipientApproved     =  $emailSentDocketRecipientData->where('status',1)->count();

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }

                $recipientName  =    "";
                foreach($dateWise->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($dateWise->recipientInfo->count() > 1)
                        if ($dateWise->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }
                }
                $userName = $dateWise->senderUserInfo->first_name." ".$dateWise->senderUserInfo->last_name;
                $company = $dateWise->senderCompanyInfo->name;
                $companyDocketId = 'rt-'.$dateWise->company_id.'-edoc-'.$dateWise->company_docket_id;
                $isApproved = $totalRecipientApproved;
                $conversationArray[] = new InvoiceFilterDocketResource($dateWise,'emailConversation',$userName,$company,null,null,$recipientName,$dateWise->senderUserInfo->image,
                $approvalText,$dateWise->status,$isApproved,$companyDocketId);
            }
            $conversationsArray[]    =   array('date' => array('date' => Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),
                                    'day' => Carbon::parse($sentDocketsDate['date'])->format('l')), 'dockets'   =>   $conversationArray);
        }
        return $conversationsArray;
    }

    public function getEmailTimelineByUserId($userId){
        $authUserId = auth()->user()->id;
        $conversationArray      =   array();
        $sentDocketsDates    = $this->emailSentDocketRepository->getDataWhere([['user_id', $authUserId],['receiver_user_id', $userId],['created_at', '<=',Carbon::now()]])
            ->get(array(DB::raw('Date(created_at) as date')))
            ->toArray();
        foreach($sentDocketsDates as $sentDocketsDate){
            $sentDocketArray    =   array();
            $dateWiseQuery  =    $this->emailSentDocketRepository->getDataWhere([['user_id', $authUserId],['receiver_user_id', $userId]])->whereDate('created_at',$sentDocketsDate)->orderBy('created_at','desc')->get();
            foreach ($dateWiseQuery as $result){
                if($result->status==0)
                    $docketStatus   =   "Sent";
                if($result->status==1)
                    $docketStatus ="Approved";

                $sentDocketArray[]   =  new DocketEmailConversationResource($result,$docketStatus);
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentDocketsDate['date'])->format('l')), 'sentDockets'   =>   $sentDocketArray);
            unset($sentDocketArray);
        }
        return $conversationArray;
    }

    public function getEmailInvoiceTimelineByUserId($userId){
        $authUserId = auth()->user()->id;
        $conversationArray      =   array();
        $sentInvoiceDates  = $this->emailSentInvoiceRepository->getDataWhere([['user_id', $authUserId],['receiver_user_id', $userId],['created_at','<=',Carbon::now()]]) 
                                ->get(array(DB::raw('Date(created_at) as date')))
                                ->toArray();
        foreach ($sentInvoiceDates as $sentInvoiceDate){
            $sentInvoiceArray    =   array();
            $dateWiseQuery  =   $this->emailSentInvoiceRepository->getDataWhere([['user_id', $authUserId],['receiver_user_id', $userId]])->whereDate('created_at',$sentInvoiceDate)->with('invoiceInfo','senderUserInfo')->orderBy('created_at','desc')->get();

            foreach ($dateWiseQuery as $result){
                if($result->status==0)
                    $invoiceStatus   =   "Sent";
                if($result->status==1)
                    $invoiceStatus ="Approved";

                $userName = $result->senderUserInfo->first_name.' '.$result->senderUserInfo->last_name;
                $company   = $result->senderCompanyInfo->name;
                $receiver = $result->receiverInfo->email;
                $sentInvoiceArray[] = new  InvoiceTimelineResource($result,$userName,$company,$invoiceStatus,$receiver);
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentInvoiceDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentInvoiceDate['date'])->format('l')), 'sentInvoices'   =>   $sentInvoiceArray);
            unset($sentDocketArray);
            }
        return $conversationArray;
    }

    public function  postEmailUser($request){
        $validator = new MailgunValidator(StaticValue::MailgunPubKey());
        if($validator->validate($request->email)) {
            $emailUser = $this->emailUserRepository->getDataWhere([['email', $request->email]]);
            $companyId = auth()->user()->companyInfo->id;
            if ($emailUser->count() != 0) {
                if ($this->emailClientRepository->getDataWhere([['email_user_id', $emailUser->first()->id],['company_id', $companyId]])->count() != 0) {
                    return response()->json(["message" => 'This email is already added on your Custom Clients as ' . @$this->emailClientRepository->getDataWhere([['email_user_id', $emailUser->first()->id]])->first()->full_name],500);
                } else {
                    $profile = array('id' => $emailUser->first()->id, 'email' => $emailUser->first()->email);
                    return response()->json(['profile' => $profile],200);
                }
            } else {
                $usercustomRequest = new Request();
                $usercustomRequest['email'] = $request->email;
                $usercustomRequest['name'] = "";
                $usercustomRequest['company_name'] = "";
                $usercustom = $this->emailUserRepository->insertAndUpdate($usercustomRequest);
                $profile = array('id' => $usercustom->id, 'email' => $usercustom->email);
                return response()->json(["message" => 'Email client add successfully.', 'profile' => $profile],200);
            }
        }else{
            return response()->json(["message" => 'Invalid Email address.'],500);
        }
    }
}