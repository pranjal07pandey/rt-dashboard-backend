<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketRecipient;

class SentDocketRecipientRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketRecipient();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function getDataWhereIn($col,$value)
    {
        return $this->getModel()->whereIn($col,$value);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('sent_docket_recipient_id')) {
            $sent_docket_recipient = $this->getModel()->find($request->sent_docket_recipient__id);
        } else {
            $sent_docket_recipient = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                    $sent_docket_recipient->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('user_id'))?:                           $sent_docket_recipient->user_id  = $request->user_id;
        (!$request->has('approval'))?:                          $sent_docket_recipient->approval  = $request->approval;
        (!$request->has('approval'))?:                          $sent_docket_recipient->approval  = $request->approval;

        $sent_docket_recipient->save();
        return $sent_docket_recipient;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_recipient = $this->getModel()->find($request->id);
        $sent_docket_recipient->delete();
        return $sent_docket_recipient;
    }
}
