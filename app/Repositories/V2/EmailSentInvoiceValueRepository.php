<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentInvoiceValue;

class EmailSentInvoiceValueRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentInvoiceValue();
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
        if ($request->has('email_sent_invoice_value_id')) {
            $email_sent_invoice_value = $this->getModel()->find($request->email_sent_invoice_value_id);
        } else {
            $email_sent_invoice_value = $this->getModel();
        }

        (!$request->has('email_sent_invoice_id'))?:          $email_sent_invoice_value->email_sent_invoice_id   = $request->email_sent_invoice_id;
        (!$request->has('invoice_field_id'))?:               $email_sent_invoice_value->invoice_field_id   = $request->invoice_field_id;
        (!$request->has('value'))?:                          $email_sent_invoice_value->value  = $request->value;
        (!$request->has('label'))?:                          $email_sent_invoice_value->label  = $request->label;

        $email_sent_invoice_value->save();
        return $email_sent_invoice_value;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_invoice_value = $this->getModel()->find($request->id);
        $email_sent_invoice_value->delete();
        return $email_sent_invoice_value;
    }
}
