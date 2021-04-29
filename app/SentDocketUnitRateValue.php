<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketUnitRateValue extends Model
{
    public function docketUnitRateInfo(){
        return $this->hasOne('App\DocketUnitRate','id', 'docket_unit_rate_id');
    }

    public function sentDocketValue(){
        return $this->hasOne('App\SentDocketsValue','id', 'sent_docket_value_id');
    }
}
