<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocManualTimer;

class EmailSentDocManualTimerRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocManualTimer();
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
        if ($request->has('email_sent_doc_manual_timer_id')) {
            $email_sent_doc_manual_timer = $this->getModel()->find($request->email_sent_doc_manual_timer__id);
        } else {
            $email_sent_doc_manual_timer = $this->getModel();
        }

        (!$request->has('sent_docket_value_id'))?:                  $email_sent_doc_manual_timer->sent_docket_value_id   = $request->sent_docket_value_id;
        (!$request->has('docket_manual_timer_id'))?:                $email_sent_doc_manual_timer->docket_manual_timer_id  = $request->docket_manual_timer_id;
        (!$request->has('value'))?:                                 $email_sent_doc_manual_timer->value  = $request->value;
        (!$request->has('label'))?:                                 $email_sent_doc_manual_timer->label  = $request->label;

        $email_sent_doc_manual_timer->save();
        return $email_sent_doc_manual_timer;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_doc_manual_timer = $this->getModel()->find($request->id);
        $email_sent_doc_manual_timer->delete();
        return $email_sent_doc_manual_timer;
    }
}
