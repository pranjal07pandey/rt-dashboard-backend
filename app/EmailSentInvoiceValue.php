<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSentInvoiceValue extends Model
{
    public function invoiceFieldInfo(){
        return $this->hasOne('App\InvoiceField', 'id','invoice_field_id');
    }

    public function  emailSentInvoiceImageValue(){
        return $this->hasMany('App\EmailSentInvoiceImage', 'email_sent_invoice_value_id','id');
    }
}
