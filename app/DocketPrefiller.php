<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketPrefiller extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
    public function docketPrefillerValue(){
        return $this->hasMany('App\DocketPrefillerValue','docket_prefiller_id','id');
    }
}
