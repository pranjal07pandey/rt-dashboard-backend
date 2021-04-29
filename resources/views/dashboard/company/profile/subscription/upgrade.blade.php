@extends('layouts.companyDashboard')

@section('content')
    <?php $flag =   false; ?>
    <div class="row">
        <div class="col-md-12">
            <section class="content-header">
                <h1>
                    <i class="fa fa-dollar"></i> Upgrade Plan
                </h1>
                <ol class="breadcrumb hidden-sm hidden-xs">
                    <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="#">Profile</a></li>
                    <li class="active">Upgrade Plan</li>
                </ol>
            </section>

            <div class="subscriptionDetailsWrapper">
                <div class="pull-left" style="margin-right:50px;">
                    <strong style="padding-bottom: 10px;"><i class="fa fa-calendar"></i> Current Plan</strong>
                    <div class="planDetailBullet">
                        @if($status=="freeSubscription")
                            Single User (20 dockets/month)
                        @else
                            @if($status=="no_stripe_subscription" || $status=="trialing" || $status=="subscriptionCancel" )
                                {{ $subscriptionPlan->name }}
                            @else
                                {{ @$subscriptionPlan['name'] }}
                            @endif
                        @endif
                    </div>
                </div>
                <div class="pull-left">
                    <strong style="padding-bottom: 10px;"><i class="fa fa-dollar"></i> Cost Per Month</strong>
                    <div class="planDetailBullet">
                        <i class="fa fa-dollar"></i> @if($status=="no_stripe_subscription" || $status=="trialing" ||  $status=="subscriptionCancel" || $status=="freeSubscription" ) 0.00 @else {{ $subscriptionPlan->amount }}@endif
                    </div>
                </div>
                <div class="pull-right text-right">
                    <a href="{{ route('Company.Subscription') }}" class="btn btn-raised btn-sm" style="font-size:10px;margin-top: 20px;margin-bottom: 10px;">View Plan Details</a>
                    @if($status=="no_stripe_subscription" || $status=="trialing" || $status=="subscriptionCancel" || $status=="freeSubscription")
                        <div style="font-size: 12px;">
                            <i> Trial Subscription :   {{ \Carbon\Carbon::parse($companyStripeCustomerQuery->expiry_date)->subDays(30)->format('d-M-Y') }} to {{ \Carbon\Carbon::parse($companyStripeCustomerQuery->expiry_date)->format('d-M-Y') }}</i>
                        </div>
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
            <br/>
            <section class="content-header">
                <h1>
                    <i class="fa fa-bookmark"></i> Pick a suitable plan
                </h1>
            </section>
            <ul class="planDetails">
                <li class="smallToMedPlan plan">
                    <ul>
                        <li class="planTitle">
                            <h2>Free User Plan</h2>
                        </li>
                        <li class="planPrice"> <span>$ 0</span>/Month</li>
                        <li>
                            <ul class="planDescriptions">
                                <li><a href="#numdevices" class="pPlanLinks"></a>Send 5 Dockets a month</li>
                                <li><a href="#numdevices" class="pPlanLinks"></a>Receive Unlimited Dockets</li>
                                <li><a href="#numdevices" class="pPlanLinks"></a>Unlimited Docket Templates</li>
                                <li><a href="#numdevices" class="pPlanLinks"></a>Unlimited Invoice Templates</li>
                                <li><a href="#numdevices" class="pPlanLinks"></a>Send 1 Invoice a Month</li>
                                <li><a href="#numdevices" class="pPlanLinks"></a>Receive Unlimited Invoices</li>
                            </ul>
                        </li>
                        <li>
                            @if($status=="freeSubscription" || $status=="no_stripe_subscription")
                                <?php $flag =   true; ?>
                                <a href="#" class="btn btn-primary btn-raised btn-sm">Current plan</a>
                            @else
                                <a href="#" class="btn btn-success btn-raised btn-sm" data-toggle="modal" data-target="#freeSubscription">Free</a>
                            @endif
                        </li>
                    </ul>
                </li>

                @if($subscriptionPlans)
                    @foreach($subscriptionPlans as $plan)
                        <li class="smallToMedPlan plan">
                            <ul>
                                <li class="planTitle">
                                    <h2>{{ $plan->name }}</h2>
                                </li>
                                <li class="planPrice"> <span><small>$</small> {{ $plan->amount }}</span>/Month</li>
                                <li>
                                    <ul class="planDescriptions">
                                        @if($plan->description)
                                            @foreach($plan->description as $description)
                                                <li> <a href="#numdevices" class="pPlanLinks"></a>{{ $description->description }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li>
                                    @if($status!="freeSubscription")
                                        @if($status=="no_stripe_subscription" || $status=="trialing" || $status=="subscriptionCancel" )
                                            @if($subscriptionPlan->id==$plan->id)
                                                <a href="#" class="btn btn-primary btn-raised btn-sm">Current plan</a>
                                                <?php $flag=true; ?>
                                            @else
                                                <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($plan->id) }}" dataPlanText="{{ $plan->name }}" dataPrice="$ {{ $plan->amount }}">
                                                    @if($flag==true)Upgrade @else Downgrade @endif
                                                </a>
                                            @endif
                                        @else
                                            @if($subscriptionPlan->id==$plan->id)
                                                <a href="#" class="btn btn-primary btn-raised btn-sm">Current plan</a>
                                                <?php $flag=true; ?>
                                            @else
                                                <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($plan->id) }}" dataPlanText="{{ $plan->name }}" dataPrice="$ {{ $plan->amount }}">
                                                    @if($flag==true)Upgrade @else Downgrade @endif
                                                </a>
                                            @endif
                                        @endif
                                    @else
                                        <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($plan->id) }}" dataPlanText="{{ $plan->name }}" dataPrice="$ {{ $plan->amount }}">
                                            Upgrade
                                        </a>
                                    @endif
                                </li>
                            </ul>
                        </li>
                    @endforeach
                @endif
                <div class="clearfix"></div>
            </ul>
            @if($companyStripeCustomerQuery->subscription_plan_id!="")
                @if($companyStripeCustomerQuery->trialSubscription->type==1)
                    {{ \App\SubscriptionPlan::where('id',11)->first()  }}
                    <section class="content-header">
                        <h1>
                            <i class="fa fa-bookmark"></i> Custom Plan
                        </h1>
                    </section>
                    <ul class="planDetails">
                        <li class="smallToMedPlan plan">
                            <ul>
                                <li class="planTitle">
                                    <h2>{{ $companyStripeCustomerQuery->trialSubscription->name }}</h2>
                                </li>
                                <li class="planPrice"> <span><small>$</small> {{  $companyStripeCustomerQuery->trialSubscription->amount }}</span>/Month</li>
                                <li>
                                    <ul class="planDescriptions">
                                        @if( $companyStripeCustomerQuery->trialSubscription->description)
                                            @foreach( $companyStripeCustomerQuery->trialSubscription->description as $description)
                                                <li> <a href="#numdevices" class="pPlanLinks"></a>{{ $description->description }}</li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </li>
                                <li>
                                    <a href="#" class="btn btn-primary btn-raised btn-sm">Current plan</a>
                                </li>
                            </ul>
                        </li>
                        <div class="clearfix"></div>
                    </ul>
                    <br/>
                @endif
            @endif
            <p class="text-center">
                <strong style="font-size: 16px;">Need more users?</strong><br/>
                Please send an email to <a href="mailto:info@recordtime.com.au" target="_top">info@recordtime.com.au</a> OR<br/> Call us on <strong>0421 955 630</strong>
            </p>
        </div>
    </div>




    @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($companyStripeCustomerQuery->expiry_date)))
        <!-- Modal -->
        <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header themeSecondaryBg">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-dollar"></i>&nbsp;&nbsp;Upgrade Plan</h4>
                    </div>

                    {{--            @if(\Carbon\Carbon::now()->gte(\Carbon\Carbon::parse($companyStripeCustomerQuery->expiry_date)))--}}

                    {{ Form::open(['url' => 'dashboard/company/profile/subscription', 'files' => true, 'id' => 'payment-form']) }}
                    <div class="modal-body">

                        @if(count(@$cards['data'])>0)
                            <input type="hidden" name="plan" id="formPlan">
                            <strong>CURRENT PAYMENT METHOD</strong>
                            <br/>
                            <strong>
                                {{ $cards['data'][0]["brand"] }} ending with {{ $cards['data'][0]["last4"] }}
                            </strong>
                            <a href="{{ route('Company.CreditCard.Update') }}" class="" style="color: #fff;position: absolute;right: 10px;top: 30px;color:#999;font-weight: bold;font-size:12px;">Update Card</a>

                        @else
                            <div id="charge-error" class="alert alert-danger {{ !Session::has('error') ? 'hidden' : ''}}">
                                {{ Session::get('error') }}
                            </div>
                            <div class="row">
                                <input type="hidden" name="plan" id="formPlan">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top: 0px;">
                                        <label class="control-label" for="cardholder-name">Card Holder Name</label>
                                        <input type="text" id="card-name" class="form-control" required name="cardholder-name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="cardholder-name">Card Details</label>
                                        <div id="card-element"></div>
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group" style="margin-top: 0px;">
                                    <label class="control-label" for="userPlan">User Plan</label>
                                    <input type="text" id="userPlan" class="form-control" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group" style="margin-top: 0px;">
                                    <label class="control-label" for="planPrice">User Plan(Per Month)</label>
                                    <input type="text" id="planPrice" class="form-control" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-raised btn-sm" @if(count(@$cards['data'])==0) id="upgradeBtn" @endif style="color: #fff;position: absolute;right: 10px;top: 20px;">Upgrade</button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="margin-top: 10px;padding: 0px 20px 20px;margin-bottom: 20px;">
                        <div class="text-left">
                            <div>
                                <strong>SECURE PAYMENTS</strong><br/>
                                <span>Payments are securely processed each month with Stripe.</span>

                                <ul class="card-list ">
                                    <li><i class="fa fa-cc-visa"></i></li>
                                    <li><i class="fa fa-cc-mastercard"></i></li>
                                    <li><i class="fa fa-cc-discover"></i></li>
                                    <li><i class="fa fa-cc-amex"></i></li>
                                </ul>
                            </div>
                            <div>
                                <div class="pull-left">
                                    <strong>Note</strong><br/>
                                    <span>Record Time(We) do not save your credit card details on our servers. </span><br/>
                                </div>
                                <img src="{{ asset('assets/dashboard/images/StripeBadges/Big/powered_by_stripe.png') }}" style="margin-top:15px;" width="110px" class="pull-right">
                            </div>
                            <div class="clearfix"></div>

                        </div>
                        <div class="clearfix"></div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    @else
        <!-- Modal -->
        <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header themeSecondaryBg">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-dollar"></i>&nbsp;&nbsp;Your Subscription Plan</h4>
                    </div>

                    {{ Form::open(['url' => 'dashboard/company/profile/selectSubscription', 'id' => 'payment-form']) }}
                    <input type="hidden" name="plan" id="formPlan">
                    <div class="modal-body">
                        <strong>30 days trial subscription</strong><br/>
                        <div class="row">

                            <div class="col-md-5">

                                <div class="form-group" style="margin-top: 0px;">
                                    <label class="control-label" for="userPlan">User Plan</label>
                                    <input type="text" id="userPlan" class="form-control" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group" style="margin-top: 0px;">
                                    <label class="control-label" for="planPrice">Cost Per Month</label>
                                    <input type="text" id="planPrice" class="form-control" required value="" readonly>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success btn-raised btn-sm" style="color: #fff;position: absolute;right: 10px;top: 20px;">SUBSCRIBE</button>
                            </div>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>


    @endif

    <!-- Modal -->
    <div class="modal fade" id="freeSubscription" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-dollar"></i>&nbsp;&nbsp;Your Subscription Plan</h4>
                </div>
                <div class="modal-body">
                    {{ Form::open(['url' => 'dashboard/company/profile/freeSubscription']) }}
                    <strong>Free Subscription</strong><br/>
                    <div class="row">

                        <div class="col-md-5">

                            <div class="form-group" style="margin-top: 0px;">
                                <label class="control-label" for="userPlan">User Plan</label>
                                <input type="text" id="userPlan" class="form-control" required value="1" readonly>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group" style="margin-top: 0px;">
                                <label class="control-label" for="planPrice">Cost Per Month</label>
                                <input type="text" id="planPrice" class="form-control" required value="$ 0" readonly>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-raised btn-sm" style="color: #fff;position: absolute;right: 10px;top: 20px;">SUBSCRIBE</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <style>
        .card-list{
            list-style: none;
            margin: 0px;
            padding: 0px;
        }
        .card-list li{
            display: inline-block;
        }
    </style>
@endsection

@section("customScript")
    <script type="text/javascript">
        $(document).ready(function(){
            $(".upgradePlan").on("click",function(e){
                e.preventDefault();
                $("#userPlan").val($(this).attr('dataPlanText'));
                $("#planPrice").val($(this).attr('dataPrice'));
                $("#formPlan").val($(this).attr('dataPlan'));
                $('#myModal').modal('show')
            });
        });
    </script>
    @if(count(@$cards['data'])==0)
        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript" src="{{ asset('assets/dashboard/js/checkout.js') }}"></script>
    @endif



@endsection