<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEmailDocketInvoice extends Model
{
    public function sentEmailDocketValueInfo(){
        return $this->hasOne('App\EmailSentDocketValue', 'id','email_sent_docket_value_id');
    }
}
