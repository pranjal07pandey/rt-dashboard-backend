<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFieldNumber extends Model
{
    public function docketInfo(){
        return $this->hasOne('App\Docket','id','docket_id');
    }
}
