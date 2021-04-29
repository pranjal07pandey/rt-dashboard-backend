<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketGridPrefiller extends Model
{
    public function  docketField(){
        return $this->hasone('App\DocketFieldGrid','id','docket_field_grid_id');
    }

    public function childs() {
        return $this->hasMany('App\DocketGridPrefiller','root_id','id') ;
    }
}
