<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketPrefillerValue extends Model
{
    public function  docketPrefiller(){
        return $this->hasMany('App\DocketPrefiller','docket_prefiller_id','id');
    }
}
