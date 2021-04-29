<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable =[
       'sn'
    ];
    public function userInfo(){
        return $this->hasOne('App\User','id','user_id');
    }

    public function companyInfo(){
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function  sentDocket(){
        return $this->hasMany('App\SentDockets', 'user_id', 'user_id');
    }

    public function totalSentDocketByDocketId($docketId, $userId){
        return $this->with(['sentDocket' => function($query) use ($docketId, $userId) {
            $query->where('docket_id', $docketId)->where('user_id',$userId);
        }]);
    }
}
