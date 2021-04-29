<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\UserNotification;

class UserNotificationRepository implements IRepository
{
    public function getModel()
    {
        return new UserNotification();
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
        if ($request->has('user_notification_id')) {
            $user_notification = $this->getModel()->find($request->user_notification_id);
        } else {
            $user_notification = $this->getModel();
        }

        (!$request->has('sender_user_id'))?:                        $user_notification->sender_user_id   = $request->sender_user_id;
        (!$request->has('receiver_user_id'))?:                           $user_notification->receiver_user_id  = $request->receiver_user_id;
        (!$request->has('type'))?:                          $user_notification->type  = $request->type;
        (!$request->has('title'))?:                          $user_notification->title  = $request->title;
        (!$request->has('message'))?:                            $user_notification->message   = $request->message;
        (!$request->has('key'))?:                           $user_notification->key  = $request->key;
        (!$request->has('status'))?:                             $user_notification->status  = $request->status;

        $user_notification->save();
        return $user_notification;
    }

    public function deleteDataById($request = null)
    {
        $user_notification = $this->getModel()->find($request->id);
        $user_notification->delete();
        return $user_notification;
    }
}
