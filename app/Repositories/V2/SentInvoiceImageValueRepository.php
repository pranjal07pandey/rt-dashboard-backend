<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoiceImageValue;

class SentInvoiceImageValueRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoiceImageValue();
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
        if ($request->has('sent_invoice_image_value_id')) {
            $sent_invoice_image_value = $this->getModel()->find($request->sent_invoice_image_value__id);
        } else {
            $sent_invoice_image_value = $this->getModel();
        }

        (!$request->has('sent_invoice_value_id'))?:                 $sent_invoice_image_value->sent_invoice_value_id   = $request->sent_invoice_value_id;
        (!$request->has('value'))?:                                 $sent_invoice_image_value->value  = $request->value;

        $sent_invoice_image_value->save();
        return $sent_invoice_image_value;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_image_value = $this->getModel()->find($request->id);
        $sent_invoice_image_value->delete();
        return $sent_invoice_image_value;
    }
}
