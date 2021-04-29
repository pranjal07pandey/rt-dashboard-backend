<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\MessagesGroupUser;

class MessagesGroupUserRepository implements IRepository
{
    public function getModel()
    {
        return new MessagesGroupUser();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('message_group_user_id')) {
            $message_group_user = $this->getModel()->find($request->message_group_user__id);
        } else {
            $message_group_user = $this->getModel();
        }

        (!$request->has('message_group_id'))?:              $message_group_user->message_group_id   = $request->message_group_id;
        (!$request->has('sender_user_id'))?:                $message_group_user->sender_user_id  = $request->sender_user_id;
        (!$request->has('title'))?:                         $message_group_user->title  = $request->title;
        (!$request->has('message'))?:                       $message_group_user->message  = $request->message;

        $message_group_user->save();
        return $message_group_user;
    }

    public function deleteDataById($request = null)
    {
        $message_group_user = $this->getModel()->find($request->id);
        $message_group_user->delete();
        return $message_group_user;
    }
}
