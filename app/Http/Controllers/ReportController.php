<?php

namespace App\Http\Controllers;

use App\AssignedInvoice;
use App\EmailSentDocket;
use App\EmailSentInvoice;
use App\SentDocketRecipient;
use App\SentDocketRecipientApproval;
use App\SentDockets;
use App\SentInvoice;
use App\SubscriptionLog;
use App\User;
use App\Company;
use App\Employee;
use App\Docket;
use App\ThemePurchase;
use Illuminate\Http\Request;
use Carbon\Carbon;
use League\Csv\Writer;
use Session;
use Config;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('navigation','reports');
            Session::put('pageTitle','Reports');
            Session::put('navigationIcon','view_list');
            return $next($request);
        });
    }
    public function index(){
        $user   =    User::where('isActive',1)->orderBy('updated_at','desc')->get();
        $data   =    array();
        $data["month"]  =    array();
        $data["count"]    =   array();
        $data['docketCount']  =   array();
        $data['invoiceCount']  =   array();
        $data['emailInvoiceCount']  =   array();
        $data['emailDocketCount']  =   array();

        for ($i = 0 ; $i < 12; $i++){
            $now    =     Carbon::now();
            $now->subMonth($i);
            $users   =    User::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
            $data["month"][] = $now->format('M');
            $data['count'][] = $users;

            $data['docketCount'][]     =   SentDockets::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
            $data['invoiceCount'][]     =   SentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
            $data['emailDocketCount'][]     =   EmailSentDocket::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
            $data['emailInvoiceCount'][]     =   EmailSentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
        }
        return view('dashboard.V2.admin.reports.index',compact('user','data'));
    }

    public function company(){
        Session::put('navigation','company');
        Session::put('pageTitle','Company');
        $company =  Company::orderBy('created_at', 'asc')->get();

        $company_ids = array();
        foreach($company as $row){
            array_push($company_ids, $row->id);
        }

        $data   =    array();
        $data["month"]  =    array();
        $data["count"]    =   array();
        $data['docketCount']  =   array();
        $data['invoiceCount']  =   array();
        $data['emailInvoiceCount']  =   array();
        $data['emailDocketCount']  =   array();

        for ($i = 0 ; $i < 12; $i++){
            $now    =     Carbon::now();
            $now->subMonth($i);
            $companies   =    Company::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->count();
            $data["month"][] = $now->format('M');
            $data['count'][] = $companies;

            $data['docketCount'][]     =   SentDockets::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('sender_company_id', $company_ids)->count();
            $data['invoiceCount'][]     =   SentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('company_id', $company_ids)->count();
            $data['emailDocketCount'][]     =   EmailSentDocket::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('company_id', $company_ids)->count();
            $data['emailInvoiceCount'][]     =   EmailSentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('company_id', $company_ids)->count();
        }
        return view('dashboard.V2.admin.reports.company',compact('company','data'));
    }

    public function employee($id){
        Session::put('navigation','company');
        Session::put('pageTitle','Employee');
        $company = Company::where('id', $id)->first();
        $employee = Employee::where('company_id', $company->id)->orderBy('created_at', 'desc')->get();

        $user_ids = array();
        foreach($employee as $row){
            array_push($user_ids, $row->user_id);
        }
        $data   =    array();
        $data["month"]  =    array();
        $data["count"]    =   array();
        $data['docketCount']  =   array();
        $data['invoiceCount']  =   array();
        $data['emailInvoiceCount']  =   array();
        $data['emailDocketCount']  =   array();

        for ($i = 0 ; $i < 12; $i++){
            $now    =     Carbon::now();
            $now->subMonth($i);
            $employees   =    Employee::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->where('company_id',$id)->count();
            $data["month"][] = $now->format('M');
            $data['count'][] = $employees;

            $data['docketCount'][]     =   SentDockets::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('user_id', $user_ids)->count();
            $data['invoiceCount'][]     =   SentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('user_id', $user_ids)->count();
            $data['emailDocketCount'][]     =   EmailSentDocket::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('user_id', $user_ids)->count();
            $data['emailInvoiceCount'][]     =   EmailSentInvoice::whereBetween('created_at',array($now->startOfMonth()->toDateTimeString(),$now->endOfMonth()->toDateTimeString()))->whereIn('user_id', $user_ids)->count();

        }
        return view('dashboard.V2.admin.reports.employee',compact('company','employee','data'));

    }

    public function docketTemplate($id)
    {
        $company = Company::where('id', $id)->first();
        $docket_template = Docket::where('company_id', $company->id)->get();
        return view('dashboard.V2.admin.reports.dockets',compact('docket_template', 'company'));
    }

    public function invoices()
    {
        Session::put('navigation','stripe_invoices');
        Session::put('pageTitle','Stripe Invoices');
        $stripe_key = Config::get('app.stripe_key');
        \Stripe\Stripe::setApiKey ($stripe_key );

        $stripe_inovices = \Stripe\Invoice::all(array("limit" => 100))->data;

        $total_amount = array();

        foreach($stripe_inovices as $row){

            array_push($total_amount, $row->total/100);
        }


        $data = array();
        $data["month"]  =    array();
        $data["count"]    =   array();
        $grapData   =    array();

        for ($i = 0 ; $i < 12; $i++){
            $now    =     Carbon::now();

            $now->subMonth($i);
            $invoices   =    \Stripe\Invoice::all(array("limit" => 100 , 'date[lt]' => strtotime($now->endOfMonth()->toDateTimeString()), 'date[gt]' => strtotime($now->startOfMonth()->toDateTimeString())))->data;
            $unpaid_invoices   =    \Stripe\Invoice::all(array('paid' => false ,"limit" => 100 , 'date[lt]' => strtotime($now->endOfMonth()->toDateTimeString()), 'date[gt]' => strtotime($now->startOfMonth()->toDateTimeString())))->data;
            $collection = collect($invoices);
            $plucked = $collection->pluck('total');
            $data["month"][] = $now->format('M');
            $data['count'][] = count($invoices);
            $data['unpaid_count'][] = count($unpaid_invoices);
            $data['monthly_total'][] = $plucked;

        }


        foreach($data["monthly_total"] as $total){
            $totalSum   =   0;

            foreach($total as  $tempData){
                $totalSum   +=  $tempData;
            }
            array_push($grapData, $totalSum/100);
        }
        return view('dashboard.V2.admin.reports.invoices',compact('stripe_inovices', 'data', 'total_amount', 'grapData'));
    }

    public function purchasedTheme(){

       Session::put('navigation','purchasedThemes');
       Session::put('pageTitle','Purchased Themes');
       $theme_purchases = ThemePurchase::orderBy('created_at', 'DESC')->get();

       return view('dashboard.V2.admin.reports.purchase_theme',compact('theme_purchases'));
    }

    public function nonActiveCompany(){
        Session::put('navigation','non_active_company');
        $user = User::where('email_verification','!=',"")->select('id')->get()->toArray();
        $company = Company::whereIn('user_id',$user)->get();

        return view('dashboard.V2.admin.nonActiveCompany.index',compact('company'));
    }

    public function nonActiveCompanydelete(Request $request){
        $company = Company::where('user_id',$request->id)->first();
        SubscriptionLog::where('company_id',$company->id)->delete();
        Employee::where('user_id',$request->id)->delete();
        SentDocketRecipientApproval::where('user_id',$request->id)->delete();
        SentDocketRecipient::where('user_id',$request->id)->delete();
        AssignedInvoice::where('user_id',$request->id)->delete();
        Company::where('user_id',$request->id)->delete();
        $user   =    User::where('id',$request->id)->first();
        $user->delete();
        flash('Non active Company Detail Deleted sucessfully.','success');
        return redirect()->back();


    }

    public function excel(Request $request){
        $companies  =    Company::where('trial_period',2)->orderBy('id','desc')->get();
        $trialCompanies     =    Company::where('trial_period',1)->orderBy('id', 'desc')->get();
        $remainingCompanies     =   Company::where('trial_period','!=',1)->where('trial_period','!=',2)->orderBy('id', 'desc')->get();
        $header =   ['SN','Company', 'Address', 'Contact Number', 'No. Of Employees', 'Subscription', 'Registered At'];

        $csv    =   Writer::createFromString('');
        $csv->insertOne($header);
        $sn = 1;
        foreach ($companies as $item):
            $csv->insertOne([$sn, $item->name, $item->address, $item->contactNumber, $item->employees->count()+1, $item->trialSubscription->name,Carbon::parse($item->created_at)->format('d M Y')]);
            $sn++;
        endforeach;
        foreach ($trialCompanies as $item):
            if($item->name!='')
            $csv->insertOne([$sn, $item->name, $item->address, $item->contactNumber, $item->employees->count()+1,'On Trail',Carbon::parse($item->created_at)->format('d M Y')]);
            $sn++;
        endforeach;
        foreach ($remainingCompanies as $item):
            if($item->name!='')
            $csv->insertOne([$sn, $item->name, $item->address, $item->contactNumber, $item->employees->count()+1, 'Trail Expired/Subscription Expired',Carbon::parse($item->created_at)->format('d M Y')]);
            $sn++;
        endforeach;
        $csv->output('file.csv');
    }
}
