<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoiceValue;

class SentInvoiceValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoiceValue();
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
        if ($request->has('sent_invoice_value_id')) {
            $sent_invoice_value = $this->getModel()->find($request->sent_invoice_value__id);
        } else {
            $sent_invoice_value = $this->getModel();
        }

        (!$request->has('sent_invoice_id'))?:                   $sent_invoice_value->sent_invoice_id   = $request->sent_invoice_id;
        (!$request->has('invoice_field_id'))?:                  $sent_invoice_value->invoice_field_id  = $request->invoice_field_id;
        (!$request->has('value'))?:                             $sent_invoice_value->value  = $request->value;
        (!$request->has('label'))?:                             $sent_invoice_value->label  = $request->label;

        $sent_invoice_value->save();
        return $sent_invoice_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_value = $this->getModel()->find($request->id);
        $sent_invoice_value->delete();
        return $sent_invoice_value;
    }
}
