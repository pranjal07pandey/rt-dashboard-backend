<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Invoice;
use App\Company;
use App\StripeCharge;
use Mail;
use PDF;
use App\Helpers\V2\AmazoneBucket;

class StripeWebhooks extends Controller
{
    public function webhooks(){
        setStripeKey();
        $endpoint_secret = 'whsec_avgoyiyMI1a8lmLLC5mNaz3D0AxyAo7j';

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400); // PHP 5.4 or greater
            exit();
        } catch(\Stripe\Error\SignatureVerification $e) {
            // Invalid signature
            http_response_code(400); // PHP 5.4 or greater
            exit();
        }
        if ($event->type == 'charge.succeeded') {
            //save stripe charge details into database
            $stripeCharge               =    new StripeCharge();
            $stripeCharge->charge_id    =   $event->data['object']->id;
            $stripeCharge->customer_id     =   $event->data['object']->customer;
            $stripeCharge->amount       =   $event->data['object']->amount;
            $stripeCharge->tax          =   10;
            $stripeCharge->created      =   $event->created;
            $stripeCharge->save();

            $stripeCustomer =   $event->data['object']->customer;
            $companyQuery   =   Company::where('stripe_user',$stripeCustomer)->get();
            $company        =   array();

            if($companyQuery->count()){
                $company    =   $companyQuery->first();
            }
            $invoiceID              =   $event->data['object']->invoice;
            $stripeInvoice          =   Invoice::retrieve($invoiceID);
            $data['stripeInvoice']  =   $stripeInvoice;

            $document_path   =   'files/pdf/stripeInvoice/'.$invoiceID.'.pdf';
            $pdf = PDF::loadView('pdfTemplate.stripeInvoice',compact('stripeInvoice','company'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
            $output = $pdf->output();
            $path = storage_path($document_path);
            file_put_contents($path, $output);

            $data['downloadLink']   =   AmazoneBucket::url() . 'storage/'.$document_path;
            $data['company']        =   $company;
            Mail::send('emails.invoice', $data, function ($message) use ($company) {
                $message->from("info@recordtimeapp.com.au", "Record Time");
                $message->to($company->userInfo->email)->subject("Invoice");
                $message->replyTo("info@recordtime.com.au","Record Time");
            });
            if($company->id=="258"){
                Mail::send('emails.invoice', $data, function ($message) use ($company) {
                    $message->from("info@recordtimeapp.com.au", "Record Time");
                    $message->to("robrien@alexander.com.au")->subject("Invoice");
                    $message->replyTo("info@recordtime.com.au","Record Time");
                });
            }
            http_response_code(200); // PHP 5.4 or greater
        }
    }
}