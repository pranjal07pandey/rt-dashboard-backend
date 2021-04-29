<?php

namespace App\Http\Controllers;

use App\AppInfo;
use App\AppleSubscription;
use App\AssignedDocket;
use App\AssignedInvoice;
use App\Client;
use App\Company;
use App\CompanyXero;
use App\Docket;
use App\DocketAttachments;
use App\DocketDraft;
use App\DocketField;
use App\DocketFieldFooter;
use App\DocketFieldGrid;
use App\DocketFieldGridValue;
use App\DocketFieldNumber;
use App\DocketFiledPreFiller;
use App\DocketGridAutoPrefiller;
use App\DocketGridPrefiller;
use App\DocketManualTimer;
use App\DocketManualTimerBreak;
use App\DocketPrefiller;
use App\DocketPrefillerValue;
use App\DocketProject;
use App\DocketTallyableUnitRate;
use App\DocketTimesheet;
use App\DocketUnitRate;
use App\Email_Client;
use App\EmailSentDocket;
use App\EmailSentDocketImageValue;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketTallyUnitRateVal;
use App\EmailSentDocketValue;
use App\EmailSentDocManualTimer;
use App\EmailSentDocManualTimerBrk;
use App\EmailSentInvoice;
use App\EmailSentInvoiceDescription;
use App\EmailSentInvoiceImage;
use App\EmailSentInvoicePaymentDetail;
use App\EmailSentInvoiceValue;
use App\EmailSnetDocketUnitRateValue;
use App\EmailUser;
use App\Employee;
use App\Machine;
use App\ExportMapping;
use App\Folder;
use App\FolderItem;
use App\Invoice;
use App\InvoiceField;
use App\InvoiceSetting;
use App\InvoiceXeroSetting;
use App\DocketDocument;
use App\Invoice_Label;
use App\Jobs\Docket\SendCopyDocketJob;
use App\Jobs\EmailDocket\SendCopyEmailDocketJob;
use App\Jobs\EmailDocket\SentEmailDocketJob;
use App\Jobs\SentDocketEmail;
use App\LinkGridPrefillerFilter;
use App\LinkPrefillerFilter;
use App\Mail\EmailDocket;
use App\Mail\SendCopyDocket;
use App\Mail\SendCopyEmailDocket;
use App\Messages;
use App\MessagesGroup;
use App\MessagesGroupUser;
use App\MessagesRecipients;
use App\Notifications\AppOpen;
use App\Notifications\SentDocketNotification;
use App\Project;
use App\SendDocketImageValue;
use App\SentDcoketTimerAttachment;
use App\SentDocketAttachment;
use App\SentDocketInvoice;
use App\SentDocketInvoiceDetail;
use App\SentDocketManualTimer;
use App\SentDocketManualTimerBreak;
use App\SentDocketProject;
use App\SentDocketRecipient;
use App\SentDocketRecipientApproval;
use App\SentDocketReject;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentDocketTallyUnitRateVal;
use App\SentDocketTimesheet;
use App\SentDocketUnitRateValue;
use App\SentDocValYesNoValue;
use App\SentEInvoiceAttachedEDocket;
use App\SentEmailAttachment;
use App\SentEmailDocketInvoice;
use App\SentEmailDocValYesNoValue;
use App\SentEmailInvoiceXero;
use App\SentInvoice;
use App\SentInvoiceAttachedDocket;
use App\SentInvoiceDescription;
use App\SentInvoiceImageValue;
use App\SentInvoicePaymentDetail;
use App\SentInvoiceValue;
use App\SentInvoiceXero;
use App\SentXeroEmailInvoiceSetting;
use App\SentXeroInvoiceSetting;
use App\Support\Collection;
use App\SynXeroContact;
use App\TemplateAssignFolder;
use App\TimerAttachedTag;
use App\TimerClient;
use App\TimerComment;
use App\UserNotification;
use App\XeroField;
use App\YesNoDocketsField;
use App\YesNoFields;
use Aws\S3\S3Client;
use Carbon\CarbonPeriod;
use DateInterval;
use DOMDocument;
use function GuzzleHttp\Promise\queue;
use http\Header;
use http\Message;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Timer;
use App\TimerLog;
use SimpleXMLElement;
use Spatie\DbDumper\Compressors\GzipCompressor;
use Spatie\DbDumper\Databases\MySql;
use Symfony\Component\Process\Process;
use Validator;
use overint\MailgunValidator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\SubscriptionLog;
use Illuminate\Support\Facades\Crypt;
use App\Events\TimerChanged;
use App\DocumentTheme;
use App\TimerSetting;
use App\TimerImage;
use App\DocketFieldGridLabel;
use XeroPHP\Application\PrivateApplication;
use SoapBox\Formatter\Formatter;
use ReceiptValidator\iTunes\Validator as iTunesValidator;
use App\Helpers\V2\FunctionUtils;
use Image;
use App\Helpers\V2\AmazoneBucket;

class APIController extends Controller
{


    function testbuildAutoPrefillerTreeArray(array $prefiller, $parentId = 0){


        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->testbuildAutoPrefillerTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;

                }else{
                    $prefillers['prefiller'] =[];
                }
                if($prefillers['root_id']==0){
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>$prefillers['docket_field_grid_id'],'prefiller'=>$prefillers['prefiller']);
                }else{

                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>@DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->get()->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);

                }
            }
        }
        return $branch;
    }
    public function sentAcivity(Request $request){
        $sendDocket = SentDockets::where('user_id',$request->header('userId'))->count();
        $sendInvoice = SentInvoice::where('user_id',$request->header('userId'))->count();
        $emailDockets = EmailSentDocket::where('user_id',$request->header('userId'))->count();
        return response()->json(array("status" => true,"acivity"=>array('dockets' => $sendDocket,'invoice'=> $sendInvoice,'email_docket'=>$emailDockets)));

    }

    public function changePassword(Request $request){
        $validator  =   Validator::make(Input::all(),['oldPassword' =>     'required','newPassword'  =>  'required', 'retypeNewPassword' => 'required|same:newPassword']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:

            if (Hash::check(Input::get('oldPassword'), User::where('id',$request->header('userId'))->first()->password)) {
                User::where('id',$request->header('userId'))->update(array('password'=>Hash::make($request['newPassword'])));
                return response()->json(array("status" => true,"message"=>'Password Changed Successfully'));
            }else{
                return response()->json(array("status" => false,"message"=>'Invalid  password'));
            }
        endif;

    }

    public function profileUpdate(Request $request){

        $validator  =   Validator::make(Input::all(),['image' =>     'required | mimes:jpeg,jpg,png,HEIC']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $profile              =   Input::file('image');
            if($request->hasFile('image')) {
                if ($profile->isValid()) {
                    // $ext = $profile->getClientOriginalExtension();
                    // $filename = basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext;
                    $dest = 'files/profile';
                    // $profile->move($dest, $filename);
                    // $path = $dest . '/' . $filename;

                    $path = FunctionUtils::imageUpload($dest,$profile);
                    if (User::where('id', $request->header('userId'))->update(['image' => $path])):
                        return response()->json(array("status" => true, "message" => 'Profile Picture Update Successfully.','profile' => AmazoneBucket::url() . $path));
                    endif;
                }
            }
            else
                return response()->json(array("status" => false,"message"=>'Profile Picture Update Fails.'));
        endif;
    }

    public function nameUpdate(Request $request){
        $validator  =   Validator::make(Input::all(),['first_name' =>     'required','last_name' =>     'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            if (User::where('id', $request->header('userId'))->update(['first_name' => $request->first_name,'last_name' => $request->last_name])):
                return response()->json(array("status" => true, "message" => 'Name updated successfully..','first_name' => $request->first_name,'last_name'=>$request->last_name));
            endif;
        endif;
    }



    public function getEmployeeList(Request $request){
        $companySuperadmin  =   Company::where('id', $request->header('companyId'))->first();

        $added_company_idQuery          =   Client::where("company_id",$request->header('companyId'))->orWhere('requested_company_id',$request->header('companyId'))->get();
        $added_company_id               =   array();
        $employee                       =   array();

        foreach ($added_company_idQuery as $row){
            if($row->company_id==$request->header('companyId')){
                $added_company_id[] =   $row->requested_company_id;
            }else {
                $added_company_id[] =   $row->company_id;
            }
        }
        if (Company::where('id',$request->header('companyId'))->first()->user_id == $request->header('userId')){
            if ($companySuperadmin->docket_client ==1) {
                $addCompanySuperadmin = Company::whereIn('id', $added_company_id)->get();
                foreach ($addCompanySuperadmin as $row) {
                    if ($row->userInfo->isActive == 1) {
                        $employee[] = array('id' => 0,
                            'user_id' => $row->user_id,
                            'company_id' => $row->id,
                            'company_name' => $row->name,
                            'company_abn' => $row->abn,
                            'company_address' => $row->address,
                            'first_name' => $row->userInfo->first_name,
                            'last_name' => $row->userInfo->last_name,
                            'image' => (AmazoneBucket::fileExist(@$row->userInfo->image)) ? AmazoneBucket::url() . $row->userInfo->image : asset('assets/dashboard/images/logoAvatar.png'),

                        );
                    }
                }

                $employeeQuery = Employee::whereIn('company_id', $added_company_id)->get();
                foreach ($employeeQuery as $row) {
                    if ($row->userInfo->isActive == 1) {
                        if ($row->employed == 1) {
                            $employee[] = array('id' => $row->id,
                                'user_id' => $row->user_id,
                                'company_id' => $row->company_id,
                                'company_name' => $row->companyInfo->name,
                                'company_abn' => $row->companyInfo->abn,
                                'first_name' => $row->userInfo->first_name,
                                'last_name' => $row->userInfo->last_name,
                                'image' => (AmazoneBucket::fileExist(@$row->userInfo->image)) ? AmazoneBucket::url() . $row->userInfo->image : asset('assets/dashboard/images/logoAvatar.png'),

                            );
                        }
                    }
                }
            }
        }else{
            if (Employee::where('user_id',$request->header('userId'))->first()->docket_client ==1){

                $addCompanySuperadmin = Company::whereIn('id',$added_company_id)->get();
                foreach ($addCompanySuperadmin as $row){
                    if($row->userInfo->isActive==1) {
                        $employee[] = array('id' => 0,
                            'user_id' => $row->user_id,
                            'company_id' => $row->id,
                            'company_name' => $row->name,
                            'company_abn' => $row->abn,
                            'first_name' => $row->userInfo->first_name,
                            'last_name' => $row->userInfo->last_name,
                            'image'=>(AmazoneBucket::fileExist(@$row->userInfo->image))?AmazoneBucket::url() . $row->userInfo->image:asset('assets/dashboard/images/logoAvatar.png'),

                        );
                    }
                }

                $employeeQuery  =   Employee::whereIn('company_id',$added_company_id)->get();
                foreach ($employeeQuery as $row){
                    if($row->userInfo->isActive==1) {
                        if($row->employed==1) {
                            $employee[] = array('id' => $row->id,
                                'user_id' => $row->user_id,
                                'company_id' => $row->company_id,
                                'company_name' => $row->companyInfo->name,
                                'company_abn' => $row->companyInfo->abn,
                                'first_name' => $row->userInfo->first_name,
                                'last_name' => $row->userInfo->last_name,
                                'image'=>(AmazoneBucket::fileExist(@$row->userInfo->image))?AmazoneBucket::url() . $row->userInfo->image:asset('assets/dashboard/images/logoAvatar.png'),

                            );
                        }
                    }
                }


            }
        }

        //company superadmin and company employee
        if($companySuperadmin->can_self_docket==1){
            if($request->header('userId')==$companySuperadmin->user_id){
                $employee[] =   array('id' => 0,
                    'user_id'   =>  $companySuperadmin->user_id,
                    'company_id'    =>  $companySuperadmin->id,
                    'company_name'  =>  $companySuperadmin->name,
                    'company_abn'  =>  $companySuperadmin->abn,
                    'first_name'    =>  $companySuperadmin->userInfo->first_name,
                    'last_name'     =>  $companySuperadmin->userInfo->last_name,
                    'image'=>(AmazoneBucket::fileExist(@$companySuperadmin->userInfo->image))?AmazoneBucket::url() . $companySuperadmin->userInfo->image:asset('assets/dashboard/images/logoAvatar.png'),
                );
            }
        }
        if($companySuperadmin->appear_on_recipient==1){
            if($request->header('userId')!=$companySuperadmin->user_id){
                $employee[] =   array('id' => 0,
                    'user_id'   =>  $companySuperadmin->user_id,
                    'company_id'    =>  $companySuperadmin->id,
                    'company_name'  =>  $companySuperadmin->name,
                    'company_abn'  =>  $companySuperadmin->abn,
                    'first_name'    =>  $companySuperadmin->userInfo->first_name,
                    'last_name'     =>  $companySuperadmin->userInfo->last_name,
                    'image'=>(AmazoneBucket::fileExist(@$companySuperadmin->userInfo->image))?AmazoneBucket::url() . $companySuperadmin->userInfo->image:asset('assets/dashboard/images/logoAvatar.png'),
                );
            }
        }

        $employeeQuery  =   Employee::where('company_id',$request->header('companyId'))->get();
        foreach ($employeeQuery as $row){
            if(@$row->userInfo->isActive==1) {
                if($row->employed==1) {
                    if ($row->can_self_docket == 1) {
                        if ($request->header('userId') == $row->user_id) {
                            $employee[] = array('id' => $row->id,
                                'user_id' => $row->user_id,
                                'company_id' => $row->company_id,
                                'company_name' => $row->companyInfo->name,
                                'company_abn' => $row->companyInfo->abn,
                                'first_name' => $row->userInfo->first_name,
                                'last_name' => $row->userInfo->last_name,
                                'image' => (AmazoneBucket::fileExist(@$row->userInfo->image)) ? AmazoneBucket::url() . $row->userInfo->image : asset('assets/dashboard/images/logoAvatar.png'),
                            );
                        }
                    }

                    if($row->appear_on_recipient==1){
                        if ($request->header('userId') != $row->user_id) {
                            $employee[] =   array('id'  =>  $row->id,
                                'user_id'   =>  $row->user_id,
                                'company_id'    =>  $row->company_id,
                                'company_name'  =>  $row->companyInfo->name,
                                'company_abn'  =>  $row->companyInfo->abn,
                                'first_name'    =>  $row->userInfo->first_name,
                                'last_name'     =>  $row->userInfo->last_name,
                                'image'=>(AmazoneBucket::fileExist(@$row->userInfo->image))? AmazoneBucket::url() . $row->userInfo->image:asset('assets/dashboard/images/logoAvatar.png'),
                            );
                        }
                    }
                }
            }
        }
        return response()->json(array('status' => true, 'employee' => $employee));
    }



    public function getFrequency(Request $request){
        $docketTemplate     =    Docket::select('id','title','docket_frequency_slug','created_at','how_many','start_date')->where('company_id',$request->header('companyId'))->orderBy('id','desc')->get(2);
        $docketTemplates   =   array();

        foreach ($docketTemplate as $row ){
         $date =Carbon::parse($row->start_date)->addDay($row->docket_frequency_slug)->toDateString();
//         $sendDocket =SentDockets::where('docket_id', $row->id)->where('user_id', $request->header('userId'))->whereDate('created_at',$date)->count() > 0;
//         dd($sendDocket);

                if (SentDockets::where('docket_id', $row->id)->where('user_id', $request->header('userId'))->whereDate('created_at',$date)->count() ==0) {
                    $docketTemplates[] = array('id' => $row->id,
                        'title' => $row->title,
                    );
                }

//            if (SentDockets::where('docket_id', $row->id)->where('user_id', $request->header('userId'))->whereDate('created_at','=<',$date)->count() == 0) {
//                $docketTemplates[] = array('id' => $row->id,
//                    'title' => $row->title,
//                );
//            }


        }


        return response()->json(array('status' => true, 'docketTemplate' => $docketTemplates));
    }






        function buildTreeArray(array $prefiller, $parentId = 0) {
            $branch = array();
            foreach ($prefiller as $prefillers) {
                if ($prefillers['root_id'] == $parentId) {
                    $children = $this->buildTreeArray($prefiller, $prefillers['id']);
                    if ($children) {
                        $prefillers['prefiller'] = $children;
                    }else{
                        $prefillers['prefiller'] =[];
                    }
                    $branch[] = $prefillers;
                }
            }
            return $branch;
        }







    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */



    public function saveSentDefaultDocket(Request $request){


            //check if subscription was free count remaining docket left
            $company = Company::where('id', $request->header('companyId'))->first();
            if ($company->trial_period == 3) {
                //get last subscription created date
                $subscriptionLogQuery = SubscriptionLog::where('company_id', $company->id);
                if ($subscriptionLogQuery->count() > 0) {
                    $lastUpdatedSubscription = $subscriptionLogQuery->orderBy('id', 'desc')->first();
                    $monthDay = Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
                    $now = Carbon::now();
                    $currentMonthStart = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay);
                    $currentMonthEnd = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay)->addDay(30);
                } else {
                    $currentMonthStart = new Carbon('first day of this month');
                    $currentMonthEnd = new Carbon('last day of this month');
                }
                $sentDockets = SentDockets::where('sender_company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
                $emailDockets = EmailSentDocket::where('company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();

                $totalMonthDockets = $sentDockets + $emailDockets;

                if ($totalMonthDockets >= 5) {
                    return response()->json(array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 5 dockets per month.'));
                }
            }
            $validator = Validator::make(Input::all(), ['docket_id' => 'required', 'emailTemplateFlag' => 'required']);
            if ($validator->fails()):
                foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                    $errors[] = $messages[0];
                }
                return response()->json(array('status' => false, 'message' => $errors));
            else:
                $docketFieldsQuery = DocketField::where('docket_id', $request->docket_id)->orderBy('order', 'asc')->get();
                foreach ($docketFieldsQuery as $row) {
                    if ($row->required) {
                        if ($row->docket_field_category_id == 5) {
                            if (!Input::get('formFieldImage' . $row->id . 'count')) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }
                        if ($row->docket_field_category_id == 9) {
                            if (!Input::get('formFieldSignature' . $row->id . 'count')) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }
                        if ($row->docket_field_category_id == 14) {
                            if (!Input::get('formFieldSketchPad' . $row->id . 'count')) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }

                        if ($row->docket_field_category_id == 1 || $row->docket_field_category_id == 2 || $row->docket_field_category_id == 3 || $row->docket_field_category_id == 4 || $row->docket_field_category_id == 6 || $row->docket_field_category_id == 7 || $row->docket_field_category_id == 16 || $row->docket_field_category_id == 24 || $row->docket_field_category_id == 25  || $row->docket_field_category_id == 20) {
                            if (!Input::has("formField" . $row->id)) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is requireds.'));
                            }
                        }
                    }

                    if (!$row->required) {
                        if ($row->docket_field_category_id == 20) {
                            if (Input::get('formField' . $row->id)) {
                                foreach ($row->docketManualTimerBreak as $rowDatas) {
                                    if ($rowDatas->explanation == 1) {
                                        if (Input::get('breakSubField' . $rowDatas->id)) {

                                            if (Input::get('breakSubField' . $rowDatas->id) == "0 hour 0 minute") {

                                            } elseif (!Input::get('breakSubFieldReason' . $rowDatas->id)) {
                                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field  Explanation is required.'));
                                            }

                                        }


                                    }
                                }
                            }
                        }
                    }

                }

                $receiverNames = "";
                if (!$request->has('invoiceable'))
                    $invoiceable = 0;
                else
                    $invoiceable = $request->invoiceable;
                $themeDocumentId = Docket::where('id', $request->docket_id)->first();
                $date = Carbon::now()->format('d-M-Y');
                if ($request->emailTemplateFlag == "true"){
                    if(Input::has('email_subject')){
                        if(Input::get('email_subject') == ""){
                            $emailSubject = "";
                        }else{
                            $emailSubject = Input::get('email_subject');
                        }
                    }else{
                        $emailSubject = "";
                    }
                    //Email validation
                    $receiverUserId = Input::get('receiver_user_id');
                    foreach ($receiverUserId as $receiver) {
                        $emailUser = EmailUser::find($receiver);
                        $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
                        if ($validator->validate($emailUser->email)) {
                        } else {
                            return response()->json(array('status' => false, 'message' => $emailUser->email . ' is not valid email.'));
                        }
                    }

                    $emailcompany = Company::where('id', $request->header('companyId'))->first();
                    $emailuserFullname = User::where('id', $request->header('userId'))->first();
                    $sentDocket = new EmailSentDocket();
                    $sentDocket->user_id = $request->header('userId');
                    $sentDocket->abn = $emailcompany->abn;
                    $sentDocket->company_name = $emailcompany->name;
                    $sentDocket->company_address = $emailcompany->address;
                    $sentDocket->company_logo   =      $emailcompany->logo;
                    $sentDocket->sender_name = $emailuserFullname->first_name . ' ' . $emailuserFullname->last_name;
                    $sentDocket->docket_id = $request->docket_id;
                    $sentDocket->invoiceable = $invoiceable;
                    $sentDocket->theme_document_id = $themeDocumentId->theme_document_id;
                    $sentDocket->company_id = $request->header('companyId');
                    $sentDocket->docketApprovalType = $themeDocumentId->docketApprovalType;
//                    $sentDocket->template_title = "vbf";
                    if($emailcompany->number_system == 1){
                        if (EmailSentDocket::where('company_id',$request->header('companyId'))->count()== 0){
                            $sentDocket->company_docket_id = 1;
                        }else{
                            $companyDocketId =  EmailSentDocket::where('company_id',$request->header('companyId'))->pluck('company_docket_id')->toArray();
                            $sentDocket->company_docket_id = max($companyDocketId) + 1;
                        }
                    }else{
                        $sentDocket->company_docket_id = 0;
                    }




                    $sentDocket->status = 0;


                    // if($request->header('companyId')!=1){
                    //     //for old api
                    //     $sentDocket->receiver_user_id   =  (is_array($request->receiver_user_id)?($request->receiver_user_id[0]):$request->receiver_user_id);
                    //     //receiver info optional
                    //     $sentDocket->receiver_full_name            =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                    //     $sentDocket->receiver_company_name         =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    //     $sentDocket->receiver_company_address      =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    //     $sentDocket->hashKey            =   $this->generateRandomString();
                    //     $sentDocket->save();

                    //     $sentDocketRecipient                    =    new EmailSentDocketRecipient();
                    //     $sentDocketRecipient->email_sent_docket_id    =    $sentDocket->id;
                    //     $sentDocketRecipient->email_user_id           =    (is_array($request->receiver_user_id)?($request->receiver_user_id[0]):$request->receiver_user_id);
                    //     $sentDocketRecipient->approval  =   1;
                    //     $sentDocketRecipient->status   =   0;
                    //     $sentDocketRecipient->hashKey            =   $this->generateRandomString();
                    //     $sentDocketRecipient->receiver_full_name    =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                    //     $sentDocketRecipient->receiver_company_name	 =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    //     $sentDocketRecipient->receiver_company_address  =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                    //     $sentDocketRecipient->save();
                    // }else{

                    $sentDocket->hashKey = $this->generateRandomString();
                    $sentDocket->save();
                    if ($request->timerId) {
                        foreach ($request->timerId as $timer_id) {
                            $timer_attachment = new \App\SentDcoketTimerAttachment();
                            $timer_attachment->sent_docket_id = $sentDocket->id;
                            $timer_attachment->type = 2;
                            $timer_attachment->timer_id = $timer_id;
                            $timer_attachment->save();
                            $timer = Timer::where('id', $timer_id)->first();
                            $timer->status = 2;
                            $timer->update();
                        }


                    }

                    if($emailcompany->number_system == 1){
                        if($themeDocumentId->hide_prefix ==1){
                            $sentDocket->formatted_id = $sentDocket->company_id.'-'.$sentDocket->company_docket_id ;
                            $sentDocket->update();
                        }else{
                            $sentDocket->formatted_id = 'rt-'.$sentDocket->company_id.'-edoc-'.$sentDocket->company_docket_id ;
                            $sentDocket->update();
                        }

                    }else{
                        $findUserDocketCount = SentDockets::where('user_id', $request->header('userId'))->where('sender_company_id', $request->header('companyId'))->where('docket_id',$themeDocumentId->id)->pluck('user_docket_count')->toArray();
                        $findUserEmailDocketCount =EmailSentDocket::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('docket_id',$themeDocumentId->id)->pluck('user_docket_count')->toArray();
                        if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                            $uniquemax = 0;
                            $sentDocket->user_docket_count = $uniquemax+1;
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-1-".($uniquemax+1);
                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-1-".($uniquemax+1);
                                }
                            }else{
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }
                            }
                            $sentDocket->update();
                        }else{
                            $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                            $sentDocket->user_docket_count = $uniquemax+1;
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-1-".($uniquemax+1);
                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-1-".($uniquemax+1);
                                }
                            }else{
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }
                            }
                            $sentDocket->update();
                        }
                    }

                    //multiple recipient users
                    $receiverUserId = Input::get('receiver_user_id');
                    $docketRecipientId = Input::get('docket_approval_recipient_id');
                    if(Input::has('unsaved_email_user_id')){
                        $receiverUserId     =   Input::get('receiver_user_id');
                        $docketRecipientId  =   Input::get('docket_approval_recipient_id');
                        $unsavedEmailUser   =   Input::get('unsaved_email_user_id');

                        $sn=1;
                        foreach($receiverUserId as $receiver){
                            $sentDocketRecipient                          =    new EmailSentDocketRecipient();
                            $sentDocketRecipient->email_sent_docket_id    =    $sentDocket->id;
                            $sentDocketRecipient->email_user_id           =    $receiver;

                            $docketApproval =   0;
                            if ($themeDocumentId->docketApprovalType == 0 || $themeDocumentId->docketApprovalType == 1) {
                                foreach ($docketRecipientId as $recipientId){
                                    if($recipientId==$receiver)
                                        $docketApproval =   1;
                                }
                            }else{
                                $docketApproval =   1;
                            }


                            $sentDocketRecipient->approval  =   $docketApproval;
                            $sentDocketRecipient->status   =    ($themeDocumentId->docketApprovalType == 0 || $themeDocumentId->docketApprovalType == 1 ) ? 0 : 1;
                            $sentDocketRecipient->hashKey            =   $this->generateRandomString();
                            if(in_array($receiver, $unsavedEmailUser)){
                                $sentDocketRecipient->receiver_full_name        =   Input::has('unsaved_email_name_'.$receiver)?Input::get('unsaved_email_name_'.$receiver):"";
                                $sentDocketRecipient->receiver_company_name	    =   Input::has('unsaved_email_company_name_'.$receiver)?Input::get('unsaved_email_company_name_'.$receiver):"";
                                $sentDocketRecipient->receiver_company_address  =   Input::has('unsaved_email_company_address_'.$receiver)?Input::get('unsaved_email_company_address_'.$receiver):"";
                                $sentDocketRecipient->save();
                            }else{
                                $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver)->first();

                                $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                                $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                                $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                                $sentDocketRecipient->save();
                            }
                        }
                    }else{
                        if(count($receiverUserId)==1){
                            $sentDocketRecipient                        =    new EmailSentDocketRecipient();
                            $sentDocketRecipient->email_sent_docket_id  =    $sentDocket->id;
                            $sentDocketRecipient->email_user_id         =    $receiverUserId[0];
                            $sentDocketRecipient->approval              =    1;
                            $sentDocketRecipient->status                =    0;
                            $sentDocketRecipient->hashKey               =    $this->generateRandomString();
                            if($request->has('receiverFullName') && $request->input('receiverFullName')!=""){
                                    $sentDocketRecipient->receiver_full_name    =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                                    $sentDocketRecipient->receiver_company_name	 =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                                    $sentDocketRecipient->receiver_company_address  =   ($request->has('receiverCompanyAddress'))?$request->receiverCompanyAddress:"";
                                    $sentDocketRecipient->save();
                            }else{
                                $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiverUserId[0])->first();
                                $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                                $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                                $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                                $sentDocketRecipient->save();
                            }
                        }else{
                            $sn=1;
                            foreach($receiverUserId as $receiver){
                                $sentDocketRecipient                    =    new EmailSentDocketRecipient();
                                $sentDocketRecipient->email_sent_docket_id    =    $sentDocket->id;
                                $sentDocketRecipient->email_user_id           =   $receiver;
                                $docketApproval =   0;
                                foreach ($docketRecipientId as $recipientId){
                                    if($recipientId==$receiver)
                                        $docketApproval =   1;
                                }
                                $sentDocketRecipient->approval  =   $docketApproval;
                                $sentDocketRecipient->status   =   0;
                                $sentDocketRecipient->hashKey            =   $this->generateRandomString();
                                if(count($receiverUserId)==1){
                                    if($request->has('receiverFullName') && $request->input('receiverFullName')!=""){
                                            $sentDocketRecipient->receiver_full_name    =   ($request->has('receiverFullName'))?$request->receiverFullName:"";
                                            $sentDocketRecipient->receiver_company_name	 =   ($request->has('receiverCompanyName'))?$request->receiverCompanyName:"";
                                            $sentDocketRecipient->receiver_company_address  =   ($request->has('receiverCompanyAddress'))?$request->receiverCompanyAddress:"";
                                            $sentDocketRecipient->save();
                                    }else{
                                        $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver)->first();
                                        $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                                        $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                                        $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                                        $sentDocketRecipient->save();
                                    }
                                }else {

                                    $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver)->first();
                                    $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                                    $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                                    $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                                    $sentDocketRecipient->save();
                                }
                            }
                        }
                    }
                    //if unsaved email input is not there

//                     $docketFieldsQuery = DocketField::where('docket_id', $request->docket_id)->orderBy('order', 'asc')->get();

                    foreach ($docketFieldsQuery as $row) {

                        if ($row->docket_field_category_id == 9) {
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "signature";
                            $docketFieldValue->save();
                            $totalImages = Input::get('formFieldSignature' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldSignature' . $row->id . 'Id' . $i;
                                $imageFieldName =  Input::get('formFieldSignatureName' . $row->id . 'Id' . $i);
                                $image = Input::file($imageField);

                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new EmailSentDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;
                                        $imageValue->name = $imageFieldName;
                                        $ext = $image->getClientOriginalExtension();
                                        $filename = preg_replace("/[^a-zA-Z]+/", "",basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension())). time() . "." . $ext;
                                        $filename = str_replace(' ', '',$filename);
                                        $dest = 'files/'+$date+'/docket/signature';
                                        $cProfileImage     =   Image::make($image);
                                        if($cProfileImage->width()>500)
                                            Image::make($cProfileImage)->widen(500)->save($dest . '/' . $filename,60);
                                        else
                                            Image::make($cProfileImage)->save($dest . '/' . $filename,60);

                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }

                        } else if ($row->docket_field_category_id == 5) {
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "image";
                            $docketFieldValue->save();

                            $totalImages = Input::get('formFieldImage' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldImage' . $row->id . 'Id' . $i;
                                $image = Input::file($imageField);
                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new EmailSentDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;

                                        // $ext = $image->getClientOriginalExtension();
                                        // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                        // $filename = str_replace(' ', '',$filename);

                                        $dest = 'files/'+$date+'/docket/images';
                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }
                        } else if ($row->docket_field_category_id == 18) {
                            $getDataFromYesNoField = YesNoFields::where('docket_field_id', $row->id)->get();
                            $arraygetDataFromYesNoField = array();
                            foreach ($getDataFromYesNoField as $test) {
                                $arraygetDataFromYesNoField[] = array(
                                    'label' => ($test->label_type == 0) ? $test->label : $test->icon_image,
                                    'colour' => $test->colour,
                                );
                            }

                            $arrayvalues = array();
                            $arrayvalues["title"] = $row->label;
                            $arrayvalues["label_value"] = $arraygetDataFromYesNoField;


//                        $arrayvalues=array($arrayLabel,$arraygetDataFromYesNoField);


                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->label = serialize($arrayvalues);
                            $docketFieldValue->value = (Input::get("formField" . $row->id) == "") ? "N/a" : Input::get("formField" . $row->id);
                            $docketFieldValue->save();
                            if (Input::get("formField" . $row->id) == 0 || Input::get("formField" . $row->id) == 1 || Input::get("formField" . $row->id) == 2) {
                                $yesNoDocketField = Input::get("formField" . $row->id . "SelectedSubField");
                                $items = YesNoDocketsField::where('yes_no_field_id', $yesNoDocketField)->where('explanation', 1)->orderBy('order', 'asc')->get();
                                foreach ($items as $datas) {
                                    if ($datas->docket_field_category_id == 5) {
                                        $yesNoDocketFieldValue = new SentEmailDocValYesNoValue();
                                        $yesNoDocketFieldValue->email_sent_docket_value_id = $docketFieldValue->id;
                                        $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                        $yesNoDocketFieldValue->label = $datas->label;
                                        $yesNoDocketFieldValue->required = $datas->required;
                                        $test = array();
                                        if ($request->hasFile("subField" . $yesNoDocketField . "DocketField" . $datas->id) == 0) {
                                            $serialized_array = serialize($test);
                                            $yesNoDocketFieldValue->value = $serialized_array;
                                        } else {
                                            if ($request->hasFile("subField" . $yesNoDocketField . "DocketField" . $datas->id)) {
                                                foreach ($request->file("subField" . $yesNoDocketField . "DocketField" . $datas->id) as $image) {
                                                    // $ext = $image->getClientOriginalExtension();
                                                    // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                    // $filename = str_replace(' ', '',$filename);
                                                    $dest = 'files/'+$date+'/docket/subEmailDocketImage';
                                                    // $image->move($dest, $filename);
                                                    // $yesNoDocketFieldValue->value = $dest . '/' . $filename;

                                                    $yesNoDocketFieldValue->value = FunctionUtils::imageUpload($dest,$image);
                                                    $test[] = $yesNoDocketFieldValue->value;
                                                }
                                                $serialized_array = serialize($test);
                                                $yesNoDocketFieldValue->value = $serialized_array;
                                            }
                                        }
                                        $yesNoDocketFieldValue->save();
                                    } elseif ($datas->docket_field_category_id == 1) {
                                        $yesNoDocketFieldValue = new SentEmailDocValYesNoValue();
                                        $yesNoDocketFieldValue->email_sent_docket_value_id = $docketFieldValue->id;
                                        $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                        $yesNoDocketFieldValue->value = (Input::get("subField" . $yesNoDocketField . "DocketField" . $datas->id) == "") ? "N/a" : Input::get("subField" . $yesNoDocketField . "DocketField" . $datas->id);
                                        $yesNoDocketFieldValue->label = $datas->label;
                                        $yesNoDocketFieldValue->save();

                                    }
                                }
                            }


                        } else if ($row->docket_field_category_id == 15) {
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "document";
                            if ($docketFieldValue->save()):
                                $documentAttachement = $row->docketAttached;
                                foreach ($documentAttachement as $row) {
                                    $sentDocketAttachement = new SentEmailAttachment();
                                    $sentDocketAttachement->sent_email_value_id = $docketFieldValue->id;
                                    $sentDocketAttachement->document_name = $row->name;
                                    $sentDocketAttachement->url = $row->url;
                                    $sentDocketAttachement->save();

                                }
                            endif;
                        } else if ($row->docket_field_category_id == 14) {
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "sketchpad";
                            $docketFieldValue->save();

                            $totalImages = Input::get('formFieldSketchPad' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldSketchPad' . $row->id . 'Id' . $i;
                                $image = Input::file($imageField);
                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new EmailSentDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;

                                        // $ext = $image->getClientOriginalExtension();
                                        // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                        // $filename = str_replace(' ', '',$filename);
                                        $dest = 'files/'+$date+'/docket/sketchpad';
                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }
                        } else if($row->docket_field_category_id==13){
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value =  (Input::has("formField" . $row->id) == "") ? "" :  Input::get('formField' . $row->id);
                            $docketFieldValue->save();
                        }
                        elseif($row->docket_field_category_id == 22) {
                            $count = Input::get('formField' . $row->id . 'TotalRows');
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "Grid";
                            $docketFieldValue->save();

                            foreach ($row->girdFields as $gridField) {
                                $gridFieldLabel = new DocketFieldGridLabel();
                                $gridFieldLabel->docket_id = $sentDocket->id;
                                $gridFieldLabel->is_email_docket = 1;
                                $gridFieldLabel->docket_field_grid_id = $gridField->id;
                                $gridFieldLabel->label = $gridField->label;
                                $gridFieldLabel->docket_field_id =  $row->id;
                                $gridFieldLabel->sumable = $gridField->sumable;
                                $gridFieldLabel->save();
                            }


                            if (Input::has('formField' . $row->id . 'TotalRows') != ""){
                                for ($i = 0; $i < $count; $i++) {
                                    foreach ($row->girdFields as $gridField) {
                                        if ($gridField->docket_field_category_id == 5) {
                                            $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                            $file_values = array();
                                            if($imageInputs) {
                                                foreach ($imageInputs as $imageInput) {
                                                    $image = $imageInput;
                                                    if ($image->isValid()) {
                                                        // $ext = $image->getClientOriginalExtension();
                                                        // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                        // $filename = str_replace(' ', '',$filename);
                                                        $dest = 'files/' . $date . '/email-docket/images';
                                                        // $image->move($dest, $filename);
                                                        // array_push($file_values, $dest . '/' . $filename);

                                                        array_push($file_values, FunctionUtils::imageUpload($dest,$image));
                                                    }
                                                }
                                            }
                                            $gridFieldValue = new DocketFieldGridValue();
                                            $gridFieldValue->docket_id = $sentDocket->id;
                                            $gridFieldValue->is_email_docket = 1;
                                            $gridFieldValue->docket_field_grid_id = $gridField->id;
                                            $gridFieldValue->value = ($imageInputs) ? serialize($file_values) : "N/a";
                                            $gridFieldValue->index = $i;
                                            $gridFieldValue->docket_field_id =  $row->id;
                                            $gridFieldValue->save();

                                        }elseif($gridField->docket_field_category_id == 14){
                                            $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                            $file_values = array();
                                            if($imageInputs) {
                                                foreach ($imageInputs as $imageInput) {
                                                    $image = $imageInput;
                                                    if ($image->isValid()) {
                                                        // $ext = $image->getClientOriginalExtension();
                                                        // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                        // $filename = str_replace(' ', '',$filename);
                                                        $dest = 'files/' . $date . '/email-docket/sketchpad';
                                                        // $image->move($dest, $filename);
                                                        // array_push($file_values, $dest . '/' . $filename);

                                                        array_push($file_values, FunctionUtils::imageUpload($dest,$image));
                                                    }
                                                }
                                            }
                                            $gridFieldValue = new DocketFieldGridValue();
                                            $gridFieldValue->docket_id = $sentDocket->id;
                                            $gridFieldValue->is_email_docket = 1;
                                            $gridFieldValue->docket_field_grid_id = $gridField->id;
                                            $gridFieldValue->value =  ($imageInputs) ? serialize($file_values) : "N/a";
                                            $gridFieldValue->index = $i;
                                            $gridFieldValue->docket_field_id =  $row->id;
                                            $gridFieldValue->save();

                                        }elseif ($gridField->docket_field_category_id == 9){
                                            $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                            $signatureInputs = Input::get('formField' . $row->id . 'Grid' . $gridField->id . 'SignatureNameIndex' . $i);
                                            $file_values = array();
                                            if($imageInputs) {
                                                foreach ($imageInputs as $k => $imageInput) {
                                                    $image = $imageInput;
                                                    if ($image->isValid()) {
                                                        // $ext = $image->getClientOriginalExtension();
                                                        // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                        // $filename = str_replace(' ', '',$filename);
                                                        $dest = 'files/' . $date . '/docket/signature';
                                                        // $image->move($dest, $filename);
                                                        // $file_values[] = array("image" => $dest . '/' . $filename, "name" => $signatureInputs[$k]);

                                                        $file_values[] = array("image" => FunctionUtils::imageUpload($dest,$image), "name" => $signatureInputs[$k]);
                                                    }
                                                }
                                            }

                                            $gridFieldValue = new DocketFieldGridValue();
                                            $gridFieldValue->docket_id = $sentDocket->id;
                                            $gridFieldValue->is_email_docket = 0;
                                            $gridFieldValue->docket_field_grid_id = $gridField->id;
                                            $gridFieldValue->value = ($imageInputs) ? serialize($file_values) : "N/a";
                                            $gridFieldValue->index = $i;
                                            $gridFieldValue->docket_field_id =  $row->id;
                                            $gridFieldValue->save();

                                        }else {

                                            $input = Input::get('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);

                                            $gridFieldValue = new DocketFieldGridValue();
                                            $gridFieldValue->docket_id = $sentDocket->id;
                                            $gridFieldValue->is_email_docket = 1;
                                            $gridFieldValue->docket_field_grid_id = $gridField->id;
                                            $gridFieldValue->value = ($input == "") ? "N/a" : $input;
                                            $gridFieldValue->index = $i;
                                            $gridFieldValue->docket_field_id =  $row->id;
                                            $gridFieldValue->save();
                                        }
                                    }
                                }
                            }

                        }
                        else {
                            $docketFieldValue = new EmailSentDocketValue();
                            $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            if (Input::get('formField' . $row->id) == "") {
                                $docketFieldValue->value = "N/a";

                            }else{
                                $docketFieldValue->value = (Input::has("formField" . $row->id)) ? Input::get('formField' . $row->id) : "N/a";

                            }
                            $docketFieldValue->save();

                            if ($row->docket_field_category_id == 2 && collect($row->docketInvoiceField)->count() != 0) {
                                $emailSentDocketInvoice = new SentEmailDocketInvoice();
                                $emailSentDocketInvoice->email_sent_docket_id = $sentDocket->id;
                                $emailSentDocketInvoice->email_sent_docket_value_id = $docketFieldValue->id;
                                $emailSentDocketInvoice->type = 1;
                                $emailSentDocketInvoice->save();
                                empty($emailSentDocketInvoice);
                            }

                            //check unit rate field type and if it is 7(unit rate), insert value
                            if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {

                                if (collect($row->docketInvoiceField)->count() != 0) {
                                    $emailSentDocketInvoice = new SentEmailDocketInvoice();
                                    $emailSentDocketInvoice->email_sent_docket_id = $sentDocket->id;
                                    $emailSentDocketInvoice->email_sent_docket_value_id = $docketFieldValue->id;
                                    $emailSentDocketInvoice->type = 2;
                                    $emailSentDocketInvoice->save();
                                    empty($emailSentDocketInvoice);
                                }
                                //get docket field unit rate id's
                                $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                                foreach ($docketFieldUnitRate as $unitRateRow) {
                                    // EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                    //     'docket_unit_rate_id' => $unitRateRow->id,
                                    //     'label'=>$unitRateRow->label,
                                    //     'value' => (Input::get('formSubField' . $unitRateRow->id) == "null") ? 0 : Input::get('formSubField' . $unitRateRow->id)]);
                                    if (Input::get('formSubField' . $unitRateRow->id) == "") {
                                        EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_unit_rate_id' => $unitRateRow->id,
                                            'label' => $unitRateRow->label,
                                            'value' => 0]);

                                    } else {
                                        EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_unit_rate_id' => $unitRateRow->id,
                                            'label' => $unitRateRow->label,
                                            'value' => (Input::get('formSubField' . $unitRateRow->id) == "null") ? 0 : Input::get('formSubField' . $unitRateRow->id)]);

                                    }
                                }
                                empty($docketFieldUnitRate);
                            }

                           if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                                $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;

                                foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                                    if (Input::get('formSubField' . $docketTallyableUnitRates->id) == "") {
                                        EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                            'label' => $docketTallyableUnitRates->label,
                                            'value' => 0]);
                                    } else {
                                        if(Input::has("formField" . $row->id) == ""){
                                            $value = 0;
                                        }else{
                                            if(Input::get('formSubField' . $docketTallyableUnitRates->id) == ""){
                                                $value = 0;
                                            }else{
                                                $value = Input::get('formSubField' . $docketTallyableUnitRates->id);
                                            }
                                        }
                                        EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                            'label' => $docketTallyableUnitRates->label,
                                            'value' => $value]);
                                    }

                                }
                                empty($docketTallyableUnitRate);
                            }

                            if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 20) {
                                $docketFieldManualTimer = $docketFieldValue->docketManualTimer;
                                foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                                    if (Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id) == "") {
                                        EmailSentDocManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_manual_timer_id' => $docketManualTimerRow->id,
                                            'label' => $docketManualTimerRow->label,
                                            'value' => 0,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now()]);
                                    } else {
                                        $value =  0;
                                        if(Input::has("formField" . $row->id) == ""){
                                            $value  =    0;
                                        }else{
                                            if(Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id) == ""){
                                                $value  =    0;
                                            }else{
                                                $value = Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id);
                                            }
                                        }
                                        EmailSentDocManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_manual_timer_id' => $docketManualTimerRow->id,
                                            'label' => $docketManualTimerRow->label,
                                            'value' => $value,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now()
                                        ]);
                                    }
                                }

                                empty($docketFieldManualTimer);

                                $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                                foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                                    if (Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id) == "") {
                                        $breakTimermanual = new EmailSentDocManualTimerBrk();
                                        $breakTimermanual->sent_docket_value_id = $docketFieldValue->id;
                                        $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                                        $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                                        $breakTimermanual->value = "n/a";
                                        $breakTimermanual->reason = "n/a";
                                        $breakTimermanual->save();
                                    } else {
                                        $userInfo = User::where('id', $request->header('userId'))->first();
                                        if ($userInfo->device_type == 2 || $row->required == 0) {
                                            $value = Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id);
                                            $arrayvalue = explode(".", $value);

                                            $breakTimermanual = new EmailSentDocManualTimerBrk();
                                            $breakTimermanual->sent_docket_value_id = $docketFieldValue->id;
                                            $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                                            $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                                            $breakTimermanual->value = $arrayvalue[0] . " Hours " . $arrayvalue[1] . " Minutes";
                                            $breakTimermanual->reason = (Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id) == "") ? "n/a" : Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id);
                                            $breakTimermanual->save();
                                        } else {
                                            $breakTimermanual = new EmailSentDocManualTimerBrk();
                                            $breakTimermanual->sent_docket_value_id = $docketFieldValue->id;
                                            $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                                            $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                                            $breakTimermanual->value = (Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id) == "") ? "n/a" : Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id);
                                            $breakTimermanual->reason = (Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id) == "") ? "n/a" : Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id);
                                            $breakTimermanual->save();
                                        }


                                    }


                                }
                                empty($docketFieldManualTimerBreak);


                            }


                            empty($docketFieldValue);
                        }
                    }


                    $docketProject = DocketProject::where('docket_id', $request->docket_id)->get();
                      foreach ($docketProject as $docketProjects){
                         if ($docketProjects->project->is_close == 0){
                            $sentDocketProject = new  SentDocketProject();
                            $sentDocketProject->project_id = $docketProjects->project_id;
                            $sentDocketProject->sent_docket_id = $sentDocket->id;
                            $sentDocketProject->is_email = 1;
                            $sentDocketProject->save();
                         }
                    }



                    //sender user info
                    $userInfo = User::where('id', $request->header('userId'))->first();
                    $data['message'] = "test";
                    $docketInfo = Docket::where('id', $request->docket_id)->first();

                    $sentEmailDocket = EmailSentDocket::findOrFail($sentDocket->id);
                    $docketFields = EmailSentDocketValue::where('email_sent_docket_id', $sentDocket->id)->get();
                    $data['sentDocket'] = $sentEmailDocket;
                    $data['docketFields'] = $docketFields;
                    $data['sentDocket'] = $sentDocket;
                    $downloadlinks = $this->forwardEmailDocketById($request, $sentDocket->id);
                    $data['downloadLink'] = json_decode($downloadlinks->getContent(), true)["emailDocket"]["filePath"];

                    //pdf generation for sh
                    // $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id', $sentDocket->id)->where('type', 2)->get();
                    // $sentDocket = $sentEmailDocket;
                    // $document_name = Crypt::encryptString("emailed-docket-" . $sentDocket->id . "-" . str_replace(' ', '-', strtolower($sentDocket->senderCompanyInfo->name)));
                    // $document_path = 'files/pdf/emailedDocketForward/' . str_replace('.', '', $document_name) . '.pdf';
                    // if (!AmazoneBucket::fileExist($document_path)) {
                    // $pdf = PDF::loadView('pdfTemplate.emailedDocketForward', compact('sentDocket', 'docketFields', 'docketTimer', 'approval_type','request'));
                    //     $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                    //     $output = $pdf->output();
                    //     $path = storage_path($document_path);
                    //     file_put_contents($path, $output);
                    // }

                    // if($request->header('companyId')!=1) {
                    //     $receiverInfo = EmailUser::where('id', $request->receiver_user_id)->first();
                    //     $data['receiverInfo']   =    EmailSentDocketRecipient::where('email_sent_docket_id', $sentDocket->id)->where('email_user_id',$request->receiver_user_id)->first();
                    //     Mail::send('emails.docket.emailDocket', $data, function ($message) use ($userInfo, $docketInfo, $receiverInfo) {
                    //         $message->from("info@recordtimeapp.com.au", $docketInfo->companyInfo->name);
                    //         $message->to($receiverInfo->email)->subject($docketInfo->title);
                    //     });

                    //     return response()->json(array('status' => true, 'message' => 'Docket successfully sent to ' . $sentDocket->receiverUserInfo->email));
                    // }else{
                    if(!is_null($docketInfo->docketFolderAssign)) {
                        if ($docketInfo->docketFolderAssign->count() != 0) {
                            $folderItem = new FolderItem();
                            $folderItem->folder_id = $docketInfo->docketFolderAssign->folder_id;
                            $folderItem->ref_id = $sentDocket->id;
                            $folderItem->type = 3;
                            $folderItem->user_id = $request->header('userId');
                            $folderItem->status = 0;
                            $folderItem->company_id = $request->header('companyId');
                            if ($folderItem->save()) {
                                EmailSentDocket::where('id', $sentDocket->id)->update(['folder_status' => 1]);
                            }
                        }
                    }



                    $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id', $sentDocket->id)->where('type', 2)->get();
                    $receiverQuery = EmailSentDocketRecipient::where('email_sent_docket_id', $sentDocket->id)->get();
                    $recipientNames = "";
                    foreach ($receiverQuery as $receiverInfo) {
                        $recipientNames = $recipientNames . " " . $receiverInfo->emailUserInfo->email;
                        if ($receiverQuery->count() > 1)
                            if ($receiverQuery->last()->id != $receiverInfo->id)
                                $recipientNames = $recipientNames . ",";
                        $data['receiverInfo'] = $receiverInfo;
                        $data['docketTimer'] = $docketTimer;
                        $data['company_id'] = $request->header('companyId');

                        if($emailSubject == ""){  $emailSubject = "You’ve got a docket"; };
                        Mail::to($receiverInfo->emailUserInfo->email)->send(new \App\Mail\Docket($sentDocket,$recipient,$emailSubject));
                    }
                    $slackNotification = array('sender_name' =>$sentDocket->sender_name, 'company_name' => $sentDocket->company_name, 'template_title' => $sentDocket->template_title);
                    $userInfo->slackChannel('rt-docket-sent')->notify(new SentDocketNotification($slackNotification));
                    return response()->json(array('status' => true, 'message' => 'Docket successfully sent to ' . $recipientNames));
                }
                else {
                    $clients        =   Client::where('company_id', $request->header('companyId'))->orWhere('requested_company_id', $request->header('companyId'))->pluck('company_id')->toArray();
                    $totalEmployee = Employee::whereIn('company_id', $clients)->pluck('user_id')->toArray();
                    $totalCompany = Company::whereIn('id',$clients)->pluck('user_id')->toArray();
                    $totalUser = array_merge($totalEmployee,$totalCompany);
                    $recipentId = Input::get('receiver_user_id');
                   /* foreach ($recipentId as $recipentIds){
                        if (!in_array($recipentIds,$totalUser)){
                            return response()->json(array('status' => false, 'message' => 'Invalid receiver.'));
                        }

                    }*/
                    $company = Company::where('id', $request->header('companyId'))->first();
                    $userFullname = User::where('id', $request->header('userId'))->first();
                    $sentDocket = new SentDockets();
                    $sentDocket->user_id = $request->header('userId');
                    $sentDocket->abn = $company->abn;
                    $sentDocket->company_name = $company->name;
                    $sentDocket->company_address = $company->address;
//                    $sentDocket->company_logo = $company->logo;
                    $sentDocket->sender_name = $userFullname->first_name . ' ' . $userFullname->last_name;
                    $sentDocket->docket_id = $request->docket_id;
                    $sentDocket->theme_document_id = $themeDocumentId->theme_document_id;
//                $sentDocket->template_title  =       $themeDocumentId->title;
                    $sentDocket->invoiceable = $invoiceable;

//                    if (SentDockets::where('sender_company_id',$request->header('companyId'))->count()== 0){
//                        $sentDocket->company_docket_id = 1;
//                    }else{
//                        $companyDocketId =  SentDockets::where('sender_company_id',$request->header('companyId'))->orderBy('created_at','desc')->first();
//                        $sentDocket->company_docket_id = $companyDocketId->company_docket_id + 1;
//                    }


                    if($company->number_system == 1){
                        if (SentDockets::where('sender_company_id',$request->header('companyId'))->count()== 0){
                            $sentDocket->company_docket_id = 1;
                        }else{
                            $companyDocketId =  SentDockets::where('sender_company_id',$request->header('companyId'))->pluck('company_docket_id')->toArray();
                            $sentDocket->company_docket_id = max($companyDocketId) + 1;
                        }
                    }else{
                        $sentDocket->company_docket_id    =   0;
                    }


//                if(Employee::where('user_id', $request->receiver_user_id)->count()!=0):
//                    $companyId = Employee::where('user_id', $request->receiver_user_id)->first()->company_id;
//                else :
//                    $companyId   =   Company::where('user_id', $request->receiver_user_id)->first()->id;
//                endif;
                    $sentDocket->company_id = 0;
//                    $sentDocket->company_logo = $themeDocumentId->companyInfo->logo;
                    $sentDocket->sender_company_id = $request->header('companyId');
                    $sentDocket->status = ($themeDocumentId->docketApprovalType == 0 || $themeDocumentId->docketApprovalType == 1 ) ? 0 : 1;
                    $sentDocket->docketApprovalType = $themeDocumentId->docketApprovalType;
                    $sentDocket->save();
//                if($request->invoiceable==1){
//                    $docketInvoiceValue                     =   new SentDocketInvoiceDetail();
//                    $docketInvoiceValue->sent_docket_id     =   $sentDocket->id;
//                    $docketInvoiceValue->invoiceDescription =   $request->invoiceDescription;
//                    $docketInvoiceValue->invoiceAmount      =   $request->invoiceAmount;
//                    $docketInvoiceValue->save();
//                }

                    if ($request->timerId) {
                        foreach ($request->timerId as $timer_id) {
                            $timer_attachment = new \App\SentDcoketTimerAttachment();
                            $timer_attachment->sent_docket_id = $sentDocket->id;
                            $timer_attachment->type = 1;
                            $timer_attachment->timer_id = $timer_id;
                            $timer_attachment->save();

                            $timer = Timer::where('id', $timer_id)->first();
                            $timer->status = 2;
                            $timer->update();
                        }


                    }

                    if($company->number_system == 1){
                        if($themeDocumentId->hide_prefix ==1){
                            $sentDocket->formatted_id = $sentDocket->company_id.'-'.$sentDocket->company_docket_id ;
                            $sentDocket->update();
                        }else{
                            $sentDocket->formatted_id = 'rt-'.$sentDocket->company_id.'-doc-'.$sentDocket->company_docket_id ;
                            $sentDocket->update();
                        }
                    }else{
                        $findUserDocketCount = SentDockets::where('user_id', $request->header('userId'))->where('sender_company_id', $request->header('companyId'))->where('docket_id',$themeDocumentId->id)->pluck('user_docket_count')->toArray();
                        $findUserEmailDocketCount =EmailSentDocket::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('docket_id',$themeDocumentId->id)->pluck('user_docket_count')->toArray();
                        if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                            $uniquemax = 0;
                            $sentDocket->user_docket_count = $uniquemax+1;
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-1-".($uniquemax+1);

                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-1-".($uniquemax+1);

                                }
                            }else{
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }
                            }
                            $sentDocket->update();
                        }else{
                            $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                            $sentDocket->user_docket_count = $uniquemax+1;
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-1-".($uniquemax+1);
                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-1-".($uniquemax+1);
                                }
                            }else{
                                if($themeDocumentId->hide_prefix ==1){
                                    $sentDocket->formatted_id = $themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                                }else{
                                    $sentDocket->formatted_id = "RT-".$themeDocumentId->prefix."-".$themeDocumentId->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                                }
                            }
                            $sentDocket->update();
                        }
                    }



                    foreach ($docketFieldsQuery as $row) {
                        if ($row->docket_field_category_id == 9) {
                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->value = "signature";
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->save();
                            $totalImages = Input::get('formFieldSignature' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldSignature' . $row->id . 'Id' . $i;
                                $imageFieldName =  Input::get('formFieldSignatureName' . $row->id . 'Id' . $i);
                                $image = Input::file($imageField);
                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new SendDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;
                                        $imageValue->name = $imageFieldName;
                                        // $ext = $image->getClientOriginalExtension();
                                        // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                        // $filename = str_replace(' ', '',$filename);
                                        $dest = 'files/'+$date+'/docket/signature';
                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }

                        } else if ($row->docket_field_category_id == 15) {
                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->value = "document";
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->label = $row->label;
                            if ($docketFieldValue->save()):
                                $documentAttachement = $row->docketAttached;
                                foreach ($documentAttachement as $row) {
                                    $sentDocketAttachement = new SentDocketAttachment();
                                    $sentDocketAttachement->sent_dockets_value_id = $docketFieldValue->id;
                                    $sentDocketAttachement->document_name = $row->name;
                                    $sentDocketAttachement->url = $row->url;
                                    $sentDocketAttachement->save();
                                }
                            endif;
                        } else if ($row->docket_field_category_id == 18) {
                            $getDataFromYesNoField = YesNoFields::where('docket_field_id', $row->id)->get();
                            $arraygetDataFromYesNoField = array();
                            foreach ($getDataFromYesNoField as $test) {
                                $arraygetDataFromYesNoField[] = array(
                                    'label' => ($test->label_type == 0) ? $test->label : $test->icon_image,
                                    'colour' => $test->colour,
                                    'label_type' => $test->label_type,
                                );
                            }
                            $arrayvalues = array();
                            $arrayvalues["title"] = $row->label;
                            $arrayvalues["label_value"] = $arraygetDataFromYesNoField;
                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = serialize($arrayvalues);
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = (Input::get("formField" . $row->id) == "") ? "N/a" : Input::get("formField" . $row->id);
                            $docketFieldValue->save();
                            if (Input::get("formField" . $row->id) == 0 || Input::get("formField" . $row->id) == 1 || Input::get("formField" . $row->id) == 2) {
                                $yesNoDocketField = Input::get("formField" . $row->id . "SelectedSubField");
                                $items = YesNoDocketsField::where('yes_no_field_id', $yesNoDocketField)->where('explanation', 1)->orderBy('order', 'asc')->get();
                                foreach ($items as $datas) {
                                    if ($datas->docket_field_category_id == 5) {
                                        $yesNoDocketFieldValue = new SentDocValYesNoValue();
                                        $yesNoDocketFieldValue->sent_docket_value_id = $docketFieldValue->id;
                                        $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                        $yesNoDocketFieldValue->label = $datas->label;
                                        $yesNoDocketFieldValue->required = $datas->required;
                                        $test = array();
                                        if ($request->hasFile("subField" . $yesNoDocketField . "DocketField" . $datas->id) == 0) {
                                            $serialized_array = serialize($test);
                                            $yesNoDocketFieldValue->value = $serialized_array;
                                        } else {
                                            if ($request->hasFile("subField" . $yesNoDocketField . "DocketField" . $datas->id)) {
                                                foreach ($request->file("subField" . $yesNoDocketField . "DocketField" . $datas->id) as $image) {
                                                    // $ext = $image->getClientOriginalExtension();
                                                    // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                    // $filename = str_replace(' ', '',$filename);
                                                    $dest = 'files/'+$date+'/docket/subDocketImage';
                                                    // $image->move($dest, $filename);
                                                    // $yesNoDocketFieldValue->value = $dest . '/' . $filename;

                                                    $yesNoDocketFieldValue->value = FunctionUtils::imageUpload($dest,$image);
                                                    $test[] = $yesNoDocketFieldValue->value;
                                                }
                                                $serialized_array = serialize($test);
                                                $yesNoDocketFieldValue->value = $serialized_array;
                                            }
                                        }
                                        $yesNoDocketFieldValue->save();
                                    } elseif ($datas->docket_field_category_id == 1) {
                                        $yesNoDocketFieldValue = new SentDocValYesNoValue();
                                        $yesNoDocketFieldValue->sent_docket_value_id = $docketFieldValue->id;
                                        $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                        $yesNoDocketFieldValue->value = (Input::get("subField" . $yesNoDocketField . "DocketField" . $datas->id) == "") ? "N/a" : Input::get("subField" . $yesNoDocketField . "DocketField" . $datas->id);
                                        $yesNoDocketFieldValue->label = $datas->label;
                                        $yesNoDocketFieldValue->save();

                                    }
                                }
                            }
                        } else if ($row->docket_field_category_id == 5) {
                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->value = "image";
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->save();
                            $totalImages = Input::get('formFieldImage' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldImage' . $row->id . 'Id' . $i;
                                $image = Input::file($imageField);
                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new SendDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;
                                        // $ext = $image->getClientOriginalExtension();
                                        // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                        // $filename = str_replace(' ', '',$filename);
                                        $dest = 'files'+$date+'/docket/images';
                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }
                        } else if ($row->docket_field_category_id == 14) {


                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->value = "sketchpad";
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->save();
                            $totalImages = Input::get('formFieldSketchPad' . $row->id . 'count');
                            for ($i = 0; $i < $totalImages; $i++) {
                                $imageField = 'formFieldSketchPad' . $row->id . 'Id' . $i;
                                $image = Input::file($imageField);
                                if ($request->hasFile($imageField)) {
                                    if ($image->isValid()) {
                                        $imageValue = new SendDocketImageValue();
                                        $imageValue->sent_docket_value_id = $docketFieldValue->id;
                                        // $ext = $image->getClientOriginalExtension();
                                        // $filename = basename($request->file($imageField)->getClientOriginalName(), '.' . $request->file($imageField)->getClientOriginalExtension()) . time() . "." . $ext;
                                        // $filename = str_replace(' ', '',$filename);
                                        $dest = 'files/'+$date+'/docket/sketchpad';
                                        // $image->move($dest, $filename);
                                        // $imageValue->value = $dest . '/' . $filename;

                                        $imageValue->value = FunctionUtils::imageUpload($dest,$image);
                                        $imageValue->save();
                                    }
                                }
                            }


                        } else if($row->docket_field_category_id==13) {
                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = (Input::has("formField" . $row->id)) ? Input::get('formField' . $row->id) : " ";
                            $docketFieldValue->save();
                        }elseif($row->docket_field_category_id == 22){
                            $count = Input::get('formField' . $row->id.  'TotalRows');

                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->value = "Grid";
                            $docketFieldValue->save();

                            foreach ($row->girdFields as $gridField) {
                                $gridFieldLabel = new DocketFieldGridLabel();
                                $gridFieldLabel->docket_id = $sentDocket->id;
                                $gridFieldLabel->is_email_docket = 0;
                                $gridFieldLabel->docket_field_grid_id = $gridField->id;
                                $gridFieldLabel->label = $gridField->label;
                                $gridFieldLabel->docket_field_id =  $row->id;
                                $gridFieldLabel->save();
                            }

                            for($i = 0; $i< $count ; $i++)
                            {

                                foreach ($row->girdFields as $gridField) {

                                    if ($gridField->docket_field_category_id == 5) {
                                        $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                        $file_values = array();
                                        if($imageInputs) {
                                            foreach ($imageInputs as $imageInput) {
                                                $image = $imageInput;
                                                if ($image->isValid()) {
                                                    // $ext = $image->getClientOriginalExtension();
                                                    // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                    // $filename = str_replace(' ', '',$filename);
                                                    $dest = 'files/' . $date . '/docket/images';
                                                    // $image->move($dest, $filename);
                                                    // array_push($file_values, $dest . '/' . $filename);
                                                    
                                                    array_push($file_values, FunctionUtils::imageUpload($dest,$image));
                                                }
                                            }
                                            $gridFieldValue = new DocketFieldGridValue();
                                            $gridFieldValue->docket_id = $sentDocket->id;
                                            $gridFieldValue->is_email_docket = 0;
                                            $gridFieldValue->docket_field_grid_id = $gridField->id;
                                            $gridFieldValue->value = ($imageInputs) ? serialize($file_values) : "N/a";
                                            $gridFieldValue->index = $i;
                                            $gridFieldValue->docket_field_id =  $row->id;
                                            $gridFieldValue->save();
                                        }


                                    }elseif($gridField->docket_field_category_id == 14){
                                        $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                        $file_values = array();
                                        if($imageInputs) {
                                            foreach ($imageInputs as $imageInput) {
                                                $image = $imageInput;
                                                if ($image->isValid()) {
                                                    // $ext = $image->getClientOriginalExtension();
                                                    // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                    // $filename = str_replace(' ', '',$filename);
                                                    $dest = 'files/' . $date . '/docket/sketchpad';
                                                    // $image->move($dest, $filename);
                                                    // array_push($file_values, $dest . '/' . $filename);

                                                    array_push($file_values, FunctionUtils::imageUpload($dest,$image));
                                                }
                                            }
                                        }
                                        $gridFieldValue = new DocketFieldGridValue();
                                        $gridFieldValue->docket_id = $sentDocket->id;
                                        $gridFieldValue->is_email_docket = 0;
                                        $gridFieldValue->docket_field_grid_id = $gridField->id;
                                        $gridFieldValue->value = ($imageInputs) ? serialize($file_values) : "N/a";
                                        $gridFieldValue->index = $i;
                                        $gridFieldValue->docket_field_id =  $row->id;
                                        $gridFieldValue->save();

                                    }elseif ($gridField->docket_field_category_id == 9){
                                        $imageInputs = Input::file('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                        $signatureInputs = Input::get('formField' . $row->id . 'Grid' . $gridField->id . 'SignatureNameIndex' . $i);
                                        $file_values = array();
                                        if($imageInputs) {
                                            foreach ($imageInputs as $k => $imageInput) {
                                                $image = $imageInput;
                                                if ($image->isValid()) {
                                                    // $ext = $image->getClientOriginalExtension();
                                                    // $filename = basename($image->getClientOriginalName(), '.' . $image->getClientOriginalExtension()) . time() . "." . $ext;
                                                    // $filename = str_replace(' ', '',$filename);
                                                    $dest = 'files/' . $date . '/docket/signature';
                                                    // $image->move($dest, $filename);
                                                    // $file_values[] = array("image" => $dest . '/' . $filename, "name" => $signatureInputs[$k]);
                                                    
                                                    $file_values[] = array("image" =>  FunctionUtils::imageUpload($dest,$image), "name" => $signatureInputs[$k]);
                                                }
                                            }
                                        }



                                        $gridFieldValue = new DocketFieldGridValue();
                                        $gridFieldValue->docket_id = $sentDocket->id;
                                        $gridFieldValue->is_email_docket = 0;
                                        $gridFieldValue->docket_field_grid_id = $gridField->id;
                                        $gridFieldValue->value = ($imageInputs) ? serialize($file_values) : "N/a";
                                        $gridFieldValue->index = $i;
                                        $gridFieldValue->docket_field_id =  $row->id;
                                        $gridFieldValue->save();

                                    }
                                    else {
                                        $input = Input::get('formField' . $row->id . 'Grid' . $gridField->id . 'Index' . $i);
                                        //dd($gridField->id);

                                        $gridFieldValue = new DocketFieldGridValue();
                                        $gridFieldValue->docket_id = $sentDocket->id;
                                        $gridFieldValue->is_email_docket = 0;
                                        $gridFieldValue->docket_field_grid_id = $gridField->id;
                                        $gridFieldValue->value = ($input == "") ? "N/a" : $input ;
                                        $gridFieldValue->index = $i;
                                        $gridFieldValue->docket_field_id =  $row->id;
                                        $gridFieldValue->save();
                                    }
                                }
                            }
                        } else {

                            $docketFieldValue = new SentDocketsValue();
                            $docketFieldValue->sent_docket_id = $sentDocket->id;
                            $docketFieldValue->docket_field_id = $row->id;
                            $docketFieldValue->label = $row->label;
//                            $docketFieldValue->is_hidden = $row->is_hidden;
                            $docketFieldValue->last_edited_value_id = 0;

                            if (Input::get('formField' . $row->id) == "") {
                                $docketFieldValue->value = "N/a";

                            }else{
                                $docketFieldValue->value = (Input::has("formField" . $row->id)) ? Input::get('formField' . $row->id) : "N/a";

                            }
                            $docketFieldValue->save();
                            //check invoiceable docket and insert into sentDocketInvoice table
                            if ($row->docket_field_category_id == 2 && collect($row->docketInvoiceField)->count() != 0) {
                                $sentDocketInvoice = new SentDocketInvoice();
                                $sentDocketInvoice->sent_docket_id = $sentDocket->id;
                                $sentDocketInvoice->sent_docket_value_id = $docketFieldValue->id;
                                $sentDocketInvoice->type = 1;
                                $sentDocketInvoice->save();
                                empty($sentDocketInvoice);
                            }
                            //check unit rate field type and if it is 7(unit rate), insert value
                            if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {
                                if (collect($row->docketInvoiceField)->count() != 0) {
                                    $sentDocketInvoice = new SentDocketInvoice();
                                    $sentDocketInvoice->sent_docket_id = $sentDocket->id;
                                    $sentDocketInvoice->sent_docket_value_id = $docketFieldValue->id;
                                    $sentDocketInvoice->type = 2;
                                    $sentDocketInvoice->save();
                                    empty($sentDocketInvoice);
                                }
                                //get docket field unit rate id's
                                $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                                foreach ($docketFieldUnitRate as $unitRateRow) {
                                    if (Input::get('formSubField' . $unitRateRow->id) == "") {
                                        SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_unit_rate_id' => $unitRateRow->id,
                                            'label' => $unitRateRow->label,
                                            'value' => 0]);

                                    } else {
                                        $value = 0;
                                        if(Input::has("formField" . $row->id) == ""){}
                                        else{
                                            if(Input::get('formSubField' . $unitRateRow->id) == ""){}
                                            else{ $value = Input::get('formSubField' . $unitRateRow->id);}
                                        }
                                        SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_unit_rate_id' => $unitRateRow->id,
                                            'label' => $unitRateRow->label,
                                            'value' => $value]);
                                    }
                                }

                                empty($docketFieldUnitRate);
                            }


                            if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                                $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;
                                foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                                    if (Input::get('formSubField' . $docketTallyableUnitRates->id) == "") {
                                        SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                            'label' => $docketTallyableUnitRates->label,
                                            'value' => 0]);
                                    } else {
                                        $value = 0;
                                        if(Input::has("formField" . $row->id) == ""){}
                                        else{
                                            if(Input::get('formSubField' . $docketTallyableUnitRates->id) == ""){}
                                            else{
                                                $value = Input::get('formSubField' . $docketTallyableUnitRates->id);
                                            }
                                        }
                                        SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                            'label' => $docketTallyableUnitRates->label,
                                            'value' => $value]);
                                    }

                                }
                                empty($docketTallyableUnitRate);
                            }


                            if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 20) {
                                $docketFieldManualTimer = $docketFieldValue->docketManualTimer;
                                foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                                    if (Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id) == "") {
                                        SentDocketManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_manual_timer_id' => $docketManualTimerRow->id,
                                            'label' => $docketManualTimerRow->label,
                                            'value' => 0,
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now()]);
                                    } else {
                                        SentDocketManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                            'docket_manual_timer_id' => $docketManualTimerRow->id,
                                            'label' => $docketManualTimerRow->label,
                                            // 'value' => (Input::has("formField" . $row->id) == "") ? 0 : (Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id) == "") ? 0 : Input::get('formSubFieldManualTimer' . $docketManualTimerRow->id),
                                            'created_at' => Carbon::now(),
                                            'updated_at' => Carbon::now()
                                        ]);
                                    }
                                }
                                empty($docketFieldManualTimer);


                                $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                                foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                                    if (Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id) == "") {
                                        $breakTimermanual = new SentDocketManualTimerBreak();
                                        $breakTimermanual->sent_docket_value_id = $docketFieldValue->id;
                                        $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                                        $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                                        $breakTimermanual->value = "n/a";
                                        $breakTimermanual->reason = "n/a";
                                        $breakTimermanual->save();


                                    } else {
                                        $breakTimermanual = new SentDocketManualTimerBreak();
                                        $breakTimermanual->sent_docket_value_id = $docketFieldValue->id;
                                        $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                                        $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                                        $breakTimermanual->value = (Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id) == "") ? "n/a" : Input::get('breakSubField' . $docketFieldManualTimerBreakrow->id);
                                        $breakTimermanual->reason = (Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id) == "") ? "n/a" : Input::get('breakSubFieldReason' . $docketFieldManualTimerBreakrow->id);
                                        $breakTimermanual->save();
                                    }
                                }
                                empty($docketFieldManualTimerBreak);
                            }
                            empty($docketFieldValue);
                        }
                    }


                    $docketInfo = Docket::where('id', $request->docket_id)->first();


                    if ($docketInfo->docketFolderAssign!=null){
                        $folderItem = new FolderItem();
                        $folderItem->folder_id = $docketInfo->docketFolderAssign->folder_id;
                        $folderItem->ref_id = $sentDocket->id;
                        $folderItem->type = 1;
                        $folderItem->user_id = $request->header('userId');
                        $folderItem->status = 0;
                        $folderItem->company_id = $request->header('companyId');
                        if ($folderItem->save()){
                            SentDockets::where('id',$sentDocket->id)->update(['folder_status'=>1]);
                        }
                    }

//                    $docketProject = DocketProject::where('docket_id', $request->docket_id)->pluck('project_id')->toArray();
//                    foreach ($docketProject as $docketProjects){
//                        $sentDocketProject = new  SentDocketProject();
//                        $sentDocketProject->project_id = $docketProjects;
//                        $sentDocketProject->sent_docket_id = $sentDocket->id;
//                        $sentDocketProject->is_email = 0;
//                        $sentDocketProject->save();
//                    }

                    $docketProject = DocketProject::where('docket_id', $request->docket_id)->get();
                    foreach ($docketProject as $docketProjects){
                        if ($docketProjects->project->is_close == 0){
                            $sentDocketProject = new  SentDocketProject();
                            $sentDocketProject->project_id = $docketProjects->project_id;
                            $sentDocketProject->sent_docket_id = $sentDocket->id;
                            $sentDocketProject->is_email = 0;
                            $sentDocketProject->save();
                        }
                    }


                    if (Docket::findorfail($request->docket_id)->xero_timesheet == 1) {
                        $docketTimesheets = DocketTimesheet::where('docket_id', $request->docket_id)->get();
                        foreach ($docketTimesheets as $items) {
                            $docketTimesheet = new SentDocketTimesheet();
                            $docketTimesheet->sent_docket_id = $sentDocket->id;
                            $docketTimesheet->docket_field_id = $items->docket_field_id;
                            $docketTimesheet->save();
                        }
                    }

                    //multiple recipient users
                    $receiverUserId = Input::get('receiver_user_id');
                    $docketRecipientId = Input::get('docket_approval_recipient_id');
                    $sn = 1;

                    foreach ($receiverUserId as $receiver) {
                            $sentDocketRecipient = new SentDocketRecipient();
                            $sentDocketRecipient->sent_docket_id = $sentDocket->id;
                            $sentDocketRecipient->user_id = $receiver;
                            $docketApproval = 0;
                            if($docketRecipientId) {
                                foreach ($docketRecipientId as $recipientId) {
                                    if ($recipientId == $receiver)
                                        $docketApproval = 1;
                                }
                            }
                            $sentDocketRecipient->approval = $docketApproval;
                            $sentDocketRecipient->status = 0;
                            $sentDocketRecipient->save();

                        $sentDocketReceiverInfo = User::where('id', $receiver)->first();
                        if ($sn == 1) {
                            $receiverNames = @$sentDocketReceiverInfo->first_name . " " . @$sentDocketReceiverInfo->last_name;
                        } elseif ($sn == count($receiverUserId)) {
                            $receiverNames = $receiverNames . ", " . @$sentDocketReceiverInfo->first_name . " " . @$sentDocketReceiverInfo->last_name . ".";
                        } else {
                            $receiverNames = $receiverNames . ", " . @$sentDocketReceiverInfo->first_name . " " . @$sentDocketReceiverInfo->last_name;
                        }
                        $userNotification = new UserNotification();
                        $userNotification->sender_user_id = $request->header('userId');
                        $userNotification->receiver_user_id = @$sentDocketReceiverInfo->id;
                        $userNotification->type = 3;
                        $userNotification->title = 'New Docket';
                        $userNotification->message = "You have received an docket from " . $sentDocket->senderUserInfo->first_name . " " . $sentDocket->senderUserInfo->last_name;
                        $userNotification->key = $sentDocket->id;
                        $userNotification->status = 0;
                        $userNotification->save();
                        if ($sentDocketReceiverInfo->device_type == 2) {
                            sendiOSNotification($sentDocketReceiverInfo->deviceToken, 'New Docket', "You have received an docket from " . $sentDocket->senderUserInfo->first_name . " " . $sentDocket->senderUserInfo->last_name, array('type' => 3, 'id' => $sentDocket->id));
                        } else if ($sentDocketReceiverInfo->device_type == 1) {
                            sendAndroidNotification($sentDocketReceiverInfo->deviceToken, 'New Docket', "You have received an docket from " . $sentDocket->senderUserInfo->first_name . " " . $sentDocket->senderUserInfo->last_name, array('type' => 3, 'id' => $sentDocket->id));
                        }
                        $sn++;
                    }

                    //save docket approval users details
                    if ($themeDocumentId->docketApprovalType == 0 || $themeDocumentId->docketApprovalType == 1) {
                        foreach ($docketRecipientId as $recipient) {
                            $sentDocketRecipientApproval = new SentDocketRecipientApproval();
                            $sentDocketRecipientApproval->sent_docket_id = $sentDocket->id;
                            $sentDocketRecipientApproval->user_id = $recipient;
//                            $sentDocketRecipientApproval->hashKey = $this->generateRandomString();
                            $sentDocketRecipientApproval->status = 0;
                            $sentDocketRecipientApproval->name = "null";
                            $sentDocketRecipientApproval->signature = "null";
                            $sentDocketRecipientApproval->save();
                        }
                    }


                    // sent docket copy in email
                    if($sentDocket->recipientInfo){
                        $emailSubjectFields =   DocketField::where('docket_id',$sentDocket->docket_id)->where('is_emailed_subject',1)->orderBy('order','asc')->get();
                        $emailSubject   =   "";
                        foreach($emailSubjectFields as $subjectField){
                            $emailSubjectQuery   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->where('docket_field_id', $subjectField->id)->get();
                            if($emailSubjectQuery->count()>0){
                                if($emailSubjectQuery->first()->value!="") {
                                    $emailSubject = $emailSubject . $emailSubjectQuery->first()->label . ": " . $emailSubjectQuery->first()->value . " ";
                                }
                            }
                        }



                        foreach($sentDocket->recipientInfo as $sentDocketReceiver){
                            if($sentDocketReceiver->userInfo->receive_docket_copy==1){

                                //new docket email copy
                                if($request->header('companyId')==1){
                                    if($emailSubject == ""){  $emailSubject = "You’ve got a docket"; };

                                    Mail::to($sentDocketReceiver->userInfo->email)->send(new \App\Mail\Docket($sentDocket, $sentDocketReceiver->userInfo(), $emailSubject));
                                }
                                else {

                                    $sentDocketRecepients = array();
                                    foreach ($sentDocket->recipientInfo as $sentDocketRecepient) {
                                        if ($sentDocketRecepient->userInfo->employeeInfo) {
                                            $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
                                        } else if ($sentDocketRecepient->userInfo->companyInfo) {
                                            $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
                                        }
                                        $sentDocketRecepients[] = array(
                                            'name' => $sentDocketRecepient->userInfo->first_name . " " . $sentDocketRecepient->userInfo->last_name,
                                            'company_name' => $companyNameRecipent,
                                        );
                                    }

                                    $datass = (new Collection($sentDocketRecepients))->sortBy('company_name');
                                    $receiverDetail = array();
                                    foreach ($datass as $datas) {
                                        $receiverDetail[$datas['company_name']][] = $datas['name'];
                                    }

                                    if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocketReceiver->sent_docket_id)->where('user_id', $sentDocketReceiver->user_id)->count() == 1) {
                                        $sentDocketRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $sentDocketReceiver->sent_docket_id)->where('user_id', $sentDocketReceiver->user_id)->first();
                                    } else {
                                        $sentDocketRecipientApprovals = null;
                                    }


                                    $data['company'] = $company;
                                    $data['sentDocket'] = $sentDocket;
                                    $data['receiverDetail'] = $receiverDetail;
                                    $data['sentDocketRecipientApprovals'] = $sentDocketRecipientApprovals;

                                    $document_name = "docket-" . $sentDocket->id . "-" . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower(Company::find($sentDocket->first()->sender_company_id)->name)));
                                    //$path = \Config::get('app.storage_url_pdf');
                                    $document_path = 'files/pdf/docketForward/' . str_replace('.', '', $document_name) . '.pdf';
                                    $pdf = PDF::loadView('emails.docket.docket', compact('company', 'sentDocket', 'receiverDetail', 'sentDocketRecipientApprovals'));
                                    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
                                    $output = $pdf->output();
                                    $path = storage_path($document_path);
                                    file_put_contents($path, $output);
                                    $fileName = str_replace('.', '', $document_name) . '.pdf';
                                    $filePath = \Config::get('app.storage_url_pdf') . $document_path;

                                    Mail::send('emails.docket.docket', $data, function ($message) use ($sentDocket, $sentDocketReceiver, $fileName, $filePath, $emailSubject) {
                                        $message->from("info@recordtimeapp.com.au", $sentDocket->senderCompanyInfo->name);
                                        $message->replyTo($sentDocket->senderUserInfo->email, @$sentDocket->senderUserInfo->first_name . " " . @$sentDocket->senderUserInfo->last_name);
                                        $message->to($sentDocketReceiver->userInfo->email)->subject(($emailSubject == " ") ? $sentDocket->template_title : $emailSubject);
                                        $message->attach($filePath, [
                                            'as' => $fileName,
                                            'mime' => 'application/pdf',
                                        ]);
                                    });
                                }
                            }
                        }
                    }//.sent copy of docket

                    $slackNotification = array('sender_name' =>$sentDocket->sender_name, 'company_name' => $sentDocket->company_name, 'template_title' => $sentDocket->template_title);
                    $sentDocket->senderUserInfo->slackChannel('rt-docket-sent')->notify(new SentDocketNotification($slackNotification));

                    return response()->json(array('status' => true, 'message' => 'Docket successfully sent to ' . $receiverNames));
                }//email template flag
            endif;



    }

    function sendiOSNotification($deviceID, $titles, $message){
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        //The device token.
        $token = $deviceID; //token here
        //Title of the Notification.
        $title = $titles;
        //Body of the Notification.
        $body = $message;
        //Creating the notification array.
        $notification = array('title' =>$title , 'body' => $body, 'sound'=>'default', "content_available"=>true);
        //This array contains, the token and the notification. The 'to' attribute stores the token.
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        //Generating JSON encoded string form the above array.
        $json = json_encode($arrayToSend);
        //Setup headers:
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key= AIzaSyBvGkKWzgG0Ah-dw5EDlszZfX6Tiby67po'; // key here
        //Setup curl, add headers and post parameters.
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        //Send the request
        $response = curl_exec($ch);
        //Close request
        curl_close($ch);
    }

    function sendAndroidNotification($deviceId, $titles, $message){
        $registrationIds = array( $deviceId );
        $msg = array
        (
            'message'   => $message,
            'title'     =>$titles,
            'vibrate'   => 1,
            'sound'     => 1
        );
        $fields = array
        (
            'registration_ids'  => $registrationIds,
            'data'          => $msg
        );

        $headers = array
        (
            'Authorization: key= AAAAYXeBuFI:APA91bFidufG2_gC3OOZWz7y37FWQ0B-tIA1OdAa8lu4HYN4wfX8HbNZXa8Wxg76iWgD_VU4kmvAYu71aCeRPmn99jCsMP2f-BVgVhjRcLVypMFSVB5gKXcQS0Prk5088MIDSJ_mrs-E' ,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        // echo $result;
    }

    public function getCompanyId($userId){
        $companyId  =   0;
        if(Employee::where('user_id', $userId)->count()!=0):
            $companyId = Employee::where('user_id', $userId)->first()->company_id;
        else :
            $companyId   =   Company::where('user_id', $userId)->first()->id;
        endif;
        return $companyId;
    }

    public function getCompanyAllUserId($companyId){
        $userId =   array();
        $employee  =   Employee::where('company_id',$companyId)->pluck('user_id')->toArray();
        $userId     =   array_merge(array(Company::find($companyId)->user_id),$employee);
        return $userId;
    }

    public function getInvoiceableDocketList(Request $request,$userId){
        $totalSentDocketID  =    array();
        $receiverCompanyId  =    $this->getCompanyId($userId);
        $receiverCompanyUserId  =   $this->getCompanyAllUserId($receiverCompanyId);

        $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',$request->header('companyId'))->where('invoiceable',1)->orderBy('id','desc')->get();
        foreach($sentDocketQueryTemp as $sentDocket){
            if($sentDocket->sentDocketRecipientApproval){
                foreach ($sentDocket->sentDocketRecipientApproval as $approvalUserID){
                    $status     =    true;
                    if(!in_array($approvalUserID->user_id,$receiverCompanyUserId)){
                        $status =   false;
                        break;
                    }
                }
            }
            if($status){
                $totalSentDocketID[]    =   $sentDocket->id;
            }
        }

//        $totalSentDocketID  =   array();
//        $admin  =   array();
//        $admin    =   Employee::where('company_id',$request->header('companyId'))->where('is_admin',1)->where('employed',1)->pluck('user_id')->toArray();
//        $admin[]   =   Company::where('id',$request->header('companyId'))->first()->user_id;
//
//        if(in_array($request->header('userId'),$admin)){
//            $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',$request->header('companyId'))->where('invoiceable',1)->orderBy('id','desc')->get();
//        }else{
//            $sentDocketQueryTemp              =   SentDockets::where('user_id',$request->header('userId'))->where('invoiceable',1)->orderBy('id','desc')->get();
//        }
//        foreach($sentDocketQueryTemp as $sentDocket){
//            if ($sentDocket->recipientInfo->count() == 1){
//                if ($sentDocket->recipientInfo->first()->user_id == $userId) {
//                    $totalSentDocketID[] = $sentDocket->id;
//                }
//            }else if($sentDocket->recipientInfo->count()>=2){
//                //get all recipients by sent dockets id
//                $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
//                if ($this->array_equal($tempSentDocketRecipient,array($request->header('userId'),$userId))) {
//                    $totalSentDocketID[]    =   $sentDocket->id;
//                }
//            }
//        }

        $sentDocketQuery    =    SentDockets::whereIn('id',$totalSentDocketID);

        $invoiceableDockets =   array();

        if($sentDocketQuery->count()>0) {
            $resultQuery = $sentDocketQuery->orderBy('created_at', 'desc')->get();

            foreach ($resultQuery as $result) {
                if ($result->sender_company_id == $request->header('companyId')):
                    $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                    $senderImage = $result->senderUserInfo->image;
                    $company = $result->senderCompanyInfo->name;
//                    if ($result->status == 0):
//                        $docketStatus = "Sent";
//                    endif;
//
//                    if ($result->status == 1)
//                        $docketStatus = "Approved";

                    $recipientsQuery    =   $result->recipientInfo;
                    $recipientData      =   "";
                    foreach($recipientsQuery as $recipient) {
                        if($recipient->id==$recipientsQuery->first()->id)
                            $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                        else
                            $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    }


                    //approval text
                    $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
                    $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

                    //check is approval
                    $isApproval                 =   0;
                    $isApproved                 =   0;

                    if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->count()==1){
                        $isApproval             =   1;

                        //check is approved
                        if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                            $isApproved             =   1;
                        }
                    }

                    if ($totalRecipientApproved == $totalRecipientApprovals ){
                        $approvalText               =  "Approved";

                    }else{
                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                    }



                    $invoiceDescription     =    array();
                    $invoiceDescriptionQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',1)->get();
                    foreach($invoiceDescriptionQuery as $description){
                        $invoiceDescription[]   =   array('label'=> $description->sentDocketValueInfo->label,'value' => $description->sentDocketValueInfo->value);
                    }

                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        if(is_numeric($unitRate[0]["value"])){
                            $unitRate1= $unitRate[0]["value"];
                        }else{
                            $unitRate1=0;
                        }
                        if(is_numeric($unitRate[1]["value"])){
                            $unitRate2= $unitRate[1]["value"];
                        }else{
                            $unitRate2= 0;
                        }
                        $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
//                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    //                if($invoiceAmount != 0) {
                    $invoiceableDockets[] = array('id' => $result->id,
                        'companyDocketId'=>$result->formatted_id,
                        'user_id' => $result->user_id,
                        'docketName' => $result->docketInfo->title,
                        'docketTemplateId' => $result->docketInfo->id,
                        'sender' => $userName,
                        'company' => $company,
                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                        'invoiceDescription' => $invoiceDescription,
                        'invoiceAmount' => $invoiceAmount,
                        'recipient'=>$recipientData,
                        'senderImage'=>AmazoneBucket::url() . $senderImage,
                        'status' => $approvalText,
                        'isApproval'=>$isApproval,
                        'isApproved'=>$isApproved,
                        );
                    //                }
                    empty($invoiceDescription);
                    empty($invoiceAmount);
                endif;
            }

        }
        return response()->json(array('status' => true, 'invoiceableDockets' =>$invoiceableDockets));
    }





    public function getLatestConversationList(Request $request){
        $conversationArray      =   array();
        $uniqueRecipients       =   array();
        //get all the sent docket associate with logged in user
        $sentDocketRecipientsQueryID    =   SentDocketRecipient::where('user_id',$request->header('userId'))->orderBy('id','desc')->get()->pluck('sent_docket_id')->toArray();
        $sentDocketQueryID              =   SentDockets::where('user_id',$request->header('userId'))->orderBy('id','desc')->get()->pluck('id')->toArray();
        $totalSentDocketID              =   array_unique(array_merge($sentDocketRecipientsQueryID, $sentDocketQueryID));
        $filteredSentDocketsID          =   array();

        if(count($totalSentDocketID)>0) {
            $sentDocketsQuery = SentDockets::whereIn('id', $totalSentDocketID)->orderBy('id', 'desc')->get();

            foreach ($sentDocketsQuery as $sentDocket) {
                if (count($uniqueRecipients) == 0) {
                    $uniqueRecipients[] = array_unique(array_merge($sentDocket->recipientInfo->pluck('user_id')->toArray(), array($sentDocket->user_id)));
                    $filteredSentDocketsID[] = $sentDocket->id;
                } else {
                    $tempRecipients = array_unique(array_merge($sentDocket->recipientInfo->pluck('user_id')->toArray(), array($sentDocket->user_id)));
                    //check old uniqueRecipients

                    $flag = true;
                    for ($i = 0; $i < count($uniqueRecipients); $i++) {
                        if ($this->array_equal($uniqueRecipients[$i], $tempRecipients)) {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        $filteredSentDocketsID[] = $sentDocket->id;
                        $uniqueRecipients[] = $tempRecipients;
                    }
                }
            }

            $filteredSentDocketsQuery = SentDockets::whereIn('id', $filteredSentDocketsID)->orderBy('created_at', 'desc')->get();
            foreach ($filteredSentDocketsQuery as $row) {
                if ($row->recipientInfo->count()) {
                    $conversationName = "";
                    if ($row->recipientInfo->count() > 1) {
                        $tempRecipientIds = array_values(array_unique(array_merge($row->recipientInfo->pluck('user_id')->toArray(), array($row->user_id))));
                        $sn = 1;
                        foreach ($tempRecipientIds as $recipient) {
                            if ($sn == 1) {
                                $conversationName = User::find($recipient)->first_name;
                            } else {
                                $conversationName = $conversationName . ", " . User::find($recipient)->first_name;
                            }
                            $sn++;
                        }
                        $recipientsArray["sentDocket" . $row->id] = $tempRecipientIds;
                        $conversationProfile = asset("assets/dashboard/images/multipleRecipient2.png");
                    } else {
                        if ($row->user_id == $request->header('userId')) {
                            $conversationName = @User::find($row->recipientInfo->first()->user_id)->first_name . " " . @User::find($row->recipientInfo->first()->user_id)->last_name;
                            $conversationProfile = AmazoneBucket::url() . User::find($row->recipientInfo->first()->user_id)->image;
                            $recipientsArray["sentDocket" . $row->id] = array(User::find($row->recipientInfo->first()->user_id)->id);
                        } else {
                            $conversationName = User::find($row->user_id)->first_name . " " . User::find($row->user_id)->last_name;
                            $conversationProfile = AmazoneBucket::url() . User::find($row->user_id)->image;
                            $recipientsArray["sentDocket" . $row->id] = array($row->user_id);
                        }
                    }
                    $companyIds = array();
                    $companies = "";
                    foreach ($recipientsArray["sentDocket" . $row->id] as $userId) {
                        //find company admin
                        if (Company::where('user_id', $userId)->count() > 0) {
                            $companyIds[] = Company::where('user_id', $userId)->first()->id;
                        } else {
                            $companyIds[] = Employee::where('user_id', $userId)->first()->company_id;
                        }
                    }
                    $sn = 1;
                    foreach (array_unique($companyIds) as $companyId) {
                        if ($sn == 1) {
                            $companies = Company::where('id', $companyId)->first()->name;
                        } else {
                            $companies = $companies . ", " . Company::where('id', $companyId)->first()->name;
                        }
                        $sn++;
                    }

                    //check approved or not /.if status == 1 approved
                    if($row->status==1){
                        $status    =    "Approved";
                    }elseif($row->user_id==$request->header('userId')){
                        $status     =   "Sent";
                    }else{
                        $status     =   "Received";
                    }

                    $lastDocket = array('docketName' => $row->docketInfo->title, 'sender' => $row->senderUserInfo->first_name,'status' => $status);
                    $conversation = array('id' => $row->id, 'name' => $conversationName,
                        'dateAdded' => Carbon::parse($row->created_at)->format('d-M-Y'),
                        'profile' => $conversationProfile,
                        'companies' => $companies,
                        'recipients' => $recipientsArray["sentDocket" . $row->id]);
                    $conversationArray[] = array('conversation' => $conversation, 'lastDocket' => $lastDocket);
                }
            }
        }
        return response()->json(array('status' => true, 'conversation' => $conversationArray));
    }
//    public function getLatestDockets(Request $request){
//        $this->subscriptionCheck($request->header('companyId'));
//        $jsonData               =    array();
//        $latestSentDocketIds        =   SentDockets::where('user_id', $request->header('userId'))->orderBy('created_at','desc')->take(10)->pluck('id')->toArray();
//        $latestRecipientSentDocketIds   =    SentDocketRecipient::where('user_id',$request->header('userId'))->orderBy('created_at','desc')->take(10)->pluck('sent_docket_id')->toArray();
//        $totalSentDocketIds     =   array_unique(array_merge($latestSentDocketIds, $latestRecipientSentDocketIds));
//
//        $sentDockets            =    SentDockets::whereIn('id',$totalSentDocketIds)->orderBy('created_at','desc')->take(10)->get();
//        $sentDocketIds 			=	array();
//
//        foreach ($sentDockets as $row):
//            $sentDocketIds[] 	=	$row->id;
//
//            $recipientsQuery    =   $row->recipientInfo;
//            $recipientData      =   "";
//            foreach($recipientsQuery as $recipient) {
//                if($recipient->id==$recipientsQuery->first()->id)
//                    $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
//                else
//                    $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
//            }
//
//            //check approved or not /.if status == 1 approved
//            if($row->status==1){
//                $status    =    "Approved";
//            }elseif($row->user_id==$request->header('userId')){
//                $status     =   "Sent";
//            }else{
//                $status     =   "Received";
//            }
//
//            //approval text
//            $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$row->id)->count();
//            $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$row->id)->where('status',1)->count();
//
//            //check is approval
//            $isApproval                 =   0;
//            $isApproved                 =   0;
//            if(SentDocketRecipientApproval::where('sent_docket_id',$row->id)->where('user_id',$request->header('userId'))->count()==1){
//                $isApproval             =   1;
//
//                //check is approved
//                if(SentDocketRecipientApproval::where('sent_docket_id',$row->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
//                    $isApproved             =   1;
//                }
//            }
//
//
//            $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
//            $jsonData[]         =   array('id'      =>  $row->id,
//                'user_id'       =>  $row->user_id,
//                'sender'        =>  $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name,
//                'profile'       =>  asset($row->senderUserInfo->image),
//                'docketName'    =>  $row->docketInfo->title,
//                'company'       =>  $row->senderCompanyInfo->name,
//                'recipients'    =>  $recipientData,
//                'dateAdded'     =>  Carbon::parse($row->created_at)->format('d-M-Y'),
//                'dateSorting'   =>  Carbon::parse($row->created_at)->format('d-M-Y H:i:s'),
//                'approvalText'  =>  $approvalText,
//                'isApproval'    =>  $isApproval,
//                'isApproved'    =>  $isApproved,
//                'status'        =>  $status);
//        endforeach;
//
//        $sentDocketRecipients   =   SentDocketRecipient::where('user_id', $request->header('userId'))->whereNotIn('sent_docket_id',$sentDocketIds)->orderBy('created_at','desc')->take(10)->get();
//        foreach($sentDocketRecipients as $row){
//
//            $recipientsQuery    =   $row->sentDocketInfo->recipientInfo;
//            $recipientData      =   "";
//            foreach($recipientsQuery as $recipient) {
//                if($recipient->id==$recipientsQuery->first()->id)
//                    $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
//                else
//                    $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
//            }
//
//            //check approved or not /.if status == 1 approved
//            if($row->sentDocketInfo->status==1){
//                $status    =    "Approved";
//            }elseif($row->sentDocketInfo->user_id==$request->header('userId')){
//                $status     =   "Sent";
//            }else{
//                $status     =   "Received";
//            }
//
//            //approval text
//            $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$row->sentDocketInfo->id)->count();
//            $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$row->sentDocketInfo->id)->where('status',1)->count();
//            $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
//
//            //check is approval
//            $isApproval                 =   0;
//            $isApproved                 =   0;
//            if(SentDocketRecipientApproval::where('sent_docket_id',$row->sentDocketInfo->id)->where('user_id',$request->header('userId'))->count()==1){
//                $isApproval             =   1;
//
//                //check is approved
//                if(SentDocketRecipientApproval::where('sent_docket_id',$row->sentDocketInfo->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
//                    $isApproved             =   1;
//                }
//            }
//
//            $jsonData[]         =   array('id'      =>  $row->sentDocketInfo->id,
//                'user_id'       =>  $row->sentDocketInfo->user_id,
//                'sender'        =>  $row->sentDocketInfo->senderUserInfo->first_name." ".$row->sentDocketInfo->senderUserInfo->last_name,
//                'profile'       =>  asset($row->sentDocketInfo->senderUserInfo->image),
//                'docketName'    =>  $row->sentDocketInfo->docketInfo->title,
//                'company'       =>  $row->sentDocketInfo->senderCompanyInfo->name,
//                'recipients'    =>  $recipientData,
//                'dateAdded'     =>  Carbon::parse($row->sentDocketInfo->created_at)->format('d-M-Y'),
//                'dateSorting'   =>  Carbon::parse($row->sentDocketInfo->created_at)->format('d-M-Y H:i:s'),
//                'approvalText'  =>  $approvalText,
//                'isApproval'    =>  $isApproval,
//                'isApproved'    =>  $isApproved,
//                'status'        =>  $status);
//        }
//        //conversation sorting according to dateAdded
//        $size = count($jsonData);
//        for($i = 0; $i<$size; $i++){
//            for ($j=0; $j<$size-1-$i; $j++) {
//                if (strtotime($jsonData[$j+1]["dateSorting"]) > strtotime($jsonData[$j]["dateSorting"])) {
//                    $tempArray   =    $jsonData[$j+1];
//                    $jsonData[$j+1] = $jsonData[$j];
//                    $jsonData[$j]  =   $tempArray;
//                }
//            }
//        }
//
//        //check if subscription was free/count remaining docket left
//        $company    =    Company::where('id',$request->header('companyId'))->first();
//        if($company->trial_period==3){
//            //get last subscription created date
//            $subscriptionLogQuery    =   SubscriptionLog::where('company_id',$company->id);
//            if($subscriptionLogQuery->count()>0){
//                $lastUpdatedSubscription    =    $subscriptionLogQuery->orderBy('id','desc')->first();
//                $monthDay   =    Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
//                $now    =   Carbon::now();
//                $currentMonthStart  =   Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay);
//                $currentMonthEnd = Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->addDay(30);
//            }else{
//                $currentMonthStart = new Carbon('first day of this month');
//                $currentMonthEnd = new Carbon('last day of this month');
//            }
//            $sentDockets    =   SentDockets::where('sender_company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
//            $emailDockets   =   EmailSentDocket::where('company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
//
//            $totalMonthDockets  =   $sentDockets + $emailDockets;
//
//            if($totalMonthDockets>=20){
//                $freeSubscriptionStatus    =    array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 20 dockets per month.');
//            }
//        }else{
//            $freeSubscriptionStatus    =   array('status' => false, 'message' => '');
//        }
//
//        $notificationCount  =    UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->count();
//        return response()->json(array('status' => true, 'dockets' =>$jsonData, 'freeSubscriptionStatus' => $freeSubscriptionStatus,'notificationCount' =>$notificationCount ));
//    }

    public function previewDocketData($row){
        return  view('dashboard.company.docketManager.partials.table-view.sent-docket-preview',compact('row'))->render();
    }

    public function previewEmailDocketData($row){
        return  view('dashboard.company.docketManager.partials.table-view.email-sent-docket-preview',compact('row'))->render();
    }


    public function getLatestDockets(Request $request){

        //canReject Status
//        if ($recipientApproval->user_id == $request->header('userId')){
//            if ($recipientApproval->status == 0){
//                $canReject = 1;
//            }else{
//                $canReject = 0;
//            }
//        }
        $this->subscriptionCheck($request->header('companyId'));
        $jsonData               =    array();
        $latestSentDocketIds        =   SentDockets::where('user_id', $request->header('userId'))->orderBy('created_at','desc')->pluck('id')->toArray();
        $latestRecipientSentDocketIds   =    SentDocketRecipient::where('user_id',$request->header('userId'))->pluck('sent_docket_id')->toArray();
        $totalSentDocketIds     =   array_merge($latestSentDocketIds, $latestRecipientSentDocketIds);

        $sentDockets            =    SentDockets::whereIn('id',$totalSentDocketIds)->orderBy('created_at','desc')->take(30)->get();
        $sentDocketIds 			=	array();

        foreach ($sentDockets as $row):
              $sentDocketIds[] = $row->id;
              $recipientsQuery = $row->recipientInfo;
              $recipientData = "";
              foreach ($recipientsQuery as $recipient) {
                  if ($recipient->id == $recipientsQuery->first()->id)
                      $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                  else
                      $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
              }
              //check approved or not /.if status == 1 approved
//            if($row->status==1){
//                $status    =    "Approved";
//            }else


           if ($row->status == 3){
               $status = "Rejected";
           }else{
               if($row->is_cancel == 1){
                   $status = "Cancelled";
               }else{
                   if($row->user_id==$request->header('userId')){
                       $status     =   "Sent";
                   }else{
                       $status     =   "Received";
                   }
               }
           }




              //approval text
              $totalRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $row->id)->count();
              $totalRecipientApproved = SentDocketRecipientApproval::where('sent_docket_id', $row->id)->where('status', 1)->count();

              //check is approval
              $isApproval = 0;
              $isApproved = 0;
              if (SentDocketRecipientApproval::where('sent_docket_id', $row->id)->where('user_id', $request->header('userId'))->count() == 1) {
                  $isApproval = 1;

                  //check is approved
                  if (SentDocketRecipientApproval::where('sent_docket_id', $row->id)->where('user_id', $request->header('userId'))->where('status', 1)->count() == 1) {
                      $isApproved = 1;
                  }
              }

              if ($totalRecipientApproved == $totalRecipientApprovals) {
                  $approvalText = "Approved";

              } else {
                  $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

              }


            //canreject
            $canRejectDocket = SentDocketRecipientApproval::where('sent_docket_id',$row->id)->where('user_id',$request->header('userId'));
            $canReject = 0;
            $isReject = 0;
            if($canRejectDocket->count() > 0 ){
                if ($canRejectDocket->first()->status == 0){
                    if ($row->status == 0) {
                        $canReject = 1;
                    }else{
                        $canReject = 0;
                    }
                }else{
                    $canReject = 0;
                }
                if ($row->status == 3){
                    $isReject = 1;
                }else{
                    $isReject = 0;
                }
            }
            $preview = "";
            if(@$row->docketInfo->previewFields->count()>0):
                   $preview = $this->previewDocketData($row);
            endif;



         if ($row->is_cancel) {
             if ($row->user_id == $request->header('userId')){
                 $jsonData[] = array('id' => $row->id,
                     'companyDocketId'=>$row->formatted_id,
                     'user_id' => $row->user_id,
                     'sender' => $row->senderUserInfo->first_name . " " . $row->senderUserInfo->last_name,
                     'profile' => AmazoneBucket::url() . $row->senderUserInfo->image,
                     'docketName' => $row->docketInfo->title,
                     'company' => $row->senderCompanyInfo->name,
                     'recipients' => $recipientData,
                     'dateAdded' => Carbon::parse($row->created_at)->format('d-M-Y'),
                     'dateSorting' => Carbon::parse($row->created_at)->format('d-M-Y H:i:s'),
                     'approvalText' => $approvalText,
                     'isApproval' => $isApproval,
                     'isApproved' => $isApproved,
                     'canReject'=>$canReject,
                     'isReject' => $isReject,
                     'preview' => $preview,
                     'isCancel' => $row->is_cancel,
                     'status' => $status);
             }
          }else{
                 $jsonData[] = array('id' => $row->id,
                     'user_id' => $row->user_id,
                     'companyDocketId'=>$row->formatted_id,
                     'sender' => $row->senderUserInfo->first_name . " " . $row->senderUserInfo->last_name,
                     'profile' => AmazoneBucket::url() . $row->senderUserInfo->image,
                     'docketName' => $row->docketInfo->title,
                     'company' => $row->senderCompanyInfo->name,
                     'recipients' => $recipientData,
                     'dateAdded' => Carbon::parse($row->created_at)->format('d-M-Y'),
                     'dateSorting' => Carbon::parse($row->created_at)->format('d-M-Y H:i:s'),
                     'approvalText' => $approvalText,
                     'isApproval' => $isApproval,
                     'isApproved' => $isApproved,
                     'isCancel' => $row->is_cancel,
                     'isReject' => $isReject,
                     'preview' => $preview,
                     'canReject'=>$canReject,
                     'status' => $status);
         }
        endforeach;

        //conversation sorting according to dateAdded
        $size = count($jsonData);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($jsonData[$j+1]["dateSorting"]) > strtotime($jsonData[$j]["dateSorting"])) {
                    $tempArray   =    $jsonData[$j+1];
                    $jsonData[$j+1] = $jsonData[$j];
                    $jsonData[$j]  =   $tempArray;
                }
            }
        }

        //check if subscription was free/count remaining docket left
        $company    =    Company::where('id',$request->header('companyId'))->first();
        if($company->trial_period==3){
            //get last subscription created date
            $subscriptionLogQuery    =   SubscriptionLog::where('company_id',$company->id);
            if($subscriptionLogQuery->count()>0){
                $lastUpdatedSubscription    =    $subscriptionLogQuery->orderBy('id','desc')->first();
                $monthDay   =    Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
                $now    =   Carbon::now();
                $currentMonthStart  =   Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay);
                if($now->gte($currentMonthStart)){
                   $currentMonthEnd = Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->addDay(30);
                }else{
                   $currentMonthEnd =   $currentMonthStart;
                   $currentMonthStart =      Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->subDays(30);
                }
            }else{
                $currentMonthStart = new Carbon('first day of this month');
                $currentMonthEnd = new Carbon('last day of this month');
            }
            $sentDockets    =   SentDockets::where('sender_company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
            $emailDockets   =   EmailSentDocket::where('company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();

            $totalMonthDockets  =   $sentDockets + $emailDockets;

            if($totalMonthDockets>=5){
                $freeSubscriptionStatus    =    array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 5 dockets per month.');
            }else{
                $freeSubscriptionStatus    =   array('status' => true, 'message' => '');
            }
        }else{
            $freeSubscriptionStatus    =   array('status' => false, 'message' => '');
        }

        $notificationCount  =    UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->count();
        $draftCount = DocketDraft::where('user_id',$request->header('userId'))->count();
        return response()->json(array('status' => true, 'dockets' =>$jsonData, 'freeSubscriptionStatus' => $freeSubscriptionStatus,'notificationCount' => $notificationCount,'message_status'=>1,'draftCount'=>$draftCount));
    }

    public function getLatestEmailDocketHome(Request $request){
        $conversationArray  =   array();
        $sentEmailDocketQuery   =   EmailSentDocket::where('user_id',$request->header('userId'))->take(10)->orderBy('created_at','desc')->get();
        foreach ($sentEmailDocketQuery as $result) {
//            if ($result->status == 1)
//                $docketStatus = "Approved";
//            else
                $docketStatus = "Sent";

            $sender     =    "";
            $recipientName  =    "";
            foreach($result->recipientInfo as $recipient) {
                $sender = $sender . "" . $recipient->emailUserInfo->email;

                if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                    $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                }else{
                    $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                }
                if ($result->recipientInfo->count() > 1)
                    if ($result->recipientInfo->last()->id != $recipient->id){
                        $sender = $sender . ", ";
                        $recipientName  = $recipientName.", ";
                    }

            }

            $preview = "";
            if(@$result->docketInfo->previewFields->count()>0):
                $preview = $this->previewDocketData($result);
            endif;

            //approval text
            $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
            $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();

            if ($totalRecipientApproved == $totalRecipientApprovals ){
                $approvalText               =  "Approved";
            }else{
                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
            }

            $senderUser                 =   User::find($request->header('userId'));
            $company                    =   Company::find($request->header('companyId'));
            if($request->header('companyId')==1){
            $conversationArray[]    =      array('id'           => $result->id,
                                                 'companyDocketId'=>$result->formatted_id,
                                                'user_id'       => $senderUser->id,
                                                'docketName'    => $result->docketInfo->title,
                                                'sender'        => $senderUser->first_name." ".$senderUser->last_name,
                                                "profile"       =>   AmazoneBucket::url() . $senderUser->image,
                                                'company'       => $company->name,
                                                "recipients"    => $recipientName,
                                                'dateAdded'     => Carbon::parse($result->created_at)->format('d-M-Y'),
                                                "dateSorting"   => Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                                                "approvalText"  => $approvalText,
                                                "preview"=>$preview,
                                                "isApproved"    => $result->status,
                                                'status'        => $docketStatus);
            }else {
                $conversationArray[] = array('id' => $result->id,
                    'companyDocketId'=>$result->formatted_id,
                    'user_id' => $senderUser->id,
                    'docketName' => $result->docketInfo->title,
                    'sender' => $sender,
                    "profile"   =>   AmazoneBucket::url() . $senderUser->image,
                    'company' => $company->name,
                    "recipients"    => $recipientName,
                    'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                    "dateSorting" => Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                    "approvalText" =>$approvalText,
                    "preview"=>$preview,
                    "isApproved" => $result->status,
                    'status' => $docketStatus);
            }
        }
        //conversation sorting according to dateAdded
        $size = count($conversationArray);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($conversationArray[$j+1]["dateSorting"]) > strtotime($conversationArray[$j]["dateSorting"])) {
                    $tempArray   =    $conversationArray[$j+1];
                    $conversationArray[$j+1] = $conversationArray[$j];
                    $conversationArray[$j]  =   $tempArray;
                }
            }
        }
        return response()->json(array('status' => true, 'dockets' => $conversationArray));
    }

    public function getLatestEmailInvoiceHome(Request $request){
        $conversationArray  =   array();
        $sentEmailInvoiceQuery  =   EmailSentInvoice::where('user_id',$request->header('userId'))->take(10)->orderBy('created_at','desc')->get();
        foreach ($sentEmailInvoiceQuery as $result) {
            if ($result->status == 1)
                $invoiceStatus = "Approved";
            else
                $invoiceStatus = "Sent";

            $conversationArray[] = array('id' => $result->id,
                'companyInvoiceId'=>$result->formatted_id,
                'user_id' => $result->receiver_user_id,
                'invoiceName' => $result->invoiceInfo->title,
                'receiver' => $result->receiverInfo->email,
                'company'   => $result->senderCompanyInfo->name,
                'sender'=>$result->senderUserInfo->first_name.' '.$result->senderUserInfo->last_name,
                'profile'=>AmazoneBucket::url() . $result->senderUserInfo->image,
                'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                'status' => $invoiceStatus);

        }
        //conversation sorting according to dateAdded
        $size = count($conversationArray);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($conversationArray[$j+1]["dateAdded"]) > strtotime($conversationArray[$j]["dateAdded"])) {
                    $tempArray   =    $conversationArray[$j+1];
                    $conversationArray[$j+1] = $conversationArray[$j];
                    $conversationArray[$j]  =   $tempArray;
                }
            }
        }
        return response()->json(array('status' => true, 'invoices' => $conversationArray));
    }

    public function getLatestEmailConversationList(Request $request){
        if($request->header('companyId')==1){
            $conversationArray  =   array();
            $uniqueRecipients   =   array();
            $filteredEmailSentDocketsID  =   array();
            $emailSentDocketQuery    =    EmailSentDocket::where('user_id',$request->header('userId'))->orderBy('created_at','desc')->get();

            foreach ($emailSentDocketQuery as $emailSentDocket) {
                if(count($uniqueRecipients) == 0){
                    $uniqueRecipients[] =   $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $recipientsArray["emailSentDocket" . $emailSentDocket->id] = $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $filteredEmailSentDocketsID[] =   $emailSentDocket->id;
                }else{
                    $tempRecipients     =    $emailSentDocket->recipientInfo->pluck('email_user_id')->toArray();
                    $flag   =    true;
                    for($i = 0; $i<count($uniqueRecipients); $i++){
                        if($this->array_equal($uniqueRecipients[$i],$tempRecipients)){
                            $flag   =    false;
                        }
                    }
                    if($flag){
                        $filteredEmailSentDocketsID[]   =   $emailSentDocket->id;
                        $uniqueRecipients[]             =   $tempRecipients;
                        $recipientsArray["emailSentDocket" . $emailSentDocket->id] = $tempRecipients;
                    }
                }
            }

            $filteredEmailSentDocketsQuery  =    EmailSentDocket::whereIn('id',$filteredEmailSentDocketsID)->orderBy('created_at','desc')->get();
            foreach ($filteredEmailSentDocketsQuery as $row) {
                if($row->status==1)
                    $docketStatus ="Approved";
                else
                    $docketStatus   =   "Sent";

                $recipientName =   "";
                foreach($row->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($row->recipientInfo->count() > 1)
                        if ($row->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }
                }
                $profile    =    "";
                if($row->recipientInfo->count()>0){
                    $profile    =   asset("assets/dashboard/images/multipleRecipient2.png");
                }else{
                    $profile    =   AmazoneBucket::url() . $row->senderUserInfo->image;
                }
                $conversationArray[]   =   array('id' => $row->id,
                                            'name'  =>  $recipientName,
                                            'profile'   =>  $profile,
                                            'user_id'   =>  $row->user_id,
                                            'docketName' => $row->docketInfo->title,
                                            'sender' => $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name,
                                            'company'   =>  $row->senderCompanyInfo->name,
                                            'dateAdded' =>  Carbon::parse($row->created_at)->format('d-M-Y'),
                                            'recipients'    => $recipientsArray["emailSentDocket" . $row->id],
                                            'status'    => $docketStatus);
            }//foreach end

            //conversation sorting according to dateAdded
            $size = count($conversationArray);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($conversationArray[$j+1]["dateAdded"]) > strtotime($conversationArray[$j]["dateAdded"])) {
                        $tempArray   =    $conversationArray[$j+1];
                        $conversationArray[$j+1] = $conversationArray[$j];
                        $conversationArray[$j]  =   $tempArray;
                    }
                }
            }

            return response()->json(array('status' => true, 'dockets' => $conversationArray));
        }else{
            $sentEmailDocketReceiverId    =    EmailSentDocket::select('receiver_user_id')->where('user_id',$request->header('userId'))->orderBy('created_at','desc')->distinct()->get();
            $conversationArray  =   array();

            foreach ($sentEmailDocketReceiverId as $row){
                $sentEmailDocketQuery   =   EmailSentDocket::where('receiver_user_id',$row->receiver_user_id)->where('user_id',$request->header('userId'))->orderBy('created_at','desc')->first();
    //           echo  $row->receiver_user_id."br".$row->id."br/";
                if($sentEmailDocketQuery->status==1)
                    $docketStatus ="Approved";
                else
                    $docketStatus   =   "Sent";

                $conversationArray[]   =   array('id' => $sentEmailDocketQuery->id,
                    'user_id'   =>  $sentEmailDocketQuery->receiver_user_id,
                    'docketName' => $sentEmailDocketQuery->docketInfo->title,
                    'sender' => $sentEmailDocketQuery->receiverUserInfo->email,
                    'company'   =>  "",
                    'dateAdded' =>  Carbon::parse($sentEmailDocketQuery->created_at)->format('d-M-Y'),
                    'status'    => $docketStatus);

                //conversation sorting according to dateAdded
                $size = count($conversationArray);
                for($i = 0; $i<$size; $i++){
                    for ($j=0; $j<$size-1-$i; $j++) {
                        if (strtotime($conversationArray[$j+1]["dateAdded"]) > strtotime($conversationArray[$j]["dateAdded"])) {
                            $tempArray   =    $conversationArray[$j+1];
                            $conversationArray[$j+1] = $conversationArray[$j];
                            $conversationArray[$j]  =   $tempArray;
                        }
                    }
                }
            }
            return response()->json(array('status' => true, 'dockets' => $conversationArray));
        }
    }

    public function getLatestEmailInvoiceConversationList(Request $request){
        $sentEmailInvoiceReceiverId     =    EmailSentInvoice::select('receiver_user_id')->where('user_id',$request->header('userId'))->orderBy('created_at','desc')->distinct()->get();
        $conversationArray  =   array();

        foreach ($sentEmailInvoiceReceiverId as $row){
            $sentEmailInvoiceQuery  =   EmailSentInvoice::where('receiver_user_id',$row->receiver_user_id)->where('user_id',$request->header('userId'))->orderBy('created_at','desc')->first();
            if($sentEmailInvoiceQuery->status==1)
                $invoiceStatus ="Approved";
            else
                $invoiceStatus   =   "Sent";

            $conversationArray[]   =   array('id' => $sentEmailInvoiceQuery->id,
                'user_id'   =>  $sentEmailInvoiceQuery->receiver_user_id,
                'invoiceName' => $sentEmailInvoiceQuery->invoiceInfo->title,
                'receiver' => $sentEmailInvoiceQuery->receiverInfo->email,
                'company'   =>  "",
                'dateAdded' =>  Carbon::parse($sentEmailInvoiceQuery->created_at)->format('d-M-Y'),
                'status'    => $invoiceStatus);

            //conversation sorting according to dateAdded
            $size = count($conversationArray);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($conversationArray[$j+1]["dateAdded"]) > strtotime($conversationArray[$j]["dateAdded"])) {
                        $tempArray   =    $conversationArray[$j+1];
                        $conversationArray[$j+1] = $conversationArray[$j];
                        $conversationArray[$j]  =   $tempArray;
                    }
                }
            }
        }
        return response()->json(array('status' => true, 'invoices' => $conversationArray));
    }

    public function getConversationChatByUserId(Request $request,$userId){
        $sentDocketQuery    =    SentDockets::where(function($query) use ($request, $userId){
            return $query->where('user_id', $request->header('userId'))
                ->where('receiver_user_id', $userId);
        })->orWhere(function($query) use($userId, $request) {
            return $query->where('receiver_user_id', $request->header('userId'))
                ->where('user_id', $userId);
        });
        if($sentDocketQuery->count()>0){
            $resultQuery = $sentDocketQuery->orderBy('created_at','desc')->get();
            foreach ($resultQuery as $result){
                if($result->company_id==$request->header('companyId')):
                    $userName   =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $company    =   $result->senderCompanyInfo->name;
                    if($result->status==0):
                        if($result->receiver_user_id==$request->header('userId')){
                            $docketStatus   =   "Received";
                        }else{
                            $docketStatus   =   "Sent";
                        }
                    endif;
                else :
                    $userName   =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $company    =   $result->companyInfo->name;
                    if($result->status==0):
                        $docketStatus   =   "Sent";
                    endif;
                endif;
                if($result->status==1)
                    $docketStatus ="Approved";

                $conversationArray[]   =   array('id' => $result->id,
                    'user_id'   =>  $result->user_id,
                    'docketName' => $result->docketInfo->title,
                    'sender' => $userName,
                    'company'   =>  $company,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $docketStatus);
            }
        }
        return response()->json(array('status' => true, 'dockets' =>$conversationArray));
    }

    public function getEmailConversationChatByUserId(Request $request,$userId){

        $sentDocketQuery    =    EmailSentDocket::where('user_id', $request->header('userId'))
            ->where('receiver_user_id', $userId);
        $conversationArray  =   array();
        if($sentDocketQuery->count()>0){
            $resultQuery = $sentDocketQuery->orderBy('created_at','desc')->get();
            foreach ($resultQuery as $result){

                if($result->status==0)
                    $docketStatus   =   "Sent";
                if($result->status==1)
                    $docketStatus ="Approved";

                $conversationArray[]   =   array('id' => $result->id,
                    'user_id'   =>  $result->user_id,
                    'docketName' => $result->docketInfo->title,
                    'sender' => $result->receiverUserInfo->email,
                    'company'   =>  '',
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $docketStatus);
            }
        }

        return response()->json(array('status' => true, 'dockets' =>$conversationArray));

    }

    public function getTimelineChatByRecipients(Request $request){
        $conversationArray      =   array();
        $totalSentDocketID      =   array();
        $recipientsId = array_map('intval',Input::get('recipientId'));
        $recipientsWithUserId 	=	array_unique(array_merge($recipientsId,array((int)$request->header('userId'))));

        //check sent docket
        foreach($recipientsWithUserId as $recipientId) {
            $sentDocketQuery              =   SentDockets::where('user_id',$recipientId)->orderBy('id','desc')->get();
            foreach($sentDocketQuery as $sentDocket){
                if($sentDocket->recipientInfo->count()>0):
                    //get all recipients by sent dockets id
                    $tempSentDocketRecipient    =    array_unique(array_merge(array((int)$recipientId), $sentDocket->recipientInfo->pluck('user_id')->toArray()));

                    if ($this->array_equal($tempSentDocketRecipient,$recipientsWithUserId)) {

                        $totalSentDocketID[]    =   $sentDocket->id;
                    }
                endif;
            }
        }


        $sentDocketsDates    =    SentDockets::whereIn('id',$totalSentDocketID)->where('created_at', '<=',Carbon::now())->groupBy('date')->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))->toArray();

        foreach ($sentDocketsDates as $sentDocketsDate){
            $sentDocketArray    =   array();
            $dateWiseQuery  =    SentDockets::whereIn('id',$totalSentDocketID)->whereDate('created_at',$sentDocketsDate)->orderBy('created_at','desc')->get();
            foreach ($dateWiseQuery as $dateWise){
                //check approved or not /.if status == 1 approved
//                if($dateWise->status==1){
//                    $status    =    "Approved";
//                }else
                if ($dateWise->status == 3){
                    $status = "Rejected";
                }else{
                    if($dateWise->is_cancel== 1){
                        $status     =   "Cancelled";
                    }else{
                        if($dateWise->user_id==$request->header('userId')){
                            $status     =   "Sent";
                        }else{
                            $status     =   "Received";
                        }
                    }
                }

                //approval text
                $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$dateWise->id)->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$dateWise->id)->where('status',1)->count();

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if(SentDocketRecipientApproval::where('sent_docket_id',$dateWise->id)->where('user_id',$request->header('userId'))->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if(SentDocketRecipientApproval::where('sent_docket_id',$dateWise->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }




              foreach ($dateWiseQuery as $dateWiseQuerys) {
                  $recipientsQuery = $dateWiseQuerys->recipientInfo;
                  $recipientData = "";
                  foreach ($recipientsQuery as $recipient) {
                      if ($recipient->id == $recipientsQuery->first()->id)
                          $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                      else
                          $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                  }
              }

                //canreject
                $canRejectDocket = SentDocketRecipientApproval::where('sent_docket_id',$dateWise->id)->where('user_id',$request->header('userId'));
                $canReject = 0;
                $isReject = 0;
                if($canRejectDocket->count() > 0 ){
                    if ($canRejectDocket->first()->status == 0){
                        if ($dateWise->status == 0) {
                            $canReject = 1;
                        }else{
                            $canReject = 0;
                        }
                    }else{
                        $canReject = 0;
                    }


                    if ($dateWise->status == 3){
                        $isReject = 1;
                    }else{
                        $isReject = 0;
                    }

                }

                $preview = "";
                if(@$dateWise->docketInfo->previewFields->count()>0):
                    $preview = $this->previewDocketData($dateWise);
                endif;

               if ($dateWise->is_cancel){
                   if ($dateWise->user_id == $request->header('userId')){
                       $sentDocketArray[]    =   array('id'            => $dateWise->id,
                           'companyDocketId'=>$dateWise->formatted_id,
                           'name'          => $dateWise->senderUserInfo->first_name." ".$dateWise->senderUserInfo->last_name,
                           'company'       => $dateWise->senderCompanyInfo->name,
                           'profile'       => AmazoneBucket::url() . $dateWise->senderUserInfo->image,
                           'docket'        => $dateWise->docketInfo->title,
                           'recipients'    =>  $recipientData,
                           'addedDate'     => Carbon::parse($dateWise->created_at)->format('d-M-Y'),
                           'approvalText'  =>  $approvalText,
                           'isApproval'    =>  $isApproval,
                           'isApproved'    =>  $isApproved,
                           'canReject'=>$canReject,
                           'isReject' => $isReject,
                           'preview' =>$preview,
                           'isCancel'      =>$dateWise->is_cancel,
                            'status'        =>  $status);
                   }
               }else{
                   $sentDocketArray[]    =   array('id'            => $dateWise->id,
                       'companyDocketId'=>$dateWise->formatted_id,
                       'name'          => $dateWise->senderUserInfo->first_name." ".$dateWise->senderUserInfo->last_name,
                       'company'       => $dateWise->senderCompanyInfo->name,
                       'profile'       => AmazoneBucket::url() . $dateWise->senderUserInfo->image,
                       'docket'        => $dateWise->docketInfo->title,
                       'recipients'    =>  $recipientData,
                       'addedDate'     => Carbon::parse($dateWise->created_at)->format('d-M-Y'),
                       'approvalText'  =>  $approvalText,
                       'isApproval'    =>  $isApproval,
                       'isApproved'    =>  $isApproved,
                       'canReject'=>$canReject,
                       'isReject' => $isReject,
                       'preview' =>$preview,
                       'isCancel'      =>$dateWise->is_cancel,
                       'status'        =>  $status);
               }

            }


            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentDocketsDate['date'])->format('l')), 'sentDockets'   =>   $sentDocketArray);
            unset($sentDocketArray);
        }
        return response()->json(array('status' => true, 'timeline' => $conversationArray));
    }

    public function getEmailTimelineByRecipients(Request $request){
        $conversationArray      =   array();
        $conversationsArray      =   array();
        $totalSentDocketID      =   array();
        $recipientsId = array_map('intval',Input::get('recipientId'));
        $totalSentDocketID  =   array();
        //check sent docket
        $sentEmailDocketQuery              =   EmailSentDocket::where('user_id',$request->header('userId'))->orderBy('created_at','desc')->get();
        foreach($sentEmailDocketQuery as $sentEmailDocket){
            if($sentEmailDocket->recipientInfo->count()>0):
                if ($this->array_equal($sentEmailDocket->recipientInfo->pluck('email_user_id')->toArray(),$recipientsId)) {
                    $totalSentDocketID[]    =   $sentEmailDocket->id;
                }
            endif;
        }

        $emailSentDocketsDates    =    EmailSentDocket::whereIn('id',$totalSentDocketID)->where('created_at', '<=',Carbon::now())->groupBy('date')->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))->toArray();
        foreach ($emailSentDocketsDates as $sentDocketsDate){
            $sentDocketArray    =   array();
            $dateWiseQuery  =    EmailSentDocket::whereIn('id',$totalSentDocketID)->whereDate('created_at',$sentDocketsDate)->orderBy('created_at','desc')->get();
            foreach ($dateWiseQuery as $dateWise){
                //check approved or not /.if status == 1 approved
//                if($dateWise->status==1){
//                    $status    =    "Approved";
//                }else{
                    $status     =   "Sent";
//                }


                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$dateWise->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$dateWise->id)->where('approval',1)->where('status',1)->count();


                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }



                $recipientName  =    "";
                foreach($dateWise->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($dateWise->recipientInfo->count() > 1)
                        if ($dateWise->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }
                }

                $preview = "";
                if(@$dateWise->docketInfo->previewFields->count()>0):
                    $preview = $this->previewEmailDocketData($dateWise);
                endif;

                $conversationArray[]    =      array('id'           => $dateWise->id,
                    'companyDocketId'=>'rt-'.$dateWise->company_id.'-edoc-'.$dateWise->company_docket_id,
                    'user_id'       => $dateWise->user_id,
                    'docketName'    => $dateWise->docketInfo->title,
                    'sender'        => $dateWise->senderUserInfo->first_name." ".$dateWise->senderUserInfo->last_name,
                    "profile"       =>   AmazoneBucket::url() . $dateWise->senderUserInfo->image,
                    'company'       => $dateWise->senderCompanyInfo->name,
                    "recipients"    => $recipientName,
                    'dateAdded'     => Carbon::parse($dateWise->created_at)->format('d-M-Y'),
                    "dateSorting"   => Carbon::parse($dateWise->created_at)->format('d-M-Y H:i:s'),
                    "approvalText"  => $approvalText,
                    "preview" => $preview,
                    "isApproved"    => $dateWise->status,
                    'status'        => $status);
            }

            $conversationsArray[]    =   array('date' => array('date'=>Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentDocketsDate['date'])->format('l')), 'dockets'   =>   $conversationArray);
            unset($conversationArray);
        }
        return response()->json(array('status' => true, 'timeline' => $conversationsArray));
    }
    public function getEmailTimelineByUserId(Request $request,$userId){

        $conversationArray      =   array();
        $sentDocketsDates    =     EmailSentDocket::where('user_id', $request->header('userId'))
            ->where('receiver_user_id', $userId)->where('created_at', '<=',Carbon::now())->groupBy('date')
            ->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))
            ->toArray();

        foreach($sentDocketsDates as $sentDocketsDate){
            $sentDocketArray    =   array();
            $dateWiseQuery  =    EmailSentDocket::where('user_id', $request->header('userId'))->where('receiver_user_id', $userId)->whereDate('created_at',$sentDocketsDate)->orderBy('created_at','desc')->get();
            foreach ($dateWiseQuery as $result){
                if($result->status==0)
                    $docketStatus   =   "Sent";
                if($result->status==1)
                    $docketStatus ="Approved";

                $sentDocketArray[]   =   array('id' => $result->id,
                    'user_id'   =>  $result->user_id,
                    'docketName' => $result->docketInfo->title,
                    'sender' => $result->receiverUserInfo->email,
                    'company'   =>  '',
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $docketStatus);
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentDocketsDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentDocketsDate['date'])->format('l')), 'sentDockets'   =>   $sentDocketArray);
            unset($sentDocketArray);
        }
        return response()->json(array('status' => true, 'timeline' => $conversationArray));
    }

    public function getEmailInvoiceTimelineByUserId(Request $request,$userId){
        $conversationArray      =   array();
        $sentInvoiceDates  =   EmailSentInvoice::where('user_id',$request->header('userId'))
                                ->where('receiver_user_id',$userId)->where('created_at','<=',Carbon::now())->groupBy('date')
                                ->orderBy('date','desc')->get(array(DB::raw('Date(created_at) as date')))
                                ->toArray();

        foreach ($sentInvoiceDates as $sentInvoiceDate){
            $sentInvoiceArray    =   array();
            $dateWiseQuery  =    EmailSentInvoice::where('user_id', $request->header('userId'))->where('receiver_user_id', $userId)->whereDate('created_at',$sentInvoiceDate)->orderBy('created_at','desc')->get();

            foreach ($dateWiseQuery as $result){
                if($result->status==0)
                    $invoiceStatus   =   "Sent";
                if($result->status==1)
                    $invoiceStatus ="Approved";

                $sentInvoiceArray[]   =   array('id' => $result->id,
                    'companyInvoiceId'=>'rt-'.$result->company_id.'-einv-'.$result->company_invoice_id,
                    'user_id'   =>  $result->receiver_user_id,
                    'invoiceName' => $result->invoiceInfo->title,
                    'receiver' => $result->receiverInfo->email,
                    'company'   => $result->senderCompanyInfo->name,
                    'sender'=>$result->senderUserInfo->first_name.' '.$result->senderUserInfo->last_name,
                    'profile'=> AmazoneBucket::url() . $result->senderUserInfo->image,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'status'    => $invoiceStatus);
            }
            $conversationArray[]    =   array('date' => array('date'=>Carbon::parse($sentInvoiceDate['date'])->format('d-M-Y'),'day'=>Carbon::parse($sentInvoiceDate['date'])->format('l')), 'sentInvoices'   =>   $sentInvoiceArray);
            unset($sentDocketArray);
            }
        return response()->json(array('status' => true, 'timeline' => $conversationArray));
    }

    public function array_equal($a, $b) {
        return (
            is_array($a)
            && is_array($b)
            && count($a) == count($b)
            && array_diff($a, $b) === array_diff($b, $a)
        );
    }
    public function getDocketList(Request $request){
        $sentDocketQuery    =   SentDockets::where('company_id',$request->header('companyId'))->orWhere('sender_company_id',$request->header('companyId'))->orderBy('created_at','desc')->get();
        $sentDocket = array();

        foreach ($sentDocketQuery as $row):
            if($row->company_id==$request->header('companyId')):
                if($row->status==0):
                    $docketStatus   =   "Received";
                endif;
            else :
                if($row->status==0):
                    $docketStatus   =   "Sent";
                endif;
            endif;
            if($row->status==1)
                $docketStatus ="Approved";

            $sentDocket[]   =   array('id' => $row->id,
                'docketName' => $row->docketInfo->title,
                'sender' => $row->senderUserInfo->first_name. " ".$row->senderUserInfo->last_name,
                'company'   =>  $row->senderCompanyInfo->name,
                'dateAdded' =>  Carbon::parse($row->created_at)->format('d-M-Y'),
                'status'    => $docketStatus);
        endforeach;

        return response()->json(array('status' => true, 'dockets' => $sentDocket));
    }
    public function getDocketDetailByIdWebView(Request $request,$id){

        $sentDocket     =   SentDockets::findOrFail($id);
        $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',1)->get();
//        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
//        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
//        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
//        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
//        $company    =   Company::whereIn('id',$request->header('companyId'))->first();
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




        if ($sentDocket->theme_document_id == 0){
//            $companyName = array();
//            foreach ($company as $row){
//                $companyName[] = $row->name;
//            }
//            $receiver_detail = array();
//            foreach ($sentDocket->recipientInfo as $row){
//                $receiver_detail[] =   $row->userInfo->first_name." ".$row->userInfo->last_name;
//
//            }
//            $company_name =implode(", ", $companyName);
//            $employee_name =implode(", ", $receiver_detail);
            if($sentDocket->sender_company_id==$request->header('companyId')){
                $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
//                return view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','company','docketTimer','approval_type','company_name','employee_name'));
                return (array('docket' => view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','docketTimer','request','receiverDetail'))->render()));
            }else{
                //get total company employee ids
                $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
                $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;
                if(SentDocketRecipient::whereIn('user_id',$employeeIds)->where('sent_docket_id',$id)->count()>0){
                    $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
//                    return view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','company','docketTimer','approval_type','company_name','employee_name'));
                    return (array( 'docket' => view('dashboard.company.docketManager.preview',compact('sentDocket','docketFields','docketTimer','request','receiverDetail'))->render()));
                }else{
                    return response()->json(array('status' => false,'message' => 'Docket not found.'));
                }
            }
        }else{

            $theme = DocumentTheme::where('id', $sentDocket->theme_document_id)->first();

            if($sentDocket->sender_company_id==$request->header('companyId')){
                $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                return (array('docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','docketTimer','employee_name'))->render()));
            }else{
                //get total company employee ids
                $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
                $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;

                if(SentDocketRecipient::whereIn('user_id',$employeeIds)->where('sent_docket_id',$id)->count()>0){
                    $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                    return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','docketTimer','employee_name'))->render()));

                }else{
                    return response()->json(array('status' => false,'message' => 'Docket not found.'));
                }
            }

        }
    }
    public function getDocketDetailsById(Request $request, $id){
        $sentDocket     =   SentDockets::where('id',$id);
        if($sentDocket->count()==1):
            $webView = $this->getDocketDetailByIdWebView($request,$id);
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');

            $validCompanyId     =   array();
            $validCompanyId[]   =   $sentDocket->first()->sender_company_id;
            //get recipient company id
            if($sentDocket->first()->recipientInfo){
                foreach($sentDocket->first()->recipientInfo as $recipient):
                    if(Company::where("user_id",$recipient->user_id)->count()){
                        $validCompanyId[]   =   Company::where("user_id",$recipient->user_id)->first()->id;
                    }else{
                        $validCompanyId[]   =   @Employee::where('user_id',$recipient->user_id)->first()->company_id;
                    }
                endforeach;
            }
            // if($request->header('companyId')==1){
            //     var_dump(array('test'=>$webView);
            // }
            if(in_array($companyId,$validCompanyId)){
                $sentDocketValueQuery    =    SentDocketsValue::where('sent_docket_id',$id)->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    if((!$row->docketFieldInfo->is_hidden && $row->sentDocket->sender_company_id!=$companyId) || $row->sentDocket->sender_company_id==$companyId):
                        $subFiled   =   [];
                        if($row->docketFieldInfo->docket_field_category_id==7):
                            foreach($row->sentDocketUnitRateValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'label' => $subFiledRow->docketUnitRateInfo->label,
                                    'value' => $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => $row->value,
                                'subFiled' => $subFiled);
                            unset($subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==9):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => AmazoneBucket::url() . $row->value,
                                'subFiled' => $subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==14):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;
                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => AmazoneBucket::url() . $row->value,
                                'subFiled' => $subFiled);

                        elseif($row->docketFieldInfo->docket_field_category_id==15):
                            foreach($row->sentDocketAttachment as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'name'=>$subFiledRow->document_name,
                                    'url' => AmazoneBucket::url() . $subFiledRow->url);
                            endforeach;
                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => AmazoneBucket::url() . $row->url,
                                'subFiled' => $subFiled);

                        elseif($row->docketFieldInfo->docket_field_category_id==5):
                            foreach($row->sentDocketImageValue as $subFiledRow):
                                $subFiled[]    =     array('id' => $subFiledRow->id,
                                    'value' => AmazoneBucket::url() . $subFiledRow->value);
                            endforeach;

                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => AmazoneBucket::url() . $row->value,
                                'subFiled' => $subFiled);
                        elseif($row->docketFieldInfo->docket_field_category_id==6):
                            if ($row->value== "N/a"){
                                $sentDocketValue[]    =     array('id' => $row->id,
                                    'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                    'docket_field_category' =>  $row->label,
                                    'label' => $row->label,
                                    'value' =>$row->value,
                                    'subFiled' => $subFiled);
                            }else{
                                $sentDocketValue[]    =     array('id' => $row->id,
                                    'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                    'docket_field_category' =>  $row->label,
                                    'label' => $row->label,
                                    'value' =>  Carbon::parse($row->value)->format('d-M-Y'),
                                    'subFiled' => $subFiled);
                            }


                        elseif ($row->docketFieldInfo->docket_field_category_id==13):
                            //                        $footerDetails = DocketFieldFooter::select('id', 'value')->where("field_id", $row->docket_field_id)->orderBy('id', 'asc')->first();
                            $footers = array('id' => $row->id,
                                'docket_field_category_id' => $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' => $row->label,
                                'label' => $row->label,
                                'value' => $row->value,
                                'subField' => array());
                        else:
                            $sentDocketValue[]    =     array('id' => $row->id,
                                'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                                'docket_field_category' =>  $row->label,
                                'label' => $row->label,
                                'value' => $row->value,
                                'subFiled' => $subFiled);
                        endif;
                    endif;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }


                //approval text
                $totalRecipientApprovalsQuery   =   SentDocketRecipientApproval::where('sent_docket_id',$id)->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$id)->where('status',1)->count();

                $rejectRecipent = array();
                $approvedUsers   =   array();
                $nonApprovedUsers   =   array();
                $canReject = 0;
                $isReject = 0;
                $isApproved = 0;
                foreach ($totalRecipientApprovalsQuery as $recipientApproval){
                    if ($recipientApproval->status == 3){
                        $rejectRecipent[] = array(
                            'id'=>$recipientApproval->user_id,
                            'user_name'=>$recipientApproval->userInfo->first_name." ".$recipientApproval->userInfo->last_name,
                            'message'=> SentDocketReject::where('sent_docket_id',$recipientApproval->sent_docket_id)->where('user_id',$recipientApproval->user_id)->first()->explanation,
                            'time' => Carbon::parse(SentDocketReject::where('sent_docket_id',$recipientApproval->sent_docket_id)->where('user_id',$recipientApproval->user_id)->first()->updated_at)->format('d/m/Y h:i A')." AEDT",
                        );
                        $isReject = 1;
                    }else{

                        if($recipientApproval->status==1) {
                            $approvedUsers[] = array('id' => $recipientApproval->userInfo->id,
                                'userName' => $recipientApproval->userInfo->first_name . " " . $recipientApproval->userInfo->last_name,
                                'time' => Carbon::parse($recipientApproval->updated_at)->format('d/m/Y h:i A')." AEDT");


                        }else{

                            $nonApprovedUsers[] = array('id' => $recipientApproval->userInfo->id,
                                'userName' => $recipientApproval->userInfo->first_name . " " . $recipientApproval->userInfo->last_name);

                        }

                        //canReject Status
                        if ($recipientApproval->user_id == $request->header('userId')){
                            if ($recipientApproval->status == 0){
                                $canReject = 1;
                            }else{
                                $canReject = 0;
                            }
                        }
                    }

                }

//               if (SentDocketRecipientApproval::where('sent_docket_id',$id)->where('user_id',$request->header('userId'))->count()==1){
//                   $docketApprovalType= $sentDocket->first()->docketApprovalType;
//               }


                $recipentId = array();
                foreach ($sentDocket->first()->sentDocketRecipientApproval as $itesm){
                    $recipentId[]= $itesm->userInfo->id;
                }


                //check is approved
                if (SentDocketRecipientApproval::where('sent_docket_id',  $sentDocket->first()->id)->where('user_id', $request->header('userId'))->where('status', 1)->count() == 1) {
                    $isApproved = 1;
                }

                $docketStatus   =   array(
                    'receivedTime'=> Carbon::parse(SentDockets::where('id',$id)->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                    'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                    'docketStatus'=> (SentDockets::where('id',$id)->first()->user_id==$request->header('userId'))?"sent":"received",
                    'docket_approval_type'=>$sentDocket->first()->docketApprovalType,
                    'receiver_id'=>$recipentId,
                    'isCancelled'=>$sentDocket->first()->is_cancel,
                    'can_reject'=>$canReject,
                    'isRejected'=>$isReject,
                    'isApproved'=>$isApproved,
                    'approvedUser' => $approvedUsers,
                    'reject_user'=>$rejectRecipent,
                    'nonApprovedUser' => $nonApprovedUsers);

                $userNotificationQuery  =   UserNotification::where('type',3)->where('receiver_user_id',$request->header('userId'))->where('key',$id);
                if($userNotificationQuery->count()>0){
//                    if($userNotificationQuery->first()->status==0){
                    UserNotification::where('type',3)->where('receiver_user_id',$request->header('userId'))->where('key',$id)->update(['status'=>1]);
//                    }
                }

                $jsonResponse =  array('status' => true,'docketStatus' => $docketStatus, 'docketsValue' => $sentDocketValue,'webView'=>$webView);

                if(SentDocketRecipientApproval::where('sent_docket_id',$id)->where('user_id',$request->header('userId'))->count()==1){
                    $jsonResponse["docketApprovalType"] = $sentDocket->first()->docketApprovalType;
                }


                return response()->json($jsonResponse);


            }
            else {
                echo "not authorized";
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;

    }

    public function getEmailDocketDetailsByIdWebView(Request $request,$id){
        $sentDocket     =   EmailSentDocket::findOrFail($id);
        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
        if ($sentDocket->theme_document_id == 0){
            $companyName = array();
            foreach ($company as $row){
                $companyName[] = $row->receiver_company_name;
            }
            $receiver_detail = array();
            foreach ($sentDocket->recipientInfo as $row){
                $receiver_detail[] =   $row->emailUserInfo->email;

            }
            $company_name =implode(", ", $companyName);
            $employee_name =implode(", ", $receiver_detail);
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
            if($sentDocket->company_id==$request->header('companyId')){
                $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
//            return view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer'));
                return (array( 'docket' => view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));

            }else {
                flash('Invalid action ! Please try with valid action.','warning');
                return redirect()->back();
            }
        }else{

            $companyName = array();
            foreach ($company as $row){
                $companyName[] = $row->receiver_company_name;
            }
            $receiver_detail = array();
            foreach ($sentDocket->recipientInfo as $row){
                $receiver_detail[] =   $row->emailUserInfo->email;
            }
            $company_name =implode(", ", $companyName);
            $employee_name =implode(", ", $receiver_detail);
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
            $theme = DocumentTheme::where('id', $sentDocket->theme_document_id)->first();
            if($sentDocket->company_id==$request->header('companyId')){
                $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
//            return view('dashboard.company.docketManager.emailPreview',compact('sentDocket','docketFields','company','docketTimer'));
                return (array( 'docket' => view('dashboard/company/theme/'.$theme->slug.'/mobile',compact('sentDocket','docketFields','company','docketTimer','company_name','employee_name'))->render()));
            }else {
                flash('Invalid action ! Please try with valid action.','warning');
                return redirect()->back();
            }
        }

    }

    public function getEmailDocketDetailsById(Request $request, $id){
        $sentDocket     =   EmailSentDocket::where('id',$id);
        $webView = $this->getEmailDocketDetailsByIdWebView($request, $id);
        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');
            if($sentDocket->where('company_id',$companyId)->count()>0){
                $sentDocketValueQuery    =    EmailSentDocketValue::where('email_sent_docket_id',$id)->get();
                $sentDocketValue    = array();
                foreach ($sentDocketValueQuery as $row){
                    $subFiled   =   [];
                    if($row->docketFieldInfo->docket_field_category_id==7):
                        foreach($row->sentDocketUnitRateValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'label' => $subFiledRow->docketUnitRateInfo->label,
                                'value' => $subFiledRow->value);
                        endforeach;
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => $row->value,
                            'subFiled' => $subFiled);
                        unset($subFiled);
                    elseif($row->docketFieldInfo->docket_field_category_id==9):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);

                    elseif($row->docketFieldInfo->docket_field_category_id==14):

                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);
                    elseif($row->docketFieldInfo->docket_field_category_id==5):
                        foreach($row->sentDocketImageValue as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'value' => AmazoneBucket::url() . $subFiledRow->value);
                        endforeach;

                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->value,
                            'subFiled' => $subFiled);
                    elseif ($row->docketFieldInfo->docket_field_category_id==13):
                        $footerDetails = DocketFieldFooter::select('id', 'value')->where("field_id", $row->docket_field_id)->orderBy('id', 'asc')->first();
                        $footers = array('id' => $row->id,
                            'docket_field_category_id' => $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' => $row->label,
                            'label' => $row->label,
                            'value' => @$footerDetails->value,
                            'subField' => array());

                    elseif($row->docketFieldInfo->docket_field_category_id==15):
                        foreach($row->sentEmailAttachment as $subFiledRow):
                            $subFiled[]    =     array('id' => $subFiledRow->id,
                                'name'=>$subFiledRow->name,
                                'url' => AmazoneBucket::url() . $subFiledRow->url);
                        endforeach;
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => AmazoneBucket::url() . $row->url,
                            'subFiled' => $subFiled);
                    else:
                        $sentDocketValue[]    =     array('id' => $row->id,
                            'docket_field_category_id'  =>  $row->docketFieldInfo->docket_field_category_id,
                            'docket_field_category' =>  $row->label,
                            'label' => $row->label,
                            'value' => $row->value,
                            'subFiled' => $subFiled);
                    endif;
                }
                if(@$footers){
                    $sentDocketValue[] =   $footers;
                }

                //approval text
                $totalRecipientApprovalsQuery   =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->get();
                $totalRecipientApprovals    =   $totalRecipientApprovalsQuery->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('status',1)->count();

                $approvedUsers   =   array();
                $nonApprovedUsers   =   array();
                foreach ($totalRecipientApprovalsQuery as $recipientApproval){

                    if($recipientApproval->status==1) {
                        $approvedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email,
                            'time' => Carbon::parse($recipientApproval->updated_at)->format('d/m/Y h:i A')." AEDT");
                    }else{
                        $nonApprovedUsers[] = array('id' => $recipientApproval->emailUserInfo->id,
                            'userName' => $recipientApproval->emailUserInfo->email);
                    }

                }

                $docketStatus   =   array('receivedTime'=> Carbon::parse(EmailSentDocket::where('id',$id)->first()->created_at)->format('d/m/Y h:i A')." AEDT",
                    'status'=> $totalRecipientApproved."/".$totalRecipientApprovals,
                    'docketStatus'=> (EmailSentDocket::where('id',$id)->first()->status==1)?"Approved":"Sent",
                    'approvedUser' => $approvedUsers,
                    'docket_approval_type' =>$sentDocket->first()->docketApprovalType,
                    'nonApprovedUser' => $nonApprovedUsers);

                $userNotificationQuery  =   UserNotification::where('type',5)->where('receiver_user_id',$request->header('userId'))->where('key',$id);
                if($userNotificationQuery->count()>0){
                    if($userNotificationQuery->first()->status==0){
                        UserNotification::where('type',5)->where('receiver_user_id',$request->header('userId'))->where('key',$id)->update(['status'=>1]);
                    }
                }
                return response()->json(array('status' => true, 'docketStatus' => $docketStatus,'docketsValue' => $sentDocketValue,'docketApprovalType'=>$sentDocket->first()->docketApprovalType,'webView'=>$webView));
            } else {
                echo "not authorized";
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;

    }

//    public function approveDocketById(Request $request){
//
//        $sentDocket =SentDockets::where('id',$request->sentDocketId);
//        if($sentDocket->count()==1):
//            $sentDocketRecipientApprovalQuery    =   SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->Where('user_id',$request->header('userId'))->where('status',0);
//
//        if($sentDocketRecipientApprovalQuery->count()==1){
//
//            if ($sentDocket->first()->docketApprovalType == 1){
//                $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required','name' =>     'required','signature' =>     'required']);
//                if ($validator->fails()):
//                    foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
//                    return response()->json(array('status' => false,'message' => $errors));
//                else:
//                $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
//                $sentDocketRecipientApproval->status     =   1;
//                $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
//                $sentDocketRecipientApproval->name =$request->name;
//                $signature              =   Input::file('signature');
//                if($request->hasFile('signature')) {
//                    if ($signature->isValid()) {
//                        $ext = $signature->getClientOriginalExtension();
//                        $filename = basename($request->file('signature')->getClientOriginalName(), '.' . $request->file('signature')->getClientOriginalExtension()) . time() . "." . $ext;
//                        $dest = 'xfiles/docket/images';
//                        $signature->move($dest, $filename);
//                        $path = $dest . '/' . $filename;
//                        $sentDocketRecipientApproval->signature=$path;
//                    }
//                }
//                $sentDocketRecipientApproval->save();
//                endif;
//            }else{
//                $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required']);
//                if ($validator->fails()):
//                    foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
//                    return response()->json(array('status' => false,'message' => $errors));
//                else:
//                $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
//                $sentDocketRecipientApproval->status     =   1;
//                $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
//                $sentDocketRecipientApproval->save();
//                    endif;
//
//            }
//                $sentDocketSenderInfo    =    User::where('id',$sentDocket->first()->user_id)->first();
//                $sentDocketReceiverInfo    =    User::where('id',$request->header('userId'))->first();
//
//
//                if(SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->where('status',0)->count()==0){
//                    $sentDocketUpdate   =    SentDockets::findOrFail($request->sentDocketId);
//                    $sentDocketUpdate->status   =    1;
//                    $sentDocketUpdate->save();
//
//                    if(SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->count()>1) {
//                        if ($sentDocketSenderInfo->device_type == 2) {
//                            $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
//                        }
//                        if ($sentDocketSenderInfo->device_type == 1) {
//                            $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
//                        }
//                    }
//                }
//
//                if($sentDocketSenderInfo->device_type==2){
//                    $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
//                }
//                if($sentDocketSenderInfo->device_type==1){
//                    $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
//                }
//
//                $userNotification   =    new UserNotification();
//                $userNotification->sender_user_id   =    $request->header('userId');
//                $userNotification->receiver_user_id =   SentDockets::find($request->sentDocketId)->user_id;
//                $userNotification->type     =   3;
//                $userNotification->title    =   'Docket Approved';
//                $userNotification->message  =   $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.";
//                $userNotification->key      =   $request->sentDocketId;
//                $userNotification->status   =   0;
//                $userNotification->save();
//
//                return response()->json(array('status' => true,'message' => 'Docket approved successfully.'));
//            } else {
//                return response()->json(array('status' => true,'message' => 'Docket already approved.'));
//            }
//        else:
//            return response()->json(array('status' => false,'message' => 'Docket not found.'));
//        endif;
//    }

    public function approveDocketById(Request $request){
        $sentDocket     =   SentDockets::where('id',$request->sentDocketId);
        if($sentDocket->count()==1):
            $sentDocketRecipientApprovalQuery    =   SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->Where('user_id',$request->header('userId'))->where('status',0);

            if($sentDocketRecipientApprovalQuery->count()==1){

                if ($sentDocket->first()->docketApprovalType == 1){
                    $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required','name' =>     'required','signature' =>     'required']);
                    if ($validator->fails()):
                        foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
                        return response()->json(array('status' => false,'message' => $errors));
                    else:
                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->name =$request->name;
                        $signature              =   Input::file('signature');
                        if($request->hasFile('signature')) {
                            if ($signature->isValid()) {
                                // $ext = $signature->getClientOriginalExtension();
                                // $filename = basename($request->file('signature')->getClientOriginalName(), '.' . $request->file('signature')->getClientOriginalExtension()) . time() . "." . $ext;
                                $dest = 'xfiles/docket/images';
                                // $signature->move($dest, $filename);
                                // $path = $dest . '/' . $filename;
                                $path = FunctionUtils::imageUpload($dest,$signature);
                                $sentDocketRecipientApproval->signature=$path;
                            }
                        }
                        $sentDocketRecipientApproval->save();
                    endif;
                }else{
                    $validator  =   Validator::make(Input::all(),['sentDocketId' =>     'required']);
                    if ($validator->fails()):
                        foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
                        return response()->json(array('status' => false,'message' => $errors));
                    else:
                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->save();
                    endif;

                }

                $sentDocketSenderInfo    =    User::where('id',$sentDocket->first()->user_id)->first();
                $sentDocketReceiverInfo    =    User::where('id',$request->header('userId'))->first();


                if(SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->where('status',0)->count()==0){
                    $sentDocketUpdate   =    SentDockets::findOrFail($request->sentDocketId);
                    $sentDocketUpdate->status   =    1;
                    $sentDocketUpdate->save();

                    if(SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->count()>1) {
                        if ($sentDocketSenderInfo->device_type == 2) {
                            $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                        }
                        if ($sentDocketSenderInfo->device_type == 1) {
                            $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                        }
                    }
                }

                if($sentDocketSenderInfo->device_type==2){
                    $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
                }
                if($sentDocketSenderInfo->device_type==1){
                    $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =    $request->header('userId');
                $userNotification->receiver_user_id =   SentDockets::find($request->sentDocketId)->user_id;
                $userNotification->type     =   3;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.";
                $userNotification->key      =   $request->sentDocketId;
                $userNotification->status   =   0;
                $userNotification->save();

                return response()->json(array('status' => true,'message' => 'Docket approved successfully.'));
            } else {
                return response()->json(array('status' => true,'message' => 'Docket already approved.'));
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;
    }


//    public function approveDocketById(Request $request){
////        $sentDocket     =   SentDockets::where('id',$id);
//        $sentDocket =SentDockets::where('id',$request->sentDocketId);
//        if(collect($sentDocket)->count()==1):
//            $sentDocketRecipientApprovalQuery    =   SentDocketRecipientApproval::where('sent_docket_id',$sentDocket)->Where('user_id',$request->header('userId'))->where('status',0);
//
//            if($sentDocketRecipientApprovalQuery->count()==1){
//                $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
//                $sentDocketRecipientApproval->status     =   1;
//                $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
//                $sentDocketRecipientApproval->name =$request->name;
//                $signature              =   Input::file('signature');
//                if($request->hasFile('signature')) {
//                    if ($signature->isValid()) {
//                        $ext = $signature->getClientOriginalExtension();
//                        $filename = basename($request->file('signature')->getClientOriginalName(), '.' . $request->file('signature')->getClientOriginalExtension()) . time() . "." . $ext;
//                        $dest = 'files/docket/images';
//                        $signature->move($dest, $filename);
//                        $path = $dest . '/' . $filename;
//                        $sentDocketRecipientApproval->signature=$path;
//                    }
//                }
//                $sentDocketRecipientApproval->save();
//                $sentDocketSenderInfo    =    User::where('id',$sentDocket->first()->user_id)->first();
//                $sentDocketReceiverInfo    =    User::where('id',$request->header('userId'))->first();
//
//
//                if(SentDocketRecipientApproval::where('sent_docket_id',$sentDocket)->where('status',0)->count()==0){
//                    $sentDocketUpdate   =    SentDockets::findOrFail($sentDocket);
//                    $sentDocketUpdate->status   =    1;
//                    $sentDocketUpdate->save();
//
//                    if(SentDocketRecipientApproval::where('sent_docket_id',$sentDocket)->count()>1) {
//                        if ($sentDocketSenderInfo->device_type == 2) {
//                            $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
//                        }
//                        if ($sentDocketSenderInfo->device_type == 1) {
//                            $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
//                        }
//                    }
//                }
//
//                if($sentDocketSenderInfo->device_type==2){
//                    $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
//                }
//                if($sentDocketSenderInfo->device_type==1){
//                    $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.");
//                }
//
//                $userNotification   =    new UserNotification();
//                $userNotification->sender_user_id   =    $request->header('userId');
//                $userNotification->receiver_user_id =   SentDockets::find($sentDocket)->user_id;
//                $userNotification->type     =   3;
//                $userNotification->title    =   'Docket Approved';
//                $userNotification->message  =   $sentDocketReceiverInfo->first_name. " ". $sentDocketReceiverInfo->last_name." has approved your docket.";
//                $userNotification->key      =   $sentDocket;
//                $userNotification->status   =   0;
//                $userNotification->save();
//
//                return response()->json(array('status' => true,'message' => 'Docket approved successfully.'));
//            } else {
//                return response()->json(array('status' =>true,'message' => 'Docket already approved.'));
//            }
//        else:
//            return response()->json(array('status' => false,'message' => 'Docket not found.'));
//        endif;
//    }



    //-----------------email user section ----------------------//
    public function  postEmailUser(Request $request){
        $validator  =   Validator::make(Input::all(),['email' =>     'required']);
        $user=User::where('email', $request->email);
        // if($user->count()!=0){
        //     return response()->json(array("status" => false, "message" => 'That email is already registered to Record Time as ' .$user->first()->first_name.' '.$user->first()->last_name.' Please connect with '. $user->first()->first_name.' '.$user->first()->last_name .' user via "Find Client" in backend.'));
        // }
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
            if($validator->validate($request->email)) {
                $emailUser = EmailUser::where('email', $request->email);
                if ($emailUser->count() != 0) {
                    if (Email_Client::where('email_user_id', $emailUser->first()->id)->where('company_id', $request->header('companyId'))->count() != 0) {
                        return response()->json(array("status" => false, "message" => 'This email is already added on your Custom Clients as ' . @Email_Client::where('email_user_id', $emailUser->first()->id)->first()->full_name));
                    } else {
                        $profile = array('id' => $emailUser->first()->id, 'email' => $emailUser->first()->email);
                        return response()->json(array("status" => true, 'profile' => $profile));
                    }
                } else {
                    $usercustom = new EmailUser();
                    $usercustom->email = $request->email;
                    $usercustom->name = "";
                    $usercustom->company_name = "";
                    $usercustom->save();
                    $profile = array('id' => $usercustom->id, 'email' => $usercustom->email);
                    return response()->json(array("status" => true, "message" => 'Email client add successfully.', 'profile' => $profile));
                    }
            }else{
                return response()->json(array("status" => false, "message" => 'Invalid Email address.'));
            }
        endif;







//        $emailUser=EmailUser::where('email',$request->email);
//        if ($emailUser->count()!=0){
//
//            return response()->json(array("status" => false, "message" => 'This email is already added on your Custom Clients as '.@Email_Client::where('email_user_id', $emailUser->first()->id)->first()->full_name));
//        }
//
//        if ($validator->fails()):
//            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
//            return response()->json(array('status' => false,'message' => $errors));
//        else:
//            $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
//            if($validator->validate($request->email)!= "false" && $validator->validate($request->email)!= null) {
//                $queryEmailUser =   EmailUser::where('email',$request->email);
//                if(User::where('email',$request->email)->count()>0){
//                    return response()->json(array('status' => false,'message' => "That email is already registered to Record Time as ".
//                        User::where('email',$request->email)->first()->first_name." ". User::where('email',$request->email)->first()->last_name));
//                }else {
//                    if ($queryEmailUser->count() > 0) {
//                        $userInfo = $queryEmailUser->first();
//                        $profile = array('id' => $userInfo->id, 'email' => $userInfo->email);
//                    } else {
//                        $user = new EmailUser();
//                        $user->email = $request->email;
//                        $user->name     =   "";
//                        $user->company_name  =   "";
//                        $user->save();
//                        $profile = array('id' => $user->id, 'email' => $user->email);
//                    }
//                }
//                return response()->json(array('status' => true, 'profile' => $profile));
//            }else{
//                return response()->json(array("status" => false, "message" => 'Invalid Email address.'));
//            }
//        endif;
    }


    public function approveEmailedDocket(Request $request,$id,$hashKey){

        $sentDocketQuery     =    EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('hashKey',$hashKey);
        $sentdockets= EmailSentDocket::where('id',$id)->first();
        if($sentDocketQuery->count()==1){
          if ($sentdockets->docketApprovalType == 0){
            $sentDocket =   $sentDocketQuery->first();
            if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                $sentDocket->hashKey = '';
                $sentDocket->status     =   1;
                $sentDocket->save();
                    if(EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('hashKey','!=','')->count()==0){
                        EmailSentDocket::where('id',$id)->update(['status'=>1]);

                    }

                $emailSentDocket    =    EmailSentDocket::find($id);

                if($emailSentDocket->senderUserInfo->device_type == 2){
                    sendiOSNotification($emailSentDocket->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->emailUserInfo->email." has approved your docket",array('type'=>5));
                }else if($emailSentDocket->senderUserInfo->device_type == 1){
                    sendAndroidNotification($emailSentDocket->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->emailUserInfo->email." has approved your docket",array('type'=>5));
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =    $sentDocket->email_user_id;
                $userNotification->receiver_user_id =   EmailSentDocket::find($id)->user_id;
                $userNotification->type     =   5;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $sentDocket->emailUserInfo->email." has approved your docket.";
                $userNotification->key      =   $id;
                $userNotification->status   =   0;
                $userNotification->save();
                $docketApprovalType =$sentDocket->docketApprovalType;
                $message    =   "Requested docket has been approved successfully.";
                return view('errors.errorPage', compact('message','docketApprovalType'));
            }else {
                $message    =   "Your link has expired.";
                return view('errors.errorPage', compact('message'));
            }

          }else{

              $sentDockets =   $sentDocketQuery->first();
              $sentDocket     =   EmailSentDocket::findOrFail($id);
              $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
              $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
              return view('errors.signatureErrorPage', compact('sentDocket','sentDockets','docketTimer','docketFields'));

          }

        }else{

            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
    }

    public function approveEmailedDocketByApprovalType(Request $request){
        $sentDocketQuery     =    EmailSentDocketRecipient::where('email_sent_docket_id',$request->sentDocketId)->where('hashKey',$request->hashKey);
        if($sentDocketQuery->count()==1){
                $sentDocket =   $sentDocketQuery->first();
                if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                    $sentDocket->hashKey = '';
                    $sentDocket->status     =   1;
                    $sentDocket->approval_time =Carbon::now()->toDateTimeString();
                    $sentDocket->name =$request->name;
                    $image = $request->signature;  // your base64 encoded
                    $image = str_replace('data:image/png;base64,', '', $image);
                    $image = str_replace(' ', '+', $image);
                    $imageName = 'public/files/docket/images/signature'.time().'.'.'png';
                    //  \File::put(public_path(). '/signature/' . $imageName, base64_decode($image));
                    \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                    $sentDocket->signature=$imageName;
                    $sentDocket->save();
                    if(EmailSentDocketRecipient::where('email_sent_docket_id',$request->sentDocketId)->where('approval',1)->where('hashKey','!=','')->count()==0){
                        EmailSentDocket::where('id',$request->sentDocketId)->update(['status'=>1]);

                    }

                    $emailSentDocket    =    EmailSentDocket::find($request->sentDocketId);

                    if($emailSentDocket->senderUserInfo->device_type == 2){
                        sendiOSNotification($emailSentDocket->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->emailUserInfo->email." has approved your docket",array('type'=>5));
                    }else if($emailSentDocket->senderUserInfo->device_type == 1){
                        sendAndroidNotification($emailSentDocket->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->emailUserInfo->email." has approved your docket",array('type'=>5));
                    }

                    $userNotification   =    new UserNotification();
                    $userNotification->sender_user_id   =    $sentDocket->email_user_id;
                    $userNotification->receiver_user_id =   EmailSentDocket::find($request->sentDocketId)->user_id;
                    $userNotification->type     =   5;
                    $userNotification->title    =   'Docket Approved';
                    $userNotification->message  =   $sentDocket->emailUserInfo->email." has approved your docket.";
                    $userNotification->key      =   $request->sentDocketId;
                    $userNotification->status   =   0;
                    $userNotification->save();
                    $docketApprovalType =$sentDocket->docketApprovalType;
                    $message    =   "Requested docket has been approved successfully.";
//                    return view('errors.errorPage', compact('message','docketApprovalType'));
                    return response()->json(['status' => true, 'message' => $message]);
                }else {
                    $message    =   "Your link has expired.";
//                    return view('errors.errorPage', compact('message'));
                    return response()->json(['status' => false, 'message' => $message]);

                }

        }else{

            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
    }




    function generateRandomString($length = 60) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function companyDockets(Request $request){
        //find requested user is super admin or not
        $user   =    User::find($request->header('userId'));
        $allDocketTemplates    =   Docket::select('id','title')->where('company_id',$request->header('companyId'))->orderBy('id','desc')->get();
        $activeDocket          =   array();

        if($user->user_type==2){
            foreach ($allDocketTemplates as $docketTemplate){

                if(SentDockets::where('docket_id',$docketTemplate["id"])->count()>0){
                    $activeDocket[] =   $docketTemplate;
                }
            }
        }else{
            foreach ($allDocketTemplates as $docketTemplate){
                if(AssignedDocket::where('docket_id',$docketTemplate["id"])->where('user_id',$request->header('userId'))->count()>0){
                    $activeDocket[] =   $docketTemplate;
                }
            }
        }
        $docketTemplate     =    Docket::select('id','title')->where('company_id',$request->header('companyId'))->orderBy('id','desc')->get();
        return response()->json(array('status' => true, 'docketTemplate' => $activeDocket));
    }

    function filterDocket(Request $request){


        if ($request->emailFlag == "true"){

            $sentemailDocketsQuery =  EmailSentDocket::query();
            $sentemailDocketsQuery->where('user_id',$request->header('userId'));
            if ($request->date_type){
                if($request->date_type == "2"){
                    if($request->from ){
                        $sentemailDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                    }
                    if($request->to ){
                        $sentemailDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                    }

                }
            }

            if ($request->date_type){
                if($request->date_type == "1"){
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentemailDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                try{
                                    Carbon::parse($rowValue->value);
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }catch(\Exception $e) {
                                    break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = EmailSentDocket::whereIn('id', $tempSentDocket);
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);


                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = EmailSentDocketValue::whereIn('docket_field_id', $docketFieldsIds)->where('email_sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentemailDocketsQuery = EmailSentDocket::whereIn('id', $tempSentDocket);

                    }
                }
            }

            if($request->docketTemplateId && $request->docketTemplateId != ""){
                $sentemailDocketsQuery->whereIn('docket_id',$request->docketTemplateId);
            }
            if($request->docketId && $request->docketId != ""){
                $sentemailDocketsQuery->where('company_docket_id',$request->docketId);
            }

            $sentEmailDockets     =   $sentemailDocketsQuery->get();

            $dockets        =   array();
            foreach($sentEmailDockets as $result){

                $userId  = 	$result->user_id;
                $userName  =   $result->sender_name;
                $company    =   $result->company_name;

//                if($result->user_id==$request->header('userId')){
//                    if($result->status==0):
                $docketStatus   =   "Sent";
//                    endif;
//                }
//                if($result->status==1)
//                    $docketStatus ="Approved";


                $recipientsQuery    =   $result->recipientInfo;

                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   @$recipient->emailUserInfo->email;
                    else
                        $recipientData  =   $recipientData.", ".@$recipient->emailUserInfo->email;
                }






                //approval text
                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }


                $dockets[]   =   array('id' => $result->id,
                    'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                    'user_id'   =>  $userId,
                    'docketName' => $result->docketInfo->title,
                    'docketId' => $result->docketInfo->id,
                    'sender' => $userName,
                    'profile' => AmazoneBucket::url() . $result->senderUserInfo->image,
                    'company'   =>  $company,
                    'recipient' => $recipientData,
                    'recipients' => $recipientData,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                    'approvalText'  =>  $approvalText,
                    'isApproved'    =>  $result->status,
                    'preview' => "",
                    'status'    => $docketStatus);
            }

            //        conversation sorting according to dateAdded
            $size = count($dockets);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
                        $tempArray   =    $dockets[$j+1];
                        $dockets[$j+1] = $dockets[$j];
                        $dockets[$j]  =   $tempArray;
                    }
                }
            }
            return response()->json(array('dockets' => $dockets));


        }else if ($request->emailFlag == "false"){

            //get total company employee ids
            // $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
            // $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;

            $receivedSentDocketIds  =   SentDocketRecipient::where('user_id',$request->header('userId'))->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
            $sentDocketIds          =   SentDockets::where('user_id',$request->header('userId'))->pluck('id')->toArray();
            $sentDocketId          =   SentDockets::where('user_id',$request->header('userId'))->get();


            if(count($receivedSentDocketIds)!=0 && count($sentDocketIds) != 0){
                $totalSentDocketIds     =   array_unique(array_merge($sentDocketIds, $receivedSentDocketIds));
            }else if(count($receivedSentDocketIds)!=0){
                $totalSentDocketIds =  $sentDocketIds;
            }else{
                $totalSentDocketIds =  $receivedSentDocketIds;
            }
            $sentDocketsQuery =  SentDockets::query();


            if ($request->date_type){
                if($request->date_type == "2"){
                    if($request->from ){
                        $sentDocketsQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
                    }
                    if($request->to ){
                        $sentDocketsQuery->whereDate('created_at','<=',Carbon::parse($request->to)->format('Y-m-d'));
                    }

                }
            }

            if($request->date_type) {
                if ($request->date_type == "1") {
                    if ($request->from) {
                        $carbonDateFrom = Carbon::parse($request->from);
                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                try{
                                    Carbon::parse($rowValue->value);
                                    if ($rowValue->value != "" && $rowValue->value != "null") {
                                        if ($carbonDateFrom->lte(Carbon::parse($rowValue->value)))
                                            $flag = true;
                                    }
                                    if ($flag == true)
                                        break;
                                }catch(\Exception $e) {
                                    break;
                                }
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);
                    }

                    if ($request->to) {
                        $carbonDateTo = Carbon::parse($request->to);


                        unset($tempSentDocket);
                        $tempSentDocket = array();
                        foreach ($sentDocketsQuery->get() as $row) {
                            $flag = false;
                            $docketTemplate = $row->docketInfo();
                            $docketFieldsIds = $docketTemplate->first()->getDocketFieldsByCategoryId(6)->get()->pluck('id');

                            $getAllSentDocketDateFieldsValues = SentDocketsValue::whereIn('docket_field_id', $docketFieldsIds)->where('sent_docket_id', $row->id)->get();
                            foreach ($getAllSentDocketDateFieldsValues as $rowValue) {
                                if ($rowValue->value != "" && $rowValue->value != "null") {
                                    if ($carbonDateTo->gte(Carbon::parse($rowValue->value)))
                                        $flag = true;
                                }
                                if ($flag == true)
                                    break;
                            }

                            if ($flag == true) {
                                $tempSentDocket[] = $row->id;
                            }
                        }
                        unset($sentDocketsQuery);
                        $sentDocketsQuery = SentDockets::whereIn('id', $tempSentDocket);

                    }


                }
            }


            if($request->docketTemplateId && $request->docketTemplateId != ""){
                $sentDocketsQuery->whereIn('docket_id',$request->docketTemplateId);
            }
            if($request->docketId && $request->docketId != ""){
                $sentDocketsQuery->where('company_docket_id',$request->docketId);
            }

            $sentDocketsQuery->whereIn('id',$totalSentDocketIds);
//        $sentDocketsQuery->where(function($query) use ($request){
//            return $query->where('user_id',$request->header('userId'));
//        });
            $sentDockets     =   $sentDocketsQuery->get();





            $dockets        =   array();
            foreach($sentDockets as $result){
                $userId  = 	$result->user_id;
                $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;


                if ($result->status == 3){
                    $docketStatus = "Rejected";
                }else{
                    if($result->is_cancel == 1){
                        $docketStatus = "Cancelled";
                    }else{
                        if($result->user_id==$request->header('userId')){

                            $docketStatus   =   "Sent";

                        } else {

                            $docketStatus   =   "Received";

                        }
                    }
                }




                // if($result->status==1)
                //     $docketStatus ="Approved";


                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   @$recipient->userInfo->first_name." ".@$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".@$recipient->userInfo->first_name." ".@$recipient->userInfo->last_name;
                }




                //approval text
                $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }


                //canreject
                $canRejectDocket = SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'));
                $canReject = 0;
                $isReject = 0;
                if($canRejectDocket->count() > 0 ){
                    if ($canRejectDocket->first()->status == 0){
                        if ($result->status == 0) {
                            $canReject = 1;
                        }else{
                            $canReject = 0;
                        }
                    }else{
                        $canReject = 0;
                    }
                    if ($result->status == 3){
                        $isReject = 1;
                    }else{
                        $isReject = 0;
                    }

                }



                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                $dockets[]   =   array('id' => $result->id,
                    'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
                    'user_id'   =>  $userId,
                    'docketName' => $result->docketInfo->title,
                    'docketId' => $result->docketInfo->id,
                    'sender' => $userName,
                    'profile' => AmazoneBucket::url() . $result->senderUserInfo->image,
                    'company'   =>  $company,
                    'recipient' => $recipientData,
                    'recipients' => $recipientData,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                    'approvalText'  =>  $approvalText,
                    'isApproval'    =>  $isApproval,
                    'isApproved'    =>  $isApproved,
                    'canReject'=>$canReject,
                    'isReject' => $isReject,
                    'preview' => "",
                    'status'    => $docketStatus);
            }

            //        conversation sorting according to dateAdded
            $size = count($dockets);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
                        $tempArray   =    $dockets[$j+1];
                        $dockets[$j+1] = $dockets[$j];
                        $dockets[$j]  =   $tempArray;
                    }
                }
            }


            return response()->json(array('dockets' => $dockets));
        }


    }


    function filterDocument(Request $request){
        //get total company employee ids
        $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
        $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;

        if($request->document_type == 1){

            $receivedSentDocketIds  =   SentDocketRecipient::whereIn('user_id',$employeeIds)->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
            $sentDocketIds          =   SentDockets::whereIn('user_id',$employeeIds)->pluck('id')->toArray();

            if(count($receivedSentDocketIds)!=0 && count($sentDocketIds) != 0){
                $totalSentDocketIds     =   array_unique(array_merge($sentDocketIds, $receivedSentDocketIds));
            }else if(count($receivedSentDocketIds)!=0){
                $totalSentDocketIds =  $sentDocketIds;
            }else{
                $totalSentDocketIds =  $receivedSentDocketIds;
            }

            $sentDocketsQuery =  SentDockets::query();


            if($request->from && $request->from != ""){
                $sentDocketsQuery->whereDate('created_at','>=',$request->from);
            }
            if($request->to && $request->to != ""){
                $sentDocketsQuery->whereDate('created_at','<=',$request->to);
            }

            $sentDocketsQuery->whereIn('id',$totalSentDocketIds);

            $sentDockets     =   $sentDocketsQuery->get();

            if($request->search != ""){

                    $matchedIDArray     =   array();

                    $searchKey = $request->search;

                    //check docket id
                    $matchedIDArray =   SentDockets::where('id','like','%'.$searchKey.'%')->whereIn('id',$totalSentDocketIds)->pluck('id')->toArray();
                    if(count($matchedIDArray)>0){
                        $totalSentDocketIds  =   array_merge(array_diff($totalSentDocketIds,$matchedIDArray),array_diff($matchedIDArray,$totalSentDocketIds));
                    }
                    $receiverDocketQuery    =    SentDockets::whereIn('id',$totalSentDocketIds)->get();


                    //check docket info(sender name, sender company name , receiver name, company name //
                    foreach ($receiverDocketQuery as $row){

                        $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
                        $senderCompanyName  =   $row->senderCompanyInfo->name;
                        if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                            $matchedIDArray[]   =   $row->id;
                            continue;
                        }
                        //receiver info
                        $receiversName  =   "";
                        $receiversCompanyName   =   "";
                        //for receivers name
                        if($row->recipientInfo){
                            $sn = 1;
                            foreach($row->recipientInfo as $recipient):
                                $receiversName  =   $receiversName.@$recipient->userInfo->first_name." ". @$recipient->userInfo->last_name;
                                if($sn!=$row->recipientInfo->count()):
                                    $receiversName  =   $receiversName.", ";
                                endif;
                                $sn++;
                            endforeach;

                        }
                        //for receivers company name
                        $recipientIds   =   $row->recipientInfo->pluck('user_id');
                        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

                        if(preg_match("/".$searchKey."/i",$receiversName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                            $matchedIDArray[]   =   $row->id;
                            continue;
                        }

                        if(preg_match("/".$searchKey."/i",$row->docketInfo->title)){
                            $matchedIDArray[]   =   $row->id;
                            continue;
                        }

                        if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                            $matchedIDArray[]   =   $row->id;
                            continue;
                        }

                        //for docket field value
                        if($row->sentDocketValue){
                            foreach ($row->sentDocketValue as $rowValue){
                                if($rowValue->docketFieldInfo->docket_filed_category_id!=5 && $rowValue->docketFieldInfo->docket_filed_category_id!=7 && $rowValue->docketFieldInfo->docket_filed_category_id!=8 && $rowValue->docketFieldInfo->docket_filed_category_id!=9 &&
                                    $rowValue->docketFieldInfo->docket_filed_category_id!=12 && $rowValue->docketFieldInfo->docket_filed_category_id!=13 && $rowValue->docketFieldInfo->docket_filed_category_id!=14){

                                    if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                                        $matchedIDArray[]   =   $row->id;
                                    }
                                }
                            }
                        }

                    }

                $sentDockets     =    SentDockets::whereIn('id',$matchedIDArray)->get();
            }

            $dockets        =   array();
            foreach($sentDockets as $result){

                $userId  = 	$result->user_id;
                $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $company    =   $result->senderCompanyInfo->name;

                if($result->user_id==$request->header('userId')){
                    if($result->status==0):
                        $docketStatus   =   "Sent";
                    endif;
                } else {

                    if($result->status==0):
                        $docketStatus   =   "Received";
                    endif;
                }

                if($result->status==1)
                    $docketStatus ="Approved";


                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                }

                //approval text
                $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;
                if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }


                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                $dockets[]   =   array('id' => $result->id,
                    'user_id'   =>  $userId,
                    'docketName' => $result->docketInfo->title,
                    'sender' => $userName,
                    'profile' => AmazoneBucket::url() . $result->senderUserInfo->image,
                    'company'   =>  $company,
                    'recipient' => $recipientData,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                    'isApproved'    =>  $result->status,
                    'approvalText'  =>  $approvalText,
                    'isApproval'    =>  $isApproval,
                    'isApproved'    =>  $isApproved,
                    'status'    => $docketStatus);
            }

            //        conversation sorting according to dateAdded
            $size = count($dockets);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
                        $tempArray   =    $dockets[$j+1];
                        $dockets[$j+1] = $dockets[$j];
                        $dockets[$j]  =   $tempArray;
                    }
                }
            }

            return response()->json(array('dockets' => $dockets));
        }elseif($request->document_type == 2){

            $sentInvoiceIds = SentInvoice::whereIn('user_id',$employeeIds)->pluck('id')->toArray();

            if(count($sentInvoiceIds) != 0){
                $totalSentInvoiceIds = $sentInvoiceIds;
            }

            $sentInvoicesQuery = SentInvoice::query();

            if ($request->from){
                $sentInvoicesQuery->whereDate('created_at', '>=', $request->from);
            }

            if ($request->to){
                $sentInvoicesQuery->whereDate('created_at', '<=', $request->to);
            }

            $sentInvoicesQuery->whereIn('id',$totalSentInvoiceIds);

            $sentInvoices     =   $sentInvoicesQuery->get();

            if($request->search != ""){

                $matchedIDArray = array();

                $searchKey = $request->search;

                //check docket id
                $matchedIDArray = SentInvoice::where('id', 'like', '%' . $searchKey . '%')->whereIn('id', $totalSentInvoiceIds)->pluck('id')->toArray();
                if (count($matchedIDArray) > 0) {
                    $totalSentInvoiceIds = array_merge(array_diff($totalSentInvoiceIds, $matchedIDArray), array_diff($matchedIDArray, $totalSentInvoiceIds));
                }

                //check docket info(sender name, sender company name , receiver name, company name //
                $sentInvoiceQuery = SentInvoice::whereIn('id', $totalSentInvoiceIds)->get();

                foreach ($sentInvoiceQuery as $row){

                    $receiverName=$row->receiverUserInfo->first_name." ".$row->receiverUserInfo->last_name;
                    $receiverCompanyName  =   $row->senderCompanyInfo->name;
                    if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }


                    $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
                    $senderCompanyName  =   $row->senderCompanyInfo->name;
                    if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }



                    if(preg_match("/".$searchKey."/i",$row->invoiceInfo->title)){
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }

                    if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                        $matchedIDArray[]   =   $row->id;
                        continue;
                    }
                }

                $sentInvoices     =    SentInvoice::whereIn('id',$matchedIDArray)->get();
            }


            $invoices = array();

            foreach($sentInvoices as $result){

                $profile = "";
                if($result->user_id==$request->header('userId')){

                    $userId  = 	$result->receiver_user_id;
                    $userName  =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                    $profile    =    AmazoneBucket::url() . $result->receiverUserInfo->image;
                    $company    =   $result->receiverCompanyInfo->name;

                    if($result->status==0):
                        $invoiceStatus   =   "Sent";
                    endif;
                } else {
                    $userId  = 	$result->user_id;
                    $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                    $profile    =    AmazoneBucket::url() . $result->senderUserInfo->image;
                    $company    =   $result->senderCompanyInfo->name;

                    if($result->status==0):
                        $invoiceStatus   =   "Received";
                    endif;
                }

                if($result->status==1)
                    $invoiceStatus ="Approved";

                $invoices[]   =   array('id' => $result->id,
                    'user_id'   =>  $userId,
                    'invoiceName' => $result->invoiceInfo->title,
                    'sender' => $userName,
                    'profile' => $profile,
                    'company'   =>  $company,
                    'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                    'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y h:i:s'),
                    'status'    => $invoiceStatus);
            }

            //conversation sorting according to dateAdded
            $size = count($invoices);
            for($i = 0; $i<$size; $i++){
                for ($j=0; $j<$size-1-$i; $j++) {
                    if (strtotime($invoices[$j+1]["dateSorting"]) > strtotime($invoices[$j]["dateSorting"])) {
                        $tempArray   =    $invoices[$j+1];
                        $invoices[$j+1] = $invoices[$j];
                        $invoices[$j]  =   $tempArray;
                    }
                }
            }

            return response()->json(array('invoices' => $invoices));

        }

    }

    function searchByKeywordDocket(Request $request){
        $searchKey = $request->search;
        $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
        $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;
        $receivedSentDocket  =   SentDocketRecipient::whereIn('user_id',$employeeIds)->distinct('sent_docket_id')->pluck('sent_docket_id')->toArray();
        $sentDocket    =   SentDockets::where('sender_company_id',$request->header('companyId'))->pluck('id')->toArray();
        $possibleSentDocketsID    =   SentDockets::whereIn('id',array_unique(array_merge($receivedSentDocket,$sentDocket)))->pluck('id')->toArray();
        $filteredSentDockets    =   array();
        $matchedIDArray     =   array();

        //check docket id
        $matchedIDArray =   SentDockets::where('id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
        if(count($matchedIDArray)>0){
            $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
        }
        $receiverDocketQuery    =    SentDockets::whereIn('id',$possibleSentDocketsID)->get();

        //check docket info(sender name, sender company name , receiver name, company name //
        foreach ($receiverDocketQuery as $row){

            $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
            $senderCompanyName  =   $row->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }
            //receiver info
            $receiversName  =   "";
            $receiversCompanyName   =   "";
            //for receivers name
            if($row->recipientInfo){
                $sn = 1;
                foreach($row->recipientInfo as $recipient):
                    $receiversName  =   $receiversName.@$recipient->userInfo->first_name." ". @$recipient->userInfo->last_name;
                    if($sn!=$row->recipientInfo->count()):
                        $receiversName  =   $receiversName.", ";
                    endif;
                    $sn++;
                endforeach;

            }
            //for receivers company name
            $recipientIds   =   $row->recipientInfo->pluck('user_id');
            $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
            $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
            $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
            $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();

            if(preg_match("/".$searchKey."/i",$receiversName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",$row->docketInfo->title)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            //for docket field value
            if($row->sentDocketValue){
                foreach ($row->sentDocketValue as $rowValue){
                    if($rowValue->docketFieldInfo->docket_filed_category_id!=5 && $rowValue->docketFieldInfo->docket_filed_category_id!=7 && $rowValue->docketFieldInfo->docket_filed_category_id!=8 && $rowValue->docketFieldInfo->docket_filed_category_id!=9 &&
                        $rowValue->docketFieldInfo->docket_filed_category_id!=12 && $rowValue->docketFieldInfo->docket_filed_category_id!=13 && $rowValue->docketFieldInfo->docket_filed_category_id!=14){

                        if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                            $matchedIDArray[]   =   $row->id;
                        }
                    }
                }
            }

        }

        $sentDockets    =   SentDockets::whereIn('id',$matchedIDArray)->orderBy('created_at','desc')->get();

        foreach($sentDockets as $result){
            $userId  = 	$result->user_id;
            $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
            $company    =   $result->senderCompanyInfo->name;

            if($result->user_id==$request->header('userId')){
                if($result->status==0):
                    $docketStatus   =   "Sent";
                endif;
            } else {

                if($result->status==0):
                    $docketStatus   =   "Received";
                endif;
            }

            if($result->status==1)
                $docketStatus ="Approved";


            $recipientsQuery    =   $result->recipientInfo;
            $recipientData      =   "";
            foreach($recipientsQuery as $recipient) {
                if($recipient->id==$recipientsQuery->first()->id)
                    $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                else
                    $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
            }

            //approval text
            $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
            $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

            //check is approval
            $isApproval                 =   0;
            $isApproved                 =   0;
            if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->count()==1){
                $isApproval             =   1;

                //check is approved
                if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                    $isApproved             =   1;
                }
            }


            $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

            $dockets[]   =   array('id' => $result->id,
                'user_id'   =>  $userId,
                'docketName' => $result->docketInfo->title,
                'sender' => $userName,
                'profile' => AmazoneBucket::url() . $result->senderUserInfo->image,
                'company'   =>  $company,
                'recipient' => $recipientData,
                'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                'isApproved'    =>  $result->status,
                'approvalText'  =>  $approvalText,
                'isApproval'    =>  $isApproval,
                'isApproved'    =>  $isApproved,
                'status'    => $docketStatus);
        }

        $size = count($dockets);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($dockets[$j+1]["dateSorting"]) > strtotime($dockets[$j]["dateSorting"])) {
                    $tempArray   =    $dockets[$j+1];
                    $dockets[$j+1] = $dockets[$j];
                    $dockets[$j]  =   $tempArray;
                }
            }
        }

        return response()->json(array('dockets' => $dockets));


    }

    function searchByKeywordEmailDocket(Request $request){
        $searchKey = $request->search;
        $totalDocketLabel =   \App\DocketLabel::where('company_id',$request->header('companyId'))->count();
        $possibleSentDocketsID    =   EmailSentDocket::where('company_id',$request->header('companyId'))->orderBy('created_at','desc')->pluck('id')->toArray();
        $employes = Employee::where('company_id',$request->header('companyId'))->pluck('user_id')->toArray();
        $company   =   array(Company::where('id',$request->header('companyId'))->first()->user_id);
        $total =  array_merge($company, $employes);
        $docketusedbyemail = EmailSentDocket::select('docket_id')->whereIn('user_id',$total)->groupBy('docket_id')->get();

        $filteredSentDockets    =   array();
        $matchedIDArray     =   array();

        //check docket id
        $matchedIDArray =   EmailSentDocket::where('id','like','%'.$searchKey.'%')->whereIn('id',$possibleSentDocketsID)->pluck('id')->toArray();
        if(count($matchedIDArray)>0){
            $possibleSentDocketsID  =   array_merge(array_diff($possibleSentDocketsID,$matchedIDArray),array_diff($matchedIDArray,$possibleSentDocketsID));
        }

        //check docket info(sender name, sender company name , receiver name, company name //
        $sentDocketQuery    =    EmailSentDocket::whereIn('id',$possibleSentDocketsID)->get();
        foreach ($sentDocketQuery as $row){
            //sender info
            $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
            $senderCompanyName  =   $row->senderCompanyInfo->name;
            if((preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName))){

                $matchedIDArray[]   =   $row->id;
                continue;
            }

            //for receivers Email Company name Company address Company full name
            $receiverEmailed= $row->receiverUserInfo->email;
            if(preg_match("/".$searchKey."/i",$receiverEmailed)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            $receiverFullName = $row->receiver_full_name;
            $receiverCompanyAddress = $row->receiver_company_address;
            $receiverCompanyName = $row->receiver_company_name;

            if (preg_match("/".$searchKey."/i",$receiverFullName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }
            if (preg_match("/".$searchKey."/i",$receiverCompanyAddress)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }
            if (preg_match("/".$searchKey."/i",$receiverCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",$row->docketInfo->title)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            //for docket field value
            if($row->sentDocketValue){
                foreach ($row->sentDocketValue as $rowValue){
                    if($rowValue->docketFieldInfo->docket_filed_category_id!=5 && $rowValue->docketFieldInfo->docket_filed_category_id!=7 && $rowValue->docketFieldInfo->docket_filed_category_id!=8 && $rowValue->docketFieldInfo->docket_filed_category_id!=9 &&
                        $rowValue->docketFieldInfo->docket_filed_category_id!=12 && $rowValue->docketFieldInfo->docket_filed_category_id!=13 && $rowValue->docketFieldInfo->docket_filed_category_id!=14){

                        if(preg_match("/".$searchKey."/i",$rowValue->value)) {
                            $matchedIDArray[]   =   $row->id;
                        }
                    }
                }
            }
        }

        $sentDockets   =   EmailSentDocket::whereIn('id',$matchedIDArray)->orderBy('created_at','desc')->get();

        $sentEmailDockets = array();

        foreach($sentDockets as $result){
            $userId  = 	$result->user_id;
            $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
            $company    =   $result->senderCompanyInfo->name;

            if($result->user_id==$request->header('userId')){
                if($result->status==0):
                    $docketStatus   =   "Sent";
                endif;
            } else {

                if($result->status==0):
                    $docketStatus   =   "Received";
                endif;
            }

            if($result->status==1)
                $docketStatus ="Received";


            $recipientsQuery    =   $result->recipientInfo;
            $recipientData      =   "";
            foreach($recipientsQuery as $recipient) {
                if($recipient->id==$recipientsQuery->first()->id)
                    $recipientData  =   $recipient->receiver_full_name;
                else
                    $recipientData  =   $recipientData.", ".$recipient->receiver_full_name;
            }

            $sentEmailDockets[]   =   array('id' => $result->id,
                'user_id'   =>  $userId,
                'docketName' => $result->docketInfo->title,
                'sender' => $userName,
                'profile' => AmazoneBucket::url() . $result->senderUserInfo->image,
                'company'   =>  $company,
                'recipient' => $recipientData,
                'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y H:i:s'),
                'status'    => $docketStatus);
        }

        $size = count($sentEmailDockets);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($sentEmailDockets[$j+1]["dateSorting"]) > strtotime($sentEmailDockets[$j]["dateSorting"])) {
                    $tempArray   =    $sentEmailDockets[$j+1];
                    $sentEmailDockets[$j+1] = $sentEmailDockets[$j];
                    $sentEmailDockets[$j]  =   $tempArray;
                }
            }
        }

        return response()->json(array('sentEmailDockets' => $sentEmailDockets));


    }

    function searchByKeywordInvoice(Request $request){

        $employeeIds    =   Employee::where('company_id',$request->header('companyId'))->pluck('user_id');
        $employeeIds[]  =   Company::find($request->header('companyId'))->user_id;

        $sentInvoiceIds = SentInvoice::whereIn('user_id',$employeeIds)->pluck('id')->toArray();

        if(count($sentInvoiceIds) != 0){
            $totalSentInvoiceIds = $sentInvoiceIds;
        }

        $matchedIDArray = array();

        $searchKey = $request->search;

        //check docket id
        $matchedIDArray = SentInvoice::where('id', 'like', '%' . $searchKey . '%')->whereIn('id', $totalSentInvoiceIds)->pluck('id')->toArray();
        if (count($matchedIDArray) > 0) {
            $totalSentInvoiceIds = array_merge(array_diff($totalSentInvoiceIds, $matchedIDArray), array_diff($matchedIDArray, $totalSentInvoiceIds));
        }

        //check docket info(sender name, sender company name , receiver name, company name //
        $sentInvoiceQuery = SentInvoice::whereIn('id', $totalSentInvoiceIds)->get();

        foreach ($sentInvoiceQuery as $row){

            $receiverName=$row->receiverUserInfo->first_name." ".$row->receiverUserInfo->last_name;
            $receiverCompanyName  =   $row->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$receiverName) || preg_match("/".$searchKey."/i",$receiverCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }


            $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
            $senderCompanyName  =   $row->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }



            if(preg_match("/".$searchKey."/i",$row->invoiceInfo->title)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                $matchedIDArray[]   =   $row->id;
                continue;
            }
        }

        $sentInvoices     =    SentInvoice::whereIn('id',$matchedIDArray)->get();


        $invoices = array();

        foreach($sentInvoices as $result){

            $profile = "";
            if($result->user_id==$request->header('userId')){

                $userId  = 	$result->receiver_user_id;
                $userName  =   $result->receiverUserInfo->first_name. " ".$result->receiverUserInfo->last_name;
                $profile    =    AmazoneBucket::url() . $result->receiverUserInfo->image;
                $company    =   $result->receiverCompanyInfo->name;

                if($result->status==0):
                    $invoiceStatus   =   "Sent";
                endif;
            } else {
                $userId  = 	$result->user_id;
                $userName  =   $result->senderUserInfo->first_name. " ".$result->senderUserInfo->last_name;
                $profile    =    AmazoneBucket::url() . $result->senderUserInfo->image;
                $company    =   $result->senderCompanyInfo->name;

                if($result->status==0):
                    $invoiceStatus   =   "Received";
                endif;
            }

            if($result->status==1)
                $invoiceStatus ="Approved";

            $invoices[]   =   array('id' => $result->id,
                'user_id'   =>  $userId,
                'invoiceName' => $result->invoiceInfo->title,
                'sender' => $userName,
                'profile' => $profile,
                'company'   =>  $company,
                'dateAdded' =>  Carbon::parse($result->created_at)->format('d-M-Y'),
                'dateSorting' =>  Carbon::parse($result->created_at)->format('d-M-Y h:i:s'),
                'status'    => $invoiceStatus);
        }

        //conversation sorting according to dateAdded
        $size = count($invoices);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($invoices[$j+1]["dateSorting"]) > strtotime($invoices[$j]["dateSorting"])) {
                    $tempArray   =    $invoices[$j+1];
                    $invoices[$j+1] = $invoices[$j];
                    $invoices[$j]  =   $tempArray;
                }
            }
        }

        return response()->json(array('invoices' => $invoices));
    }

    function searchByKeywordEmailInvoice(Request $request){

        $searchKey = $request->search;
        $possibleSentDocketsID     =    EmailSentInvoice::where('company_id',$request->header('companyId'))->orderBy('created_at','desc')->pluck('id')->toArray();
        $filteredSentDockets = array();


        $matchedIDArray = array();

        //check docket id
        $matchedIDArray = EmailSentInvoice::where('id', 'like', '%' . $searchKey . '%')->whereIn('id', $possibleSentDocketsID)->pluck('id')->toArray();
        if (count($matchedIDArray) > 0) {
            $possibleSentDocketsID = array_merge(array_diff($possibleSentDocketsID, $matchedIDArray), array_diff($matchedIDArray, $possibleSentDocketsID));
        }

        //check docket info(sender name, sender company name , receiver name, company name //
        $sentInvoiceQuery = EmailSentInvoice::whereIn('id', $possibleSentDocketsID)->get();

        foreach ($sentInvoiceQuery as $row){

            //for receivers Email Company name Company address Company full name
            $receiverEmailed= $row->receiverInfo->email;
            if(preg_match("/".$searchKey."/i",$receiverEmailed)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            $receiverFullName = $row->receiver_full_name;
            $receiverCompanyAddress = $row->receiver_company_address;
            $receiverCompanyName = $row->receiver_company_name;

            if (preg_match("/".$searchKey."/i",$receiverFullName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }
            if (preg_match("/".$searchKey."/i",$receiverCompanyAddress)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }
            if (preg_match("/".$searchKey."/i",$receiverCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }


            $senderName =   $row->senderUserInfo->first_name." ".$row->senderUserInfo->last_name;
            $senderCompanyName  =   $row->senderCompanyInfo->name;
            if(preg_match("/".$searchKey."/i",$senderName) || preg_match("/".$searchKey."/i",$senderCompanyName)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }



            if(preg_match("/".$searchKey."/i",$row->invoiceInfo->title)){
                $matchedIDArray[]   =   $row->id;
                continue;
            }

            if(preg_match("/".$searchKey."/i",Carbon::parse($row->created_at)->format('d-M-Y'))) {
                $matchedIDArray[]   =   $row->id;
                continue;
            }

        }

        $sentEmailInvoices     =    EmailSentInvoice::whereIn('id',$matchedIDArray)->get();


        $invoices = array();

        foreach ($sentEmailInvoices as $result) {
            if ($result->status == 1)
                $invoiceStatus = "Approved";
            else
                $invoiceStatus = "Sent";

            $invoices[] = array('id' => $result->id,
                'user_id' => $result->receiver_user_id,
                'invoiceName' => $result->invoiceInfo->title,
                'receiver' => $result->receiverInfo->email,
                'company' => "",
                'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                'status' => $invoiceStatus);

        }
        //conversation sorting according to dateAdded
        $size = count($invoices);
        for($i = 0; $i<$size; $i++){
            for ($j=0; $j<$size-1-$i; $j++) {
                if (strtotime($invoices[$j+1]["dateAdded"]) > strtotime($invoices[$j]["dateAdded"])) {
                    $tempArray   =    $invoices[$j+1];
                    $invoices[$j+1] = $invoices[$j];
                    $invoices[$j]  =   $tempArray;
                }
            }
        }
        return response()->json(array('status' => true, 'invoices' => $invoices));
    }


    //*********************invoice section api*******************//




    public function getRandNum()
    {
        $randNum = strval(rand(1000,100000));
        return $randNum;
    }














    public function logout(Request $request){
        User::where('id',$request->header('userId'))->update(array('deviceToken'=>''));
        return response()->json(array('status' => true, 'message' => "logout."));
    }

    //===============================forwarding section=================================//
    public function forwardDocketById(Request $request,$id){
        ini_set('memory_limit','256M');
        set_time_limit(0);
        $sentDocket     =   SentDockets::where('id',$id);

        if($sentDocket->count()==1):
            //check docket associated with user or not
            $companyId  =    $request->header('companyId');

            $validCompanyId     =   array();
            $validCompanyId[]   =   $sentDocket->first()->sender_company_id;
            //get recipient company id
            if($sentDocket->first()->recipientInfo){
                foreach($sentDocket->first()->recipientInfo as $recipient):
                    if(Company::where("user_id",$recipient->user_id)->count()){
                        $validCompanyId[]   =   Company::where("user_id",$recipient->user_id)->first()->id;
                    }else{
                        $validCompanyId[]   =   Employee::where('user_id',$recipient->user_id)->first()->company_id;
                    }
                endforeach;
            }
            if(in_array($companyId,$validCompanyId)){

                $document_name  =  "docket-".$id."-".preg_replace('/[^A-Za-z0-9\-]/', '',str_replace(' ', '-', strtolower(Company::find($sentDocket->first()->sender_company_id)->name)));
                $document_path   =   'files/pdf/docketForward/'.str_replace('.', '',$document_name).'.pdf';
                if(!AmazoneBucket::fileExist($document_path)){
                    $sentDocket    =   $sentDocket->first();
                    $approval_type = array();
                    foreach ($sentDocket->sentDocketRecipientApproval as $items){
                        $approval_type[] = array(
                            'id' => $items->id,
                            'status' =>$items->status,
                            'full_name' => $items->userInfo->first_name." ".$items->userInfo->last_name,
                            'approval_time' =>$items->approval_time,
                            'name'=>$items->name,
                            'signature'=>AmazoneBucket::url() . $items->signature
                        );
                    }


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

                    $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id');
                    $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                    $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                    $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                    $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                    $docketFields   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
                    $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',1)->get();

                    if($sentDocket->theme_document_id == 0){
                        $pdf = PDF::loadView('pdfTemplate.docketForward', compact('sentDocket','company','docketFields','docketTimer','approval_type','request','receiverDetail'));
                    }else{

                        $theme = DocumentTheme::where('id', $sentDocket->theme_document_id)->first();
                        $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('sentDocket','company','docketFields'));
                    }
                    $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                    $output = $pdf->output();
                    $path = storage_path($document_path);
                    file_put_contents($path, $output);
                }
                return response()->json(array('status' => true,'emailDocket' => array('fileName'=>str_replace('.', '',$document_name).'.pdf','filePath' => (\Config::get('app.storage_url_pdf').$document_path),'subject' => "Docket ".$id." ".Company::find(SentDockets::where('id',$id)->first()->sender_company_id)->name)));
            }
            else {
                return response()->json(array('status' => false,'message' => 'Not authorized.'));
            }
        else:
            return response()->json(array('status' => false,'message' => 'Docket not found.'));
        endif;


    }

    public function forwardInvoiceById(Request $request,$id){
        $sentInvoice     =   SentInvoice::findOrFail($id);
        $companyId  =    $request->header('companyId');

        if($sentInvoice->company_id== $companyId || $sentInvoice->receiver_company_id == $companyId){
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

            $invoice_name  =  "invoice-".$id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name));
            $document_path   =   'files/pdf/invoiceForward/'.str_replace('.', '',$invoice_name).'.pdf';
            if(!AmazoneBucket::fileExist($document_path)){
                if($sentInvoice->theme_document_id == 0){

                    $pdf = PDF::loadView('pdfTemplate.docketForward', compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                }else{

                    $theme = DocumentTheme::where('id', $sentInvoice->theme_document_id)->first();
                    $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('invoiceSetting','sentInvoice','invoiceDescription','sentInvoiceValue'));
                }
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(array('status' => true,'emailInvoice' => array('fileName'=>str_replace('.', '',$invoice_name).'.pdf','filePath' => asset('storage/'.$document_path),'subject' => "Invoice ".$id." ".$sentInvoice->senderCompanyInfo->name)));

        }else {
            return response()->json(array('status' => false,'message' => 'Invalid attempt ! Please try with valid action.'));
        }
    }

    public function forwardEmailDocketById(Request $request,$id){
        $sentDocket     =   EmailSentDocket::findOrFail($id);
        $companyId  =    $request->header('companyId');
        if($sentDocket->company_id==$companyId){
            $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$sentDocket->id)->where('type',2)->get();
            $docketFields   =  EmailSentDocketValue::where('email_sent_docket_id',$sentDocket->id)->get();
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
                        'signature'=>AmazoneBucket::url() . $items->signature
                    );
                }
                $isFromBackend  =   false;
                $pdf = PDF::loadView('pdfTemplate.emailedDocketForward', compact('sentDocket','docketFields','docketTimer','approval_type','isFromBackend'));
                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(array('status' => true,'emailDocket' => array('fileName'=>str_replace('.', '',$document_name).'.pdf','filePath' => (\Config::get('app.storage_url_pdf').$document_path),'subject' => "Emailed Docket ".$id." ".$sentDocket->senderCompanyInfo->name)));
        }else {
            return response()->json(array('status' => false,'message' => 'Invalid attempt ! Please try with valid action.'));
        }
    }

    public function forwardEmailInvoiceById(Request $request,$id){
        $sentInvoice    =    EmailSentInvoice::findOrFail($id);
        $companyId  =    $request->header('companyId');
        if($sentInvoice->company_id==$companyId){

            $document_name  = "emailed-invoice-".$id."-".str_replace(' ', '-', strtolower($sentInvoice->senderCompanyInfo->name)).str_replace(' ', '-',Carbon::now()->toDateTimeString());
            $document_path   =   'files/pdf/emailedInvoiceForward/'.str_replace('.', '',$document_name).'.pdf';
            if(!AmazoneBucket::fileExist($document_path)){

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

                if($sentInvoice->theme_document_id == 0){
                    $pdf = PDF::loadView('pdfTemplate.emailedDocketForward', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                }else{
                    $theme = DocumentTheme::where('id', $sentInvoice->theme_document_id)->first();
                    $pdf = PDF::loadView('dashboard/company/theme/'.$theme->slug.'/pdf', compact('sentInvoiceValue','companyDetails','invoice','invoiceDescription','invoiceSetting'));
                }

                $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);

                $output = $pdf->output();
                $path = storage_path($document_path);
                file_put_contents($path, $output);
            }
            return response()->json(array('status' => true,'emailInvoice' => array('fileName'=>str_replace('.', '',$document_name).'.pdf','filePath' => (\Config::get('app.storage_url_pdf').$document_path),'subject' => "Emailed Invoice ".$id." ".$sentInvoice->senderCompanyInfo->name)));
        }else {
            return response()->json(array('status' => false,'message' => 'Invalid attempt ! Please try with valid action.'));
        }
    }

    public function subscriptionCheck($companyId){
        $company    =    Company::where('id',$companyId)->first();
       if($company->trial_period==3) {
           //deactivate all employee
           deactivateAllEmployee($companyId);
       }
    }



    //user/group notification section
    public function getNotificationList(Request $request){
        $userNotifications   =   UserNotification::where('receiver_user_id',$request->header('userId'))->orderBy('created_at','desc')->paginate(10);
        $notificationData   =    array();

        foreach ($userNotifications as $notification){
            $subtitle   =    '';
            $messagesGroups     =    array();

            if($notification->type==2){
                $message   =    Messages::with('messagesGroups')->where('id',$notification->key)->get();
                if($message->count()!=0){
                    $message = $message->first();
                    if(count($message->messagesGroups->messagesGroupUserinfo)!=null )   {
                        $subtitle   =   $message->messagesGroups->title;
                        $userId = $request->header('userId');
                        $groupTitle = "";
                        $memberNumber = array();
                        $users = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', @$message->messagesGroups->id)->where('is_active', 1)->inRandomOrder()->take(2)->get();
                        $groupTitle = @$message->messagesGroups->title ;

                        foreach (@$message->messagesGroups->messagesGroupUserinfo as $datas) {
                            $memberNumber[] = array(
                                'id' => $datas->userInfo->id,
                                'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                                'profile'=>($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png")?"":AmazoneBucket::url() . $datas->userInfo->image
                            );
                        }
                        $messagesGroups[] = array(
                            'id' => $message->messagesGroups->id,
                            'title' => $groupTitle,
                            'member' => $memberNumber,
                        );
                    }
                }
            }



            $time   =    "";
            if(Carbon::parse($notification->created_at)->diffInDays(Carbon::now())==0) {
                if (Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInHours(Carbon::now()) . " Hours Ago";
                }
                elseif(Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) . " Minutes Ago";
                }
                else {
                    if (Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) == 0)
                        $time = "Now";
                    else
                        $time = Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) . " Seconds Ago";
                }
            }
            else{ $time = Carbon::parse($notification->created_at)->format('d-M-Y '); }

            $docket     =    array();
            if($notification->type == 3) {
                $sentDocket = SentDockets::find($notification->key);
                if($sentDocket):
                    $recipientsQuery = $sentDocket->recipientInfo;
                    $recipientData = "";
                    foreach ($recipientsQuery as $recipient) {
                        if ($recipient->id == $recipientsQuery->first()->id)
                            $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                        else
                            $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                    }

                    //check approved or not /.if status == 1 approved

                    if ($sentDocket->status == 3){
                        $status = "Rejected";
                    }else {
                        if ($sentDocket->is_cancel == 1) {
                            $status = "Cancelled";
                        } else {
                            if ($sentDocket->status == 1) {
                                $status = "Approved";
                            } elseif ($sentDocket->user_id == $request->header('userId')) {
                                $status = "Sent";
                            } else {
                                $status = "Received";
                            }
                        }

                    }



                    //approval text
                    $totalRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->count();
                    $totalRecipientApproved = SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('status', 1)->count();

                    //check is approval
                    $isApproval = 0;
                    $isApproved = 0;
                    if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('user_id', $request->header('userId'))->count() == 1) {
                        $isApproval = 1;
                        //check is approved
                        if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('user_id', $request->header('userId'))->where('status', 1)->count() == 1) {
                            $isApproved = 1;
                        }
                    }
                    $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

                    $canRejectDocket = SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('user_id',$request->header('userId'));
                    $canReject = 0;
                    $isReject = 0;

                    if($canRejectDocket->count() > 0 ){
                        if ($canRejectDocket->first()->status == 0){
                            if ($sentDocket->status == 0) {
                                $canReject = 1;
                            }else{
                                $canReject = 0;
                            }
                        }else{
                            $canReject = 0;
                        }

                        if ($sentDocket->status == 3){
                            $isReject = 1;
                        }else{
                            $isReject = 0;
                        }

                    }

                    $docket = array('id' => $sentDocket->id,
                        'user_id' => $sentDocket->user_id,
                        'sender' => $sentDocket->senderUserInfo->first_name . " " . $sentDocket->senderUserInfo->last_name,
                        'profile' => AmazoneBucket::url() . $sentDocket->senderUserInfo->image,
                        'docketName' => $sentDocket->docketInfo->title,
                        'company' => $sentDocket->senderCompanyInfo->name,
                        'recipients' => $recipientData,
                        'dateAdded' => Carbon::parse($sentDocket->created_at)->format('d-M-Y'),
                        'dateSorting' => Carbon::parse($sentDocket->created_at)->format('d-M-Y H:i:s'),
                        'approvalText' => $approvalText,
                        'isApproval' => $isApproval,
                        'isApproved' => $isApproved,
                        'canReject'=>$canReject,
                        'isReject' => $isReject,
                        'status' => $status);
                endif;
            }

            $invoice    =    array();
            if($notification->type == 4) {
                $sentInvoice     =   SentInvoice::find($notification->key);
                if($sentInvoice!=null) {
                    $userId = $sentInvoice->user_id;
                    $userName = $sentInvoice->senderUserInfo->first_name . " " . $sentInvoice->senderUserInfo->last_name;
                    $profile = AmazoneBucket::url() . $sentInvoice->senderUserInfo->image;
                    $company = $sentInvoice->senderCompanyInfo->name;

                    if ($sentInvoice->user_id == $request->header('userId')) {
                        if ($sentInvoice->status == 0):
                            $invoiceStatus = "Sent";
                        endif;
                    } else {
                        if ($sentInvoice->status == 0):
                            $invoiceStatus = "Received";
                        endif;
                    }

                    if ($sentInvoice->status == 1)
                        $invoiceStatus = "Approved";

                    $invoice = array('id' => $sentInvoice->id,
                        'user_id' => $userId,
                        'invoiceName' => $sentInvoice->invoiceInfo->title,
                        'sender' => $userName,
                        'profile' => $profile,
                        'company' => $company,
                        'receiver' => $sentInvoice->receiverUserInfo->first_name . " " . $sentInvoice->receiverUserInfo->last_name,
                        'dateAdded' => Carbon::parse($sentInvoice->created_at)->format('d-M-Y'),
                        'dateSorting' => Carbon::parse($sentInvoice->created_at)->format('d-M-Y h:i:s'),
                        'status' => $invoiceStatus);
                }
            }

            $emailSentDockets   =   array();
            if($notification->type == 5) {
                $emailSentDocket     =   EmailSentDocket::find($notification->key);
                $userId = $emailSentDocket->user_id;
                $userName = @$notification->senderEmailUserDetails;
                $profile = "";
                $company = "";

                if ($emailSentDocket->status == 1)
                    $docketStatus = "Approved";
                else
                    $docketStatus = "Sent";

                $sender     =    "";
                $recipientName  =    "";
                foreach($emailSentDocket->recipientInfo as $recipient) {
                    $sender = $sender . "" . $recipient->emailUserInfo->email;

                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($emailSentDocket->recipientInfo->count() > 1)
                        if ($emailSentDocket->recipientInfo->last()->id != $recipient->id){
                            $sender = $sender . ", ";
                            $recipientName  = $recipientName.", ";
                        }

                }

                //approval text
                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$emailSentDocket->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$emailSentDocket->id)->where('approval',1)->where('status',1)->count();
                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                $senderUser                 =   User::find($request->header('userId'));
                $company                    =   Company::find($request->header('companyId'));
                $emailSentDockets    =      array('id'           => $emailSentDocket->id,
                    'user_id'       => $senderUser->id,
                    'docketName'    => $emailSentDocket->docketInfo->title,
                    'sender'        => $senderUser->first_name." ".$senderUser->last_name,
                    "profile"       =>   AmazoneBucket::url() . $senderUser->image,
                    'company'       => $company->name,
                    "recipients"    => $recipientName,
                    'dateAdded'     => Carbon::parse($emailSentDocket->created_at)->format('d-M-Y'),
                    "dateSorting"   => Carbon::parse($emailSentDocket->created_at)->format('d-M-Y H:i:s'),
                    "approvalText"  => $totalRecipientApproved / $totalRecipientApprovals . " Approved",
                    "isApproved"    => $emailSentDocket->status,
                    'status'        => $docketStatus);
            }

            $name   =   "";
            if($notification->type==5){
                $name   =   $notification->senderEmailUserDetails->email;
            }else{
                $name   =    $notification->senderDetails->first_name." ".$notification->senderDetails->last_name;
            }

            $notificationData[]   =  array('id' => $notification->id,
                'type'      =>    $notification->type,
                'sender'    =>  array('id' => $notification->sender_user_id,
                    'name' =>$name,
                    'profile' => ($notification->type==5)?"":AmazoneBucket::url() . $notification->senderDetails->image),
                'subtitle'  =>  $subtitle,
                'message'   =>   strip_tags($notification->message),
                'formattedMessage'   =>  $notification->message,
                'key'       =>    $notification->key,
                'docket'    =>  $docket,
                'invoice'    =>  $invoice,
                'emailSentDocket' => $emailSentDockets,
                'time'      =>  $time,
                'status'    =>  $notification->status);
        }
        return response()->json(array('status' => true, 'notification' => $notificationData));

    }


    public  function  checkMessageDetail($request,$message){
        $userId = $request->header('userId');
//        foreach ($message->messagesGroups as $rowData) {
        $groupTitle = "";
        $groupProfile = array();
        $memberNumber = array();
        if ($message->messagesGroups->title == null) {
            $users = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $message->messagesGroups->id)->where('is_active', 1)->inRandomOrder()->take(2)->get();
            $groupTitle = $message->messagesGroups->title ;
            foreach ($users as $users) {
                $groupProfile[] = AmazoneBucket::url() . $users->userInfo->image;
            }
            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $memberNumber[] = array(
                    'id' => $datas->userInfo->id,
                    'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                    'profile'=>($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png")?"":AmazoneBucket::url() . $datas->userInfo->image
                );
            }
        } else {
            $user = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id',$message->messagesGroups->id)->first();
            $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
            $groupProfile[] = AmazoneBucket::url() . $user->userInfo->image;

            foreach ($message->messagesGroups->messagesGroupUserinfo as $datas) {
                $memberNumber[] = array(
                    'id' => $datas->userInfo->id,
                    'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
                    'profile' => AmazoneBucket::url() . $datas->userInfo->image
                );
            }
        }

        if ($message->messagesGroups->title == null) {
            $messagesGroups[] = array(
                'id' => $message->messagesGroups->id,
                'type' => 1,
                'date' => Carbon::parse($message->messagesGroups->created_date)->format('d-M-Y'),
                'profile' => $groupProfile,
                'title' => $groupTitle,
                'member' => $memberNumber,


            );
        } else {
            $messagesGroups[] = array(
                'id' => $message->messagesGroups->id,
                'type' => 2,
                'date' => Carbon::parse($message->messagesGroups->created_date)->format('d-M-Y'),
                'profile' => $groupProfile,
                'title' => $groupTitle,
                'member' => $memberNumber,

            );
        }

        return $messagesGroups;
    }


    public function getNotificationListUpdateAndroid(Request $request){
        $userNotifications   =   UserNotification::where('receiver_user_id',$request->header('userId'))->orderBy('created_at','desc')->paginate(10);
        $notificationData   =    array();
        foreach ($userNotifications as $notification){
            $subtitle   =    '';
            if($notification->type==2){
                 $message = Messages::where('id',$notification->key)->first();
                 $messageGroup = $this->checkMessageDetail($request ,$message);
            }

            if($notification->type==1){
                $messageGroup = $this->checkMessageDetail($request ,$message);
            }

            $time   =    "";
            if(Carbon::parse($notification->created_at)->diffInDays(Carbon::now())==0) {
                if (Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInHours(Carbon::now()) . " Hours Ago";
                }
                elseif(Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) > 60) {
                    $time = Carbon::parse($notification->created_at)->diffInMinutes(Carbon::now()) . " Minutes Ago";
                }
                else {
                    if (Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) == 0)
                        $time = "Now";
                    else
                        $time = Carbon::parse($notification->created_at)->diffInSeconds(Carbon::now()) . " Seconds Ago";
                }
            }
            else{ $time = Carbon::parse($notification->created_at)->format('d-M-Y '); }

            $docket     =    array();
            if($notification->type == 3) {
                $sentDocket = SentDockets::find($notification->key);
                $recipientsQuery = $sentDocket->recipientInfo;
                $recipientData = "";
                foreach ($recipientsQuery as $recipient) {
                    if ($recipient->id == $recipientsQuery->first()->id)
                        $recipientData = $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                    else
                        $recipientData = $recipientData . ", " . $recipient->userInfo->first_name . " " . $recipient->userInfo->last_name;
                }

                //check approved or not /.if status == 1 approved
                if ($sentDocket->status == 1) {
                    $status = "Approved";
                } elseif ($sentDocket->user_id == $request->header('userId')) {
                    $status = "Sent";
                } else {
                    $status = "Received";
                }

                //approval text
                $totalRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->count();
                $totalRecipientApproved = SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('status', 1)->count();

                //check is approval
                $isApproval = 0;
                $isApproved = 0;
                if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('user_id', $request->header('userId'))->count() == 1) {
                    $isApproval = 1;
                    //check is approved
                    if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->where('user_id', $request->header('userId'))->where('status', 1)->count() == 1) {
                        $isApproved = 1;
                    }
                }
                $approvalText = $totalRecipientApproved . "/" . $totalRecipientApprovals . " Approved";

                $docket[] = array('id' => $sentDocket->id,
                    'user_id' => $sentDocket->user_id,
                    'sender' => $sentDocket->senderUserInfo->first_name . " " . $sentDocket->senderUserInfo->last_name,
                    'profile' => AmazoneBucket::url() . $sentDocket->senderUserInfo->image,
                    'docketName' => $sentDocket->docketInfo->title,
                    'company' => $sentDocket->senderCompanyInfo->name,
                    'recipients' => $recipientData,
                    'dateAdded' => Carbon::parse($sentDocket->created_at)->format('d-M-Y'),
                    'dateSorting' => Carbon::parse($sentDocket->created_at)->format('d-M-Y H:i:s'),
                    'approvalText' => $approvalText,
                    'isApproval' => $isApproval,
                    'isApproved' => $isApproved,
                    'status' => $status);
            }

            $invoice    =    array();
            if($notification->type == 4) {
                $sentInvoice     =   SentInvoice::find($notification->key);
                $userId = $sentInvoice->user_id;
                $userName = $sentInvoice->senderUserInfo->first_name . " " . $sentInvoice->senderUserInfo->last_name;
                $profile = AmazoneBucket::url() . $sentInvoice->senderUserInfo->image;
                $company = $sentInvoice->senderCompanyInfo->name;

                if ($sentInvoice->user_id == $request->header('userId')) {
                    if ($sentInvoice->status == 0):
                        $invoiceStatus = "Sent";
                    endif;
                } else {
                    if ($sentInvoice->status == 0):
                        $invoiceStatus = "Received";
                    endif;
                }

                if ($sentInvoice->status == 1)
                    $invoiceStatus = "Approved";

                $invoice = array('id' => $sentInvoice->id,
                    'user_id' => $userId,
                    'invoiceName' => $sentInvoice->invoiceInfo->title,
                    'sender' => $userName,
                    'profile' => $profile,
                    'company' => $company,
                    'receiver'  => $sentInvoice->receiverUserInfo->first_name." ".$sentInvoice->receiverUserInfo->last_name,
                    'dateAdded' => Carbon::parse($sentInvoice->created_at)->format('d-M-Y'),
                    'dateSorting' => Carbon::parse($sentInvoice->created_at)->format('d-M-Y h:i:s'),
                    'status' => $invoiceStatus);
            }

            $emailSentDockets   =   array();
            if($notification->type == 5) {
                $emailSentDocket     =   EmailSentDocket::find($notification->key);
                $userId = $emailSentDocket->user_id;
                $userName = @$notification->senderEmailUserDetails;
                $profile = "";
                $company = "";

                if ($emailSentDocket->status == 1)
                    $docketStatus = "Approved";
                else
                    $docketStatus = "Sent";

                $sender     =    "";
                $recipientName  =    "";
                if($emailSentDocket->recipientInfo){
                    foreach($emailSentDocket->recipientInfo as $recipient) {
                        $sender = $sender . "" . $recipient->emailUserInfo->email;

                        if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                            $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                        }else{
                            $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                        }
                        if ($emailSentDocket->recipientInfo->count() > 1)
                            if ($emailSentDocket->recipientInfo->last()->id != $recipient->id){
                                $sender = $sender . ", ";
                                $recipientName  = $recipientName.", ";
                            }

                    }
                }



                //approval text
                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$emailSentDocket->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$emailSentDocket->id)->where('approval',1)->where('status',1)->count();
                $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                $senderUser                 =   User::find($request->header('userId'));
                $company                    =   Company::find($request->header('companyId'));
                $emailSentDockets    =      array('id'           => $emailSentDocket->id,
                    'user_id'       => $senderUser->id,
                    'docketName'    => $emailSentDocket->docketInfo->title,
                    'sender'        => $senderUser->first_name." ".$senderUser->last_name,
                    "profile"       =>   AmazoneBucket::url() . $senderUser->image,
                    'company'       => $company->name,
                    "recipients"    => $recipientName,
                    'dateAdded'     => Carbon::parse($emailSentDocket->created_at)->format('d-M-Y'),
                    "dateSorting"   => Carbon::parse($emailSentDocket->created_at)->format('d-M-Y H:i:s'),
                    "approvalText"  => $totalRecipientApproved / $totalRecipientApprovals . " Approved",
                    "isApproved"    => $emailSentDocket->status,
                    'status'        => $docketStatus);
            }

            $name   =   "";
            if($notification->type==5){
                $name   =   $notification->senderEmailUserDetails->email;
            }else{
                $name   =    $notification->senderDetails->first_name." ".$notification->senderDetails->last_name;
            }

            $notificationData[]   =  array('id' => $notification->id,
                'type'      =>    $notification->type,
                'sender'    =>  array('id' => $notification->sender_user_id,
                'name' =>$name,
                 'profile' => ($notification->type==5)?"":AmazoneBucket::url() . $notification->senderDetails->image),
                'subtitle'  =>  $subtitle,
                'message'   =>   strip_tags($notification->message),
                'formattedMessage'   =>  $notification->message,
                'messageGroup'=>$messageGroup,
                'key'       =>    $notification->key,
                'docket'    =>  $docket,
                'invoice'    =>  $invoice,
                'emailSentDocket' => $emailSentDockets,
                'time'      =>  $time,
                'status'    =>  $notification->status);
        }

        $unreadNotificationCount =  UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->count();

        return response()->json(array('status' => true, 'notification' => $notificationData,'unreadNotificationCount'=>$unreadNotificationCount));



    }





    public function markAsRead(Request $request,$key){
        $userNotification   =    UserNotification::find($key);
        if($userNotification->receiver_user_id==$request->header('userId')){
            $userNotification->status   =    1;
            $userNotification->save();
            $unreadMessage  =    UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->count();
            return response()->json(array('status' => true, 'message' => "Success",'unreadMessage' => $unreadMessage));
        }else{
            return response()->json(array('status' => false, 'message' => "Invalid Attempt!"));
        }
    }

    public function emailUserList(Request $request){
        $emailClient = Email_Client::where("company_id",$request->header('companyId'))->select('id','email_user_id','full_name','company_name','company_address');
        $emailClients= array();
        if ($emailClient->count()>0){
            foreach ($emailClient->orderBy('id','desc')->get() as $row){
                $emailClients[] =   array('id'=> $row->id,
                    'email_user_id'=>$row->emailUser->id,
                    'email'          => $row->emailUser->email,
                    'full_name'=> $row->full_name,
                    'company_name'         =>  $row->company_name,
                    'company_address'  => $row->company_address,
                );

            }

        }
        return response()->json(array("status" => true,"emailClients"=>$emailClients ));


    }

    public function  saveEmailClient(Request $request)
    {
        $user=User::where('email', $request->email);

        /*if($user->count()!=0){
            return response()->json(array("status" => false, "message" => 'That email is already registered to Record Time as "' .$user->first()->first_name.' '.$user->first()->last_name.'" Please connect with "'. $user->first()->first_name.' '.$user->first()->last_name .'". via "Find Client" in backend.'));
        }*/
        $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
        if ($validator->validate($request->email) != "false" && $validator->validate($request->email) != null) {
            $emailUser=EmailUser::where('email', $request->email)->first();
            if (EmailUser::where('email', $request->email)->count() != 0) {
                if (Email_Client::where('email_user_id', $emailUser->id)->where('company_id',$request->header('companyId'))->count() == 0):
                    $addEmailClient = new Email_Client();
                    $addEmailClient->full_name = $request->full_name;

                    // $addEmailClient->company_name = $request->company_name;
                    // $addEmailClient->company_address = $request->company_address;
                    if ($request->company_name==""){
                        $addEmailClient->company_name = "";
                    }else{
                        $addEmailClient->company_name = $request->company_name;
                    }
                    if ($request->company_address==""){
                        $addEmailClient->company_address = "";
                    }else{
                        $addEmailClient->company_address = $request->company_address;
                    }
                    $addEmailClient->company_id = $request->header('companyId');
                    $addEmailClient->email_user_id = $emailUser->id;
                    if ($addEmailClient->save()):
                        return response()->json(array("status" => true, "message" => 'Email client add successfully.', 'email_user_id' => EmailUser::where('email', $request->email)->first()->id));
                    else:
                        return response()->json(array("status" => false, "message" => 'Email client add fails.'));
                    endif;
                else:
                    return response()->json(array("status" => false, "message" => 'This email is already added on your Custom Clients as user '.Email_Client::where('email_user_id', $emailUser->id)->where('company_id',$request->header('companyId'))->first()->full_name ));
                endif;

            } else {
                $emailClient = new EmailUser();
                $emailClient->email = $request->email;
                $emailClient->name = "";
                $emailClient->company_name = "";
                if ($emailClient->save()) {
                    $addEmailClient = new Email_Client();
                    $addEmailClient->full_name = $request->full_name;

                    // $addEmailClient->company_name = $request->company_name;
                    // $addEmailClient->company_address = $request->company_address;
                    if ($request->company_name==""){
                        $addEmailClient->company_name = "";
                    }else{
                        $addEmailClient->company_name = $request->company_name;
                    }
                    if ($request->company_address==""){
                        $addEmailClient->company_address = "";
                    }else{
                        $addEmailClient->company_address = $request->company_address;
                    }

                    $addEmailClient->company_id = $request->header('companyId');
                    $addEmailClient->email_user_id = $emailClient->id;
                    if ($addEmailClient->save()):
                        return response()->json(array("status" => true, "message" => 'Email client add successfully.', 'email_user_id' => $emailClient->id));
                    else:
                        return response()->json(array("status" => false, "message" => 'Email client add fails.'));
                    endif;
                }
            }

        }else{
            return response()->json(array("status" => false, "message" => 'Invalid Email address.'));

        }

    }

    //Timer API Endpoints//

    //Timer API Endpoints//
















    // public function calculateOldTime(Request $request){
    //     $timer = Timer::where('id','<=','415')->get();
    //     $totalInterval = 0;
    //         foreach ($timer as $row) {
    //             if ($row->total_time == "00:00:00"){
    //                 $datetime1 = \Carbon\Carbon::  parse($row->time_started);
    //                 $datetime2 = \Carbon\Carbon::parse($row->time_ended);
    //                 $interval = $datetime2->diffInSeconds($datetime1);
    //                 $date = $interval - $totalInterval;
    //                 $row->total_time = gmdate("H:i:s", $date);
    //                 $row->save();
    //             }
    //     }
    // }



    public function getInvoiceableEmailDocketList(Request $request,$key){
        $emailSentDocket = EmailSentDocket::where('user_id',$request->header('userId'))->where('company_id',$request->header('companyId'))->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $key){
                  $arrays[]=$items->email_sent_docket_id;
                }
            }
        }
        $matchEmailDocket = EmailSentDocket::whereIn('id',$arrays);
        $invoiceableEmailDockets =   array();
        if($matchEmailDocket->count()>0) {
            $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
            foreach ($resultQuery as $result) {
                if ($result->company_id == $request->header('companyId')):
                    $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                    $company = $result->senderCompanyInfo->name;
                    $senderImage = $result->senderCompanyInfo->userInfo->image;

//                    if ($result->status == 0):
//                        $docketStatus = "Sent";
//                    endif;
//
//                    if ($result->status == 1)
//                        $docketStatus = "Approved";
                    $recipientName  =    "";
                    foreach($result->recipientInfo as $recipient) {
                        if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                            $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                        }else{
                            $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                        }
                        if ($result->recipientInfo->count() > 1)
                            if ($result->recipientInfo->last()->id != $recipient->id){
                                $recipientName  = $recipientName.", ";
                            }

                    }
                    //approval text
                    $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                    $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
                    // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                    if ($totalRecipientApproved == $totalRecipientApprovals ){
                        $approvalText               =  "Approved";
                    }else{
                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                    }


                    $invoiceDescription     =    array();
                    $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
                    foreach($invoiceDescriptionQuery as $description){
                        $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                    }
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    //                if($invoiceAmount != 0) {
                    $invoiceableEmailDockets[] = array('id' => $result->id,
                        'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                        'user_id' => $result->user_id,
                        'docketName' => $result->docketInfo->title,
                        'docketTemplateId' => $result->docketInfo->id,
                        'sender' => $userName,
                        'company' => $company,
                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                        'invoiceDescription' => $invoiceDescription,
                        'invoiceAmount' => $invoiceAmount,
                        'status' => $approvalText,
                        'recipient'=>$recipientName,
                        "isApproved"    => $result->status,
                        'senderImage'=>AmazoneBucket::url() . $senderImage,


                        );
                    //                }
                    empty($invoiceDescription);
                    empty($invoiceAmount);
                endif;
            }

        }
        return response()->json(array('status' => true, 'invoiceableEmailDockets' =>$invoiceableEmailDockets));


    }

    public function markAllAsRead(Request $request){
        UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->update(['status'=>1]);
        return response()->json(array("status" => true, "message" => "Success"));

    }

    public function memberLastSeens($messages,$key){
        $messages_ids= $messages->pluck('id')->toArray();
        $group_member = Messages::where('id',$key)->first()->messagesGroups->messagesGroupUserinfo;
        $memberLastSeen = array();
        foreach ($group_member as $member){
            $last_seen_msg = MessagesRecipients::whereIn('message_id',$messages_ids)->where('user_id',$member->user_id)->where('is_read',1)->orderBy('created_at','desc')->get();
            if (count($last_seen_msg)==0){
                $memberLastSeen[] = array($member->user_id,0
                );
            }else{
                $memberLastSeen[]= array(
                    $member->user_id,$last_seen_msg->first()->message_id
                );
            }
        }
        return $memberLastSeen;
    }




    public function approveDocketByEmail(Request $request,$id,$hashKey){
        $sentDocketQuery     =    SentDocketRecipientApproval::where('sent_docket_id',$id)->where('hashKey',$hashKey);
        $sentdockets= SentDockets::where('id',$id)->first();
        if($sentDocketQuery->count()==1){
            if ($sentdockets->docketApprovalType == 0){
                $sentDocket =   $sentDocketQuery->first();
                if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                    $sentDocket->hashKey = '';
                    $sentDocket->status     =   1;
                    $sentDocket->save();


                    if(SentDocketRecipientApproval::where('sent_docket_id',$id)->where('hashKey','!=','')->count()==0){
                        SentDockets::where('id',$id)->update(['status'=>1]);
                    }

                    $sentDocketss    =    SentDockets::find($id);
                    if($sentDocketss->senderUserInfo->device_type == 2){
                        sendiOSNotification($sentDocketss->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                    }else if($sentDocketss->senderUserInfo->device_type == 1){
                        sendAndroidNotification($sentDocketss->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                    }
                    $userNotification   =    new UserNotification();
                    $userNotification->sender_user_id   =    $sentDocket->user_id;
                    $userNotification->receiver_user_id =   SentDockets::find($id)->user_id;
                    $userNotification->type     =   3;
                    $userNotification->title    =   'Docket Approved';
                    $userNotification->message  =   $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket.";
                    $userNotification->key      =   $id;
                    $userNotification->status   =   0;
                    $userNotification->save();
                    $docketApprovalType =$sentDocket->docketApprovalType;
                    $message    =   "Requested docket has been approved successfully.";
                    return view('errors.errorPage', compact('message','docketApprovalType'));
                }else {
                    $message    =   "Your link has expired.";
                    return view('errors.errorPage', compact('message'));
                }
            }else{
                $sentDockets =   $sentDocketQuery->first();
                $sentDocket     =   SentDockets::findOrFail($id);
                $docketTimer = SentDcoketTimerAttachment::where('sent_docket_id',$id)->where('type',2)->get();
                $docketFields   =  SentDocketsValue::where('sent_docket_id',$sentDocket->id)->get();
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
                $datass= (new Collection($sentDocketRecepients))->sortBy('company_name');
                $receiverDetail = array();
                foreach ($datass as $datas){
                    $receiverDetail[$datas['company_name']][]= $datas['name'];
                }
                return view('errors.sentDocket', compact('sentDocket','sentDockets','docketTimer','docketFields','receiverDetail'));
            }
        }else{

            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
    }


    public function approvedDocketSignature(Request $request){
        $sentDocketQuery     =    SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->where('hashKey',$request->hashKey);
        if($sentDocketQuery->count()==1){
            $sentDocket =   $sentDocketQuery->first();
            if($sentDocket->hashKey!='' && $sentDocket->status!='1'){
                $sentDocket->hashKey = '';
                $sentDocket->status     =   1;
                $sentDocket->approval_time =Carbon::now()->toDateTimeString();
                $sentDocket->name =$request->name;
                $image = $request->signature;  // your base64 encoded
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'files/docket/images/signature'.time().'.'.'png';
                //  \File::put(public_path(). '/signature/' . $imageName, base64_decode($image));
                \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                $sentDocket->signature=$imageName;
                $sentDocket->save();
                if(SentDocketRecipientApproval::where('sent_docket_id',$request->sentDocketId)->where('hashKey','!=','')->count()==0){
                    SentDockets::where('id',$request->sentDocketId)->update(['status'=>1]);
                }
                $sentDocketss    =    SentDockets::find($request->sentDocketId);
                if($sentDocketss->senderUserInfo->device_type == 2){
                    sendiOSNotification($sentDocketss->senderUserInfo->deviceToken,"Docket Approved", $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                }else if($sentDocketss->senderUserInfo->device_type == 1){
                    sendAndroidNotification($sentDocketss->senderUserInfo->deviceToken,'Docket Approved', $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket",array('type'=>3));
                }
                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =    $sentDocket->user_id;
                $userNotification->receiver_user_id =   SentDockets::find($request->sentDocketId)->user_id;
                $userNotification->type     =   3;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $sentDocket->userInfo->first_name.' '.$sentDocket->userInfo->last_name." has approved your docket.";
                $userNotification->key      =   $request->sentDocketId;
                $userNotification->status   =   0;
                $userNotification->save();
                $docketApprovalType =$sentDocket->docketApprovalType;
                $message    =   "Requested docket has been approved successfully.";
                return response()->json(['status' => true, 'message' => $message]);
            }else {
                $message    =   "Your link has expired.";
                return response()->json(['status' => false, 'message' => $message]);

            }
        }else{
            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
    }




//    public function getMessagesList(Request $request)
//    {
//        $companyId = $request->header('companyId');
//        $userId = $request->header('userId');
//        $messageGroupUser = MessagesGroupUser::where('user_id', $userId)->get();
//
//        $messagesGroups = array();
//        foreach ($messageGroupUser as $rowData) {
//            $groupTitle = "";
//            $groupProfile = array();
//            $memberNumber = array();
//            if ($rowData->messagesGroupinfo->messagesInfo->count() != 0) {
//                $isRead = "";
//                if ($rowData->messagesGroupinfo->messagesInfo->last()->user_id == $userId) {
//                    $isRead = 1;
//                } else {
//                    $readStatus = MessagesRecipients::where("user_id", $userId)->where('message_id', $rowData->messagesGroupinfo->messagesInfo->last()->id)->first();
//                    $isRead = $readStatus->is_read;
//                }
//                $lastMessages = array(
//                    "id" => $rowData->messagesGroupinfo->messagesInfo->last()->id,
//                    "message" => $rowData->messagesGroupinfo->messagesInfo->last()->message,
//                    "is_read" => $isRead,
//                    "created_date" => Carbon::parse($rowData->messagesGroupinfo->messagesInfo->last()->created_date)->format('d-M-Y'),
//                );
//                $dateSorting = $rowData->messagesGroupinfo->messagesInfo->last()->created_date;
//
//            } else {
//                $lastMessages = null;
//                $dateSorting = $rowData->messagesGroupinfo->created_date;
//            }
//            if ($rowData->is_group_message == 1) {
//                $users = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $rowData->messages_groups_id)->where('is_active', 1)->inRandomOrder()->take(2)->get();
//                $groupTitle = $rowData->messagesGroupinfo->title;
//                foreach ($users as $users) {
//                    $groupProfile[] = asset($users->userInfo->image);
//                }
//                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
//                    $memberNumber[] = array(
//                        'id' => $datas->userInfo->id,
//                        'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
//                        'profile'=>($datas->userInfo->image == "assets/dashboard/images/logoAvatar.png")?"":asset($datas->userInfo->image)
//                    );
//                }
//            } else {
//                $user = MessagesGroupUser::where("user_id", '!=', $userId)->where('messages_groups_id', $rowData->messages_groups_id)->first();
//                $groupTitle = $user->userInfo->first_name . " " . $user->userInfo->last_name;
//                $groupProfile[] = asset($user->userInfo->image);
//
//                foreach ($rowData->messagesGroupinfo->messagesGroupUserinfo as $datas) {
//                    $memberNumber[] = array(
//                        'id' => $datas->userInfo->id,
//                        'name' => $datas->userInfo->first_name . ' ' . $datas->userInfo->last_name,
//                        'profile' => asset($datas->userInfo->image)
//
//
//                    );
//                }
//            }
//            if ($rowData->messagesGroupinfo->title == null) {
//                $messagesGroups[] = array(
//                    'id' => $rowData->messagesGroupinfo->id,
//                    'type' => 1,
//                    'date' => Carbon::parse($rowData->messagesGroupinfo->created_date)->format('d-M-Y'),
//                    'profile' => $groupProfile,
//                    'title' => $groupTitle,
//                    'member' => $memberNumber,
//                    'last_messages' => $lastMessages,
//                    'sortingDate' => $dateSorting,
//
//
//                );
//            } else {
//                $messagesGroups[] = array(
//                    'id' => $rowData->messagesGroupinfo->id,
//                    'type' => 2,
//                    'date' => Carbon::parse($rowData->messagesGroupinfo->created_date)->format('d-M-Y'),
//                    'profile' => $groupProfile,
//                    'title' => $groupTitle,
//                    'member' => $memberNumber,
//                    'last_messages' => $lastMessages,
//                    'sortingDate' => $dateSorting,
//
//                );
//            }
//
//        }
////        dd($messagesGroups);
//
//
//        $size = count($messagesGroups);
//        for ($i = 0; $i < $size; $i++) {
//            for ($j = 0; $j < $size - 1 - $i; $j++) {
//                if (strtotime($messagesGroups[$j + 1]['sortingDate']) > strtotime($messagesGroups[$j]['sortingDate'])) {
//                    $tempArray = $messagesGroups[$j + 1];
//                    $messagesGroups[$j + 1] = $messagesGroups[$j];
//                    $messagesGroups[$j] = $tempArray;
//                }
//
//            }
//        }
//
//
//
//
//
//
//        return response()->json(array("status" => true, "messageGroup" =>$messagesGroups ));
//    }
//
//    public  function markAsReadStatusCheck($request,$messages)
//    {
//        $markasRead = false;
//
//        $lastMessage = $messages->last();
//        if($lastMessage) {
//            if ($lastMessage->user_id != $request->header('userId')) {
//                if (MessagesRecipients::where('user_id', $request->header('userId'))->where('message_id', $lastMessage->id)->where('is_read', 0)->count() != 0) {
//                    $markasRead = true;
//                }
//            }
//        }
//        return $markasRead;
//
//    }
//
//    public function memberLastSeen($messages,$key)
//    {
//        $messages_ids= $messages->pluck('id')->toArray();
//        $group_member = MessagesGroup::where('id',$key)->first()->messagesGroupUserinfo;
//        $memberLastSeen = array();
//        foreach ($group_member as $member){
//            $last_seen_msg = MessagesRecipients::whereIn('message_id',$messages_ids)->where('user_id',$member->user_id)->where('is_read',1)->orderBy('created_at','desc')->get();
//            if (count($last_seen_msg)==0){
//                $memberLastSeen[] = array($member->user_id,0
//                );
//            }else{
//                $memberLastSeen[]= array(
//                    $member->user_id,$last_seen_msg->first()->message_id
//                );
//            }
//        }
//        return $memberLastSeen;
//    }
//
//
//    public function messages(Request $request,$key)
//    {
//        $messages = Messages::where('messages_groups_id',$key)->get();
//        $checkMarkAsRead =$this->markAsReadStatusCheck($request,$messages);
//        $member_last_seen =$this->memberLastSeen($messages,$key);
//        $messagelist = array();
//        foreach ($messages as $items){
//            $messagesTotalUser= array();
//            foreach ($items->messagesGroups->messagesGroupUserinfo as $messagesGroupUserinfo){
//                $messagesTotalUser[] = $messagesGroupUserinfo->user_id;
//            }
//            $seenUser = array();
//            foreach ($items->messagesRecInfo as $messagesRecInfo) {
//                if ($messagesRecInfo->is_read == 1){
//                    $seenUser[] = $messagesRecInfo->userInfo->first_name;
//                }
//            }
//            $seen = array();
//            foreach ($member_last_seen as $lastSeen){
//                if ($items->id ==$lastSeen[1]){
//                    $user = User::find($lastSeen[0]);
//                    $seen[]= array(
//                        'user_id'=>$user->id,
//                        'user_name'=>$user->first_name,
//                        'user_profile'=>($user->image == "assets/dashboard/images/logoAvatar.png")?"":asset($user->image)
//                    );
//                }
//            }
//            if (count($seenUser)== count($messagesTotalUser)){
//                $seen_user = "Seen By Everyone";
//            }else{
//                $seen_user ="Seen By ".implode(', ',$seenUser);
//            }
//            $messagelist[] = array(
//                'id'=>$items->id,
//                'message'=>$items->message,
//                'user_id'=>$items->user_id,
//                'userName'=>$items->userInfo->first_name." ".$items->userInfo->last_name,
//                'profile'=>asset($items->userInfo->image),
//                'date'=> Carbon::parse($items->created_date)->format('d-M-Y'),
//                'seen'=>$seen,
//                'seen_by'=>$seen_user,
//            );
//        }
//        return response()->json(array("status" => true, "messages" =>$messagelist,"markAsRead"=>$checkMarkAsRead ));
//
//    }
//
//    public function markAsReads(Request $request)
//    {
//        $messages = Messages::where('messages_groups_id',$request->messages_groups_id)->pluck('id')->toArray();
//        $checkMessageRecipent=MessagesRecipients::where('message_id','<=',$request->last_message_id)->pluck('id')->toArray();
//        MessagesRecipients::whereIn('id',$checkMessageRecipent)->whereIn('message_id',$messages)->where('user_id',$request->header('userId'))->where('is_read',0)->update(['is_read'=>1]);
//        return response()->json(array("status" => true, "message" => "Success"));
//
//    }
//
//    public function getInvoiceableEmailDocketList(Request $request,$key)
//    {
//        $emailSentDocket = EmailSentDocket::where('user_id',$request->header('userId'))->where('company_id',$request->header('companyId'))->get();
//        $arrays = array();
//        foreach ($emailSentDocket as $row){
//            foreach ($row->recipientInfo as $items){
//                if ($items->email_user_id == $key){
//                    $arrays[]=$items->email_sent_docket_id;
//                }
//            }
//        }
//        $matchEmailDocket = EmailSentDocket::whereIn('id',$arrays);
//        $invoiceableEmailDockets =   array();
//        if($matchEmailDocket->count()>0) {
//            $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
//            foreach ($resultQuery as $result) {
//                if ($result->company_id == $request->header('companyId')):
//                    $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
//                    $company = $result->senderCompanyInfo->name;
//                    $senderImage = $result->senderCompanyInfo->userInfo->image;
//
//                    $recipientName  =    "";
//                    foreach($result->recipientInfo as $recipient) {
//                        if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
//                            $recipientName  =   $recipientName."".$recipient->receiver_full_name;
//                        }else{
//                            $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
//                        }
//                        if ($result->recipientInfo->count() > 1)
//                            if ($result->recipientInfo->last()->id != $recipient->id){
//                                $recipientName  = $recipientName.", ";
//                            }
//
//                    }
//                    //approval text
//                    $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
//                    $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
//                    // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
//
//                    if ($totalRecipientApproved == $totalRecipientApprovals ){
//                        $approvalText               =  "Approved";
//                    }else{
//                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
//                    }
//
//                    $invoiceDescription     =    array();
//                    $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
//                    foreach($invoiceDescriptionQuery as $description){
//                        $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
//                    }
//                    $invoiceAmount  =    0;
//                    $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
//                    foreach($invoiceAmountQuery as $amount){
//                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
//                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
//                    }
//                    //                if($invoiceAmount != 0) {
//                    $invoiceableEmailDockets[] = array('id' => $result->id,
//                        'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
//                        'user_id' => $result->user_id,
//                        'docketName' => $result->docketInfo->title,
//                        'docketTemplateId' => $result->docketInfo->id,
//                        'sender' => $userName,
//                        'company' => $company,
//                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
//                        'invoiceDescription' => $invoiceDescription,
//                        'invoiceAmount' => $invoiceAmount,
//                        'status' => $approvalText,
//                        'recipient'=>$recipientName,
//                        "isApproved"    => $result->status,
//                        'senderImage'=>asset($senderImage),
//
//                    );
//                    //                }
//                    empty($invoiceDescription);
//                    empty($invoiceAmount);
//                endif;
//            }
//
//        }
//
//        return response()->json(array('status' => true, 'invoiceableEmailDockets' =>$invoiceableEmailDockets));
//
//
//    }
//
//
//    public function markAllAsRead(Request $request){
//        UserNotification::where('receiver_user_id',$request->header('userId'))->where('status',0)->update(['status'=>1]);
//        return response()->json(array("status" => true, "message" => "Success"));
//
//    }



    public  function sentDocketReject(Request $request){
        $this->validate($request,['sent_docket_id' =>     'required','explanation'=>'required']);
        $sentDocketId = Input::get('sent_docket_id');
        $explanation = Input::get('explanation');

        if (SentDockets::where('id', $sentDocketId)->where('status',3)->count() == 0){
            $sentDocketRecipientApprovalQuery = SentDocketRecipientApproval::where('sent_docket_id', $sentDocketId)->Where('user_id', $request->header('userId'))->where('status', 0);
            if ($sentDocketRecipientApprovalQuery->count() == 1){
                $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                $sentDocketRecipientApproval->status     =   3;
                $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();

                if ($sentDocketRecipientApproval->save()){
                    $sentDocketExplanation = new SentDocketReject();
                    $sentDocketExplanation->sent_docket_id =  $sentDocketId;
                    $sentDocketExplanation->explanation =  $explanation;
                    $sentDocketExplanation->user_id =  $request->header('userId');
                    $sentDocketExplanation->save();
                }
                SentDockets::where('id',$sentDocketId)->update(['status'=> 3]);
                $sentDocket = SentDockets::where('id',$sentDocketId)->first();


                $companyAdminUser = $sentDocket->senderCompanyInfo->userInfo;
//                    Company::where('id',$request->header('companyId'))->first()->userInfo;


                $userNotification   =   new UserNotification();
                $userNotification->sender_user_id   =    $request->header('userId');
                $userNotification->receiver_user_id = $sentDocket->user_id;
                $userNotification->type     =   3;
                $userNotification->title    =   'Docket Rejected';
                $userNotification->message  =   "Your Docket has been rejected by";
                $userNotification->key      =   $sentDocketId;
                $userNotification->status   =   0;
                if ($userNotification->save()){
                    if ($sentDocket->senderUserInfo->deviceToken != ""){
                        if ($sentDocket->senderUserInfo->device_type == 2)
                        {
                            $this->sendiOSNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message );
                        }
                        if ($sentDocket->senderUserInfo->device_type == 1)
                        {
                            $this->sendAndroidNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
                        }
                    }
                }


                if ($sentDocket->user_id != $companyAdminUser->id){
                    $userNotification   =   new UserNotification();
                    $userNotification->sender_user_id   =   $request->header('userId');
                    $userNotification->receiver_user_id = $companyAdminUser->id;
                    $userNotification->type     =   3;
                    $userNotification->title    =   'Docket Rejected';
                    $userNotification->message  =   "Your Docket has been rejected by ".User::where('id',$request->header('userId'))->first()->first_name.' '.User::where('id',$request->header('userId'))->first()->last_name ;
                    $userNotification->key      =   $request->docket_id;
                    $userNotification->status   =   0;
                    if ($userNotification->save()) {
                        if ($companyAdminUser->deviceToken != "") {
                            if ($companyAdminUser->device_type == 2) {
                                $this->sendiOSNotification($companyAdminUser->deviceToken, $userNotification->title, $userNotification->message);
                            }
                            if ($companyAdminUser->device_type == 1) {
                                $this->sendAndroidNotification($companyAdminUser->deviceToken, $userNotification->title, $userNotification->message);
                            }
                        }
                    }
                }


                return response()->json(array('status' => true,'message' => 'Docket rejected successfully.'));

            }else{
                return response()->json(array('status' => true,'message' => 'Docket already rejected.'));

            }

        }else{
            return response()->json(array('status' => true,'message' => 'Docket already rejected.'));

        }









    }


     public  function  receiptValidator(Request $request){


         $validator = new iTunesValidator(iTunesValidator::ENDPOINT_SANDBOX);
         $receiptBase64Data = $request->purchase_token;

         try {
             $response = $validator->setReceiptData($receiptBase64Data)->validate();
              $sharedSecret = '8b7a80de3f6441dfbdd58964b5525261'; // Generated in iTunes Connect's In-App Purchase menu
              $response = $validator->setSharedSecret($sharedSecret)->setReceiptData($receiptBase64Data)->validate();
         } catch (Exception $e) {
             return response()->json(array('status' => false,'message' => 'got error'. $e->getMessage()));
         }
         if ($response->isValid()) {
             $data = array();
             foreach ($response->getPurchases() as $purchase) {
                $data[] = $purchase->getRawResponse();
             }
              if (count(AppleSubscription::where('company_id',$request->header('companyId'))->get()) == 0){
                  foreach ($data as $datas){
                      $applepurchase = new AppleSubscription();
                      $applepurchase->product_id = $datas['product_id'];
                      $applepurchase->company_id =  $request->header('companyId');
                      $applepurchase->transaction_id = $datas['transaction_id'];
                      $applepurchase->purchase_date = $datas['purchase_date'];
                      $applepurchase->expiry_date = $datas['expires_date'];
                      if ($applepurchase->save()){
                          Company::where('id',$request->header('companyId'))->update(['trial_period'=>4]);

                      }
                  }
              }else{
                  $rowData = AppleSubscription::where('company_id',$request->header('companyId'))->get();
                  foreach($rowData as $row){
                      $row->delete();
                  }
                  foreach ($data as $datas){
                      $applepurchase = new AppleSubscription();
                      $applepurchase->product_id = $datas['product_id'];
                      $applepurchase->company_id =  $request->header('companyId');
                      $applepurchase->transaction_id = $datas['transaction_id'];
                      $applepurchase->purchase_date = $datas['purchase_date'];
                      $applepurchase->expiry_date = $datas['expires_date'];
                      if ($applepurchase->save()){
                          Company::where('id',$request->header('companyId'))->update(['trial_period'=>4]);
                      }
                  }

              }
             if (Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count() !=0){
                 $appleSubscriptions = AppleSubscription::where('company_id',$request->header('companyId'))->get()->last();
                 $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($appleSubscriptions->expiry_date)->format('Y-m-d H:i:s'), 'UTC');
                 $timestamp->setTimezone('Australia/Canberra');
                 $now = Carbon::now();
                 if ($timestamp->gt($now)){
                     $companyAdmin = Company::where('user_id',$request->header('userId'))->count();
                     $company_admin = ($companyAdmin == 0) ? false : true;

                     $stripeSubcription= Company::where('user_id',$request->header('userId'))->where('trial_period',2)->count();
                     if ( $stripeSubcription == 0){
                         $stripe_subcription = false;
                     }else{
                         $stripe_subcription = true;
                     }

                     $appleSubcription = Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count();
                     $apple_subcription = ($appleSubcription == 0) ? false : true ;
                     return response()->json(array('status' => true,'company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription));

                 }

             }else{

                 $companyAdmin = Company::where('user_id',$request->header('userId'))->count();
                 $company_admin = ($companyAdmin == 0) ? false : true;

                 $stripeSubcription= Company::where('user_id',$request->header('userId'))->where('trial_period',2)->count();
                 if ( $stripeSubcription == 0){
                     $stripe_subcription = false;
                 }else{
                     $stripe_subcription = true;
                 }

                 $appleSubcription = Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count();
                 $apple_subcription = ($appleSubcription == 0) ? false : true ;
                 return response()->json(array('status' => true,'company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription));
             }


         } else {
             return response()->json(array('status' => false,'message' =>'Receipt is not valid. Receipt result code = ' . $response->getResultCode()));
         }



     }

     public  function  subscriptionStatus(Request $request){

        $company = Company::where('user_id',$request->header('userId'));

//        if ($company->where('trial_period',3)->count() ==0){
            $appleSubscriptions = AppleSubscription::where('company_id',$request->header('companyId'))->get()->last();
            $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($appleSubscriptions->expiry_date)->format('Y-m-d H:i:s'), 'UTC');
            $timestamp->setTimezone('Australia/Canberra');

            $now = Carbon::now();
            if ($timestamp->gt($now)){
                $companyAdmin = Company::where('user_id',$request->header('userId'))->count();
                $company_admin = ($companyAdmin == 0) ? false : true;

                $stripeSubcription= Company::where('user_id',$request->header('userId'))->where('trial_period',2)->count();
                if ( $stripeSubcription == 0){
                    $stripe_subcription = false;
                }else{
                    $stripe_subcription = true;
                }
//                $free_user = ($freeUser == 0) ? false : true ;

                $appleSubcription = Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count();
                $apple_subcription = ($appleSubcription == 0) ? false : true ;

                return response()->json(array('status' => true,'company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription));

            }
            elseif ($timestamp->lt($now)){
//                Company::where('user_id',$request->header('userId'))->update(['trial_period'=>3]);
                $companyAdmin = Company::where('user_id',$request->header('userId'))->count();
                $company_admin = ($companyAdmin == 0) ? false : true;

                $stripeSubcription= Company::where('user_id',$request->header('userId'))->where('trial_period',2)->count();
                if ( $stripeSubcription == 0){
                    $stripe_subcription = false;
                }else{
                    $stripe_subcription = true;
                }

                $appleSubcription = Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count();
                $apple_subcription = ($appleSubcription == 0) ? false : true ;
                return response()->json(array('status' => true,'company_admin'=>$company_admin ,'stripe_subcription'=>$stripe_subcription,'apple_subcription'=>$apple_subcription));

            }
//        }
//        else{
//            $companyAdmin = Company::where('user_id',$request->header('userId'))->count();
//            $company_admin = ($companyAdmin == 0) ? false : true;
//            $freeUser = Company::where('user_id',$request->header('userId'))->where('trial_period',3)->count();
//            $firstMonth =  Company::where('user_id',$request->header('userId'))->where('trial_period',0)->count();
//            if ( $freeUser == 0 || $firstMonth == 0 ){
//                $free_user = false;
//            }else{
//                $free_user = true;
//            }
//            $appleSubcription = Company::where('user_id',$request->header('userId'))->where('trial_period',4)->count();
//            $apple_subcription = ($appleSubcription == 0) ? false : true ;
//            return response()->json(array('status' => true,'company_admin'=>$company_admin ,'free_user'=>$free_user,'apple_subcription'=>$apple_subcription));
//        }


     }

    function unique_multidim_array($array, $key) {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }




    public  function getInvoiceDocketFilterParameter(Request $request){
        $userId = $request->record_time_user;
        $totalSentDocketID  =    array();
        $receiverCompanyId  =    $this->getCompanyId($userId);
        $receiverCompanyUserId  =   $this->getCompanyAllUserId($receiverCompanyId);

        $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',$request->header('companyId'))->where('invoiceable',1)->orderBy('id','desc')->get();
        foreach($sentDocketQueryTemp as $sentDocket){
            if($sentDocket->sentDocketRecipientApproval){
                foreach ($sentDocket->sentDocketRecipientApproval as $approvalUserID){
                    $status     =    true;
                    if(!in_array($approvalUserID->user_id,$receiverCompanyUserId)){
                        $status =   false;
                        break;
                    }
                }
            }
            if($status){
                $totalSentDocketID[]    =   $sentDocket->id;
            }
        }


        $sentDocketQuery    =    SentDockets::whereIn('id',$totalSentDocketID);

        if($sentDocketQuery->count()>0) {
            $resultQuery = $sentDocketQuery->orderBy('created_at', 'desc')->get();

            $docketName = array();
            $amounts = array();

            foreach ($resultQuery as $result) {
                $docketName[] = array(
                    'id'=>$result->docketInfo->id,
                    'title' => $result->docketInfo->title
                );
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    if(is_numeric($unitRate[0]["value"])){
                        $unitRate1= $unitRate[0]["value"];
                    }else{
                        $unitRate1=0;
                    }
                    if(is_numeric($unitRate[1]["value"])){
                        $unitRate2= $unitRate[1]["value"];
                    }else{
                        $unitRate2= 0;
                    }
                    $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                }
                $amounts[] = $invoiceAmount;
            }
            $uniqueDocketName = $this->unique_multidim_array($docketName,'id');
            $doc = array();
            foreach ($uniqueDocketName as $uniqueDocketNames){
                $doc[] = array(
                    'id'=>$uniqueDocketNames['id'],
                    'title' => $uniqueDocketNames['title']
                );
            }
            $data = array();
            $data['docket_template'] = $doc;
            $data['range'] = array(
                'min'=>min($amounts),
                'max'=>max($amounts));
            return response()->json($data);
        }
    }

    public  function filterInvoiceableDocket(Request $request){
        $userId = $request->record_time_user;
        $totalSentDocketID  =   array();

        //get company superadmin, admins user id
        $admin  =   array();
        $admin    =   Employee::where('company_id',$request->header('companyId'))->where('is_admin',1)->where('employed',1)->pluck('user_id')->toArray();
        $admin[]   =   Company::where('id',$request->header('companyId'))->first()->user_id;

        if(in_array($request->header('userId'),$admin)){
            $sentDocketQueryTemp    =   SentDockets::where('sender_company_id',$request->header('companyId'))->where('invoiceable',1)->orderBy('id','desc')->get();
            // dd($sentDocketQueryTemp->pluck('id'));
        }else{
            $sentDocketQueryTemp              =   SentDockets::where('user_id',$request->header('userId'))->where('invoiceable',1)->orderBy('id','desc')->get();
        }
        foreach($sentDocketQueryTemp as $sentDocket){
            if ($sentDocket->recipientInfo->count() == 1){
                // echo $sentDocket->id."<br/>";
                if ($sentDocket->recipientInfo->first()->user_id == $userId) {
                    $totalSentDocketID[] = $sentDocket->id;
                }
            }else if($sentDocket->recipientInfo->count()>=2){
                //get all recipients by sent dockets id
                $tempSentDocketRecipient    =    $sentDocket->recipientInfo->pluck('user_id')->toArray();
                if ($this->array_equal($tempSentDocketRecipient,array($request->header('userId'),$userId))) {
                    $totalSentDocketID[]    =   $sentDocket->id;
                }
            }
        }
        $sentDocketQuery    =    SentDockets::whereIn('id',$totalSentDocketID);


        if(Input::has("to")){
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }



        if($request->docket_template_id && $request->docket_template_id != null){
          $sentDocketQuery->whereIn('docket_id',$request->docket_template_id);
        }

        if($request->sent_docket_id && $request->sent_docket_id != null){
            $sentDocketQuery->where('id',$request->sent_docket_id);
        }

        $rangeValue = array();
        if ($request->min_amount!= '' && $request->min_amount != ''&& Input::has('min_amount') && Input::has('max_amount')){
            $range = array();
            foreach ($sentDocketQuery->get()  as $sentDocketQuerys  ){
                if ($sentDocketQuerys->sender_company_id == $request->header('companyId')){
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$sentDocketQuerys->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                        if(is_numeric($unitRate[0]["value"])){
                            $unitRate1= $unitRate[0]["value"];
                        }else{
                            $unitRate1=0;
                        }
                        if(is_numeric($unitRate[1]["value"])){
                            $unitRate2= $unitRate[1]["value"];
                        }else{
                            $unitRate2= 0;
                        }
                        $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
//                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    $range[] =
                        array(
                        'docket_id'=>$sentDocketQuerys->docketInfo->id,
                            'amount'=> $invoiceAmount

                    );
                }

            }


            $rangeData = new Collection($range);


            if ($request->min_amount != '' && $request->max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $request->min_amount && $rangeDatas['amount'] <= $request->max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }

        }





        $filterData    =   $sentDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableDockets =   array();
        foreach ($filterData as $result){
            if ($result->sender_company_id == $request->header('companyId')):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $senderImage = $result->senderUserInfo->image;
                $company = $result->senderCompanyInfo->name;
                $recipientsQuery    =   $result->recipientInfo;
                $recipientData      =   "";
                foreach($recipientsQuery as $recipient) {
                    if($recipient->id==$recipientsQuery->first()->id)
                        $recipientData  =   $recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                    else
                        $recipientData  =   $recipientData.", ".$recipient->userInfo->first_name." ".$recipient->userInfo->last_name;
                }
                //approval text
                $totalRecipientApprovals    =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->count();
                $totalRecipientApproved     =   SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('status',1)->count();

                //check is approval
                $isApproval                 =   0;
                $isApproved                 =   0;

                if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->count()==1){
                    $isApproval             =   1;

                    //check is approved
                    if(SentDocketRecipientApproval::where('sent_docket_id',$result->id)->where('user_id',$request->header('userId'))->where('status',1)->count()==1){
                        $isApproved             =   1;
                    }
                }
                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }
                $invoiceDescription     =    array();
                $invoiceDescriptionQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',1)->get();
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentDocketValueInfo->label,'value' => $description->sentDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentDocketInvoice::where('sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                    if(is_numeric($unitRate[0]["value"])){
                        $unitRate1= $unitRate[0]["value"];
                    }else{
                        $unitRate1=0;
                    }
                    if(is_numeric($unitRate[1]["value"])){
                        $unitRate2= $unitRate[1]["value"];
                    }else{
                        $unitRate2= 0;
                    }
                    $invoiceAmount   =   $invoiceAmount + $unitRate1 * $unitRate2;
                }
                if (!Input::has('min_amount') && !Input::has('max_amount')){
                    $invoiceableDockets[] = array('id' => $result->id,
                        'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
                        'user_id' => $result->user_id,
                        'docketName' => $result->docketInfo->title,
                        'docketTemplateId' => $result->docketInfo->id,
                        'sender' => $userName,
                        'company' => $company,
                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                        'invoiceDescription' => $invoiceDescription,
                        'invoiceAmount' => $invoiceAmount,
                        'recipient'=>$recipientData,
                        'senderImage'=>AmazoneBucket::url() . $senderImage,
                        'status' => $approvalText,
                        'isApproval'=>$isApproval,
                        'isApproved'=>$isApproved,
                    );
                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $invoiceableDockets[] = array('id' => $result->id,
                            'companyDocketId'=>'rt-'.$result->sender_company_id.'-doc-'.$result->company_docket_id,
                            'user_id' => $result->user_id,
                            'docketName' => $result->docketInfo->title,
                            'docketTemplateId' => $result->docketInfo->id,
                            'sender' => $userName,
                            'company' => $company,
                            'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                            'invoiceDescription' => $invoiceDescription,
                            'invoiceAmount' => $invoiceAmount,
                            'recipient'=>$recipientData,
                            'senderImage'=>AmazoneBucket::url() . $senderImage,
                            'status' => $approvalText,
                            'isApproval'=>$isApproval,
                            'isApproved'=>$isApproved,
                        );
                    }
                }
                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;

        }
        return response()->json(array('status' => true, 'invoiceableDockets' =>$invoiceableDockets));

    }


    public  function getInvoiceEmailDocketFilterParameter(Request $request){
        $emailSentDocket = EmailSentDocket::where('user_id',$request->header('userId'))->where('company_id',$request->header('companyId'))->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $request->record_time_user){
                    $arrays[]=$items->email_sent_docket_id;
                }
            }
        }
        $matchEmailDocket = EmailSentDocket::whereIn('id',$arrays);
        if($matchEmailDocket->count()>0) {
            $docketDetail = array();
            $amounts = array();
            $resultQuery = $matchEmailDocket->orderBy('created_at', 'desc')->get();
            foreach ($resultQuery as $result) {
                $docketDetail[] = array(
                    'id'=>$result->docketInfo->id,
                    'title' => $result->docketInfo->title
                );

                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];

                }
                $amounts[] = $invoiceAmount;

            }
            $uniqueDocketName = $this->unique_multidim_array($docketDetail,'id');
            $doc = array();
            foreach ($uniqueDocketName as $uniqueDocketNames){
                $doc[] = array(
                    'id'=>$uniqueDocketNames['id'],
                    'title' => $uniqueDocketNames['title']
                );
            }
            $data = array();
            $data['docket_template'] = $doc;
            $data['range'] = array(
                'min'=>min($amounts),
                'max'=>max($amounts));
            return response()->json($data);
        }

    }



    public  function filterInvoiceableEmailDocket(Request $request){
        $userId = $request->record_time_user;
        $emailSentDocket = EmailSentDocket::where('user_id',$request->header('userId'))->where('company_id',$request->header('companyId'))->get();
        $arrays = array();
        foreach ($emailSentDocket as $row){
            foreach ($row->recipientInfo as $items){
                if ($items->email_user_id == $userId){
                    $arrays[]=$items->email_sent_docket_id;
                }
            }
        }

        $sentEmailDocketQuery = EmailSentDocket::whereIn('id',$arrays);
        if(Input::has("to")){
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','>=',Carbon::parse($request->from)->format('Y-m-d'));
            }
            if($request->to ){
                $sentEmailDocketQuery->whereDate('created_at','<=',Carbon::parse($request->to )->format('Y-m-d'));
            }
        }else{
            if($request->from ){
                $sentEmailDocketQuery->whereDate('created_at','==',Carbon::parse($request->from)->format('Y-m-d'));
            }
        }


        if($request->docket_template_id && $request->docket_template_id != null){
            $sentEmailDocketQuery->whereIn('docket_id',$request->docket_template_id);
        }

        if($request->sent_docket_id && $request->sent_docket_id != null){
            $sentEmailDocketQuery->where('id',$request->sent_docket_id);
        }


        $rangeValue = array();
        if ($request->min_amount!= '' && $request->min_amount != ''&& Input::has('min_amount') && Input::has('max_amount')){
            $range = array();
            foreach ($sentEmailDocketQuery->get()  as $sentDocketQuerys  ){
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$sentDocketQuerys->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];

                    }

                    $range[] =
                        array(
                            'docket_id'=>$sentDocketQuerys->docketInfo->id,
                            'amount'=> $invoiceAmount

                        );

            }


            $rangeData = new Collection($range);

            if ($request->min_amount != '' && $request->max_amount != ''){
                foreach ($rangeData as $rangeDatas){
                    if ($rangeDatas['amount'] >= $request->min_amount && $rangeDatas['amount'] <= $request->max_amount){
                        $rangeValue[] = $rangeDatas['amount'];
                    }
                }
            }
        }

        $filterData    =   $sentEmailDocketQuery->orderBy('created_at', 'desc')->get();
        $invoiceableEmailDockets =   array();
        foreach ($filterData as $result) {
            if ($result->company_id == $request->header('companyId')):
                $userName = $result->senderUserInfo->first_name . " " . $result->senderUserInfo->last_name;
                $company = $result->senderCompanyInfo->name;
                $senderImage = $result->senderCompanyInfo->userInfo->image;
                $recipientName  =    "";
                foreach($result->recipientInfo as $recipient) {
                    if($recipient->receiver_full_name!="" && $recipient->receiver_full_name!=null){
                        $recipientName  =   $recipientName."".$recipient->receiver_full_name;
                    }else{
                        $recipientName  =   $recipientName."".$recipient->emailUserInfo->email;
                    }
                    if ($result->recipientInfo->count() > 1)
                        if ($result->recipientInfo->last()->id != $recipient->id){
                            $recipientName  = $recipientName.", ";
                        }

                }
                //approval text
                $totalRecipientApprovals    =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->count();
                $totalRecipientApproved     =   EmailSentDocketRecipient::where('email_sent_docket_id',$result->id)->where('approval',1)->where('status',1)->count();
                // $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";

                if ($totalRecipientApproved == $totalRecipientApprovals ){
                    $approvalText               =  "Approved";
                }else{
                    $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                }


                $invoiceDescription     =    array();
                $invoiceDescriptionQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',1)->get();
                foreach($invoiceDescriptionQuery as $description){
                    $invoiceDescription[]   =   array('label'=> $description->sentEmailDocketValueInfo->label,'value' => $description->sentEmailDocketValueInfo->value);
                }
                $invoiceAmount  =    0;
                $invoiceAmountQuery    =    SentEmailDocketInvoice::where('email_sent_docket_id',$result->id)->where('type',2)->get();
                foreach($invoiceAmountQuery as $amount){
                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                }

                if (!Input::has('min_amount') && !Input::has('max_amount')){
                    $invoiceableEmailDockets[] = array('id' => $result->id,
                        'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                        'user_id' => $result->user_id,
                        'docketName' => $result->docketInfo->title,
                        'docketTemplateId' => $result->docketInfo->id,
                        'sender' => $userName,
                        'company' => $company,
                        'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                        'invoiceDescription' => $invoiceDescription,
                        'invoiceAmount' => $invoiceAmount,
                        'status' => $approvalText,
                        'recipient'=>$recipientName,
                        "isApproved"    => $result->status,
                        'senderImage'=>AmazoneBucket::url() . $senderImage,
                    );
                }else{
                    if ( in_array($invoiceAmount,array_unique($rangeValue))){
                        $invoiceableEmailDockets[] = array('id' => $result->id,
                            'companyDocketId'=>'rt-'.$result->company_id.'-edoc-'.$result->company_docket_id,
                            'user_id' => $result->user_id,
                            'docketName' => $result->docketInfo->title,
                            'docketTemplateId' => $result->docketInfo->id,
                            'sender' => $userName,
                            'company' => $company,
                            'dateAdded' => Carbon::parse($result->created_at)->format('d-M-Y'),
                            'invoiceDescription' => $invoiceDescription,
                            'invoiceAmount' => $invoiceAmount,
                            'status' => $approvalText,
                            'recipient'=>$recipientName,
                            "isApproved"    => $result->status,
                            'senderImage'=>AmazoneBucket::url() . $senderImage,
                        );
                    }
                }


                empty($invoiceDescription);
                empty($invoiceAmount);
            endif;
        }
        return response()->json(array('status' => true, 'invoiceableEmailDockets' =>$invoiceableEmailDockets));
    }

    public  function myPermission(Request $request){
        $company = Company::where("id",$request->header('companyId'))->first();
        if ($request->header('userId') == $company->user_id){
            $userType = "superAdmin";
        }else{
            $userType = "employee";
        }
        $myPermission = array();
        if ($userType == "superAdmin"){
            if ($company->can_docket== 1) {
                $myPermission[] = array('id' => 1, 'name' => 'Docket');
            }
            if ($company->can_invoice== 1) {
                $myPermission[] = array('id' => 2, 'name' => 'Invoice');
            }
            if ($company->can_timer== 1) {
                $myPermission[] = array('id' => 3, 'name' => 'Timer');
            }
        }elseif ($userType == "employee"){
            $employee = Employee::where('user_id',$request->header('userId'))->where('company_id',$request->header('companyId'))->first();
            if ($employee->docket== 1){
                $myPermission[]  =   array('id' => 1, 'name' => 'Docket');
            }

            if ($employee->invoice== 1){
                $myPermission[]  =   array('id' => 2, 'name' => 'Invoice');
            }

            if ($employee->timer== 1){
                $myPermission[] =   array('id' => 3, 'name' => 'Timer');
            }
        }

        $company = Company::where('id',$request->header('companyId'))->first();
        $free_user = ($company->trial_period== 3 ) ? 0 : 1;

//        if($company->trial_period==3){
            //get last subscription created date
            $subscriptionLogQuery    =   SubscriptionLog::where('company_id',$company->id);
            if($subscriptionLogQuery->count()>0){
                $lastUpdatedSubscription    =    $subscriptionLogQuery->orderBy('id','desc')->first();
                $monthDay   =    Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
                $now    =   Carbon::now();
                $currentMonthStart  =   Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay);
                if($now->gte($currentMonthStart)){
                    $currentMonthEnd = Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->addDay(30);
                }else{
                    $currentMonthEnd =   $currentMonthStart;
                    $currentMonthStart =      Carbon::parse($now->format('Y')."-".$now->format('m')."-".$monthDay)->subDays(30);
                }
            }else{
                $currentMonthStart = new Carbon('first day of this month');
                $currentMonthEnd = new Carbon('last day of this month');
            }

            $sentDockets    =   SentDockets::where('sender_company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
            $emailDockets   =   EmailSentDocket::where('company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();

            $sentInvoice = SentInvoice::where('company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();
            $emailInvoice = EmailSentInvoice::where('company_id',$company->id)->whereBetween('created_at',array($currentMonthStart,$currentMonthEnd))->count();

            $totalMonthDockets  =   $sentDockets + $emailDockets;
            $totalMonthInvoices  =   $sentInvoice + $emailInvoice;

            if ($totalMonthDockets>=5){
                $docket_limit = 0;
            }else{
                $docket_limit = 5-$totalMonthDockets;
            }

            if ($totalMonthInvoices>=1){
                $invoice_limit = 0;
            }else{
                $invoice_limit = 1-$totalMonthInvoices;
            }
//        }
        $user   =   User::find($request->header('userId'));
        $user->slackChannel('rt-app-reopen')->notify(new AppOpen($user));
        return response()->json(array('free_user'=>$free_user,'docket_limit'=>$docket_limit,'invoice_limit'=>$invoice_limit,'permission'=>$myPermission));
    }


    public  function draftImageSave(Request $request){
        $number = 0;
        $date = Carbon::now()->format('d-M-Y');
        $response_array = array();
            foreach($request->all() as $key => $imgs){
                if (explode("-",$key)[2]=="row"){
                    $imagePram = intval(explode("-",$key)[1]);
                    $imageRowId = intval(explode("-",$key)[3]);
                    $imageGridId = intval(explode("-",$key)[5]);
                    $arrayImage = array();
                    foreach ($imgs as  $keys => $img){
                        // $ext = $img->getClientOriginalExtension();
                        // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                        $dest = 'files/draft/images/'.$date;
                        // $img->move($dest, $filename);
                        // $arrayImage[] = array(
                        //     'url' =>  asset($dest . '/' . $filename),
                        //     'index'=>$keys,
                        // );

                        $arrayImage[] = array(
                            'url' =>  FunctionUtils::imageUpload($dest,$img),
                            'index'=>$keys,
                        );
                        $number++;
                    }
                    $response_array[] = array("id" => $imagePram,"row"=>$imageRowId,"sub_field_id"=>$imageGridId, "images" => $arrayImage);
                } elseif (explode("-",$key)[2]=="yesNo"){
                    $imagePram = intval(explode("-",$key)[1]);
                    $imageYesNoId = intval(explode("-",$key)[3]);
                    $arrayImage = array();
                    foreach ($imgs  as  $keys => $img){
                        // $ext = $img->getClientOriginalExtension();
                        // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                        $dest = 'files/draft/images/'.$date;
                        // $img->move($dest, $filename);
                        // $arrayImage[] = array(
                        //     'url' =>  asset($dest . '/' . $filename),
                        //     'index'=>$keys,
                        // );

                        $arrayImage[] = array(
                            'url' =>  FunctionUtils::imageUpload($dest,$img),
                            'index'=>$keys,
                        );
                        $number++;
                    }
                    $response_array[] = array("id" => $imagePram,"sub_field_id"=>$imageYesNoId, "images" => $arrayImage);

                }else{
                    $imagePram = intval(explode("-",$key)[1]);
                    $arrayImage = array();
                    foreach ($imgs as  $keys => $img){
                        // $ext = $img->getClientOriginalExtension();
                        // $filename = basename($img->getClientOriginalName(), '.' . $img->getClientOriginalExtension()) . time()."-".$number. "." . $ext;
                        $dest = 'files/draft/images/'.$date;
                        // $img->move($dest, $filename);
                        // $arrayImage[] = array(
                        //     'url' =>  asset($dest . '/' . $filename),
                        //     'index'=>$keys,
                        // );

                        $arrayImage[] = array(
                            'url' =>  FunctionUtils::imageUpload($dest,$img),
                            'index'=>$keys,
                        );
                        $number++;
                    }
                    $response_array[] = array("id" => $imagePram, "images" => $arrayImage);
                }
        }
        return response()->json($response_array);
    }


    public  function saveDocketDraft(Request $request){
        $validator  =   Validator::make(Input::all(),['docket_id'=>'required','value'=>'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $docketId = json_decode($request->value, true)['template']['id'];
            if(Docket::find($docketId)){
                $docketDraft = new DocketDraft();
                $docketDraft->user_id = $request->header('userId');
                $docketDraft->docket_id = json_decode($request->value, true)['template']['id'];
                $docketDraft->value = $request->value;
                $docketDraft->is_draft = ($request->has('is_draft')) ? 1 : 0;
                $docketDraft->is_admin = ($request->has('is_admin')) ? 1 : 0;
                $docketDraft->save();
                return response()->json(array('status' => true,'message' => "Docket draft saved successfully.", 'docket_draft' => array('draft_id' => $docketDraft->id)));
            }else{
                return response()->json(array('status' => false,'message' => "Invalid Docket Id"));

            }


        endif;
    }


    public  function updateDocketDraft(Request $request){
        $validator  =   Validator::make(Input::all(),['draft_id'=>'required','docket_id'=>'required','value'=>'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            $updateDocketDraft = DocketDraft::where('id',$request->draft_id)->where('docket_id',$request->docket_id)->first();
            if ($updateDocketDraft){
                DocketDraft::where('id',$request->draft_id)->update(['value'=>$request->value]);
                return response()->json(array('status' => true,'message' => "Docket draft update sucessfully."));
            }else{
                return response()->json(array('status' => false,'message' => "Invalid Data"));
            }
        endif;
    }
    public function getDocketDraftList(Request $request){
        $docketDraft = DocketDraft::where('user_id',$request->header('userId'))->get();
        $docketDraftList = array();
        if (count($docketDraft)>0){
            foreach ($docketDraft as $row){
                $docketDraftList [] = array(
                    'draft_id'=>$row->id,
                    'user_id'=>$row->user_id,
                    'docket_id'=>$row->docket_id,
                    'value'=>$row->value,
                );
            }

        }
        return response()->json(array('docket_draft_list' => $docketDraftList));
    }



    public function updateDeviceToken(Request $request){
        $validator  =   Validator::make(Input::all(),['device_token'=>'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){ $errors[]=$messages[0]; }
            return response()->json(array('status' => false,'message' => $errors));
        else:
            User::where('id',$request->header('userId'))->update(['deviceToken' => $request->device_token]);
            return response()->json(array('status' => true,'message' => "Device Token update sucessfully."));
        endif;
    }


    public static function convertHrsMin($parameter) {
        $minutes =  ($parameter/(1000*60))%60;
        $hours = ($parameter /(1000*60*60))%1000000;
        $hours = ($hours < 10) ? "0" + $hours : $hours;
        $minutes = ($minutes < 10) ? "0" + $minutes : $minutes;

        if ($hours == 1 || $hours == 0 ){
            $hoursParm = " Hour";
        }else{
            $hoursParm = " Hours";
        }

        if ($minutes == 1 || $minutes == 0 ){
            $minutesParm = " Minute";
        }else{
            $minutesParm = " Minutes";
        }

        return $hours.$hoursParm ." ".$minutes.$minutesParm;
    }

//    public function  search($array,$key){
//        $results = array();
//        if (is_array($array)) {
//            if (isset($array[$key])) {
//                $results[] = $array;
//            }
//        }
//        return $results;
//        dd($results['']);
//    }

    public function v1getDocketTemplateDetailsById(Request $request,$id){
        if(!$request->headers->has('companyId')){
            if(isset(auth()->user()->id)){
                $request->headers->set('userId', auth()->user()->id);
                $request->headers->set('companyId', auth()->user()->companyInfo->id);
            }
        }
        ini_set('memory_limit','256M');
        set_time_limit(0);
        if(Docket::where('id',$id)->count()>0){
            $docket     =   Docket::where('id',$id)->first();
            $docketFieldQuery    =   DocketField::where('docket_id',$docket->id)->orderBy('order','asc')->get();
            $docketFields   =   array();
            foreach ($docketFieldQuery as $row){
                $subField   =   array();
                if($row->docket_field_category_id == 7) {
                    $subField = DocketUnitRate::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subField,
                        'subFieldUnitRate' => $subField);
                }elseif ($row->docket_field_category_id==13){
                    $subField = DocketFieldFooter::select('id', 'value')->where("field_id", $row->id)->orderBy('id', 'asc')->get();
                    $footers = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField'  => $subField,
                        'subFieldFooter' => $subField);

                }elseif ($row->docket_field_category_id == 29) {
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }


                    }elseif($row->is_dependent == 2){

                        $defaultPrefillerValue = "";
                        $canAddChild = false;

                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                $prefiller = array();
                                $arrayIndex =   array();
                                if(count($arrayData) > 100000){
                                    $isBigData = true;
                                }else{
                                    if($row->prefillerEcowise){
                                        if($row->linkPrefillerFilter){
                                            foreach ($row->linkPrefillerFilter as $linkPrefillerFilters){
                                                $filtervalue = json_decode($row->prefillerEcowise->data, true)[$linkPrefillerFilters->link_prefiller_filter_label];
                                                $withoutFilterData = array();
                                                foreach ($filtervalue as $keyValues=>$filtervalues){
                                                    if($linkPrefillerFilters->link_prefiller_filter_value == "Empty Data"){
                                                        if($filtervalues == []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }elseif($linkPrefillerFilters->link_prefiller_filter_value == "Not Empty Data"){
                                                        if($filtervalues != []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }else{
                                                        if($linkPrefillerFilters->link_prefiller_filter_value == $filtervalues){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }
                                                }
                                                if(count($arrayIndex)== 0){
                                                    $arrayIndex = $withoutFilterData;
                                                }else{
                                                    $arrayIndex = array_intersect($withoutFilterData,$arrayIndex);
                                                }
                                            }
                                        }
                                    }
                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }

                            }
                        }


                    }else{

                        if(count($row->docketPreFiller) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }

                    }

                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$defaultPrefillerValue),
                        'required'=>$row->required,
                        'subField'  => $subField,
                        'send_copy_docket'=>$row->send_copy_docket);


                }elseif ($row->docket_field_category_id==20) {
                    $subField = DocketManualTimer::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $breakSubField = DocketManualTimerBreak::select('id','type', 'label','explanation')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $merge= array();
                    foreach($subField as $rowDtas){
                        $merge[]  = array('id' =>$rowDtas->id,
                            'type'=>$rowDtas->type,
                            'label'=>$rowDtas->label,

                        );
                    }
                    foreach($breakSubField as $rowDta){
                        $merge[]  = array('id' =>$rowDta->id,
                            'type'=>$rowDta->type,
                            'label'=>$rowDta->label,
                            'explanation'=> intval($rowDta->explanation)
                        );
                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $merge,
                        'manualTimerSubField' => $merge);

                }
                elseif ($row->docket_field_category_id==3){
                    $prefiller = array();
                    $docketFieldNumber = DocketFieldNumber::select('min', 'max','tolerance')->where("docket_field_id", $row->id)->first();
                    if ($docketFieldNumber==null){
                        $docketFieldNumbers = array(
                            'min' => null,
                            'max' => null,
                            'tolerance' => null,
                        );
                    }else{
                        $docketFieldNumbers= $docketFieldNumber;
                    }
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;


                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }
                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                $prefiller = array();
                                $arrayIndex = array();
                                if(count($arrayData) > 100000){
                                    $isBigData = true;
                                }else{
                                    if($row->prefillerEcowise){
                                        if($row->linkPrefillerFilter){
                                            foreach ($row->linkPrefillerFilter as $linkPrefillerFilters){
                                                $filtervalue = json_decode($row->prefillerEcowise->data, true)[$linkPrefillerFilters->link_prefiller_filter_label];
                                                $withoutFilterData = array();
                                                foreach ($filtervalue as $keyValues=>$filtervalues){
                                                    if($linkPrefillerFilters->link_prefiller_filter_value == "Empty Data"){
                                                        if($filtervalues == []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }elseif($linkPrefillerFilters->link_prefiller_filter_value == "Not Empty Data"){
                                                        if($filtervalues != []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }else{
                                                        if($linkPrefillerFilters->link_prefiller_filter_value == $filtervalues){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }
                                                }
                                                if(count($arrayIndex)== 0){
                                                    $arrayIndex = $withoutFilterData;
                                                }else{
                                                    $arrayIndex = array_intersect($withoutFilterData,$arrayIndex);
                                                }
                                            }
                                        }
                                    }
                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }


                            }
                        }
                    }
                    else{

                        if(count($row->docketPreFiller) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }
                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'prefiller_data'=>($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent, 'canAddChild'=>$canAddChild ,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'config'=>$docketFieldNumbers,
                        'subField'  => $subField);


                }
                elseif ($row->docket_field_category_id == 18) {
                    $subFields = array();

                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 1){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 0){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 2){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required' => $row->required,
                        'subField' => $subFields,
                        'yesNoSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id==15) {
                    $subFields= array();
                    foreach($row->docketAttached as $subRow):
                        $subFields[]   =     array(
                            'id' => $subRow->id,
                            'name'=>$subRow->name,
                            'url' => AmazoneBucket::url() . $subRow->url);
                    endforeach;
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subFields,
                        'documentSubField' => $subFields);

                }elseif($row->docket_field_category_id==9){
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    if ($row->is_dependent == 1){

                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }


                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }

                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                if(count($arrayData) > 100000){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    $arrayIndex = array();
                                    if($row->prefillerEcowise){
                                        if($row->linkPrefillerFilter){
                                            foreach ($row->linkPrefillerFilter as $linkPrefillerFilters){
                                                $filtervalue = json_decode($row->prefillerEcowise->data, true)[$linkPrefillerFilters->link_prefiller_filter_label];
                                                $withoutFilterData = array();
                                                foreach ($filtervalue as $keyValues=>$filtervalues){
                                                    if($linkPrefillerFilters->link_prefiller_filter_value == "Empty Data"){
                                                        if($filtervalues == []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }elseif($linkPrefillerFilters->link_prefiller_filter_value == "Not Empty Data"){
                                                        if($filtervalues != []){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }else{
                                                        if($linkPrefillerFilters->link_prefiller_filter_value == $filtervalues){
                                                            $withoutFilterData[] = $keyValues;
                                                        }
                                                    }
                                                }
                                                if(count($arrayIndex)== 0){
                                                    $arrayIndex = $withoutFilterData;
                                                }else{
                                                    $arrayIndex = array_intersect($withoutFilterData,$arrayIndex);
                                                }
                                            }
                                        }
                                    }


                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;

                                }

                            }
                        }
                    }else{
                        if(count($row->docketPreFiller) > 100000){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }

                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'name_required'=> (@$row->docketFieldSignatureOption->name == null) ? 0: @$row->docketFieldSignatureOption->name,
                        'required'=>$row->required ,
                        'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'subField'  => $subField);
                }elseif ($row->docket_field_category_id == 6){

                    $prefiller = array();
                    foreach($row->docketPreFiller as $subRow):
                        $prefiller[]   =  array(
                            'id'=> $subRow->id,
                            'value'=> $subRow->value,
                            'root_id'=> intval($subRow->root_id),
                        );
                    endforeach;
                    $datas = $this->buildTreeArray($prefiller);
                    if($row->default_prefiller_id == null){
                        $defaultPrefillerValue = "";
                    }else{
                        if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                            $defaultPrefillerValue = "";

                        }else{
                            $defaultPrefillerValue = DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->pluck('value')->toArray();
                        }
                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'prefiller'=> $datas ,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'subField'  => $subField);

                }elseif($row->docket_field_category_id == 24){
                    $subField = DocketTallyableUnitRate::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subField,
                    );

                }
                elseif ($row->docket_field_category_id == 22){
                    $modularField  = array();
                    $sumableStatus = false;
                    $canAddChild = true;
                    $isEmailedSubject = false;
                    $isBigData = false;

                    foreach ($row->girdFields as $gridField)
                    {

                        if($gridField->is_emailed_subject == 1){
                            $isEmailedSubject = true;
                        }

                        if ($gridField->docket_field_category_id == 3){
                            if ($gridField->sumable == 1){
                                $sumableStatus = true;
                            }
                        }

                        if ($gridField->is_dependent == 1){
                            if ($gridField->auto_field == 1){
                                $prefiller = array();
                                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();
                                if(count($docketPrefillerValue) > 100000){
                                    $isBigData = true;
                                }else{
                                    foreach($docketPrefillerValue as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->label,
                                            'index'=> $subRow->index,
                                            'docket_field_id'=>$gridField->docket_field_id,
                                            'docket_field_grid_id'=>$gridField->id,
                                            'root_id'=> intval($subRow->root_id),
                                        );
                                    endforeach;
                                    $datas = $this->buildAutoPrefillerTreeArray($prefiller);
                                }
                            }else{
                                $prefiller = array();
                                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();
                                if(count($docketPrefillerValue) > 100000){
                                    $isBigData = true;
                                }else{
                                    foreach($docketPrefillerValue as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->label,
                                            'root_id'=> intval($subRow->root_id),
                                            'index'=> intval($subRow->index),
                                        );
                                    endforeach;
                                    $datas = $this->buildTreeArray($prefiller);
                                }

                            }
                            if($gridField->default_prefiller_id == null){
                                $defaultPrefillerValue = "";
                            }else{
                                if( DocketPrefillerValue::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                                    $defaultPrefillerValue = "";
                                }else{
                                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                                    $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                    $prefillerArray =    array();
                                    foreach ($parentPrefillers as $prefiller) {
                                        $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                        $value = $this->array_values_recursive($parentArray);
                                        $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                        if( count($value) == 0){
                                            $prefillerArray[] = implode(',',$defaultValue);
                                        }else{
                                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                        }
                                    }
                                }
                            }
                            $docketPrefillers = DocketPrefiller::where('id',$gridField->docket_prefiller_id)->first();
                            if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                                $canAddChild = false;
                            }


                        }elseif($gridField->is_dependent == 2){
                            $defaultPrefillerValue = "";
                            $datas = "";

                            $arrayIndex = array();
                            $filterDataArrayIndex = array();

                            if($gridField->prefillerEcowise){
                                if($gridField->linkPrefillerFilter){
                                    foreach ($gridField->linkPrefillerFilter as $linkPrefillerFilters){
                                        $filtervalue = json_decode($gridField->prefillerEcowise->data, true)[$linkPrefillerFilters->link_prefiller_filter_label];
                                        $withoutFilterData = array();
                                        foreach ($filtervalue as $keyValues=>$filtervalues){
                                            if($linkPrefillerFilters->link_prefiller_filter_value == "Empty Data"){
                                                if($filtervalues == []){
                                                    $withoutFilterData[] = $keyValues;
                                                }
                                            }elseif($linkPrefillerFilters->link_prefiller_filter_value == "Not Empty Data"){
                                                if($filtervalues != []){
                                                    $withoutFilterData[] = $keyValues;
                                                }
                                            }else{
                                                if($linkPrefillerFilters->link_prefiller_filter_value == $filtervalues){
                                                    $withoutFilterData[] = $keyValues;
                                                }
                                            }
                                        }

                                        if(count($filterDataArrayIndex)== 0){
                                            $filterDataArrayIndex = $withoutFilterData;
                                        }else{
                                            $filterDataArrayIndex = array_intersect($withoutFilterData,$filterDataArrayIndex);
                                        }
                                    }
                                }
                            }

                            if(count($filterDataArrayIndex) > 100000){
                                $isBigData = true;
                            }else{
                                if ($gridField->auto_field == 1){
                                    $canAddChild = false;
                                    if($gridField->selected_index_value != null){
                                        $firstIndex = str_replace('_', ' ', $gridField->selected_index_value);
                                        $esowise = json_decode($gridField->prefillerEcowise->data,true);
                                        if(array_key_exists($firstIndex,$esowise) == true) {
                                            $firstIndexData = $esowise[$firstIndex];
                                            $prefiller = array();
                                            foreach ($firstIndexData as $key => $firstIndexDatas) {
                                                if(in_array($key,$filterDataArrayIndex)){
                                                    $prefiller[] = array(
                                                        'id' => strval($key + 1),
                                                        'value' => (is_array($firstIndexDatas) == 1) ? "" : $firstIndexDatas,
                                                        'index' => 1,
                                                        'link_grid_field_id' => $gridField->id,
                                                        'root_id' => strval(0),
                                                    );
                                                }
                                            }
                                        }

                                        $ecowiseAutoPrefiller = (new Collection($gridField->gridFieldAutoPreFiller))->sortBy('index');
                                        foreach ($ecowiseAutoPrefiller as $ecowiseAutoPrefillers){
                                            if(array_key_exists(str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index),$esowise) == true) {
                                                $prefillerData  = $esowise[str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index)];
                                                foreach ($prefillerData as $key=> $prefillerDatas){

                                                    if($ecowiseAutoPrefillers->index == 2){
                                                        if(in_array($key,$filterDataArrayIndex)){
                                                            $prefiller[] = array(
                                                                'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                                                'value'=> (is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                                                'index'=> $ecowiseAutoPrefillers->index,
                                                                'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                                                'root_id'=> strval($key+1),
                                                            );
                                                        }
                                                    }else{
                                                        if(in_array($key,$filterDataArrayIndex)){
                                                            $prefiller[] = array(
                                                                'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                                                'value'=>(is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                                                'index'=> $ecowiseAutoPrefillers->index,
                                                                'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                                                'root_id'=> ($key+1)."-".($ecowiseAutoPrefillers->index-1),
                                                            );
                                                        }
                                                    }


                                                }
                                            }

                                        }

                                        $datas =   $this->findEcowisePrefillerValue($prefiller);
                                    }


                                }else{
                                    $canAddChild = false;
                                    if($gridField->selected_index_value != null) {
                                        $keyValue = str_replace('_', ' ', $gridField->selected_index_value);
                                        if(array_key_exists($keyValue,json_decode($gridField->prefillerEcowise->data, true)) == true){
                                            $arrayData = json_decode($gridField->prefillerEcowise->data, true)[$keyValue];
                                            $prefiller = array();
                                            foreach ($arrayData as $key=>$arrayDatas){
                                                if(in_array($key,$filterDataArrayIndex)){
                                                    $prefiller[] = array(
                                                        'id' => 0,
                                                        'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                        'index' => 0,
                                                        'docket_field_id' => $gridField->docket_field_id,
                                                        'docket_field_grid_id' => $gridField->id,
                                                        'root_id' => 0,
                                                    );
                                                }
                                            }
                                            $datas = $prefiller;
                                        }
                                    }
                                }
                            }

                        }else{
                            if ($gridField->auto_field == 1){
                                if(count($gridField->gridFieldPreFiller) > 100000){
                                    $isBigData = true;
                                }else{
                                    $prefillerss = array();
                                    foreach($gridField->gridFieldPreFiller as $subRow):
                                        $prefillerss[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->value,
                                            'index'=> $subRow->index,
                                            'docket_field_id'=>$gridField->docket_field_id,
                                            'docket_field_grid_id'=>$subRow->docket_field_grid_id,
                                            'root_id'=> intval($subRow->root_id),
                                        );
                                    endforeach;
                                    $datas = $this->buildAutoPrefillerTreeArray($prefillerss);
                                }

                            }

                            else{
                                if(count($gridField->gridFieldPreFiller) > 100000){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    foreach($gridField->gridFieldPreFiller as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->value,
                                            'root_id'=> intval($subRow->root_id),
                                            'index'=> intval($subRow->index),
                                        );
                                    endforeach;
                                    $datas = $this->buildTreeArray($prefiller);
                                }

                            }


                            if($gridField->default_prefiller_id == null){
                                $defaultPrefillerValue = "";
                            }else{
                                if( DocketGridPrefiller::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                                    $defaultPrefillerValue = "";
                                }else{
                                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                                    $parentPrefillers = DocketGridPrefiller::whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                    $prefillerArray =    array();
                                    foreach ($parentPrefillers as $prefiller) {
                                        $parentArray= $this->getParentData($prefiller->root_id);
                                        $value = $this->array_values_recursive($parentArray);
                                        $defaultValue   =   DocketGridPrefiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                        if( count($value) == 0){
                                            $prefillerArray[] = implode(',',$defaultValue);
                                        }else{
                                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                        }
                                    }
                                }
                            }
                        }

                        if ($gridField->gridFieldFormula != null){
                            $formulaValue = unserialize($gridField->gridFieldFormula->formula);
                            $formulaArray = array();
                            foreach ($formulaValue as $formulaValues){

                                if (is_numeric($formulaValues)){
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "number"
                                    );
                                }elseif (preg_match("/TDiff/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "function"
                                    );
                                }
                                elseif (preg_match("/cell/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => ltrim($formulaValues, 'cell'),
                                        "type" => "cell"
                                    );
                                }else{
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "operator"
                                    );
                                }



                            }

                        }else{
                            $formulaArray = array();
                        }

                        $gridManualTimer  = array();
                        if($gridField->docket_field_category_id == 20){

                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 1,
                                'label' => "From"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 2,
                                'label' => "To"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 3,
                                'label' => "Total Break",
                                'explanation'=> 0
                            );

                        }

                        $gridAutoFillerDefault = 0;
                        if($gridField->auto_field == 1){
                            $gridAutoFillerDefault = $gridField->default_auto_fill_prefiller;
                        }


                        $data['id'] = $gridField->id;
                        $data['docket_field_id'] = $gridField->docket_field_id;
                        $data['docket_field_category_id'] = $gridField->docketFieldCategory->id;
                        $data['docket_field_category_label'] = $gridField->docketFieldCategory->title;
                        $data['label'] = $gridField->label;
                        $data['order'] = $gridField->order;
                        $data['is_emailed_subject'] = $gridField->is_emailed_subject;
                        $data['required'] = $gridField->required;
                        $data['prefiller_data'] =($isBigData == true) ? array('hasExtraPrefiller'=> true,'autoPrefiller'=>$gridField->auto_field,'gridAutoFillerDefaultId'=>$gridAutoFillerDefault,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>  [] ) : array('autoPrefiller'=>$gridField->auto_field,'gridAutoFillerDefaultId'=>$gridAutoFillerDefault,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=> ($datas == "")? [] : $datas );
                        $data['default_value'] = ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray);
                        $data['subField'] =$gridManualTimer;
                        $data['manualTimerSubField'] = $gridManualTimer;
                        $data['sumable'] = ($gridField->sumable== 1)? true : false ;
                        $data['formula']=  @$formulaArray;
                        $data['send_copy_docket']=  $gridField->send_copy_docket;
                        array_push($modularField, $data);
                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'is_emailed_subject'=>($isEmailedSubject == true) ? 1: 0,
                        'modularGrid' => $modularField,
                        'sumable'=> $sumableStatus,
                        'subField'  => $subField);
                }elseif($row->docket_field_category_id == 28){
                    $templateFolderAssign = TemplateAssignFolder::where('template_id',$id)->get()->first();
                    $subFields= array();
                    $folderName = Folder::where('company_id',$request->header('companyId'))->where('type',0)->orderBy('name','asc')->get();
                    $folderList = array();
                    foreach($folderName as $subRow):
                        $folderList[]   =  array(
                            'id'=> $subRow->id,
                            'name'=> $subRow->name,
                            'root_id'=> intval($subRow->root_id),
                        );
                    endforeach;
                    $folderLists = $this->folderList($folderList);
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subFields,
                        'folderList'=>$folderLists,
                        'default_value'=> ($row->folder_default_id != 0) ? $row->folder_default_id : (($templateFolderAssign == null)? "" : strval($templateFolderAssign->folder_id)),

                        // 'default_value'=> ($templateFolderAssign == null)? "" : strval($templateFolderAssign->folder_id),
                    );
                }elseif ($row->docket_field_category_id != 30) {
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    $datas = [];
                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 100){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }
                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }

                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){

                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                if(count($arrayData) > 100){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    $arrayIndex = array();
                                    if($row->link_prefiller_filter_label){
                                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                                        foreach ($filtervalue as $keyValue=>$filtervalues){
                                            if($row->link_prefiller_filter_value != $filtervalues){
                                                $arrayIndex[] = $keyValue;
                                            }
                                        }
                                    }

                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (!in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }


                            }
                        }
                    }else{
                        if(count($row->docketPreFiller) < 100){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);

                                    }
                                }
                            }
                        }

                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'prefiller_data' =>($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'required'=>$row->required,
                        'subField'  => $subField);
                }

            }
            if(@$footers){
                $docketFields[] =   $footers;
            }

            $isDocketNumber = false;
            $company = Company::where('id',$request->header('companyId'))->first();
            if($company->number_system == 1){
                $isDocketNumber = false;
            }else{
                if($docket->is_docket_number == 1){
                    $isDocketNumber = true;
                }else{
                    $isDocketNumber = false;
                }
            }

            $template =   array(
                'id'=> $docket->id,
                'title'=>$docket->title,
                'subTitle'=>$docket->subTitle,
                'invoiceable'=>$docket->invoiceable,
                'timer_attachement'=>$docket->timer_attachement,
                'docket_field'=> $docketFields,
                'isDocketNumber'=>  $isDocketNumber,
            );
            $data= [];

            if($row->docketInfo->defaultRecipient){
                $rt_user_receivers = array();
                $email_user_receivers = array();
                foreach ($row->docketInfo->defaultRecipient as $defaultRecipients){
                    if(@$defaultRecipients->user_type== 1){
                        if(Employee::where('user_id', $defaultRecipients->userInfo->id)->count()!=0):
                            $companyId = Employee::where('user_id', $defaultRecipients->userInfo->id)->first()->company_id;
                        else :
                            $companyId   =   Company::where('user_id', $defaultRecipients->userInfo->id)->first()->id;
                        endif;
                        $rt_user_receivers[] = array(
                            'user_id'=> $defaultRecipients->userInfo->id,
                            'company_id'=>$companyId,
                            'company_name'=> Company::findorFail($companyId)->name,
                            'company_abn'=>Company::findorFail($companyId)->abn,
                            'first_name'=>$defaultRecipients->userInfo->first_name,
                            'last_name'=>$defaultRecipients->userInfo->last_name,
                            'image'=>AmazoneBucket::url() . $defaultRecipients->userInfo->image
                        );
                    }
                    if(@$defaultRecipients->user_type== 2){
                        if (@$defaultRecipients->emailUser->emailClient->email_user_id == @$defaultRecipients->emailUser->id){
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'full_name'=>$defaultRecipients->emailUser->emailClient->full_name,
                                'company_name'=>$defaultRecipients->emailUser->emailClient->company_name,
                                'company_address'=>$defaultRecipients->emailUser->emailClient->company_address,
                                'saved'=>true
                            );
                        }else{
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'saved'=>false
                            );
                        }
                    }
                }
            }
            $rt_user_approvers =[];
            $email_user_approvers =[];
            return response()->json(array('status' => true,'template'=>$template,'rt_user_receivers'=>$rt_user_receivers,'rt_user_approvers'=>$rt_user_approvers,'email_user_receivers'=>$email_user_receivers,'email_user_approvers'=>$email_user_approvers,'data'=>$data));
        }else{
            return response()->json(array("status" => true,"message"=>'Docket not found!'));
        }

    }

    public function getGridPrefiller(Request $request){
        $gridField = DocketFieldGrid::where('docket_field_id',$request->field_id)->where('id',$request->grid_field_id)->get()->first();

        $canAddChild = true;
        if ($gridField->is_dependent == 1){
            if ($gridField->auto_field == 1){
                $prefiller = array();
                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();
                foreach($docketPrefillerValue as $subRow):
                    $prefiller[]   =  array(
                        'id'=> $subRow->id,
                        'value'=> $subRow->label,
                        'index'=> $subRow->index,
                        'docket_field_id'=>$gridField->docket_field_id,
                        'docket_field_grid_id'=>$gridField->id,
                        'root_id'=> intval($subRow->root_id),
                    );
                endforeach;

                $datas = $this->testbuildAutoPrefillerTreeArray($prefiller);



            }
            else{
                $prefiller = array();
                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();
                foreach($docketPrefillerValue as $subRow):
                    $prefiller[]   =  array(
                        'id'=> $subRow->id,
                        'value'=> $subRow->label,
                        'root_id'=> intval($subRow->root_id),
                        'index'=> intval($subRow->index),
                    );
                endforeach;
                $datas = $this->buildTreeArray($prefiller);


            }
            if($gridField->default_prefiller_id == null){
                $defaultPrefillerValue = "";
            }else{
                if( DocketPrefillerValue::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                    $defaultPrefillerValue = "";
                }else{
                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                    $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                    $prefillerArray =    array();
                    foreach ($parentPrefillers as $prefiller) {
                        $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                        $value = $this->array_values_recursive($parentArray);
                        $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                        if( count($value) == 0){
                            $prefillerArray[] = implode(',',$defaultValue);
                        }else{
                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                        }
                    }
                }
            }
            $docketPrefillers = DocketPrefiller::where('id',$gridField->docket_prefiller_id)->first();
            if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                $canAddChild = false;
            }


        }
        elseif($gridField->is_dependent == 2){
            $defaultPrefillerValue = "";
            $datas = "";

            $arrayIndex = array();
            if($gridField->prefillerEcowise){
                if($gridField->link_prefiller_filter_label){
                    $filtervalue = json_decode($gridField->prefillerEcowise->data, true)[$gridField->link_prefiller_filter_label];
                    foreach ($filtervalue as $keyValue=>$filtervalues){
                        if($gridField->link_prefiller_filter_value != $filtervalues){
                            $arrayIndex[] = $keyValue;
                        }
                    }
                }
            }

            if ($gridField->auto_field == 1){
                $canAddChild = false;
                if($gridField->selected_index_value != null){
                    $firstIndex = str_replace('_', ' ', $gridField->selected_index_value);
                    $esowise = json_decode($gridField->prefillerEcowise->data,true);
                    if(array_key_exists($firstIndex,$esowise) == true) {
                        $firstIndexData = $esowise[$firstIndex];
                        $prefiller = array();
                        foreach ($firstIndexData as $key => $firstIndexDatas) {
                            if(!in_array($key,$arrayIndex)){
                                $prefiller[] = array(
                                    'id' => strval($key + 1),
                                    'value' => (is_array($firstIndexDatas) == 1) ? "" : $firstIndexDatas,
                                    'index' => 1,
                                    'link_grid_field_id' => $gridField->id,
                                    'root_id' => strval(0),
                                );
                            }
                        }
                    }

                    $ecowiseAutoPrefiller = (new Collection($gridField->gridFieldAutoPreFiller))->sortBy('index');
                    foreach ($ecowiseAutoPrefiller as $ecowiseAutoPrefillers){
                        if(array_key_exists(str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index),$esowise) == true) {
                            $prefillerData  = $esowise[str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index)];
                            foreach ($prefillerData as $key=> $prefillerDatas){

                                if($ecowiseAutoPrefillers->index == 2){
                                    if(!in_array($key,$arrayIndex)){
                                        $prefiller[] = array(
                                            'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                            'value'=> (is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                            'index'=> $ecowiseAutoPrefillers->index,
                                            'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                            'root_id'=> strval($key+1),
                                        );
                                    }
                                }else{
                                    if(!in_array($key,$arrayIndex)){
                                        $prefiller[] = array(
                                            'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                            'value'=>(is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                            'index'=> $ecowiseAutoPrefillers->index,
                                            'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                            'root_id'=> ($key+1)."-".($ecowiseAutoPrefillers->index-1),
                                        );
                                    }
                                }


                            }
                        }

                    }

                    $datas =   $this->findEcowisePrefillerValue($prefiller);
                }


            }else{
                $canAddChild = false;
                if($gridField->selected_index_value != null) {
                    $keyValue = str_replace('_', ' ', $gridField->selected_index_value);
                    if(array_key_exists($keyValue,json_decode($gridField->prefillerEcowise->data, true)) == true){
                        $arrayData = json_decode($gridField->prefillerEcowise->data, true)[$keyValue];
                        $prefiller = array();
                        foreach ($arrayData as $key=>$arrayDatas){
                            if(!in_array($key,$arrayIndex)){
                                $prefiller[] = array(
                                    'id' => 0,
                                    'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                    'index' => 0,
                                    'docket_field_id' => $gridField->docket_field_id,
                                    'docket_field_grid_id' => $gridField->id,
                                    'root_id' => 0,
                                );
                            }
                        }
                        $datas = $prefiller;
                    }
                }
            }

        }
        else{
            if ($gridField->auto_field == 1){
                $prefillerss = array();
                foreach($gridField->gridFieldPreFiller as $subRow):
                    $prefillerss[]   =  array(
                        'id'=> $subRow->id,
                        'value'=> $subRow->value,
                        'index'=> $subRow->index,
                        'docket_field_id'=>$gridField->docket_field_id,
                        'docket_field_grid_id'=>$subRow->docket_field_grid_id,
                        'root_id'=> intval($subRow->root_id),
                    );
                endforeach;
                // dd($prefiller);
                $datas = $this->testbuildAutoPrefillerTreeArray($prefillerss);
                // dd($datas);

            }

            else{
                $prefiller = array();
                foreach($gridField->gridFieldPreFiller as $subRow):
                    $prefiller[]   =  array(
                        'id'=> $subRow->id,
                        'value'=> $subRow->value,
                        'root_id'=> intval($subRow->root_id),
                        'index'=> intval($subRow->index),
                    );
                endforeach;
                $datas = $this->buildTreeArray($prefiller);

            }


            if($gridField->default_prefiller_id == null){
                $defaultPrefillerValue = "";
            }else{
                if( DocketGridPrefiller::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                    $defaultPrefillerValue = "";
                }else{
                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                    $parentPrefillers = DocketGridPrefiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                    $prefillerArray =    array();
                    foreach ($parentPrefillers as $prefiller) {
                        $parentArray= $this->getParentData($prefiller->root_id);
                        $value = $this->array_values_recursive($parentArray);
                        $defaultValue   =   DocketGridPrefiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                        if( count($value) == 0){
                            $prefillerArray[] = implode(',',$defaultValue);
                        }else{
                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                        }
                    }

                }
            }
        }
        $gridAutoFillerDefault = 0;
        if($gridField->auto_field == 1){
            $gridAutoFillerDefault = $gridField->default_auto_fill_prefiller;
        }
        $result = array();
        $result['prefiller'] = ($datas == "")? [] : $datas;
        $result['autoPrefiller']= $gridField->auto_field;
        $result['gridAutoFillerDefaultId'] = $gridAutoFillerDefault;
        $result['isDependent'] = $gridField->is_dependent;
        $result['canAddChild']= $canAddChild;
        return response()->json(['status'=>true, 'data'=>$result]);
    }

    public function getPrefiller(Request $request){
        $row = DocketField::where('id',$request->field_id)->get()->first();
        $prefiller = array();
        $canAddChild = true;
        if ($row->is_dependent == 1){

            $prefiller = array();
            $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
            foreach($docketPrefillerValue as $subRow):

                $prefiller[]   =  array(
                    'id'=> $subRow->id,
                    'value'=> $subRow->label,
                    'root_id'=> intval($subRow->root_id),
                    'index'=> intval($subRow->index),
                );

            endforeach;
            $datas = $this->buildTreeArray($prefiller);

            if($row->default_prefiller_id == null){
                $defaultPrefillerValue = "";
            }else{
                if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                    $defaultPrefillerValue = "";
                }else{
                    $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                    $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                    $prefillerArray =    array();
                    foreach ($parentPrefillers as $prefiller) {
                        $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                        $value = $this->array_values_recursive($parentArray);
                        $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                        if( count($value) == 0){
                            $prefillerArray[] = implode(',',$defaultValue);
                        }else{
                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                        }
                    }

                }
            }

            $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
            if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                $canAddChild = false;
            }

        }
        elseif($row->is_dependent == 2){
            $defaultPrefillerValue = "";
            $canAddChild = false;
            if($row->selected_index != null) {
                $keyValue = str_replace('_', ' ', $row->selected_index);
                if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                    $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                    $prefiller = array();
                    $arrayIndex = array();
                    if($row->link_prefiller_filter_label){
                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                        foreach ($filtervalue as $keyValue=>$filtervalues){
                            if($row->link_prefiller_filter_value != $filtervalues){
                                $arrayIndex[] = $keyValue;
                            }
                        }
                    }


                    foreach ($arrayData as $KEY=>$arrayDatas){
                        if (!in_array($KEY,$arrayIndex)){
                            $prefiller[] = array(
                                'id' => 0,
                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas,
                                'index' => 0,
                                'docket_field_id' => $row->id,
                                'root_id' => 0,
                            );
                        }
                    }
                    $datas = $prefiller;
                }
            }
        }
        else{
            foreach($row->docketPreFiller as $subRow):
                $prefiller[]   =  array(
                    'id'=> $subRow->id,
                    'value'=> $subRow->value,
                    'root_id'=> intval($subRow->root_id),
                    'index'=> intval($subRow->index),
                );
            endforeach;
            $datas = $this->buildTreeArray($prefiller);

            if($row->default_prefiller_id == null){
                $defaultPrefillerValue = "";
            }else{
                if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                    $defaultPrefillerValue = "";
                }else{
                    $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                    $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                    $prefillerArray =    array();
                    foreach ($parentPrefillers as $prefiller) {
                        $parentArray= $this->getNormalParentData($prefiller->root_id);
                        $value = $this->array_values_recursive($parentArray);
                        $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                        if( count($value) == 0){
                            $prefillerArray[] = implode(',',$defaultValue);
                        }else{
                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                        }
                    }
                }
            }

        }

        $result = array();
        $result['prefiller'] =  $datas;
        $result['isDependent'] = $row->is_dependent;
        $result['canAddChild']= $canAddChild;
        return response()->json(['status'=>true, 'data'=>$result]);

    }





    function findEcowisePrefillerValue(array $prefiller, $parentId = "0"){
        $branch = array();

        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->findEcowisePrefillerValue($prefiller, $prefillers['id']);
                if ($children) {

                    $prefillers['prefiller'] = array(['id' =>0, 'root_id' => 0, 'index' => $children[0]['index'], 'value' => $children[0]['value'], 'link_grid_field_id' => $children[0]['link_grid_field_id'],'prefiller' =>  $children[0]['prefiller']]);
                } else {
                    $prefillers['prefiller'] = [];
                }
                $branch[] = array('id' => 0, 'root_id' => 0, 'index' => $prefillers['index'], 'value' => $prefillers['value'], 'link_grid_field_id' => $prefillers['link_grid_field_id'], 'prefiller' => $prefillers['prefiller']);
            }
        }
        return $branch;

    }

    function buildAutoPrefillerTreeArray(array $prefiller, $parentId = 0){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            // $autoPrefillerLinkedGridId =  DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->first();
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildAutoPrefillerTreeArray($prefiller, $prefillers['id']);
                if ($children) {
                    $prefillers['prefiller'] = $children;

                }else{
                    $prefillers['prefiller'] =[];
                }
                //  $branch[]  = $prefillers;
                if($prefillers['root_id']==0){
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],
                    'link_grid_field_id'=>$prefillers['docket_field_grid_id'],'prefiller'=>$prefillers['prefiller']);
                }else{
                    // dd($autoPrefillerLinkedGridId->first());
                    // if($autoPrefillerLinkedGridId != null){
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],
                    'link_grid_field_id'=>@DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])
                ->where('index',$prefillers['index'])->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);
                    // }
                }
            }
        }
        return $branch;
    }

//    function folderList (array $folder, $parentId = 0) {
////        dd($folder);
//        $branch = array();
//        foreach ($folder as $folders) {
//            if ($folders['root_id'] == $parentId) {
//                $children = $this->folderList($folders, $folders['id']);
//                if ($children) {
//                    $folders['folder'] = $children;
//                }else{
//                    $folders['folder'] =[];
//                }
//                $branch[] = $folders;
//            }
//        }
//        return $branch;
//
//    }

    function folderList(array $folderList, $parentId = 0) {
        $branch = array();
        foreach ($folderList as $folderLists) {
            if ($folderLists['root_id'] == $parentId) {
                $children = $this->folderList($folderList, $folderLists['id']);
                if ($children) {
                    $folderLists['folder'] = $children;
                }else{
                    $folderLists['folder'] =[];
                }
                $branch[] = $folderLists;
            }
        }
        return $branch;
    }





    public function searchForId($categoryId, $id, $array) {
        foreach ($array as $key => $val) {
            if ($val['category_id'] == $categoryId && $val['form_field_id'] == $id ) {
                return $val;
            }
        }
        return null;
    }

    public function convertMilisecondtoMinHrs($data){
        $input = $data;

        $uSec = $input % 1000;
        $input = floor($input / 1000);

        $seconds = $input % 60;
        $input = floor($input / 60);

        $minutes = $input % 60;
        $input = floor($input / 60);

        $hour = $input ;

        $hrs = "";
        $min = "";

        if (sprintf('%02d', $hour) == 01 || sprintf('%02d', $hour) == 00 ){
            $hrs = sprintf('%02d', $hour)." hour";

        }else{
            $hrs = sprintf('%02d', $hour)." hours";
        }



        if(sprintf('%02d', $minutes) == 01 || sprintf('%02d', $minutes) == 00 ){
            $min = sprintf('%02d', $minutes)." minute";
        }else {
            $min = sprintf('%02d', $minutes)." minutes";
        }


        return  $hrs." ".$min;
    }

    function findAttachetTimerWithCategoryId($data) {
        foreach($data as $index => $datas) {
            if($datas['category_id'] == 17){
                return $datas;
            }
        }

    }
   public function removeNamespaceFromXML( $xml )
    {
        // Because I know all of the the namespaces that will possibly appear in
        // in the XML string I can just hard code them and check for
        // them to remove them
        $toRemove = ['rap', 'turss', 'crim', 'cred', 'j', 'rap-code', 'evic'];
        // This is part of a regex I will use to remove the namespace declaration from string
        $nameSpaceDefRegEx = '(\S+)=["\']?((?:.(?!["\']?\s+(?:\S+)=|[>"\']))+.)["\']?';

        // Cycle through each namespace and remove it from the XML string
        foreach( $toRemove as $remove ) {
            // First remove the namespace from the opening of the tag
            $xml = str_replace('<' . $remove . ':', '<', $xml);
            // Now remove the namespace from the closing of the tag
            $xml = str_replace('</' . $remove . ':', '</', $xml);
            // This XML uses the name space with CommentText, so remove that too
            $xml = str_replace($remove . ':commentText', 'commentText', $xml);
            // Complete the pattern for RegEx to remove this namespace declaration
            $pattern = "/xmlns:{$remove}{$nameSpaceDefRegEx}/";
            // Remove the actual namespace declaration using the Pattern
            $xml = preg_replace($pattern, '', $xml, 1);
        }

        // Return sanitized and cleaned up XML with no namespaces
        return $xml;
    }



    public function v1SaveSentDefaultDockets(Request $request){
        return $this->v1SaveSentDefaultDocket($request);
    }

    function v1SaveSentDefaultDocket($request){
//        dd($request->all());
        //check if subscription was free count remaining docket left
        $company = Company::where('id', $request->header('companyId'))->first();
        if ($company->trial_period == 3) {
            //get last subscription created date
            $subscriptionLogQuery = SubscriptionLog::where('company_id', $company->id);
            if ($subscriptionLogQuery->count() > 0) {
                $lastUpdatedSubscription = $subscriptionLogQuery->orderBy('id', 'desc')->first();
                $monthDay = Carbon::parse($lastUpdatedSubscription->created_at)->format('d');
                $now = Carbon::now();
                $currentMonthStart = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay);
                $currentMonthEnd = Carbon::parse($now->format('Y') . "-" . $now->format('m') . "-" . $monthDay)->addDay(30);
            } else {
                $currentMonthStart = new Carbon('first day of this month');
                $currentMonthEnd = new Carbon('last day of this month');
            }
            $sentDockets = SentDockets::where('sender_company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();
            $emailDockets = EmailSentDocket::where('company_id', $company->id)->whereBetween('created_at', array($currentMonthStart, $currentMonthEnd))->count();

            $totalMonthDockets = $sentDockets + $emailDockets;

            if ($totalMonthDockets >= 5) {
                return response()->json(array('status' => true, 'message' => 'Please upgrade your subscription. Your active subscription has an ability to send a maximum of 5 dockets per month.'));
            }
        }
        $validator = Validator::make($request->all(), ['data' => 'required']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                $errors[] = $messages[0];
            }
            return response()->json(array('status' => false, 'message' => $errors));
        else:
            $sentDocketData = json_decode($request->data, true);
            $docketFieldsQuery = DocketField::where('docket_id',$sentDocketData['template']['id'])->orderBy('order', 'asc')->get();
            $templateData =  Docket::where('id', $sentDocketData['template']['id'])->first();

            foreach ($docketFieldsQuery as $row) {
                if ($row->required) {
                    $searchData = $this->searchForId($row->docket_field_category_id,$row->id, $sentDocketData['docket_data']['docket_field_values']);

                    if ($searchData != null) {
                        if ($searchData['category_id'] == 5) {
                            if (count($searchData['image_value']) == 0) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }
                        if ($searchData['category_id'] == 9) {
                            if (count($searchData['signature_value']) == 0) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }
                        if ($searchData['category_id'] == 14) {
                            if (count($searchData['image_value']) == 0) {
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }
                        if ($searchData['category_id'] == 7 || $searchData['category_id'] == 24){
                            if (count($searchData['unit_rate_value']) == 0){
                                return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }

                        if ($searchData['category_id'] == 1 || $searchData['category_id'] == 2 || $searchData['category_id'] == 3 ||
                            $searchData['category_id'] == 4 || $searchData['category_id'] == 6  || $searchData['category_id'] == 16  ||
                            $searchData['category_id'] == 20   || $searchData['category_id'] == 25  ||  $searchData['category_id'] == 26) {
                            if ($searchData['value'] == "") {
                               return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field is required.'));
                            }
                        }


                        if ($searchData['category_id'] == 20 ){
                            foreach ($row->docketManualTimerBreak as $rowDatas) {
                                if ($rowDatas->explanation == 1) {
                                    if($searchData['manual_timer_value']['breakDuration'] != ""){
                                        if($searchData['manual_timer_value']['breakDuration'] == "0"){
                                        }elseif ($searchData['manual_timer_value']['explanation'] == "") {
                                            return response()->json(array('status' => false, 'message' => 'The ' . $row->label . ' field  Explanation is required.'));
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
            }

            if($sentDocketData['docket_data']['is_email'] == 'true'){
                $sentDocketData['docket_data']['is_email'] = true;
            }else{
                $sentDocketData['docket_data']['is_email'] = false;
            }

            if ($sentDocketData['docket_data']['is_email'] == true || count($sentDocketData['email_user_receivers'])!==0){
                $data = $this->saveEmailDockets($sentDocketData, $templateData,$request);
                if($data['status'] == false){
                    return response()->json(array('status' => true, 'message' => $data['data']));
                }else{
                    return response()->json(array('status' => true, 'message' => 'Docket successfully sent to '. $data['data']));
                }
            }elseif($sentDocketData['docket_data']['is_email'] == false || count($sentDocketData['rt_user_receivers'])!==0){
                $data = $this->saveDockets($sentDocketData,$templateData,$request);
                return response()->json(array('status' => true, 'message' => 'Docket successfully sent to '.$data["data"]));
            }
        endif;
    }

    public function saveDockets($sentDocketData, $templateData,$request){
        $sendDocketCopy = array();
        $folderStatusSave  = false;
        $company=Company::where('id',$request->header('companyId'))->first();
        $userFullname= User::where('id',$request->header('userId'))->first();
        $sentDocket                     =       new SentDockets();
        $sentDocket->user_id            =       $request->header('userId');
        $sentDocket->abn                =      $company->abn;
        $sentDocket->company_name       =      $company->name;
        $sentDocket->company_address    =      $company->address;
        $sentDocket->company_logo       =      $company->logo;
        $sentDocket->sender_name        =      $userFullname->first_name.' '.$userFullname->last_name;
        $sentDocket->docket_id          =      $sentDocketData['template']['id'];
        $sentDocket->theme_document_id  =       $templateData->theme_document_id;
        $sentDocket->invoiceable        =      $sentDocketData['template']['invoiceable'];
        $sentDocket->company_id         =   0;
        $sentDocket->sender_company_id	=   $request->header('companyId');
        $sentDocket->template_title  =       $templateData->title;
        $sentDocket->status             =   ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
        $sentDocket->docketApprovalType =   $templateData->docketApprovalType;
        $sentDocket->user_docket_count  = 0;
        if($company->number_system == 1){
            if (SentDockets::where('sender_company_id',$request->header('companyId'))->count()== 0){
                $sentDocket->company_docket_id = 1;
            }else{
                $companyDocketId =  SentDockets::where('sender_company_id',$request->header('companyId'))->pluck('company_docket_id')->toArray();
                $sentDocket->company_docket_id = max($companyDocketId) + 1;
            }
        }else{
            $sentDocket->company_docket_id    =   0;
        }

        // if (SentDockets::where('sender_company_id',$request->header('companyId'))->count()== 0){
        //     $sentDocket->company_docket_id = 1;
        // }else{
        //     $companyDocketId =  SentDockets::where('sender_company_id',$request->header('companyId'))->orderBy('created_at','desc')->first();
        //     $sentDocket->company_docket_id = $companyDocketId->company_docket_id + 1;
        // }


        $sentDocket->save();
        if($company->number_system == 1){
            if($templateData->hide_prefix == 1){
                $sentDocket->formatted_id = $sentDocket->sender_company_id.'-'.$sentDocket->company_docket_id ;
            }else{
                $sentDocket->formatted_id = 'rt-'.$sentDocket->sender_company_id.'-doc-'.$sentDocket->company_docket_id ;
            }
            $sentDocket->update();
        }
        else{
            $findUserDocketCount = SentDockets::where('user_id', $request->header('userId'))->where('sender_company_id', $request->header('companyId'))->where('docket_id',$templateData->id)->pluck('user_docket_count')->toArray();
            $findUserEmailDocketCount =EmailSentDocket::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('docket_id',$templateData->id)->pluck('user_docket_count')->toArray();
            if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                $uniquemax = 0;
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                }
                $sentDocket->update();
            }else{
                $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                }
                $sentDocket->update();
            }
        }


        $docketFieldsQuery   =  DocketField::where('docket_id',$templateData->id)->orderBy('order','asc')->get();
        $timerAttached = $this->findAttachetTimerWithCategoryId($sentDocketData['docket_data']['docket_field_values']);

        if($timerAttached){
            foreach($timerAttached['timer_value'] as $timer_id){
                $timer_attachment                   = new \App\SentDcoketTimerAttachment();
                $timer_attachment->sent_docket_id   = $sentDocket->id;
                $timer_attachment->type             = 1;
                $timer_attachment->timer_id         = $timer_id;
                $timer_attachment->save();
                $timer = Timer::where('id', $timer_id)->first();
                $timer->status = 2;
                $timer->update();
            }
        }

        foreach ($docketFieldsQuery as $row){

            $searchData = $this->searchForId($row->docket_field_category_id,$row->id,  $sentDocketData['docket_data']['docket_field_values']);

            if ($searchData != null) {

                if ($searchData['category_id'] == 9) {
                    $docketFieldValue   =   new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "signature";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){
                        foreach ($searchData['signature_value'] as $items){
                            $data = $items['image'];
                            $arrayData = explode("/", $data);
                            $imageValue     =    new SendDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->name = $items['name'];
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }
                else if($searchData['category_id'] == 28){

                    if(count($searchData['folder_value']['folders']) != 0){
                        $folderStatusSave = true;
                        $folderItem = new FolderItem();
                        $folderItem->folder_id = end($searchData['folder_value']['folders'])['id'];
                        $folderItem->ref_id = $sentDocket->id;
                        $folderItem->type = 1;
                        $folderItem->user_id = $request->header('userId');
                        $folderItem->status = 0;
                        $folderItem->company_id = $request->header('companyId');
                        if ($folderItem->save()){
                            SentDockets::where('id',$sentDocket->id)->update(['folder_status'=>1]);
                        }

                    }
                }
                else if($searchData['category_id'] == 29){
                    $emailArray = array();
                    $docketFieldValue   =   new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value  = serialize($emailArray)  ;
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){

                        if(count($searchData['email_list_value']) != 0){
                            if(count($searchData['email_list_value']['email_list']) != 0){
                                foreach($searchData['email_list_value']['email_list'] as $data){
                                    $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                }
                                $docketFieldValue->value = serialize($searchData['email_list_value']['email_list']);
                                $docketFieldValue->update();
                            }
                        }
                    }
                }
                else if($searchData['category_id'] == 5){
                    $docketFieldValue   =   new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "image";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){
                        foreach ($searchData['image_value'] as $itemsss){
                            $arrayData = explode("/", $itemsss);
                            $imageValue     =    new SendDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }
                else if($searchData['category_id'] == 18){
                    $getDataFromYesNoField = YesNoFields::where('docket_field_id',$row->id)->get();
                    $arraygetDataFromYesNoField= array();
                    foreach ($getDataFromYesNoField as $test){
                        $arraygetDataFromYesNoField[] = array(
                            'label' => ($test->label_type==0) ? $test->label : $test->icon_image,
                            'colour' => $test->colour,
                            'label_type' => $test->label_type,
                        );
                    }
                    $arrayvalues=array();
                    $arrayvalues["title"] = $row->label;
                    $arrayvalues["label_value"]=$arraygetDataFromYesNoField;
                    $docketFieldValue = new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = serialize($arrayvalues);
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->value = (array_key_exists("selected_type",$searchData['yes_no_value'])) ? (($searchData['yes_no_value']['selected_type']) ? $searchData['yes_no_value']['selected_type'] : "N/a") : "N/a" ;
                    if($docketFieldValue->save()){


                        if(count($searchData['yes_no_value']['explanation']) !=0){
                            $yesNoDocketField = $searchData['yes_no_value']['selected_id'];
                            if(YesNoFields::where('id',$yesNoDocketField)->where('explanation',1)->count() == 1){
                                $items=YesNoDocketsField::where('yes_no_field_id',$yesNoDocketField)->orderBy('order','asc')->get();
                                foreach ($items as $datas){
                                    $searchDatas = $this->searchForId($datas->docket_field_category_id,$datas->id, $searchData['yes_no_value']['explanation']);
                                    if ($searchDatas != null) {
                                        if ($searchDatas['category_id'] == 5) {
                                            $yesNoDocketFieldValue = new SentDocValYesNoValue();
                                            $yesNoDocketFieldValue->sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $test   =    array();
                                            foreach ($searchDatas['image_value'] as $yesNoDocketexplanations){
                                                $arrayData = explode("/", $yesNoDocketexplanations);
                                                if (count($searchDatas['image_value'])!= 0){
                                                    $test[] =   implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                                                }

                                            }
                                            $serialized_array = serialize($test);
                                            $yesNoDocketFieldValue->value = $serialized_array;
                                            $yesNoDocketFieldValue->save();
                                        }else if($searchDatas['category_id'] == 1){
                                            $yesNoDocketFieldValue = new SentDocValYesNoValue();
                                            $yesNoDocketFieldValue->sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->value =$searchDatas['value'];
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $yesNoDocketFieldValue->save();
                                        }else if($searchDatas['category_id'] == 2){
                                            $yesNoDocketFieldValue = new SentDocValYesNoValue();
                                            $yesNoDocketFieldValue->sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->value =$searchDatas['value'];
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $yesNoDocketFieldValue->save();
                                        }
                                    }
                                }
                            }
                        }




                    }
                }
                else if($searchData['category_id'] == 15){
                    $docketFieldValue   =   new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "document";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()):
                        $documentAttachement = $row->docketAttached;
                        foreach ($documentAttachement as $rows){
                            $sentDocketAttachement = new SentDocketAttachment();
                            $sentDocketAttachement->sent_dockets_value_id    =  $docketFieldValue->id;
                            $sentDocketAttachement->document_name = $rows->name;
                            $sentDocketAttachement->url =$rows->url;
                            $sentDocketAttachement->save();
                        }
                    endif;
                }
                else if($searchData['category_id'] == 14){
                    $docketFieldValue   =   new SentDocketsValue();
                    $docketFieldValue->sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "sketchpad";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()){
                        foreach ($searchData['image_value'] as $itemsss){
                            $arrayData = explode("/", $itemsss);
                            $imageValue     =    new SendDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }
                else if($searchData['category_id'] == 13){
                    $docketFieldValue = new SentDocketsValue();
                    $docketFieldValue->sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = $row->label;
                    $docketFieldValue->value = $searchData['value'];
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->save();
                }
                else if ($searchData['category_id'] == 22 ){
                    $docketFieldValue = new SentDocketsValue();
                    $docketFieldValue->sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = $row->label;
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->value = "Grid";
                    $docketFieldValue->save();
                    foreach ($row->girdFields as $gridField) {
                        $gridFieldLabel = new DocketFieldGridLabel();
                        $gridFieldLabel->docket_id = $sentDocket->id;
                        $gridFieldLabel->is_email_docket = 0;
                        $gridFieldLabel->docket_field_grid_id = $gridField->id;
                        $gridFieldLabel->label = $gridField->label;
                        $gridFieldLabel->sumable =  $gridField->sumable;
                        $gridFieldLabel->docket_field_id =  $row->id;
                        $gridFieldLabel->save();
                    }
                    foreach ($row->girdFields as $gridField) {
                        $forCount = 0;
                        if(isset($sentDocketData['docket_data']['isWeb'])){
                            if($sentDocketData['docket_data']['isWeb'] == 'true'){
                                $forCount = 1;
                            }
                        }
                        $oneLoop = 1;
                        for ($i = 0; $i < count($searchData['grid_value']) + $forCount ; $i++) {
                            if(isset($sentDocketData['docket_data']['isWeb']) && $oneLoop == 1){
                                if($sentDocketData['docket_data']['isWeb'] == 'true'){
                                    $i = $i+1;
                                }
                            }
                            $oneLoop = 0;
                            $gridSearchData = $this->searchForId($gridField->docket_field_category_id,$gridField->id, $searchData['grid_value'][$i]);
                            if ($gridSearchData['category_id'] == 5){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)));
                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }elseif($gridSearchData['category_id'] == 29){
                                $valueArrayData = array();
                                $gridFieldValue   =   new DocketFieldGridValue();
                                $gridFieldValue->docket_id   =   $sentDocket->id;
                                $gridFieldValue->is_email_docket  =   0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value  = serialize($valueArrayData)  ;
                                $gridFieldValue->index  =   $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                if ($gridFieldValue->save()){
                                    if(count($gridSearchData['email_list_value']) != 0){
                                        if(count($gridSearchData['email_list_value']['email_list']) != 0){
                                            foreach($gridSearchData['email_list_value']['email_list'] as $data){
                                                $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                            }
                                            $gridFieldValue->value = serialize($gridSearchData['email_list_value']['email_list']);
                                            $gridFieldValue->update();
                                        }
                                    }
                                }
                            }elseif($gridSearchData['category_id'] == 14){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)));
                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }elseif ($gridSearchData['category_id'] == 9){
                                $file_values = array();
                                if (count($gridSearchData['signature_value'])){
                                    foreach ($gridSearchData['signature_value'] as $items){
                                        $data = $items['image'];
                                        $arrayData = explode("/", $data);
                                        $file_values[] = array("image" => implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)), "name" => $items['name']);

                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }elseif($gridSearchData['category_id'] == 20){
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value =  ($gridSearchData['manual_timer_value'] != null && $gridSearchData['manual_timer_value'] != "" ) ? json_encode($gridSearchData['manual_timer_value']) : "N/a";
                                $gridFieldValue->index = $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();

                            }else{
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 0;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value =  ($gridSearchData["value"] != "") ? $gridSearchData["value"] : "N/a";
                                $gridFieldValue->index = $i;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }
                        }
                    }
                }
                else if($searchData['category_id'] == 20){
                    $docketFieldValue = new SentDocketsValue();
                    $docketFieldValue->sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value = (@$searchData['value'] != "") ? $this->convertMilisecondtoMinHrs($searchData['value']) : "N/a";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()){
                        $docketFieldManualTimer = $docketFieldValue->docketManualTimer;

                        foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                            if($docketManualTimerRow->type == 1){
                                SentDocketManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                    'docket_manual_timer_id' => $docketManualTimerRow->id,
                                    'label' => $docketManualTimerRow->label,
                                    'value' => ($searchData['manual_timer_value']['from'] == "") ? 0 : $searchData['manual_timer_value']['from'],
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);
                            }else if($docketManualTimerRow->type == 2){
                                SentDocketManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                    'docket_manual_timer_id' => $docketManualTimerRow->id,
                                    'label' => $docketManualTimerRow->label,
                                    'value' => ($searchData['manual_timer_value']['to'] == "") ? 0 : $searchData['manual_timer_value']['to'],
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);
                            }
                        }
                        empty($docketFieldManualTimer);
                        $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                        foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                            $breakTimermanual = new SentDocketManualTimerBreak();
                            $breakTimermanual->sent_docket_value_id =$docketFieldValue->id;
                            $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                            $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                            $breakTimermanual->value =  ($searchData['manual_timer_value']['breakDuration'] == "") ? "n/a" : $this->convertMilisecondtoMinHrs($searchData['manual_timer_value']['breakDuration']);
                            $breakTimermanual->reason = ($searchData['manual_timer_value']['explanation'] == "" ) ? "n/a" : $searchData['manual_timer_value']['explanation'] ;
                            $breakTimermanual->save();
                        }
                        empty($docketFieldManualTimerBreak);
                    }

                }
                else{
                    $docketFieldValue = new SentDocketsValue();
                    $docketFieldValue->sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value = (@$searchData['value'] != "") ? @$searchData['value'] : "N/a";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->save();

//                    if ($searchData['category_id'] == 2 && collect($row->docketInvoiceField)->count() != 0) {
//                        $emailSentDocketInvoice = new SentDocketInvoice();
//                        $emailSentDocketInvoice->sent_docket_id = $sentDocket->id;
//                        $emailSentDocketInvoice->sent_docket_value_id = $docketFieldValue->id;
//                        $emailSentDocketInvoice->type = 1;
//                        $emailSentDocketInvoice->save();
//                        empty($emailSentDocketInvoice);
//                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {
                        $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                        if($docketFieldUnitRate){
                            if (collect($row->docketInvoiceField)->count() != 0) {
                                $emailSentDocketInvoice = new SentDocketInvoice();
                                $emailSentDocketInvoice->sent_docket_id = $sentDocket->id;
                                $emailSentDocketInvoice->sent_docket_value_id = $docketFieldValue->id;
                                $emailSentDocketInvoice->type = 2;
                                $emailSentDocketInvoice->save();
                                empty($emailSentDocketInvoice);

                                $emailSentDocketInvoice = new SentDocketInvoice();
                                $emailSentDocketInvoice->sent_docket_id = $sentDocket->id;
                                $emailSentDocketInvoice->sent_docket_value_id = $docketFieldValue->id;
                                $emailSentDocketInvoice->type = 1;
                                $emailSentDocketInvoice->save();
                                empty($emailSentDocketInvoice);

                            }
                        }

                        foreach ($docketFieldUnitRate as $unitRateRow) {
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($unitRateRow->type == 1){
                                    SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => $searchData['unit_rate_value']['per_unit_rate']]);
                                }else if($unitRateRow->type == 2){
                                    SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => $searchData['unit_rate_value']['total_unit']]);
                                }
                            }else{
                                if($unitRateRow->type == 1){
                                    SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => 0]);
                                }else if($unitRateRow->type == 2){
                                    SentDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => 0]);
                                }

                            }
                        }
                        empty($docketFieldUnitRate);
                    }
                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                        $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;
                        foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($docketTallyableUnitRates->type == 1){
                                    SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => $searchData['unit_rate_value']['per_unit_rate']]);
                                }else if($docketTallyableUnitRates->type == 2){
                                    SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => $searchData['unit_rate_value']['total_unit']]);
                                }
                            }else{
                                if($docketTallyableUnitRates->type == 1){
                                    SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => 0]);
                                }else if($docketTallyableUnitRates->type == 2){
                                    SentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => 0]);
                                }
                            }
                        }
                        empty($docketTallyableUnitRate);
                    }

                    empty($docketFieldValue);
                }
            }
        }
        if($folderStatusSave == false){

            if (@$templateData->docketFolderAssign!=null){
                $folderItem = new FolderItem();
                $folderItem->folder_id = $templateData->docketFolderAssign->folder_id;
                $folderItem->ref_id = $sentDocket->id;
                $folderItem->type = 1;
                $folderItem->user_id = $request->header('userId');
                $folderItem->status = 0;
                $folderItem->company_id = $request->header('companyId');
                if ($folderItem->save()){
                    SentDockets::where('id',$sentDocket->id)->update(['folder_status'=>1]);
                }
            }
        }
        $docketProject = DocketProject::where('docket_id', $templateData->id)->get();
        foreach ($docketProject as $docketProjects){
            if ($docketProjects->project->is_close == 0){
                $sentDocketProject = new  SentDocketProject();
                $sentDocketProject->project_id = $docketProjects->project_id;
                $sentDocketProject->sent_docket_id = $sentDocket->id;
                $sentDocketProject->is_email = 0;
                $sentDocketProject->save();
            }
        }
        if ($templateData->xero_timesheet==1) {
            $docketTimesheets = DocketTimesheet::where('docket_id',$templateData->id)->get();
            foreach ($docketTimesheets as $items) {
                $docketTimesheet = new SentDocketTimesheet();
                $docketTimesheet->sent_docket_id = $sentDocket->id;
                $docketTimesheet->docket_field_id = $items->docket_field_id;
                $docketTimesheet->save();
            }
        }

        //multiple recipient users
        $receiverUserId =  $sentDocketData['rt_user_receivers'];
        $docketRecipientId = $sentDocketData['rt_user_approvers'];
        $sn=1;
        foreach($receiverUserId as $receiver){

            $sentDocketRecipient     =    new SentDocketRecipient();
            $sentDocketRecipient     =    new SentDocketRecipient();
            $sentDocketRecipient->sent_docket_id    =    $sentDocket->id;
            $sentDocketRecipient->user_id           =   $receiver['user_id'];
            $docketApproval =   0;
            if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
                foreach ($docketRecipientId as $recipientId){
                    if($recipientId==$receiver['user_id'])
                        $docketApproval =   1;
                }
            }else{
                $docketApproval =   1;
            }

            $sentDocketRecipient->approval  =   $docketApproval;
            $sentDocketRecipient->status   =   0;
            $sentDocketRecipient->save();
            $sentDocketReceiverInfo    =    User::where('id',$receiver['user_id'])->first();
            $receiverNames = '';
            if($sn==1){
                $receiverNames  =  @$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name;
            }elseif($sn==count($receiverUserId)){
                $receiverNames  =  $receiverNames.", ".@$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name.".";
            }else{
                $receiverNames  =  $receiverNames.", ".@$sentDocketReceiverInfo->first_name." ".@$sentDocketReceiverInfo->last_name;
            }
            $userNotification   =    new UserNotification();
            $userNotification->sender_user_id   =    $request->header('userId');
            $userNotification->receiver_user_id =   @$sentDocketReceiverInfo->id;
            $userNotification->type     =   3;
            $userNotification->title    =   'New Docket';
            $userNotification->message  =   "You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name;
            $userNotification->key      =   $sentDocket->id;
            $userNotification->status   =   0;
            $userNotification->save();

            if($sentDocketReceiverInfo->device_type == 2){
                sendiOSNotification($sentDocketReceiverInfo->deviceToken,'New Docket',"You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name,array('type'=>3,'id'=>$sentDocket->id));
            }elseif($sentDocketReceiverInfo->device_type == 1){
                sendAndroidNotification($sentDocketReceiverInfo->deviceToken,'New Docket',"You have received a docket from ".$sentDocket->senderUserInfo->first_name." ".$sentDocket->senderUserInfo->last_name,array('type'=>3,'id'=>$sentDocket->id));
            }

            $sn++;
        }
        //save docket approval users details
        if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
            foreach($docketRecipientId as $recipient){
                $sentDocketRecipientApproval    =    new SentDocketRecipientApproval();
                $sentDocketRecipientApproval->sent_docket_id    =   $sentDocket->id;
                $sentDocketRecipientApproval->user_id           =   $recipient['user_id'];
                $sentDocketRecipientApproval->status            =   0;
                $sentDocketRecipientApproval->name = "null";
                $sentDocketRecipientApproval->signature = "null";
                $sentDocketRecipientApproval->save();
                $sentDocketRecipientApproval->save();
            }
        }
        if($sentDocket->recipientInfo){
            $emailSubjectFields =   DocketField::where('docket_id',$sentDocket->docket_id)->where('is_emailed_subject',1)->orderBy('order','asc')->get();
            $emailSubject   =   "";
            foreach($emailSubjectFields as $subjectField){
                $emailSubjectQuery   =   SentDocketsValue::where('sent_docket_id',$sentDocket->id)->where('docket_field_id', $subjectField->id)->get();
                if($emailSubjectQuery->count()>0){
                    if($emailSubjectQuery->first()->value!="") {
                        $emailSubject = $emailSubject . $emailSubjectQuery->first()->label . ": " . $emailSubjectQuery->first()->value . " ";
                    }
                }
            }

            // foreach($sentDocket->recipientInfo as $sentDocketReceiver){
            //     if($sentDocketReceiver->userInfo->receive_docket_copy == 1){
            //         //new docket email copy
            //         // if($request->header('companyId')==1){
            //         //     if($emailSubject == ""){  $emailSubject = "You’ve got a docket"; };
            //         //     Mail::to($sentDocketReceiver->userInfo->email)->send(new \App\Mail\Docket($sentDocket, $sentDocketReceiver->userInfo(), $emailSubject));
            //         // }
            //         // else {
            //             $sentDocketRecepients = array();
            //             foreach ($sentDocket->recipientInfo as $sentDocketRecepient) {
            //                 if ($sentDocketRecepient->userInfo->employeeInfo) {
            //                     $companyNameRecipent = $sentDocketRecepient->userInfo->employeeInfo->companyInfo->name;
            //                 } else if ($sentDocketRecepient->userInfo->companyInfo) {
            //                     $companyNameRecipent = $sentDocketRecepient->userInfo->companyInfo->name;
            //                 }
            //                 $sentDocketRecepients[] = array(
            //                     'name' => $sentDocketRecepient->userInfo->first_name . " " . $sentDocketRecepient->userInfo->last_name,
            //                     'company_name' => $companyNameRecipent,
            //                 );
            //             }
            //             $datass = (new Collection($sentDocketRecepients))->sortBy('company_name');
            //             $receiverDetail = array();
            //             foreach ($datass as $datas) {
            //                 $receiverDetail[$datas['company_name']][] = $datas['name'];
            //             }
            //             if (SentDocketRecipientApproval::where('sent_docket_id', $sentDocketReceiver->sent_docket_id)->where('user_id', $sentDocketReceiver->user_id)->count() == 1) {
            //                 $sentDocketRecipientApprovals = SentDocketRecipientApproval::where('sent_docket_id', $sentDocketReceiver->sent_docket_id)->where('user_id', $sentDocketReceiver->user_id)->first();
            //             } else {
            //                 $sentDocketRecipientApprovals = null;
            //             }
            //             $data['company'] = $company;
            //             $data['sentDocket'] = $sentDocket;
            //             $data['receiverDetail'] = $receiverDetail;
            //             $data['sentDocketRecipientApprovals'] = $sentDocketRecipientApprovals;
            //             $document_name = "docket-" . $sentDocket->id . "-" . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower(Company::find($sentDocket->first()->sender_company_id)->name)));
            //             //$path = \Config::get('app.storage_url_pdf');
            //             $document_path   =   'files/pdf/docketForward/'.str_replace('.', '',$document_name).'.pdf';
            //             // return view('emails.docket.docket',compact('company', 'sentDocket', 'receiverDetail', 'sentDocketRecipientApprovals'));
            //             $pdf = PDF::loadView('emails.docket.docket', compact('company', 'sentDocket', 'receiverDetail', 'sentDocketRecipientApprovals'));
            //             $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
            //             $output = $pdf->output();
            //             $path = storage_path($document_path);
            //             file_put_contents($path, $output);
            //             $fileName = str_replace('.', '', $document_name) . '.pdf';
            //             $filePath = \Config::get('app.storage_url_pdf') . $document_path;
            //             Mail::send('emails.docket.docket', $data, function ($message) use ($sentDocket, $sentDocketReceiver, $fileName, $filePath, $emailSubject) {
            //                 $message->from("info@recordtimeapp.com.au", $sentDocket->senderCompanyInfo->name);
            //                 $message->replyTo($sentDocket->senderUserInfo->email, @$sentDocket->senderUserInfo->first_name . " " . @$sentDocket->senderUserInfo->last_name);
            //                 $message->to($sentDocketReceiver->userInfo->email)->subject(($emailSubject == " ") ? $sentDocket->template_title : $emailSubject);
            //                 $message->attach($filePath, [
            //                     'as' => $fileName,
            //                     'mime' => 'application/pdf',
            //                 ]);
            //             });
            //         // }
            //     }
            // }
        }


        $tempUserIdsEmail = collect();
        $tempUserIdsEmail->push($company->user_id);

        $employeeUserIds = Employee::where('company_id',$company->id)->pluck('user_id');

        $userIdsEmail = $employeeUserIds->merge($tempUserIdsEmail);

        $receiverUserIdArray = array();
        array_push($receiverUserIdArray,$company->user_id);
        $receiverUserId = $sentDocketData['rt_user_receivers'];
        foreach($receiverUserId as $receiver){
            $receiverUserIdArray[] = $receiver['user_id'];
        }


        $usersData = User::whereIn('id',$receiverUserIdArray)->where('receive_docket_copy',1)->pluck('email');
        foreach ($usersData as $key => $email) {
            $sendDocketCopy[] = array('email'=>$email,'sendCopy' => true);
        }

        if(count($sendDocketCopy)!=0){
            $input = array_map("unserialize", array_unique(array_map("serialize", $sendDocketCopy)));
            foreach($input as $sendDocketCopy){
                $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');

                if($validator->validate($sendDocketCopy['email'])) {
                    if($sendDocketCopy['sendCopy'] == true){
                        if($emailSubject == ""){  $emailSubject = "You’ve got a docket copy";};
                           $jobData = array(
                               'sendDocketCopy'=> $sendDocketCopy,
                               'sentDocket'=> $sentDocket,
                               'emailSubject'=>$emailSubject
                           );
                           SendCopyDocketJob::dispatch($jobData);
                    }
                }

            }
        }

        $slackNotification = array('sender_name' =>$sentDocket->sender_name, 'company_name' => $sentDocket->company_name, 'template_title' => $sentDocket->template_title);
        $userFullname->slackChannel('rt-docket-sent')->notify(new SentDocketNotification($slackNotification));
        return  $response = array('status'=>true,'data'=>$receiverNames);


    }

    public function saveEmailDockets($sentDocketData,$templateData,$request){
        $sendDocketCopy = array();
        $folderStatusSave  = false;
        $response = array();
        if($sentDocketData['docket_data']['email_subject'] == ""){
            $emailSubject = "";
        }else{
            $emailSubject = $sentDocketData['docket_data']['email_subject'];
        }
        $receiverUserId = $sentDocketData['email_user_receivers'];
        // foreach ($receiverUserId as $receiver) {
        //     $emailUser = EmailUser::find($receiver['email_user_id']);
        //     $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
        //     if ($validator->validate($emailUser->email)) {
        //     } else {
        //         return  $response = array('status'=>false,'data'=>$emailUser->email . ' is not valid email.');

        //     }
        // }
        $emailcompany=Company::where('id',$request->header('companyId'))->first();
        $emailuserFullname= User::where('id',$request->header('userId'))->first();
        $sentDocket                     =   new EmailSentDocket();
        $sentDocket->user_id            =   $request->header('userId');
        $sentDocket->abn                =      $emailcompany->abn;
        $sentDocket->company_name       =      $emailcompany->name;
        $sentDocket->company_address    =      $emailcompany->address;
        $sentDocket->company_logo   =      $emailcompany->logo;
        $sentDocket->sender_name        =      $emailuserFullname->first_name.' '.$emailuserFullname->last_name;
        $sentDocket->docket_id          =   $sentDocketData['template']['id'];
        $sentDocket->invoiceable        =   $sentDocketData['template']['invoiceable'];
        $sentDocket->theme_document_id  =   $templateData->theme_document_id;
        $sentDocket->company_id	        =   $request->header('companyId');
        $sentDocket->docketApprovalType    =   $templateData->docketApprovalType;
        $sentDocket->user_docket_count = 0;
        // $sentDocket->template_title  =       $templateData->title;

        if($emailcompany->number_system == 1){
            if (EmailSentDocket::where('company_id',$request->header('companyId'))->count()== 0){
                $sentDocket->company_docket_id = 1;
            }else{
                $companyDocketId =  EmailSentDocket::where('company_id',$request->header('companyId'))->pluck('company_docket_id')->toArray();
                $sentDocket->company_docket_id = max($companyDocketId) + 1;
            }
        }else{
            $sentDocket->company_docket_id = 0;
        }

        $sentDocket->status             =   ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
        $sentDocket->hashKey            =  ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? $this->generateRandomString() : "" ;
        $sentDocket->save();

        if($emailcompany->number_system == 1){

            if($templateData->hide_prefix == 1){
                $sentDocket->formatted_id = $sentDocket->company_id.'-'.$sentDocket->company_docket_id ;
            }else{
                $sentDocket->formatted_id = 'rt-'.$sentDocket->company_id.'-edoc-'.$sentDocket->company_docket_id ;
            }

            $sentDocket->update();
        }
        else{
            $findUserDocketCount = SentDockets::where('user_id', $request->header('userId'))->where('sender_company_id', $request->header('companyId'))->where('docket_id',$templateData->id)->pluck('user_docket_count')->toArray();
            $findUserEmailDocketCount =EmailSentDocket::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('docket_id',$templateData->id)->pluck('user_docket_count')->toArray();
            if(max(array_merge($findUserDocketCount,$findUserEmailDocketCount)) == 0){
                $uniquemax = 0;
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }
                }
                $sentDocket->update();
            }else{
                $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                $sentDocket->user_docket_count = $uniquemax+1;
                $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                if($employeeData->count() == 0){
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-1-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-1-".($uniquemax+1);
                    }
                }else{
                    if($templateData->hide_prefix == 1){
                        $sentDocket->formatted_id = $templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                    }else{
                        $sentDocket->formatted_id = "RT-".$templateData->prefix."-".$templateData->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                    }
                }
                $sentDocket->update();
            }
        }
        $receiverUserId = $sentDocketData['email_user_receivers'];
        $docketRecipientId = $sentDocketData['email_user_approvers'];
        foreach($receiverUserId as $receiver){
            // if($receiver['saved'] == true){
            //     $sn=1;
            //     $sentDocketRecipient                          =    new EmailSentDocketRecipient();
            //     $sentDocketRecipient->email_sent_docket_id    =    $sentDocket->id;
            //     $sentDocketRecipient->email_user_id           =    $receiver['email_user_id'];

            //     $docketApproval =   0;
            //     if ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1) {
            //         foreach ($docketRecipientId as $recipientId){
            //             if($recipientId['email_user_id']==$receiver['email_user_id'])
            //                 $docketApproval =   1;
            //         }
            //     }else{
            //         $docketApproval =   1;
            //     }


            //     $sentDocketRecipient->approval  =   $docketApproval;
            //     $sentDocketRecipient->status   =    ($templateData->docketApprovalType == 0 || $templateData->docketApprovalType == 1 ) ? 0 : 1;
            //     $sentDocketRecipient->hashKey            =   $this->generateRandomString();


            //     if($receiver['saved'] == true){
            //         $sentDocketRecipient->receiver_full_name        =   ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
            //         $sentDocketRecipient->receiver_company_name	    =   ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
            //         $sentDocketRecipient->receiver_company_address  =   ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
            //         $sentDocketRecipient->save();
            //     }else{
            //         $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver['email_user_id'])->first();

            //         $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
            //         $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
            //         $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
            //         $sentDocketRecipient->save();
            //     }

            // }else{

                if(count($receiverUserId)==1){
                    $sentDocketRecipient                        =    new EmailSentDocketRecipient();
                    $sentDocketRecipient->email_sent_docket_id  =    $sentDocket->id;
                    $sentDocketRecipient->email_user_id         =    $receiverUserId[0]['email_user_id'];
                    $sentDocketRecipient->approval              =    1;
                    $sentDocketRecipient->status                =    0;
                    $sentDocketRecipient->hashKey               =    $this->generateRandomString();


                    if($receiver['full_name'] !=""){
                        $sentDocketRecipient->receiver_full_name    =   ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
                        $sentDocketRecipient->receiver_company_name	 =   ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
                        $sentDocketRecipient->receiver_company_address  =   ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
                        $sentDocketRecipient->save();
                    }else{
                        $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiverUserId[0]["email_user_id"])->first();
                        $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                        $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                        $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                        $sentDocketRecipient->save();
                    }

                }else{

                    $sn=1;
                    foreach($receiverUserId as $receiver){
                        $sentDocketRecipient                    =    new EmailSentDocketRecipient();
                        $sentDocketRecipient->email_sent_docket_id    =    $sentDocket->id;
                        $sentDocketRecipient->email_user_id           =   $receiver['email_user_id'];

                        $docketApproval =   0;


                        foreach ($docketRecipientId as $recipientId){
                            if($recipientId['email_user_id']==$receiver['email_user_id'])
                                $docketApproval =   1;
                        }
                        $sentDocketRecipient->approval  =   $docketApproval;
                        $sentDocketRecipient->status   =   0;
                        $sentDocketRecipient->hashKey            =   $this->generateRandomString();


                        if(count($receiverUserId)==1){
                            if($receiver['full_name'] !=""){
                                $sentDocketRecipient->receiver_full_name    = ($receiver['full_name'] != "") ? $receiver['full_name'] : "";
                                $sentDocketRecipient->receiver_company_name	 =  ($receiver['company_name'] != "") ? $receiver['company_name'] : "";
                                $sentDocketRecipient->receiver_company_address  =  ($receiver['company_address'] != "") ? $receiver['company_address'] : "";
                                $sentDocketRecipient->save();
                            }else{
                                $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver['email_user_id'])->first();
                                $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                                $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                                $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                                $sentDocketRecipient->save();
                            }
                        }else {

                            $emailClient = Email_Client::where('company_id', $request->header('companyId'))->where('email_user_id', $receiver['email_user_id'])->first();
                            $sentDocketRecipient->receiver_full_name = @$emailClient->full_name;
                            $sentDocketRecipient->receiver_company_name = @$emailClient->company_name;
                            $sentDocketRecipient->receiver_company_address = @$emailClient->company_address;
                            $sentDocketRecipient->save();
                        }
                    }
                }

            // }
        }

        $timerAttached = $this->findAttachetTimerWithCategoryId($sentDocketData['docket_data']['docket_field_values']);
        if($timerAttached){
            foreach($timerAttached['timer_value'] as $timer_id){
                $timer_attachment                   = new \App\SentDcoketTimerAttachment();
                $timer_attachment->sent_docket_id   = $sentDocket->id;
                $timer_attachment->type             = 2;
                $timer_attachment->timer_id         = $timer_id;
                $timer_attachment->save();
                $timer = Timer::where('id', $timer_id)->first();
                $timer->status = 2;
                $timer->update();
            }
        }


        $docketFieldsQuery   =  DocketField::where('docket_id',$templateData->id)->orderBy('order','asc')->get();
        foreach ($docketFieldsQuery as $row){
            $searchData = $this->searchForId($row->docket_field_category_id,$row->id, $sentDocketData['docket_data']['docket_field_values']);
            if ($searchData != null) {
                if ($searchData['category_id'] == 9) {
                    $docketFieldValue   =   new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "signature";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){
                        foreach ($searchData['signature_value'] as $items){
                            $data = $items['image'];
                            $arrayData = explode("/", $data);
                            $imageValue     =    new EmailSentDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->name = $items['name'];
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }else if($searchData['category_id'] == 28){
                    if(count($searchData['folder_value']['folders']) != 0){
                        $folderStatusSave = true;
                        $folderItem = new FolderItem();
                        $folderItem->folder_id =  end($searchData['folder_value']['folders'])['id'];
                        $folderItem->ref_id = $sentDocket->id;
                        $folderItem->type = 3;
                        $folderItem->user_id = $request->header('userId');
                        $folderItem->status = 0;
                        $folderItem->company_id = $request->header('companyId');
                        if ($folderItem->save()) {
                            EmailSentDocket::where('id', $sentDocket->id)->update(['folder_status' => 1]);
                        }

                    }
                }else if($searchData['category_id'] == 29){
                    $emailArray = array();
                    $docketFieldValue   =   new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value  = serialize($emailArray)  ;
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){
                        if(count($searchData['email_list_value']) != 0){
                            if(count($searchData['email_list_value']['email_list']) != 0){
                                foreach($searchData['email_list_value']['email_list'] as $data){
                                    $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                }
                                $docketFieldValue->value = serialize($searchData['email_list_value']['email_list']);
                                $docketFieldValue->update();
                            }
                        }
                    }
                }else if($searchData['category_id'] == 5){
                    $docketFieldValue   =   new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "image";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if ($docketFieldValue->save()){
                        foreach ($searchData['image_value'] as $itemsss){
                            $arrayData = explode("/", $itemsss);
                            $imageValue     =    new EmailSentDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }else if($searchData['category_id'] == 18){
                    $getDataFromYesNoField = YesNoFields::where('docket_field_id',$row->id)->get();
                    $arraygetDataFromYesNoField= array();
                    foreach ($getDataFromYesNoField as $test){
                        $arraygetDataFromYesNoField[] = array(
                            'label' => ($test->label_type==0) ? $test->label : $test->icon_image,
                            'colour' => $test->colour,
                            'label_type' => $test->label_type,
                        );
                    }
                    $arrayvalues=array();
                    $arrayvalues["title"] = $row->label;
                    $arrayvalues["label_value"]=$arraygetDataFromYesNoField;
                    $docketFieldValue = new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = serialize($arrayvalues);
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->value =  (array_key_exists("selected_type",$searchData['yes_no_value'])) ? (($searchData['yes_no_value']['selected_type']) ? $searchData['yes_no_value']['selected_type'] : "N/a") : "N/a";
                    if($docketFieldValue->save()){

                        if(count($searchData['yes_no_value']['explanation']) !=0){
                            $yesNoDocketField = $searchData['yes_no_value']['selected_id'];
                            if(YesNoFields::where('id',$yesNoDocketField)->where('explanation',1)->count() == 1){
                                $items=YesNoDocketsField::where('yes_no_field_id',$yesNoDocketField)->orderBy('order','asc')->get();
                                foreach ($items as $datas){
                                    $searchDatas = $this->searchForId($datas->docket_field_category_id,$datas->id, $searchData['yes_no_value']['explanation']);
                                    if ($searchDatas != null) {
                                        if ($searchDatas['category_id'] == 5) {
                                            $yesNoDocketFieldValue = new SentEmailDocValYesNoValue();
                                            $yesNoDocketFieldValue->email_sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $test   =    array();
                                            foreach ($searchDatas['image_value'] as $yesNoDocketexplanations){
                                                $arrayData = explode("/", $yesNoDocketexplanations);
                                                if (count($searchDatas['image_value'])!= 0){
                                                    $test[] =   implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                                                }
                                            }

                                            $serialized_array = serialize($test);

                                            $yesNoDocketFieldValue->value = $serialized_array;
                                            $yesNoDocketFieldValue->save();
                                        }else if($searchDatas['category_id'] == 1){
                                            $yesNoDocketFieldValue = new SentEmailDocValYesNoValue();
                                            $yesNoDocketFieldValue->email_sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->value =$searchDatas['value'];
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $yesNoDocketFieldValue->save();
                                        }else if($searchDatas['category_id'] == 2){
                                            $yesNoDocketFieldValue = new SentEmailDocValYesNoValue();
                                            $yesNoDocketFieldValue->email_sent_docket_value_id = $docketFieldValue->id;
                                            $yesNoDocketFieldValue->yes_no_docket_field_id = $datas->id;
                                            $yesNoDocketFieldValue->value =$searchDatas['value'];
                                            $yesNoDocketFieldValue->label =$datas->label;
                                            $yesNoDocketFieldValue->required = $datas->required;
                                            $yesNoDocketFieldValue->save();
                                        }
                                    }

                                }
                            }
                        }
                    }

                }else if($searchData['category_id'] == 15){
                    $docketFieldValue   =   new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "document";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()):
                        $documentAttachement = $row->docketAttached;
                        foreach ($documentAttachement as $rows){
                            $sentDocketAttachement = new SentEmailAttachment();
                            $sentDocketAttachement->sent_email_value_id    =  $docketFieldValue->id;
                            $sentDocketAttachement->document_name = $rows->name;
                            $sentDocketAttachement->url =$rows->url;
                            $sentDocketAttachement->save();
                        }
                    endif;
                }else if($searchData['category_id'] == 14){
                    $docketFieldValue   =   new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id   =   $sentDocket->id;
                    $docketFieldValue->docket_field_id  =   $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value            =   "sketchpad";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()){
                        foreach ($searchData['image_value'] as $itemsss){
                            $arrayData = explode("/", $itemsss);
                            $imageValue     =    new EmailSentDocketImageValue();
                            $imageValue->sent_docket_value_id    =  $docketFieldValue->id;
                            $imageValue->value = implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6));
                            $imageValue->save();
                        }
                    }
                }else if($searchData['category_id'] == 13){
                    $docketFieldValue = new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = $row->label;
                    $docketFieldValue->value = $searchData['value'];
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->save();
                }else if ($searchData['category_id'] == 22 ){
                    $docketFieldValue = new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label = $row->label;
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->value = "Grid";
                    $docketFieldValue->save();
                    foreach ($row->girdFields as $gridField) {
                        $gridFieldLabel = new DocketFieldGridLabel();
                        $gridFieldLabel->docket_id = $sentDocket->id;
                        $gridFieldLabel->is_email_docket = 1;
                        $gridFieldLabel->docket_field_grid_id = $gridField->id;
                        $gridFieldLabel->label = $gridField->label;
                        $gridFieldLabel->sumable =  $gridField->sumable;
                        $gridFieldLabel->docket_field_id =  $row->id;
                        $gridFieldLabel->save();
                    }
                    foreach ($row->girdFields as $gridField) {
                        foreach($searchData['grid_value'] as $key => $searchDataGridValue){
                             $gridSearchData = $this->searchForId($gridField->docket_field_category_id,$gridField->id, $searchDataGridValue);
                            if ($gridSearchData['category_id'] == 5){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)));
                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }else if($gridSearchData['category_id'] == 29){
                                $valueArrayData = array();
                                $gridFieldValue   =   new DocketFieldGridValue();
                                $gridFieldValue->docket_id   =   $sentDocket->id;
                                $gridFieldValue->is_email_docket  =   1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value  = serialize($valueArrayData)  ;
                                $gridFieldValue->index  =   $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                if ($gridFieldValue->save()){
                                    if(count($gridSearchData['email_list_value']) != 0){
                                        if(count($gridSearchData['email_list_value']['email_list']) != 0){
                                            foreach($gridSearchData['email_list_value']['email_list'] as $data){
                                                $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                                            }
                                            $gridFieldValue->value = serialize($gridSearchData['email_list_value']['email_list']);
                                            $gridFieldValue->update();
                                        }
                                    }
                                }
                            }elseif($gridSearchData['category_id'] == 14){
                                $file_values = array();
                                if (count($gridSearchData['image_value'])){
                                    foreach ($gridSearchData['image_value'] as $itemsss){
                                        $arrayData = explode("/", $itemsss);
                                        array_push($file_values, implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)));
                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }elseif ($gridSearchData['category_id'] == 9){
                                $file_values = array();
                                if (count($gridSearchData['signature_value'])){
                                    foreach ($gridSearchData['signature_value'] as $items){
                                        $data = $items['image'];
                                        $arrayData = explode("/", $data);
                                        $file_values[] = array("image" => implode("/",array_splice($arrayData, FunctionUtils::implodeImage(), 6)), "name" => $items['name']);

                                    }
                                }
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                                $gridFieldValue->index = $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }elseif($gridSearchData['category_id'] == 20){
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value =  ($gridSearchData['manual_timer_value'] != null && $gridSearchData['manual_timer_value'] != "" ) ? json_encode($gridSearchData['manual_timer_value']) : "N/a";
                                $gridFieldValue->index = $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();

                            }else{
                                $gridFieldValue = new DocketFieldGridValue();
                                $gridFieldValue->docket_id = $sentDocket->id;
                                $gridFieldValue->is_email_docket = 1;
                                $gridFieldValue->docket_field_grid_id = $gridField->id;
                                $gridFieldValue->value =  ($gridSearchData["value"] != "") ? $gridSearchData["value"] : "N/a";
                                $gridFieldValue->index = $key;
                                $gridFieldValue->docket_field_id =  $row->id;
                                $gridFieldValue->save();
                            }
                        }
                        // for ($i = 0; $i < count($searchData['grid_value']); $i++) {
                        //     $gridSearchData = $this->searchForId($gridField->docket_field_category_id,$gridField->id, $searchData['grid_value'][$i]);
                        //     if ($gridSearchData['category_id'] == 5){
                        //         $file_values = array();
                        //         if (count($gridSearchData['image_value'])){
                        //             foreach ($gridSearchData['image_value'] as $itemsss){
                        //                 $arrayData = explode("/", $itemsss);
                        //                 array_push($file_values, implode("/",array_splice($arrayData, 4, 6)));
                        //             }
                        //         }
                        //         $gridFieldValue = new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id = $sentDocket->id;
                        //         $gridFieldValue->is_email_docket = 1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                        //         $gridFieldValue->index = $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         $gridFieldValue->save();
                        //     }else if($gridSearchData['category_id'] == 29){
                        //         $valueArrayData = array();
                        //         $gridFieldValue   =   new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id   =   $sentDocket->id;
                        //         $gridFieldValue->is_email_docket  =   1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value  = serialize($valueArrayData)  ;
                        //         $gridFieldValue->index  =   $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         if ($gridFieldValue->save()){
                        //             if(count($gridSearchData['email_list_value']) != 0){
                        //                 if(count($gridSearchData['email_list_value']['email_list']) != 0){
                        //                     foreach($gridSearchData['email_list_value']['email_list'] as $data){
                        //                         $sendDocketCopy[] = array('email'=>$data['email'],'sendCopy' =>$data['send_copy']);
                        //                     }
                        //                     $gridFieldValue->value = serialize($gridSearchData['email_list_value']['email_list']);
                        //                     $gridFieldValue->update();
                        //                 }
                        //             }
                        //         }
                        //     }elseif($gridSearchData['category_id'] == 14){
                        //         $file_values = array();
                        //         if (count($gridSearchData['image_value'])){
                        //             foreach ($gridSearchData['image_value'] as $itemsss){
                        //                 $arrayData = explode("/", $itemsss);
                        //                 array_push($file_values, implode("/",array_splice($arrayData, 4, 6)));
                        //             }
                        //         }
                        //         $gridFieldValue = new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id = $sentDocket->id;
                        //         $gridFieldValue->is_email_docket = 1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                        //         $gridFieldValue->index = $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         $gridFieldValue->save();
                        //     }elseif ($gridSearchData['category_id'] == 9){
                        //         $file_values = array();
                        //         if (count($gridSearchData['signature_value'])){
                        //             foreach ($gridSearchData['signature_value'] as $items){
                        //                 $data = $items['image'];
                        //                 $arrayData = explode("/", $data);
                        //                 $file_values[] = array("image" => implode("/",array_splice($arrayData, 4, 6)), "name" => $items['name']);

                        //             }
                        //         }
                        //         $gridFieldValue = new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id = $sentDocket->id;
                        //         $gridFieldValue->is_email_docket = 1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value = ($file_values != []) ? serialize($file_values) : "N/a";
                        //         $gridFieldValue->index = $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         $gridFieldValue->save();
                        //     }elseif($gridSearchData['category_id'] == 20){
                        //         $gridFieldValue = new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id = $sentDocket->id;
                        //         $gridFieldValue->is_email_docket = 1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value =  ($gridSearchData['manual_timer_value'] != null && $gridSearchData['manual_timer_value'] != "" ) ? json_encode($gridSearchData['manual_timer_value']) : "N/a";
                        //         $gridFieldValue->index = $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         $gridFieldValue->save();

                        //     }else{
                        //         $gridFieldValue = new DocketFieldGridValue();
                        //         $gridFieldValue->docket_id = $sentDocket->id;
                        //         $gridFieldValue->is_email_docket = 1;
                        //         $gridFieldValue->docket_field_grid_id = $gridField->id;
                        //         $gridFieldValue->value =  ($gridSearchData["value"] != "") ? $gridSearchData["value"] : "N/a";
                        //         $gridFieldValue->index = $i;
                        //         $gridFieldValue->docket_field_id =  $row->id;
                        //         $gridFieldValue->save();
                        //     }

                        // }
                    }
                }elseif($searchData['category_id'] == 20){
                    $docketFieldValue = new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value = (@$searchData['value'] != "") ? $this->convertMilisecondtoMinHrs($searchData['value']) : "N/a";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    if($docketFieldValue->save()){

                        $docketFieldManualTimer = $docketFieldValue->docketManualTimer;
                        foreach ($docketFieldManualTimer as $docketManualTimerRow) {
                            if($docketManualTimerRow->type == 1){
                                EmailSentDocManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                    'docket_manual_timer_id' => $docketManualTimerRow->id,
                                    'label' => $docketManualTimerRow->label,
                                    'value' => ($searchData['manual_timer_value']['from'] == "") ? 0 : $searchData['manual_timer_value']['from'],
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);
                            }else if($docketManualTimerRow->type == 2){
                                EmailSentDocManualTimer::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                    'docket_manual_timer_id' => $docketManualTimerRow->id,
                                    'label' => $docketManualTimerRow->label,
                                    'value' => ($searchData['manual_timer_value']['to'] == "") ? 0 : $searchData['manual_timer_value']['to'],
                                    'created_at'=>Carbon::now(),
                                    'updated_at'=>Carbon::now()
                                ]);
                            }
                        }
                        empty($docketFieldManualTimer);
                        $docketFieldManualTimerBreak = $docketFieldValue->docketManualTimerBreak;
                        foreach ($docketFieldManualTimerBreak as $docketFieldManualTimerBreakrow) {
                            $breakTimermanual = new EmailSentDocManualTimerBrk();
                            $breakTimermanual->sent_docket_value_id =$docketFieldValue->id;
                            $breakTimermanual->manual_timer_break_id = $docketFieldManualTimerBreakrow->id;
                            $breakTimermanual->label = $docketFieldManualTimerBreakrow->label;
                            $breakTimermanual->value =  ($searchData['manual_timer_value']['breakDuration'] == "") ? "n/a" : $this->convertMilisecondtoMinHrs($searchData['manual_timer_value']['breakDuration']);
                            $breakTimermanual->reason = ($searchData['manual_timer_value']['explanation'] == "") ? "n/a" : $searchData['manual_timer_value']['explanation'] ;
                            $breakTimermanual->save();
                        }
                        empty($docketFieldManualTimerBreak);

                    }
                }else{
                    $docketFieldValue = new EmailSentDocketValue();
                    $docketFieldValue->email_sent_docket_id = $sentDocket->id;
                    $docketFieldValue->docket_field_id = $row->id;
                    $docketFieldValue->label  =   $row->label;
                    $docketFieldValue->value = (@$searchData['value'] != "") ? @$searchData['value'] : "N/a";
                    $docketFieldValue->is_hidden = $row->is_hidden;
                    $docketFieldValue->save();
                    if ($searchData['category_id'] == 2 && collect($row->docketInvoiceField)->count() != 0) {
                        $emailSentDocketInvoice = new SentEmailDocketInvoice();
                        $emailSentDocketInvoice->email_sent_docket_id = $sentDocket->id;
                        $emailSentDocketInvoice->email_sent_docket_value_id = $docketFieldValue->id;
                        $emailSentDocketInvoice->type = 1;
                        $emailSentDocketInvoice->save();
                        empty($emailSentDocketInvoice);
                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 7) {
                        //get docket field unit rate id's
                        $docketFieldUnitRate = $docketFieldValue->docketUnitRate;
                        foreach ($docketFieldUnitRate as $unitRateRow) {
                            if (collect($row->docketInvoiceField)->count() != 0) {
                                $emailSentDocketInvoice = new SentEmailDocketInvoice();
                                $emailSentDocketInvoice->email_sent_docket_id = $sentDocket->id;
                                $emailSentDocketInvoice->email_sent_docket_value_id = $docketFieldValue->id;
                                $emailSentDocketInvoice->type = 2;
                                $emailSentDocketInvoice->save();
                                empty($emailSentDocketInvoice);
                            }

                            if (count($searchData['unit_rate_value']) != 0) {
                                if($unitRateRow->type == 1){
                                    EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => $searchData['unit_rate_value']['per_unit_rate']]);
                                }else if($unitRateRow->type == 2){
                                    EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => $searchData['unit_rate_value']['total_unit']]);
                                }
                            }else{
                                if($unitRateRow->type == 1){
                                    EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => 0]);
                                }else if($unitRateRow->type == 2){
                                    EmailSnetDocketUnitRateValue::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_unit_rate_id' => $unitRateRow->id,
                                        'label' => $unitRateRow->label,
                                        'value' => 0]);
                                }
                            }
                        }
                        empty($docketFieldUnitRate);
                    }

                    if ($docketFieldValue->docketFieldInfo->docket_field_category_id == 24){
                        $docketTallyableUnitRate =  $docketFieldValue->tallyableUnitRate;
                        foreach ($docketTallyableUnitRate as $docketTallyableUnitRates){
                            if (count($searchData['unit_rate_value']) != 0) {
                                if($docketTallyableUnitRates->type == 1){
                                    EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => $searchData['unit_rate_value']['per_unit_rate']]);
                                }else if($docketTallyableUnitRates->type == 2){
                                    EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => $searchData['unit_rate_value']['total_unit']]);
                                }
                            }else{
                                if($docketTallyableUnitRates->type == 1){
                                    EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => 0]);
                                }else if($docketTallyableUnitRates->type == 2){
                                    EmailSentDocketTallyUnitRateVal::insert(['sent_docket_value_id' => $docketFieldValue->id,
                                        'docket_tally_unit_rate_id' => $docketTallyableUnitRates->id,
                                        'label' => $docketTallyableUnitRates->label,
                                        'value' => 0]);
                                }
                            }

                        }
                        empty($docketTallyableUnitRate);
                    }

                    empty($docketFieldValue);

                }
            }

        }

        $docketProject = DocketProject::where('docket_id', $templateData->id)->get();
        foreach ($docketProject as $docketProjects){
            if ($docketProjects->project->is_close == 0){
                $sentDocketProject = new  SentDocketProject();
                $sentDocketProject->project_id = $docketProjects->project_id;
                $sentDocketProject->sent_docket_id = $sentDocket->id;
                $sentDocketProject->is_email = 1;
                $sentDocketProject->save();
            }
        }

        if($folderStatusSave == false) {
            if (@$templateData->docketFolderAssign!=null){
                $folderItem = new FolderItem();
                $folderItem->folder_id = $templateData->docketFolderAssign->folder_id;
                $folderItem->ref_id = $sentDocket->id;
                $folderItem->type = 3;
                $folderItem->user_id = $request->header('userId');
                $folderItem->status = 0;
                $folderItem->company_id = $request->header('companyId');
                if ($folderItem->save()){
                    EmailSentDocket::where('id',$sentDocket->id)->update(['folder_status'=>1]);
                }
            }
        }


        $receiverQuery = EmailSentDocketRecipient::where('email_sent_docket_id', $sentDocket->id)->get();
        $recipientNames =  "";

        foreach ($receiverQuery as $receiverInfo) {
            $recipientNames = $recipientNames." ".$receiverInfo->emailUserInfo->email;
            if($receiverQuery->count()>1)
                if($receiverQuery->last()->id!=$receiverInfo->id)
                    $recipientNames =   $recipientNames.",";
            if($emailSubject == ""){  $emailSubject = "You’ve got a docket"; };
                $jobData = array(
                    'receiverInfo'=> $receiverInfo,
                    'sentDocket'=> $sentDocket,
                    'emailSubject'=>$emailSubject
                );
                SentEmailDocketJob::dispatch($jobData);

//                Mail::to($receiverInfo->emailUserInfo->email)->send(new EmailDocket($sentDocket,$receiverInfo,$emailSubject));
        }

        $tempUserIdsEmail = collect();
        $tempUserIdsEmail->push($emailcompany->user_id);

        $employeeUserIds = Employee::where('company_id',$emailcompany->id)->pluck('user_id');

        $userIdsEmail = $employeeUserIds->merge($tempUserIdsEmail);
        $usersData = User::whereIn('id',$userIdsEmail)->where('receive_docket_copy',1)->pluck('email');
        foreach ($usersData as $key => $email) {
            $sendDocketCopy[] = array('email'=>$email,'sendCopy' => true);
        }

        if(count($sendDocketCopy)!=0){
            $input = array_map("unserialize", array_unique(array_map("serialize", $sendDocketCopy)));
            foreach($input as $sendDocketCopy){
                $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
                if($validator->validate($sendDocketCopy['email'])) {
                    if($sendDocketCopy['sendCopy'] == true){
                        if($emailSubject == ""){  $emailSubject = "You’ve got a email docket copy"; };
                        $jobData = array(
                            'sendDocketCopy'=> $sendDocketCopy,
                            'sentDocket'=> $sentDocket,
                            'emailSubject'=>$emailSubject
                        );
                        SendCopyEmailDocketJob::dispatch($jobData);
//                        dispatch((new SentInvoiceJob($mailData))->delay(Carbon::now()->addSecond(10)));
//                        Mail::to($sendDocketCopy['email'])->send(new SendCopyEmailDocket($sentDocket,$sendDocketCopy,$emailSubject));
                    }
                }
            }
        }
        $slackNotification = array('sender_name' =>$sentDocket->sender_name, 'company_name' => $sentDocket->company_name, 'template_title' => $sentDocket->template_title);
        $emailuserFullname>slackChannel('rt-docket-sent')->notify(new SentDocketNotification($slackNotification));
        return array('status'=>true,'data'=>$recipientNames);
    }

    function buildAutoPrefillerTreeArrayList(array $prefiller, $parentId, $rootId,$gridField){
        $branch = array();
        foreach ($prefiller as $prefillers) {
            $autoPrefillerLinkedGridId =  DocketGridAutoPrefiller::where('grid_field_id',$prefillers['docket_field_grid_id'])->where('docket_field_id',$prefillers['docket_field_id'])->where('index',$prefillers['index'])->get();
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildAutoPrefillerTreeArrayList($prefiller, $prefillers['id'],$rootId,$gridField);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }

                if($gridField->auto_field == 0){
                    if($prefillers['root_id']==0){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                    }elseif($prefillers['root_id'] == $rootId){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                    }else{
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                    }
                }else{
                    if($prefillers['root_id']==0){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>intval($prefillers['docket_field_grid_id']),'prefiller'=>$prefillers['prefiller']);
                    }elseif($prefillers['root_id'] == $rootId){
                        $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>intval($prefillers['docket_field_grid_id']),'prefiller'=>$prefillers['prefiller']);
                    }else{
                        $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'link_grid_field_id'=>@$autoPrefillerLinkedGridId->first()->link_grid_field_id,'prefiller'=>$prefillers['prefiller']);
                    }
                }
            }
        }
        return $branch;
    }


    function buildTreeArrayList(array $prefiller, $parentId, $rootId) {
        $branch = array();
        foreach ($prefiller as $prefillers) {
            if ($prefillers['root_id'] == $parentId) {
                $children = $this->buildTreeArrayList($prefiller, $prefillers['id'],$rootId);
                if ($children) {
                    $prefillers['prefiller'] = $children;
                }else{
                    $prefillers['prefiller'] =[];
                }

               if($prefillers['root_id'] == $rootId){
                    $branch = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                }else{
                    $branch[] = array('id'=>$prefillers['id'],'root_id'=>$prefillers['root_id'],'index'=>$prefillers['index'],'value'=>$prefillers['value'],'prefiller'=>$prefillers['prefiller']);
                }
            }
        }
        return $branch;
    }


    public function saveGridPrefiller (Request $request){
        $parentId = $request->parentId;
        if($request->isdependent == 1){
            $data = array();
            $gridField = DocketFieldGrid::where('docket_field_id',$request->docketFieldId)->where('id', $request->gridFieldId)->get()->first();
            if (max(DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                    $valueData = Input::get('value_'.$i);
                        if($i == $request->index){
                        $docketPrefiller = new DocketPrefillerValue();
                        $docketPrefiller->docket_prefiller_id = $gridField->docket_prefiller_id;
                        $docketPrefiller->label = $valueData;
                        $docketPrefiller->index = $request->index;
                        $docketPrefiller->root_id = $request->parentId;
                        $docketPrefiller->save();
                        $data[] = array(
                            'id'=>$docketPrefiller->id,
                            'value'=>$docketPrefiller->label,
                            'root_id'=>intval($docketPrefiller->root_id),
                            'index'=>  intval($docketPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketPrefiller->id;
                    }else{
                        $docketPrefiller = new DocketPrefillerValue();
                        $docketPrefiller->docket_prefiller_id = $gridField->docket_prefiller_id;
                        $docketPrefiller->label = $valueData;
                        $docketPrefiller->index = $i;
                        $docketPrefiller->root_id = $updateParentId;
                        $docketPrefiller->save();
                        $data[] = array(
                            'id'=>$docketPrefiller->id,
                            'value'=>$docketPrefiller->label,
                            'root_id'=>intval($docketPrefiller->root_id),
                            'index'=>  intval($docketPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketPrefiller->id;
                    }

                }





//                $dataValue = $request->value;
//                ksort($dataValue);
//                foreach($dataValue as $key => $value){
//                    if($key == $request->index){
//                        $docketPrefiller = new DocketPrefillerValue();
//                        $docketPrefiller->docket_prefiller_id = $gridField->docket_prefiller_id;
//                        $docketPrefiller->label = $value;
//                        $docketPrefiller->index = $request->index;
//                        $docketPrefiller->root_id = $request->parentId;
//                        $docketPrefiller->save();
//                        $data[] = array(
//                            'id'=>$docketPrefiller->id,
//                            'value'=>$docketPrefiller->label,
//                            'root_id'=>intval($docketPrefiller->root_id),
//                            'index'=>  intval($docketPrefiller->index),
//                            'docket_field_id'=>$request->docketFieldId,
//                            'docket_field_grid_id'=>$request->gridFieldId,
//                        );
//                        $updateParentId = $docketPrefiller->id;
//                    }else{
//                        $docketPrefiller = new DocketPrefillerValue();
//                        $docketPrefiller->docket_prefiller_id = $gridField->docket_prefiller_id;
//                        $docketPrefiller->label = $value;
//                        $docketPrefiller->index = $key;
//                        $docketPrefiller->root_id = $updateParentId;
//                        $docketPrefiller->save();
//                        $data[] = array(
//                            'id'=>$docketPrefiller->id,
//                            'value'=>$docketPrefiller->label,
//                            'root_id'=>intval($docketPrefiller->root_id),
//                            'index'=>  intval($docketPrefiller->index),
//                            'docket_field_id'=>$request->docketFieldId,
//                            'docket_field_grid_id'=>$request->gridFieldId,
//                        );
//                        $updateParentId = $docketPrefiller->id;
//                    }
//                }
                $datas = $this->buildAutoPrefillerTreeArrayList($data,$parentId,$parentId,$gridField);
                return response()->json(array('status' => true, 'prefiller' => $datas));
            }else{
                return response()->json(array('status' => false, 'message' => 'Invalid Data'));
            }
        }else if($request->isdependent == 0){
            $data = array();
            $gridField = DocketFieldGrid::where('docket_field_id',$request->docketFieldId)->where('id', $request->gridFieldId)->get()->first();
            if (max($gridField->gridFieldPreFiller->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                    $valueData = Input::get('value_'.$i);
                    if($i == $request->index){
                        $docketGridPrefiller = new DocketGridPrefiller();
                        $docketGridPrefiller->docket_field_grid_id = $request->gridFieldId;
                        $docketGridPrefiller->value = $valueData;
                        $docketGridPrefiller->index = $request->index;
                        $docketGridPrefiller->root_id = $request->parentId;
                        $docketGridPrefiller->save();
                        $data[] = array(
                            'id'=>$docketGridPrefiller->id,
                            'value'=>$docketGridPrefiller->value,
                            'root_id'=>intval($docketGridPrefiller->root_id),
                            'index'=>  intval($docketGridPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketGridPrefiller->id;
                    }else{
                        $docketGridPrefiller = new DocketGridPrefiller();
                        $docketGridPrefiller->docket_field_grid_id = $request->gridFieldId;
                        $docketGridPrefiller->value = $valueData;
                        $docketGridPrefiller->index = $i;
                        $docketGridPrefiller->root_id = $updateParentId;
                        $docketGridPrefiller->save();

                        $data[] = array(
                            'id'=>$docketGridPrefiller->id,
                            'value'=>$docketGridPrefiller->value,
                            'root_id'=>intval($docketGridPrefiller->root_id),
                            'index'=> intval($docketGridPrefiller->index),
                            'docket_field_id'=>$request->docketFieldId,
                            'docket_field_grid_id'=>$request->gridFieldId,
                        );
                        $updateParentId = $docketGridPrefiller->id;
                    }

                }

//                $dataValue = $request->value;
//                ksort($dataValue);
//                foreach($dataValue as $key => $value){
//                    if($key == $request->index){
//                        $docketGridPrefiller = new DocketGridPrefiller();
//                        $docketGridPrefiller->docket_field_grid_id = $request->gridFieldId;
//                        $docketGridPrefiller->value = $value;
//                        $docketGridPrefiller->index = $request->index;
//                        $docketGridPrefiller->root_id = $request->parentId;
//                        $docketGridPrefiller->save();
//                        $data[] = array(
//                            'id'=>$docketGridPrefiller->id,
//                            'value'=>$docketGridPrefiller->value,
//                            'root_id'=>intval($docketGridPrefiller->root_id),
//                            'index'=>  intval($docketGridPrefiller->index),
//                            'docket_field_id'=>$request->docketFieldId,
//                            'docket_field_grid_id'=>$request->gridFieldId,
//                        );
//                        $updateParentId = $docketGridPrefiller->id;
//                    }else{
//                        $docketGridPrefiller = new DocketGridPrefiller();
//                        $docketGridPrefiller->docket_field_grid_id = $request->gridFieldId;
//                        $docketGridPrefiller->value = $value;
//                        $docketGridPrefiller->index = $key;
//                        $docketGridPrefiller->root_id = $updateParentId;
//                        $docketGridPrefiller->save();
//
//                        $data[] = array(
//                            'id'=>$docketGridPrefiller->id,
//                            'value'=>$docketGridPrefiller->value,
//                            'root_id'=>intval($docketGridPrefiller->root_id),
//                            'index'=> intval($docketGridPrefiller->index),
//                            'docket_field_id'=>$request->docketFieldId,
//                            'docket_field_grid_id'=>$request->gridFieldId,
//                        );
//                        $updateParentId = $docketGridPrefiller->id;
//
//                    }
//                }
                $datas = $this->buildAutoPrefillerTreeArrayList($data,$parentId,$parentId,$gridField);
                return response()->json(array('status' => true, 'prefiller' => $datas));
            }else{
                return response()->json(array('status' => false, 'message' => 'Invalid Data'));
            }
        }
    }

    public function savePrefiller (Request $request){
        $parentId = $request->parentId;
        if($request->isdependent == 1){
            $data = array();
            $docketField = DocketField::where('id', $request->docketFieldId)->get()->first();
            if (max(DocketPrefillerValue::where('docket_prefiller_id',$docketField->docket_prefiller_id)->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                     $valueData = Input::get('value_'.$i);
                    if($i == $request->index){
                        $docketPrefiller = new DocketPrefillerValue();
                        $docketPrefiller->docket_prefiller_id = $docketField->docket_prefiller_id;
                        $docketPrefiller->label = $valueData;
                        $docketPrefiller->index = $request->index;
                        $docketPrefiller->root_id = $request->parentId;
                        $docketPrefiller->save();
                        $data[]   =  array(
                            'id'=> $docketPrefiller->id,
                            'value'=> $docketPrefiller->label,
                            'index'=>$docketPrefiller->index,
                            'root_id'=> intval($docketPrefiller->root_id),
                        );
                        $updateParentId = $docketPrefiller->id;
                    }else{
                        $docketPrefiller = new DocketPrefillerValue();
                        $docketPrefiller->docket_prefiller_id = $docketField->docket_prefiller_id;
                        $docketPrefiller->label = $valueData;
                        $docketPrefiller->index = $i;
                        $docketPrefiller->root_id = $updateParentId;
                        $docketPrefiller->save();
                        $data[]   =  array(
                            'id'=> $docketPrefiller->id,
                            'value'=> $docketPrefiller->label,
                            'index'=>$docketPrefiller->index,
                            'root_id'=> intval($docketPrefiller->root_id),
                        );
                        $updateParentId = $docketPrefiller->id;
                    }
                }
                $datas = $this->buildTreeArrayList($data,$parentId,$parentId);
                return response()->json(array('status' => true, 'prefiller' => $datas));
            }else{
                return response()->json(array('status' => false, 'message' => 'Invalid Data'));
            }
        }else if($request->isdependent == 0){
            $data = array();
            $docketField = DocketField::where('id', $request->docketFieldId)->get()->first();
            if (max($docketField->docketPreFiller->pluck('index')->toArray()) >= $request->index){
                $updateParentId = "";
                $valueCount = Input::get('lastIndex');
                for ($i = $request->index; $i <= $valueCount; $i++ ){
                    $valueData = Input::get('value_'.$i);
                    if($i == $request->index){
                        $docketGridPrefiller = new DocketFiledPreFiller();
                        $docketGridPrefiller->docket_field_id = $request->docketFieldId;
                        $docketGridPrefiller->value = $valueData;
                        $docketGridPrefiller->index = $request->index;
                        $docketGridPrefiller->root_id = $request->parentId;
                        $docketGridPrefiller->save();
                        $data[]   =  array(
                            'id'=> $docketGridPrefiller->id,
                            'value'=> $docketGridPrefiller->value,
                            'index'=>$docketGridPrefiller->index,
                            'root_id'=> intval($docketGridPrefiller->root_id),
                        );
                        $updateParentId = $docketGridPrefiller->id;
                    }else{
                        $docketPrefiller = new DocketFiledPreFiller();
                        $docketPrefiller->docket_prefiller_id = $docketField->docket_prefiller_id;
                        $docketPrefiller->label = $valueData;
                        $docketPrefiller->index = $i;
                        $docketPrefiller->root_id = $updateParentId;
                        $docketPrefiller->save();
                        $data[]   =  array(
                            'id'=> $docketPrefiller->id,
                            'value'=> $docketPrefiller->label,
                            'index'=>$docketPrefiller->index,
                            'root_id'=> intval($docketPrefiller->root_id),
                        );
                        $updateParentId = $docketPrefiller->id;
                    }
                }
                $datas = $this->buildTreeArrayList($data,$parentId,$parentId);
                return response()->json(array('status' => true, 'prefiller' => $datas));
            }else{
                return response()->json(array('status' => false, 'message' => 'Invalid Data'));
            }
        }
    }


    public function numberSystem(Request $request){


        //employee section sn
//        $employeessss = array_unique(Employee::pluck('company_id')->toArray());
//        foreach ($employeessss as $employ){
//          $empl=   Employee::where('company_id',$employ)->get();
//          $sn = 2;
//          foreach ($empl as $data){
//              Employee::where('id',$data->id)->update(['sn'=>$sn]);
//              $sn++;
//          }
//        }

        //only for local
//          $docket  = SentDockets::where('id','<=','8235')->get();
//          foreach ($docket as $items){
//              $items->company_docket_id = $items->id;
//              $items->save();
//          }

//        $docket  = SentDockets::where('id','<=','8235')->get();
//        foreach ($docket as $dockets){
//            $dockets->formatted_id =  "rt-".$dockets->sender_company_id."-doc-".$dockets->company_docket_id;
//            $dockets->update();
//        }


        //only for local
//          $docket  = EmailSentDocket::where('id','<=','8235')->get();
//          foreach ($docket as $items){
//              $items->company_docket_id = $items->id;
//              $items->save();
//          }

//        $docket  = EmailSentDocket::where('id','<=','8235')->get();
//        foreach ($docket as $dockets){
//            $dockets->formatted_id =  "rt-".$dockets->company_id."-edoc-".$dockets->company_docket_id;
//            $dockets->update();
//        }

//        $docket  = Docket::where('id','<=','8235')->get();
//        foreach ($docket as $dockets){
//            $dockets->prefix = "doc";
//            $dockets->save();
//        }
//
//        $invoice  = Invoice::where('id','<=','8235')->get();
//        foreach ($invoice as $invoices){
//            $invoices->prefix = "inv";
//            $invoices->save();
//        }

        //only for local
//        $invoice  = SentInvoice::where('id','<=','8235')->get();
//        foreach ($invoice as $items){
//            $items->company_invoice_id = $items->id;
//            $items->save();
//        }

//        $invoice  = SentInvoice::where('id','<=','8235')->get();
//        foreach ($invoice as $invoices){
//            $invoices->formatted_id =  "rt-".$invoices->company_id."-inv-".$invoices->company_invoice_id;
//            $invoices->update();
//        }

        //only for local
//        $invoice  = EmailSentInvoice::where('id','<=','8235')->get();
//          foreach ($invoice as $items){
//              $items->company_invoice_id = $items->id;
//              $items->save();
//          }


//        $invoice  = EmailSentInvoice::where('id','<=','8235')->get();
//        foreach ($invoice as $invoices){
//            $invoices->formatted_id =  "rt-".$invoices->company_id."-einv-".$invoices->company_invoice_id;
//            $invoices->update();
//        }


    }


    public  function deleteDraft(Request $request){
        $docketId = $request->docket_draft_id;
        DocketDraft::whereIn('id', $docketId)->where('user_id',$request->header('userId'))->delete();
        return response()->json(array('status' => true));
    }


    public function nextDocketId(Request $request){

        if($request->type == "DOCKET"){
            $docket= Docket::where('id',$request->template_id)->where('company_id',$request->header('companyId'))->get()->first();
            if($docket != null){
                $company = Company::where('id',$request->header('companyId'))->first();
                if($company->number_system == 1){
                    return response()->json(array('status' => false, 'message'=>'Invalid Data'));
                }else{

                    if($docket->is_docket_number == 1){
                        $sentDocket = "";
                        $findUserDocketCount = SentDockets::where('user_id', $request->header('userId'))->where('sender_company_id', $request->header('companyId'))->where('docket_id',$request->template_id)->pluck('user_docket_count')->toArray();
                        $findUserEmailDocketCount =EmailSentDocket::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->where('docket_id',$request->template_id)->pluck('user_docket_count')->toArray();
                        $mergeData =array_merge($findUserDocketCount,$findUserEmailDocketCount);
                        if(count($mergeData) == 0){
                            $maxData = 0;
                        }else{
                            $maxData=  max($mergeData);
                        }

                        if($maxData == 0){

                            $uniquemax = 0;
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($docket->hide_prefix == 1){
                                    $sentDocket = $docket->id."-1-".($uniquemax+1);
                                }else{
                                    $sentDocket = "RT-".$docket->prefix."-".$docket->id."-1-".($uniquemax+1);
                                }
                            }else{
                                if($docket->hide_prefix == 1){
                                    $sentDocket = $docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }else{
                                    $sentDocket = "RT-".$docket->prefix."-".$docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);
                                }
                            }

                        }else{
                            $uniquemax = max(array_merge($findUserDocketCount,$findUserEmailDocketCount));
                            $employeeData = Employee::where('user_id', $request->header('userId'))->where('company_id', $request->header('companyId'))->get();
                            if($employeeData->count() == 0){
                                if($docket->hide_prefix == 1){
                                    $sentDocket = $docket->id."-1-".($uniquemax+1);

                                }else{
                                    $sentDocket = "RT-".$docket->prefix."-".$docket->id."-1-".($uniquemax+1);
                                }
                            }else{
                                if($docket->hide_prefix == 1){
                                    $sentDocket = $docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                                }else{
                                    $sentDocket = "RT-".$docket->prefix."-".$docket->id."-".$employeeData->first()->sn."-".($uniquemax+1);

                                }
                            }

                        }

                        return response()->json(array('status' => true, 'data'=>$sentDocket));
                    }else{
                        return response()->json(array('status' => false, 'message'=>'Show Number System must me checked'));
                    }


                }
            }else{
                return response()->json(array('status' => false, 'message'=>'Invalid Data'));
            }

        }else{
            return response()->json(array('status' => false, 'message'=>'Invalid Data'));
        }
    }




    public function updateDocketAprovalMethod(Request $request){

        $validator  =   Validator::make($request->all(),['docket_id'=>'required|int','docket_approval_type'=>'required|int']);
        if ($validator->fails()):
            foreach ($validator->messages()->getMessages() as $field_name => $messages){
                $errors[]=$messages[0];
            }
            return response()->json(array('status' => false,'message' => $errors[0]));
        else:
            Docket::where('id',$request->docket_id)->where('company_id',$request->header('companyId'))->update(['docketApprovalType'=>$request->docket_approval_type]);
            return response()->json(array('status' => true));
        endif;
    }


    public function getParentData($data){
        return FunctionUtils::docketPreFiller(new DocketGridPrefiller(),$data,'value');
    }

    public function getDocketPrefiller($data){
        return FunctionUtils::docketPreFiller(new DocketPrefillerValue(),$data,'label');
    }

    public function getNormalParentData($data){
        return FunctionUtils::docketPreFiller(new DocketFiledPreFiller(),$data,'value');
    }
    public static function array_values_recursive($ary){
        $lst = array();
        foreach( array_keys($ary) as $k ){
            $v = $ary[$k];
            if (is_scalar($v)) { $lst[] = $v;}
            elseif (is_array($v)) {
                $lst = array_merge( $lst, FunctionUtils::array_values_recursive($v));
            }
        }
        return $lst;
    }
    public function testgetDocketTemplateDetailsById(Request $request,$id){
        ini_set('memory_limit','256M');
        set_time_limit(0);
        if(Docket::where('id',$id)->count()>0){
            $docket     =   Docket::where('id',$id)->first();
            $docketFieldQuery    =   DocketField::where('docket_id',$docket->id)->orderBy('order','asc')->get();
            $docketFields   =   array();
            foreach ($docketFieldQuery as $row){
                $subField   =   array();
                if($row->docket_field_category_id == 7) {
                    $subField = DocketUnitRate::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subField,
                        'subFieldUnitRate' => $subField);
                }elseif ($row->docket_field_category_id==13){
                    $subField = DocketFieldFooter::select('id', 'value')->where("field_id", $row->id)->orderBy('id', 'asc')->get();
                    $footers = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField'  => $subField,
                        'subFieldFooter' => $subField);

                }elseif ($row->docket_field_category_id == 29) {
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 10){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }


                    }elseif($row->is_dependent == 2){

                        $defaultPrefillerValue = "";
                        $canAddChild = false;

                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                $prefiller = array();
                                $arrayIndex =   array();
                                if(count($arrayData) > 10){
                                    $isBigData = true;
                                }else{

                                    if($row->link_prefiller_filter_label){
                                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                                        foreach ($filtervalue as $keyValue=>$filtervalues){
                                            if($row->link_prefiller_filter_value != $filtervalues){
                                                $arrayIndex[] = $keyValue;
                                            }
                                        }
                                    }

                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (!in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }

                            }
                        }


                    }else{

                        if(count($row->docketPreFiller) > 10){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }

                    }

                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$defaultPrefillerValue),
                        'required'=>$row->required,
                        'subField'  => $subField,
                        'send_copy_docket'=>$row->send_copy_docket);


                }elseif ($row->docket_field_category_id==20) {
                    $subField = DocketManualTimer::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $breakSubField = DocketManualTimerBreak::select('id','type', 'label','explanation')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $merge= array();
                    foreach($subField as $rowDtas){
                        $merge[]  = array('id' =>$rowDtas->id,
                            'type'=>$rowDtas->type,
                            'label'=>$rowDtas->label,

                        );
                    }
                    foreach($breakSubField as $rowDta){
                        $merge[]  = array('id' =>$rowDta->id,
                            'type'=>$rowDta->type,
                            'label'=>$rowDta->label,
                            'explanation'=> intval($rowDta->explanation)
                        );
                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $merge,
                        'manualTimerSubField' => $merge);

                }
                elseif ($row->docket_field_category_id==3){
                    $prefiller = array();
                    $docketFieldNumber = DocketFieldNumber::select('min', 'max','tolerance')->where("docket_field_id", $row->id)->first();
                    if ($docketFieldNumber==null){
                        $docketFieldNumbers = array(
                            'min' => null,
                            'max' => null,
                            'tolerance' => null,
                        );
                    }else{
                        $docketFieldNumbers= $docketFieldNumber;
                    }
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;


                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 10){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }
                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                $prefiller = array();
                                $arrayIndex = array();
                                if(count($arrayData) > 10){
                                    $isBigData = true;
                                }else{
                                    if($row->link_prefiller_filter_label){
                                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                                        foreach ($filtervalue as $keyValue=>$filtervalues){
                                            if($row->link_prefiller_filter_value != $filtervalues){
                                                $arrayIndex[] = $keyValue;
                                            }
                                        }
                                    }
                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (!in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }


                            }
                        }
                    }
                    else{

                        if(count($row->docketPreFiller) > 10){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }
                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'prefiller_data'=>($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent, 'canAddChild'=>$canAddChild ,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'config'=>$docketFieldNumbers,
                        'subField'  => $subField);


                }
                elseif ($row->docket_field_category_id == 18) {
                    $subFields = array();

                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 1){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 0){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;
                    foreach ($row->yesNoField as $subRow):
                        if($subRow->type == 2){
                            $subDocket = array();
                            foreach ($subRow->yesNoDocketsField as $subRowDocket):
                                $subDocket[] = array(
                                    'id' => $subRowDocket->id,
                                    'docket_field_category_id' => $subRowDocket->docket_field_category_id,
                                    'order' => $subRowDocket->order,
                                    'required' => $subRowDocket->required,
                                    'label' => $subRowDocket->label,
                                );
                            endforeach;
                            $subFields[] = array(
                                'id' => $subRow->id,
                                'label' => $subRow->label,
                                'type' => $subRow->type,
                                'colour' => $subRow->colour,
                                'explanation' => $subRow->explanation,
                                'docket_field_id' => $subRow->docket_field_id,
                                'label_icon' => AmazoneBucket::url() . $subRow->icon_image,
                                'label_type' => $subRow->label_type,
                                'subDocket' => ($subRow->explanation == 0) ? [] : $subDocket,
                            );
                        }
                    endforeach;


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required' => $row->required,
                        'subField' => $subFields,
                        'yesNoSubField' => $subFields);

                }
                elseif ($row->docket_field_category_id==15) {
                    $subFields= array();
                    foreach($row->docketAttached as $subRow):
                        $subFields[]   =     array(
                            'id' => $subRow->id,
                            'name'=>$subRow->name,
                            'url' => AmazoneBucket::url() . $subRow->url);
                    endforeach;
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subFields,
                        'documentSubField' => $subFields);

                }elseif($row->docket_field_category_id==9){
                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    if ($row->is_dependent == 1){

                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 10){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }


                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }

                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){
                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                if(count($arrayData) > 10){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    $arrayIndex = array();
                                    if($row->link_prefiller_filter_label){
                                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                                        foreach ($filtervalue as $keyValue=>$filtervalues){
                                            if($row->link_prefiller_filter_value != $filtervalues){
                                                $arrayIndex[] = $keyValue;
                                            }
                                        }
                                    }


                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (!in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;

                                }

                            }
                        }
                    }else{
                        if(count($row->docketPreFiller) > 10){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }
                            }
                        }

                    }


                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'name_required'=> (@$row->docketFieldSignatureOption->name == null) ? 0: @$row->docketFieldSignatureOption->name,
                        'required'=>$row->required ,
                        'prefiller_data' => ($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'subField'  => $subField);
                }elseif ($row->docket_field_category_id == 6){

                    $prefiller = array();
                    foreach($row->docketPreFiller as $subRow):
                        $prefiller[]   =  array(
                            'id'=> $subRow->id,
                            'value'=> $subRow->value,
                            'root_id'=> intval($subRow->root_id),
                        );
                    endforeach;
                    $datas = $this->buildTreeArray($prefiller);
                    if($row->default_prefiller_id == null){
                        $defaultPrefillerValue = "";
                    }else{
                        if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                            $defaultPrefillerValue = "";

                        }else{
                            $defaultPrefillerValue = DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->pluck('value')->toArray();
                        }
                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'prefiller'=> $datas ,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'subField'  => $subField);

                }elseif($row->docket_field_category_id == 24){
                    $subField = DocketTallyableUnitRate::select('id', 'type', 'label')->where("docket_field_id", $row->id)->orderBy('type', 'asc')->get();
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subField,
                    );

                }
                elseif ($row->docket_field_category_id == 22){
                    $modularField  = array();
                    $sumableStatus = false;
                    $canAddChild = true;
                    $isEmailedSubject = false;


                    foreach ($row->girdFields as $gridField)
                    {
                        $isBigData = false;

                        if($gridField->is_emailed_subject == 1){
                            $isEmailedSubject = true;
                        }

                        if ($gridField->docket_field_category_id == 3){
                            if ($gridField->sumable == 1){
                                $sumableStatus = true;
                            }
                        }

                        if ($gridField->is_dependent == 1){
                            if ($gridField->auto_field == 1){
                                $prefiller = array();
                                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();

                                if(count($docketPrefillerValue) > 10){
                                    $isBigData = true;
                                }else{
                                    foreach($docketPrefillerValue as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->label,
                                            'index'=> $subRow->index,
                                            'docket_field_id'=>$gridField->docket_field_id,
                                            'docket_field_grid_id'=>$gridField->id,
                                            'root_id'=> intval($subRow->root_id),
                                        );
                                    endforeach;
                                    $datas = $this->testbuildAutoPrefillerTreeArray($prefiller);
                                }
                            }else{
                                $prefiller = array();
                                $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$gridField->docket_prefiller_id)->get();
                                if(count($docketPrefillerValue) > 10){
                                    $isBigData = true;
                                }else{
                                    foreach($docketPrefillerValue as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->label,
                                            'root_id'=> intval($subRow->root_id),
                                            'index'=> intval($subRow->index),
                                        );
                                    endforeach;
                                    $datas = $this->buildTreeArray($prefiller);
                                }

                            }
                            if($gridField->default_prefiller_id == null){
                                $defaultPrefillerValue = "";
                            }else{
                                if( DocketPrefillerValue::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                                    $defaultPrefillerValue = "";
                                }else{
                                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                                    $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                    $prefillerArray =    array();
                                    foreach ($parentPrefillers as $prefiller) {
                                        $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                        $value = $this->array_values_recursive($parentArray);
                                        $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();
                                        if( count($value) == 0){
                                            $prefillerArray[] = implode(',',$defaultValue);
                                        }else{
                                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                        }
                                    }
                                }
                            }
                            $docketPrefillers = DocketPrefiller::where('id',$gridField->docket_prefiller_id)->first();
                            if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                                $canAddChild = false;
                            }


                        }elseif($gridField->is_dependent == 2){
                            $defaultPrefillerValue = "";
                            $datas = "";

                            $arrayIndex = array();
                            if($gridField->prefillerEcowise){
                                if($gridField->link_prefiller_filter_label){
                                    $filtervalue = json_decode($gridField->prefillerEcowise->data, true)[$gridField->link_prefiller_filter_label];
                                    foreach ($filtervalue as $keyValue=>$filtervalues){
                                        if($gridField->link_prefiller_filter_value != $filtervalues){
                                            $arrayIndex[] = $keyValue;
                                        }
                                    }
                                }
                            }

                            if(count($arrayIndex) > 10){
                                $isBigData = true;
                            }else{
                                if ($gridField->auto_field == 1){
                                    $canAddChild = false;
                                    if($gridField->selected_index_value != null){
                                        $firstIndex = str_replace('_', ' ', $gridField->selected_index_value);
                                        $esowise = json_decode($gridField->prefillerEcowise->data,true);
                                        if(array_key_exists($firstIndex,$esowise) == true) {
                                            $firstIndexData = $esowise[$firstIndex];
                                            $prefiller = array();
                                            foreach ($firstIndexData as $key => $firstIndexDatas) {
                                                if(!in_array($key,$arrayIndex)){
                                                    $prefiller[] = array(
                                                        'id' => strval($key + 1),
                                                        'value' => (is_array($firstIndexDatas) == 1) ? "" : $firstIndexDatas,
                                                        'index' => 1,
                                                        'link_grid_field_id' => $gridField->id,
                                                        'root_id' => strval(0),
                                                    );
                                                }
                                            }
                                        }

                                        $ecowiseAutoPrefiller = (new Collection($gridField->gridFieldAutoPreFiller))->sortBy('index');
                                        foreach ($ecowiseAutoPrefiller as $ecowiseAutoPrefillers){
                                            if(array_key_exists(str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index),$esowise) == true) {
                                                $prefillerData  = $esowise[str_replace('_', ' ', $ecowiseAutoPrefillers->selected_index)];
                                                foreach ($prefillerData as $key=> $prefillerDatas){

                                                    if($ecowiseAutoPrefillers->index == 2){
                                                        if(!in_array($key,$arrayIndex)){
                                                            $prefiller[] = array(
                                                                'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                                                'value'=> (is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                                                'index'=> $ecowiseAutoPrefillers->index,
                                                                'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                                                'root_id'=> strval($key+1),
                                                            );
                                                        }
                                                    }else{
                                                        if(!in_array($key,$arrayIndex)){
                                                            $prefiller[] = array(
                                                                'id'=> ($key+1)."-".$ecowiseAutoPrefillers->index,
                                                                'value'=>(is_array($prefillerDatas)== 1)? "" : $prefillerDatas,
                                                                'index'=> $ecowiseAutoPrefillers->index,
                                                                'link_grid_field_id'=>$ecowiseAutoPrefillers->link_grid_field_id,
                                                                'root_id'=> ($key+1)."-".($ecowiseAutoPrefillers->index-1),
                                                            );
                                                        }
                                                    }


                                                }
                                            }

                                        }

                                        $datas =   $this->findEcowisePrefillerValue($prefiller);
                                    }


                                }else{
                                    $canAddChild = false;
                                    if($gridField->selected_index_value != null) {
                                        $keyValue = str_replace('_', ' ', $gridField->selected_index_value);
                                        if(array_key_exists($keyValue,json_decode($gridField->prefillerEcowise->data, true)) == true){
                                            $arrayData = json_decode($gridField->prefillerEcowise->data, true)[$keyValue];
                                            $prefiller = array();
                                            foreach ($arrayData as $key=>$arrayDatas){
                                                if(!in_array($key,$arrayIndex)){
                                                    $prefiller[] = array(
                                                        'id' => 0,
                                                        'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                        'index' => 0,
                                                        'docket_field_id' => $gridField->docket_field_id,
                                                        'docket_field_grid_id' => $gridField->id,
                                                        'root_id' => 0,
                                                    );
                                                }
                                            }
                                            $datas = $prefiller;
                                        }
                                    }
                                }
                            }

                        }else{
                            if ($gridField->auto_field == 1){
                                if(count($gridField->gridFieldPreFiller) > 10){
                                    $isBigData = true;
                                }else{
                                    $prefillerss = array();
                                    foreach($gridField->gridFieldPreFiller as $subRow):
                                        $prefillerss[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->value,
                                            'index'=> $subRow->index,
                                            'docket_field_id'=>$gridField->docket_field_id,
                                            'docket_field_grid_id'=>$subRow->docket_field_grid_id,
                                            'root_id'=> intval($subRow->root_id),
                                        );
                                    endforeach;
                                    $datas = $this->testbuildAutoPrefillerTreeArray($prefillerss);
                                }

                            }

                            else{
                                if(count($gridField->gridFieldPreFiller) > 10){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    foreach($gridField->gridFieldPreFiller as $subRow):
                                        $prefiller[]   =  array(
                                            'id'=> $subRow->id,
                                            'value'=> $subRow->value,
                                            'root_id'=> intval($subRow->root_id),
                                            'index'=> intval($subRow->index),
                                        );
                                    endforeach;
                                    $datas = $this->buildTreeArray($prefiller);
                                }

                            }


                            if($gridField->default_prefiller_id == null){
                                $defaultPrefillerValue = "";
                            }else{
                                if( DocketGridPrefiller::whereIn('id',unserialize($gridField->default_prefiller_id))->count()==0){
                                    $defaultPrefillerValue = "";
                                }else{
                                    $defaultPrefillerValue = unserialize($gridField->default_prefiller_id);
                                    $parentPrefillers = DocketGridPrefiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                    $prefillerArray =    array();
                                    foreach ($parentPrefillers as $prefiller) {
                                        $parentArray= $this->getParentData($prefiller->root_id);
                                        $value = $this->array_values_recursive($parentArray);
                                        $defaultValue   =   DocketGridPrefiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                        if( count($value) == 0){
                                            $prefillerArray[] = implode(',',$defaultValue);
                                        }else{
                                            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                        }
                                    }

                                }
                            }
                        }

                        if ($gridField->gridFieldFormula != null){
                            $formulaValue = unserialize($gridField->gridFieldFormula->formula);
                            $formulaArray = array();
                            foreach ($formulaValue as $formulaValues){

                                if (is_numeric($formulaValues)){
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "number"
                                    );
                                }elseif (preg_match("/TDiff/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "function"
                                    );
                                }
                                elseif (preg_match("/cell/i", $formulaValues)) {
                                    $formulaArray[] = array(
                                        "value" => ltrim($formulaValues, 'cell'),
                                        "type" => "cell"
                                    );
                                }else{
                                    $formulaArray[] = array(
                                        "value" => $formulaValues,
                                        "type" => "operator"
                                    );
                                }



                            }

                        }else{
                            $formulaArray = array();
                        }

                        $gridManualTimer  = array();
                        if($gridField->docket_field_category_id == 20){

                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 1,
                                'label' => "From"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 2,
                                'label' => "To"
                            );
                            $gridManualTimer[]= array(
                                'id'=> 0,
                                'type'=> 3,
                                'label' => "Total Break",
                                'explanation'=> 0
                            );

                        }

                        $gridAutoFillerDefault = 0;
                        if($gridField->auto_field == 1){
                            $gridAutoFillerDefault = $gridField->default_auto_fill_prefiller;
                        }


                        $data['id'] = $gridField->id;
                        $data['docket_field_id'] = $gridField->docket_field_id;
                        $data['docket_field_category_id'] = $gridField->docketFieldCategory->id;
                        $data['docket_field_category_label'] = $gridField->docketFieldCategory->title;
                        $data['label'] = $gridField->label;
                        $data['order'] = $gridField->order;
                        $data['is_emailed_subject'] = $gridField->is_emailed_subject;
                        $data['required'] = $gridField->required;
                        $data['prefiller_data'] =($isBigData == true) ? array('hasExtraPrefiller'=> true,'autoPrefiller'=>$gridField->auto_field,'gridAutoFillerDefaultId'=>$gridAutoFillerDefault,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>  [] ) : array('autoPrefiller'=>$gridField->auto_field,'gridAutoFillerDefaultId'=>$gridAutoFillerDefault,'isDependent'=>$gridField->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=> ($datas == "")? [] : $datas );
                        $data['default_value'] = ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray);
                        $data['subField'] =$gridManualTimer;
                        $data['manualTimerSubField'] = $gridManualTimer;
                        $data['sumable'] = ($gridField->sumable== 1)? true : false ;
                        $data['formula']=  @$formulaArray;
                        $data['send_copy_docket']=  $gridField->send_copy_docket;
                        array_push($modularField, $data);
                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'time_required'=>(@$row->docketFieldDateOption->time == null) ? 0: @$row->docketFieldDateOption->time,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'is_emailed_subject'=>($isEmailedSubject == true) ? 1: 0,
                        'modularGrid' => $modularField,
                        'sumable'=> $sumableStatus,
                        'subField'  => $subField);
                }elseif($row->docket_field_category_id == 28){
                    $templateFolderAssign = TemplateAssignFolder::where('template_id',$id)->get()->first();
                    $subFields= array();
                    $folderName = Folder::where('company_id',$request->header('companyId'))->where('type',0)->orderBy('name','asc')->get();
                    $folderList = array();
                    foreach($folderName as $subRow):
                        $folderList[]   =  array(
                            'id'=> $subRow->id,
                            'name'=> $subRow->name,
                            'root_id'=> intval($subRow->root_id),
                        );
                    endforeach;
                    $folderLists = $this->folderList($folderList);
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'required'=>$row->required,
                        'subField' => $subFields,
                        'folderList'=>$folderLists,
                        'default_value'=> ($row->folder_default_id != 0) ? $row->folder_default_id : (($templateFolderAssign == null)? "" : strval($templateFolderAssign->folder_id)),

                        // 'default_value'=> ($templateFolderAssign == null)? "" : strval($templateFolderAssign->folder_id),
                    );
                }elseif ($row->docket_field_category_id != 30) {

                    $prefiller = array();
                    $canAddChild = true;
                    $isBigData = false;
                    if ($row->is_dependent == 1){
                        $prefiller = array();
                        $docketPrefillerValue = DocketPrefillerValue::where('docket_prefiller_id',$row->docket_prefiller_id)->get();
                        if(count($docketPrefillerValue) > 10){
                            $isBigData = true;
                        }else{
                            foreach($docketPrefillerValue as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->label,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }
                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketPrefillerValue::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketPrefillerValue::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getDocketPrefiller($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketPrefillerValue::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('label')->toArray();

                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
                                    }
                                }

                            }
                        }

                        $docketPrefillers = DocketPrefiller::where('id',$row->docket_prefiller_id)->first();
                        if (@$docketPrefillers->type == 1 || @$docketPrefillers->type == 2){
                            $canAddChild = false;
                        }

                    }elseif($row->is_dependent == 2){
                        $defaultPrefillerValue = "";
                        $canAddChild = false;
                        if($row->selected_index != null) {
                            $keyValue = str_replace('_', ' ', $row->selected_index);
                            if(array_key_exists($keyValue,json_decode($row->prefillerEcowise->data, true)) == true){

                                $arrayData = json_decode($row->prefillerEcowise->data, true)[$keyValue];
                                if(count($arrayData) > 10){
                                    $isBigData = true;
                                }else{
                                    $prefiller = array();
                                    $arrayIndex = array();
                                    if($row->link_prefiller_filter_label){
                                        $filtervalue = json_decode($row->prefillerEcowise->data, true)[$row->link_prefiller_filter_label];
                                        foreach ($filtervalue as $keyValue=>$filtervalues){
                                            if($row->link_prefiller_filter_value != $filtervalues){
                                                $arrayIndex[] = $keyValue;
                                            }
                                        }
                                    }

                                    foreach ($arrayData as $KEY=>$arrayDatas){
                                        if (!in_array($KEY,$arrayIndex)){
                                            $prefiller[] = array(
                                                'id' => 0,
                                                'value' =>(is_array($arrayDatas)== 1)? "" : $arrayDatas ,
                                                'index' => 0,
                                                'docket_field_id' => $row->id,
                                                'root_id' => 0,
                                            );
                                        }
                                    }
                                    $datas = $prefiller;
                                }


                            }
                        }
                    }else{

                        if(count($row->docketPreFiller) > 10){
                            $isBigData = true;
                        }else{
                            foreach($row->docketPreFiller as $subRow):
                                $prefiller[]   =  array(
                                    'id'=> $subRow->id,
                                    'value'=> $subRow->value,
                                    'root_id'=> intval($subRow->root_id),
                                    'index'=> intval($subRow->index),
                                );
                            endforeach;
                            $datas = $this->buildTreeArray($prefiller);
                        }

                        if($row->default_prefiller_id == null){
                            $defaultPrefillerValue = "";
                        }else{
                            if( DocketFiledPreFiller::whereIn('id',unserialize($row->default_prefiller_id))->count()==0){
                                $defaultPrefillerValue = "";
                            }else{
                                $defaultPrefillerValue = unserialize($row->default_prefiller_id);
                                $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
                                $prefillerArray =    array();
                                foreach ($parentPrefillers as $prefiller) {
                                    $parentArray= $this->getNormalParentData($prefiller->root_id);
                                    $value = $this->array_values_recursive($parentArray);
                                    $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
                                    if( count($value) == 0){
                                        $prefillerArray[] = implode(',',$defaultValue);
                                    }else{
                                        $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);

                                    }
                                }
                            }
                        }

                    }
                    $docketFields[] = array('id' => $row->id,
                        'docket_field_category_id' => $row->docket_field_category_id,
                        'docket_field_category' => $row->fieldCategoryInfo->title,
                        'label' => $row->label,
                        'order' => $row->order,
                        'prefiller_data' =>($isBigData == true) ? array('hasExtraPrefiller'=> true,'isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>[]) : array('isDependent'=>$row->is_dependent,'canAddChild'=>$canAddChild,'prefiller'=>$datas),
                        'is_emailed_subject' => $row->is_emailed_subject,
                        'default_value'=> ($defaultPrefillerValue=="")? "" : implode(", ",$prefillerArray),
                        'required'=>$row->required,
                        'subField'  => $subField);
                }

            }
            if(@$footers){
                $docketFields[] =   $footers;
            }

            $isDocketNumber = false;
            $company = Company::where('id',$request->header('companyId'))->first();
            if($company->number_system == 1){
                $isDocketNumber = false;
            }else{
                if($docket->is_docket_number == 1){
                    $isDocketNumber = true;
                }else{
                    $isDocketNumber = false;
                }
            }

            $template =   array(
                'id'=> $docket->id,
                'title'=>$docket->title,
                'subTitle'=>$docket->subTitle,
                'invoiceable'=>$docket->invoiceable,
                'timer_attachement'=>$docket->timer_attachement,
                'docket_field'=> $docketFields,
                'isDocketNumber'=>  $isDocketNumber,
            );
            $data= [];

            if($row->docketInfo->defaultRecipient){
                $rt_user_receivers = array();
                $email_user_receivers = array();
                foreach ($row->docketInfo->defaultRecipient as $defaultRecipients){
                    if(@$defaultRecipients->user_type== 1){
                        if(Employee::where('user_id', $defaultRecipients->userInfo->id)->count()!=0):
                            $companyId = Employee::where('user_id', $defaultRecipients->userInfo->id)->first()->company_id;
                        else :
                            $companyId   =   Company::where('user_id', $defaultRecipients->userInfo->id)->first()->id;
                        endif;
                        $rt_user_receivers[] = array(
                            'user_id'=> $defaultRecipients->userInfo->id,
                            'company_id'=>$companyId,
                            'company_name'=> Company::findorFail($companyId)->name,
                            'company_abn'=>Company::findorFail($companyId)->abn,
                            'first_name'=>$defaultRecipients->userInfo->first_name,
                            'last_name'=>$defaultRecipients->userInfo->last_name,
                            'image'=>AmazoneBucket::url() . $defaultRecipients->userInfo->image
                        );
                    }
                    if(@$defaultRecipients->user_type== 2){
                        if (@$defaultRecipients->emailUser->emailClient->email_user_id == @$defaultRecipients->emailUser->id){
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'full_name'=>$defaultRecipients->emailUser->emailClient->full_name,
                                'company_name'=>$defaultRecipients->emailUser->emailClient->company_name,
                                'company_address'=>$defaultRecipients->emailUser->emailClient->company_address,
                                'saved'=>true
                            );
                        }else{
                            $email_user_receivers[] = array(
                                'email_user_id'=>$defaultRecipients->emailUser->id,
                                'email'=> $defaultRecipients->emailUser->email,
                                'saved'=>false
                            );
                        }
                    }
                }
            }
            $rt_user_approvers =[];
            $email_user_approvers =[];
            return response()->json(array('status' => true,'template'=>$template,'rt_user_receivers'=>$rt_user_receivers,'rt_user_approvers'=>$rt_user_approvers,'email_user_receivers'=>$email_user_receivers,'email_user_approvers'=>$email_user_approvers,'data'=>$data));
        }else{
            return response()->json(array("status" => true,"message"=>'Docket not found!'));
        }
    }


    public function awsStoreImage(){



    }


    public function storeImages3(Request $request){


        $aws = Storage::disk('s3');

        // $data = DocketField::get();
        // foreach ($data as $datas){
        //   if($datas->link_prefiller_filter_label){
        //       $linkPrefillerFilter = new LinkPrefillerFilter();
        //       $linkPrefillerFilter->docket_field_id = $datas->id;
        //       $linkPrefillerFilter->link_prefiller_filter_label = $datas->link_prefiller_filter_label;
        //       $linkPrefillerFilter->link_prefiller_filter_value = $datas->link_prefiller_filter_value;
        //       $linkPrefillerFilter->save();
        //   }
        // }

        // $data = DocketFieldGrid::get();
        // foreach ($data as $datas){
        //     if($datas->link_prefiller_filter_label){
        //         $linkPrefillerFilter = new LinkGridPrefillerFilter();
        //         $linkPrefillerFilter->docket_field_grid_id = $datas->id;
        //         $linkPrefillerFilter->link_prefiller_filter_label = $datas->link_prefiller_filter_label;
        //         $linkPrefillerFilter->link_prefiller_filter_value = $datas->link_prefiller_filter_value;
        //         $linkPrefillerFilter->save();
        //     }
        // }

        try{
            DB::beginTransaction();
            // $companies = Company::all();
            // foreach($companies as $company){
            //     if($company->logo){
            //         if(file_exists($company->logo)){
            //             $img = Image::make($company->logo)->stream()->__toString();
            //             $aws->put($company->logo, $img,'public');
            //             // $company->logo = AmazoneBucket::url() .'/'. $company->logo;
            //             // $company->save();
            //         }
            //     }
            // }

            // $attachments = DocketAttachments::all();
            // foreach($attachments as $attachment){
            //     if($attachment->url){
            //         if(file_exists($attachment->url)){
            //             // $img = Image::make($attachment->url)->stream()->__toString();
            //             $aws->put($attachment->url,file_get_contents($attachment->url),'public');
            //             // $attachment->url = AmazoneBucket::url() .'/'. $attachment->url;
            //             // $attachment->save();
            //         }
            //     }
            // }

            // $docketDocuments = DocketDocument::all();
            // foreach($docketDocuments as $docketDocument){
            //     if($docketDocument->files){
            //         if(file_exists($docketDocument->files)){
            //             // $pdf = PDF::loadView($docketDocument->files);
            //             $aws->put($docketDocument->files,file_get_contents($docketDocument->files),'public');
            //             // $docketDocument->files = AmazoneBucket::url() .'/'. $docketDocument->files;
            //             // $docketDocument->save();
            //         }
            //     }
            // }

            // $documentTheme = DocumentTheme::all();
            // foreach($documentTheme as $theme){
            //     if($theme->preview){
            //         // $save = false;
            //         // $temp = [];
            //         if(unserialize($theme->screenshot)){
            //             foreach(unserialize($theme->screenshot) as $screenshot){
            //                 // $screenshotUrl = '';
            //                 if(file_exists($screenshot)){
            //                     $img = Image::make($screenshot)->stream()->__toString();
            //                     $aws->put($screenshot, $img,'public');
            //                     // $screenshotUrl = AmazoneBucket::url() .'/'. $screenshot;
            //                     // $save = true;
            //                 }else{
            //                     // $screenshotUrl = $screenshot;
            //                 }
            //                 // array_push($temp,$screenshotUrl);
            //             }
            //         }
            //         // $theme->screenshot = serialize($temp);
            //         if(file_exists($theme->preview)){
            //             $img = Image::make($theme->preview)->stream()->__toString();
            //             $aws->put($theme->preview, $img,'public');
            //             // $theme->preview = AmazoneBucket::url() .'/'. $theme->preview;
            //             // $save = true;
            //         }
            //         // if($save){
            //         //     $theme->save();
            //         // }
            //     }
            // }

            // $emailSentDockets = EmailSentDocket::all();
            // foreach($emailSentDockets as $emailSentDocket){
            //     if($emailSentDocket->company_logo){
            //         if(file_exists($emailSentDocket->company_logo)){
            //             $img = Image::make($emailSentDocket->company_logo)->stream()->__toString();
            //             $aws->put($emailSentDocket->company_logo, $img,'public');
            //             // $emailSentDocket->company_logo = AmazoneBucket::url() .'/'. $emailSentDocket->company_logo;
            //             // $emailSentDocket->save();
            //         }
            //     }
            // }

            // $emailSentDocketImageValues = EmailSentDocketImageValue::all();
            // foreach($emailSentDocketImageValues as $emailSentDocketImageValue){
            //     if($emailSentDocketImageValue->value){
            //         if(file_exists($emailSentDocketImageValue->value)){
            //             $img = Image::make($emailSentDocketImageValue->value)->stream()->__toString();
            //             $aws->put($emailSentDocketImageValue->value, $img,'public');
            //             // $emailSentDocketImageValue->value = AmazoneBucket::url() .'/'. $emailSentDocketImageValue->value;
            //             // $emailSentDocketImageValue->save();
            //         }
            //     }
            // }

            // $docketFieldGridValues = DocketFieldGridValue::all();
            // foreach($docketFieldGridValues as $docketFieldGridValue){
            //     if($docketFieldGridValue->value){
            //         $save = false;
            //         $checkSerialized = @unserialize($docketFieldGridValue->value);
            //         if($checkSerialized !== false){
            //             if(unserialize($docketFieldGridValue->value)){
            //                 // $valueUrlArray =[];
            //                 foreach(unserialize($docketFieldGridValue->value) as $value){
            //                     if(!is_array($value)){
            //                         if(file_exists($value)){
            //                             $img = Image::make($value)->stream()->__toString();
            //                             $aws->put($value, $img,'public');
            //                             // $valueUrlArray[] = AmazoneBucket::url() .'/'. $value;
            //                             // $save = true;
            //                         }
            //                     }
            //                     if(is_array($value)){
            //                         if(isset($value['image'])){
            //                             // $temp = [];
            //                             // $valueUrl = '';
            //                             if(file_exists($value['image'])){
            //                                 $img = Image::make($value['image'])->stream()->__toString();
            //                                 $aws->put($value['image'], $img,'public');
            //                                 // $valueUrl = AmazoneBucket::url() .'/'. $value['image'];
            //                                 // $save = true;
            //                             }
            //                             // $temp['image'] = $valueUrl;
            //                             // $temp['name'] = $value['name'];
            //                             // array_push($valueUrlArray,$temp);
            //                         }
            //                         // if($save){
            //                         //     $docketFieldGridValue->value = $valueUrlArray;
            //                         // }
            //                     }
            //                 }
            //             }
            //         }
            //         // if($save){
            //         //     $docketFieldGridValue->value = serialize($valueUrlArray);
            //         //     $docketFieldGridValue->save();
            //         // }
            //     }
            // }


            // $emailSentInvoices = EmailSentInvoice::all();
            // foreach($emailSentInvoices as $emailSentInvoice){
            //     if($emailSentInvoice->company_logo){
            //         if(file_exists($emailSentInvoice->company_logo)){
            //             $img = Image::make($emailSentInvoice->company_logo)->stream()->__toString();
            //             $aws->put($emailSentInvoice->company_logo, $img,'public');
            //             // $emailSentInvoice->company_logo = AmazoneBucket::url() .'/'. $emailSentInvoice->company_logo;
            //             // $emailSentInvoice->save();
            //         }
            //     }
            // }

            // $emailSentInvoiceImages = EmailSentInvoiceImage::all();
            // foreach($emailSentInvoiceImages as $emailSentInvoiceImage){
            //     if($emailSentInvoiceImage->value){
            //         if(file_exists($emailSentInvoiceImage->value)){
            //             $img = Image::make($emailSentInvoiceImage->value)->stream()->__toString();
            //             $aws->put($emailSentInvoiceImage->value, $img,'public');
            //             // $emailSentInvoiceImage->value = AmazoneBucket::url() .'/'. $emailSentInvoiceImage->value;
            //             // $emailSentInvoiceImage->save();
            //         }
            //     }
            // }

            // $invoice_Labels = Invoice_Label::all();
            // foreach($invoice_Labels as $invoice_Label){
            //     if($invoice_Label->icon){
            //         if(file_exists($invoice_Label->icon)){
            //             $img = Image::make($invoice_Label->icon)->stream()->__toString();
            //             $aws->put($invoice_Label->icon, $img,'public');
            //             // $invoice_Label->icon = AmazoneBucket::url() .'/'. $invoice_Label->icon;
            //             // $invoice_Label->save();
            //         }
            //     }
            // }


            // $sendDocketImageValues = SendDocketImageValue::all();
            // foreach($sendDocketImageValues as $sendDocketImageValue){
            //     if($sendDocketImageValue->value){
            //         if(file_exists($sendDocketImageValue->value)){
            //             $img = Image::make($sendDocketImageValue->value)->stream()->__toString();
            //             $aws->put($sendDocketImageValue->value, $img,'public');
            //             // $sendDocketImageValue->value = AmazoneBucket::url() .'/'. $sendDocketImageValue->value;
            //             // $sendDocketImageValue->save();
            //         }
            //     }
            // }

            // $sentDocValYesNoValues = SentDocValYesNoValue::all();
            // foreach($sentDocValYesNoValues as $sentDocValYesNoValue){
            //     if($sentDocValYesNoValue->value){
            //         $save = false;
            //         $checkSerialized = @unserialize($sentDocValYesNoValue->value);
            //         if($checkSerialized !== false){
            //             if(unserialize($sentDocValYesNoValue->value)){
            //                 $valueUrlArray =[];
            //                 foreach(unserialize($sentDocValYesNoValue->value) as $value){
            //                     if(file_exists($value)){
            //                         $img = Image::make($value)->stream()->__toString();
            //                         $aws->put($value, $img,'public');
            //                         // $valueUrlArray[] = AmazoneBucket::url() .'/'. $value;
            //                         // $save = true;
            //                         // $sentDocValYesNoValue->save();
            //                     }
            //                 }
            //                 // if($save){
            //                 //     $sentDocValYesNoValue->value = serialize($valueUrlArray);
            //                 //     $sentDocValYesNoValue->save();
            //                 // }
            //             }
            //         }
            //     }
            // }

            // $sentEmailAttachments = SentEmailAttachment::all();
            // foreach($sentEmailAttachments as $sentEmailAttachment){
            //     if($sentEmailAttachment->url){
            //         if(file_exists($sentEmailAttachment->url)){
            //             // $img = Image::make($sentEmailAttachment->url)->stream()->__toString();
            //             $aws->put($sentEmailAttachment->url, file_get_contents($sentEmailAttachment->url),'public');
            //             // $sentEmailAttachment->url = AmazoneBucket::url() .'/'. $sentEmailAttachment->url;
            //             // $sentEmailAttachment->save();
            //         }
            //     }
            // }

            // $sentEmailDocValYesNoValues = SentEmailDocValYesNoValue::all();
            // foreach($sentEmailDocValYesNoValues as $sentEmailDocValYesNoValue){
            //     if($sentEmailDocValYesNoValue->value){
            //         $save = false;
            //         $checkSerialized = @unserialize($sentEmailDocValYesNoValue->value);
            //         if($checkSerialized !== false){
            //             if(unserialize($sentEmailDocValYesNoValue->value)){
            //                 $valueUrlArray =[];
            //                 foreach(unserialize($sentEmailDocValYesNoValue->value) as $value){
            //                     if(file_exists($value)){
            //                         $img = Image::make($value)->stream()->__toString();
            //                         $aws->put($value, $img,'public');
            //                         // $valueUrlArray[] = AmazoneBucket::url() .'/'. $value;
            //                         // $save = true;
            //                         // $sentEmailDocValYesNoValue->save();
            //                     }
            //                 }
            //                 // if($save){
            //                 //     $sentEmailDocValYesNoValue->value = serialize($valueUrlArray);
            //                 //     $sentEmailDocValYesNoValue->save();
            //                 // }
            //             }
            //         }
            //     }
            // }

            // $sentInvoices = SentInvoice::all();
            // foreach($sentInvoices as $sentInvoice){
            //     if($sentInvoice->company_logo){
            //         if(file_exists($sentInvoice->company_logo)){
            //             $img = Image::make($sentInvoice->company_logo)->stream()->__toString();
            //             $aws->put($sentInvoice->company_logo, $img,'public');
            //             // $sentInvoice->company_logo = AmazoneBucket::url() .'/'. $sentInvoice->company_logo;
            //             // $sentInvoice->save();
            //         }
            //     }
            // }

            // $sentInvoiceImageValues = SentInvoiceImageValue::all();
            // foreach($sentInvoiceImageValues as $sentInvoiceImageValue){
            //     if($sentInvoiceImageValue->value){
            //         if(file_exists($sentInvoiceImageValue->value)){
            //             $img = Image::make($sentInvoiceImageValue->value)->stream()->__toString();
            //             $aws->put($sentInvoiceImageValue->value, $img,'public');
            //             // $sentInvoiceImageValue->value = AmazoneBucket::url() .'/'. $sentInvoiceImageValue->value;
            //             // $sentInvoiceImageValue->save();
            //         }
            //     }
            // }

            // $timerImages = TimerImage::all();
            // foreach($timerImages as $timerImage){
            //     if($timerImage->image){
            //         if(file_exists($timerImage->image)){
            //             $img = Image::make($timerImage->image)->stream()->__toString();
            //             $aws->put($timerImage->image, $img,'public');
            //             // $timerImage->image = AmazoneBucket::url() .'/'. $timerImage->image;
            //             // $timerImage->save();
            //         }
            //     }
            // }

            // $users = User::all();
            // foreach($users as $user){
            //     if($user->image){
            //         if(file_exists($user->image)){
            //             $img = Image::make($user->image)->stream()->__toString();
            //             $aws->put($user->image, $img,'public');
            //             // $user->image = AmazoneBucket::url() .'/'. $user->image;
            //             // $user->save();
            //         }
            //     }
            // }

            // $yesNoFields = YesNoFields::all();
            // foreach($yesNoFields as $yesNoField){
            //     if($yesNoField->icon_image){
            //         if(file_exists($yesNoField->icon_image)){
            //             $img = Image::make($yesNoField->icon_image)->stream()->__toString();
            //             $aws->put($yesNoField->icon_image, $img,'public');
            //             // $yesNoField->icon_image = AmazoneBucket::url() .'/'. $yesNoField->icon_image;
            //             // $yesNoField->save();
            //         }
            //     }
            // }
            
            DB::commit();
            dd('hit');
        }catch(\Exception $ex){
            DB::rollback();
            dd($ex);
        }



        //companyTable Migration
//        $company = Company::get();
//         foreach ($company as $companys){
//             $arrayData = explode('/',$companys->logo);
//             $fileName = end($arrayData);
//
//         }




    //    $image = $request->file;
    //    $imageFileName = time() . '.' . $image->getClientOriginalExtension();
    //    $filePath = '/images/' . $imageFileName;
    //    $aws->put($filePath, file_get_contents($image), 'public');
    //    return back()->with('success', 'Image Successfully Saved');





////        dataBasebackup and autoremove 15days old data
//        $today = Carbon::now()->format('Y-m-d-h:i:s');
//        $directory = $aws->allFiles('Backup');
//        foreach ($directory as $items){
//            $str_arr = explode ("/", $items);
//            $data = explode('.',$str_arr[1])[0];
//            $now = Carbon::now();
//            if($now->subDays(1)->format('Y-m-d') >  Carbon::createFromFormat('Y-m-d-h:i:s',$data)->format('Y-m-d') ){
//                $aws->delete($items);
//            }
//        }
//
//        $image = $request->file;
//        $filePath = '/Backup/' .$today.".sql";
//        $aws->put($filePath, file_get_contents($image), 'public');
//        return back()->with('success', 'Image Successfully Saved');


    }


}


