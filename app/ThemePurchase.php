<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ThemePurchase extends Model
{
    public function themeInfo(){
        return $this->hasOne('App\DocumentTheme','id','theme_id');
    }

    public function companyInfo(){
        return $this->hasOne('App\Company','id','company_id');
    }

}
