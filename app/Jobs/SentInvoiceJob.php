<?php

namespace App\Jobs;

use App\Mail\SentInvoiceMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SentInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $mailData;
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email = new SentInvoiceMail();
        Mail::to($this->mailData['sentInvoice']->receiverInfo->email)->send($email->subject($this->mailData['subject'])->with([
                                                                        'emailInvoice' => $this->mailData['sentInvoice'],
                                                                        'recipient' => $this->mailData['receiverInfo'],
                                                                    ]));
    }
}
