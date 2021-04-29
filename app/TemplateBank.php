<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemplateBank extends Model
{
    protected $fillable = [
        'downloads'
    ];
    public function docket(){
        return $this->hasOne('App\Docket', 'id','template_id');
    }
    public function company(){
        return $this->hasOne('App\Company', 'id','company_id');
    }
    public function user(){
        return $this->hasOne('App\User', 'id','user_id');
    }
}
