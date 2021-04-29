<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SentDocketsValue extends Model
{
    public function sentDocket(){
        return $this->hasOne('App\SentDockets', 'id','sent_docket_id');
    }

    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField', 'id','docket_field_id');
    }

    public function docketFieldCategoryInfo(){
        return $this->hasOne('App\DocketField');
    }

    public function docketUnitRate(){
        return $this->hasMany('App\DocketUnitRate','docket_field_id','docket_field_id');
    }

    public function sentDocketUnitRateValue(){
        return $this->hasMany('App\SentDocketUnitRateValue', 'sent_docket_value_id', 'id');
    }

    public function  sentDocketImageValue(){
        return $this->hasMany('App\SendDocketImageValue', 'sent_docket_value_id','id');
    }

    public function attachedDocument(){
        return $this->hasMany('App\DocketAttachments','docket_field_id','docket_field_id');
    }

    public function sentDocketAttachment(){
        return $this->hasMany('App\SentDocketAttachment','sent_dockets_value_id','id');
    }

    public function SentDocValYesNoValueInfo(){
        return $this->hasMany('App\SentDocValYesNoValue','sent_docket_value_id','id');
    }

    public function docketManualTimer(){
        return $this->hasMany('App\DocketManualTimer','docket_field_id','docket_field_id');
    }
    public function docketManualTimerBreak(){
        return $this->hasMany('App\DocketManualTimerBreak','docket_field_id','docket_field_id');
    }

    public function sentDocketManualTimer(){
        return $this->hasMany('App\SentDocketManualTimer', 'sent_docket_value_id', 'id');
    }
    public function sentDocketManualTimerBreak(){
        return $this->hasMany('App\SentDocketManualTimerBreak', 'sent_docket_value_id', 'id');

    }

    public function sentDocketFieldGridValues(){
        return $this->hasMany('App\DocketFieldGridValue', 'docket_id', 'sent_docket_id')->where('is_email_docket', 0)->where('docket_field_id',$this->docket_field_id);
    }

    public function sentDocketFieldGridLabels(){
        return $this->hasMany('App\DocketFieldGridLabel', 'docket_id', 'sent_docket_id')->where('is_email_docket', 0)->where('docket_field_id',$this->docket_field_id);
    }


    public function tallyableUnitRate(){
        return $this->hasMany('App\DocketTallyableUnitRate','docket_field_id','docket_field_id');
    }

    public function sentDocketTallyableUnitRateValue(){
        return $this->hasMany('App\SentDocketTallyUnitRateVal', 'sent_docket_value_id', 'id');
    }


}
