<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentEmailAttachment extends Model
{
    public function sentDocketsValue(){
        return $this->hasMany('App\EmailSentDocketValue','sent_dockets_value_id','id');
    }
}
