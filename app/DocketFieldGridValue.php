<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFieldGridValue extends Model
{
    public function girdFields(){
        return $this->hasMany('App\DocketFieldGrid','docket_field_grid_id','id');
    }
}
