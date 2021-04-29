<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocketFieldGrid extends Model
{
    protected $fillable = [
        'auto_field','csv_header', 'is_show','export_value','is_emailed_subject','time_format','is_hidden'
    ];
    public function docketFieldCategory(){
        return $this->hasOne('App\DocketFiledCategory','id','docket_field_category_id');
    }
    public function sentDocketlabelInfo(){
        return $this->hasOne('App\DocketFieldGridLabel','docket_field_grid_id','id')->where('is_email_docket', 0);
    }
    public function emailSentDocketlabelInfo(){
        return $this->hasOne('App\DocketFieldGridLabel','docket_field_grid_id','id')->where('is_email_docket', 1);
    }

    public function gridFieldFormula(){
        return $this->hasOne('App\GridFieldFormula','docket_field_grid_id','id');
    }

    public function  gridFieldPreFiller(){
        return $this->hasMany('App\DocketGridPrefiller','docket_field_grid_id','id');
    }

    public function  gridFieldAutoPreFiller(){
        return $this->hasMany('App\DocketGridAutoPrefiller','grid_field_id','id');
    }

    public function  linkGridFieldAutoPrefiller(){
        return $this->hasMany('App\DocketGridAutoPrefiller','link_grid_field_id','id');
    }

    public function prefillerEcowise(){
        return $this->hasOne('App\PrefillerEcowise','id','echowise_id');

    }
    public  function linkPrefillerFilter(){
        return $this->hasMany('App\LinkGridPrefillerFilter','docket_field_grid_id','id');
    }







}
