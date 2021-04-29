<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Email_Client extends Model
{
    public function CompanyInfo(){
        return $this->hasMany('App\Company','company_id' , 'id');
    }

    public function emailUser(){
        return $this->belongsTo('App\EmailUser', 'email_user_id', 'id');
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }
}
