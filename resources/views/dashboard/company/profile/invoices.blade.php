@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> Stripe
            <small>Invoices</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('companyProfile') }}">Profile</a></li>
            <li class="active">Stripe Invoices</li>
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
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li ><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li class="active"><a href="{{ url('dashboard/company/profile/stripeInvoices') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li> 
                        <li><a href="{{ url('dashboard/company/profile/xeroSetting') }}"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Xero Setting</a></li> 
                        <li><a href="{{ route('Company.timezone') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Timezone</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h3  class="pull-left" style="font-size: 20px; margin: 15px 0px 10px;font-weight: 500;display:inline-block"> <i class="fa fa-usd"></i>&nbsp;My Invoices</h3>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Date of Payment</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $row)
                        <tr>
                            <td>#{{ $row->number }}</td>
                            <td>{{ \Date('d M Y',intval($row->date)) }}</td>
                            <td>A$&nbsp;{{ $row->total/100 }}</td>
                            <td>@if($row->paid == true) <span class="label label-success">Paid</span> @else span class="label label-danger">Un-paid</span> @endif</td>
                            <td>
                                <a href="{{ $row->hosted_invoice_url }}" target="_blank"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;
                                <a href="{{ $row->invoice_pdf }}" target="_blank"><i class="fa fa-file-pdf-o"></i></a>
                            </td>
                        </tr>
                    @endforeach
                    @if(count(@$invoices)==0)
                        <tr>
                            <td colspan="5">
                                <center>Data Empty</center>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div><br/><br/>
@endsection