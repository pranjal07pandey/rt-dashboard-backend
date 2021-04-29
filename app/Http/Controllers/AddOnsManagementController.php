<?php

namespace App\Http\Controllers;

use App\Addons;
use App\AddonsPlan;
use App\StripeProduct;
use Illuminate\Http\Request;
use Session;
use Stripe\Plan;
use Stripe\Product;
use Stripe\Stripe;

class
AddOnsManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            Session::put('navigation','addOnsManagement');
            Session::put('pageTitle','Add-ons Management');
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
        $this->setupAddons();

        $addons =   Addons::get();
        return view('dashboard.v2.admin.addOnsManagement.index',compact('addons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $addons     =   Addons::find($id);
        return view('dashboard.v2.admin.addOnsManagement.edit',compact('addons'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

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
        $this->validate($request,['title' => 'required']);
        $addons     =    Addons::find($id);
        $this->setupAddons();

        $plan       =   Plan::retrieve($addons->stripe_product_id);

        $product    =   Product::retrieve($plan->product);
        $product->name  =   $request->title;
        $product->save();

        $addons->title  =    $request->title;
        $addons->save();

        flash('Add-ons details updated successfully!','success');
        return redirect('dashboard/addOnsManagement');
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

    public function setupAddons(){
        $userPlans  =    array(1,4,9,14,25);

        //set add-ons
        setStripeKey();
        $checkAddonProduct  =   StripeProduct::where('slug','add-ons')->count();
        if(!$checkAddonProduct){
            $product    =   Product::create(array("name" => 'Add-ons', 'type' => 'service'));
            $stripProduct  =    new StripeProduct();
            $stripProduct->product_id   =   $product->id;
            $stripProduct->type         =   "service";
            $stripProduct->name         =   "Add-ons";
            $stripProduct->slug         =   "add-ons";
            $stripProduct->save();
        }else{
            $stripProduct   =   StripeProduct::where('slug','add-ons')->first();
            $product    =   Product::retrieve($stripProduct->product_id);
        }


        //messaging add-on
        $checkMessaging     =   Addons::where('slug','messaging')->count();
        if(!$checkMessaging){
            $addonMessaging =   new Addons();
            $addonMessaging->product_id =   $stripProduct->product_id;
            $addonMessaging->title  =   "Messaging";
            $addonMessaging->slug   =   "messaging";
            $addonMessaging->rate   =   5;
            $addonMessaging->save();

            //setup use wise stripe plans
            foreach($userPlans as $userLimit){
                $plan   =   Plan::create([
                    'currency'  => 'aud',
                    'interval'  => 'month',
                    'product' => $stripProduct->product_id,
                    'amount'    => 5*$userLimit*100,
                    'nickname'  =>  "Messaging",
                    'metadata'  =>  array('type' => 1,'max_user' => $userLimit,'addon'=>'messaging')
                ]);
                $addonsPlan =    new AddonsPlan();
                $addonsPlan->addon_id   =   $addonMessaging->id;
                $addonsPlan->max_user   =   $userLimit;
                $addonsPlan->amount     =   5*$userLimit;
                $addonsPlan->plan_id    =   $plan->id;
                $addonsPlan->save();
            }
        }
        //document manager add-on
        $checkMessaging     =   Addons::where('slug','document-manager')->count();
        if(!$checkMessaging){
            $addonMessaging =   new Addons();
            $addonMessaging->product_id =   $stripProduct->product_id;
            $addonMessaging->title  =   "Document Manager";
            $addonMessaging->slug   =   "document-manager";
            $addonMessaging->rate   =   5;
            $addonMessaging->save();

            //setup use wise stripe plans
            foreach($userPlans as $userLimit){
                $plan   =   Plan::create([
                    'currency'  => 'aud',
                    'interval'  => 'month',
                    'product' => $stripProduct->product_id,
                    'amount'    => 5*$userLimit*100,
                    'nickname'  =>  "Document Manager",
                    'metadata'  =>  array('type' => 1,'max_user' => $userLimit,'addon'=>'Document Manager')
                ]);
                $addonsPlan =    new AddonsPlan();
                $addonsPlan->addon_id   =   $addonMessaging->id;
                $addonsPlan->max_user   =   $userLimit;
                $addonsPlan->amount     =   5*$userLimit;
                $addonsPlan->plan_id    =   $plan->id;
                $addonsPlan->save();
            }
        }

        //text manager add-on
        $checkMessaging     =   Addons::where('slug','text-manager')->count();
        if(!$checkMessaging){
            $addonMessaging =   new Addons();
            $addonMessaging->product_id =   $stripProduct->product_id;
            $addonMessaging->title  =   "Pre-filler";
            $addonMessaging->slug   =   "text-manager";
            $addonMessaging->rate   =   5;
            $addonMessaging->save();


            //setup use wise stripe plans
            foreach($userPlans as $userLimit){
                $plan   =   Plan::create([
                    'currency'  => 'aud',
                    'interval'  => 'month',
                    'product' => $stripProduct->product_id,
                    'amount'    => 5*$userLimit*100,
                    'nickname'  =>  "Pre-filler Manager",
                    'metadata'  =>  array('type' => 1,'max_user' => $userLimit,'addon'=>'Pre-filler Manager')
                ]);
                $addonsPlan =    new AddonsPlan();
                $addonsPlan->addon_id   =   $addonMessaging->id;
                $addonsPlan->max_user   =   $userLimit;
                $addonsPlan->amount     =   5*$userLimit;
                $addonsPlan->plan_id    =   $plan->id;
                $addonsPlan->save();
            }
        }
    }
}
