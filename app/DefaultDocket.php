<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DefaultDocket extends Model
{

    public function getDefaultCategory(){
        return $this->hasOne('App\DefaultCategory', 'id', 'category_id');
    }
    public function getDefaultDocketCategory(){
        return $this->hasMany('App\DefaultDocketCategory', 'default_docket_id', 'id');
    }

    public function getDocketFieldsByCategoryId($categoryId){
        return $this->hasMany('App\DefaultDocketField','default_docket_id','id')->where('default_docket_field_category_id',$categoryId)->orderBy('order','asc');
    }

}
