<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketRecipientApproval;

class SentDocketRecipientApprovalRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketRecipientApproval();
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
        if ($request->has('sent_docket_recipient_approval_id')) {
            $sent_docket_recipient_approval = $this->getModel()->find($request->sent_docket_recipient_approval_id);
        } else {
            $sent_docket_recipient_approval = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                        $sent_docket_recipient_approval->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('user_id'))?:                               $sent_docket_recipient_approval->user_id  = $request->user_id;
        (!$request->has('status'))?:                                $sent_docket_recipient_approval->status  = $request->status;
        (!$request->has('name'))?:                                  $sent_docket_recipient_approval->name  = $request->name;
        (!$request->has('signature'))?:                             $sent_docket_recipient_approval->signature   = $request->signature;
        (!$request->has('approval_time'))?:                         $sent_docket_recipient_approval->approval_time  = $request->approval_time;

        $sent_docket_recipient_approval->save();
        return $sent_docket_recipient_approval;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_recipient_approval = $this->getModel()->find($request->id);
        $sent_docket_recipient_approval->delete();
        return $sent_docket_recipient_approval;
    }
}
