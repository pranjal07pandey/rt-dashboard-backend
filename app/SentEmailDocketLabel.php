<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEmailDocketLabel extends Model
{
    public function  emailSentDocket(){
        return $this->hasMany('App\EmailSentDocket','email_sent_docket_id','id');
    }

    public function  docketLabel(){
        return $this->hasOne('App\DocketLabel','id','docket_label_id');
    }
}
