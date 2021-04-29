<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\EmailSentInvoicePaymentDetail;

class EmailSentInvoicePaymentDetailRepository implements IRepository
{
    public function getModel()
    {
        return new EmailSentInvoicePaymentDetail();
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
        if ($request->has('email_sent_invoice_payment_detail_id')) {
            $email_sent_invoice_payment_detail = $this->getModel()->find($request->email_sent_invoice_payment_detail__id);
        } else {
            $email_sent_invoice_payment_detail = $this->getModel();
        }

        (!$request->has('email_sent_invoice_id'))?:         $email_sent_invoice_payment_detail->email_sent_invoice_id   = $request->email_sent_invoice_id;
        (!$request->has('company_id'))?:                    $email_sent_invoice_payment_detail->company_id  = $request->company_id;
        (!$request->has('bank_name'))?:                     $email_sent_invoice_payment_detail->bank_name  = $request->bank_name;
        (!$request->has('account_name'))?:                  $email_sent_invoice_payment_detail->account_name  = $request->account_name;
        (!$request->has('bsb_number'))?:                    $email_sent_invoice_payment_detail->bsb_number  = $request->bsb_number;
        (!$request->has('account_number'))?:                $email_sent_invoice_payment_detail->account_number  = $request->account_number;
        (!$request->has('instruction'))?:                   $email_sent_invoice_payment_detail->instruction  = $request->instruction;
        (!$request->has('additional_information'))?:        $email_sent_invoice_payment_detail->additional_information  = $request->additional_information;
        
        $email_sent_invoice_payment_detail->save();
        return $email_sent_invoice_payment_detail;
    }

    public function deleteDataById($request = null)
    {
        $email_sent_invoice_payment_detail = $this->getModel()->find($request->id);
        $email_sent_invoice_payment_detail->delete();
        return $email_sent_invoice_payment_detail;
    }
}
