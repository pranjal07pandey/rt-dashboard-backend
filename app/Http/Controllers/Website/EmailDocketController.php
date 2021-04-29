<?php

namespace App\Http\Controllers\Website;

use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentDocketValue;
use App\UserNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use App\SentDcoketTimerAttachment;
use PDF;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Helpers\V2\AmazoneBucket;
class EmailDocketController extends Controller
{
    public function view($id,$recipient){
        try {
            $emailDocket    =   EmailSentDocket::findOrFail(Crypt::decrypt($id));
            $emailRecipient     =   EmailSentDocketRecipient::findOrFail(Crypt::decrypt($recipient));
        }
        catch(DecryptException $e){
            $message    =   "Invalid request. Pleae check your url and try again.";
            return view('website.emailDocket.approved', compact('message'));
        }
        return view('website.emailDocket.view',compact('emailDocket','id','emailRecipient'));
    }

    public function showCopyEmailDocketView($id){
            $emailDocket    =   EmailSentDocket::findOrFail(Crypt::decrypt($id));
            $emailRecipient     =   null;

        return view('website.emailDocket.view',compact('emailDocket','id','emailRecipient'));
    }

    public function download($id){
        ini_set('memory_limit','256M');
        set_time_limit(0);

        $sentDocket    =   EmailSentDocket::findOrFail(Crypt::decrypt($id));
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
        $isFromBackend  =   true;
        $pdf = PDF::loadView('pdfTemplate.emailedDocketForward',compact('sentDocket','docketFields','docketTimer','approval_type','isFromBackend'))->setPaper('a4','landscape')->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
        $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
        $fileName=preg_replace('/\s+/', '', $sentDocket->docketInfo->title."".$sentDocket->id);
        return $pdf->download($fileName.'.pdf');
    }

    public function approve(Request $request, $id,$hashKey){
        $id =   Crypt::decrypt($id);
        $emailDocket        =   EmailSentDocket::findOrFail($id);
        $emailRecipient     =   EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('hashKey',$hashKey)->get();

        if($emailRecipient->count()!=1){
            $message    =   "Your link has expired.";
            return view('errors.errorPage', compact('message'));
        }
        $emailRecipient =   $emailRecipient->first();
        if($emailDocket->docketApprovalType == 0){
            if($emailRecipient->hashKey!='' && $emailRecipient->status!='1'){
                $emailRecipient->hashKey = '';
                $emailRecipient->status     =   1;
                $emailRecipient->approval_time =Carbon::now()->toDateTimeString();
                $emailRecipient->save();

                if(EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('hashKey','!=','')->count()==0){
                    EmailSentDocket::where('id',$id)->update(['status'=>1]);
                }

                if($emailDocket->senderUserInfo->device_type == 2){
                    sendiOSNotification($emailDocket->senderUserInfo->deviceToken,"Docket Approved", $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }else if($emailDocket->senderUserInfo->device_type == 1){
                    sendAndroidNotification($emailDocket->senderUserInfo->deviceToken,'Docket Approved', $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =   $emailRecipient->email_user_id;
                $userNotification->receiver_user_id =   $emailDocket->user_id;
                $userNotification->type     =   5;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $emailRecipient->emailUserInfo->email." has approved your docket.";
                $userNotification->key      =   $id;
                $userNotification->status   =   0;
                $userNotification->save();

                $message    =   "Requested docket has been approved successfully.";
                return view('website.emailDocket.approved', compact('message','emailDocket'));
            }else {
                $message    =   "Your link has expired.";
                return view('errors.errorPage', compact('message'));
            }
        }else{
            if($emailRecipient->hashKey!='' && $emailRecipient->status!='1'){
                $emailRecipient->hashKey = '';
                $emailRecipient->status     =   1;
                $emailRecipient->approval_time =Carbon::now()->toDateTimeString();
                $emailRecipient->name =$request->name;
                $image = $request->signature;
                $image = str_replace('data:image/png;base64,', '', $image);
                $image = str_replace(' ', '+', $image);
                $imageName = 'files/docket/images/signature'.time().'.'.'png';
                \File::put(base_path(''). '/' . $imageName, base64_decode($image));
                $emailRecipient->signature=$imageName;
                $emailRecipient->save();

                if(EmailSentDocketRecipient::where('email_sent_docket_id',$id)->where('approval',1)->where('hashKey','!=','')->count()==0){
                    EmailSentDocket::where('id',$id)->update(['status'=>1]);
                }

                if($emailDocket->senderUserInfo->device_type == 2){
                    sendiOSNotification($emailDocket->senderUserInfo->deviceToken,"Docket Approved", $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }else if($emailDocket->senderUserInfo->device_type == 1){
                    sendAndroidNotification($emailDocket->senderUserInfo->deviceToken,'Docket Approved', $emailRecipient->emailUserInfo->email." has approved your docket",array('type'=>5));
                }

                $userNotification   =    new UserNotification();
                $userNotification->sender_user_id   =   $emailRecipient->email_user_id;
                $userNotification->receiver_user_id =   $emailDocket->user_id;
                $userNotification->type     =   5;
                $userNotification->title    =   'Docket Approved';
                $userNotification->message  =   $emailRecipient->emailUserInfo->email." has approved your docket.";
                $userNotification->key      =   $id;
                $userNotification->status   =   0;
                $userNotification->save();
            }else{
            }
        }
    }

    public function approved(){
        $message    =   "Requested docket has been approved successfully.";
        return view('website.emailDocket.approved', compact('message'));
    }
}
