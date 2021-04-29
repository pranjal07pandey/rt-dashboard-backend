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
                    @if(\Illuminate\Support\Facades\Auth::user()->first_name!='')
                        {{ \Illuminate\Support\Facades\Auth::user()->first_name }} {{ \Illuminate\Support\Facades\Auth::user()->last_name }}
                    @else
                        {{ \Illuminate\Support\Facades\Auth::user()->email }}
                    @endif
                </h3>
                <h5 class="widget-user-desc">@if(Session::get('adminType')==1) Super Admin @else Admin @endif</h5>
            </div>
            <div class="box-footer no-padding">
                <ul class="nav nav-stacked">
                    <li><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My Subscription</a></li>
                    <li class="active"><a href="#"><i class="fa fa-id-card"></i> My Profile</a></li>
                    <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                    <li><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
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
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-id-card"></i>&nbsp;My Profile</h3>
            {{ Form::open(['url' => 'dashboard/company/profile/', 'files' => true]) }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">First Name</label>
                            <input type="text" name="firstName" class="form-control" required="required" value="{!! $userProfile->first_name !!}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" for="title">Last Name</label>
                            <input type="text" name="lastName" class="form-control" required="required" value="{!! $userProfile->last_name !!}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Email</label>
                            <input type="text" required="required" class="form-control"  value="{!! $companyProfile->userInfo->email !!}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="companyName">Company Name</label>
                            <input type="text" required="required" class="form-control" name="companyName" value="{!! $companyProfile->name !!}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="abn">Company ABN:</label>
                            <input type="text" required="required" class="form-control" name="abn" value="{!! $companyProfile->abn !!}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="contactNumber">Contact Number</label>
                            <input type="text" required="required" class="form-control" name="contactNumber" value="{!! $companyProfile->contactNumber !!}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group label-floating">
                            <label class="control-label" required for="address">Address</label>
                            <input type="text" required="required" class="form-control" name="address" value="{!! $companyProfile->address !!}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group ">
                            <input type="file" id="image" name="image">
                            <input type="text" readonly="" class="form-control" placeholder="Company Logo">
                            <i style="font-size:12px;color:#999;">File Type : jpeg, bmp, png only</i>
                            @if(AmazoneBucket::fileExist($companyProfile->logo))
                                <br>
                                <strong>Current Logo</strong><br/>
                                <img src="{{ AmazoneBucket::url() }}{{ $companyProfile->logo }}" width="100px">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group ">
                            <input type="file" id="image" name="profile"  @if(AmazoneBucket::fileExist($userProfile->image)) @else required @endif>
                            <input type="text" readonly="" class="form-control" placeholder="Profile Image">
                            <i style="font-size:12px;color:#999;">File Type : jpeg, bmp, png only</i>
                            @if(AmazoneBucket::fileExist($userProfile->image))
                                <br>
                                <strong>Current Profile</strong><br/>
                                <img src="{{ AmazoneBucket::url() }}{{ $userProfile->image }}" width="100px">
                            @endif
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