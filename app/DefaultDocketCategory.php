<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultDocketCategory extends Model
{
    public function getDefaultDocket(){
        return $this->hasOne('App\DefaultDocket','id','default_docket_id');
    }
}
