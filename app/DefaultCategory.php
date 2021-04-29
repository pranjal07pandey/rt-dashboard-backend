<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultCategory extends Model
{
    public function  getDefaultDocket(){
        return $this->hasMany('App\DefaultDocket','docket_id','id');
    }



}
