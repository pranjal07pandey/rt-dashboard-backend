<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SentInvoice extends Model
{
    public function invoiceInfo(){
        return $this->hasOne('App\Invoice','id','invoice_id');
    }

    public function senderUserInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function receiverUserInfo(){
        return $this->hasOne('App\User', 'id', 'receiver_user_id');
    }

    public function receiverCompanyInfo(){
        return $this->hasOne('App\Company','id' , 'receiver_company_id');
    }

    public function senderCompanyInfo(){
        return $this->hasOne('App\Company','id' , 'company_id');
    }

    public function attachedDocketsInfo(){
        return $this->hasMany('App\SentInvoiceAttachedDocket','sent_invoice_id','id');
    }
    //for labeling
    public function sentInvoiceLabels(){
        return $this->hasMany('App\SentInvoiceLabel','sent_invoice_id','id');
    }

    public function invoiceDescription()
    {
        return $this->hasMany('App\SentInvoiceDescription','sent_invoice_id','id');
    }
    public function themeInfo(){
        return $this->hasOne('App\DocumentTheme', 'id','theme_document_id');
    }
    public function sentInvoiceXero(){
        return $this->hasMany('App\SentInvoiceXero','sent_invoice_id','id');
    }
    public function xeroSyncedInvoice(){
        return $this->hasMany('App\XeroSyncedInvoice','sent_invoice_id' , 'id');
    }


    public function formattedInvoiceID(){
        return 'rt-'.$this->company_id.'-inv-'.$this->company_invoice_id;
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }
    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }
}
