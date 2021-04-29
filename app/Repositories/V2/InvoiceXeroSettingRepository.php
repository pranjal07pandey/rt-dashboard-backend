<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\InvoiceXeroSetting;

class InvoiceXeroSettingRepository implements IRepository
{
    public function getModel()
    {
        return new InvoiceXeroSetting();
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
        if ($request->has('invoice_xero_setting_id')) {
            $invoice_xero_setting = $this->getModel()->find($request->invoice_xero_setting__id);
        } else {
            $invoice_xero_setting = $this->getModel();
        }

        (!$request->has('invoice_id'))?:                $invoice_xero_setting->invoice_id   = $request->invoice_id;
        (!$request->has('company_xero_id'))?:           $invoice_xero_setting->company_xero_id  = $request->company_xero_id;
        (!$request->has('xero_syn_invoice'))?:          $invoice_xero_setting->xero_syn_invoice  = $request->xero_syn_invoice;

        $invoice_xero_setting->save();
        return $invoice_xero_setting;
    }

    public function deleteDataById($request = null)
    {
        $invoice_xero_setting = $this->getModel()->find($request->id);
        $invoice_xero_setting->delete();
        return $invoice_xero_setting;
    }
}
