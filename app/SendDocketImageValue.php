<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SendDocketImageValue extends Model
{
    public function  senddocketsValue(){
        return $this->hasMany('App\SendDocketsValue');
    }
}
