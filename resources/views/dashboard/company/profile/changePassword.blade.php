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
                        <img class="img-circle" src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}" alt="User Avatar">
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
                        <li><a href="#"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li class="active"><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li><a href="{{ route('Company.billingHistory') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li>
                        <li><a href="{{ url('dashboard/company/profile/xeroSetting') }}"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Xero Setting</a></li> 
                        <li><a href="{{ route('Company.timezone') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Timezone</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-id-card"></i>&nbsp;Change Password</h3>
            {{ Form::open(['url' => 'dashboard/company/profile/changePassword']) }}
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group" style="margin-top:0px;">
                        <label class="control-label" for="title">Old Password</label>
                        <input type="password" class="form-control" name="old_password" required>
                    </div>
                    <div class="form-group" style="margin-top:0px;">
                        <label class="control-label" for="title">New Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="form-group" style="margin-top:0px;">
                        <label class="control-label" for="title">Retype new password</label>
                        <input type="password" class="form-control" name="retypePassword"required>
                    </div>
                </div>
            </div>
            <div class="col-md-12"><br/>
                <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-upload"></i> Update</button>
            </div>

            {{ Form::close() }}
        </div>
    </div><br/><br/>
@endsection