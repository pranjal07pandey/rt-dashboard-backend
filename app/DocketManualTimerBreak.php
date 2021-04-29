<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketManualTimerBreak extends Model
{
    protected $fillable = ['csv_header','is_show'];

    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField','id','docket_field_id');
    }
}
