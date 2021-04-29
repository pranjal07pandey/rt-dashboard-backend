<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    public function userInfo(){
        return $this->hasOne('App\User', 'id','user_id');
    }
    public function companyInfo(){
        return $this->hasOne('App\Company', 'id','company_id');
    }
    public function  assignedInvoice(){
        return $this->hasMany('App\AssignedInvoice','invoice_id','id');
    }
    public function themeInfo(){
        return $this->hasOne('App\DocumentTheme', 'id','theme_document_id');
    }
    public function invoiceXeroSetting(){
        return $this->hasMany('App\InvoiceXeroSetting', 'invoice_id','id');
    }
    public function docketFolderAssign(){
        return $this->hasOne('App\TemplateAssignFolder','template_id','id')->where('type','=',2);
    }
}
