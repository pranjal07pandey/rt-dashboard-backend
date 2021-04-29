<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentInvoiceImage;

class EmailSentInvoiceImageRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentInvoiceImage();
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
        if ($request->has('email_sent_invoice_image_id')) {
            $email_sent_invoice_image = $this->getModel()->find($request->email_sent_invoice_image_id);
        } else {
            $email_sent_invoice_image = $this->getModel();
        }

        (!$request->has('email_sent_invoice_value_id'))?:          $email_sent_invoice_image->email_sent_invoice_value_id   = $request->email_sent_invoice_value_id;
        (!$request->has('value'))?:                          $email_sent_invoice_image->value  = $request->value;

        $email_sent_invoice_image->save();
        return $email_sent_invoice_image;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_invoice_image = $this->getModel()->find($request->id);
        $email_sent_invoice_image->delete();
        return $email_sent_invoice_image;
    }
}
