<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentInvoiceLabel extends Model
{
    public function  sentInvoice(){
        return $this->hasMany('App\SentInvoice','sent_invoice_id','id');
    }

    public function  invoiceLabel(){
        return $this->hasOne('App\Invoice_Label','id','invoice_label_id');
    }
}
