<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimerClient extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }

    public function emailUserInfo(){
        return $this->hasOne('App\EmailUser', 'id','user_id');

    }
    public function timer(){
        return $this->hasOne('App\Timer', 'id','timer_id');
    }
}
