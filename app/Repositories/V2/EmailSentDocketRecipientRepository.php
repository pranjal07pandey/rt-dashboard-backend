<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocketRecipient;

class EmailSentDocketRecipientRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocketRecipient();
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
        if ($request->has('email_sent_docket_recipient_id')) {
            $email_sent_docket_recipient = $this->getModel()->find($request->email_sent_docket_recipient__id);
        } else {
            $email_sent_docket_recipient = $this->getModel();
        }

        (!$request->has('email_sent_docket_id'))?:                      $email_sent_docket_recipient->email_sent_docket_id   = $request->email_sent_docket_id;
        (!$request->has('email_user_id'))?:                             $email_sent_docket_recipient->email_user_id  = $request->email_user_id;
        (!$request->has('approval'))?:                                  $email_sent_docket_recipient->approval  = $request->approval;
        (!$request->has('hashKey'))?:                                   $email_sent_docket_recipient->hashKey  = $request->hashKey;
        (!$request->has('receiver_full_name'))?:                        $email_sent_docket_recipient->receiver_full_name  = $request->receiver_full_name;
        (!$request->has('receiver_company_name'))?:                     $email_sent_docket_recipient->receiver_company_name  = $request->receiver_company_name;
        (!$request->has('receiver_company_address'))?:                  $email_sent_docket_recipient->receiver_company_address  = $request->receiver_company_address;
        (!$request->has('status'))?:                                    $email_sent_docket_recipient->status  = $request->status;
        (!$request->has('name'))?:                                      $email_sent_docket_recipient->name  = $request->name;
        (!$request->has('signature'))?:                                 $email_sent_docket_recipient->signature  = $request->signature;
        (!$request->has('approval_time'))?:                             $email_sent_docket_recipient->approval_time  = $request->approval_time;

        $email_sent_docket_recipient->save();
        return $email_sent_docket_recipient;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket_recipient = $this->getModel()->find($request->id);
        $email_sent_docket_recipient->delete();
        return $email_sent_docket_recipient;
    }
}
