<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YesNoFields extends Model
{
    protected $fillable = ['id','yes_no_field_id','docket_field_category_id','order','required','label','csv_header','is_show'];

    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField','id','docket_field_id');
    }
    public function yesNoDocketsField(){
        return $this->hasMany('App\YesNoDocketsField','yes_no_field_id','id');
    }
}
