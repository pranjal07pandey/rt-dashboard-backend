<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailSentDocketValue extends Model
{
    public function emailSentDocket(){
        return $this->hasOne('App\EmailSentDocket', 'id','email_sent_docket_id');
    }

    public function docketFieldInfo(){
        return $this->hasOne('App\DocketField', 'id','docket_field_id')->withTrashed();
    }

    public function docketFieldCategoryInfo(){
        return $this->hasOne('App\DocketFIl');
    }

    public function docketUnitRate(){
        return $this->hasMany('App\DocketUnitRate','docket_field_id','docket_field_id');
    }

    public function sentDocketUnitRateValue(){
        return $this->hasMany('App\EmailSnetDocketUnitRateValue', 'sent_docket_value_id', 'id');
    }

    public function  sentDocketImageValue(){
        return $this->hasMany('App\EmailSentDocketImageValue', 'sent_docket_value_id','id');
    }
    public function attachedDocument(){
        return $this->hasMany('App\DocketAttachments','docket_field_id','docket_field_id');
    }

    public function sentEmailAttachment(){
        return $this->hasMany('App\SentEmailAttachment','sent_email_value_id','id');
    }
    public function SentEmailDocValYesNoValueInfo(){
        return $this->hasMany('App\SentEmailDocValYesNoValue','email_sent_docket_value_id','id');
    }

    public function docketManualTimer(){
        return $this->hasMany('App\DocketManualTimer','docket_field_id','docket_field_id');
    }
    public function docketManualTimerBreak(){
        return $this->hasMany('App\DocketManualTimerBreak','docket_field_id','docket_field_id');
    }
    public function emailSentDocManualTimer(){
        return $this->hasMany('App\EmailSentDocManualTimer', 'sent_docket_value_id', 'id');
    }
    public function emailSentDocManualTimerBrk(){
        return $this->hasMany('App\EmailSentDocManualTimerBrk', 'sent_docket_value_id', 'id');
    }

    public function emailSentDocketFieldGridValues(){
        return $this->hasMany('App\DocketFieldGridValue', 'docket_id', 'email_sent_docket_id')->where('is_email_docket', 1)->where('docket_field_id',$this->docket_field_id);
    }

    public function emailSentDocketFieldGridLabels(){
        return $this->hasMany('App\DocketFieldGridLabel', 'docket_id', 'email_sent_docket_id')->where('is_email_docket', 1)->where('docket_field_id',$this->docket_field_id);
    }
    public function tallyableUnitRate(){
        return $this->hasMany('App\DocketTallyableUnitRate','docket_field_id','docket_field_id');
    }

    public function sentDocketTallyableUnitRateValue(){
        return $this->hasMany('App\EmailSentDocketTallyUnitRateVal', 'sent_docket_value_id', 'id');
    }


}
