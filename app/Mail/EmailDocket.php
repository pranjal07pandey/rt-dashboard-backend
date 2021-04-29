<?php

namespace App\Mail;

use App\EmailSentDocket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailDocket extends Mailable
{
    use Queueable, SerializesModels;

    public $emailDocket;
    public $recipient;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailSentDocket $emailSentDocket, $recipient,$subject)
    {
        $this->emailDocket  =   $emailSentDocket;
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
            ->view('emails.v2.docket.emailDocket')
            ->text('emails.v2.docket.emailDocketPlain');

//        return $this->from('info@recordtimeapp.com.au',$this->emailDocket->sender_name. " via Record Time")
//                    ->replyTo($this->emailDocket->senderUserInfo->email, $this->emailDocket->sender_name)
//                    ->subject($this->subject)
//                    ->view('emails.V2.docket.emailDocket')
//                    ->text('emails.V2.docket.emailDocketPlain');
    }
}
