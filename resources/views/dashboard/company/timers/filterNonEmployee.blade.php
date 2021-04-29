@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> Timers Management
            <small>View Timer</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Timer Management</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">
        <ul class="horizontalMenuTab">
            <li ><a href="{{ route('timers') }}" >All Timer</a></li>
            <li class="active" ><a href="{{ route('timers.nonemployee') }}" >Non Employee Timers</a></li>
        </ul><!--/.horizontalMenuTab-->
        <div class="col-md-12">
            <div class="filterDiv" style="margin-bottom: 20px;">
                {{ Form::open(['url' => 'dashboard/company/timers/filterNonEmployeeTimer/', 'files' => true]) }}
                <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: 10px;padding-bottom: 5px;">Timer Filter </strong>
                <button type="submit" class="btn btn-info btn-xs btn-raised" style="position: absolute;top: -8px;right: 15px;margin: 0px;">Filter</button>
                <div class="row">
                    <div class="col-md-12">
                        <div  style="border-bottom: 1px solid #ddd;    background-color: #f6f6f6;padding: 15px;">
                            <div class="row">
                                <div class="col-md-12">
                                    <strong>Date</strong>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" style="margin-top:0px;">
                                                <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from"  id="fromDatePicker" placeholder="From" value="{{$request->from}}" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="margin-top:0px;">
                                                <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" name="to" placeholder="To" value="{{$request->to}}" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <strong>Clients</strong>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-top:0px;">
                                                <select id="framework" class="form-control" multiple  name="client[]">
                                                    {{--<option value="">Select Employee</option>--}}
                                                    @if($uniqueTimersClient)
                                                        @foreach($uniqueTimersClient as $row)
                                                            <option value="{{$row->id}}" @if(in_array($row->id,$request->client)) selected  @else  @endif>{{$row->first_name}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <strong>Location</strong>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-top:0px;">
                                                <input type="text" class="form-control"   name="location" placeholder="Location" value="{{$request->location}}" >

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <strong>Tags</strong>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-top:0px;">
                                                <input type="text" class="form-control"  name="tags" placeholder="tags" value="{{$request->tags}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <strong>Duration</strong>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-top:0px;">
                                                <input type="text" id="range_08" name="duration" value=""  value="{{$request->duration}}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
            </div>

            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Non Employee Timers</h3>

            <div class="clearfix"></div>
            <table class="table">
                <thead>
                <tr>
                    <th style="width: 544px;">Details</th>
                    <th>Break Details</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                    @if($timerQuries)
                        @foreach($timerQuries as $row)
                            @php
                                $timerLogs = @@\App\TimerLog::where('timer_id', $row->id)->get();
                                $count = 1;
                                $totalInterval = 0;
                            @endphp
                            <tr>
                                <td style="    padding: 24px 14px 14px 13px;">
                                    <div style="position: absolute;    margin-top: 26px; margin-left: 35px;" >
                                        <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">Total</p>
                                        <p style="margin: 0;margin-top: -5px;margin-bottom: -5px;"><b style="font-size: 18px;">{{$row->total_time}}</b></p>

                                        <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">#{{$row->id}}</p>
                                    </div>
                                    <svg style="float: left;    float: left;margin-left: -34px;margin-top: -36px;" class="circle-chart" viewbox="0 0 33.83098862 33.83098862" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                                        <circle class="circle-chart__background"  stroke="#efefef" stroke-width="1" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
                                        <circle class="circle-chart__circle" stroke="#00acc1" stroke-width="1" stroke-dasharray="{{ gmdate("s", @$date)}},100" stroke-linecap="square" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
                                    </svg>
                                    <div style="float: right; margin-left: 0px;">
                                        <p style="margin: 0;font-size: 15px;font-weight: 600;">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</p>
                                        <p style="margin: 0;"><i style="font-size: 18px;" class="fa fa-map-marker" aria-hidden="true"></i>   {{ $row->location }}</p>
                                        <p><i style="font-size: 18px;" class="fa fa-clock-o" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($row->time_started)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_started)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($row->time_ended)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_ended)->format('H:i:s') }}</p>

                                        {{--<span># {{$items->tag}}</span>--}}
                                        <h5 style="font-weight: 600;">Tags</h5>
                                        <ul style="list-style-type: none;margin: 0; margin-top: -6px;padding: 0;overflow: hidden; width: 340px;" >
                                            @foreach($row->timerAttachedTag as $items)
                                                <li style="    display: block; float: left;    margin-right: 8px;"><span>&#8226; {{$items->tag}}</span></li>
                                            @endforeach
                                        </ul>

                                        <br>

                                        <button type="button" class="btn btn-info btn-xs btn-raised" data-toggle="modal" data-target="#myModal" data-lat='{{ $row->latitude }}' data-lng='{{ $row->longitude }}'>
                                            View Map
                                        </button>
                                    </div>
                                </td>

                                <td>
                                    <div style="@if(count($timerLogs) > 1) height: 200px; overflow-y: scroll; @else @endif">
                                        @if(count($timerLogs) > 0)
                                            @foreach($timerLogs as $timerLog)
                                                <p style="margin: 0;"><b># {{ $count++ }}. {{ $timerLog->reason }}</b></p>
                                                <p style="padding: 0 0 0px 24px; margin: 0;">{{ $timerLog->location }}</p>
                                                <p style="padding: 0 0 0px 24px; ">{{ $timerLog->time_started }} - {{ $timerLog->time_finished }}</p>
                                            @endforeach
                                    </div>

                                    @endif
                                </td>
                                <td>
                                    @if($row->status == 0)
                                        <span style="border-radius: 10px;" class="label label-info">Started</span>
                                    @endif
                                    @if($row->status == 1)
                                        <span style="border-radius: 10px;" class="label label-success">Finished</span>
                                    @endif
                                    @if($row->status == 2)
                                        <span style="border-radius: 10px;" class="label label-info">Attached To Docket</span>
                                    @endif

                                </td>
                                <td>
                                    @if($row->time_ended == NULL)
                                        {{--<a href="{{ url('/dashboard/company/timers/pause/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Pause</a>--}}
                                        {{--<a href="{{ url('/dashboard/company/timers/stop/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Stop</a>--}}
                                    @endif
                                    <a href="{{ url('/dashboard/company/timers/'.$row->id.'/view') }}" class="btn btn-xs btn-raised btn-block btn-success">View</a>
                                    <a href="{{ url('/dashboard/company/timers/'.$row->id.'/download') }}" class="btn btn-xs btn-raised btn-block btn-success">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if(count($timerQuries)==0)
                        <tr>
                            <td colspan="4">
                                <center>Data Empty</center>
                            </td>
                        </tr>
                    @endif


                    <style>
                        .circle-chart__circle {
                            animation: circle-chart-fill 2s reverse;
                            transform: rotate(-90deg);
                            transform-origin: center;
                        }

                        @keyframes circle-chart-fill {
                            to { stroke-dasharray: 0 100; }
                        }
                    </style>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Map</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map">
                                <div style="width: 600px; height: 400px;" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Map</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map1">
                                <div style="width: 600px; height: 400px;" id="map_canvas1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br />
@endsection

@section('customScript')
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <link rel="stylesheet" href="{{asset("assets/rangeSlider/ion.rangeSlider.css")}}">
    <link rel="stylesheet" href="{{asset("assets/rangeSlider/normalize.css")}}">
    <link rel="stylesheet" href="{{asset("assets/rangeSlider/ion.rangeSlider.skinFlat.css")}}">
    <style>
        .chips.chips-initial {
            border-bottom: 1px solid #ddd;
        }
        .chip {
            font-size: 15px;
            /*font-weight: bold;*/
            /*line-height: 30px;*/
            padding: 0px 13px;
            border-radius: 16px;
            background-color: #eef2f3;
            margin: 11px 7px 10px 1px;
            display: inline-block;
            outline: none;
        }
        .chip:focus {
            background-color: #51c5c2;
            color: #fff;
        }
        .closebtn {
            cursor: pointer;
        }
        .chips.chips-initial input.chip-input {
            display: inline-block ;
            float: none;
            width: 120px;
            min-width: 30px;
            border:none;
            outline: none;
            min-height: 36px;
        }
    </style>
    <script src="{{asset("assets/rangeSlider/ion.rangeSlider.min.js")}}"></script>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg"></script>
    <script>
        $("#range_08").ionRangeSlider({
            grid: true,
            from: 18,
            min: 0.5,
            max: 10,
            values: [
                "0.5", "1",
                "1.5", "2",
                "2.5", "3",
                "3.5", "4",
                "4.5", "5.5",
                "6", "6.5",
                "7", "7.5",
                "8", "8.5",
                "9","9.5",
                "10"
            ]
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#framework').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Clients',
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            var map = null;
            var myMarker;
            var myLatlng;

            function initializeGMap(lat, lng) {
                myLatlng = new google.maps.LatLng(lat, lng);

                var myOptions = {
                    zoom: 12,
                    zoomControl: true,
                    center: myLatlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

                myMarker = new google.maps.Marker({
                    position: myLatlng
                });
                myMarker.setMap(map);
            }

            function initializeGMap2(lat, lng) {
                myLatlng = new google.maps.LatLng(lat, lng);

                var myOptions = {
                    zoom: 12,
                    zoomControl: true,
                    center: myLatlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                map = new google.maps.Map(document.getElementById("map_canvas1"), myOptions);

                myMarker = new google.maps.Marker({
                    position: myLatlng
                });
                myMarker.setMap(map);
            }

            // Re-init map before show modal
            $('#myModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                initializeGMap(button.data('lat'), button.data('lng'));
                $("#location-map").css("width", "100%");
                $("#map_canvas").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal').on('shown.bs.modal', function() {
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });

            // Re-init map before show modal
            $('#myModal1').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                initializeGMap2(button.data('lat'), button.data('lng'));
                $("#location-map1").css("width", "100%");
                $("#map_canvas1").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal1').on('shown.bs.modal', function() {
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });

        });
    </script>
@endsection