<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentDocket;

class EmailSentDocketRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentDocket();
    }

    public function getDataById($request = null)
    {
        return $this->getModel()->find($request->id);
    }

    public function getDataWhere($array = [])
    {
        return $this->getModel()->where($array);
    }

    public function getDataWhereIn($col,$val)
    {
        return $this->getModel()->whereIn($col,$val);
    }

    public function insertAndUpdate($request = null)
    {
        if ($request->has('email_sent_docket_id')) {
            $email_sent_docket = $this->getModel()->find($request->email_sent_docket__id);
        } else {
            $email_sent_docket = $this->getModel();
        }

        (!$request->has('user_id'))?:                                   $email_sent_docket->user_id   = $request->user_id;
        (!$request->has('docket_id'))?:                                 $email_sent_docket->docket_id  = $request->docket_id;
        (!$request->has('receiver_user_id'))?:                          $email_sent_docket->receiver_user_id  = $request->receiver_user_id;
        (!$request->has('company_id'))?:                                $email_sent_docket->company_id  = $request->company_id;
        (!$request->has('receiver_full_name'))?:                        $email_sent_docket->receiver_full_name  = $request->receiver_full_name;
        (!$request->has('receiver_company_name'))?:                     $email_sent_docket->receiver_company_name  = $request->receiver_company_name;
        (!$request->has('receiver_company_address'))?:                  $email_sent_docket->receiver_company_address  = $request->receiver_company_address;
        (!$request->has('hashKey'))?:                                   $email_sent_docket->hashKey  = $request->hashKey;
        (!$request->has('status'))?:                                    $email_sent_docket->status  = $request->status;
        (!$request->has('invoiceable'))?:                               $email_sent_docket->invoiceable  = $request->invoiceable;
        (!$request->has('abn'))?:                                       $email_sent_docket->abn  = $request->abn;
        (!$request->has('company_name'))?:                              $email_sent_docket->company_name  = $request->company_name;
        (!$request->has('company_address'))?:                           $email_sent_docket->company_address  = $request->company_address;
        (!$request->has('sender_name'))?:                               $email_sent_docket->sender_name  = $request->sender_name;
        (!$request->has('docketApprovalType'))?:                        $email_sent_docket->docketApprovalType  = $request->docketApprovalType;
        (!$request->has('theme_document_id'))?:                         $email_sent_docket->theme_document_id  = $request->theme_document_id;
        (!$request->has('folder_status'))?:                             $email_sent_docket->folder_status  = $request->folder_status;
        (!$request->has('company_docket_id'))?:                         $email_sent_docket->company_docket_id  = $request->company_docket_id;
        (!$request->has('company_logo'))?:                              $email_sent_docket->company_logo  = $request->company_logo;
        (!$request->has('user_docket_count'))?:                         $email_sent_docket->user_docket_count  = $request->user_docket_count;
        (!$request->has('formatted_id'))?:                              $email_sent_docket->formatted_id  = $request->formatted_id;

        $email_sent_docket->save();
        return $email_sent_docket;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_docket = $this->getModel()->find($request->id);
        $email_sent_docket->delete();
        return $email_sent_docket;
    }
}
