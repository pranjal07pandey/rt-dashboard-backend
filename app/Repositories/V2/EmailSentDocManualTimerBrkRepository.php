<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocManualTimerBrk;

class EmailSentDocManualTimerBrkRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocManualTimerBrk();
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
        if ($request->has('email_sent_doc_manual_timer_brk_id')) {
            $email_sent_doc_manual_timer_brk = $this->getModel()->find($request->email_sent_doc_manual_timer_brk__id);
        } else {
            $email_sent_doc_manual_timer_brk = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                  $email_sent_doc_manual_timer_brk->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('manual_timer_break_id'))?:                 $email_sent_doc_manual_timer_brk->manual_timer_break_id  = $request->manual_timer_break_id;
        (!$request->has('value'))?:                                 $email_sent_doc_manual_timer_brk->value  = $request->value;
        (!$request->has('label'))?:                                 $email_sent_doc_manual_timer_brk->label  = $request->label;
        (!$request->has('reason'))?:                                $email_sent_doc_manual_timer_brk->reason  = $request->reason;

        $email_sent_doc_manual_timer_brk->save();
        return $email_sent_doc_manual_timer_brk;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_doc_manual_timer_brk = $this->getModel()->find($request->id);
        $email_sent_doc_manual_timer_brk->delete();
        return $email_sent_doc_manual_timer_brk;
    }
}
