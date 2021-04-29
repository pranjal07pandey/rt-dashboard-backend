<?php

namespace App\Http\Controllers\Website;

use App\EmailSentInvoice;
use App\EmailSentInvoicePaymentDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use PDF;
use App\EmailSentInvoiceValue;
use App\Company;
use App\EmailSentInvoiceDescription;

class EmailInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, $recipient)
    {
        $sentInvoice    =   EmailSentInvoice::findOrFail(Crypt::decrypt($id));
        if($sentInvoice->receiver_user_id==Crypt::decrypt($recipient)){
            return view('website.emailInvoice.view',compact('sentInvoice','id','recipient'));
        }else{
            $message    =   "Invalid request. Please check your url and try again.";
            return view('website.emailDocket.approved', compact('message'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function download($id){
        ini_set('memory_limit','256M');
        set_time_limit(0);
        $id =   Crypt::decrypt($id);
        $sentInvoice    =    EmailSentInvoice::findOrFail($id);

        $document_name  = "emailed-invoice-".$id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name)).str_replace(' ', '-',Carbon::now()->toDateTimeString());

        $sentInvoiceValueQuery = EmailSentInvoiceValue::where('email_sent_invoice_id', $id)->get();
        $sentInvoiceValue = array();
        foreach ($sentInvoiceValueQuery as $row) {
            $subFiled = [];
            $sentInvoiceValue[] = array('id' => $row->id,
                'invoice_field_category_id' => $row->invoiceFieldInfo->invoice_field_category_id,
                'invoice_field_category' => $row->label,
                'label' => $row->label,
                'value' => $row->value,
                'subFiled' => $subFiled);
        }

        $invoice = EmailSentInvoice::where('id', $id)->first();
        $companyDetails = Company::where('id', $invoice->company_id)->first();
        $invoiceDescription = EmailSentInvoiceDescription::where('email_sent_invoice_id', $invoice->id)->get();
        $invoiceSetting = array();
        //check invoice payment info
        if (EmailSentInvoicePaymentDetail::where('email_sent_invoice_id', $id)->count() == 1) {
            $invoiceSetting = EmailSentInvoicePaymentDetail::where('email_sent_invoice_id', $id)->first();
        }

        $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue', 'companyDetails', 'invoice', 'invoiceDescription', 'invoiceSetting'));
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

        return $pdf->download($document_name . '.pdf');
    }
}
