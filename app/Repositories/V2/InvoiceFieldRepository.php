<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\InvoiceField;

class InvoiceFieldRepository implements IRepository
{
    public function getModel()
    {
        return new InvoiceField();
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
        if ($request->has('inoice_field_id')) {
            $inoice_field = $this->getModel()->find($request->inoice_field__id);
        } else {
            $inoice_field = $this->getModel();
        }

        (!$request->has('invoice_id'))?:                        $inoice_field->invoice_id   = $request->invoice_id;
        (!$request->has('invoice_field_category_id'))?:         $inoice_field->invoice_field_category_id  = $request->invoice_field_category_id;
        (!$request->has('order'))?:                             $inoice_field->order   = $request->order;
        (!$request->has('label'))?:                             $inoice_field->label   = $request->label;

        $inoice_field->save();
        return $inoice_field;
    }

    public function deleteDataById($request = null)
    {
        $inoice_field = $this->getModel()->find($request->id);
        $inoice_field->delete();
        return $inoice_field;
    }
}
