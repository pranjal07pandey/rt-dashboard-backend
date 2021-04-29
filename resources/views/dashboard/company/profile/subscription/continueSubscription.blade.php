@extends('layouts.companyDashboard')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <section class="content-header">
                <h1>
                    <i class="fa fa-bookmark"></i> Pick a suitable plan
                </h1>
            </section>
            @include('dashboard.company.include.flashMessages')
            <div class="subscriptionDetailsWrapper">
                <div>
                    <strong style="padding-bottom: 10px;">Your last subscription : {{ $company->trialSubscription()->first()->name."($".$company->trialSubscription()->first()->amount."/month)" }}</strong>
                    <a href="#" class="pull-right btn btn-success btn-raised btn-sm upgradePlan" style="background: #fff;color: #08b3ba;margin:0px" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($company->trialSubscription()->first()->id) }}" dataPlanText="{{ $company->trialSubscription()->first()->name }}" dataPrice="$ {{ $company->trialSubscription()->first()->amount }}">
                        Reactivate
                    </a>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>
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
                            <a href="#" class="btn btn-success btn-raised btn-sm" data-toggle="modal" data-target="#freeSubscription">Free</a>
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
                                    <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($plan->id) }}" dataPlanText="{{ $plan->name }}" dataPrice="$ {{ $plan->amount }}">
                                        Subscribe
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @endforeach
                @endif
                <div class="clearfix"></div>
            </ul>
            @if($company->trialSubscription()->first()->type==1)
                <section class="content-header">
                    <h1>
                        <i class="fa fa-bookmark"></i> Custom Plan
                    </h1>
                </section>
                <ul class="planDetails">
                    <li class="smallToMedPlan plan">
                        <ul>
                            <li class="planTitle">
                                <h2>{{ $company->trialSubscription()->first()->name }}</h2>
                            </li>
                            <li class="planPrice"> <span><small>$</small> {{  $company->trialSubscription()->first()->amount }}</span>/Month</li>
                            <li>
                                <ul class="planDescriptions">
                                    @if(  $company->trialSubscription()->first()->description)
                                        @foreach( $company->trialSubscription()->first()->description as $description)
                                            <li> <a href="#numdevices" class="pPlanLinks"></a>{{ $description->description }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan"  dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($company->trialSubscription()->first()->id) }}" dataPlanText="{{ $company->trialSubscription()->first()->name }}" dataPrice="$ {{ $company->trialSubscription()->first()->amount }}">
                                    Reactivate
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <br/>
            @endif
            <p class="text-center">
                <strong style="font-size: 16px;">Need more users?</strong><br/>
                Please send an email to <a href="mailto:info@recordtime.com.au" target="_top">info@recordtime.com.au</a> OR<br/> Call us on <strong>0421 955 630</strong>
            </p>
        </div>

        <!-- Modal -->
        <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header themeSecondaryBg">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-dollar"></i>&nbsp;&nbsp;Subscribe Plan</h4>
                    </div>
                    {{ Form::open(['url' => 'dashboard/company/profile/continueSubscription', 'files' => true, 'id' => 'payment-form']) }}
                    <div class="modal-body">

                        @if(count($cards['data'])>0)
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
                                <button type="submit" class="btn btn-success btn-raised btn-sm" @if(count($cards['data'])==0) id="upgradeBtn" @endif style="color: #fff;position: absolute;right: 10px;top: 20px;">Subscribe</button>
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
            @if(count($cards['data'])==0)
                <script src="https://js.stripe.com/v3/"></script>
                <script type="text/javascript" src="{{ asset('assets/dashboard/js/checkout.js') }}"></script>
    @endif

@endsection