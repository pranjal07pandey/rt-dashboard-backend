<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public function docketProjectInfo(){
        return $this->hasMany('App\DocketProject','project_id','id');
    }

    public function sentDocketProjectInfo(){
        return $this->hasMany('App\SentDocketProject','project_id','id');
    }

    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
