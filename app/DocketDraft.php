<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketDraft extends Model
{
    public function getValueAttribute($value)
    {
        return json_decode($value,true);
    }

    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }

    public function docket(){
        return $this->hasOne('App\Docket', 'id','docket_id');
    }
}
