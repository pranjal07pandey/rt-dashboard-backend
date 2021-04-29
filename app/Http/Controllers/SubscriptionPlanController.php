<?php

namespace App\Http\Controllers;

use App\SubscriptionPlan;
use App\SubscriptionPlanDescription;
use Illuminate\Http\Request;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use Stripe\Subscription;
use App\Company;
use App\User;
use Carbon\Carbon;
use Session;

class SubscriptionPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('navigation','subscriptionPlan');
            Session::put('pageTitle','Subscription Plans');
            Session::put('navigationIcon','view_list');
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
//        $stripe = Stripe::setApiKey('sk_test_szvLCxnJFMLsOO6M5TCDwRFIW');
//        $plan   =   Plan::create([
//            'currency'  => 'aud',
//            'interval' => 'month',
//            'amount' => 21500,
//            'product' => 'prod_FUEkaKwVZCxY6Q',
//        ]);

        $plans  =   SubscriptionPlan::orderBy('created_at','asc')->get();
        return view('dashboard.V2.admin.subscriptionPlan.index',compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //create plan
        Stripe::setApiKey('sk_test_szvLCxnJFMLsOO6M5TCDwRFIW');
        $plan   =   Plan::create([
            'currency'  => 'aud',
            'interval'  => 'month',
            'product' => array(
                'name' => $request->name
              ),
            'amount'    => $request->amount*100,
        ]);

        $subscriptionPlan   =   new SubscriptionPlan();
        $subscriptionPlan->plan_id   =  $plan->id;
        $subscriptionPlan->currency  =  'aud';
        $subscriptionPlan->interval  =   'month';
        $subscriptionPlan->name     =   $request->name;
        $subscriptionPlan->amount   =   $request->amount;
        $subscriptionPlan->max_user     =   $request->maxUserLimit;
        $subscriptionPlan->save();
        flash('Subscription plan added successfully!','success');
        return redirect('dashboard/subscriptionPlan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\Response
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\Response
     */
    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SubscriptionPlan  $subscriptionPlan
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        Stripe::setApiKey('sk_test_nyksk5lrV3jOZmX8K7V2ltaN');
        $plan   =   Plan::retrieve($subscriptionPlan->plan_id);
        $plan->delete();
        $subscriptionPlan->delete();
        flash('Subscription plan deleted successfully!','success');
        return redirect()->back();
    }

    public function description($planId){
        $subscriptionPlan   =    SubscriptionPlan::find($planId);
        return view('dashboard.V2.admin.subscriptionPlan.description.index',compact('subscriptionPlan'));
    }
    public function descriptionStore(Request $request,$planId){
        $this->validate($request,['description' => 'required']);

        $planDescription                            =   new SubscriptionPlanDescription();
        $planDescription->subscription_plan_id   =   $planId;
        $planDescription->description               =   $request->description;
        if($planDescription->save()):
            flash('Subscription plan description added successfully!','success');
            return redirect('dashboard/subscriptionPlan/description/'.$planId);
        endif;
        return redirect('dashboard/subscriptionPlan');
    }

//    public function integration()
//    {
//        //march 2 integration
//
//        //create stripe account for old users
//        //last company id = 77
//        // $company    =   Company::where('id','<',78)->get();
//        // foreach ($company as $row) {
//        //     $user   =    User::where('id',$row->user_id)->first();
//        //     Stripe::setApiKey('sk_live_XoiqEVR07obBr1YQwoY8AGu8');
//        //     $stripeCustomer     =   Customer::create(['email' => $user->email,
//        //                             'description' => 'Customer for '.$user->email,
//        //                             'metadata'  => array('companyId'=>$row->id)]);
//        //     $row->stripe_user   =   $stripeCustomer->id;
//        //     $row->save();
//        // }
//
//        //set all company trial period 1 and add expiry date now+7days
//        // $company    =   Company::where('id','<',78)->get();
//        // foreach ($company as $row) {
//        //     $row->trial_period  =   1;
//        //     $row->expiry_date   =   Carbon::now()->addDay(7);
//        //     $row->save();
//        // }
//
//
//        //set all company max user 25 and subscription_plan_id == 5
//        $company    =   Company::where('id','<',78)->get();
//        foreach ($company as $row) {
//            $row->max_user  =   25;
//            $row->subscription_plan_id   =   5;
//            $row->save();
//        }
//        dd($company);
//    }
}
