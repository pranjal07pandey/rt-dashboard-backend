<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentTheme extends Model
{
	public function  themePurchase(){
        return $this->hasMany('App\ThemePurchase','theme_id','id');
    }
}
