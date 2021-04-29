@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-user-circle-o"></i> My Account
            <small>Update Account</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Profile</a></li>
            <li class="active">Update Account</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">
        <div class="col-md-4">
            <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header themePrimaryBg">
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}" alt="User Avatar" style="height: 65px;">
                    </div>
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username">
                        @if(auth()->user()->first_name!='')
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </h3>
                    <h5 class="widget-user-desc">@if(Session::get('adminType')==1) Super Admin @else Admin @endif</h5>
                </div>
                <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                        <li><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My Subscription</a></li>
                        <li><a href="{{ route('companyProfile') }}"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li class="active"><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li><a href="{{ route('Company.billingHistory') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li>
                        <!-- <li><a href="#"><i class="fa fa-calendar-minus-o"></i> Payment Plans </a></li> -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8" style="min-height:500px;">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-credit-card-alt"></i>&nbsp;Update Payment Method</h3>
            @if(count($cards['data'])>0)
                <button class="btn btn-success btn-raised btn-sm updateCard"  id="updateCard" style="color: #fff;position: absolute;right: 10px;top: -10px;font-weight: bold;font-size:12px;">Update Card</button>
                <button class="btn btn-danger btn-raised btn-sm updateCard"  id="backBtn" style="display:none;color: #fff;position: absolute;right: 10px;top: -10px;font-weight: bold;font-size:12px;">Back</button>
            @endif
        @if(count($cards['data'])>0)
            <div id="currentCardDetails" style="position: absolute;top: 40px;width: calc(100% - 15px);">
                <div class="pull-left">
                    <strong>CURRENT PAYMENT METHOD</strong>
                    <br/>
                    <strong>
                        {{ $cards['data'][0]["brand"] }} ending with {{ $cards['data'][0]["last4"] }}
                    </strong>
                </div>
                <a class="btn btn-danger btn-raised btn-sm" href="{{ route('Company.CreditCard.Remove') }}"  style="color: #fff;position: absolute;right: 10px;top: -10px;font-weight: bold;font-size:12px;">Remove Card Details</a>
                <div class="clearfix"></div>
                <div>
                    <div class="text-left" style="margin-top: 40px;margin-bottom: 20px;">
                        <div>
                            <strong>SECURE PAYMENTS</strong><br>
                            <span>Payments are securely processed each month with Stripe.</span>

                            <ul class="card-list ">
                                <li><i class="fa fa-cc-visa"></i></li>
                                <li><i class="fa fa-cc-mastercard"></i></li>
                                <li><i class="fa fa-cc-discover"></i></li>
                                <li><i class="fa fa-cc-amex"></i></li>
                            </ul>
                        </div>
                        <div>
                            <div class="pull-left"><br/>
                                <strong>Note</strong><br>
                                <span>Record Time(We) do not save your credit card details on our servers. </span><br>
                            </div>
                            <img src="{{ asset('assets/dashboard/images/StripeBadges/Big/powered_by_stripe.png') }}" style="margin-top:35px;maring-right:10px;" width="110px" class="pull-right">
                        </div>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div>
            @endif
            <div    id="updateCardForm" @if(count($cards['data'])>0)style="display: none;position: absolute;top:20px;"@endif>
                <div id="charge-error" class="alert alert-danger {{ !Session::has('error') ? 'hidden' : ''}}">
                    {{ Session::get('error') }}
                </div>
                <div  class="row">
                    {{ Form::open(['url' => 'dashboard/company/profile/updateCreditCard', 'id' => 'payment-form']) }}
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top: 0px;">
                            <label class="control-label" for="cardholder-name">Card Holder Name</label>
                            <input type="text" id="card-name" class="form-control" required name="cardholder-name">
                        </div>
                    </div>
                    <div class="col-md-10">
                        <div class="form-group" style="margin-top:0px;">
                            <label class="control-label" for="cardholder-name">Card Details</label>
                            <div id="card-element" style="margin-top: 10px;"></div>
                            <div id="card-errors" role="alert" style="margin-top:10px;color:red;"></div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success btn-raised btn-sm" @if(count($cards['data'])==0) id="upgradeBtn" @endif style="color: #fff;margin-top:50px;">Update</button>
                    </div>
                    <div class="col-md-12">
                        <div class="text-left" style="margin-top: 40px;margin-bottom: 20px;">
                            <div>
                                <strong>SECURE PAYMENTS</strong><br>
                                <span>Payments are securely processed each month with Stripe.</span>

                                <ul class="card-list ">
                                    <li><i class="fa fa-cc-visa"></i></li>
                                    <li><i class="fa fa-cc-mastercard"></i></li>
                                    <li><i class="fa fa-cc-discover"></i></li>
                                    <li><i class="fa fa-cc-amex"></i></li>
                                </ul>
                            </div>
                            <div>
                                <div class="pull-left"><br/>
                                    <strong>Note</strong><br>
                                    <span>Record Time(We) do not save your credit card details on our servers. </span><br>
                                </div>
                                <img src="{{ asset('assets/dashboard/images/StripeBadges/Big/powered_by_stripe.png') }}" style="margin-top:35px;" width="110px" class="pull-right">
                            </div>
                            <div class="clearfix"></div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
</div><br/><br/>
    <style>
        /**
    * The CSS shown here will not be introduced in the Quickstart guide, but shows
    * how you can use CSS to style your Element's container.
    */
        .StripeElement {
            background-color: white;
            height: 40px;
            padding: 10px 12px;
            border-radius: 4px;
            border: 1px solid transparent;
            box-shadow: 0 1px 3px 0 #e6ebf1;
            -webkit-transition: box-shadow 150ms ease;
            transition: box-shadow 150ms ease;
        }

        .StripeElement--focus {
            box-shadow: 0 1px 3px 0 #cfd7df;
        }

        .StripeElement--invalid {
            border-color: #fa755a;
        }

        .StripeElement--webkit-autofill {
            background-color: #fefde5 !important;
        }
        .ui-tooltip{
            display: none;
        }

        .card-list{
            margin: 0px;
            padding: 0px;
        }
        .card-list li {
            display: inline-block;
            margin: 0px 15px 0px 0px;
            overflow: hidden;
            font-size: 30px;
        }
        .planDetails{
            list-style:none;
            margin: 20px 0px 10px;
            padding: 0px;
            clear: both;
        }
        .plan {
            float: left;
            background-color: #fafafa;
            border-radius: 3px;
            margin: 0px 0px 10px 0px;
            text-align: center;
            border: 1px solid #eaeaea;
            height: 420px;
        }
        .plan li {
            padding: 10px 0 10px 0;
        }
        .plan ul{
            margin: 0px;
            padding: 0px;
            list-style: none;
        }
        .planTitle {
            background-color: #fff;
        }
        .planTitle h2{
            font-size: 20px;
            font-weight: 500;
        }
        .planPrice {
            background-color: #F0F0F0;
            color: #000;
        }
        .freemonth {
            font-size: 14px;
        }
        .planTitle{
            font-weight: 300;
            line-height: 1.1;
            margin-bottom: .8em;
        }
        .highlightPlan {
            background-color: #0C3455;
            color: #fff;
        }
        .planPrice span {
            font-size: 30px;
        }
    </style>
@endsection
@section('customScript')
    <script src="https://js.stripe.com/v3/"></script>
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/checkout.js') }}"></script>
    <script>
        $(document).ready(function(){
            $(".updateCard").on('click',function(){
                $("#backBtn").fadeToggle();
                $("#updateCard").fadeToggle();
                $("#currentCardDetails").fadeToggle();
                $("#updateCardForm").fadeToggle();
            });
        });
    </script>
@endsection
