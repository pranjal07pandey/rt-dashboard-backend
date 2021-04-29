<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentInvoicePaymentDetail;

class SentInvoicePaymentDetailRepository implements IRepository
{
    public function getModel()
    {
        return new SentInvoicePaymentDetail();
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
        if ($request->has('sent_invoice_payment_detail_id')) {
            $sent_invoice_payment_detail = $this->getModel()->find($request->sent_invoice_payment_detail__id);
        } else {
            $sent_invoice_payment_detail = $this->getModel();
        }

        (!$request->has('sent_invoice_id'))?:                   $sent_invoice_payment_detail->sent_invoice_id   = $request->sent_invoice_id;
        (!$request->has('company_id'))?:                        $sent_invoice_payment_detail->company_id  = $request->company_id;
        (!$request->has('bank_name'))?:                         $sent_invoice_payment_detail->bank_name  = $request->bank_name;
        (!$request->has('account_name'))?:                      $sent_invoice_payment_detail->account_name  = $request->account_name;
        (!$request->has('bsb_number'))?:                        $sent_invoice_payment_detail->bsb_number  = $request->bsb_number;
        (!$request->has('account_number'))?:                    $sent_invoice_payment_detail->account_number  = $request->account_number;
        (!$request->has('instruction'))?:                       $sent_invoice_payment_detail->instruction  = $request->instruction;
        (!$request->has('additional_information'))?:            $sent_invoice_payment_detail->additional_information  = $request->additional_information;

        $sent_invoice_payment_detail->save();
        return $sent_invoice_payment_detail;
    }

    public function deleteDataById($request = null)
    {
        $sent_invoice_payment_detail = $this->getModel()->find($request->id);
        $sent_invoice_payment_detail->delete();
        return $sent_invoice_payment_detail;
    }
}
