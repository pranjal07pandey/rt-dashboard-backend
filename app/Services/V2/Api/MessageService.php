<?php
namespace App\Services\V2\Api;

use App\Http\Resources\V2\Docket\MessaageDocketResource;
use App\Http\Resources\V2\Docket\SearchDocketResource;
use App\Http\Resources\V2\Message\MessaageListResource;
use App\Http\Resources\V2\Message\MessaageListTempResource;
use App\Http\Resources\V2\Message\MessaageResource;
use App\Http\Resources\V2\Message\MessageGroupUserInfoResource;
use Carbon\Carbon;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\AmazoneBucket;

class MessageService extends ConstructorService {

    public function getMessagesList($request){
        $userId = auth()->user()->id;
        $messageGroupUser = $this->messagesGroupUserRepository->getDataWhere([['user_id', $userId]])->with('messagesGroupinfo.messagesinfo')->get();

        $messagesGroups = array();
        foreach ($messageGroupUser as $rowData) {
            $groupTitle = "";
            $groupProfile = array();
            $memberNumber = array();
            if ($rowData->messagesGroupinfo->messagesInfo->count() != 0) {
                $isRead = "";
                if ($rowData->messagesGroupinfo->messagesInfo->last()->user_id == $userId) {
                    $isRead = 1;
                } else {
                    $readStatus = $this->messagesRecipientsRepository->getDataWhere([["user_id", $userId],['message_id', $rowData->messagesGroupinfo->messagesInfo->last()->id]])->first();
                    $isRead = $readStatus->is_read;
                }
                $lastMessages = array(
                    "id" => $rowData->messagesGroupinfo->messagesInfo->last()->id,
                    "message" => $rowData->messagesGroupinfo->messagesInfo->last()->message,
                    "is_read" => $isRead,
                    "created_date" => Carbon::parse($rowData->messagesGroupinfo->messagesInfo->last()->created_date)->format('d-M-Y'),
                );
                $dateSorting = $rowData->messagesGroupinfo->messagesInfo->last()->created_date;

            } else {
                $lastMessages = null;
                $dateSorting = $rowData->messagesGroupinfo->created_date;
            }
            if ($rowData->is_group_message == 1) {
                $users = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id', $rowData->messages_groups_id],['is_active', 1]])->inRandomOrder()->take(2)->get();
                $groupTitle = $rowData->messagesGroupinfo->title;
                foreach ($users as $users) {
                    $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
                }
                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
                    $profile = ($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $datas->userInfo->image;
                    $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
                }
            } else {
                if ($messageGroupUser->where('messages_groups_id', $rowData->messages_groups_id)->count() == 1) {
                    $user = $messageGroupUser->where('messages_groups_id', $rowData->messages_groups_id)->first();
                    $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
                } else {
                    $user = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id', $rowData->messages_groups_id]])->first();
                    $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
                }
                $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;

                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
                    $profile = AmazoneBucket::url() . $datas->userInfo->image;
                    $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
                }
            }
            $temp = new MessaageListTempResource($rowData->messagesGroupinfo,$groupProfile,$groupTitle,$memberNumber,$lastMessages,$dateSorting);
            $messagesGroups[] = $temp;
        }
        
