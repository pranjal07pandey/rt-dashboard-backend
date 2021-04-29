<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEInvoiceAttachedEDocket extends Model
{
    public function docketInfo(){
        return $this->hasOne('App\EmailSentDocket','id','sent_email_docket_id');
    }
}
