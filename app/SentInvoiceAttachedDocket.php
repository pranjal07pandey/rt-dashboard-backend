<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentInvoiceAttachedDocket extends Model
{
    public function docketInfo(){
        return $this->hasOne('App\SentDockets','id','sent_docket_id');
    }
}
