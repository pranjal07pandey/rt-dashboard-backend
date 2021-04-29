<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    public function senderDetails(){
        return $this->hasOne('App\User','id','sender_user_id');
    }
    public function messageGroupMsg(){
        return $this->hasOne('App\MessageGroupMsg','id','key');
    }

    public function senderEmailUserDetails(){
        return $this->hasOne('App\EmailUser','id','sender_user_id');
    }
}
   