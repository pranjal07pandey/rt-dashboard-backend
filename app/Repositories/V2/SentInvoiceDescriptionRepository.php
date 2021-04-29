<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoiceDescription;

class SentInvoiceDescriptionRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoiceDescription();
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
        if ($request->has('sent_invoice_description_id')) {
            $sent_invoice_description = $this->getModel()->find($request->sent_invoice_description__id);
        } else {
            $sent_invoice_description = $this->getModel();
        }

        (!$request->has('sent_invoice_id'))?:                 $sent_invoice_description->sent_invoice_id   = $request->sent_invoice_id;
        (!$request->has('description'))?:                     $sent_invoice_description->description  = $request->description;
        (!$request->has('amount'))?:                          $sent_invoice_description->amount  = $request->amount;

        $sent_invoice_description->save();
        return $sent_invoice_description;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_description = $this->getModel()->find($request->id);
        $sent_invoice_description->delete();
        return $sent_invoice_description;
    }
}
