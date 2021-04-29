<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentInvoiceDescription;

class EmailSentInvoiceDescriptionRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentInvoiceDescription();
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
        if ($request->has('email_sent_invoice_description_id')) {
            $email_sent_invoice_description = $this->getModel()->find($request->email_sent_invoice_description__id);
        } else {
            $email_sent_invoice_description = $this->getModel();
        }

        (!$request->has('email_sent_invoice_id'))?:                 $email_sent_invoice_description->email_sent_invoice_id   = $request->email_sent_invoice_id;
        (!$request->has('description'))?:                           $email_sent_invoice_description->description  = $request->description;
        (!$request->has('amount'))?:                                $email_sent_invoice_description->amount  = $request->amount;

        $email_sent_invoice_description->save();
        return $email_sent_invoice_description;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_invoice_description = $this->getModel()->find($request->id);
        $email_sent_invoice_description->delete();
        return $email_sent_invoice_description;
    }
}
