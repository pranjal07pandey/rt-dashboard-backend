@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-user-circle-o"></i> My Account
            <small>Update Account</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('companyProfile') }}">Profile</a></li>
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
                        <li class="active"><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My Subscription</a></li>
                        <li><a href="{{ route('companyProfile') }}"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li ><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li><a href="{{ route('Company.billingHistory') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li>
                        <li><a href="{{ url('dashboard/company/profile/xeroSetting') }}"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Xero Setting</a></li> 
                        <li><a href="{{ route('Company.timezone') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Timezone</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3  class="pull-left" style="font-size: 20px; margin: 15px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-id-card"></i>&nbsp;My Subscription</h3>
            @if($status=="active")<button  class="pull-right btn btn-danger btn-raised btn-sm upgradePlan" data-toggle="modal" data-target="#myModal">Cancel Subscription</button>@endif
            <div class="clearfix"></div>

            @if(!Session::get('isTrial'))
                {{--<button class="pull-right btn-xs btn-primary">Payment Log</button>--}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">Company ABN</label>
                            <input type="text" name="companyABN" class="form-control" required="required" value="{{ $company->abn }}"  disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="address">Account Status</label>
                            <input type="text" required="required" class="form-control" name="address" value="Active" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="contactNumber">Subscription Since</label>
                            <input type="text" required="required" class="form-control" name="contactNumber" value="{{ \Carbon\Carbon::parse($company->created_at)->format('d-M-Y') }}" disabled="disabled">
                        </div>
                    </div>
                    @if($status!="subscriptionCancel")
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">@if($status=="trialing" || $status=="no_stripe_subscription") Trial Expiry Date @else Next Payment Date @endif</label>
                            <input type="text" name="nextPaymentDate" class="form-control" required="required"
                                   value="@if($status=='freeSubscription')Free Subscription @else @if($status=="trialing" || $status=="no_stripe_subscription" ) {{ \Carbon\Carbon::parse($company['expiry_date'])->format('d-M-Y') }} @else {{ \Carbon\Carbon::createFromTimestamp($subscriptionPlan['current_period_end'])->format('d-M-Y') }} @endif @endif" disabled="disabled">
                        </div>
                    </div>
                    @endif

                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Max. Users</label>
                            <input type="text" required="required" class="form-control" name="companyName" value="@if($status=='freeSubscription')1 @else @if($status=="trialing" || $status=="no_stripe_subscription") {{ $company->max_user }} @else {{ $company->subscription->max_user }} @endif @endif" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Max. Number of Devices included</label>
                            <input type="text" required="required" class="form-control" name="companyName" value="@if($status=='freeSubscription')1
                                @else @if($status=="trialing" || $status=="no_stripe_subscription") {{ $company->max_user }} @else {{ $company->subscription->max_user }} @endif @endif" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="abn">Plan Details</label>
                            <input type="text" required="required" class="form-control" name="abn" value="@if($status=='freeSubscription')Single User(5 Dockets/Month)
                            @else @if($status=="trialing" || $status=="no_stripe_subscription" || $status=="subscriptionCancel") {{  $company->trialSubscription->name }} @else {{  $company->trialSubscription->name }} @endif @endif" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br/>
                        <a href="{{ route('Company.Subscription.Upgrade') }}" class="btn btn-success btn-raised btn-sm upgradePlan">Upgrade</a>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">Company ABN</label>
                            <input type="text" name="companyABN" class="form-control" required="required" value="{{ $company->abn }}"  disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="address">Account Status</label>
                            <input type="text" required="required" class="form-control" name="address" value="Active" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="contactNumber">Subscription Since</label>
                            <input type="text" required="required" class="form-control" name="contactNumber" value="{{ \Carbon\Carbon::createFromTimestamp($subscriptionPlan['start'])->format('d-M-Y') }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">Next Payment Date</label>
                            <input type="text" name="nextPaymentDate" class="form-control" required="required" value="{{ \Carbon\Carbon::createFromTimestamp($subscriptionPlan['current_period_end'])->format('d-M-Y') }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Total Users</label>
                            <input type="text" required="required" class="form-control" name="companyName" value="{{ $company->subscription->max_user }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Number of Devices included</label>
                            <input type="text" required="required" class="form-control" name="companyName" value="{{ $company->subscription->max_user }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="abn">Plan Details</label>
                            <input type="text" required="required" class="form-control" name="abn" value="{{ $subscriptionPlan["plan"]->name }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <br/>
                        <a href="{{ route('Company.Subscription.Upgrade') }}" class="btn btn-success btn-raised btn-sm upgradePlan">Upgrade</a>
                    </div>
                </div>
            @endif
        </div>
    </div><br/><br/>

    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-dollar"></i>&nbsp;&nbsp;Cancel Subscription</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Are your sure you want to cancel Record Time subscription?</strong>

                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 10px;padding: 0px 20px 20px;margin-bottom: 20px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                    <a href=" {{ route('Company.Subscription.Cancel') }}" class="btn btn-primary" style="margin: 0px;">Yes</a>
                </div>
            </div>
        </div>
    </div>
@endsection