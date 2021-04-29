<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    public function messagesRecInfo(){
        return $this->hasMany('App\MessagesRecipients','message_id','id');
    }
    public  function messagesGroups(){
        return $this->hasOne('App\MessagesGroup','id','messages_groups_id');
    }

    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }

    public function messagesRecipientsByUser(User $user){
        return $this->messagesRecInfo()->where('user_id',$user->id)->first();
    }
}
