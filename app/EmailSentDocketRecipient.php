<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailSentDocketRecipient extends Model
{
    public function emailUserInfo(){
        return $this->hasOne('App\EmailUser','id','email_user_id');
    }

    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }
}
