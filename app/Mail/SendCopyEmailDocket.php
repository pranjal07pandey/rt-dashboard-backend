<?php

namespace App\Mail;

use App\EmailSentDocket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCopyEmailDocket extends Mailable
{
    use Queueable, SerializesModels;

    public $emailDocket;
    public $subject;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailSentDocket $emailDocket, $recipient, $subject)
    {
        $this->emailDocket = $emailDocket;
        $this->subject    = $subject;
        $this->recipient  = $recipient;
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
            ->view('emails.v2.docket.sendEmailDocketCopy')
            ->text('emails.v2.docket.sendEmailDocketCopyPlain');
    }

}
