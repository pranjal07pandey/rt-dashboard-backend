<?php

namespace App\Jobs\EmailDocket;

use App\Mail\EmailDocket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SentEmailDocketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobData)
    {
      $this->jobData = $jobData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
     Mail::to($this->jobData['receiverInfo']->emailUserInfo->email)->send(new EmailDocket($this->jobData['sentDocket'],$this->jobData['receiverInfo'],$this->jobData['emailSubject']));
    }
}
