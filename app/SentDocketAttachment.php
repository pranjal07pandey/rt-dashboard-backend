<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketAttachment extends Model
{
    public function sentDocketsValue(){
        return $this->hasMany('App\SentDocketsValue','sent_dockets_value_id','id');
    }
}
