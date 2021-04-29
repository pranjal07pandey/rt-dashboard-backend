<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFooter extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
