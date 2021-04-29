<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketManualTimer extends Model
{
    public function docketManualTimer(){
        return $this->hasOne('App\DocketManualTimer','id', 'docket_manual_timer_id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\SentDocketsValue','sent_docket_value_id','id');
    }
}
