<?php

namespace App\Http\Controllers;

use App\AssignedDocket;
use App\CompanyXero;
use App\Docket;
use App\DocketField;
use App\Notifications\ProfileComplete;
use App\SubscriptionLog;
use App\SubscriptionPlan;
use Illuminate\Http\Request;
use App\Company;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use App\PaymentLog;
use Laracasts\Flash\Flash;
use Stripe\Customer;
use Stripe\Error\Card;
use Stripe\Invoice;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Charge;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\CompanySubscription;
use Stripe\Subscription;
use Illuminate\Support\Facades\Crypt;
use App\User;
use Illuminate\Support\Facades\Input;
use Mail;
use App\Employee;
use PDF;
use XeroPHP\Application\PrivateApplication;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Helpers\V2\FunctionUtils;
use App\Helpers\V2\AmazoneBucket;

class CompanyProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(Session::get('company_id')==''){
                if(Employee::where('user_id', Auth::user()->id)->count()!=0):
                    $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
                    Session::put('adminType',2);
                else :
                    $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
                    Session::put('adminType',1);
                endif;
                Session::put('company_id',$companyId);
            }

            return $next($request);
        });
    }

    public function cardDeclined(){
        flash("", "danger");
        return view('dashboard.company.profile.cardDeclined');
    }

    public function canceled(){
        flash("Due to too many failed payments, your subscription has been downgraded into free user plan. Please subscribe new one or reactivate old subscription.", "danger");
        setStripeKey();
        $companyStripeCustomerQuery    =    Company::where('id', Session::get('company_id'))->first();
        if($companyStripeCustomerQuery->stripe_user) {
            $companyStripeCustomer = $companyStripeCustomerQuery->stripe_user;
            $customer = Customer::retrieve($companyStripeCustomer);
            if ($customer->sources != null) {
                $cards = $customer->sources->all(array('limit' => 3, 'object' => 'card'));
            } else {
                $cards = array();
            }

            $subscriptionPlans = SubscriptionPlan::where('type', 0)->orderBy('created_at', 'asc')->get();
            $customSubscriptionPlans = SubscriptionPlan::where('type', 1)->orderBy('created_at', 'asc')->get();
            return view('dashboard.company.profile.subscriptionCanceled', compact('cards', 'subscriptionPlans', 'subscriptionPlan', 'status', 'companyStripeCustomerQuery', 'subscriptionProduct', 'customSubscriptionPlans'));
        }else{

        }
    }

    public function stripeInvoices(){

        $company = Company::where('id', Session::get('company_id'))->first();
        Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
        $stripe_customer = Customer::retrieve($company->stripe_user);
        $invoices = Invoice::all(array('customer' => $stripe_customer->id, 'limit' => 100))->data;

        return view('dashboard.company.profile.invoices',compact('company','invoices'));
    }

    public function xeroSetting()

    {
        $xeroUserDetail = CompanyXero::where('company_id', Session::get('company_id'));


//        dd(unserialize($xeroUserDetail->first()->xero_organization_contact));
        return view('dashboard.company.profile.xero',compact('xeroUserDetail'));
    }


    public function updateXero(Request $request)
    {

        $config = [
            'oauth' => [
                'callback' => 'https://recordtime.dev/dashboard/company/xero/organisations',
                'consumer_key' => $request->consumer_key,
                'consumer_secret' => $request->consumer_secret,
                'rsa_private_key' => 'file://' .Input::file('rsa_private_key'),
            ],
        ];

        try {
            $xero = new PrivateApplication($config);
            $contacts = $xero->load('Accounting\\Organisation')->execute();
            $return = openssl_pkey_get_private(file_get_contents(Input::file('rsa_private_key')));
            if ($return === false) {
                flash('Please select .pem file', 'danger');
                return redirect()->route('Company.xero.setting');
            }else{
                if($company = CompanyXero::where('company_id', Session::get('company_id'))->where('status',1)->count()==0){
                    $xeroCompany = new CompanyXero();
                    $xeroCompany->status = 1;
                    $xeroCompany->company_id = Session::get('company_id');
                    $xeroCompany->consumer_key = $request->consumer_key;
                    $xeroCompany->consumer_secret = $request->consumer_secret;
                    $rsa_private_key = Input::file('rsa_private_key');
                    if ($request->hasFile('rsa_private_key')) {
                        if ($rsa_private_key->isValid()) {
                            // $ext = $rsa_private_key->getClientOriginalExtension();
                            // $filename = basename($request->file('rsa_private_key')->getClientOriginalName(), '.' . $request->file('rsa_private_key')->getClientOriginalExtension()) . time() . "." . $ext;
                            $dest = 'files/company';
                            // $rsa_private_key->move($dest, $filename);
                            // $xeroCompany->rsa_private_key = $dest . '/' . $filename;
                            $xeroCompany->rsa_private_key = FunctionUtils::imageUpload($dest,$rsa_private_key);
                        }
                    }
                    $xeroCompany->save();
                    flash('Xero Settings updated successfully!', 'success');
                    return redirect()->route('Company.xero.setting');
                }
            }

        } catch (\Exception $e) {
            flash('Consumer key was not recognised', 'success');
            return redirect()->route('Company.xero.setting');
        }

    }

    public function unlinkXero($id){
        $company = CompanyXero::where('id',$id)->where('company_id', Session::get('company_id'))->first();
        $company->status= 0;
        $company->update();
        flash('Xero Account unlionk successfully!', 'success');
        return redirect()->route('Company.xero.setting');


    }


    public function subscriptionSelectTrial(){
        $status     =   checkSubscription();
        if($status=="noSubscription"){
            setStripeKey();
            $companyStripeCustomerQuery = Company::where('id', Session::get('company_id'))->first();

            if (@$companyStripeCustomerQuery->stripe_user!="") {
                $companyStripeCustomer = $companyStripeCustomerQuery->stripe_user;
                $customer = Customer::retrieve($companyStripeCustomer);
                $subscriptionPlans = SubscriptionPlan::where('type',0)->orderBy('created_at', 'asc')->get();
                return view('dashboard.company.profile.subscription.select', compact('subscriptionPlans'));
            }else{
                //company admin
                $user   =   User::where('id',$companyStripeCustomerQuery->user_id)->first();

                Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
                $stripeCustomer     =   Customer::create(['email' => $user->email,
                    'description' => 'Customer for '.$user->email,
                    'metadata'  => array('companyId'=>$companyStripeCustomerQuery->id)]);
                $companyStripeCustomerQuery->stripe_user   =   $stripeCustomer->id;
                $companyStripeCustomerQuery->save();
                return redirect()->route('Company.Subscription.Select');
            }
        }else{
            return redirect()->back();
        }
    }

    //trial subscription submission
    public function updateSubscription(Request $request){
        $status     =   checkSubscription();
        $this->validate($request, ['plan' => 'required']);
        $formPlan = SubscriptionPlan::where('id', Crypt::decryptString($request->plan))->firstOrFail();

        if($status=='noSubscription'){
            $company = Company::where('id', Session::get('company_id'))->first();
            $trialEnd   =   Carbon::now()->addDay(30);

            $company->trial_period  =   1;
            $company->expiry_date   =   $trialEnd;
            $company->max_user      =   $formPlan->max_user;
            $company->subscription_plan_id  =   $formPlan->id;
            $company->save();

            $subscriptionLog    =    new SubscriptionLog();
            $subscriptionLog->company_id    =    Session::get('company_id');
            $subscriptionLog->type          =   1;
            $subscriptionLog->subscription_plan_id  =    $formPlan->id;
            $subscriptionLog->save();

            $email  =   $company->userInfo->email;
            $data['email']   =   $email;
            $data['plan']   =   $formPlan->name;
            Mail::send('emails.subscription.trialSubscriptionEmail', $data, function ($message) use($email) {
                $message->from("info@recordtimeapp.com.au","Record Time" );
                $message->to($email)->subject("Trial Subscription Notification");
                $message->replyTo("info@recordtime.com.au","Record Time");
            });

            Session::forget('message');
            return redirect('dashboard/company/profile');
        }else{
            $company    =    Company::where('id',Session::get('company_id'))->first();

            if(Carbon::now()->lte(Carbon::parse($company->expiry_date))){
                $company->trial_period  =   1;
                $company->max_user      =   $formPlan->max_user;
                $company->subscription_plan_id  =   $formPlan->id;
                $company->save();

                $subscriptionLog    =    new SubscriptionLog();
                $subscriptionLog->company_id    =    Session::get('company_id');
                $subscriptionLog->type          =   1;
                $subscriptionLog->subscription_plan_id  =    $formPlan->id;
                $subscriptionLog->save();


                $email  =   $company->userInfo->email;
                $data['email']   =   $email;
                $data['plan']   =   $formPlan->name;
                Mail::send('emails.subscription.trialContinueEmail', $data, function ($message) use($email) {
                    $message->from("info@recordtimeapp.com.au","Record Time" );
                    $message->replyTo("info@recordtime.com.au","Record Time");
                    $message->to($email)->subject("Trial Subscription Notification");
                });

                Session::forget('message');

                //deactivate all employee
                deactivateAllEmployee(Session::get('company_id'));
                flash('Your Subscription has been updated successfully. All employees are deactivated. Please activate them manually.','success');

                return redirect()->route('Company.Subscription');
            }
            return redirect()->route('Company.Subscription');
        }
    }

    public function continueSubscription(){
        $status     =   checkSubscription();
        if($status=='subscriptionCancel'){
            Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
            $companyStripeCustomerQuery = Company::where('id', Session::get('company_id'))->first();
            if ($companyStripeCustomerQuery->stripe_user) {
                $companyStripeCustomer = $companyStripeCustomerQuery->stripe_user;
                $customer = Customer::retrieve($companyStripeCustomer);
                $cards = $customer->sources->all(array('limit' => 3, 'object' => 'card'));
                $subscriptionPlans = SubscriptionPlan::where('type',0)->orderBy('created_at', 'asc')->get();

                $companySubscription = CompanySubscription::where('company_id', Session::get('company_id'))->first();
                $company    =    Company::where('id',Session::get('company_id'))->first();
                return view('dashboard.company.profile.subscription.continueSubscription', compact('cards',  'subscriptionPlans','company'));
            }
        }
        else {
            return redirect()->back();
        }
    }

    public function subscription(){
        if(!checkProfileComplete()){
            return redirect()->route('companyProfile');
        }

        $status     =   checkSubscription();
        if($status=='subscriptionCancel'){
            return redirect()->route('Company.Subscription.Continue');
        }

        if($status=="freeSubscription"){
            $company            =    Company::where('id',Session::get('company_id'))->first();
            $companySubscription    =    SubscriptionPlan::where('id', $company->subscription_plan_id)->first();
            $subscriptionPlan       =   array();
            return view('dashboard.company.profile.subscription',compact('company','subscriptionPlan','status'));
        }else {
            if ($status == "trialing" || $status == "no_stripe_subscription" || $status == "subscriptionCancel") {
                $company = Company::where('id', Session::get('company_id'))->first();
                $companySubscription = SubscriptionPlan::where('id', $company->subscription_plan_id)->first();
                $subscriptionPlan = array();
                return view('dashboard.company.profile.subscription',compact('company','subscriptionPlan','status'));
            } else {
                Session::forget('message');
                $company = Company::where('id', Session::get('company_id'))->first();
                $companyPaymentLog = PaymentLog::where('company_id', Session::get('company_id'))->get();

                setStripeKey();
                $companySubscription = CompanySubscription::where('company_id', Session::get('company_id'))->orderBy('id','desc')->first();
                $subscriptionPlan = Subscription::retrieve($companySubscription->subscription_plan_id);
            }
        }

        return view('dashboard.company.profile.subscription',compact('company','companyPaymentLog','subscriptionPlan','status'));
    }

    public function freeSubscription(Request $request){
        //check trial period value for checking initial setup or not
        $company    =    Company::where('id',Session::get('company_id'))->firstOrFail();
//        if($company->trial_period==0){
            $company->trial_period  =   3;      // 3 means default subscription(1 user with max 20 docs
            $company->max_user  =   1;
            $company->save();

            //store subscription log
            $subscriptionLog    =    new SubscriptionLog();
            $subscriptionLog->company_id    =   Session::get('company_id');
            $subscriptionLog->type  =    3;
            $subscriptionLog->save();

            //check old subscription and terminate
            $oldSubscription    =   CompanySubscription::where('company_id',Session::get('company_id'));
            if($oldSubscription->count()==1){
                $companySubscription    =   $oldSubscription->first();

                $subscriptionPlan   =   Subscription::retrieve($companySubscription->subscription_plan_id);
                $subscriptionPlan->cancel();

                $companySubscription->subscription_plan_id= "";
                $companySubscription->isCancel  =   1;
                $companySubscription->save();
                flash('Your Subscription has been downgraded successfully.','success');
            }

            //deactivate all employee
            deactivateAllEmployee(Session::get('company_id'));
            flash('Your Subscription has been downgraded successfully. All employees are deactivated. Please activate them manually.','success');

            $email  =   $company->userInfo->email;
            $data['email']   =   $email;

            Mail::send('emails.subscription.freeSubscriptionEmail', $data, function ($message) use($email) {
                $message->from("info@recordtimeapp.com.au","Record Time" );
                $message->replyTo("info@recordtime.com.au","Record Time");
                $message->to($email)->subject("Subscription Notification");
            });

            return redirect()->route('Company.Subscription');
//        }else{
//            return redirect()->back();
//        }
    }

    public function subscriptionCancel(){
        Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');


        $companySubscription    =    CompanySubscription::where('company_id',Session::get('company_id'))->first();
        $subscriptionPlan   =   Subscription::retrieve($companySubscription->subscription_plan_id);
        $subscriptionPlan->cancel();

        $companySubscription->isCancel  =   1;
        $companySubscription->save();
        deactivateAllEmployee(Session::get('company_id'));

        $company    =   Company::find(Session::get('company_id'));
        $company->max_user  =   1;
        $company->trial_period  =   5;
        $company->save();

        $companyStripeCustomer  =   $company->stripe_user;
        $stripeCustomer =   Customer::retrieve($companyStripeCustomer);
        $cards  =   @$stripeCustomer->sources->all(array('limit'=>3, 'object' => 'card'));
        if(count($cards["data"])>0) {
            $stripeCustomer::deleteSource($company->stripe_user,$cards["data"][0]->id);
        }
        $email  =   $company->userInfo->email;
        $data['email']   =   $email;
        Mail::send('emails.subscription.cancelSubscriptionEmail', $data, function ($message) use($email) {
            $message->from("info@recordtimeapp.com.au","Record Time" );
            $message->replyTo("info@recordtime.com.au","Record Time");
            $message->to($email)->subject("Subscription Notification");
        });


        flash('Your Subscription has been canceled successfully.','success');
        return redirect()->route('Company.Subscription.Upgrade');
    }

    public function upgradeSubscription(){
        $status     =   checkSubscription();
        if ($status=='canceled'){
            return redirect()->route('Company.Subscription.Canceled');
        }
        setStripeKey();

        $companyStripeCustomerQuery    =    Company::where('id', Session::get('company_id'))->first();
        if($companyStripeCustomerQuery->stripe_user) {
            $companyStripeCustomer = $companyStripeCustomerQuery->stripe_user;
            $customer = Customer::retrieve($companyStripeCustomer);
            if($customer->sources!=null){
                $cards = $customer->sources->all(array('limit'=>3, 'object' => 'card'));
            }else{
                $cards =  array();
            }


            $subscriptionPlans  =    SubscriptionPlan::where('type',0)->orderBy('created_at','asc')->get();
            $customSubscriptionPlans  =    SubscriptionPlan::where('type',1)->orderBy('created_at','asc')->get();

            if($companyStripeCustomerQuery->trial_period==1){
                $companySubscription    =   array();
                $subscriptionPlan       =   SubscriptionPlan::find($companyStripeCustomerQuery->subscription_plan_id);
            }else if($companyStripeCustomerQuery->trial_period==3) {
                $companySubscription    =   array();
                $subscriptionPlan       =   SubscriptionPlan::find($companyStripeCustomerQuery->subscription_plan_id);
            }else {
                $companySubscription    =    CompanySubscription::where('company_id', Session::get('company_id'))->first();
                if($companySubscription->isCancel==1){
                    $companySubscription    =   array();
                    $subscriptionPlan       =   SubscriptionPlan::find($companyStripeCustomerQuery->subscription_plan_id);
                }else{
                    $subscriptionPlan   =   Subscription::retrieve($companySubscription->subscription_plan_id);
                }
            }
        }
        return view('dashboard.company.profile.subscription.upgrade',compact('cards','subscriptionPlans','subscriptionPlan','status','companyStripeCustomerQuery','customSubscriptionPlans'));
    }

    public function continueSubscriptionSubmit(Request $request){
        $this->validate($request,['plan'   => 'required']);
        Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
        $formPlan   =    SubscriptionPlan::where('id',Crypt::decryptString($request->plan))->firstOrFail();
        $company    =   Company::where('id',Session::get('company_id'))->first();

        $companyStripeCustomerQuery    =    Company::where('id', Session::get('company_id'))->first();
        if($companyStripeCustomerQuery->stripe_user){
            $companyStripeCustomer  =   $companyStripeCustomerQuery->stripe_user;
            $customer   =    Customer::retrieve($companyStripeCustomer);
            $card       =   $customer->sources->all(array('limit'=>3, 'object' => 'card'));
            if(count($card['data'])>0){
                $subscription = Subscription::create(['customer' => $companyStripeCustomer,
                    'items' => [['plan' => $formPlan->plan_id]]]);


                $companySubscription    =    CompanySubscription::where('company_id',Session::get('company_id'))->first();
                $companySubscription->subscription_plan_id  =   $subscription->id;
                $companySubscription->max_user              =   $formPlan->max_user;
                $companySubscription->isCancel              =   0;
                $companySubscription->stripe_cancel         =   0;
                $companySubscription->save();

                $company    =    Company::where('id',Session::get('company_id'))->first();
                $company->max_user  =   $formPlan->max_user;
                $company->trial_period  =   2;
                $company->subscription_plan_id  =   $formPlan->id;
                $company->save();
                flash('New Subscription activated successfully.','success');
                return redirect()->route('Company.Subscription');
            }
        }
    }

    public function subscriptionSubmit(Request $request){
        $this->validate($request,['plan'   => 'required']);
        setStripeKey();

        $formPlan   =    SubscriptionPlan::where('id',Crypt::decryptString($request->plan))->firstOrFail();
        $company    =   Company::where('id',Session::get('company_id'))->first();
        $status     =   checkSubscription();

        $companyStripeCustomerQuery    =    Company::where('id', Session::get('company_id'))->first();
        if($companyStripeCustomerQuery->stripe_user){
            $companyStripeCustomer  =   $companyStripeCustomerQuery->stripe_user;
            $customer   =    Customer::retrieve($companyStripeCustomer);
            $card       =   $customer->sources->all(array('limit'=>3, 'object' => 'card'));
            if(count($card['data'])==0){
                // create new customer source
                if($companyStripeCustomer){
                    $stripeCustomer     =    Customer::retrieve($companyStripeCustomer);
                    $stripeCustomer->sources->create(array('source' => $request->input('stripeToken')));
                    $stripeCustomer->sources->create(array('source' => $request->input('stripeToken')));
                }
            }

            if($status=="no_stripe_subscription" || $status=="trialing" ||  $status=="subscriptionCancel"  || $status=="freeSubscription"){
                $subscription = Subscription::create(['customer' => $company->stripe_user,'items' => [['plan' => $formPlan->plan_id]]]);

                $companySubscription = new CompanySubscription();
                $companySubscription->company_id = $company->id;
                $companySubscription->subscription_plan_id = $subscription->id;
                $companySubscription->max_user = $formPlan->max_user;
                $companySubscription->isCancel  =   0;
                $companySubscription->save();

                $company->trial_period  =   2;
                $company->max_user      =   $formPlan->max_user;
                $company->subscription_plan_id  =   $formPlan->id;
                $company->save();

                $subscriptionLog    =    new SubscriptionLog();
                $subscriptionLog->company_id    =   Session::get('company_id');
                $subscriptionLog->type      =   2;
                $subscriptionLog->subscription_plan_id  =    $formPlan->id;
                $subscriptionLog->save();

                //always upgraded
                $email  =   $company->userInfo->email;
                $data['email']   =   $email;
                $data['plan']   =   $formPlan;
                Mail::send('emails.subscription.upgradeSubscriptionEmail', $data, function ($message) use($email) {
                    $message->from("info@recordtimeapp.com.au","Record Time" );
                    $message->replyTo("info@recordtime.com.au","Record Time");
                    $message->to($email)->subject("Upgrade Plan Notification");
                });
            }
            else{
                $companySubscription    =    CompanySubscription::where('company_id', Session::get('company_id'))->first();
                $oldMaxUser     =   $company->max_user;

                $subscriptionPlan   =   Subscription::retrieve($companySubscription->subscription_plan_id);

                $sub    =   Subscription::retrieve($companySubscription->subscription_plan_id);
                $sub->plan  =   $formPlan->plan_id;
                $sub->save();

                $companySubscription    =    CompanySubscription::where('company_id',Session::get('company_id'))->first();
                $companySubscription->subscription_plan_id  =   $sub->id;
                $companySubscription->max_user              =   $formPlan->max_user;
                $companySubscription->isCancel  =   0;
                $companySubscription->save();

                $company->trial_period  =   2;
                $company->max_user      =   $formPlan->max_user;
                $company->subscription_plan_id  =   $formPlan->id;
                $company->save();

                $newMaxUser =    $formPlan->max_user;

                if($newMaxUser>$oldMaxUser){
                    $email  =   $company->userInfo->email;
                    $data['email']   =   $email;
                    $data['plan']   =   $formPlan;
                    Mail::send('emails.subscription.upgradeSubscriptionEmail', $data, function ($message) use($email) {
                        $message->from("info@recordtimeapp.com.au","Record Time" );
                        $message->replyTo("info@recordtime.com.au","Record Time");
                        $message->to($email)->subject("Upgrade Plan Notification");
                    });
                }else{
                    $email  =   $company->userInfo->email;
                    $data['email']   =   $email;
                    $data['plan']   =   $formPlan;
                    Mail::send('emails.subscription.downgradeSubscriptionEmail', $data, function ($message) use($email) {
                        $message->from("info@recordtimeapp.com.au","Record Time" );
                        $message->replyTo("info@recordtime.com.au","Record Time");
                        $message->to($email)->subject("Downgrade Plan Notification");
                    });
                }
                $subscriptionLog    =    new SubscriptionLog();
                $subscriptionLog->company_id    =   Session::get('company_id');
                $subscriptionLog->type      =   2;
                $subscriptionLog->subscription_plan_id  =    $formPlan->id;
                $subscriptionLog->save();
            }
            //deactivate all employee
//            deactivateAllEmployee(Session::get('company_id'));
            flash('New Subscription activated successfully. All employees are deactivated. Please activate them manually.','success');

            return redirect()->route('Company.Subscription');
        }
    }

    //==============================Credit Card Section==========================//
    public function updateCreditCard(){
        if(!checkProfileComplete()){
            return redirect()->route('companyProfile');
        }
        setStripeKey();
        $companyStripeCustomerQuery = Company::where('id', Session::get('company_id'))->first();
        if ($companyStripeCustomerQuery->stripe_user) {
            $companyStripeCustomer = $companyStripeCustomerQuery->stripe_user;
            $customer = Customer::retrieve($companyStripeCustomer);

            $cards = $customer->sources->all(array('limit' => 3, 'object' => 'card'));
            return view('dashboard.company.profile.creditCard.index',compact('cards'));
        }
    }

    public function creditCardStore(Request $request){
        if(!checkProfileComplete()){
            return redirect()->route('companyProfile');
        }
        $this->validate($request,['stripeToken'   => 'required']);

        setStripeKey();
        $companyStripeCustomerQuery    =    Company::where('id', Session::get('company_id'))->first();
        $companyStripeCustomer  =   $companyStripeCustomerQuery->stripe_user;
        $stripeCustomer =   Customer::retrieve($companyStripeCustomer);
        $cards  =   $stripeCustomer->sources->all(array('limit'=>3, 'object' => 'card'));
        if(count($cards["data"])>0){
            $cardId     = $cards["data"][0]->id;
            $stripeCustomer->sources->retrieve($cardId)->delete();
            try {
//                $stripeCustomer->sources->create(array('source' => $request->input('stripeToken')));
                $stripeCustomer     =    Customer::retrieve($companyStripeCustomer);
                $stripeCustomer->source = $request->input('stripeToken');
                $stripeCustomer->save();
                flash('Your credit card details updated successfully.','success');
            }
            catch(Card $e) {
                $body = $e->getJsonBody();
                $err  = $body['error'];
                $error = $err['message'];
                flash($err['message'],'warning');
            }
            return redirect()->route('Company.CreditCard.Update');
        }else{
            try {
                $stripeCustomer     =    Customer::retrieve($companyStripeCustomer);
                $stripeCustomer->source = $request->input('stripeToken');
                $stripeCustomer->save();

                flash('Your credit card details updated successfully.','success');
            }
            catch(Card $e) {
                    $body = $e->getJsonBody();
                    $err  = $body['error'];
                    $error = $err['message'];
                flash($err['message'],'warning');
            }
            return redirect()->route('Company.CreditCard.Update');
        }
    }

    public function companyProfile(){
        $status     =   checkSubscription();
        if($status=='noSubscription')
            return redirect('dashboard/company/profile/selectSubscription');

        checkProfileComplete();

        $userProfile    =    User::find(Auth::user()->id);
        $companyProfile =   Company::find(Session::get('company_id'));
        return view('dashboard.company.profile.index', compact('userProfile','companyProfile'));
    }


    public function companyProfileSubmit(Request $request){
        ini_set('memory_limit','512M');
        $this->validate($request,['firstName'   => 'required',
            'lastName'      => 'required',
            'companyName'   => 'required',
            'abn'           => 'required',
            'contactNumber' => 'required',
            'address'       => 'required',
            'image'         =>  'mimes:jpeg,bmp,png',
            'profile'       =>  'mimes:jpeg,bmp,png']);
        if(User::findOrFail(Auth::user()->id)){


            $user                   =   User::find(Auth::user()->id);
            $user->first_name        =   $request->firstName;
            $user->last_name         =   $request->lastName;
            $profile              =   Input::file('profile');
            if($request->hasFile('profile')) {
                if ($profile->isValid()) {
                    // $ext = $profile->getClientOriginalExtension();
                    // $filename =preg_replace('/\s+/', '', basename($request->file('profile')->getClientOriginalName(), '.' . $request->file('profile')->getClientOriginalExtension()) . time() . "." . $ext);
                    $dest = 'files/profile';
//                    $cProfileImage     =   Image::make($profile);
//                    if($cProfileImage->width()>500)
////                        Image::make($cProfileImage)->widen(500)->save($dest . '/' . $filename,60);
////                    else
////                        Image::make($cProfileImage)->save($dest . '/' . $filename,60);

                    // $profile->move($dest, $filename);
                    // $user->image = $dest . '/' . $filename;
                    $user->image = FunctionUtils::imageUpload($dest,$profile);
                }
            }

            if($user->save()){
                $company                =   Company::find(Session::get('company_id'));
                $company->name          =   $request->companyName;
                $company->abn           =   $request->abn;
                $company->contactNumber =   $request->contactNumber;
                $company->address       =   $request->address;
                $image              =   Input::file('image');
                if($request->hasFile('image')) {
                    if ($image->isValid()) {
                        // $ext = $image->getClientOriginalExtension();
                        // $filename = preg_replace('/\s+/', '', basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext);
                        $dest = 'files/company/logo';
//                        $cImage     =   Image::make($image);
//
//
//                        if($cImage->width()>500)
//                            Image::make($image)->widen(500)->save($dest . '/' . $filename,60);
//                        else
//                            Image::make($image)->save($dest . '/' . $filename,60);

                        // $image->move($dest, $filename);
                        // $company->logo = $dest . '/' . $filename;
                        $company->logo = FunctionUtils::imageUpload($dest,$image);
                    }
                } else {
                    $user->image = "assets/dashboard/images/logoAvatar.png";
                }
                $company->save();

                if ($company->docket_status == 0){
                    $docketTitle[]   =   array('id' => 1, 'name' => 'Pre Start Checklist');
                    $docketTitle[]   =   array('id' => 2, 'name' => 'Delivery Docket');
                     foreach ($docketTitle as $docketTitles){
                         $tempDocket             =    new Docket();
                         $tempDocket->title      =   $docketTitles['name'];
                         $tempDocket->subTitle   =   "";
                         $tempDocket->user_id    =   Auth::user()->id;
                         $tempDocket->invoiceable    =   0;
                         $tempDocket->docketApprovalType = 0;
                         $tempDocket->invoiceable = 0;
                         $tempDocket->theme_document_id = 0;
                         $tempDocket->timer_attachement = 0;
                         $tempDocket->xero_timesheet = 0;
                         $tempDocket->company_id =   Session::get('company_id');
                          if ($tempDocket->save()){


                              if ($tempDocket->title == "Pre Start Checklist"){
                                DocketField::insert([
                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '6',
                                        'order' =>  1,
                                        'required'=> 0,
                                        'label' => 'Date'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '1',
                                        'order' =>  2,
                                        'required'=> 0,
                                        'label' => 'Operator Name'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '1',
                                        'order' =>  3,
                                        'required'=> 0,
                                        'label' => 'Machine Make/Model'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '4',
                                        'order' =>  4,
                                        'required'=> 0,
                                        'label' => 'Job Location'],
                                ]);

                            }elseif($tempDocket->title =="Delivery Docket"){
                                DocketField::insert([
                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '6',
                                        'order' =>  1,
                                        'required'=> 0,
                                        'label' => 'Date'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '1',
                                        'order' =>  2,
                                        'required'=> 0,
                                        'label' => 'Delivery Driver'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '1',
                                        'order' =>  3,
                                        'required'=> 0,
                                        'label' => 'Client'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '4',
                                        'order' =>  4,
                                        'required'=> 0,
                                        'label' => 'Delivery Location'],


                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '2',
                                        'order' =>  5,
                                        'required'=> 0,
                                        'label' => 'Description of Items'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '5',
                                        'order' =>  6,
                                        'required'=> 0,
                                        'label' => 'Image of Items'],

                                    ['docket_id'    =>  $tempDocket->id,
                                        'docket_field_category_id' =>  '9',
                                        'order' =>  7,
                                        'required'=> 0,
                                        'label' => 'Recipient Signature'],
                                ]);
                            }
                              $companyAdmin  = Company::where('id',Session::get('company_id'))->select('user_id')->first();
                              $assignDocket = new AssignedDocket();
                              $assignDocket->user_id = $companyAdmin->user_id;
                              $assignDocket->assigned_by = $companyAdmin->user_id;
                              $assignDocket->docket_id = $tempDocket->id;
                              $assignDocket->save();


                          }

                     }

                    Company::where('id',Session::get('company_id'))->update(['docket_status'=>1]);

                }


                if (!checkProfileComplete()) {
                    $user->slackChannel('rt-profile-complete')->notify(new ProfileComplete($user));

                    $client = new \BaseCRM\Client(['accessToken' => 'c7bbb4e30c0aa8bcc55899c22bb07c17d2fcc971a735db650e0b0fc3ca6640e1']);
                    $lead = $client->leads->create(['first_name' => $request->firstName, 'last_name'=> $request->lastName,'email'=>$user->email,'organization_name'=>$request->companyName,'mobile'=>$request->contactNumber,]);
                    $lead['address']['city'] = $request->address;
                    $lead['custom_fields']['Company Abn'] = $request->abn;
                    $lead['custom_fields']['Company Logo'] = AmazoneBucket::url() . $company->logo;
                    $lead['custom_fields']['Profile Image'] = AmazoneBucket::url() . $user->image;
                    $lead['status'] = "signed up (trial period)";
                    $client->leads->update($lead['id'], $lead);
                }
                flash('Profile updated successfully!','success');
                return redirect()->route('companyProfile');
            }
            flash('Internal server error!','warning');
            return redirect()->route('companyProfile');
        }
    }

    public function removeCreditCard(){
        //get card details
        Stripe::setApiKey('sk_live_XoiqEVR07obBr1YQwoY8AGu8');
        $company    =   Company::where('id',Session::get('company_id'))->first();
        $companyStripeCustomer  =   $company->stripe_user;
        $stripeCustomer =   Customer::retrieve($companyStripeCustomer);
        $cards  =   $stripeCustomer->sources->all(array('limit'=>3, 'object' => 'card'));

        if(count($cards["data"])>0){
            $stripeCustomer->sources->retrieve($cards["data"][0]->id)->delete();
            flash('Credit card removed successfully.','success');
            return redirect()->route('Company.CreditCard.Update');
        }else{
            return redirect()->route('Company.CreditCard.Update');
        }
    }

    public function timezone()
    {
        $company    =   Company::where('id',Session::get('company_id'))->first();

        return view('dashboard.company.profile.timezone', compact('company'));

    }

    public function storeTimeZone(Request $request)
    {

        $company    =   Company::where('id',Session::get('company_id'))->first();
        $company->time_zone = $request->timezone;
        $company->save();

        flash('Timezon updated successfully.','success');
        return redirect()->route('Company.timezone');
    }

    // billing history
    function billingHistory(){
        $status     =   checkSubscription();
        $company = Company::where('id', Session::get('company_id'))->first();
        Stripe::setApiKey('sk_live_XoiqEVR07obBr1YQwoY8AGu8');
        $stripe_customer = Customer::retrieve($company->stripe_user);
        $invoices = Invoice::all(array('customer' => $stripe_customer->id, 'limit' => 100))->data;
        return view('dashboard.company.profile.billingHistory.index',compact('company','invoices', 'status'));
    }

    function billingHistoryView($key){
        $status     =   checkSubscription();
        $company = Company::where('id', Session::get('company_id'))->first();
        setStripeKey();
        $stripe_customer = Customer::retrieve($company->stripe_user);
        $invoices = Invoice::all(array('customer' => $stripe_customer->id, 'limit' => 100))->data;

        $flag   =    false;
        foreach($invoices as $invoice){
            if($invoice->id==$key){
                $flag   =   true;
                break;
            }
        }
        if($flag){
            $stripeInvoice    =   Invoice::retrieve($key);
            $company    =   Company::find(Session::get('company_id'));
            return view('dashboard.company.profile.billingHistory.view',compact('stripeInvoice','company'));
        }else{
            flash('Warning! Invalid request.','warning');
            return redirect()->route('Company.billingHistory');
        }
    }

    function downloadInvoice($key){
        $status     =   checkSubscription();
        $company = Company::where('id', Session::get('company_id'))->first();
        setStripeKey();
        $stripe_customer = Customer::retrieve($company->stripe_user);
        $invoices = Invoice::all(array('customer' => $stripe_customer->id, 'limit' => 100))->data;

        $flag   =    false;
        foreach($invoices as $invoice){
            if($invoice->id==$key){
                $flag   =   true;
                break;
            }
        }
        if($flag) {
            $stripeInvoice = Invoice::retrieve($key);
            $company = Company::find(Session::get('company_id'));
            $pdf = PDF::loadView('pdfTemplate.stripeInvoice',compact('stripeInvoice','company'))->setOptions(['dpi'=> 150,'isRemoteEnabled'=>true]);
            $pdf->setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif', 'isRemoteEnabled' => true]);
            return $pdf->download($key.'.pdf');
        }else{
            flash('Warning! Invalid request.','warning');
            return redirect()->route('Company.billingHistory');
        }
    }
}
