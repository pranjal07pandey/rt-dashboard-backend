<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Client extends Model
{
    public function companyInfo(){
        return $this->hasOne('App\Company','id','company_id');
    }

    public function requestedCompanyInfo(){
        return $this->hasOne('App\Company','id','requested_company_id');
    }
    public function userInfo(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-M-Y');
    }
}
