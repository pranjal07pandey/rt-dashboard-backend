<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDcoketTimerAttachment;

class SentDocketTimerAttachmentRepository implements IRepository
{
    public function getModel()
    {
        return new SentDcoketTimerAttachment();
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
        if ($request->has('sent_docket_timer_attachment_id')) {
            $sent_docket_timer_attachment = $this->getModel()->find($request->sent_docket_timer_attachment__id);
        } else {
            $sent_docket_timer_attachment = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                    $sent_docket_timer_attachment->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('type'))?:                              $sent_docket_timer_attachment->type   = $request->type;
        (!$request->has('timer_id'))?:                          $sent_docket_timer_attachment->timer_id   = $request->timer_id;

        $sent_docket_timer_attachment->save();
        return $sent_docket_timer_attachment;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_timer_attachment = $this->getModel()->find($request->id);
        $sent_docket_timer_attachment->delete();
        return $sent_docket_timer_attachment;
    }
}
