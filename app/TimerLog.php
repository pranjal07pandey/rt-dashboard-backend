<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimerLog extends Model
{
    protected $fillable = ['timer_id', 'location', 'longitude', 'latitude', 'time_started', 'time_finished', 'reason'];

    public function timerInfo(){
        return $this->hasOne('App\Timer', 'id','timer_id');
    }
}
