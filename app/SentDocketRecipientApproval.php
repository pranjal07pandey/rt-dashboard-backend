<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketRecipientApproval extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function sentDocketInfo(){
        return $this->hasOne('App\SentDockets','id','sent_docket_id');
    }

    public function sentDocketReject(){
        return $this->hasMany('App\SentDocketReject','id','sent_docket_id');
    }
}
