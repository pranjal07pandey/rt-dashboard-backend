<?php

use App\AppleSubscription;
use Stripe\Stripe;
use App\CompanySubscription;
use Stripe\Subscription;
use Carbon\Carbon;
use Stripe\Customer;
use Illuminate\Support\Facades\Session;
use App\Company;
use App\SubscriptionPlan;
use App\Employee;
use App\User;
use App\SentDockets;
use App\EmailSentDocket;
use App\SubscriptionLog;
use Illuminate\Support\Facades\Redirect;

function checkSubscription(){
    //check local trial period
    $company    =    Company::where('id',Session::get('company_id'))->first();
    if($company->trial_period== 4){
        $appleSubscriptions = AppleSubscription::where('company_id',Session::get('company_id'))->get()->last();
        $timestamp = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($appleSubscriptions->expiry_date)->format('Y-m-d H:i:s'), 'UTC');
        $timestamp->setTimezone('Australia/Canberra');
        $now = Carbon::now();
        if ($timestamp->lt($now)){
            Company::where('user_id',Auth::user()->id)->update(['trial_period'=>3]);
        }
    }
    if($company->trial_period==0){
        Session::flash('message',array('Please subscribe to one of our subscription plans to continue.','warning',''));
        return "noSubscription";
    }
    else if($company->trial_period==3) {
        //check if subscription was free, count remaining docket left

        //get last subscription created date
        $subscriptionLogQuery    =   SubscriptionLog::where('company_id',Session::get('company_id'));
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
        $totalRemaining =   0;
        if($totalMonthDockets > 5){
            $totalRemaining  =   0;
        }else{
            $totalRemaining =   5-$totalMonthDockets;
        }
        Session::flash('docketLimit',array('Remaining dockets: '.$totalRemaining,'',''));

        //deactivate all employee
        deactivateAllEmployee(Session::get('company_id'));
        Session::flash('message',array('Please upgrade your subscription. Your active subscription has an ability to send a maximum of 5 dockets per month.','danger',route('Company.Subscription.Upgrade'),'Click here to upgrade !'));
        return "freeSubscription";
    }
    else if($company->trial_period==1){
        $now = Carbon::now();
        $currentPeriodEnd =   Carbon::parse($company['expiry_date']);
        //get card details
        setStripeKey();

        $companyStripeCustomer  =   $company->stripe_user;
        $stripeCustomer =   Customer::retrieve($companyStripeCustomer);
        $cards  =   @$stripeCustomer->sources->all(array('limit'=>3, 'object' => 'card'));
        if($now->gte($currentPeriodEnd)){
            if(count($cards["data"])>0){
                try{
                     $subscription = Subscription::create(['customer' => $company->stripe_user,'items' => [['plan' => SubscriptionPlan::where('id',$company->subscription_plan_id)->first()->plan_id]]]);
                } catch(Exception $e){
                    if($e->stripeCode=="card_declined"){
                        return "card_declined";
                    }
                }

                CompanySubscription::where('company_id',$company->id)->delete();

                $companySubscription = new CompanySubscription();
                $companySubscription->company_id = $company->id;
                $companySubscription->subscription_plan_id = $subscription->id;
                $companySubscription->max_user = SubscriptionPlan::where('id',$company->subscription_plan_id)->first()->max_user;
                $companySubscription->isCancel  =   0;
                $companySubscription->save();

                $company->trial_period  =   2;
                $company->max_user      =   SubscriptionPlan::where('id',$company->subscription_plan_id)->first()->max_user;
                $company->save();

                deactivateAllEmployee(Session::get('company_id'));
            }else{
                $company->max_user  =   1;
                $company->subscription_plan_id  =   SubscriptionPlan::first()->id;
                $company->trial_period  =   3;
                $company->save();

                $subscriptionLog    =    new SubscriptionLog();
                $subscriptionLog->company_id    =    Session::get('company_id');
                $subscriptionLog->type          =   3;
                $subscriptionLog->save();

                //deactivate all employee
                deactivateAllEmployee(Session::get('company_id'));

                Session::flash('message',array('Please upgrade your subscription. Your trial period has expired and your account has been downgraded to a single/free user plan with an ability to send a maximum of 5 dockets per month.','danger',route('Company.Subscription.Upgrade'),'Click here to upgrade !'));
                return "no_stripe_subscription";
            }
        }else{
            $expiryText = $currentPeriodEnd->diffInDays($now);
            if ($expiryText == 0) {
                $expiryText = $currentPeriodEnd->diffInHours($now) . " hours";
                if ($expiryText == 0) {
                    $expiryText = $currentPeriodEnd->diffInMinutes($now) . " minutes";
                }
            } else {
                if($expiryText==1){
                    $expiryText = $expiryText . " day";
                }else{
                    $expiryText = $expiryText . " days";
                }
            }

            //check credit card details available or not
            if(count($cards["data"])>0){

            }else{
                Session::flash('message',array("Your trial period will expire in " . $expiryText . ". Please update your credit card details to avoid interruptions after the trial ends.",'warning',route('Company.CreditCard.Update'),'Add Credit Card Details'));
            }
        }
        return "trialing";
    }
    else if($company->trial_period==5){
        if(CompanySubscription::where('company_id',Session::get('company_id'))->count()==1) {
            $companySubscription = CompanySubscription::where('company_id', Session::get('company_id'))->first();
            if($companySubscription->stripe_cancel==1) {
                Session::flash('message', array('Due to too many failed payments, your subscription has been cancelled. Please check \'billing history\' to update your payment details and to subscribe or update your plan.', 'warning', ''));
                return "subscriptionCancel";
            }
        }
        flash('Please subscribe our one of the subscription plan.','warning');
        return "subscriptionCancel";
    }
    else if(CompanySubscription::where('company_id',Session::get('company_id'))->count()==1){
        $companySubscription    =   CompanySubscription::where('company_id',Session::get('company_id'))->first();
        if($companySubscription->isCancel==1){
            flash('Please subscribe our one of the subscription plan.','warning');
            $company->max_user  =   1;
            $company->trial_period  =   5;
            $company->save();
            $companySubscription->max_user  =   1;
            $companySubscription->save();
            deactivateAllEmployee(Session::get('company_id'));
            return "subscriptionCancel";
        }
        else{
            setStripeKey();
            $subscriptionPlan = Subscription::retrieve($companySubscription->subscription_plan_id);
            if($subscriptionPlan['status']=="past_due") {
                flash('Something went wrong please check your <a href="https://www.recordtimeapp.com.au/backend/dashboard/company/profile/billingHistory">Billing History.</a>', "danger");
                $subLastDueDate     =     Carbon::createFromTimestamp($subscriptionPlan['current_period_start'])->addDays(7);
                $now    =   Carbon::now();
                if($now->gte($subLastDueDate)){
                    deactivateAllEmployee(Session::get('company_id'));
                    return "card_declined";
                }
                return "past_due";
            }else if($subscriptionPlan['status']=="active"){
                return "active";
            }else if($subscriptionPlan['status']=="canceled"){
                $company->max_user  =   1;
                $company->trial_period  =   5;
                $company->save();
                $companySubscription->max_user  =   1;
                $companySubscription->isCancel  =   1;
                $companySubscription->stripe_cancel    =  1;
                $companySubscription->save();
                deactivateAllEmployee(Session::get('company_id'));
                return "subscriptionCancel";
            }
        }
    }else{

    }

   flash('Please subscribe our one of the subscription plan.','warning');
   return true;
}

