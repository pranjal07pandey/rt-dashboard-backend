<?php


namespace App\Repositories\V2;

use App\AppInterface\IRepository;
use App\InvoiceSetting;

class InvoiceSettingRepository implements IRepository
{
    public function getModel()
    {
        return new InvoiceSetting();
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
        if ($request->has('invoice_setting_id')) {
            $invoice_setting = $this->getModel()->find($request->invoice_setting__id);
        } else {
            $invoice_setting = $this->getModel();
        }

        (!$request->has('user_id'))?:                           $invoice_setting->user_id   = $request->user_id;
        (!$request->has('company_id'))?:                        $invoice_setting->company_id  = $request->company_id;
        (!$request->has('bank_name'))?:                         $invoice_setting->bank_name  = $request->bank_name;
        (!$request->has('account_name'))?:                      $invoice_setting->account_name  = $request->account_name;
        (!$request->has('bsb_number'))?:                        $invoice_setting->bsb_number  = $request->bsb_number;
        (!$request->has('account_number'))?:                    $invoice_setting->account_number  = $request->account_number;
        (!$request->has('instruction'))?:                       $invoice_setting->instruction  = $request->instruction;
        (!$request->has('additional_information'))?:            $invoice_setting->additional_information  = $request->additional_information;

        $invoice_setting->save();
        return $invoice_setting;
    }

    public function deleteDataById($request = null)
    {
        $invoice_setting = $this->getModel()->find($request->id);
        $invoice_setting->delete();
        return $invoice_setting;
    }
}
