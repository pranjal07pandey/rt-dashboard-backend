<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoice;

class SentInvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoice();
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
        if ($request->has('sent_invoice_id')) {
            $sent_invoice = $this->getModel()->find($request->sent_invoice__id);
        } else {
            $sent_invoice = $this->getModel();
        }

        (!$request->has('invoice_id'))?:                    $sent_invoice->invoice_id   = $request->invoice_id;
        (!$request->has('user_id'))?:                       $sent_invoice->user_id  = $request->user_id;
        (!$request->has('receiver_user_id'))?:              $sent_invoice->receiver_user_id  = $request->receiver_user_id;
        (!$request->has('company_id'))?:                    $sent_invoice->company_id  = $request->company_id;
        (!$request->has('receiver_company_id'))?:           $sent_invoice->receiver_company_id  = $request->receiver_company_id;
        (!$request->has('status'))?:                        $sent_invoice->status  = $request->status;
        (!$request->has('gst'))?:                           $sent_invoice->gst  = $request->gst;
        (!$request->has('isDocketAttached'))?:              $sent_invoice->isDocketAttached  = $request->isDocketAttached;
        (!$request->has('amount'))?:                        $sent_invoice->amount  = $request->amount;
        (!$request->has('abn'))?:                           $sent_invoice->abn  = $request->abn;
        (!$request->has('sender_name'))?:                   $sent_invoice->sender_name  = $request->sender_name;
        (!$request->has('company_address'))?:               $sent_invoice->company_address  = $request->company_address;
        (!$request->has('company_name'))?:                  $sent_invoice->company_name  = $request->company_name;
        (!$request->has('theme_document_id'))?:             $sent_invoice->theme_document_id  = $request->theme_document_id;
        (!$request->has('syn'))?:                           $sent_invoice->syn  = $request->syn;
        (!$request->has('xero_invoice_id'))?:               $sent_invoice->xero_invoice_id  = $request->xero_invoice_id;
        (!$request->has('company_logo'))?:                  $sent_invoice->company_logo  = $request->company_logo;
        (!$request->has('company_invoice_id'))?:            $sent_invoice->company_invoice_id  = $request->company_invoice_id;
        (!$request->has('folder_status'))?:                 $sent_invoice->folder_status  = $request->folder_status;
        (!$request->has('user_invoice_count'))?:            $sent_invoice->user_invoice_count  = $request->user_invoice_count;
        (!$request->has('formatted_id'))?:                  $sent_invoice->formatted_id  = $request->formatted_id;

        $sent_invoice->save();
        return $sent_invoice;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice = $this->getModel()->find($request->id);
        $sent_invoice->delete();
        return $sent_invoice;
    }
}
