<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketAttachments extends Model
{
    protected $fillable = ['docket_field_id','name','url'];
    public function docketField(){
        return $this->hasMany('App\DocketField','docket_field_id','id')->withTrashed();
    }



}
