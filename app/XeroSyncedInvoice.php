<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class XeroSyncedInvoice extends Model
{
    public function sentInvoiceInfo(){
        return $this->hasOne('App\SentInvoice','id','sent_invoice_id');
    }}
