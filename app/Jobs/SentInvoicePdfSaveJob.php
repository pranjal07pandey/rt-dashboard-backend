<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use PDF;
use App\Repositories\V2\CompanyRepository;
use App\Repositories\V2\EmailSentInvoiceDescriptionRepository;
use App\Repositories\V2\EmailSentInvoicePaymentDetailRepository;

class SentInvoicePdfSaveJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $sendInvoiceData;
    public function __construct($sendInvoiceData)
    {
        $this->sendInvoiceData = $sendInvoiceData;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companyRepository = new CompanyRepository();
        $emailSentInvoiceDescriptionRepository = new EmailSentInvoiceDescriptionRepository();
        $emailSentInvoicePaymentDetailRepository = new EmailSentInvoicePaymentDetailRepository();

        $sentInvoiceValue = $this->sendInvoiceData['sentInvoiceValue'];
        $invoice     =     $this->sendInvoiceData['sentInvoice'];
        $companyDetails =  $companyRepository->getDataWhere([['id',$invoice->company_id]])->first();
        $invoiceDescription     =  $emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->get();
        $invoiceSetting =   array();
        //check invoice payment info
        $emailSentInvoicePaymentDetailDoc = $emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->first();
        if($emailSentInvoicePaymentDetailDoc != null){
            $invoiceSetting =  $emailSentInvoicePaymentDetailDoc;
        }

        $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

        $output = $pdf->output();
        $path = storage_path($this->sendInvoiceData['document_path']);
        file_put_contents($path, $output);
    }
}
