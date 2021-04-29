<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFieldFooter extends Model
{
    public function docketInfo(){
        return $this->hasOne('App\Docket','id','docket_id');
    }

    public function docketField(){
        return $this->hasMany('App\DocketField','field_id','id');
    }
}
