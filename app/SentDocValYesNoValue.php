<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocValYesNoValue extends Model
{
    public function YesNoDocketsField(){
        return $this->hasOne('App\YesNoDocketsField','id','yes_no_docket_field_id');
    }
    public function sentDocketsValue(){
        return $this->hasMany('App\SentDocketsValue','sent_docket_value_id','id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\SentDocketsValue','sent_docket_value_id','id');
    }
}
