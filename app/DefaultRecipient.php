<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultRecipient extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User','id','recipient_id');
    }

    public function emailUser(){
        return $this->hasOne('App\EmailUser','id','recipient_id');
    }
}
