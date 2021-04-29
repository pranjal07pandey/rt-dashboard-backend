<?php

namespace App\Mail;

use App\EmailSentInvoice;
use App\EmailUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $emailInvoice;
    public $subject;
    public $recipient;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailSentInvoice $emailInvoice, EmailUser $recipient, $subject)
    {
        $this->emailInvoice =   $emailInvoice;
        $this->recipient    =   $recipient;
        $this->subject      =   $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('notifications@recordtimeapp.com.au',"Record Time")
            ->subject($this->subject)
            ->view('emails.V2.invoice.emailInvoice')
            ->text('emails.V2.invoice.emailInvoicePlain');
//
//        return $this->from('info@recordtimeapp.com.au',$this->emailInvoice->sender_name. " via Record Time")
//            ->replyTo($this->emailInvoice->senderUserInfo->email, $this->emailInvoice->sender_name)
//            ->subject($this->subject)
//            ->view('emails.V2.invoice.emailInvoice')
//            ->text('emails.V2.invoice.emailInvoicePlain');
    }
}
