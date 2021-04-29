@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> Xero
            <small>Detail</small>
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
                        <img class="img-circle" src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}"
                             alt="User Avatar" style="height: 65px;">
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
                        <li><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My
                                Subscription</a></li>
                        <li><a href="{{ route('companyProfile') }}"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i>
                                Invoice Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i>
                                Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change
                                Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i>
                                Update Credit Card </a></li>
                        <li><a href="{{ url('dashboard/company/profile/stripeInvoices') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing
                                History</a></li>

                        <li class="active"><a href="{{ url('dashboard/company/profile/xeroSetting') }}"><i
                                        class="fa fa-cogs"></i>&nbsp;&nbsp;Xero Setting</a></li>


                    </ul>
                </div>
            </div>
        </div>


        @if($xeroUserDetail->count() == 0)
            <div class="col-md-8">
                <h3 class="pull-left"
                    style="font-size: 20px; margin: 15px 0px 10px;font-weight: 500;display:inline-block"><i
                            class="fa fa-usd"></i>&nbsp; Xero Profile </h3>
                <div class="d-flex justify-content-center">
                    <div style="     width: 98%;margin-top: 18px;border: 1px solid #adadad52;box-shadow:none;"
                         class="card col-md-5">
                        <div class="card-body">
                            <h5 class="card-title">Connect to Xero</h5>
                            <p class="card-text">To continue, connect an organisation with your application.</p>
{{--                            @if ($xeroUserDetail->first()->payroll_status == 1)--}}
{{--                                    <a style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised btn-info pull-right" onclick="window.location.href='{{url('dashboard/company/xero/connect/1')}}'">Connection to Xero</a>--}}
{{--                            @else--}}
                                    <a class="btn btn-xs btn-raised btn-info pull-right" style="margin: 16px 17px 0px 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connection to Xero</a>
{{--                            @endif--}}
                            <br/><br/>
                            <p><i class="fa fa-info-circle" aria-hidden="true"></i> This is for syncing invoices and timesheets</p>
                        </div>
                    </div>

                </div>
            </div>

        @else
            <div class="col-md-8">
                <h3 class="pull-left"
                    style="font-size: 20px; margin: 15px 0px 10px;font-weight: 500;display:inline-block"><i
                            class="fa fa-usd"></i>&nbsp; Xero Profile </h3>

                {{--<a href=""><img src="{{asset('assets/xeroconnection.png')}}"></a>--}}
                @if(\Illuminate\Support\Facades\Session::get('xero_oauth')==null)


                        @if ($xeroUserDetail->first()->payroll_status == 1)
                                <a style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised btn-info pull-right" onclick="window.location.href='{{url('dashboard/company/xero/connect/1')}}'">Connection to Xero</a>
                        @else
                                <a class="btn btn-xs btn-raised btn-info pull-right" style="margin: 16px 17px 0px 0px;" data-toggle="modal" data-target="#connectionxeropopup" >Connection to Xero</a>
                        @endif


                @else
                    @if(\Illuminate\Support\Facades\Session::get('xero_oauth')->expires != "")
                        <a style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised btn-danger pull-right"
                           href="{{url('dashboard/company/xero/disconnected')}}">Disconnect Xero</a>
                    @endif


                @endif
                @if(Session::get('company_id')==1)
                    <a style="margin: 16px 17px 0px 0px;" class="btn btn-xs btn-raised btn-warning pull-right"
                       href="{{url('dashboard/company/xero/reset/'.\Illuminate\Support\Facades\Session::get('company_id'))}}">Reset</a>
                @endif


                <div class="col-md-12">
                    <h5 style="    font-weight: 600;">&#9679; Xero User Information</h5>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                         Email
                     </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        {{$xeroUserDetail->first()->xero_email}}
                     </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                         Name
                     </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        {{$xeroUserDetail->first()->xero_user_first_name}} {{$xeroUserDetail->first()->xero_user_last_name}}
                     </span>
                    </div>
                </div>

                <div class="col-md-12">
                    <h5 style="    font-weight: 600;">&#9679; Linked Organization</h5>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        Name
                    </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                       {{$xeroUserDetail->first()->xero_organization_name}}
                    </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        Line Of Business
                    </span>
                    </div>
                    <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                         {{$xeroUserDetail->first()->organization_line_of_business}}

                    </span>
                    </div>


                    <?php
                    $demo = $xeroUserDetail->first()->xero_organization_contact;
                    $test = unserialize($demo);


                    $add = $xeroUserDetail->first()->xero_organination_address;
                    $addData = unserialize($add);



                    ?>
                    <div class="col-md-12"
                         style="border: 1px solid #80808038;padding: 9px 0 9px 15px;    border-bottom: none;">
                        <span style="font-size: 15px;font-weight: 500;">Address</span>
                    </div>
                    @if($addData != null)
                        @if(count($addData)==1)
                            <div class="col-md-12"
                                 style="border: 1px solid #80808038;padding: 9px 0 9px 15px;    border-top: none;    min-height: 211px;">
                         <span style="font-size: 15px;font-weight: 500;">
                           <span style="font-size: 15px;font-weight: 500; ">
                            @if (array_key_exists('AddressType', $addData[0]))
                                   <h5>{{$addData[0]['AddressType']}}</h5>
                               @endif
                               <ul>
                                   @if (array_key_exists('AddressLine1', $addData[0]))
                                       <li>Address: {{$addData[0]['AddressLine1']}}</li>
                                   @endif
                                   @if (array_key_exists('City', $addData[0]))
                                       <li>City: {{$addData[0]['City']}}</li>
                                   @endif
                                   @if (array_key_exists('Region', $addData[0]))
                                       <li>Region: {{$addData[0]['Region']}}</li>
                                   @endif
                                   @if (array_key_exists('PostalCode', $addData[0]))
                                       <li>PostalCode: {{$addData[0]['PostalCode']}}</li>
                                   @endif
                                   @if (array_key_exists('Country', $addData[0]))
                                       <li>Country: {{$addData[0]['Country']}}</li>
                                   @endif
                                   @if (array_key_exists('AttentionTo', $addData[0]))
                                       <li>AttentionTo: {{$addData[0]['AttentionTo']}}</li>
                                   @endif
                              </ul>
                          </span>
                        </span>
                            </div>
                        @else
                            <div class="col-md-6"
                                 style="border: 1px solid #80808038;padding: 9px 0 9px 15px;    border-top: none;    min-height: 211px;">
                         <span style="font-size: 15px;font-weight: 500;">
                           <span style="font-size: 15px;font-weight: 500; ">
                            @if (array_key_exists('AddressType', $addData[0]))
                                   <h5>{{$addData[0]['AddressType']}}</h5>
                               @endif
                               <ul>
                                   @if (array_key_exists('AddressLine1', $addData[0]))
                                       <li>Address: {{$addData[0]['AddressLine1']}}</li>
                                   @endif
                                   @if (array_key_exists('City', $addData[0]))
                                       <li>City: {{$addData[0]['City']}}</li>
                                   @endif
                                   @if (array_key_exists('Region', $addData[0]))
                                       <li>Region: {{$addData[0]['Region']}}</li>
                                   @endif
                                   @if (array_key_exists('PostalCode', $addData[0]))
                                       <li>PostalCode: {{$addData[0]['PostalCode']}}</li>
                                   @endif
                                   @if (array_key_exists('Country', $addData[0]))
                                       <li>Country: {{$addData[0]['Country']}}</li>
                                   @endif
                                   @if (array_key_exists('AttentionTo', $addData[0]))
                                       <li>AttentionTo: {{$addData[0]['AttentionTo']}}</li>
                                   @endif
                              </ul>
                          </span>
                        </span>
                            </div>
                            <div class="col-md-6"
                                 style="border: 1px solid #80808038;padding: 9px 0 9px 15px;    border-top: none; min-height: 211px;">
                            <span style="font-size: 15px;font-weight: 500;">
                              <span style="font-size: 15px;font-weight: 500; ">
                                    @if (array_key_exists('AddressType', $addData[1]))
                                      <h5>{{$addData[1]['AddressType']}}</h5>
                                  @endif

                                  <ul>
                                            @if (array_key_exists('AddressLine1', $addData[1]))
                                          <li>Address: {{$addData[1]['AddressLine1']}}</li>
                                      @endif
                                      @if (array_key_exists('City', $addData[1]))
                                          <li>City: {{$addData[1]['City']}}</li>
                                      @endif
                                      @if (array_key_exists('Region', $addData[1]))
                                          <li>Region: {{$addData[1]['Region']}}</li>
                                      @endif
                                      @if (array_key_exists('PostalCode', $addData[1]))
                                          <li>PostalCode: {{$addData[1]['PostalCode']}}</li>
                                      @endif
                                      @if (array_key_exists('Country', $addData[1]))
                                          <li>Country: {{$addData[1]['Country']}}</li>
                                      @endif
                                      @if (array_key_exists('AttentionTo', $addData[1]))
                                          <li>AttentionTo: {{$addData[1]['AttentionTo']}}</li>
                                      @endif
                                        </ul>
                               </span>
                            </span>
                            </div>

                        @endif

                    @else
                        <div class="col-md-12"
                             style="border: 1px solid #80808038;padding: 9px 0 9px 15px;    border-top: none;">
                            <span style="font-size: 15px;font-weight: 500;">Empty Data</span>
                        </div>
                    @endif





                    @if(isset($test[0]))
                        @if (array_key_exists('PhoneType', $test[0]))
                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        {{$test[0]['PhoneType']}}
                    </span>
                            </div>

                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                               <span style="font-size: 15px;font-weight: 500;"> @if (array_key_exists('PhoneCountryCode', $test[0])) {{$test[0]['PhoneCountryCode']}}- @endif

                                   @if (array_key_exists('PhoneNumber', $test[0])) {{$test[0]['PhoneNumber']}}@endif

                               </span>
                            </div>
                        @endif
                    @endif

                    @if(isset($test[1]))
                        @if (array_key_exists('PhoneType', $test[1]))
                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        {{$test[1]['PhoneType']}}
                    </span>
                            </div>

                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">

                             @if (array_key_exists('PhoneCountryCode', $test[1])) {{$test[1]['PhoneCountryCode']}}- @endif

                         @if (array_key_exists('PhoneNumber', $test[1])) {{$test[1]['PhoneNumber']}}@endif

                    </span>
                            </div>
                        @endif
                    @endif
                    @if(isset($test[2]))
                        @if (array_key_exists('PhoneType', $test[2]))
                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">
                        {{$test[2]['PhoneType']}}
                    </span>
                            </div>

                            <div class="col-md-6" style="border: 1px solid #80808038;padding: 9px 0 9px 15px;">
                     <span style="font-size: 15px;font-weight: 500;">

                             {@if (array_key_exists('PhoneCountryCode', $test[2])) {{$test[2]['PhoneCountryCode']}}- @endif
                         @if (array_key_exists('PhoneNumber', $test[2])) {{$test[2]['PhoneNumber']}}@endif

                    </span>
                            </div>
                        @endif
                    @endif


                    <div class="col-md-12" style="height: 32px;">
                    </div>
                    <p><i class="fa fa-info-circle" aria-hidden="true"></i> This is for syncing invoices and timesheets</p>
                </div>


            </div>

        @endif
    </div><br/><br/>
@endsection