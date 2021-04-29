@extends('layouts.companyDashboard')

@section('content')
    <div class="col-md-12">
        <section class="content-header">
            <h1>
                <i class="fa fa-dollar"></i> Record Time Subscription Plan
            </h1>
            <ol class="breadcrumb hidden-sm hidden-xs">
                <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">Select Plan</li>
            </ol>
        </section>
        @include('dashboard.company.include.flashMessages')
        <div class="subscriptionDetailsWrapper">
            <div class="pull-left" style="margin-right:20px;">
                <strong style="padding-bottom: 10px;margin-bottom: 20px;"><i class="fa fa-calendar"></i> Current Plan</strong>
                <div class="planDetailBullet">
                    30 Days Trial Subscription
                </div>

            </div>
            <div class="pull-right">
                <div style="margin-top: 45px;font-size: 12px;">
                    <i> Trial Subscription : {{ \Carbon\Carbon::now()->format('d-M-Y') }} to {{ \Carbon\Carbon::now()->addDay(30)->format('d-M-Y') }}</i>
                </div>
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
            @if($subscriptionPlans)
                <li class="smallToMedPlan plan">
                    <ul>
                        <li class="planTitle">
                            <h2>Free User Plan</h2>
                        </li>
                        <li class="planPrice"> <span>$ 0</span>/Month</li>
                        <li>
                            <ul class="planDescriptions">
                                <li><a href="#numdevices" class="pPlanLinks"></a>Send 20 Dockets a month</li>
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
                @foreach($subscriptionPlans as $plan)
                    <li class="smallToMedPlan plan">
                        <ul>
                            <li class="planTitle">
                                <h2>{{ $plan->name }}</h2>
                            </li>
                            <li class="planPrice"> <span>$ {{ $plan->amount }}</span>/Month</li>
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
                                <a href="#" class="btn btn-success btn-raised btn-sm upgradePlan" dataPlan="{{ \Illuminate\Support\Facades\Crypt::encryptString($plan->id) }}" dataPlanText="{{ $plan->name }}" dataPrice="$ {{ $plan->amount }}">Free Trial</a>
                            </li>
                        </ul>
                    </li>
                @endforeach
            @endif
        </ul>
        <div class="clearfix"></div>
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

@endsection