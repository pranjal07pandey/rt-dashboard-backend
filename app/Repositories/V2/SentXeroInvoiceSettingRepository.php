<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\SentXeroInvoiceSetting;

class SentXeroInvoiceSettingRepository implements IRepository
{
    public function getModel()
    {
        return new SentXeroInvoiceSetting();
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
        if ($request->has('sent_xero_invoice_setting_id')) {
            $sent_xero_invoice_setting = $this->getModel()->find($request->sent_xero_invoice_setting__id);
        } else {
            $sent_xero_invoice_setting = $this->getModel();
        }

        (!$request->has('xero_field_id'))?:                             $sent_xero_invoice_setting->xero_field_id   = $request->xero_field_id;
        (!$request->has('sent_invoice_xero_id'))?:                      $sent_xero_invoice_setting->sent_invoice_xero_id  = $request->sent_invoice_xero_id;
        (!$request->has('value'))?:                                     $sent_xero_invoice_setting->value  = $request->value;

        $sent_xero_invoice_setting->save();
        return $sent_xero_invoice_setting;
    }

    public function deleteDataById($request = null)
    {
        $sent_xero_invoice_setting = $this->getModel()->find($request->id);
        $sent_xero_invoice_setting->delete();
        return $sent_xero_invoice_setting;
    }
}
