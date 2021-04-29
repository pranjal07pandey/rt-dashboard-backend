<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentInvoiceXero extends Model
{
    public function  sentInvoice(){
        return $this->hasMany('App\SentInvoice','sent_invoice_id','id');
    }
    public function sentXeroInvoiceSettingInfo(){
        return $this->hasMany('App\SentXeroInvoiceSetting','sent_invoice_xero_id','id');
    }


}
