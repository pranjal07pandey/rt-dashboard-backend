<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimerAttachedTag extends Model
{
    public function timerInfo(){
        return $this->hasOne('App\Timer', 'id','timer_id');
    }

    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
