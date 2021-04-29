<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['category_id', 'name',' slug', 'description', 'featured_image'];

    public function categoryInfo(){
        return $this->hasOne('App\Category', 'id','category_id');
    }

    public function postScreenshot(){
        return $this->hasMany('App\PostScreenshot', 'post_id','id');
    }
}
