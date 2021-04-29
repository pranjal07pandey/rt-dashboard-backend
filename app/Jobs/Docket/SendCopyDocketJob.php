<?php

namespace App\Jobs\Docket;

use App\Mail\SendCopyDocket;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendCopyDocketJob implements ShouldQueue
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


         Mail::to($this->jobData['sendDocketCopy']['email'])->send(new SendCopyDocket($this->jobData['sentDocket'],$this->jobData['sendDocketCopy'],$this->jobData['emailSubject']));
    }
}
