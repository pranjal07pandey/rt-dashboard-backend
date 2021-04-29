<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSentInvoiceLabel extends Model
{
    public function  sentEmailedInvoice(){
        return $this->hasMany('App\EmailSentInvoice','email_sent_id','id');
    }

    public function  invoiceLabel(){
        return $this->hasOne('App\Invoice_Label','id','invoice_label_id');
    }
}
