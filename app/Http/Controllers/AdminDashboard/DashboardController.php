<?php

namespace App\Http\Controllers\AdminDashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Company;
use App\User;
use App\Employee;
use App\Docket;
use App\DocketField;
use App\Invoice;
use App\EmailSentDocket;
use App\EmailSentInvoice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function getCompanyDetails(){
        $companies = Company::all();
        return $companies;

    }

    public function getCompanyById($id){
        return Company::findOrFail($id);
    }

    //update the expiration date of the company
    public function updateCompanyDetails(Request $request, $company_id){
        $company = Company::findOrFail($company_id);
        // dd($request->input('expiry_date'));
        $company->expiry_date = Carbon::parse($request->input('expiry_date'))->toDateTimeString();
        $company->update();
        // $new_expiry_date = Carbon::parse($company['expiry_date'])->toDateTimeString();
        return response(['message'=>'Expiration Time Successfully updated'], 200);
    }

    public function companiesCountByMonth(){
        $result = Company::selectRaw('MONTH(created_at) month, count(*) registered_companies')
                ->groupBy('month')
                ->get();
        
        return $result;
    }

    public function docketsCountByMonth(){
        $result = Docket::selectRaw('MONTH(created_at) month, count(*) dockets_sent')
                ->groupBy('month')
                ->get();
        
        return $result;
    }

    public function invoicesCountByMonth(){
        $result = Invoice::selectRaw('MONTH(created_at) month, count(*) invoices_sent')
                ->groupBy('month')
                ->get();
        
        return $result;
    }

    public function emailDocketsByMonth(){
        $result = EmailSentDocket::selectRaw('MONTH(created_at) month, count(*) email_dockets_sent')
                ->groupBy('month')
                ->get();
        
        return $result;
    }

    public function emailInvoicesByMonth(){
        $result = EmailSentInvoice::selectRaw('MONTH(created_at) month, count(*) email_invoices_sent')
                ->groupBy('month')
                ->get();
        
        return $result;
    }
    
    public function getAllUsers(){
        // $users = User::where('isActive', '=', 1)->get();
        $users = User::all();
        return $users;
    }

    public function updateUserInfo(Request $request, $user_id){
        # code...
        $user = User::findOrFail($user_id);
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->user_type = $request->input('user_type');
        $user->email = $request->input('email');

        if($request->input('password')){
            $user->password = Hash::make($request->input('password')); 
        }
        
        $user->update();

        return response(['message'=>'User successfully updated'], 200);
        // dd($request->input());

    }

    public function getUserById($user_id){
        $user = User::findOrFail($user_id);
        return $user;
    }

    public function usersCountByMonth(){
        $result = User::selectRaw('MONTH(created_at) month, count(*) user_registered')
                ->groupBy('month')
                ->get();
        
        return $result;
    }

    //filter active user withing the given date range
    public function filterUsersByDate($d1,$d2){

        // $start_date = Carbon::parse($request->start_date)->toDateTimeString();
        // $end_date = Carbon::parse($request->end_date)->toDateTimeString();

        $start_date = Carbon::parse($d1)->toDateTimeString();
        $end_date = Carbon::parse($d2)->toDateTimeString();

        return User::whereBetween('updated_at',[$start_date,$end_date])->get();

    }

    public function getEmployeesFromCompanyId($id){
        $employees = Employee::where('company_id', '=', $id)->get();
        return $employees;
    }

    public function getDocketsFromCompanyId($id){
        $dockets = Docket::where('company_id', '=', $id)->get();
        return $dockets;
    }

    public function getDocketDetailsFromDocketId($id){
        $docket = Docket::findOrFail($id);
        return $docket;
    }

    public function getDocketFieldsFromDocketId($id){
        $docket_fields = DocketField::where('docket_id', '=', $id)->get();
        return $docket_fields;
    }

    public function getDocketsFromUserId($user_id){
        $dockets = Docket::where('user_id', '=', $user_id)->get();
        return $dockets;
    }

    public function getInvoicesFromUserId($user_id){
        $invoices = Invoice::where('user_id', '=', $user_id)->get();
        return $invoices;
    }

    public function getCompanyFromUserId($user_id){
        $company = Company::where('user_id', '=', $user_id)->get();
        return $company;
    }

    public function mostFrequentlyUsedDocket($id){
        $frequent_dockets = Docket::groupBy('title')
                            ->selectRaw('count(*) as total, title')
                            ->where('company_id', '=', $id)
                            ->get();
        
        return $frequent_dockets;
    }

}
