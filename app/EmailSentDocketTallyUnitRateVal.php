<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSentDocketTallyUnitRateVal extends Model
{
    public function docketUnitRateInfo(){
        return $this->hasOne('App\DocketTallyableUnitRate','id', 'docket_tally_unit_rate_id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\EmailSentDocketsValue','sent_docket_value_id','id');
    }
}
