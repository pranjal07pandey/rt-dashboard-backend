<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostScreenshot extends Model
{
    protected $fillable = ['post_id', 'name',' image'];

    public function postInfo(){
        return $this->hasOne('App\Post', 'id','post_id');
    }
}
