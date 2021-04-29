<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XeroField extends Model
{
    public function xeroInvoiceValue(){
        return $this->hasMany('App\XeroInvoiceValue', 'xero_field_id','id');
    }
    public function sentXeroInvoiceSetting(){
        return $this->hasMany('App\SentXeroInvoiceSetting', 'xero_field_id','id');
    }
    public function sentXeroEmailInvoiceSetting(){
        return $this->hasMany('App\SentXeroEmailInvoiceSetting', 'xero_field_id','id');
    }
}
