<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YesNoDocketsField extends Model
{
    protected $fillable = ['csv_header','is_show'];
    public function YesNoFieldInfo(){
        return $this->hasOne('App\YesNoFields','id','yes_no_field_id');
    }
    public function SentDocValYesNoValueInfo(){
        return $this->hasMany('App\SentDocValYesNoValue','yes_no_docket_field_id','id');
    }
    public function SentEmailDocValYesNoValueInfo(){
        return $this->hasMany('App\SentEmailDocValYesNoValue','yes_no_docket_field_id','id');
    }
}
