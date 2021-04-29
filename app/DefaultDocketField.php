<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultDocketField extends Model
{
    public function docketInfo(){
        return $this->hasOne('App\DefaultDocket','id','default_docket_id');
    }
}
