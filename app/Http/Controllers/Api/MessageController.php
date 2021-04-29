<?php

namespace App\Http\Controllers\Api;
use App\Messages;
use App\MessagesGroup;
use App\MessagesGroupUser;
use App\MessagesRecipients;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helpers\V2\AmazoneBucket;

class MessageController extends Controller
{


    public function getMessagesList(Request $request)
    {
        $companyId = $request->header('companyId');
        $userId = $request->header('userId');
        $messageGroupUser = MessagesGroupUser::where('user_id', $userId)->get();

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
                    $readStatus = MessagesRecipients::where("user_id", $userId)->where('message_id', $rowData->messagesGroupinfo->messagesInfo->last()->id)->first();
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
                $users = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $rowData->messages_groups_id)->where('is_active', 1)->inRandomOrder()->take(2)->get();
                $groupTitle = $rowData->messagesGroupinfo->title;
                foreach ($users as $users) {
                    $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
                }
                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
                    $memberNumber[] = array(
                        'id' => $datas->userInfo->id,
                        'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                        'profile' => ($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $datas->userInfo->image
                    );
                }
            } else {
                if (MessagesGroupUser::where("user_id", $userId)->where('messages_groups_id', $rowData->messages_groups_id)->count() == 1) {
                    $user = MessagesGroupUser::where("user_id", $userId)->where('messages_groups_id', $rowData->messages_groups_id)->first();
                    $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
                } else {
                    $user = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $rowData->messages_groups_id)->first();
                    $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
                }
                $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;

                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
                    $memberNumber[] = array(
                        'id' => $datas->userInfo->id,
                        'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                        'profile' => AmazoneBucket::url() . $datas->userInfo->image
                    );
                }
            }
            if ($rowData->messagesGroupinfo->title == null) {
                $messagesGroups[] = array(
                    'id' => $rowData->messagesGroupinfo->id,
                    'type' => 1,
                    'date' => Carbon::parse($rowData->messagesGroupinfo->created_date)->format('d-M-Y'),
                    'profile' => $groupProfile,
                    'title' => $groupTitle,
                    'member' => $memberNumber,
                    'last_messages' => $lastMessages,
                    'sortingDate' => $dateSorting,


                );
            } else {
                $messagesGroups[] = array(
                    'id' => $rowData->messagesGroupinfo->id,
                    'type' => 2,
                    'date' => Carbon::parse($rowData->messagesGroupinfo->created_date)->format('d-M-Y'),
                    'profile' => $groupProfile,
                    'title' => $groupTitle,
                    'member' => $memberNumber,
                    'last_messages' => $lastMessages,
                    'sortingDate' => $dateSorting,

                );
            }

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


        return response()->json(array("status" => true, "messageGroup" => $messagesGroups));
    }

    public function messages(Request $request, $key)
    {
        $messages = Messages::where('messages_groups_id', $key)->get();
        $checkMarkAsRead = $this->markAsReadStatusCheck($request, $messages);
        $member_last_seen = $this->memberLastSeen($messages, $key);
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
                    $user = User::find($lastSeen[0]);
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
            $messagelist[] = array(
                'id' => $items->id,
                'message' => $items->message,
                'user_id' => $items->user_id,
                'userName' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                'profile' => AmazoneBucket::url() . $items->userInfo->image,
                'date' => Carbon::parse($items->created_date)->format('d-M-Y'),
                'seen' => $seen,
                'seen_by' => $seen_user,
            );
        }
        return response()->json(array("status" => true, "messages" => $messagelist, "markAsRead" => $checkMarkAsRead));

    }

    public function message(Request $request, $key)
    {
        $message = Messages::where('id', $key)->first();
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
                    $user = User::find($lastSeen[0]);
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
            $messagelist[] = array(
                'id' => $items->id,
                'message' => $items->message,
                'user_id' => $items->user_id,
                'userName' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                'profile' => AmazoneBucket::url() . $items->userInfo->image,
                'date' => Carbon::parse($items->created_date)->format('d-M-Y'),
                'seen' => $seen,
                'seen_by' => $seen_user,
            );
        }


        $userId = $request->header('userId');
        $groupTitle = "";
        $groupProfile = array();
        $memberNumber = array();
        if ($message->messagesGroups->title == null) {
            $users = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $message->messagesGroups->id)->where('is_active', 1)->inRandomOrder()->take(2)->get();
            $groupTitle = $message->messagesGroups->title;
            foreach ($users as $users) {
                $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
            }
            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $memberNumber[] = array(
                    'id' => $datas->userInfo->id,
                    'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                    'profile' => ($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png") ? "" : AmazoneBucket::url() . $datas->userInfo->image
                );
            }
        } else {
            $user = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $message->messagesGroups->id)->first();
            $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
            $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;

            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $memberNumber[] = array(
                    'id' => $datas->userInfo->id,
                    'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                    'profile' => AmazoneBucket::url() . $datas->userInfo->image
                );
            }
        }

        if ($message->messagesGroups->title == null) {
            $messagesGroups[] = array(
                'id' => $message->messagesGroups->id,
                'type' => 1,
                'date' => Carbon::parse($message->messagesGroups->created_date)->format('d-M-Y'),
                'profile' => $groupProfile,
                'title' => $groupTitle,
                'member' => $memberNumber,


            );
        } else {
            $messagesGroups[] = array(
                'id' => $message->messagesGroups->id,
                'type' => 2,
                'date' => Carbon::parse($message->messagesGroups->created_date)->format('d-M-Y'),
                'profile' => $groupProfile,
                'title' => $groupTitle,
                'member' => $memberNumber,

            );
        }
        return response()->json(array("status" => true, "messages" => $messagelist, 'messagesGroups' => $messagesGroups, "markAsRead" => $checkMarkAsRead));


    }


    public function markAsReads(Request $request)
    {
        $messages = Messages::where('messages_groups_id', $request->messages_groups_id)->pluck('id')->toArray();
        $checkMessageRecipent = MessagesRecipients::where('message_id', '<=', $request->last_message_id)->pluck('id')->toArray();
        MessagesRecipients::whereIn('id', $checkMessageRecipent)->whereIn('message_id', $messages)->where('user_id', $request->header('userId'))->where('is_read', 0)->update(['is_read' => 1]);
        return response()->json(array("status" => true, "message" => "Success"));

    }


    public function memberLastSeen($messages, $key)
    {
        $messages_ids = $messages->pluck('id')->toArray();
        $group_member = MessagesGroup::where('id', $key)->first()->messagesGroupUserinfo;
        $memberLastSeen = array();
        foreach ($group_member as $member) {
            $last_seen_msg = MessagesRecipients::whereIn('message_id', $messages_ids)->where('user_id', $member->user_id)->where('is_read', 1)->orderBy('created_at', 'desc')->get();
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


    public function markAsReadStatusCheck($request, $messages)
    {
        $markasRead = false;

        $lastMessage = $messages->last();
        if ($lastMessage) {
            if ($lastMessage->user_id != $request->header('userId')) {
                if (MessagesRecipients::where('user_id', $request->header('userId'))->where('message_id', $lastMessage->id)->where('is_read', 0)->count() != 0) {
                    $markasRead = true;
                }
            }
        }
        return $markasRead;

    }


}