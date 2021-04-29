<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketTallyableUnitRate extends Model
{
    protected $fillable = ['id','label','type','csv_header','is_show'];
    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField','id','docket_field_id');
    }
    public function sentDocketValue(){
        return $this->hasOne('App\SentDocketsValue','id', 'sent_docket_value_id');
    }
}
