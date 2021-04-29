<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoiceXero;

class SentInvoiceXeroRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoiceXero();
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
        if ($request->has('sent_invoice_xero_id')) {
            $sent_invoice_xero = $this->getModel()->find($request->sent_invoice_xero__id);
        } else {
            $sent_invoice_xero = $this->getModel();
        }

        (!$request->has('sent_invoice_id'))?:                            $sent_invoice_xero->sent_invoice_id   = $request->sent_invoice_id;
        (!$request->has('company_xero_id'))?:                            $sent_invoice_xero->company_xero_id  = $request->company_xero_id;
        (!$request->has('xero_invoice_id'))?:                            $sent_invoice_xero->xero_invoice_id  = $request->xero_invoice_id;

        $sent_invoice_xero->save();
        return $sent_invoice_xero;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_xero = $this->getModel()->find($request->id);
        $sent_invoice_xero->delete();
        return $sent_invoice_xero;
    }
}
