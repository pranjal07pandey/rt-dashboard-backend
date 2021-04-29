<?php

namespace App\Http\Controllers\EmployeeManagement;

use App\Company;
use App\CompanySubscription;
use App\Employee;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use overint\MailgunValidator;
use App\Helpers\V2\FunctionUtils;

class EmployeeController extends Controller
{
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $companyId = getCompanyId();
            Session::put('company_id', $companyId);
            if (!checkProfileComplete()) {
                return redirect()->route('companyProfile');
            }
            $status = checkSubscription();
            switch ($status) {
                case 'noSubscription':
                    return redirect('dashboard/company/profile/selectSubscription');
                    break;

                case 'subscriptionCancel':
//                    return redirect()->route('Company.Subscription.Continue');
                    break;

                case 'past_due':
                    break;

                default:
                    break;
            }
            Session::put('navigation', 'Employee Management');
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $company    =   Company::with('employees')->findOrFail(Session::get('company_id'));
        $maxSubscriptionUser = 0;
        $companySubscription = CompanySubscription::where('company_id', Session::get('company_id'))->get();
        if (count($companySubscription)) {
            $maxSubscriptionUser = $companySubscription->first()->max_user;
        } else {
            $maxSubscriptionUser = $company->max_user;
        }
        $currentActiveUsers = User::whereIn('id', $company->employees->pluck('id')->toArray())
                                    ->where('isActive',1)->count() + 1;
        if ($maxSubscriptionUser == $currentActiveUsers) {
            flash("Please upgrade your subscription plans.Your maximum user limit is " . $maxSubscriptionUser . '.', 'warning');
            return redirect()->route('employeeManagement.index');
        }
        return view('dashboard.company.employeeManagement.employee.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,['firstName' => 'required',
            'lastName' => 'required',
            'email'=> 'required|unique:users',
            'password' => 'confirmed|min:5',
            'image' => 'mimes:jpeg,jpg,png,gif']);

        $validator = new MailgunValidator('pubkey-6f241717413584cea1333586a2b88c57');
        if ($validator->validate($request->email) == false){
            flash('Invalid Email address.', 'warning');
            return redirect()->back();
        }
        $employee = Employee::where('company_id', Session::get('company_id'))->pluck('sn')->toArray();
        $employeeCount = "";
        if(count($employee)==0){
            $employeeCount = 1;
        }else{
            $employeeCount = max($employee)+1;
        }


        $user               =   new User();
        $user->first_name   =   $request->firstName;
        $user->last_name    =   $request->lastName;
        $user->email        =   $request->email;
        $user->password     =   Hash::make($request->password);
        $user->user_type    =   ($request->admin=='')?3:2;
        if($request->admin != 1 && $request->employed != 1 ){$user->isActive     =    0;}
        else{ $user->isActive     =    1; }
        $user->email_verification   =   "";

        if($request->hasFile('image')) {
            $image              =   Input::file('image');
            if ($image->isValid()) {
                // $ext = $image->getClientOriginalExtension();
                // $filename = basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/company/employee';
                // $image->move($dest, $filename);
                // $user->image = $dest . '/' . $filename;
                $user->image = FunctionUtils::imageUpload($dest,$image);
            }
        } else{ $user->image = "assets/dashboard/images/logoAvatar.png"; }

        if ($user->save()):
            $employee                   = new Employee();
            $employee->company_id       = Session::get('company_id');
            $employee->user_id          = $user->id;
            $employee->is_admin         = ($request->admin == '') ? 0 : 1;
            $employee->employed         = ($request->employed == '') ? 0 : 1;
            $employee->invoice          = ($request->invoice == '') ? 0 : 1;
            $employee->docket           = ($request->docket == '') ? 0 : 1;
            $employee->timer            = ($request->timer == '') ? 0 : 1;
            $employee->docket_client    = ($request->docket_client == '') ? 0 : 1;
            $employee->appear_on_recipient  = ($request->appearOnRecipient=='')?0:1;
            $employee->can_self_docket      = ($request->canSelfDocket=='')?0:1;
            $employee->sn =$employeeCount;
            $employee->save();

            $data['email'] = $user->email;
            $data['name'] = $user->first_name . " " . $user->last_name;
            $data['password'] = $request->password;
            Mail::send('emails.signup.newUser', $data, function ($message) use ($user) {
                $message->from("info@recordtimeapp.com.au", "Record Time");
                $message->to($user->email)->subject("New Account");
            });
            flash('Employee added successfully!', 'success');
            return redirect()->route('employeeManagement.index');
        endif;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = Employee::with('userInfo')->findOrFail($id);
        if($employee->company_id!=Session::get('company_id')){
            flash('Invalid attempt!', 'warning');
            return redirect()->back();
        }
        return view('dashboard.company.employeeManagement.employee.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, ['firstName' => 'required',
            'lastName' => 'required',
            'password' => 'confirmed',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif']);

        $employee   = Employee::with('userInfo')->findOrFail($id);
        if($employee->company_id!=Session::get('company_id')){
            flash('Invalid attempt!', 'warning');
            return redirect()->back();
        }

        $user               = $employee->userInfo;
        $user->first_name   = $request->firstName;
        $user->last_name    = $request->lastName;


        if ($request->admin != 1 && $request->employed != 1 ){
            $user->isActive     =    0;
            $user->hashToken = " ";
            $user->deviceToken = " ";
        }else{ $user->isActive     =    1; }

        if($request->admin==1){ $user->user_type    =   2;
        }else{ $user->user_type    =   3; }

        if ($request->has('password')) {
            if ($request->password != null && $request->password != ""){
                $user->password = Hash::make($request->password);
                $data   =   array();
                if ($user->device_type == 2)
                    sendiOSNotification($user->deviceToken, "Record Time", "You have been logout from all device.", $data);

                if ($user->device_type == 1)
                    sendAndroidNotification($user->deviceToken, "Record Time", "You have been logout from all device.", $data);

                $user->hashToken = " ";
                $user->deviceToken = " ";
            }
        }

        $image = Input::file('image');
        if ($request->hasFile('image')) {
            if ($image->isValid()) {
                // $ext = $image->getClientOriginalExtension();
                // $filename = basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/company/employee';
                // $image->move($dest, $filename);
                // $user->image = $dest . '/' . $filename;
                $user->image = FunctionUtils::imageUpload($dest,$image);
            }
        }

        if ($request->employed == 0 ) {
            $user->isActive = 0;
            $data   =   array();

            if ($user->device_type == 2)
                sendiOSNotification($user->deviceToken, "Record Time", "You have been logout from all device.", $data);

            if ($user->device_type == 1)
                sendAndroidNotification($user->deviceToken, "Record Time", "You have been logout from all device.", $data);

            $user->hashToken = " ";
            $user->deviceToken = " ";
        } else { $user->isActive = 1; }

        $granted = array();
        $revoke = array();
        if (!$request->has('invoice') && $employee->invoice == 1){ $revoke[] = array('invoice');}
        elseif($request->invoice == 1 && $employee->invoice == 0){ $granted[] = array('invoice');}

        if (!$request->has('docket') && $employee->docket == 1){ $revoke[] = array('docket');}
        elseif($request->docket == 1 && $employee->docket == 0){ $granted[] = array('docket'); }

        if (!$request->has('timer') && $employee->timer == 1){ $revoke[] = array('timer');}
        elseif($request->timer == 1 && $employee->timer == 0){ $granted[] = array('timer');}

        if ($user->save()) {
            $employee->is_admin = ($request->admin == '') ? 0 : 1;
            $employee->employed = ($request->employed == '') ? 0 : 1;
            $employee->invoice = ($request->invoice == '') ? 0 : 1;
            $employee->docket = ($request->docket == '') ? 0 : 1;
            $employee->timer = ($request->timer == '') ? 0 : 1;
            $employee->docket_client = ($request->docket_client == '') ? 0 : 1;
            $employee->appear_on_recipient = ($request->appearOnRecipient == '') ? 0 : 1;
            $employee->can_self_docket = ($request->canSelfDocket == '') ? 0 : 1;
            $employee->save();

            flash('Employee updated successfully!','success');
            return redirect()->route('employeeManagement.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function receiveDocketCopy(Request $request){
        if($request->input("data")=="" || $request->input("status")=="") {
            return "Something went wrong, Please try again later.";
        }
        $company        =   Company::where('id',Session::get('company_id'))->first();

        if(in_array($request->input('data'),$company->getAllCompanyUserIds())){
            $user   =   User::find($request->input('data'));
            $user->receive_docket_copy  =   $request->input('status');
            $user->save();
        }else{
            return "Something went wrong, Please try again later.";
        }
    }

    public function activate(Request $request)
    {
        $employee = Employee::where('id', $request->id)->firstOrFail();
        if ($employee->company_id == Session::get('company_id')){
            $maxSubscriptionUser = 0;
            $companySubscription = CompanySubscription::where('company_id', Session::get('company_id'))->get();
            if (count($companySubscription)) {
                $maxSubscriptionUser = $companySubscription->first()->max_user;
            } else {
                $company = Company::where('id', Session::get('company_id'))->first();
                $maxSubscriptionUser = $company->max_user;
            }

            $totalEmployeeId = Employee::where('company_id', Session::get('company_id'))->pluck('user_id')->toArray();
            $currentActiveUsers = User::whereIn('id', $totalEmployeeId)->where('isActive', 1)->count() + 1;

            if ($maxSubscriptionUser == $currentActiveUsers) {
                flash("Please upgrade your subscription plans.Your maximum user limit is " . $maxSubscriptionUser . '.', 'warning');
            } else {
                $user = User::find($employee->user_id);
                $user->isActive = 1;
                $user->save();
                if ($user->save()) {
                    $employee = Employee::where('id', $request->id)->firstOrFail();
                    $employee->employed = 1;
                    $employee->save();
                }

                // Sending email
//                $data['email'] = $user->email;
//                $data['name'] = $user->first_name . " " . $user->last_name;
//                Mail::send('emails.signup.employeeActive', $data, function ($message) use ($user) {
//                    $message->from("info@recordtimeapp.com.au", "Record Time");
//                    $message->to($user->email)->subject("Employee Active");
//                });

                flash('User activated successfully.', 'success');
            }
        } else {
            flash('Invalid attempt!', 'warning');
        }
        return redirect()->back();
    }

    public function editAdmin($userId){
        $company    =   Company::with('userInfo')->findOrfail(Session::get('company_id'));
        $user =   Auth::user();
        if($company->userInfo->id != $user->id){
            return redirect()->route('employeeManagement.index');
        }
        return view('dashboard.company.employeeManagement.super-admin.edit', compact('user','company'));
    }

    public function updateAdmin($userId, Request $request){
        $this->validate($request, ['firstName' => 'required',
            'lastName' => 'required',
            'password' => 'confirmed',
            'image' => 'nullable|mimes:jpeg,jpg,png,gif']);

        $company        =   Company::with('userInfo')->findOrFail(Session::get('company_id'));
        $user           =   Auth::user();
        if($company->userInfo->id != $user->id) {
            return redirect()->route('employeeManagement.index');
        }

        $user->first_name   = $request->firstName;
        $user->last_name    = $request->lastName;
        $image              = Input::file('image');
        if($request->hasFile('image')) {
            if ($image->isValid()) {
                // $ext = $image->getClientOriginalExtension();
                // $filename = basename($request->file('image')->getClientOriginalName(), '.' . $request->file('image')->getClientOriginalExtension()) . time() . "." . $ext;
                $dest = 'files/company/employee';
                // $image->move($dest, $filename);
                // $user->image = $dest . '/' . $filename;
                $user->image = FunctionUtils::imageUpload($dest,$image);
            }
        }

        if ($request->has('password')) {
            if ($request->password != null && $request->password != "") {
                $user->password = Hash::make($request->password);
            }
        }
        $user->save();

        $company->can_invoice = ($request->invoice!=Null?$request->invoice:0);
        $company->can_docket = ($request->docket!=Null?$request->docket:0);
        $company->can_timer = ($request->timer!=Null?$request->timer:0);
        $company->docket_client = ($request->docket_client!=Null?$request->docket_client:0);
        $company->appear_on_recipient  =   ($request->appearOnRecipient=='')?0:1;
        $company->can_self_docket  =   ($request->canSelfDocket=='')?0:1;
        $company->save();

        flash('Profile updated successfully!', 'success');
        return redirect()->route('employeeManagement.index');
    }
}
