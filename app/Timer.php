<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    protected $fillable = ['user_id', 'location', 'longitude', 'latitude', 'time_started', 'time_ended', 'status'];

    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }

    public function timerLog(){
        return $this->hasMany('App\TimerLog', 'timer_id','id');
    }
    public function timerAttachedTag(){
        return $this->hasMany('App\TimerAttachedTag', 'timer_id','id');
    }
    public function timerClient(){
        return $this->hasMany('App\TimerClient', 'timer_id','id');
    }

}
