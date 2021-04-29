<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentXeroEmailInvoiceSetting extends Model
{
    public function  sentInvoice(){
        return $this->hasMany('App\EmailSentInvoice','email_sent_invoice_id','id');
    }
    public function xeroFieldInfo(){
        return $this->hasOne('App\XeroField','id','xero_field_id');
    }
}
