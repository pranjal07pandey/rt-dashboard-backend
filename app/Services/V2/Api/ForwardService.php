<?php
namespace App\Services\V2\Api;

use Carbon\Carbon;
use App\Support\Collection;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Crypt;
use App\Services\V2\ConstructorService;
use App\Helpers\V2\AmazoneBucket;
class ForwardService extends ConstructorService {

    public function forwardDocketById($request,$id){
        $sentDocket     =  $this->sentDocketsRepository->getDataWhere([['id',$id]])->with('recipientInfo','senderCompanyInfo')->first();
        if($sentDocket != null):
            //check docket associated with user or not
            $companyId  =    auth()->user()->companyInfo->id;
            if($companyId == $sentDocket->sender_company_id || $companyId == $sentDocket->company_id){
                $document_name  =  "docket-".$id."-".preg_replace('/[^A-Za-z0-9\-]/', '',str_replace(' ', '-', strtolower($sentDocket->senderCompanyInfo->name)));
                $document_path   =   'files/pdf/docketForward/'.str_replace('.', '',$document_name).'.pdf';
                if(!AmazoneBucket::fileExist($document_path)){
                    $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
                    $companyEmployeeQuery   =  $this->employeeRepository->getDataWhereIn('user_id',$recipientIds)->pluck('company_id');
                    $empCompany    =  $this->companyRepository->getDataWhereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                    $adminCompanyQuery   =  $this->companyRepository->getDataWhereIn('user_id',$recipientIds)->pluck('id')->toArray();
                    $company    =   $this->companyRepository->getDataWhereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                    $docketFields   =  $this->sentDocketsValueRepository->getDataWhere([['sent_docket_id',$sentDocket->id]])
                                            ->with('sentDocket','sentDocketUnitRateValue.docketUnitRateInfo','sentDocketTallyableUnitRateValue.docketUnitRateInfo',
                                                'sentDocketManualTimer','sentDocketImageValue','sentDocketAttachment','SentDocValYesNoValueInfo.YesNoDocketsField',
                                                'sentDocketFieldGridLabels.docketFieldGrid')->get();
                    if($sentDocket->theme_document_id == 0){
                        $pdf = PDF::loadView('pdfTemplate.docketForward', compact('sentDocket','docketFields'));
                    }else{
                        $theme = $this->documentThemeRepository->getDataWhere([['id', $sentDocket->theme_document_id]])->first();
                        $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('sentDocket','company','docketFields'));
                    }
                    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                    $output = $pdf->output();
                    $path = storage_path($document_path);
                    file_put_contents($path, $output);
                }
                return response()->json(['emailDocket' => array('fileName'=>str_replace('.', '',$document_name).'.pdf', 'filePath' => asset('storage/'.$document_path),'subject' => "Docket ".$id." ". $sentDocket->senderCompanyInfo->name)],200);
            }
            else {
                return response()->json(['message' => 'Not authorized.'],500);
            }
        else:
            return response()->json(['message' => 'Docket not found.'],500);
        endif;
    }

    public function forwardInvoiceById($request,$id){
        $sentInvoice     =  $this->sentInvoiceRepository->getDataWhere([['id',$id]])->with('senderCompanyInfo')->first();
        $companyId  =    auth()->user()->companyInfo->id;

        if($sentInvoice->company_id == $companyId || $sentInvoice->receiver_company_id == $companyId){
            $invoiceDescription     =  $this->sentInvoiceDescriptionRepository->getDataWhere([['sent_invoice_id',$sentInvoice->id]])->get();
            $sentInvoiceValueQuery    =  $this->sentInvoiceValueRepository->getDataWhere([['sent_invoice_id',$id]])->get();
            $sentInvoiceValue    = array();
            foreach ($sentInvoiceValueQuery as $row){
                $subFiled   =   [];
                $sentInvoiceValue[]    =     array('id' => $row->id,
                    'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                    'invoice_field_category' =>  $row->label,
                    'label' => $row->label,
                    'value' => $row->value,
                    'subFiled' => $subFiled);
            }

            $invoiceSetting =   array();
            //check invoice payment info
            $sentInvoicePaymentDetailData = $this->sentInvoicePaymentDetailRepository->getDataWhere([['sent_invoice_id',$id]]);
            if($sentInvoicePaymentDetailData->count() == 1){
                $invoiceSetting =   $sentInvoicePaymentDetailData->first();
            }

            $invoice_name  =  "invoice-".$id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
            $document_path   =   'files/pdf/invoiceForward/'.str_replace('.', '',$invoice_name).'.pdf';
            if(!AmazoneBucket::fileExist($document_path)){
                if($sentInvoice->theme_document_id == 0){
                    $pdf = PDF::loadView('pdfTemplate.invoiceForward', compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                }else{
                    $theme = $this->documentThemeRepository->getDataWhere([['id', $sentInvoice->theme_document_id]])->first();
                    $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                }
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(['emailInvoice' => array('fileName'=>str_replace('.', '',$invoice_name).'.pdf','filePath' => asset('storage/'.$document_path),'subject' => "Invoice ".$id." ".$sentInvoice->senderCompanyInfo->name)],200);

        }else {
            return response()->json(['message' => 'Invalid attempt ! Please try with valid action.'],500);
        }
    }

    public function forwardEmailDocketById($request,$id){
        $sentDocket     =  $this->emailSentDocketRepository->getDataWhere([['id',$id]])->with('senderCompanyInfo')->first();
        $companyId  =    auth()->user()->companyInfo->id;
        if($sentDocket->company_id==$companyId){
            $docketTimer = $this->sentDocketTimerAttachmentRepository->getDataWhere([['sent_docket_id',$sentDocket->id],['type',2]])->get();
            $docketFields   = $this->emailSentDocketValueRepository->getDataWhere([['email_sent_docket_id',$sentDocket->id]])->get();
            $document_name  =  Crypt::encryptString("emailed-docket-".$id."-".str_replace(' ', '-', strtolower($sentDocket->senderCompanyInfo->name)));
            $document_path   =   'files/pdf/emailedDocketForward/'.str_replace('.', '',$document_name).'.pdf';
            if(!AmazoneBucket::fileExist($document_path)){
                $approval_type = array();
                foreach ($sentDocket->recipientInfo as $items){
                    $approval_type[] = array(
                        'id' => $items->id,
                        'status' =>$items->status,
                        'email' => $items->emailUserInfo->email,
                        'approval_time' =>$items->approval_time,
                        'name'=>$items->name,
                        'signature'=> AmazoneBucket::url() . $items->signature
                    );
                }
                $isFromBackend  =   false;
                $pdf = PDF::loadView('pdfTemplate.emailedDocketForward', compact('sentDocket','docketFields','docketTimer','approval_type','isFromBackend'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(['emailDocket' => array('fileName'=>str_replace('.', '',$document_name).'.pdf','filePath' => (\Config::get('app.storage_url_pdf').$document_path),'subject' => "Emailed Docket ".$id." ".$sentDocket->senderCompanyInfo->name)],200);
        }else {
            return response()->json(['message' => 'Invalid attempt ! Please try with valid action.'],500);
        }
    }

    public function forwardEmailInvoiceById($request,$id){
        $sentInvoice    =  $this->emailSentInvoiceRepository->getDataWhere([['id',$id]])->with('senderCompanyInfo')->first();
        $companyId  =    auth()->user()->companyInfo->id;
        if($sentInvoice->company_id == $companyId){
            $document_name  = "emailed-invoice-".$id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
            $document_path   =   'files/pdf/emailedInvoiceForward/'.str_replace('.', '',$document_name).'.pdf';
            if(!AmazoneBucket::fileExist($document_path)){
                $sentInvoiceValueQuery    =   $this->emailSentInvoiceValueRepository->getDataWhere([['email_sent_invoice_id',$id]])->get();
                $sentInvoiceValue    = array();
                foreach ($sentInvoiceValueQuery as $row){
                    $subFiled   =   [];
                    $sentInvoiceValue[]    =     array('id' => $row->id,
                        'invoice_field_category_id'  =>  $row->invoiceFieldInfo->invoice_field_category_id,
                        'invoice_field_category' =>  $row->label,
                        'label' => $row->label,
                        'value' => $row->value,
                        'subFiled' => $subFiled);
                }

                $invoice     =   $this->emailSentInvoiceRepository->getDataWhere([['id',$id]])->first();
                $companyDetails = $this->companyRepository->getDataWhere([['id',$invoice->company_id]])->first();
                $invoiceDescription     = $this->emailSentInvoiceDescriptionRepository->getDataWhere([['email_sent_invoice_id',$invoice->id]])->get();
                $invoiceSetting =   array();
                //check invoice payment info
                $emailSentInvoicePaymentDetailData = $this->emailSentInvoicePaymentDetailRepository->getDataWhere([['email_sent_invoice_id',$id]]);
                if($emailSentInvoicePaymentDetailData->count()==1){
                    $invoiceSetting =   $emailSentInvoicePaymentDetailData->first();
                }

                if($sentInvoice->theme_document_id == 0){
                    $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                }else{
                    $theme = $this->documentThemeRepository->getDataWhere([['id', $sentInvoice->theme_document_id]])->first();
                    $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                }

                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(['emailInvoice' => array('fileName'=>str_replace('.', '',$document_name).'.pdf','filePath' => (\Config::get('app.storage_url_pdf').$document_path),'subject' => "Emailed Invoice ".$id." ".$sentInvoice->senderCompanyInfo->name)],200);
        }else {
            return response()->json(['message' => 'Invalid attempt ! Please try with valid action.'],500);
        }
    }
}