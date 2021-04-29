<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketLabel extends Model
{
    public function  sentDocket(){
        return $this->hasMany('App\SentDocket','sent_docket_id','id');
    }

    public function  docketLabel(){
        return $this->hasOne('App\DocketLabel','id','docket_label_id');
    }
}
