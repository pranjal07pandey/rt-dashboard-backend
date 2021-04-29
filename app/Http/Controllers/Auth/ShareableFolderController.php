<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketValue;
use App\EmailSentInvoice;
use App\EmailSentInvoiceDescription;
use App\EmailSentInvoicePaymentDetail;
use App\EmailSentInvoiceValue;
use App\Employee;
use App\Folder;
use App\Invoice;
use App\SentDcoketTimerAttachment;
use App\SentDocketRecipient;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentInvoice;
use App\SentInvoiceDescription;
use App\SentInvoicePaymentDetail;
use App\SentInvoiceValue;
use App\ShareableFolder;
use App\ShareableFolderUser;
use Carbon\Carbon;
use PDF;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Support\Collection;
use App\Http\Controllers\Controller;
use App\Helpers\V2\AmazoneBucket;
class ShareableFolderController extends Controller
{
    public function index(){

        return view('shareable-folder/shareable-folder');

    }

    public  function verifyToken(Request $request){
        $sessionData = Session::get('shareable_folder');
        $shareablefolder = ShareableFolder::where('link',$request->token)->first();
        if($shareablefolder == null){
            return response()->json(array('status' => 404));
        }else{
            if($sessionData != null ){
                if( array_key_exists("link",$sessionData) != false){
                    if($sessionData['link'] == $shareablefolder->link){
                        if($shareablefolder->shareable_type == 'Restricted'){

                            $status = false;
                            if( array_key_exists("token",$sessionData) != false){
                                foreach ($shareablefolder->shareableFolderUsers as $shareableFolderUsers){
                                    if(Session::get('shareable_folder')['token'] == $shareableFolderUsers['token']){
                                        $status = true;
                                        $url = url('/folder');
                                        $data  = "auth";
                                        return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));
                                    }
                                }
                            }

                            if($status == false){
                                Session::forget('shareable_folder');
                                $data  = "auth";
                                $url = url('/folder/'.$shareablefolder->link);
                                return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));
                            }
                        }
                        else if($shareablefolder->shareable_type == 'Public'){
                            $data  = "no-auth";
                            $session = array('link'=>$shareablefolder->link);
                            Session::put('shareable_folder',$session);
                            $url = url('/folder');
                            return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));
                        }
                        else if($shareablefolder->shareable_type == 'Disabled'){
                            return response()->json(array('status' => 404));
                        }
                        else{
                            return response()->json(array('status' => 404));
                        }
                    }else{
                        if($shareablefolder->shareable_type == 'Restricted'){

                            $data  = "auth";
                            Session::forget('shareable_folder');
                            $url = url('/folder/'.$request->token);
                            return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));

                        }
                        else if($shareablefolder->shareable_type == 'Public'){
                            Session::forget('shareable_folder');
                            $data  = "no-auth";
                            $session = array('link'=>$request->token);
                            Session::put('shareable_folder',$session);
                            $url = url('/folder');
                            return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));
                        }
                        else{
                            return response()->json(array('status' => 404));
                        }
                    }
                }else{
                    return response()->json(array('status' => 404));
                }
            }else{
                if($shareablefolder->shareable_type == 'Restricted'){

                }else if($shareablefolder->shareable_type == 'Public'){
                    $data  = "no-auth";
                    $session = array('link'=>$request->token);
                    Session::put('shareable_folder',$session);
                    $url = url('/folder');
                    return response()->json(array('status' => 200,'data'=>$data,'url'=>$url));
                }else{
                    return response()->json(array('status' => 404));
                }
            }
        }
    }


    public function folderLogin(Request $request){
        $validator  =   Validator::make($request->all(),['token'=>'required','email'=>'required|email','password'=>'required|min:6']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
                $errors[]=$messages[0];
            }
            return response()->json(array('status' => false,'message' => $errors[0]));
        else:
            $status = false;
            $shareablefolder = ShareableFolder::where('link',$request->token)->first();

            foreach ($shareablefolder->shareableFolderUsers as $shareableFolderUsers){

                if($shareableFolderUsers->email == $request->email && password_verify($request->password,$shareableFolderUsers->password) ){
                    $status = true;
                    $token = hash('sha256', str_random(10), false);
                    $shareableFolderUsers->update(['token'=>$token]);
                    $session = array('link'=>$shareablefolder->link,'token'=>$shareableFolderUsers->token);
                    Session::put('shareable_folder',$session);
                    $data = url('/folder');
                    return response()->json(array("status" => true,"data"=>$data));

                }
            }
            if($status == false){
                return response()->json(array("status" => false,"message"=>'Invalid email and password'));
            }
        endif;
    }

    public function companyDocketViewEmailed($id){
        $id =   Crypt::decrypt($id);
        $sentDocket     =   EmailSentDocket::where('id',$id)->withTrashed()->get()->first();
            $approval_type = array();
            foreach ($sentDocket->recipientInfo as $items){
                $approval_type[] = array(
                    'id' => $items->id,
                    'status' =>$items->status,
                    'approval' =>$items->approval,
                    'email' => $items->emailUserInfo->email,
                    'approval_time' =>$items->approval_time,
                    'name'=>$items->name,
                    'signature'=> AmazoneBucket::url() . $items->signature
                );
            }
            $companyName = array();
            foreach ($sentDocket->recipientInfo as $item){
                $companyName[] =   $item->receiver_company_name;

            }
            $emailUser = array();
            foreach ($sentDocket->recipientInfo as $row){
                $emailUser[] =   $row->emailUserInfo->email;

            }
            $company_name =implode(", ", $companyName);
            $employee_name =implode(", ", $emailUser);


            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
//            if($sentDocket->company_id==Session::get('company_id')){
                $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
                $distinctValue =  EmailSentDocketRecipient::where('email_sent_docket_id',$id)->distinct('receiver_company_name')->pluck('receiver_company_name')->toArray();
                return view('shareable-folder.folder.partials.view.emailedDocket',compact('sentDocket','docketFields','docketTimer','approval_type','company_name','employee_name','distinctValue'));
//            }else {
//                flash('Invalid action ! Please try with valid action.','warning');
//                return redirect()->back();
//            }


    }


    public function companyDocketView($id){
        $id =   Crypt::decrypt($id);
        $sentDocket     =   SentDockets::where('id',$id)->withTrashed()->get()->first();
            $approval_type = array();
            foreach ($sentDocket->sentDocketRecipientApproval as $items){
                $approval_type[] = array(
                    'id' => $items->id,
                    'status' =>$items->status,
                    'full_name' => $items->userInfo->first_name." ".$items->userInfo->last_name,
                    'approval_time' =>$items->approval_time,
                    'name'=>$items->name,
                    'signature'=> AmazoneBucket::url() . $items->signature
                );
            }
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',1)->get();
//            $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
//            $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
//
//            $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
//            $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
//            $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

            $sentDocketRecepients = array();

            foreach ($sentDocket->recipientInfo as $sentDocketRecepient){
                if ($sentDocketRecepient->userInfo->employeeInfo){
                    $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
                }else if ($sentDocketRecepient->userInfo->companyInfo){
                    $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
                }


                $sentDocketRecepients[]=array(
                    'name'=>$sentDocketRecepient->userInfo->first_name." ".$sentDocketRecepient->userInfo->last_name,
                    'company_name'=> $companyNameRecipent,
                );


            }

            $data= (new Collection($sentDocketRecepients))->sortBy('company_name');

            $receiverDetail = array();

            foreach ($data as $datas){

                $receiverDetail[$datas['company_name']][]= $datas['name'];


            }




//            $companyName = array();
//            foreach ($company as $row){
//                $companyName[] = $row->name;
//            }
//            $receiver_detail = array();
//            foreach ($sentDocket->recipientInfo as $row){
//                $receiver_detail[] =   $row->userInfo->first_name." ".$row->userInfo->last_name;
//
//            }
//           $company_name =implode(", ", $companyName);
//            $employee_name =implode(", ", $receiver_detail);



//            if($sentDocket->sender_company_id==Session::get('company_id')){
//                $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
//                return view('dashboard.company.docketManager.docket.view',compact('sentDocket','docketFields','docketTimer','approval_type','receiverDetail'));
//            }else{

                //get total company employee ids
//                $employeeIds    =   Employee::where('company_id',Session::get('company_id'))->pluck('user_id');
//                $employeeIds[]  =   Company::find(Session::get('company_id'))->user_id;
//
//                if(SentDocketRecipient::whereIn('user_id',$employeeIds)->where('sent_docket_id',$id)->count()>0){
                    $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                    return view('shareable-folder.folder.partials.view.docket',compact('sentDocket','docketFields','docketTimer','approval_type','receiverDetail'));
//                }else{
//                    flash('Invalid action !.','warning');
//                    return redirect()->back();
//                }
//            }
    }

    public function viewEmailedInvoice($id){
        $id =   Crypt::decrypt($id);
        $sentInvoice =   EmailSentInvoice::where('id',$id)->first();
            $data= array();
            $data["full_name"]= $sentInvoice->receiverInfo->email;
            $data["company_name"]= $sentInvoice->receiver_company_name;
            $data["address"]= $sentInvoice->receiver_company_address;
            $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$id)->get();
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

            $invoice     =     EmailSentInvoice::where('id',$id)->first();

            $companyDetails =   Company::where('id',$invoice->company_id)->first();


            $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$invoice->id)->get();

            $invoiceSetting =   array();
            //check invoice payment info
            if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->count()==1){
                $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->first();
            }

            return view('shareable-folder.folder.partials.view.emailedInvoice',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting','sentInvoice','data'));


    }
    public function companyInvoiceView($id){
        $id =   Crypt::decrypt($id);
        $sentInvoice     =   SentInvoice::findOrFail($id);
            //check is employee super admin or not
            $data= array();
            $data["full_name"]= $sentInvoice->receiverUserInfo->first_name." ".$sentInvoice->receiverUserInfo->last_name;
            $data["company_name"]= $sentInvoice->receiverCompanyInfo->name;
            $data["address"]= $sentInvoice->receiverCompanyInfo->address;

            if(Employee::where('user_id',$sentInvoice->user_id)->count()>0)
                $senderCompanyId   =   Employee::where('user_id',$sentInvoice->user_id)->first()->company_id;
            else
                $senderCompanyId    =   Company::where('user_id',$sentInvoice->user_id)->first()->id;

                $invoiceDescription     =    SentInvoiceDescription::where('sent_invoice_id',$sentInvoice->id)->get();
                $sentInvoiceValueQuery    =    SentInvoiceValue::where('sent_invoice_id',$id)->get();
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
                if(SentInvoicePaymentDetail::where('sent_invoice_id',$id)->count()==1){
                    $invoiceSetting =   SentInvoicePaymentDetail::where('sent_invoice_id',$id)->first();
                }
                $invoices   =   Invoice::where('company_id',Session::get('company_id'))->orderBy('id','desc')->get();
                return view('shareable-folder.folder.partials.view.invoice',compact('invoiceSetting','sentInvoice', 'invoiceDescription', 'sentInvoiceValue','invoices','data'));






    }
    public function emailDocketPdfDownload($id){
        $id =   Crypt::decrypt($id);
        ini_set('memory_limit','256M');
        set_time_limit(0);
        $sentDocket     =   EmailSentDocket::findOrFail($id);
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
        $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$sentDocket->id)->where('type',2)->get();
        $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
        // return view('pdfTemplate.emailedDocketForward',compact('sentDocket','docketFields','docketTimer','approval_type'));
        $isFromBackend  =   true;
        $pdf = PDF::loadView('pdfTemplate.emailedDocketForward',compact('sentDocket','docketFields','docketTimer','approval_type','isFromBackend'))->setPaper('a4','landscape')->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true,'defaultFont' => 'sans-serif','isHtml5ParserEnabled'=>true]);
        $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".$sentDocket->id);
        return $pdf->download($fileName.'.pdf');


    }
    public function docketPdfDownload($id){
        $id =   Crypt::decrypt($id);
        ini_set('max_execution_time', 300);
        $sentDocket     =   SentDockets::findOrFail($id);
        $approval_type = array();
        foreach ($sentDocket->sentDocketRecipientApproval as $items){
            $approval_type[] = array(
                'id' => $items->id,
                'status' =>$items->status,
                'full_name' => $items->userInfo->first_name." ".$items->userInfo->last_name,
                'approval_time' =>$items->approval_time,
                'name'=>$items->name,
                'signature'=> AmazoneBucket::url() . $items->signature
            );
        }

        $sentDocketRecepients = array();
        foreach ($sentDocket->recipientInfo as $sentDocketRecepient){
            $companyNameRecipent = "";
            if ($sentDocketRecepient->userInfo->employeeInfo){
                $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
            }else if ($sentDocketRecepient->userInfo->companyInfo){
                $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
            }
            $sentDocketRecepients[]=array(
                'name'=>$sentDocketRecepient->userInfo->first_name." ".$sentDocketRecepient->userInfo->last_name,
                'company_name'=> $companyNameRecipent,
            );
        }
        $data= (new Collection($sentDocketRecepients))->sortBy('company_name');
        $receiverDetail = array();
        foreach ($data as $datas){
            $receiverDetail[$datas['company_name']][]= $datas['name'];

        }
        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
        $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
        $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',1)->get();
        // return view('pdfTemplate.docketForward',compact('sentDocket','company','docketFields','docketTimer','approval_type','receiverDetail'));
        $pdf = PDF::loadView('pdfTemplate.docketForward',compact('sentDocket','company','docketFields','docketTimer','approval_type','receiverDetail'))->setPaper('a4','landscape')->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".$sentDocket->id);

        return $pdf->download("$fileName".'.pdf');

    }
    public function emailInvoicePdfDownload($id){
        $id =   Crypt::decrypt($id);
        $sentInvoice     =   EmailSentInvoice::findOrFail($id);
        $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$id)->get();
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

        $invoice     =     EmailSentInvoice::where('id',$id)->first();
        $companyDetails =   Company::where('id',$invoice->company_id)->first();
        $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$invoice->id)->get();
        $invoiceSetting =   array();
        //check invoice payment info
        if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->count()==1){
            $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$id)->first();
        }

        $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward',compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".$sentInvoice->id);
        return $pdf->download($fileName.'.pdf');


    }
    public function invoicePdfDownload($id){
        $id =   Crypt::decrypt($id);
        $sentInvoice     =   SentInvoice::findOrFail($id);
        $invoiceDescription     =    SentInvoiceDescription::where('sent_invoice_id',$sentInvoice->id)->get();
        $sentInvoiceValueQuery    =    SentInvoiceValue::where('sent_invoice_id',$id)->get();
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
        if(SentInvoicePaymentDetail::where('sent_invoice_id',$id)->count()==1){
            $invoiceSetting =   SentInvoicePaymentDetail::where('sent_invoice_id',$id)->first();
        }
        $pdf = PDF::loadView('pdfTemplate.invoiceForward',compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".$sentInvoice->id);
        return $pdf->download($fileName.'.pdf');
    }

    public function downloadPdf(Request $request){
        $folder = Folder::where('id',$request->folderId)->get()->first();
        $selectDocketIds = array();
        $selectEmailDocketIds = array();
        $selectInvoiceIds= array();
        $selectEmailInvoice = array();

        foreach ($folder->folderItems as $folderItems){
            if($folderItems->type == 1){
                //docket
                $selectDocketIds[] = $folderItems->ref_id;
            }else if($folderItems->type == 2){
                //invoice
                $selectInvoiceIds[] = $folderItems->ref_id;
            }
            else if($folderItems->type == 3){
                //emailDocket
                $selectEmailDocketIds[] = $folderItems->ref_id;
            }
            else if($folderItems->type == 4){
                //emailInvoice
                $selectEmailInvoice[] = $folderItems->ref_id;
            }
        }

        $dir =  'files/folder/pdf/'.str_replace(" ","-",Carbon::now());
        $result = File::makeDirectory($dir);

        if($selectDocketIds){
            $checkDocketId = array();
            foreach ($selectDocketIds as $selectDocketId) {
                $id_get = SentDockets::where('id', $selectDocketId)->pluck('id');
                $checkDocketId[] = $id_get;
            }
            $sentDockets = SentDockets::whereIn('id', array_unique($checkDocketId))->get();
            foreach ($sentDockets as $sentDocket) {
                $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
                $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                $pdf = PDF::loadView('pdfTemplate.docketForward',compact('sentDocket','company','docketFields'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".str_replace("-","",$sentDocket->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }


        if($selectEmailDocketIds){
            $checkDocketId = array();
            foreach ($selectEmailDocketIds as $selectDocketId) {
                $id_get = EmailSentDocket::where('id', $selectDocketId)->pluck('id');
                $checkDocketId[] = $id_get;
            }
            $sentDockets = EmailSentDocket::whereIn('id', array_unique($checkDocketId))->get();

            foreach ($sentDockets as $sentDocket) {
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
                $docketFields   =   EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
                $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$sentDocket->id)->where('type',2)->get();
                $pdf = PDF::loadView('pdfTemplate.emailedDocketForward',compact('sentDocket','docketFields','docketTimer','approval_type'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".str_replace("-","",$sentDocket->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }

        if($selectEmailInvoice){
            $checkInvoiceId = array();
            foreach ($selectEmailInvoice as $selectInvoiceId) {
                $id_get = EmailSentInvoice::where('id', $selectInvoiceId)->pluck('id');
                $checkInvoiceId[] = $id_get;
            }
            $sentInvoices = EmailSentInvoice::whereIn('id', array_unique($checkInvoiceId))->get();

            foreach ($sentInvoices as $sentInvoice) {
                $sentInvoiceValueQuery    =    EmailSentInvoiceValue::where('email_sent_invoice_id',$selectEmailInvoice)->get();
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
                $invoice     =     EmailSentInvoice::where('id',$selectEmailInvoice)->first();
                $companyDetails =   Company::where('id',$sentInvoice->company_id)->first();
                $invoiceDescription     =    EmailSentInvoiceDescription::where('email_sent_invoice_id',$sentInvoice->id)->get();
                $invoiceSetting =   array();
                //check invoice payment info
                if(EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->count()==1){
                    $invoiceSetting =   EmailSentInvoicePaymentDetail::where('email_sent_invoice_id',$sentInvoice->id)->first();
                }

                $pdf = PDF::loadView('pdfTemplate.emailedInvoiceForward',compact('sentInvoiceValue','companyDetails','sentInvoice','invoiceDescription','invoiceSetting','invoice'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".str_replace("-","",$sentInvoice->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }

        }

        if($selectInvoiceIds){

            $checkInvoiceId = array();
            foreach ($selectInvoiceIds as $selectInvoiceId) {
                $id_get = SentInvoice::where('id', $selectInvoiceId)->pluck('id');
                $checkInvoiceId[] = $id_get;
            }
            $sentInvoices = SentInvoice::whereIn('id', array_unique($checkInvoiceId))->get();
            foreach ($sentInvoices as $sentInvoice) {
                $invoiceDescription     =    SentInvoiceDescription::where('sent_invoice_id',$sentInvoice->id)->get();
                $sentInvoiceValueQuery    =    SentInvoiceValue::where('sent_invoice_id',$sentInvoice->id)->get();
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
                if(SentInvoicePaymentDetail::where('sent_invoice_id',$selectInvoiceIds)->count()==1){
                    $invoiceSetting =   SentInvoicePaymentDetail::where('sent_invoice_id',$sentInvoice->id)->first();
                }
                $pdf = PDF::loadView('pdfTemplate.invoiceForward',compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                $fileName=preg_replace('/\s+/', '',$sentInvoice->invoiceInfo->title."".str_replace("-","",$sentInvoice->formatted_id));
                $output = $pdf->output();
                $path = base_path($dir.'/'.$fileName.'.pdf');
                file_put_contents($path, $output);
            }
        }


        $files = base_path($dir.'/');
        $now = Carbon::now();
        $zipper = new \Chumper\Zipper\Zipper;
        $zipper->make('zipFile/'.$now.'/record-time-docktes.zip')->add($files)->close();
        File::deleteDirectory(base_path($dir));
        return response()->json(array("status" => true, "messages" =>$now."/record-time-folder.zip"));
    }






}
