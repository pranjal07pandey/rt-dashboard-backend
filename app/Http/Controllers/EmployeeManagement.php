<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanySubscription;
use App\Docket;
use App\Employee;
use App\Invoice;
use App\PaymentLog;
use App\User;
use App\UserNotification;
use Carbon\Carbon;
use File;
use function GuzzleHttp\Promise\all;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Laracasts\Flash\Flash;
use overint\MailgunValidator;
use Session;
use Illuminate\Support\Facades\Auth;

class EmployeeManagement extends Controller
{





    public function checkTrialPeriod()
    {
        $company = Company::where('id', Session::get('company_id'))->first();
        if (PaymentLog::where('company_id', Session::get('company_id'))->count() > 0) {
            if ($company->trial_period == 0) {
                $company->trial_period = 2;
                $company->save();
            }
            return false;
        } else {
            if ($company->trial_period == 0) {
                $expiryDate = Carbon::parse($company->expiry_date);
                $now = Carbon::now();
                if ($now->lt($expiryDate)) {
                    $expiryText = $expiryDate->diffInDays($now);
                    if ($expiryText == 0) {
                        $expiryText = $expiryDate->diffInHours($now) . " hours";
                    } else {
                        $expiryText = $expiryText . " days";
                    }
                    flash("Your trial period expire in " . $expiryText . ". Please upgrade your plan on account section.", "warning");
                    return false;
                } else {
                    $company->trial_period = 1;
                    $company->save();
                    flash("Your trial period has been expired. Please upgrade your plan.", "danger");
                }
            } else if ($company->trial_period == 1) {
                flash("Your trial period has been expired. Please upgrade your plan.", "danger");
            }
        }
        return true;
    }



    public function sendMessage(Request $request)
    {
        $this->validate($request, ['subject' => 'required', 'employeeId' => 'required', 'message' => 'required']);
        foreach ($request->employeeId as $userId) {
            $user = User::find($userId);

            $userNotification = new UserNotification();
            $userNotification->sender_user_id = Auth::user()->id;
            $userNotification->receiver_user_id = $user->id;
            $userNotification->type = 1;
            $userNotification->title = $request->title;
            $userNotification->message = $request->message;
            $userNotification->key = 0;
            $userNotification->status = 0;
            $userNotification->save();

            if ($user->device_type == 2) {
                if ($user->deviceToken != "") {
                    sendiOSNotification($user->deviceToken, $request->subject, strip_tags($request->message), 1);
                }
            }
        }
        flash('Message sent successfully.', 'success');
        return redirect()->route('employeeManagement.index');
    }
}
