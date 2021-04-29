<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentEmailDocketInvoice;

class SentEmailDocketInvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new SentEmailDocketInvoice();
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
        if ($request->has('sent_email_docket_invoice_id')) {
            $sent_email_docket_invoice = $this->getModel()->find($request->sent_email_docket_invoice__id);
        } else {
            $sent_email_docket_invoice = $this->getModel();
        }

        (!$request->has('email_sent_docket_id'))?:                  $sent_email_docket_invoice->email_sent_docket_id   = $request->email_sent_docket_id;
        (!$request->has('email_sent_docket_value_id'))?:            $sent_email_docket_invoice->email_sent_docket_value_id  = $request->email_sent_docket_value_id;
        (!$request->has('type'))?:                                  $sent_email_docket_invoice->type  = $request->type;

        $sent_email_docket_invoice->save();
        return $sent_email_docket_invoice;
    }

    public function deleteDataById($request = null)
    {
        $sent_email_docket_invoice = $this->getModel()->find($request->id);
        $sent_email_docket_invoice->delete();
        return $sent_email_docket_invoice;
    }
}
