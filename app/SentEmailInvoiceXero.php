<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEmailInvoiceXero extends Model
{
    public function  sentInvoice(){
        return $this->hasMany('App\EmailSentInvoice','sent_email_invoice_id','id');
    }
    public function sentXeroEmailInvoiceSetting(){
        return $this->hasMany('App\SentXeroEmailInvoiceSetting','sent_email_invoice_xero_id','id');
    }
}
