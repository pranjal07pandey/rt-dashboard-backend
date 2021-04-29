<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceXeroSetting extends Model
{
    public function invoice(){
        return $this->hasOne('App\Invoice', 'id','invoice_id');
    }
    public function invoiceXeroSetting(){
        return $this->hasMany('App\InvoiceXeroSetting', 'invoice_id','id');
    }
    public function xeroInvoiceValue(){
        return $this->hasMany('App\XeroInvoiceValue', 'invoice_xero_setting_id','id');
    }

}
