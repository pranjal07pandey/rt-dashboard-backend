<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketRecipient extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function sentDocketInfo(){
        return $this->hasOne('App\SentDockets','id','sent_docket_id');
    }

}
