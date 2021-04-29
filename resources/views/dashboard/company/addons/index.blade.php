@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-cart-plus"></i> Add-ons
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">My Subscribed Add-ons</h3>

            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info popupsecond"  >
                    <i class="fa fa-clock-o"></i> Subscription History
                </button>
            </div>
            <div class="clearfix"></div>
            <div class="subscribedAddons">
                <ul>
                    <li>File Manager</li>
                    <li>Prefiller Manager</li>
                </ul>
            </div>
            <div class="addonsList">
                <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Add-ons</h3>
                <div class="row">
                    @if($addons)
                        @foreach($addons as $addon)

                            <div class="col-md-4" >
                                <div style="overflow: hidden;background: #f5f6f7;">
                                    <div style="width: 100%; height: 200px;background:url({{ asset('assets/dashboard/images/messaging.png') }});background-size: 100%;"></div>
                                </div>
                                <strong> {{ $addon->title }}</strong>
                            </div>
                        @endforeach
                    @endif
                    <div class="col-md-4" >
                        <div style="overflow: hidden;background: #f5f6f7;">
                            <div style="width: 100%; height: 200px;background:url({{ asset('assets/dashboard/images/messaging.png') }});background-size: 100%;"></div>
                        </div>
                        <strong>Messaging</strong>


                    </div>
                    <div class="col-md-4">
                        <div style="overflow: hidden;background: #f5f6f7;">
                            <div style="width: 100%; height: 200px;background:url({{ asset('assets/dashboard/images/prefiller.png') }});background-size: 138%;background-position: -1px 4px;transform: rotate(-2deg);"></div>
                        </div>
                        <strong>File Manager</strong>

                    </div>
                    <div class="col-md-4">
                        <div style="overflow: hidden;background: #f5f6f7;">

                            <div style="width: 100%; height: 200px;background:url({{ asset('assets/dashboard/images/documentManager.png') }});background-size: 138%;background-position: -300px 4px;transform: rotate(-10deg);"></div>

                        </div>
                    </div>
                </div>
            </div>
            <style>
                .subscribedAddons{
                    margin-top: 15px;
                    margin-bottom: 35px;
                }
                .subscribedAddons ul{
                    margin: 0px 15px;
                    padding: 0px;
                }
                .subscribedAddons ul li{
                    margin-bottom: 10px;
                }

                .addonsList strong{
                    display: block;
                    margin: 10px;
                }
            </style>
        </div>
    </div>
    <br/><br/>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{asset('assets/dashboard/tour/script.js')}}"></script>
@endsection