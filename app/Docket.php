<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class  Docket extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
    public function companyInfo(){
        return $this->hasOne('App\Company', 'id','company_id');
    }
    public function  assignedDockets(){
        return $this->hasMany('App\AssignedDocket','docket_id','id');
    }
    public function previewFields(){
        return $this->hasMany('App\DocketPreviewField','docket_id','id')->orderBy('order','asc');
    }

    public function getDocketFieldsByCategoryId($categoryId){
        return $this->hasMany('App\DocketField','docket_id','id')->where('docket_field_category_id',$categoryId)->orderBy('order','asc');
    }

    public  function  docketField(){
        return $this->hasMany('App\DocketField','docket_id','id');

    }
    public function docketFieldFooter(){
        return $this->hasMany('App\DocketFieldFooter','docket_id','id');
    }

    public function themeInfo(){
        return $this->hasOne('App\DocumentTheme', 'id','theme_document_id');
    }

    public function sentDockets(){
        return $this->hasMany('App\SentDockets', 'docket_id', 'id');
    }

    public function emailSentDockets(){
        return $this->hasMany('App\EmailSentDocket', 'docket_id','id');
    }

    public function docketFolderAssign(){
        return $this->hasOne('App\TemplateAssignFolder','template_id','id')->where('type','=',1);
    }

    public function defaultRecipient(){
        return $this->hasMany('App\DefaultRecipient','template_id','id')->where('type','=',1);
    }


    public function templateBank(){
        return $this->hasOne('App\TemplateBank', 'template_id','id');
    }

    public function docketDraft(){
        return $this->hasMany('App\DocketDraft','docket_id','id');
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }


}
