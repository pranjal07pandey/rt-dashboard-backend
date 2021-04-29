<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentXeroInvoiceSetting extends Model
{
    public function  sentInvoiceXero(){
        return $this->hasMany('App\SentInvoiceXero','sent_invoice_xero_id','id');
    }
    public function xeroFieldInfo(){
        return $this->hasOne('App\XeroField','id','xero_field_id');
    }
}
