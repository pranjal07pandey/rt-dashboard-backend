<?php

namespace App\Http\Controllers;

use App\AppInfo;
use App\Company;
use App\Employee;
use App\SendDocketImageValue;
use App\SentDockets;
use App\SentDocketsValue;
use App\SentDocketUnitRateValue;
use Illuminate\Http\Request;
use Auth;
use Session;
use App\User;

class DashboardController extends Controller
{
    public function index(){
        if(Auth::user()->user_type==1):
            return redirect()->route('report_by_comapny');
        elseif(Auth::user()->user_type==2):
            if(Auth::user()->isActive==0){
                flash('Your account is not yet verified. Please check your inbox/spam folder for a verification email.','warning');
                Auth::logout();
                return redirect('login');
            }
            $companyId  =   0;
            if(Employee::where('user_id', Auth::user()->id)->count()!=0):
                $companyId = Employee::where('user_id', Auth::user()->id)->first()->company_id;
                Session::put('adminType',2);
            else :
               $companyId   =   Company::where('user_id', Auth::user()->id)->first()->id;
                Session::put('adminType',1);
            endif;

            Session::put('company_id',$companyId);
            if(Auth::user()->first_name == "" || Auth::user()->last_name == ""){
//                flash('Please update your basic information!','warning');
                return redirect('dashboard/company/profile');
            }
            return redirect('dashboard/company');
        else :
            flash('Your account has not been given access to login to backend','warning');
            Auth::logout();
            return redirect('login');
        endif;
    }

    public function userManagement(){
        return view('dashboard.admin.userManagement.index');
    }

    public function clientManagementView(){
        return view('dashboard.admin.clientManagement.index');
    }

    public function submitLabel(Request $request){
        return "test";
    }

    public function deleteEmployee(){
        $employee   =   Employee::get();
        return view('dashboard.admin.extra.deleteEmployee', compact('employee'));
    }

    public function deleteEmployeeSubmit($id){
        //get employee Info
        $employee   =   Employee::find($id);

        //get all sent docket related to user
        $sentDockets    =    SentDockets::where('user_id',$employee->user_id)->orWhere('receiver_user_id',$employee->user_id)->get();
        foreach ($sentDockets as $row){
            //get docket value and delete it
            $sentDocketValue    =    SentDocketsValue::where('sent_docket_id',$row->id)->get();
            foreach ($sentDocketValue as $valueRow){

                //check value is image or not
                if($valueRow->docketFieldInfo->docket_field_category_id == 5){
                    //delete images
                    SendDocketImageValue::where('sent_docket_value_id',$valueRow->id)->delete();
                }else if($valueRow->docketFieldInfo->docket_field_category_id == 7) {
                    SentDocketUnitRateValue::where('sent_docket_value_id', $valueRow->id)->delete();
                }
                SentDocketsValue::where('id',$valueRow->id)->delete();
            }
        }
        SentDockets::where('user_id',$employee->user_id)->orWhere('receiver_user_id',$employee->user_id)->delete();

        Employee::where('id',$id)->delete();
        User::where('id',$employee->user_id)->delete();
    }


    public function appSetting(){
        Session::put('navigation','appSetting');
        Session::put('pageTitle','App Setting');
        $appInfo = AppInfo::orderBy('created_at','desc')->get();
        return view('dashboard.V2.admin.appSetting.index', compact('appInfo'));
    }
    public function saveAppInfo(Request $request){
        $this->validate($request,['field_name'=>'required','value'   => 'required']);
        if(AppInfo::where('field_name',$request->field_name)->count()!=0){
            flash('The Field Name "'.$request->field_name.'" has already been taken.','warning');
            return redirect()->back();
        }else{
            $appinfo            =    new AppInfo();
            $appinfo->field_name      =   $request->field_name;
            if ($request->field_slug == ""){
                $appinfo->field_slug  =   str_slug($request->field_name);
            }else{
                $appinfo->field_slug      =   $request->field_slug;
            }
            $appinfo->value      =   $request->value;
            $appinfo->save();
            flash('App info added successfully.','success');
            return redirect()->back();
        }
    }
    public function updateAppInfo(Request $request){
        $this->validate($request,['value'   => 'required', 'field_name' =>  'required']);
        $updateAppinfo   =   AppInfo::where('id',$request->id)->firstOrFail();
        $updateAppinfo->field_name =$request->field_name;
        if ($request->field_slug == ""){
            $updateAppinfo->field_slug  =   str_slug($request->field_name);
        }else{
            $updateAppinfo->field_slug      =   $request->field_slug;
        }
        $updateAppinfo->value      =   $request->value;
        $updateAppinfo->save();
        flash('App info Updated successfully.','success');
        return redirect()->back();

    }

}
