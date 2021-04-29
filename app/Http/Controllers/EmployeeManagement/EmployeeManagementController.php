<?php

namespace App\Http\Controllers\EmployeeManagement;

use App\Company;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EmployeeManagementController extends Controller
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
        $company = Company::with('userInfo','employees.userInfo','subscription')->where('id', Session::get('company_id'))->first();
        $activeUser = User::whereIn('id', $company->employees()->pluck('user_id')->toArray())->where('isActive', 1)->get();

        return view('dashboard.company.employeeManagement.index', compact( 'company', 'activeUser'));
    }
}
