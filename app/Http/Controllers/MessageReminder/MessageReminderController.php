<?php

namespace App\Http\Controllers\MessageReminder;

use App\Company;
use App\Messages;
use App\MessagesGroup;
use App\MessagesGroupUser;
use App\MessagesRecipients;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class MessageReminderController extends Controller
{
    public function index(Request $request){
        $company        =   Company::with('userInfo','employees.userInfo')->where('id', Session::get('company_id'))->first();
        $messageData = $this->getMessagesList($request,"All");
        return view('dashboard.company.message-reminders.index',compact('company','messageData'));
    }

    public function createGroup(Request $request){
        $this->validate($request,['employeeId'   => 'required', 'isGroup']);
        $company        =   Company::with('employees')->where('id', Session::get('company_id'))->first();
        $totalCompanyUserIDs  =   array_merge(array($company->user_id),$company->employees->pluck('user_id')->toArray());

        if($request->isGroup){
            if($request->title==""){
                return response()->json(array("status" => false, "message" =>"Title required."));
            }

            $userIDs= array();
            foreach ($request->employeeId as $rowData){
                $userIDs[] = $rowData;
                if(!in_array($rowData,$totalCompanyUserIDs)){
                    return response()->json(array("status" => false, "message" =>"Invalid attempt."));
                }
            }

            $userIDs[]=strval(Auth::user()->id);
            $messagesGroup = new MessagesGroup();
            $messagesGroup->user_id = Auth::user()->id;
            $messagesGroup->title = $request->title;
            $messagesGroup->slug = str_slug($request->title);
            $messagesGroup->created_date = Carbon::now();
            $messagesGroup->is_active = 1;
            $messagesGroup->company_id = Session::get('company_id');
            if ($messagesGroup->save()){
                foreach ($userIDs as $userID) {
                    $messagesGroupUser                      = new MessagesGroupUser();
                    $messagesGroupUser->user_id             = $userID;
                    $messagesGroupUser->messages_groups_id  = $messagesGroup->id;
                    $messagesGroupUser->created_date        = Carbon::now();
                    $messagesGroupUser->is_group_message    = 1;
                    $messagesGroupUser->is_active           = 1;
                    $messagesGroupUser->save();
                }

                $messageData = $this->getMessagesList($request,$messagesGroup);
                $messageGroupHtml   =   view('dashboard.company.message-reminders.partials.message-user-list',compact('messageData'))->render();
                return response()->json(array("status" => true, "message" =>"Messages Group created successfully.", "messageGroupHtml" => $messageGroupHtml, "messageGroupID"=>$messagesGroup->id));
            }
        }else{
            return $this->sendSingleGroupMessage($request,$totalCompanyUserIDs);
        }
    }

    public function sendSingleGroupMessage($request, $totalCompanyUserIDs){
        if($request->message==null || $request->message==""){
            return response()->json(array("status" => false, "message" =>"Message required."));
        }

        $user= array();
        array_push($user,intval($request->employeeId));
        array_push($user, Auth::user()->id);
        $totalUsers = array_unique($user);

        if(!in_array(intval($request->employeeId),$totalCompanyUserIDs)){
            return response()->json(array("status" => false, "message" =>"Invalid attempt."));
        }

        $messagesGroupID = MessagesGroupUser::select('messages_groups_id',DB::raw('count(*) as cnt'))
            ->whereIn('user_id',$totalUsers)->where('is_group_message',0)
            ->groupBy('messages_groups_id')->having('cnt',count($totalUsers))->get()->pluck('messages_groups_id')->toArray();
        if(Auth::user()->id==$request->employeeId){
            $messagesGroupID = MessagesGroupUser::select('messages_groups_id', DB::raw('count(*) as cnt'))
                ->whereIn('messages_groups_id', $messagesGroupID)->groupBy('messages_groups_id')->having('cnt', count($totalUsers))->get()->pluck('messages_groups_id')->toArray();
        }


        if(empty($messagesGroupID)){
            $messagesGroup = new MessagesGroup();
            $messagesGroup->user_id = Auth::user()->id;
            $messagesGroup->created_date = Carbon::now();
            $messagesGroup->is_active = 1;
            $messagesGroup->company_id = Session::get('company_id');
            $messagesGroup->save();

            foreach ($totalUsers as $totalUser) {
                $messagesGroupUser = new MessagesGroupUser();
                $messagesGroupUser->user_id = $totalUser;
                $messagesGroupUser->messages_groups_id = $messagesGroup->id;
                $messagesGroupUser->created_date = Carbon::now();
                $messagesGroupUser->is_active = 1;
                $messagesGroupUser->is_group_message = 0;
                $messagesGroupUser->save();
            }

            $messagesGroupID    =   $messagesGroup->id;
        }else{
            $messagesGroupID    =  $messagesGroupID[0];
        }

        $messages = new Messages();
        $messages->user_id = Auth::user()->id;
        $messages->messages_groups_id = $messagesGroupID;
        $messages->message = $request->message;
        $messages->created_date = Carbon::now();
        $messages->save();

        foreach ($totalUsers as $totalUser) {
            $messagesRecipients = new MessagesRecipients();
            $messagesRecipients->message_id = $messages->id;
            $messagesRecipients->user_id = $totalUser;
            $messagesRecipients->is_read = 0;
            $messagesRecipients->save();
        }

        if(Auth::user()->id!=$request->employeeId) {
            $userNotification = new UserNotification();
            $userNotification->sender_user_id = Auth::user()->id;
            $userNotification->receiver_user_id = $request->employeeId;
            $userNotification->type = 1;
            $userNotification->title = '';
            $userNotification->message = $request->message;
            $userNotification->key = $messages->id;
            $userNotification->status = 0;
            $userNotification->save();

            if ($messagesRecipients->userInfo->device_type == 2) {
                if ($messagesRecipients->userInfo->deviceToken != "") {
                    sendiOSNotification($messagesRecipients->userInfo->deviceToken, 'New Message From ' . Auth::user()->first_name . " " . Auth::user()->last_name, strip_tags($request->message), array('type' => 2));
                }
            } else if ($messagesRecipients->userInfo->device_type == 1) {
                if ($messagesRecipients->userInfo->deviceToken != "") {
                    sendAndroidNotification($messagesRecipients->userInfo->deviceToken, 'New Message From ' . Auth::user()->first_name . " " . Auth::user()->last_name, strip_tags($request->message), array('type' => 2));
                }
            }
        }

        $messagesId = Messages::where('messages_groups_id', $messagesGroupID)->pluck('id')->toArray();
        MessagesRecipients::whereIn('message_id', $messagesId)->where('user_id', Auth::user()->id)->where('is_read', 0)->update(['is_read' => 1]);

        $messagesGroup  =   MessagesGroup::findOrFail($messagesGroupID);
        $messageData = $this->getMessagesList($request,$messagesGroup);
        $messageGroupHtml   =   view('dashboard.company.message-reminders.partials.message-user-list',compact('messageData'))->render();
        return response()->json(array("status" => true, "message" =>"Message sent.", "messageGroupHtml" => $messageGroupHtml, "messageGroupID"=>$messagesGroup->id));
    }

    public function store(Request $request){
        $this->validate($request,['message'   => 'required','id'=>'required']);
        $groupId = $request->id;
        $messageGroup = MessagesGroup::findOrFail($groupId);

        if($messageGroup->company_id!=Session::get('company_id')){
            return response()->json(['status'=>false,'message'=>'Invalid action !']);
        }

        $messagesGroupUsers  =   $messageGroup->messagesGroupUserinfo;

        if(!in_array(Auth::user()->id,$messagesGroupUsers->pluck('user_id')->toArray())){
            return response()->json(['status'=>false,'message'=>'Invalid action !']);
        }
        $messageGroupType   =   ($messagesGroupUsers->first()->is_group_message)?2:1;
        $messages                       = new Messages();
        $messages->user_id              = Auth::user()->id;
        $messages->messages_groups_id   = $messageGroup->id;
        $messages->message              = $request->message;
        $messages->created_date         = Carbon::now();

        if($messages->save()) {
            foreach ($messagesGroupUsers as $users) {
                $messagesRecipients = new MessagesRecipients();
                $messagesRecipients->message_id = $messages->id;
                $messagesRecipients->user_id = $users->user_id;
                $messagesRecipients->is_read = 0;

                if ($messagesRecipients->save()) {
                    $userNotification = new UserNotification();
                    $userNotification->sender_user_id = Auth::user()->id;
                    $userNotification->receiver_user_id = $users->user_id;
                    $userNotification->type = $messageGroupType;
                    $userNotification->title = '';
                    $userNotification->message = $request->message;
                    $userNotification->key = $messages->id;
                    $userNotification->status = 0;
                    $userNotification->save();

                    if ($messagesRecipients->userInfo->device_type == 2) {
                        if ($messagesRecipients->userInfo->deviceToken != "") {

                            sendiOSNotification($messagesRecipients->userInfo->deviceToken, 'New Message From ' . Auth::user()->first_name . ' ' . Auth::user()->last_name, strip_tags($request->message), array('type' => $messageGroupType));
                        }
                    } else if ($messagesRecipients->userInfo->device_type == 1) {
                        if ($messagesRecipients->userInfo->deviceToken != "") {
                            sendAndroidNotification($messagesRecipients->userInfo->deviceToken, 'New Message From ' . Auth::user()->first_name . ' '. Auth::user()->last_name, strip_tags($request->message), array('type' => $messageGroupType));
                        }
                    }
                }
            }

            $messagesId = $messageGroup->messagesinfo->pluck('id')->toArray();
            MessagesRecipients::whereIn('message_id', $messagesId)->where('user_id', Auth::user()->id)->where('is_read', 0)->update(['is_read' => 1]);

            $member_last_seen = $this->memberLastSeen($request, $messageGroup);
            $messageList[] = $this->messageList($messageGroup, $messages, $member_last_seen);
            $messageHtml = view('dashboard.company.message-reminders.partials.message-list', compact('messageList'))->render();
            $senderUserId   =    $messages->user_id ;
            return response()->json(array("status" => true, "html" => $messageHtml, 'groupId' => $messageGroup->id, 'senderUserId' => $senderUserId));
        }
    }

    public function chatView(Request $request){
        $groupId = $request->id;
        $messageGroup = MessagesGroup::with('messagesinfo.userInfo','messagesinfo.messagesRecInfo.userInfo','messagesGroupUserinfo.userInfo')->findOrFail($groupId);
        if($messageGroup->company_id!=Session::get('company_id')){
            return response()->json(['status'=>false,'message'=>'Invalid action !']);
        }

        $messages = $messageGroup->messagesinfo;
        $member_last_seen = $this->memberLastSeen($request,$messageGroup);

        $messageList = array();
        foreach ($messages as $message){
            $messageList[] = $this->messageList($messageGroup,$message, $member_last_seen);
        }
        $htmlView   =   view('dashboard.company.message-reminders.partials.chatView',compact('messageList','messageGroup'))->render();
        return response()->json(['status'=>true,'html'=>$htmlView]);
    }

    public function messageList(MessagesGroup $messageGroup, Messages $message,$member_last_seen){
        $seen           = array();
        $seenUser       = array();
        $seenMemberList = array();

        foreach ($message->messagesRecInfo as $messagesRecInfo) {
            if ($messagesRecInfo->is_read == 1){
                $seenUser[] = $messagesRecInfo->userInfo->first_name;
                $seenMemberList[] = $messagesRecInfo->userInfo;
            }
        }

        foreach ($member_last_seen as $lastSeen){
            if ($message->id == $lastSeen['message_id']){
                $seen[]= array(
                    'user_id'=>$lastSeen['user']->id,
                    'user_name'=>$lastSeen['user']->first_name,
                    'user_last_name'=>$lastSeen['user']->last_name,
                    'user_profile'=>$lastSeen['user']->image,
                );
            }
        }

        if (count($seenMemberList)== $messageGroup->messagesGroupUserinfo->count()){ $seen_user = "Everyone";}
        else{ $seen_user ="Seen By ".implode(', ',$seenUser); }

        return array('id'=> $message->id,
            'user_id'=>$message->user_id,
            'userName'=>$message->userInfo->first_name." ".$message->userInfo->last_name,
            'profile'=>$message->userInfo->image,
            'date'=> Carbon::parse($message->created_date)->format('d-M-Y'),
            'message'=>$message->message,
            'seen'=>$seen,
            'seenBy'=>$seen_user);
    }

    public function memberLastSeen($request,$messageGroup){
        $messages_ids= $messageGroup->messagesinfo->pluck('id')->toArray();

        $group_member = $messageGroup->messagesGroupUserinfo;
        $memberLastSeen = array();
        foreach ($group_member as $member){
            $last_seen_msg = MessagesRecipients::with('userInfo')->whereIn('message_id',$messages_ids)->where('user_id',$member->user_id)->where('is_read',1)->orderBy('created_at','asc')->get();
            if (count($last_seen_msg)==0){
                $memberLastSeen[] = array('user'=>$member->userInfo,'message_id'=>0);
            }else{
                $memberLastSeen[]= array('user'=>$member->userInfo,'message_id'=>$last_seen_msg->last()->message_id);
            }
        }
        return $memberLastSeen;
    }

    public function getMessagesList($request, $group){
        $user = User::with('messagesGroupUser.messagesGroupinfo.messagesinfo', 'messagesGroupUser.messagesGroupinfo.messagesGroupUserinfo.userInfo')->findOrFail(Auth::user()->id);

        if($group=="All") {
            $messageGroupUser = $user->messagesGroupUser;
            $messageGroups = $messageGroupUser->pluck('messagesGroupinfo');
        }else{
            $messageGroups[]  =   $group;
        }
        $messagesGroupsArray = array();

        foreach ($messageGroups as $messagesGroup){
            $groupProfile = array();
            $groupTitle = "";
            $memberNumber = array();

            if ($messagesGroup->messagesInfo->count()!=0) {
                $isRead="";
                $message    =   $messagesGroup->messagesInfo->last();
                if ($message->user_id == $user->id){ $isRead = 1;}
                else{ $isRead = $message->messagesRecipientsByUser($user)->is_read; }

                $lastMessages = array(
                    "id" => $message->id,
                    "message" => $message->message,
                    "is_read" => $isRead,
                    "created_date" => Carbon::parse($message->created_date)->format('d-M-Y'),
                );
                $dateSorting = $message->created_date;
            }
            else{
                $lastMessages = null;
                $dateSorting = $messagesGroup->created_date;
            }

            $messagesGroupUsers  =   $messagesGroup->messagesGroupUserinfo;
            if($messagesGroupUsers->first()->is_group_message){
                $groupTitle = $messagesGroup->title;
                $randomUser     =   ($messagesGroupUsers->count()>2)?$messagesGroupUsers->random(2):$messagesGroupUsers->random(1);
                foreach ($randomUser as $messagesGroupUser){
                    $groupProfile[]= array(
                        'name'=> $messagesGroupUser->userInfo->first_name,
                        'image'=>$messagesGroupUser->userInfo->image
                    );
                }
            }
            else{
                if($messagesGroupUsers->first()->user_id!=$user->id){ $messagesGroupUser  =   $messagesGroupUsers->first(); }
                if($messagesGroupUsers->last()->user_id!=$user->id){ $messagesGroupUser  =   $messagesGroupUsers->last(); }
                if($messagesGroupUsers->last()->user_id==$messagesGroupUsers->first()->user_id){ $messagesGroupUser  =   $messagesGroupUsers->first(); }

                $groupTitle = $messagesGroupUser->userInfo->first_name . " " . $messagesGroupUser->userInfo->last_name;
                $groupProfile[] = array(
                    'name' => $messagesGroupUser->userInfo->first_name,
                    'image' => $messagesGroupUser->userInfo->image
                );
            }

            foreach ($messagesGroupUsers as $datas){
                $memberNumber[] = array(
                    'id'=> $datas->userInfo->id,
                    'name'=>$datas->userInfo->first_name.' '.$datas->userInfo->last_name,
                    'profile'=> $datas->userInfo->image
                );
            }

            $messagesGroupsArray[] = array(
                'id' =>$messagesGroup->id,
                'type'=>($messagesGroup->title==null)?1:2,
                'date' =>Carbon::parse($messagesGroup->created_date)->format('d-M-Y'),
                'profile'=>$groupProfile,
                'title'=>$groupTitle,
                'member'=>$memberNumber,
                'last_messages'=>$lastMessages,
                'sortingDate' => $dateSorting,
            );
        }

        $size = count($messagesGroupsArray);
        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size - 1 - $i; $j++) {
                if (strtotime($messagesGroupsArray[$j + 1]['sortingDate']) > strtotime($messagesGroupsArray[$j]['sortingDate'])) {
                    $tempArray = $messagesGroupsArray[$j + 1];
                    $messagesGroupsArray[$j + 1] = $messagesGroupsArray[$j];
                    $messagesGroupsArray[$j] = $tempArray;
                }
            }
        }
        return $messagesGroupsArray;
    }
}
