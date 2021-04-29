<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\MessagesRecipients;

class MessagesRecipientsRepository implements IRepository
{
    public function getModel()
    {
        return new MessagesRecipients();
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
        if ($request->has('messages_recipients_id')) {
            $messages_recipients = $this->getModel()->find($request->messages_recipients__id);
        } else {
            $messages_recipients = $this->getModel();
        }

        (!$request->has('message_id'))?:                        $messages_recipients->message_id   = $request->message_id;
        (!$request->has('user_id'))?:                           $messages_recipients->user_id  = $request->user_id;
        (!$request->has('is_read'))?:                           $messages_recipients->is_read  = $request->is_read;

        $messages_recipients->save();
        return $messages_recipients;
    }

    public function deleteDataById($request = null)
    {
        $messages_recipients = $this->getModel()->find($request->id);
        $messages_recipients->delete();
        return $messages_recipients;
    }
}
