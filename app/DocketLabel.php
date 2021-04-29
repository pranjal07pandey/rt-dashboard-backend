<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketLabel extends Model
{
    public function CompanyInfo(){
        return $this->hasMany('App\Company','company_id' , 'id');
    }
    public function  sentDocketLabel(){
        return $this->hasOne('App\SentDocketLabel','id','docket_label_id');
    }

}
