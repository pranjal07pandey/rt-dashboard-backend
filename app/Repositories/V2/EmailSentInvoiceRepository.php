<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentInvoice;

class EmailSentInvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentInvoice();
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
        if ($request->has('email_sent_invoice_id')) {
            $email_sent_invoice = $this->getModel()->find($request->email_sent_invoice__id);
        } else {
            $email_sent_invoice = $this->getModel();
        }

        (!$request->has('invoice_id'))?:                        $email_sent_invoice->invoice_id   = $request->invoice_id;
        (!$request->has('user_id'))?:                           $email_sent_invoice->user_id  = $request->user_id;
        (!$request->has('company_id'))?:                        $email_sent_invoice->company_id  = $request->company_id;
        (!$request->has('receiver_user_id'))?:                  $email_sent_invoice->receiver_user_id  = $request->receiver_user_id;
        (!$request->has('receiver_full_name'))?:                $email_sent_invoice->receiver_full_name  = $request->receiver_full_name;
        (!$request->has('receiver_company_name'))?:             $email_sent_invoice->receiver_company_name  = $request->receiver_company_name;
        (!$request->has('receiver_company_address'))?:          $email_sent_invoice->receiver_company_address  = $request->receiver_company_address;
        (!$request->has('amount'))?:                            $email_sent_invoice->amount  = $request->amount;
        (!$request->has('gst'))?:                               $email_sent_invoice->gst  = $request->gst;
        (!$request->has('isDocketAttached'))?:                  $email_sent_invoice->isDocketAttached  = $request->isDocketAttached;
        (!$request->has('hashKey'))?:                           $email_sent_invoice->hashKey  = $request->hashKey;
        (!$request->has('status'))?:                            $email_sent_invoice->status  = $request->status;
        (!$request->has('abn'))?:                               $email_sent_invoice->abn  = $request->abn;
        (!$request->has('sender_name'))?:                       $email_sent_invoice->sender_name  = $request->sender_name;
        (!$request->has('company_address'))?:                   $email_sent_invoice->company_address  = $request->company_address;
        (!$request->has('company_name'))?:                      $email_sent_invoice->company_name  = $request->company_name;
        (!$request->has('theme_document_id'))?:                 $email_sent_invoice->theme_document_id  = $request->theme_document_id;
        (!$request->has('syn'))?:                               $email_sent_invoice->syn  = $request->syn;
        (!$request->has('xero_invoice_id'))?:                   $email_sent_invoice->xero_invoice_id  = $request->xero_invoice_id;
        (!$request->has('folder_status'))?:                     $email_sent_invoice->folder_status  = $request->folder_status;
        (!$request->has('company_logo'))?:                      $email_sent_invoice->company_logo  = $request->company_logo;
        (!$request->has('company_invoice_id'))?:                $email_sent_invoice->company_invoice_id  = $request->company_invoice_id;
        (!$request->has('template_title'))?:                    $email_sent_invoice->template_title  = $request->template_title;
        (!$request->has('user_invoice_count'))?:                $email_sent_invoice->user_invoice_count  = $request->user_invoice_count;
        (!$request->has('formatted_id'))?:                      $email_sent_invoice->formatted_id  = $request->formatted_id;

        $email_sent_invoice->save();
        return $email_sent_invoice;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_invoice = $this->getModel()->find($request->id);
        $email_sent_invoice->delete();
        return $email_sent_invoice;
    }
}
