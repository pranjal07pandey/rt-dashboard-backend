<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentDocketInvoice;

class SentDocketInvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new SentDocketInvoice();
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
        if ($request->has('sent_docket_invoice_id')) {
            $sent_docket_invoice = $this->getModel()->find($request->sent_docket_invoice__id);
        } else {
            $sent_docket_invoice = $this->getModel();
        }

        (!$request->has('sent_docket_id'))?:                $sent_docket_invoice->sent_docket_id   = $request->sent_docket_id;
        (!$request->has('sent_docket_value_id'))?:          $sent_docket_invoice->sent_docket_value_id  = $request->sent_docket_value_id;
        (!$request->has('type'))?:                          $sent_docket_invoice->type  = $request->type;

        $sent_docket_invoice->save();
        return $sent_docket_invoice;
    }

    public function deleteDataById($request = null)
    {
        $sent_docket_invoice = $this->getModel()->find($request->id);
        $sent_docket_invoice->delete();
        return $sent_docket_invoice;
    }
}
