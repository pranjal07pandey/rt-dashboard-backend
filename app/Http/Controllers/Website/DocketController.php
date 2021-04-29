<?php

namespace App\Http\Controllers\Website;

use App\DocketField;
use App\DocketFiledPreFiller;
use App\SentDocketReject;
use App\SentDockets;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\Support\Collection;
use App\User;
use App\Employee;
use App\Company;
use PDF;
use App\SentDocketRecipientApproval;
use Carbon\Carbon;
use App\UserNotification;
use App\Helpers\V2\AmazoneBucket;
class DocketController extends Controller
{

    //default prefiller value
    function array_values_recursive($ary){
        $lst = array();
        foreach( array_keys($ary) as $k ){
            $v = $ary[$k];
            if (is_scalar($v)) { $lst[] = $v;}
            elseif (is_array($v)) {
                $lst = array_merge( $lst, $this->array_values_recursive($v));
            }
        }
        return $lst;
    }

    public function prefiller(){
        $docketField = DocketField::find('4109');   //change id
        $defaultPrefillerValue = unserialize($docketField->default_prefiller_id);
        $parentPrefillers = DocketFiledPreFiller::select('root_id')->whereIn('id', $defaultPrefillerValue)->groupBy('root_id')->get();
        $prefillerArray =    array();
        foreach ($parentPrefillers as $prefiller) {
            $parentArray= $this->getParentData($prefiller->root_id);
            $value = $this->array_values_recursive($parentArray);

            $defaultValue   =   DocketFiledPreFiller::where('root_id',$prefiller->root_id)->whereIn('id',$defaultPrefillerValue)->pluck('value')->toArray();
            $prefillerArray[] = implode("-",array_reverse($value)).'-'.implode(',',$defaultValue);
        }
    }

    public function getParentData($data){
        $docketPrefillerValues = DocketFiledPreFiller::where('id',$data)->select('id','root_id','value')->get();
        $child =array();
        if (count($docketPrefillerValues)!=0){
            foreach ($docketPrefillerValues as $datass){
                $child[]    = $datass['value'];
                $child[]= $this->getparentData($datass->root_id);
            }
        }
        return $child;
    }
    //default prefiller value

    public function view($id,$recipient){
        $sentDocket    =   SentDockets::findOrFail(Crypt::decrypt($id));
        $recipient     =   User::findOrFail(Crypt::decrypt($recipient));
        if(in_array($recipient->id,$sentDocket->validUsers()->pluck('id')->toArray())){
            return view('website.docket.view',compact('sentDocket','id','recipient'));
        }else{
            return response()->view('errors.404', [], 400);
        }
    }

    public function showCopyDocketView($id){
        $sentDocket    =   SentDockets::findOrFail(Crypt::decrypt($id));
        $recipient   = null;
        return view('website.docket.view',compact('sentDocket','id','recipient'));
    }

