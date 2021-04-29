<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XeroInvoiceValue extends Model
{
    public function invoiceInfo(){
        return $this->hasOne('App\Invoice', 'id', 'invoice_id');
    }

    public function xeroFieldInfo(){
        return $this->hasOne('App\XeroField','id','xero_field_id');
    }


}
