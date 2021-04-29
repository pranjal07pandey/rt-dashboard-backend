<?php

namespace App\Http\Controllers;

use App\EmailSentDocket;
use App\EmailSentDocketRecipient;
use App\EmailSentInvoice;
use App\Mail\EmailDocket;
use App\Mail\EmailInvoice;
use App\Mail\GeneralEmail;
use App\Notifications\Signup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Notification;
use Illuminate\Support\Facades\Mail;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return redirect('login');
    }

    public function  email(){
//$user   =   User::find(18);
//        $user->slackChannel('test-notification')->notify(new Signup());
//dd( $user->slackChannel('test-notification')->notify(new Signup()));
        $emailSentInvoice   =   EmailSentInvoice::find(259);
        Mail::to('bdryagya@gmail.com')->send(new EmailInvoice($emailSentInvoice, $emailSentInvoice->receiverInfo, ""));
//        Mail::to("gordon.gayford@wgtp.com.au")->send(new GeneralEmail());
        die("test");

//        $emailSentDocket     =   EmailSentDocket::find(854);
//        $receiverQuery = EmailSent  DocketRecipient::where('email_sent_docket_id', $emailSentDocket->id)->get();
//        foreach ($receiverQuery as $receiverInfo) {
//            Mail::to("test-x7cml2d62@mail-tester.com")
//                ->send(new EmailDocket($emailSentDocket,$receiverInfo));
//        }
//        die("test");
//        Mail::raw('Text Email', function ($message){
//            $message->to('u5400344@anu.edu.au');
//        });

////        $sentDocket
//        $emailSubject   =   "Docket";
//        $sentDocket     =   EmailSentDocket::find(854);
//        $data['emailDocket']    =   $sentDocket;
////        $data['recipient']      =   $receiverInfo;
//        Mail::send('emails.V2.docket.test', $data, function ($message) use ($emailSubject, $sentDocket) {
//            $message->from("info@recordtimeapp.com.au", $sentDocket->sender_name. " via Record Time");
//            $message->replyTo($sentDocket->senderUserInfo->email, $sentDocket->sender_name);
//            $message->to("mail@dileep.com.np")->subject(($emailSubject == "") ? "You’ve got a docket" : $emailSubject);
//        });
//
//        Mail::send('emails.V2.docket.emailDocket', $data, function ($message) use ($emailSubject, $sentDocket,$receiverInfo) {
//            $message->from("info@recordtimeapp.com.au", $sentDocket->sender_name. " via Record Time");
//            $message->replyTo($sentDocket->senderUserInfo->email, $sentDocket->sender_name);
//            $message->to($receiverInfo->emailUserInfo->email)->subject(($emailSubject == "") ? "You’ve got a docket" : $emailSubject);
//        });
    }
}