    public function download($id){
        ini_set('memory_limit', '256M');
        set_time_limit(0);
        $id =   Crypt::decrypt($id);
        $sentDocket = SentDockets::findOrFail($id);

        $document_name = "docket-" . $id . "-" . preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($sentDocket->company_name)));
        $document_path = 'files/pdf/docketForward/' . str_replace('.', '', $document_name) . '.pdf';
        $approval_type = array();
        foreach ($sentDocket->sentDocketRecipientApproval as $items) {
            $approval_type[] = array(
                'id' => $items->id,
                'status' => $items->status,
                'full_name' => $items->userInfo->first_name . " " . $items->userInfo->last_name,
                'approval_time' => $items->approval_time,
                'name' => $items->name,
                'signature' => $items->signature)
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

        $recipientIds   =   $sentDocket->recipientInfo->pluck('user_id')->toArray();
        $companyEmployeeQuery   =    Employee::whereIn('user_id',$recipientIds)->pluck('company_id')->toArray();
        $empCompany    =    Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
        $adminCompanyQuery   =    Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
        $company    =   Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
        $docketFields   =   $sentDocket->sentDocketValue;

        $docketTimer = $sentDocket->attachedTimer();
        $pdf = PDF::loadView('pdfTemplate.docketForward', compact('sentDocket','company','docketFields','docketTimer','approval_type','receiverDetail'));
        foreach ($docketFields as $row){
            if($row->docketFieldInfo->docket_field_category_id==22){
                $pdf->setPaper('a4','landscape');
            }
        }

        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $fileName=preg_replace('/\s+/', '-', $sentDocket->docketInfo->title."-".$sentDocket->id);
        return $pdf->download($fileName.'.pdf');
    }

    public function reject(Request $request, $id){
        $this->validate($request,['user_id' => 'required','explanation'=>'required']);

        $sentDocket    =   SentDockets::findOrFail(Crypt::decrypt($id));
        $user_id        =   Crypt::decrypt($request->user_id);
        User::findOrFail($user_id);
        $sentDocketRecipientApprovalQuery = SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->Where('user_id', $user_id)->where('status', 0);
        if($sentDocketRecipientApprovalQuery->count()){
            $sentDocketRecipientApproval    =   $sentDocketRecipientApprovalQuery->first();
            $sentDocketRecipientApproval->status     =   3;
            $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
            if ($sentDocketRecipientApproval->save()){
                $sentDocketExplanation = new SentDocketReject();
                $sentDocketExplanation->sent_docket_id =  $sentDocket->id;
                $sentDocketExplanation->explanation =  $request->explanation;
                $sentDocketExplanation->user_id =  $user_id;
                $sentDocketExplanation->save();
            }
            SentDockets::where('id',$sentDocket->id)->update(['status'=> 3]);

            // push notification
            $sentDocketRecipientApp= SentDocketRecipientApproval::where('sent_docket_id', $sentDocket->id)->pluck('user_id')->toArray();
            if (in_array($user_id, $sentDocketRecipientApp)){
                $user = User::find($user_id);
                $companyAdminUser = Company::where('id',$user->company()->id)->first()->userInfo;

                $userNotification   =   new UserNotification();
                $userNotification->sender_user_id   =   $user_id;
                $userNotification->receiver_user_id = $sentDocket->user_id;
                $userNotification->type     =   3;
                $userNotification->title    =   'Docket Rejected';
                $userNotification->message  =   "Your Docket has been rejected";
                $userNotification->key      =   $sentDocket->id;
                $userNotification->status   =   0;

                if ($userNotification->save()) {
                    if ($sentDocket->senderUserInfo->deviceToken != "") {
                        if ($sentDocket->senderUserInfo->device_type == 2) {
                            $this->sendiOSNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
                        }
                        if ($sentDocket->senderUserInfo->device_type == 1) {
                            $this->sendAndroidNotification($sentDocket->senderUserInfo->deviceToken, $userNotification->title, $userNotification->message);
                        }
                    }
                }

                if ($sentDocket->user_id != $companyAdminUser->id){
                    $userNotification   =   new UserNotification();
                    $userNotification->sender_user_id   =   $user_id;
                    $userNotification->receiver_user_id = $companyAdminUser->id;
                    $userNotification->type     =   3;
                    $userNotification->title    =   'Docket Rejected';
                    $userNotification->message  =   "Your Docket has been rejected by ".User::where('id',$user_id)->first()->first_name.' '.User::where('id',$user_id)->first()->last_name;
                    $userNotification->key      =   $sentDocket->id;
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
            }
        }
        $message    =   "Requested docket has been rejected successfully.";
        return view('website.emailDocket.approved', compact('message'));
    }

    public function approve(Request $request, $id,$hashKey){
        $id =   Crypt::decrypt($id);
        $sentDocket        =   SentDockets::findOrFail($id);
        $recipient     =   User::findOrFail(Crypt::decrypt($hashKey));

        if($sentDocket->sentDocketRecipientApproval) {
            if (in_array($recipient->id, $sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray())):
                $sentDocketRecipientApprovalQuery = SentDocketRecipientApproval::where('sent_docket_id', $id)->Where('user_id',$recipient->id)->where('status', 0);
                if ($sentDocketRecipientApprovalQuery->count() == 1) {
                    if ($sentDocket->docketApprovalType == 1){

                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->name =$request->name;
                        $image = $request->signature;  // your base64 encoded
                        $image = str_replace('data:image/png;base64,', '', $image);
                        $image = str_replace(' ', '+', $image);
                        $imageName = 'files/docket/images/signature'.time().'.'.'png';
                        \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                        $sentDocketRecipientApproval->signature=$imageName;
                        $sentDocketRecipientApproval->save();
                    }else{
                        $sentDocketRecipientApproval     =   SentDocketRecipientApproval::findOrFail($sentDocketRecipientApprovalQuery->first()->id);
                        $sentDocketRecipientApproval->status     =   1;
                        $sentDocketRecipientApproval->approval_time =Carbon::now()->toDateTimeString();
                        $sentDocketRecipientApproval->save();
                    }

                    $sentDocketSenderInfo = User::where('id', $sentDocket->user_id)->first();
                    $sentDocketReceiverInfo = $recipient;

                    if (SentDocketRecipientApproval::where('sent_docket_id', $id)->where('status', 0)->count() == 0) {
                        $sentDocketUpdate = SentDockets::findOrFail($id);
                        $sentDocketUpdate->status = 1;
                        $sentDocketUpdate->save();

                        if (SentDocketRecipientApproval::where('sent_docket_id', $id)->count() > 1) {
                            if ($sentDocketSenderInfo->device_type == 2) {
                                $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                            }
                            if ($sentDocketSenderInfo->device_type == 1) {
                                $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", "Your docket " . $sentDocketUpdate->docketInfo->title . " has fully approved by all.");
                            }
                        }
                    }

                    if ($sentDocketSenderInfo->device_type == 2) {
                        $this->sendiOSNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name . " " . $sentDocketReceiverInfo->last_name . " has approved your docket.");
                    }
                    if ($sentDocketSenderInfo->device_type == 1) {
                        $this->sendAndroidNotification($sentDocketSenderInfo->deviceToken, "Docket Approved", $sentDocketReceiverInfo->first_name . " " . $sentDocketReceiverInfo->last_name . " has approved your docket.");
                    }
                }
            endif;
        }
        $message    =   "Requested docket has been approved successfully.";
        return view('website.emailDocket.approved', compact('message'));
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
        $notification = array('title' =>$title , 'text' => $body, 'sound'=>'default', "content_available"=>true);
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
}
