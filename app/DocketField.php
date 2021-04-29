<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocketField extends Model
{
    use SoftDeletes;

    protected $fillable = ['id','docket_id','docket_field_category_id', 'order','label','required','csv_header','is_show','default_value'];

    public function fieldCategoryInfo(){
        return $this->hasOne('App\DocketFiledCategory','id','docket_field_category_id');
    }

    public function unitRate(){
        return $this->hasMany('App\DocketUnitRate','docket_field_id','id');
    }

    public function docketInfo(){
        return $this->hasOne('App\Docket','id','docket_id');
    }

    public function docketInvoiceField(){
        return $this->hasOne('App\DocketInvoiceField', 'docket_Field_id','id');
    }

    public function docketPreviewField(){
        return $this->hasOne('App\DocketPreviewField', 'docket_Field_id','id');
    }
    //Docket prefiller
    public function docketPreFiller(){
        return $this->hasMany('App\DocketFiledPreFiller','docket_field_id','id');
    }

    public function docketFieldValueBySentDocketId($sentDocketId){
        return $this->hasOne('App\SentDocketsValue','docket_field_id','id')->where('sent_docket_id',$sentDocketId);
    }

    public function docketFieldValueByEmailSentDocketId($sentDocketId){
        return $this->hasOne('App\EmailSentDocketValue','docket_field_id','id')->where('email_sent_docket_id',$sentDocketId);
    }

    public function docketFieldFooter (){
        return $this->hasOne('App\DocketFieldFooter','field_id','id');
    }
    public function docketAttached (){
        return $this->hasMany('App\DocketAttachments','docket_field_id','id');
    }
    public function yesNoField(){
        return $this->hasMany('App\YesNoFields','docket_field_id','id');
    }

    public function docketManualTimer(){
        return $this->hasMany('App\DocketManualTimer','docket_field_id','id');
    }

    public function docketManualTimerBreak(){
        return $this->hasMany('App\DocketManualTimerBreak','docket_field_id','id');
    }
    public function docketFieldNumbers(){
        return $this->hasOne('App\DocketFieldNumber','docket_field_id','id');
    }

    public function docketFieldSignatureOption(){
        return $this->hasOne('App\DocketFieldSignatureOption','docket_field_id','id');

    }
    public function docketFieldDateOption(){
        return $this->hasOne('App\DocketFieldDateOption','docket_field_id','id');
    }
    public function girdFields(){
        return $this->hasMany('App\DocketFieldGrid','docket_field_id','id')->orderBy('order', 'ASC');
    }
    public function tallyUnitRate(){
        return $this->hasMany('App\DocketTallyableUnitRate','docket_field_id','id');
    }

    public function exportMapping(){
        return $this->hasOne('App\ExportMapping','docket_field_id','id');
    }

    public  function  docketConstantField()
    {
        return $this->hasOne('App\DocketConstantField', 'docket_field_id', 'id');
    }
    public function prefillerEcowise(){
        return $this->hasOne('App\PrefillerEcowise','id','echowise_id');

    }
    public  function linkPrefillerFilter(){
        return $this->hasMany('App\LinkPrefillerFilter','docket_field_id','id');
    }





}
