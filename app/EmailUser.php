<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class EmailUser extends Model
{
    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }

    public function emailClient(){
        return $this->hasOne('App\Email_Client','email_user_id','id');
    }
}
