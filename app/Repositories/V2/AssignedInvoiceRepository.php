<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\AssignedInvoice;

class AssignedInvoiceRepository implements IRepository
{
    public function getModel()
    {
        return new AssignedInvoice();
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
        if ($request->has('assigned_invoice_id')) {
            $assigned_invoice = $this->getModel()->find($request->assigned_invoice_id);
        } else {
            $assigned_invoice = $this->getModel();
        }

        (!$request->has('user_id'))?:                                     $assigned_invoice->user_id   = $request->user_id;
        (!$request->has('assigned_by'))?:                                         $assigned_invoice->assigned_by  = $request->assigned_by;
        (!$request->has('invoice_id'))?:                                        $assigned_invoice->invoice_id  = $request->invoice_id;

        $assigned_invoice->save();
        return $assigned_invoice;
    }

    public function deleteDataById($request = null)
    {
        $assigned_invoice = $this->getModel()->find($request->id);
        $assigned_invoice->delete();
        return $assigned_invoice;
    }
}
