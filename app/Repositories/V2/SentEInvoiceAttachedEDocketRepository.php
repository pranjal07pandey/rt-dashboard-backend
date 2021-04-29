<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentEInvoiceAttachedEDocket;

class SentEInvoiceAttachedEDocketRepository implements IRepository
{
    public function getModel()
    {
        return new SentEInvoiceAttachedEDocket();
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
        if ($request->has('sent_invoice_attached_docket_id')) {
            $sent_invoice_attached_docket = $this->getModel()->find($request->sent_invoice_attached_docket__id);
        } else {
            $sent_invoice_attached_docket = $this->getModel();
        }

        (!$request->has('sent_email_invoice_id'))?:     $sent_invoice_attached_docket->sent_email_invoice_id   = $request->sent_email_invoice_id;
        (!$request->has('sent_email_docket_id'))?:      $sent_invoice_attached_docket->sent_email_docket_id  = $request->sent_email_docket_id;

        $sent_invoice_attached_docket->save();
        return $sent_invoice_attached_docket;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_attached_docket = $this->getModel()->find($request->id);
        $sent_invoice_attached_docket->delete();
        return $sent_invoice_attached_docket;
    }
}
