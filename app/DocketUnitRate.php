<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketUnitRate extends Model
{
    protected $fillable = ['id','label','type','csv_header','is_show'];
    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField','id','docket_field_id')->withTrashed();
    }
}
