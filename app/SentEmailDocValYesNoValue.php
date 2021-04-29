<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEmailDocValYesNoValue extends Model
{
    public function YesNoDocketsField(){
        return $this->hasOne('App\YesNoDocketsField','id','yes_no_docket_field_id');
    }
    public function emailSentDocketsValue(){
        return $this->hasMany('App\EmailSentDocketsValue','email_sent_docket_value_id','id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\EmailSentDocketsValue','email_sent_docket_value_id','id');
    }
}
