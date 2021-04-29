<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFiledPreFiller extends Model
{
    protected $fillable = ['docket_field_id','value','index','root_id'];
    public function  docketField(){
        return $this->hasone('App\DocketField','id','docket_field_id');
    }

    public function childs() {
        return $this->hasMany('App\DocketFiledPreFiller','root_id','id') ;
    }
}
