<?php

namespace App\Http\Controllers\Invoice;

use App\EmailSentInvoice;
use App\EmailSentInvoiceLabel;
use App\SentInvoice;
use App\SentInvoiceLabel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class LabelController extends Controller
{
    public function assign(Request $request){
        //old function "FolderController"->folderInvoiceLabelSave

        if($request->value == null){
            return response()->json(['status'=>false,'message'=>'Invalid action ! Please select Docket Label.']);
        }else{
            if($request->type == 3){
                $sentInvoice     =   SentInvoice::where('id',$request->id)->get();
                if($sentInvoice->count()==0){
                    return response()->json(['status'=>false,'message'=>'Invalid action ! Invoice not found.']);
                }
                $sentInvoice =   $sentInvoice->first();

                $sentInvoiceLabels = array();
                foreach ($request->value as $invoiceLabel) {
                    if(SentInvoiceLabel::where('sent_invoice_id',$sentInvoice->id)->where('invoice_label_id',$invoiceLabel)->count()==0) {
                        $sentInvoiceLabel = new SentInvoiceLabel();
                        $sentInvoiceLabel->sent_invoice_id = $sentInvoice->id;
                        $sentInvoiceLabel->invoice_label_id = $invoiceLabel;
                        $sentInvoiceLabel->save();
                        $sentInvoiceLabels[] = $sentInvoiceLabel;
                    }
                }
                $type   =    $request->type;
                $html   =  view('dashboard.company.invoiceManager.partials.table-view.invoice-label', compact('sentInvoiceLabels','type'))->render();
                return response()->json(['status'=>true,'message'=>'Invoice label attached successfully','html'=> $html,'id'=>"invoiceLabelIdentify".$sentInvoice->id]);
            }else if($request->type == 4){
                $emailSentInvoice     =   EmailSentInvoice::where('id',$request->id)->get();
                if($emailSentInvoice->count()==0){
                    return response()->json(['status'=>false,'message'=>'Invalid action ! Invoice not found.']);
                }
                $emailSentInvoice =   $emailSentInvoice->first();

                $sentInvoiceLabels = array();
                foreach ($request->value as $invoiceLabel) {
                    if(EmailSentInvoiceLabel::where('email_sent_id',$emailSentInvoice->id)->where('invoice_label_id',$invoiceLabel)->count()==0) {
                        $emailSentInvoiceLabel = new EmailSentInvoiceLabel();
                        $emailSentInvoiceLabel->email_sent_id = $emailSentInvoice->id;
                        $emailSentInvoiceLabel->invoice_label_id = $invoiceLabel;
                        $emailSentInvoiceLabel->save();
                        $sentInvoiceLabels[] = $emailSentInvoiceLabel;
                    }
                }
                $type   =    $request->type;
                $html   =  view('dashboard.company.invoiceManager.partials.table-view.invoice-label', compact('sentInvoiceLabels','type'))->render();
                return response()->json(['status'=>true,'message'=>'Email Sent Invoice label attached successfully','html'=> $html,'id'=>"emailInvoiceLabelIdentify".$emailSentInvoice->id]);
            }
        }
    }

    public function delete(Request $request){
        if($request->type == 3){
            $sentInvoiceLabel    =   SentInvoiceLabel::where('id', $request->id)->get();
            if($sentInvoiceLabel->count()==0){
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentInvoiceLabel    =   $sentInvoiceLabel->first();
            if($sentInvoiceLabel->invoiceLabel->company_id!=Session::get('company_id')) {
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $sentInvoiceLabel->delete();
            return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=> $request->id ]);
        }else if ($request->type == 4){
            $emailSentInvoiceLabel    =   EmailSentInvoiceLabel::where('id', $request->id)->get();
            if($emailSentInvoiceLabel->count()==0){
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $emailSentInvoiceLabel    =   $emailSentInvoiceLabel->first();
            if($emailSentInvoiceLabel->invoiceLabel->company_id!=Session::get('company_id')) {
                return response()->json(['status' => false, 'message' => 'Invalid action ! Please try with valid action.']);
            }
            $emailSentInvoiceLabel->delete();
            return response()->json(['status'=>true,'message'=>'Assigned label deleted successfully.', 'id'=> $request->id ]);
        }
    }
}
