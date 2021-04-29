<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class EmailSentInvoice extends Model
{
    public function invoiceInfo(){
        return $this->hasOne('App\Invoice','id','invoice_id');
    }

    public function senderUserInfo(){
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    public function senderCompanyInfo(){
        return $this->hasOne('App\Company','id' , 'company_id');
    }

    public function receiverInfo(){
        return $this->hasOne('App\EmailUser', 'id', 'receiver_user_id');
    }

    public function invoiceDescription(){
        return $this->hasMany('App\EmailSentInvoiceDescription','email_sent_invoice_id','id');
    }

    public function invoiceValue(){
        return $this->hasMany('App\EmailSentInvoiceValue','email_sent_invoice_id','id');
    }
    public function emailSentInvoiceLabels(){
        return $this->hasMany('App\EmailSentInvoiceLabel','email_sent_id','id');
    }
    public function sentEmailInvoiceXero(){
        return $this->hasMany('App\SentEmailInvoiceXero','sent_email_invoice_id','id');
    }

    public function attachedEmailDocketsInfo(){
        return $this->hasMany('App\SentEInvoiceAttachedEDocket','sent_email_invoice_id','id');
    }

    //helper function
    public function encryptedID(){
        return Crypt::encrypt($this->id);
    }

    public function formattedCreatedDate(){
        return Carbon::parse($this->created_at)->format('d-M-Y');
    }

    public function formattedInvoiceID(){
        return 'rt-'.$this->company_id.'-einv-'.$this->company_invoice_id;
    }

    public function paymentDetails(){
        return $this->hasOne('App\EmailSentInvoicePaymentDetail', 'email_sent_invoice_id','id');
    }
}