        $size = count($messagesGroups);
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size - 1 - $i; $j++) {
                if (strtotime($messagesGroups[$j + 1]['sortingDate']) > strtotime($messagesGroups[$j]['sortingDate'])) {
                    $tempArray = $messagesGroups[$j + 1];
                    $messagesGroups[$j + 1] = $messagesGroups[$j];
                    $messagesGroups[$j] = $tempArray;
                }

            }
        }
        return $messagesGroups;
    }

    public function messages($request,$key){
        $messages = $this->messagesRepository->getDataWhere([['messages_groups_id', $key]])->with('messagesGroups.messagesGroupUserinfo')->get();
        $checkMarkAsRead = $this->markAsReadStatusCheck($request, $messages);
        $member_last_seen = $this->memberGroupLastSeen($messages, $key);
        $messagelist = array();
        foreach ($messages as $items) {
            $messagesTotalUser = array();
            foreach ($items->messagesGroups->messagesGroupUserinfo as $messagesGroupUserinfo) {
                $messagesTotalUser[] = $messagesGroupUserinfo->user_id;
            }
            $seenUser = array();
            foreach ($items->messagesRecInfo as $messagesRecInfo) {
                if ($messagesRecInfo->is_read == 1) {
                    $seenUser[] = $messagesRecInfo->userInfo->first_name;
                }
            }
            $seen = array();
            foreach ($member_last_seen as $lastSeen) {
                if ($items->id == $lastSeen[1]) {
                    $user = $this->userRepository->getDataWhere([['id',$lastSeen[0]]])->first();
                    $seen[] = array(
                        'user_id' => $user->id,
                        'user_name' => $user->first_name,
                        'user_profile' => ($user->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $user->image
                    );
                }
            }
            if (count($seenUser) == count($messagesTotalUser)) {
                $seen_user = "Seen By Everyone";
            } else {
                $seen_user = "Seen By " . implode(', ', $seenUser);
            }
            $messagelist[] = new MessaageListResource($items,$seen,$seen_user);
        }
        return response()->json(["messages" => $messagelist, "markAsRead" => $checkMarkAsRead],200);
    }

    public function message($request,$key){
        $message = $this->messagesRepository->getDataWhere([['id', $key]])->with('messagesGroups.messagesGroupUserinfo','messagesRecInfo')->first();
        $messages = $message->messagesGroups->messagesinfo;

        $checkMarkAsRead = $this->markAsReadStatusCheck($request, $messages);
        $member_last_seen = $this->memberLastSeens($messages, $key);
        $messagelist = array();
        foreach ($messages as $items) {
            $messagesTotalUser = array();
            foreach ($items->messagesGroups->messagesGroupUserinfo as $messagesGroupUserinfo) {
                $messagesTotalUser[] = $messagesGroupUserinfo->user_id;
            }
            $seenUser = array();
            foreach ($items->messagesRecInfo as $messagesRecInfo) {
                if ($messagesRecInfo->is_read == 1) {
                    $seenUser[] = $messagesRecInfo->userInfo->first_name;
                }
            }
            $seen = array();
            foreach ($member_last_seen as $lastSeen) {
                if ($items->id == $lastSeen[1]) {
                    $user = $this->userRepository->getDataWhere([['id',$lastSeen[0]]])->first();
                    $seen[] = array(
                        'user_id' => $user->id,
                        'user_name' => $user->first_name,
                        'user_profile' => ($user->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $user->image
                    );
                }
            }
            if (count($seenUser) == count($messagesTotalUser)) {
                $seen_user = "Seen By Everyone";
            } else {
                $seen_user = "Seen By " . implode(', ', $seenUser);
            }
            $messagelist[] = new MessaageListResource($items,$seen,$seen_user);
        }


        $userId = auth()->user()->id;
        $groupTitle = "";
        $groupProfile = array();
        $memberNumber = array();
        if ($message->messagesGroups->title == null) {
            $users = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id', $message->messagesGroups->id],['is_active', 1]])->inRandomOrder()->take(2)->get();
            $groupTitle = $message->messagesGroups->title;
            foreach ($users as $users) {
                $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
            }
            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $profile = ($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $datas->userInfo->image;
                $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
            }
        } else {
            $user = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id', $message->messagesGroups->id]])->first();
            $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
            $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;

            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $profile = AmazoneBucket::url() . $datas->userInfo->image;
                $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
            }
        }
        $temp = new MessaageListTempResource($message->messagesGroups,$groupProfile,$groupTitle,$memberNumber);
        $messagesGroups[] = $temp;
        return response()->json(["messages" => $messagelist, 'messagesGroups' => $messagesGroups, "markAsRead" => $checkMarkAsRead],200);
    }

    public function markAsReadStatusCheck($request, $messages){
        $markasRead = false;
        $lastMessage = $messages->last();
        if ($lastMessage) {
            if ($lastMessage->user_id != auth()->user()->id) {
                if ($this->messagesRecipientsRepository->getDataWhere([['user_id', auth()->user()->id],['message_id', $lastMessage->id],['is_read', 0]])->count() != 0) {
                    $markasRead = true;
                }
            }
        }
        return $markasRead;
    }

    public function memberGroupLastSeen($messages, $key){
        $messages_ids = $messages->pluck('id')->toArray();
        $group_member = $this->messagesGroupRepository->getDataWhere([['id', $key]])->first()->messagesGroupUserinfo;
        $memberLastSeen = array();
        foreach ($group_member as $member) {
            $last_seen_msg = $this->messagesRecipientsRepository->getDataWhere([['user_id', $member->user_id],['is_read', 1]])->whereIn('message_id', $messages_ids)->orderBy('created_at', 'desc')->get();
            if (count($last_seen_msg) == 0) {
                $memberLastSeen[] = array($member->user_id, 0
                );
            } else {
                $memberLastSeen[] = array(
                    $member->user_id, $last_seen_msg->first()->message_id
                );
            }
        }
        return $memberLastSeen;
    }

    public function memberLastSeens($messages,$key){
        $messages_ids= $messages->pluck('id')->toArray();
        $group_member = $this->messagesRepository->getDataWhere([['id',$key]])->first()->messagesGroups->messagesGroupUserinfo;
        $memberLastSeen = array();
        foreach ($group_member as $member){
            $last_seen_msg = $this->messagesRecipientsRepository->getDataWhere([['user_id', $member->user_id],['is_read', 1]])->whereIn('message_id', $messages_ids)->orderBy('created_at', 'desc')->get();
            if (count($last_seen_msg)==0){
                $memberLastSeen[] = array($member->user_id,0
                );
            }else{
                $memberLastSeen[]= array(
                    $member->user_id,$last_seen_msg->first()->message_id
                );
            }
        }
        return $memberLastSeen;
    }

    public function markAsReads($request){
        $messages = $this->messagesRepository->getDataWhere([['messages_groups_id', $request->messages_groups_id]])->pluck('id')->toArray();
        $checkMessageRecipent = $this->messagesRecipientsRepository->getDataWhere([['message_id', '<=', $request->last_message_id]])->pluck('id')->toArray();
        $this->messagesRecipientsRepository->getDataWhere([['user_id', auth()->user()->id],['is_read', 0]])->whereIn('id', $checkMessageRecipent)->whereIn('message_id', $messages)->update(['is_read' => 1]);
    }

    public function getNotificationList(){
        $notificationData = $this->commonNotification('notificationList');
        return response()->json(['notification' => $notificationData],200);
    }

    public function getNotificationListUpdateAndroid(){
        $notificationData = $this->commonNotification('notificationListUpdateAndroid');
        $unreadNotificationCount = $this->userNotificationRepository->getDataWhere([['receiver_user_id',auth()->user()->id],['status',0]])->count();
        return response()->json(['notification' => $notificationData,'unreadNotificationCount'=>$unreadNotificationCount],200);
    }

    public function markAsReadNotification($key){
        $authUser = auth()->user()->id;
        $userNotification   =   $this->userNotificationRepository->getDataWhere([['id',$key]])->first();
        if($userNotification->receiver_user_id == $authUser){
            $userNotification->status   =    1;
            $userNotification->save();
            $unreadMessage  =  $this->userNotificationRepository->getDataWhere([['receiver_user_id',$authUser],['status',0]])->count();
            return response()->json(['message' => "Success",'unreadMessage' => $unreadMessage],200);
        }else{
            return response()->json(['message' => "Invalid Attempt!"],500);
        }
    }

    public function markAllAsRead(){
        $this->userNotificationRepository->getDataWhere([['receiver_user_id',auth()->user()->id],['status',0]])->update(['status'=>1]);
    }


    


    function  checkMessageDetail($message){
        $userId = auth()->user()->id;
        $groupTitle = "";
        $groupProfile = array();
        $memberNumber = array();
        if ($message->messagesGroups->title == null) {
            $users = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id', $message->messagesGroups->id],['is_active', 1]])->inRandomOrder()->take(2)->get();
            $groupTitle = $message->messagesGroups->title;
            foreach ($users as $users) {
                $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
            }
        } else {
            $user = $this->messagesGroupUserRepository->getDataWhere([["user_id", '!=', $userId],['messages_groups_id',$message->messagesGroups->id]])->first();
            $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
            $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;
        }

        foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
            $profile = ($message->messagesGroups->title == null) ? (($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $datas->userInfo->image) : AmazoneBucket::url() . $datas->userInfo->image;
            $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
        }

        $temp = new MessaageListTempResource($message->messagesGroups,$groupProfile,$groupTitle,$memberNumber);
        $messagesGroups[] = $temp;

        return $messagesGroups;
    }

    function commonNotification($type){
        $authUser = auth()->user()->id;
        $userNotifications   = $this->userNotificationRepository->getDataWhere([['receiver_user_id',$authUser]])->orderBy('created_at','desc')->paginate(10);
        $notificationData   =    array();
        foreach ($userNotifications as $notification){
            $subtitle   =    '';
            $time   =    "";
            $messagesGroups     =    array();
            $docket     =    array();
            $invoice    =    array();
            $emailSentDockets   =   array();

            if($notification->type == 1){
                $message   =   $this->messagesRepository->getDataWhere([['id',$notification->key]])->with('messagesGroups.messagesGroupUserinfo')->first();
                if($type == 'notificationList'){
                    if($message->count() != 0){
                        $message = $message->first();
                        if(count($message->messagesGroups->messagesGroupUserinfo)!=null )   {
                            $subtitle   =   $message->messagesGroups->title;
                            $userId = $authUser;
                            $groupTitle = "";
                            $memberNumber = array();
                            $groupTitle = @$message->messagesGroups->title ;
    
                            foreach (@$message->messagesGroups->messagesGroupUserinfo as $datas) {
                                $profile = ($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png")?"": AmazoneBucket::url() . $datas->userInfo->image;
                                $memberNumber[] = new MessageGroupUserInfoResource($datas,$profile);
                            }
                            $messagesGroups[] = array(
                                'id' => $message->messagesGroups->id,
                                'title' => $groupTitle,
                                'member' => $memberNumber,
                            );
                        }
                    }
                }else{
                    $messagesGroups = $this->checkMessageDetail($message);
                }
            }
            
            if(Carbon::parse($notification->created_at)->diffInDays(Carbon::now())==0) {
                if (Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInHours(Carbon::now()) . " Hours Ago";
                }
                elseif(Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) . " Minutes Ago";
                }
                else {
                    if (Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) == 0)
                        $time = "Now";
                    else
                        $time = Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) . " Seconds Ago";
                }
            }
            else{ $time = Carbon::parse($notification->created_at)->format('d-M-Y '); }
            
            if($notification->type == 3) {
                $sentDocket = $this->sentDocketsRepository->getDataWhere([['id',$notification->key]])->with('recipientInfo.userInfo')->first();
                if($sentDocket):
                    $recipientsQuery = $sentDocket->recipientInfo;
                    $recipientData = "";
                    foreach ($recipientsQuery as $recipient) {
                        if ($recipient->id == $recipientsQuery->first()->id)
                            $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                        else
                            $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                    }

                    //check approved or not /.if status == 1 approved

                    if ($sentDocket->status == 3){
                        $status = "Rejected";
                    }else {
                        if ($sentDocket->is_cancel == 1) {
                            $status = "Cancelled";
                        } else {
                            if ($sentDocket->status == 1) {
                                $status = "Approved";
                            } elseif ($sentDocket->user_id == $authUser) {
                                $status = "Sent";
                            } else {
                                $status = "Received";
                            }
                        }

                    }
                    //approval text
                    $sentDocketRecipientApprovalData = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $sentDocket->id]]);
                    $totalRecipientApprovals = $sentDocketRecipientApprovalData->count();
                    $totalRecipientApproved = $sentDocketRecipientApprovalData->where('status', 1)->count();

                    //check is approval
                    $isApproval = 0;
                    $isApproved = 0;
                    if ($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $sentDocket->id],['user_id', $authUser]])->count() == 1) {
                        $isApproval = 1;
                        //check is approved
                        if ($this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $sentDocket->id],['user_id', $authUser]])->where('status', 1)->count() == 1) {
                            $isApproved = 1;
                        }
                    }
                    $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

                    $canRejectDocket = $this->sentDocketRecipientApprovalRepository->getDataWhere([['sent_docket_id', $sentDocket->id],['user_id',$authUser]]);
                    $canReject = 0;
                    $isReject = 0;

                    if($canRejectDocket->count() > 0 ){
                        if ($canRejectDocket->first()->status == 0){
                            if ($sentDocket->status == 0) {
                                $canReject = 1;
                            }else{
                                $canReject = 0;
                            }
                        }else{
                            $canReject = 0;
                        }

                        if ($sentDocket->status == 3){
                            $isReject = 1;
                        }else{
                            $isReject = 0;
                        }

                    }

                    $docket = new MessaageDocketResource($sentDocket,$recipientData,$approvalText,$isApproval,$isApproved,$canReject,$isReject,$status); 
                endif;
            }
            if($notification->type == 4) {
                $sentInvoice     =  $this->sentInvoiceRepository->getDataWhere([['id',$notification->key]])
                                        ->with('senderUserInfo','senderCompanyInfo','invoiceInfo','receiverUserInfo')->first();
                if($sentInvoice!=null) {
                    $userId = $sentInvoice->user_id;
                    $userName = $sentInvoice->senderUserInfo->first_name . " " . $sentInvoice->senderUserInfo->last_name;
                    $profile = AmazoneBucket::url() . $sentInvoice->senderUserInfo->image;
                    $company = $sentInvoice->senderCompanyInfo->name;

                    if ($sentInvoice->user_id == $authUser) {
                        if ($sentInvoice->status == 0):
                            $invoiceStatus = "Sent";
                        endif;
                    } else {
                        if ($sentInvoice->status == 0):
                            $invoiceStatus = "Received";
                        endif;
                    }

                    if ($sentInvoice->status == 1)
                        $invoiceStatus = "Approved";
                        
                    $receiver = $sentInvoice->receiverUserInfo->first_name . " " . $sentInvoice->receiverUserInfo->last_name;
                    $invoice = new SearchDocketResource($sentInvoice,'invoice',$userId,$userName,$profile,$company,$invoiceStatus,null,null,null,null,$receiver);
                }
            }
            if($notification->type == 5) {
                $emailSentDocket     = $this->emailSentDocketRepository->getDataWhere([['id',$notification->key]])->with('recipientInfo','docketInfo')->first();
                $userId = $emailSentDocket->user_id;
                $userName = @$notification->senderEmailUserDetails;
                $profile = "";
                $company = "";

                if ($emailSentDocket->status == 1)
                    $docketStatus = "Approved";
                else
                    $docketStatus = "Sent";

                $sender     =    "";
                $recipientName  =    "";
                foreach($emailSentDocket->recipientInfo as $recipient) {
                    $sender = $sender . "" . $recipient->emailUserInfo->email;

                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($emailSentDocket->recipientInfo->count() > 1)
                        if ($emailSentDocket->recipientInfo->last()->id != $recipient->id){
                            $sender = $sender . ", ";
                            $recipientName  = $recipientName.", ";
                        }
                }

                //approval text
                $emailSentDocketRecipientData = $this->emailSentDocketRecipientRepository->getDataWhere([['email_sent_docket_id',$emailSentDocket->id],['approval',1]]);
                $totalRecipientApprovals    =   $emailSentDocketRecipientData->count();
                $totalRecipientApproved     =   $emailSentDocketRecipientData->where('status',1)->count();
                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                $senderUser                 =   auth()->user();
                $company                    =   auth()->user()->companyInfo;
                $userName = $senderUser->first_name." ".$senderUser->last_name;
                $profile = AmazoneBucket::url() . $senderUser->image;
                $emailSentDockets = new SearchDocketResource($emailSentDocket,'docket',$senderUser->id,$userName,$profile,$company->name,$docketStatus,$recipientName,$approvalText,$totalRecipientApprovals,$totalRecipientApproved);
            }

            $name   =   "";
            if($notification->type==5){
                $name   =   $notification->senderEmailUserDetails->email;
            }else{
                $name   =    $notification->senderDetails->first_name." ".$notification->senderDetails->last_name;
            }
            $notificationData[] = new MessaageResource($notification,$name,$subtitle,$messagesGroups,$docket,$invoice,$emailSentDockets,$time);
        }
        return $notificationData;
    }

    

}