function checkProfileComplete(){
    //check user profile name , company name, company abn, contact number and address
    $user   =    Auth::user();
    $company    =    Company::find(Session::get('company_id'));
    if($user->first_name!='' && $user->last_name!='' && $company->name!='' && $company->abn!='' && $company->contactNumber!='' && $company->address!=''){
        return true;
    }else{
        Session::flash('message',array('Please complete your profile, your information will be used when creating dockets and invoices for your company','warning',''));
        return false;
    }
}

function deactivateAllEmployee($companyId){
    //deactivate all employee
//    $employees  =   Employee::where('company_id',$companyId)->pluck('user_id')->toArray();
//    User::whereIn('id',$employees)->update(['isActive'=>0,'hashToken' =>'','deviceToken' => '','device_type' => '0']);
}

function getCompanyId(){
    $companyId  =   0;
    if(Employee::where('user_id', Auth::user()->id)->count()!=0):
        $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
    else :
        $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
    endif;
    return $companyId;
}

function sendiOSNotification($deviceID, $titles, $message,$data){
    $ch = curl_init("https://fcm.googleapis.com/fcm/send");
    //The device token.
    $token = $deviceID; //token here
    //Title of the Notification.
    $title = $titles;
    //Body of the Notification.
    $body = $message;
    //Creating the notification array.
    $notification = array('title' =>$title , 'body' => $body, 'sound'=>'default', "content_available"=>true,'data' => $data);
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

function sendAndroidNotification($deviceId, $titles, $message,$data){
    $registrationIds = array( $deviceId );
    $msg = array
    (
        'message'   => $message,
        'title'     =>$titles,
        'vibrate'   => 1,
        'sound'     => 1,
        'data' => $data
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

function setStripeKey(){
    Stripe::setApiKey('sk_test_szvLCnJFMLsOO6M5TCDwRFIW');
}

function xero(){
    $config     =  ['oauth' => [
        'callback'        => "https://recordtime-backend.test/dashboard/company/xero/connectionCallBack",
//        'callback' => "http://dev.recordtimeapp.com.au/RecordTime-Backend/dashboard/company/xero/connectionCallBack",
        'consumer_key'    => 'T8IDOYYG0SPKPRVLQLVAVCVIOKDWCB',
        'consumer_secret' => 'KCCYNJ9DMWAK2CZRN5AKHET8LINPAM',
        'signature_location'  => \XeroPHP\Remote\OAuth\Client::SIGN_LOCATION_QUERY,
        'payroll_version' => '1.0',

    ],
        'curl' => [
            CURLOPT_USERAGENT   => 'Record Time App'
        ],
    ];
    $xero = new \XeroPHP\Application\PublicApplication($config);
    return $xero;

}

function convertHrsMin($parameter) {
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



