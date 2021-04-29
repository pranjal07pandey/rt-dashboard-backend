<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentInvoiceValue extends Model
{
    public function invoiceFieldInfo(){
        return $this->hasOne('App\InvoiceField', 'id','invoice_field_id');
    }

    public function  sentInvoiceImageValue(){
        return $this->hasMany('App\SentInvoiceImageValue', 'sent_invoice_value_id','id');
    }

}
