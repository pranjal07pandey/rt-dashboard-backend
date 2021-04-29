<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssignedDocket extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function assignedBy(){
        return $this->hasOne('App\User', 'id', 'assigned_by');
    }

    public function docketInfo(){
        return $this->hasOne('App\Docket', 'id', 'docket_id');
    }
}
