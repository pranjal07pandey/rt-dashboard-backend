<?php

namespace App\Mail;

use App\SentDockets;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCopyDocket extends Mailable
{
    use Queueable, SerializesModels;

    public $sentDocket;
    public $subject;
    public $recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SentDockets $sentDocket, $recipient, $subject)
    {
        $this->sentDocket = $sentDocket;
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
            ->view('emails.V2.docket.sendDocketCopy')
            ->text('emails.V2.docket.sendDocketCopyPlain');

    }

}
