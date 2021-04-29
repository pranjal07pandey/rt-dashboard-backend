<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invoice_Label extends Model
{
    public function CompanyInfo(){
        return $this->hasMany('App\Company','company_id' , 'id');
    }
    public function  sentInvoiceLabel(){
        return $this->hasOne('App\SentInvoiceLabel','id','invoice_label_id');
    }
}
