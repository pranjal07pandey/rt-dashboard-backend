<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDcoketTimerAttachment extends Model
{
    protected $fillable = ['sent_docket_id', 'type', 'timer_id'];

    public function timerInfo(){
        return $this->hasOne('App\Timer', 'id','timer_id');
    }

    public function docketInfo(){
        return $this->hasOne('App\SentDockets', 'id','sent_docket_id');
    }
}
