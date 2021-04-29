<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessagesGroup extends Model
{
    public function messagesGroupUserinfo(){
        return $this->hasMany('App\MessagesGroupUser','messages_groups_id','id');
    }
    public function messagesinfo(){
        return $this->hasMany('App\Messages','messages_groups_id','id');
    }

    public function getGroupTitle(){

    }
}
