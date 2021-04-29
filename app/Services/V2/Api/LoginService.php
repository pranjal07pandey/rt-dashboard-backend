<?php
namespace App\Services\V2\Api;

use App\Helpers\V2\MessageDisplay;
use App\Helpers\V2\StaticValue;
use App\Notifications\Login;
use App\Repositories\V2\AppInfoRepository;
use App\Repositories\V2\CompanyRepository;
use App\Repositories\V2\EmployeeRepository;
use App\Repositories\V2\UserNotificationRepository;
use App\Repositories\V2\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use overint\MailgunValidator;
use App\Helpers\V2\AmazoneBucket;
class LoginService {
    
    protected $userRepository,$companyRepository,$employeeRepository,$userNotificationRepository,
        $appInfoRepository;

    public function __construct(UserRepository $userRepository, CompanyRepository $companyRepository,
        EmployeeRepository $employeeRepository, UserNotificationRepository $userNotificationRepository,
        AppInfoRepository $appInfoRepository)
    {
        $this->userRepository = $userRepository;
        $this->companyRepository = $companyRepository;
        $this->employeeRepository = $employeeRepository;
        $this->userNotificationRepository = $userNotificationRepository;
        $this->appInfoRepository = $appInfoRepository;
    }

    public function login($request){
        $check 	=	Auth::attempt(array('email' => Input::get('email'),'password' => Input::get('password','')));
        if(!$check):
            return response()->json(array("status" => false,"message"=>'Invalid username and password'),500);
        else:
            if(Auth::user()->isActive!=1):
                return response()->json(array("status" => false,"message"=>'This user is currently not active.'),500);
            else:
                Auth::user()->tokens->each(function($token) {
                    $token->delete();
                });
                
                $companyCount = $this->companyRepository->getDataWhere([['user_id',Auth::user()->id]])->count();
                $employees = $this->employeeRepository->getDataWhere([['user_id',Auth::user()->id]])->get();
                $tokenResult  =   Auth::user()->createToken('authToken');
                $token        =   $tokenResult->token;
                $token->save();
                if($companyCount == 1 || $employees->where('employed',1)->count()==1){
                    if ($request->deviceToken != ""){
                        $this->userRepository->getDataWhere([['id',Auth::user()->id]])->update(['hashToken' => hash('sha256',str_random(10),false),'device_type' => Input::get('device_type'),'deviceToken' => $request->deviceToken]);
                    }else{
                        $this->userRepository->getDataWhere([['id',Auth::user()->id]])->update(['hashToken' => hash('sha256',str_random(10),false),'device_type' => Input::get('device_type')]);
                    }
                    $profileQuery = $this->userRepository->getDataWhere([['id',Auth::user()->id]])->first();
                    if($companyCount == 1){
                        $can_docket = 1;
                        $can_invoice = 1;
                    }else{
                        $can_docket = $profileQuery->employeeInfo->docket;
                        $can_invoice = $profileQuery->employeeInfo->invoice;
                    }
                    // if (count($employees) != 1){
                        $profile = array('id' => $profileQuery->id,
                            'user_type' => $profileQuery->user_type,
                            'first_name' => $profileQuery->first_name,
                            'last_name' => $profileQuery->last_name,
                            'email' => $profileQuery->email,
                            'can_docket' => $can_docket,
                            'can_invoice' => $can_invoice,
                            'image' => AmazoneBucket::url() . $profileQuery->image,
                            'hashToken' => $profileQuery->hashToken);
                    // }

                    $companyId  =   0;
                    if(count($employees) != 0):
                        $companyId = $employees->first()->company_id;
                    else :
                        $companyId   =  $this->companyRepository->getDataWhere([['user_id', Auth::user()->id]])->first()->id;
                    endif;
                    $companyQuery   =  $this->companyRepository->getDataWhere([['id',$companyId]])->first();

                    $company    =   array('id'  =>  $companyQuery->id,
                        'name'  =>  $companyQuery->name,
                        'logo'  =>  AmazoneBucket::url() . $companyQuery->logo,
                        'address' =>  $companyQuery->address);

                    $notificationCount  =  $this->userNotificationRepository->getDataWhere([['receiver_user_id',$profileQuery->id],['status',0]])->count();
                    $addons =  array();
                    $addons[]   =   array('id' => 1, 'name' => 'Timer', 'status' => 1);
                    $addons[]   =   array('id' => 2, 'name' => 'Message', 'status' => 1);

                    $user   =   Auth::user();
                    if($user->company()->id!=1):
                        $user->slackChannel('rt-login-app')->notify(new Login($user));
                    endif;

                    return response()->json(["profile" => $profile, "company" => $company, 'notificationCount' =>$notificationCount , 'addons' => $addons,'access_token' => $tokenResult->accessToken],200);
                }else{
                    return response()->json(["message"=>MessageDisplay::InvalidPasswordAndUsername],500);
                }
            endif;
        endif;
    }

    public function registration($request){
        try {
            DB::beginTransaction();
            $validator = new MailgunValidator(StaticValue::MailgunPubKey());
            if($validator->validate($request->email)!= "false" && $validator->validate($request->email)!= null) {
                $userRequest = new User();
                $userRequest['first_name'] = $request->first_name;
                $userRequest['last_name'] = $request->last_name;
                $userRequest['email'] = $request->email;
                $userRequest['password'] = Hash::make($request->password);
                $userRequest['user_type'] = 2;
                $userRequest['image'] = "assets/dashboard/images/logoAvatar.png";
                $userRequest['email_verification'] = hash('sha256', str_random(10), false);
                $user = $this->userRepository->insertAndUpdate($userRequest);

                $companyRequest = new Request();
                $companyRequest['user_id'] = $user->id;
                $companyRequest['abn'] = "";
                $companyRequest['name'] = "";
                $companyRequest['trial_period'] = 0;
                $companyRequest['renew_date'] = Carbon::now();
                $companyRequest['expiry_date'] = Carbon::now()->addMonth(1);
                $companyRequest['max_user'] = 1;
                $this->companyRepository->insertAndUpdate($companyRequest);
                
                // Sending email
                $data['email'] = $user->email;
                $data['email_verification'] = $user->email_verification;

                Mail::send('emails.signup.signupVerification', $data, function ($message) use ($user) {
                    $message->from("info@recordtimeapp.com.au", "Record Time");
                    $message->to($user->email)->subject("Email Verification");
                });
                DB::commit();
                return response()->json(["message" => MessageDisplay::EmailActivation],200);
            }else{
                return response()->json(["message" => MessageDisplay::InvalidEmail],500);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json(["message" => MessageDisplay::ERROR],500);
        }
    }

    public function getAppInfo(){
        $app_info  = $this->appInfoRepository->getModel()->select('field_slug','value');
        if($app_info->count()>0){
            foreach ($app_info->orderBy('id','desc')->get() as $row){
                $app_infos[$row->field_slug]    =    $row->value;
            }
        }
        return $app_infos;
    }
}