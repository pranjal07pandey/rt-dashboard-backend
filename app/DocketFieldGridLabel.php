<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFieldGridLabel extends Model
{

    public function docketFieldGrid(){
        return $this->hasOne('App\DocketFieldGrid', 'id','docket_field_grid_id');
    }







}
