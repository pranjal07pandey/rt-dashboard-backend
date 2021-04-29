<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessagesGroupUser extends Model
{
    public function messagesGroupinfo(){
        return $this->hasOne('App\MessagesGroup','id','messages_groups_id');
    }
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
