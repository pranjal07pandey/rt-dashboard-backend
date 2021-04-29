<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\Messages;

class MessagesRepository implements IRepository
{
    public function getModel()
    {
        return new Messages();
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
        if ($request->has('message_id')) {
            $message = $this->getModel()->find($request->message__id);
        } else {
            $message = $this->getModel();
        }

        (!$request->has('user_id'))?:                               $message->user_id   = $request->user_id;
        (!$request->has('messages_groups_id'))?:                    $message->messages_groups_id  = $request->messages_groups_id;
        (!$request->has('message'))?:                               $message->message  = $request->message;

        $message->save();
        return $message;
    }

    public function deleteDataById($request = null)
    {
        $message = $this->getModel()->find($request->id);
        $message->delete();
        return $message;
    }
}
