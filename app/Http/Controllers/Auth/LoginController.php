<?php

namespace App\Http\Controllers\Auth;

use App\Company;
use App\CompanySubscription;
use App\Http\Controllers\Controller;
use App\Mail\HowToUseRecordTime;
use App\Mail\SignupEmailVerification;
use App\Notifications\Login;
use App\Notifications\Signup;
use App\SubscriptionPlan;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Invoice;
use Validator;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Auth;
use Mail;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Subscription;
use overint\MailgunValidator;
use Laracasts\Flash\Flash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function showRegistrationForm(){
        return view('auth.registration');
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(),[
//            'abn'	=>	'required',
            'email' =>  'required|unique:users',
            'password'	=>	'required|confirmed|min:5',
            'g-recaptcha-response' => 'required'
        ],[
            'g-recaptcha-response.required' => 'Please complete captcha challenge.'
        ]);

//        if($validator->fails()){
//            return redirect('registration')->withErrors($validator)->withInput();
//        }
//        else {
//            $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
//            if($validator->validate($request->email)== "true") {
                $user = new User();
                $user->first_name = "";
                $user->last_name = "";
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->user_type = 2;
                $user->image = "assets/dashboard/images/logoAvatar.png";
                $user->email_verification = hash('sha256', str_random(10), false);

                if ($user->save()):
                    $company = new Company();
                    $company->user_id = $user->id;
                    $company->abn = "";
                    $company->name = "";
                    $company->can_invoice = 1;
                    $company->can_docket    =   1;
                    $company->can_timer     =   1;
                    $company->docket_client     =   1;
                    $company->docket_status     =   0;
                    $company->trial_period = 0;
                    $company->renew_date = Carbon::now();
                    $company->expiry_date = Carbon::now()->addMonth(1);
                    $company->max_user = 1;
                    $company->save();

                    Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
                    $stripeCustomer = Customer::create(['email' => $user->email,
                        'description' => 'Customer for ' . $user->email,
                        'metadata' => array('companyId' => $company->id)]);
                    $company->stripe_user = $stripeCustomer->id;
                    $company->save();



                    // Sending email
                    $data['email'] = $user->email;
                    $data['email_verification'] = $user->email_verification;
                    Mail::to($user->email)->send(new SignupEmailVerification($user));
                    $user->slackChannel('rt-signup')->notify(new Signup($user));

                    Mail::to($user->email)->send(new HowToUseRecordTime($user));
                    $user->slackChannel('rt-signup')->notify(new Signup($user));

                    $userEmail = $request->email;
                    return view('auth/success',compact("userEmail"));
                else:
                    Flash::error('Invalid Email or Password');
                    return redirect('login');
                endif;
//            } else {
//                flash('Invalid Email address.','warning');
//                return redirect('login');
//            }
//        }
    }

    public function emailVerification($key){
        $user   =    User::where('email_verification',$key);
        if($user->count()>0){
            $userId     =    $user->first()->id;
            $company    =   Company::where('user_id',$userId)->first();

            // Sending email
            $email  =   $user->first()->email;
            $data['email']   =   $email;

            User::where('id',$userId)->update(['email_verification'=>'','isActive'=>1]);
            flash('Your account has been activated successfully.','success');
            return redirect('login');
        }else{
            $message    =    "Link Expired.";
            return view('errors.errorPage',compact('message'));
        }
    }

    public function emailVerificationMessage(){
        return view('auth/success');
    }
//    public function authenticated(Request $request){
//        $user   =   Auth::user();
//        if($user->company()->id!=1):
//        $user->slackChannel('rt-login-backend')->notify(new Login($user));
//        endif;
//    }

    public function authenticated(Request $request){
        $user   =   Auth::user();
        if($user->id!=1){
            if(@$user->company()->id!=1 ):
                $user->slackChannel('rt-login-backend')->notify(new Login($user));
            endif;
        }
    }
}
