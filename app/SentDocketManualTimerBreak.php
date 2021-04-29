<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketManualTimerBreak extends Model
{
    public function docketManualTimerBreak(){
        return $this->hasOne('App\DocketManualTimerBreak','id', 'manual_timer_break_id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\SentDocketsValue','sent_docket_value_id','id');
    }
}
