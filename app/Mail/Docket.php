<?php

namespace App\Mail;

use App\SentDockets;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Docket extends Mailable
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
    public function __construct(SentDockets $sentDocket, User $recipient, $subject)
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
            ->view('emails.v2.docket.docket')
            ->text('emails.v2.docket.docketPlain');

//        return $this->from('info@recordtimeapp.com.au',$this->sentDocket->sender_name. " via Record Time")
//            ->replyTo($this->sentDocket->senderUserInfo->email, $this->sentDocket->sender_name)
//            ->subject($this->subject)
//            ->view('emails.v2.docket.docket')
//            ->text('emails.v2.docket.docketPlain');
    }
}
