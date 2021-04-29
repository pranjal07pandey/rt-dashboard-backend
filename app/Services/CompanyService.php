<?php
namespace App\Services;

use App\AssignedInvoice;
use App\Company;
use App\EmailSentDocket;
use App\EmailSentInvoice;
use App\SentDocketRecipient;
use App\SentDockets;
use App\SentInvoice;
use Illuminate\Database\Eloquent\Collection;

class CompanyService {

    //---Dockets---//
    function sentDockets(Company $company){
        $sentDockets    =   SentDockets::with('sentDocketLabels.docketLabel','docketInfo.previewFields','sentDocketRecipientApproved','sentDocketRecipientApproval')->where('sender_company_id',$company->id)->orderBy('created_at','desc')->get();
        return $sentDockets;
    }
    function receivedDockets(Company $company){
        $receivedDockets    =   Collection::make(new SentDockets);
        $sentDocketRecipients    =   SentDocketRecipient::with('sentDocketInfo.sentDocketLabels.docketLabel','sentDocketInfo.docketInfo.previewFields', 'sentDocketInfo.sentDocketRecipientApproved','sentDocketInfo.sentDocketRecipientApproval')->whereIn('user_id',$company->getAllCompanyUserIds())->distinct('sent_docket_id')->orderBy('sent_docket_id','desc')->get();
        foreach ($sentDocketRecipients as $sentDocketRecipient){
            if($sentDocketRecipient->sentDocketInfo!=null)
                $receivedDockets->push($sentDocketRecipient->sentDocketInfo);
        }
        return $receivedDockets;
    }
    function allSentDockets(Company $company){
        $allSentDockets     =   ($this->sentDockets($company)->merge($this->receivedDockets($company)))->unique();
        return $allSentDockets;
    }

    function emailSentDockets(Company $company){
        $sentEmailDockets   =    EmailSentDocket::with('sentEmailDocketLabels.docketLabel','recipientInfo.emailUserInfo','docketInfo.previewFields')->where('company_id',$company->id)->orderBy('created_at','desc')->get();
        return $sentEmailDockets;
    }

    //---Invoice---//
    function sentInvoices(Company $company){
        $sentInvoices   =   SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo','senderCompanyInfo')->where('company_id',$company->id)->orderBy('created_at','desc')->get();
        return $sentInvoices;
    }

    function receivedInvoices(Company $company){
        $receivedInvoices   =    SentInvoice::with('sentInvoiceLabels.invoiceLabel','receiverUserInfo','receiverCompanyInfo','invoiceInfo')->Where('receiver_company_id',$company->id)->orderBy('created_at','desc')->get();
        return $receivedInvoices;
    }

    function allSentInvoices(Company $company){
        $allSentInvoices    =   ($this->sentInvoices($company)->merge($this->receivedInvoices($company))->unique());
        return $allSentInvoices;
    }

    function emailSentInvoices(Company $company){
        $emailSentInvoices  =   EmailSentInvoice::with('emailSentInvoiceLabels','invoiceDescription','receiverInfo')->where('company_id',$company->id)->orderBy('created_at','desc')->get();
        return $emailSentInvoices;
    }

    //---Invoice Template---//
    function assignedInvoiceTemplate(Company $company){
        $assignedInvoiceTemplate = AssignedInvoice::with('invoiceInfo')->whereIn('invoice_id',$company->invoices->pluck('id')->toArray())->get()->unique('invoice_id');
        return $assignedInvoiceTemplate;
    }
}