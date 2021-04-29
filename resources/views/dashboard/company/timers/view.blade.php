@extends('layouts.companyDashboard')
@section('content')
    <script src="{{asset("assets/lightBox/lc_lightbox.lite.js")}}"></script>
    <link rel="stylesheet" href="{{asset("assets/lightBox/lc_lightbox.css")}}">
    <link rel="stylesheet" href="{{asset("assets/lightBox/skins/minimal.css")}}" />
    <script src="{{asset("assets/lightBox/AlloyFinger/alloy_finger.min.js")}}" type="text/javascript"></script>

    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> Timers Management
            <small>View Timer</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Timer Management</a></li>
            <li><a href="#">View</a></li>
            {{--<li class="active">{{ $timer->id }}</li>--}}
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">
        <div class="col-md-12">


            <section class="timer-info">
                {{--<div class="container">--}}
                <div class="timer-title">
                    <p>Timer Info</p>
                </div>
                <div class="row">

                    <div class="col-md-12 invoice-col">
                        @if(AmazoneBucket::fileExist(@$companyDetail->logo))
                            <img src="{{ AmazoneBucket::url() }}{{ @$companyDetail->logo }}" style="height:150px;     margin-bottom: 10px;"><br>
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo" style="    margin-bottom: 10px;"><br>
                        @endif
                        <strong>{{ @$timer->userInfo->first_name." ".@$timer->userInfo->last_name }}</strong><br>
                        {{ @$companyDetail->name }}<br>
                        {{ @$companyDetail->address }}<br>
                        <b>ABN:</b> {{ @$companyDetail->abn }}
                        <br/><br/>

                    </div>


                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div class="session-time">
                            <ul>
                                <li><b>Session Time :</b></li>
                                <li>
                                    <a href="#">
                                        {{ \Carbon\Carbon::parse( $timer->time_started )->format('d-M-Y H:i:s') }} - {{ \Carbon\Carbon::parse( \Carbon\Carbon::now() )->format('d-M-Y H:i:s') }}
                                        {{--17-May-2018 05:35-17-May-2018 05:35--}}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="clients-parts">
                            <div class="client-title">
                                <b>Clients :</b>
                            </div>
                            <ul>
                                @php $sns = 1; @endphp
                                @if($timer_clients)

                                    @foreach($timer_clients as $row)

                                      @if($row->user_type == 1)
                                        {{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}
                                        @if($sns!=$timer_clients->count())
                                            ,
                                        @endif
                                        <?php $sns++; ?>
                                      @elseif($row->user_type == 2)
                                            {{ $row->emailUserInfo->email }}
                                            @if($sns!=$timer_clients->count())
                                                ,
                                            @endif
                                            <?php $sns++; ?>
                                      @endif

                                    @endforeach

                                @endif

                                <br>

                                <?php $sn = 0; ?>
                                @foreach ($timer_clients as $rows)
                                    <?php $sn++; ?>

                                    @if ($rows->user_type == 1)
                                        @if($sn<=count($timer_clients) && $sn!=1)
                                            ,
                                        @endif
                                        @php
                                            $companyId = 0;
                                                if (\App\Employee::where('user_id', $rows->user_id)->count() != 0):
                                                    $companyId = \App\Employee::where('user_id', $rows->user_id)->first()->company_id;
                                                else :
                                                    $companyId = \App\Company::where('user_id', $rows->user_id)->first()->id;
                                                endif;
                                        @endphp

                                        <span style="margin: 0;">  {{  \App\Company::where('id', $companyId)->first()->name}}</span>
                                    @elseif ($rows->user_type == 2)
                                        @if($sn<=count($timer_clients) && $sn!=1)
                                            ,
                                        @endif
                                        <span style="margin: 0;">  {{ $rows->emailUserInfo->emailClient->company_name}}</span>
                                    @endif
                                @endforeach
                                <br>
                                @if($timer_tags)
                                    @foreach($timer_tags->unique('user_id') as $row)
                                        @php
                                            $tags =  App\TimerAttachedTag::where('timer_id', $row->timer_id)->orderBy('created_at', 'DESC')->get();
                                            $sns = 1;
                                        @endphp
                                        @foreach($tags as $row)
                                            #{{$row->tag}}
                                            @if($sns!=$tags->count())

                                            @endif
                                            <?php $sns++; ?>
                                        @endforeach
                                    @endforeach
                                @endif
                                <br><br>

                                <b>Total Break :</b><br>
                                {{ gmdate("H:i:s", $break_time) }}
                            </ul>
                        </div>
                    </div>

                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <div id="circle" data-thickness="8" data-value="{{substr( substr_replace( $timer->total_time ,"",5),-2)* 1 / 60 }} ">
                          <span class="timer-num">
                            <p class="timer-sm">Total</p>
                            <p class="number-time"><b style="font-size: 18px;">{{substr_replace( $timer->total_time ,"",2)}}</b>h <b style="font-size: 18px;">{{substr( substr_replace( $timer->total_time ,"",5),-2)}}</b>m <b style="font-size: 18px;">{{substr( $timer->total_time ,-2)}}</b>s</p>
                            <p class="timer-sm">#{{$timer->id}}</p>
                          </span>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div style="margin-top: 16px;margin-bottom:5px;" class="map-title">
                            <b>Location</b>
                        </div>
                        <div id="osm-map"></div>
                        <br/>
                    </div>
                    @if(count($timerTimeline) > 0)
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <div class="timeline-box">
                                <div class="timeline-title">
                                    <b>Timeline</b>
                                </div>
                                {{--<a href="#">--}}
                                {{--<button class="date-button">10 Feb 2018</button>--}}
                                {{--</a>--}}
                                @foreach($timerTimeline as $row)
                                    <ul class="timeline">
                                        @if($row["type"]==1)
                                            <li class="comment">
                                                <div class="location-box">
                                                    <div class="left-box">
                                                        <p><i class="fa fa-map-marker" aria-hidden="true"></i> {{$row["location"]}}</p>
                                                        <p>{{$row["message"]}}</p>
                                                        @if(count($row["images"]) > 0)
                                                            <b>Images</b><br><br>
                                                        <!--@foreach($row["images"] as $item)-->
                                                        <!--    <img style="width: 84px;height: 51px;    margin-right: 5px;" src="{{$item}}">-->
                                                        <!--@endforeach-->
                                                            @foreach($row["images"] as $items)
                                                                <a class="elemcomment{{$row["id"]}}"  href="{{$items}}"  data-lcl-thumb="{{$items}}">
                                                                <span >
                                                                    <img style="width: 84px;height: 51px;" src="{{$items}}">
                                                                </span>
                                                                </a>
                                                                <script type="text/javascript">
                                                                    $(document).ready(function(e) {
                                                                        lc_lightbox('.elemcomment{{$row["id"]}}', {
                                                                            wrap_class: 'lcl_fade_oc',
                                                                            gallery : true,
                                                                            skin: 'minimal',
                                                                            radius: 0,
                                                                            padding : 0,
                                                                            border_w: 0,
                                                                            thumb_attr    : false,
                                                                        });
                                                                    });
                                                                </script>
                                                            @endforeach
                                                            @endif
                                                    </div>
                                                    <div class="right-box">
                                                        <a href="#">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ \Carbon\Carbon::parse( $row["time"] )->format(' g:i A') }}
                                                        </a>
                                                    </div>
                                                    <div class="right-box" style=" position: absolute;right: 7px;bottom: 7px;">
                                                        <button style="float: right;" type="button" class="date-button" data-toggle="modal" data-target="#myModal" data-lat='{{ $row["latitude"] }}' data-lng='{{ $row["longitude"] }}'>
                                                            <i class="fa fa-map" aria-hidden="true"></i>&nbsp;   View Map
                                                        </button>
                                                    </div>

                                                    <div class="clearfix"></div>

                                                </div>
                                            </li>
                                        @else
                                            <li class="pause">
                                                <div class="location-box">
                                                    <div class="left-box">
                                                        <p><i class="fa fa-map-marker" aria-hidden="true"></i>  {{$row["location"]}}</p>
                                                        <p>{{$row["reason"]}}</p>
                                                        <p> {{ \Carbon\Carbon::parse( $row["time_started"] )->format('d-M-Y H:i:s') }} - {{ \Carbon\Carbon::parse( $row["time_finished"] )->format('d-M-Y H:i:s') }}</p>

                                                        @php
                                                            $datetime1 = \Carbon\Carbon::parse($row["time_started"]);
                                                            $datetime2 = \Carbon\Carbon::parse($row["time_finished"]);
                                                            $break = $datetime2->diffInSeconds($datetime1);
                                                        @endphp
                                                        <p>Break Session <b>{{ gmdate("H:i:s", $break) }}</b> </p>
                                                        @if(count($row["images"]) > 0)
                                                            <b>Images</b><br><br>
                                                        <!--@foreach($row["images"] as $items)-->
                                                        <!--    <img style="width: 84px;height: 51px;    margin-right: 5px;" src="{{$items}}">-->
                                                        <!--@endforeach-->
                                                            @foreach($row["images"] as $items)
                                                                <a class="elempause{{$row["id"]}}"  href="{{$items}}"  data-lcl-thumb="{{$items}}">
                                                                <span >
                                                                    <img style="width: 84px;height: 51px;" src="{{$items}}">
                                                                </span>
                                                                </a>
                                                                <script type="text/javascript">
                                                                    $(document).ready(function(e) {
                                                                        lc_lightbox('.elempause{{$row["id"]}}', {
                                                                            wrap_class: 'lcl_fade_oc',
                                                                            gallery : true,
                                                                            skin: 'minimal',
                                                                            radius: 0,
                                                                            padding : 0,
                                                                            border_w: 0,
                                                                            thumb_attr    : false,
                                                                        });
                                                                    });
                                                                </script>
                                                            @endforeach
                                                            @endif
                                                    </div>
                                                    <div class="right-box">
                                                        <a href="#">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ \Carbon\Carbon::parse( $row["time_started"] )->format(' g:i A') }}
                                                        </a>
                                                    </div>
                                                    <div class="right-box" style=" position: absolute;right: 7px;bottom: 7px;">
                                                        <button style="float: right;" type="button" class="date-button" data-toggle="modal" data-target="#myModal" data-lat='{{ $row["latitude"] }}' data-lng='{{ $row["longitude"] }}'>
                                                            <i class="fa fa-map" aria-hidden="true"></i>&nbsp;   View Map
                                                        </button>
                                                    </div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </li>
                                        @endif
                                        @endforeach
                                    </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
    <br />

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
                            <div id="popup-map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>

@endsection
@section('customScript')
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .timer-info{

        }
        .timer-title{
            border-bottom: 5px solid #eeeeee;
            font-weight: bold;
            font-size: 18px;
            color: #0d2b86;
            margin-bottom: 15px;
        }
        .timer-title p{
            margin-bottom: 0;
            padding: 10px 0px 10px 0px;
        }
        .session-time {
            padding-top: 10px;
            margin-bottom: 20px;
        }
        .session-time ul{
            padding-left: 0;
            margin: 0;
            list-style: none;
        }
        .session-time a{
            text-decoration: none;
            color: #000000;

        }
        .web-deam{
            color: #bdbdbd;
            display: inline-block;
        }
        .clients-parts{
            padding-top: 10px;
        }
        .clients-parts ul{
            list-style: none;
            padding-left: 0;

        }
        .testTimer{
            padding-top: 15px;
        }
        .google-map{
            background-image: url("https://maps.googleapis.com/maps/api/staticmap?zoom=15&size=1100x250&maptype=roadmap&markers=color:red%7Clabel:<?php $timer->location ?>%7C<?php echo $timer->latitude ?>,<?php echo $timer->longitude ?>&key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg");
            height: 290px;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            margin-bottom: 22px;
            margin-top: 10px;

        }
        .location-box{
            border-radius: 5px;
            border:2px solid #eeeeee;
            /*height: 120px;*/
            padding: 10px;
        }
        .left-box{
            line-height: 12px;
            width: 50%;
            float: left;
        }
        .right-box{
            width: 50%;
            float: right;
        }
        .right-box a{
            text-decoration: none;
            color: #757575;
            /*padding-top: 20px;*/
            float: right;
        }
        .date-button{
            margin-top: 10px;
            color: #116275;
            /*background-color: #116275;*/
            font-size: 12px;
            border: none;
            border-radius: 5px;
        }
        ul.timeline {
            list-style-type: none;
            position: relative;
        }
        ul.timeline:before {
            content: ' ';
            background: #d4d9df;
            display: inline-block;
            position: absolute;
            left: 29px;
            width: 2px;
            height: 100%;
            z-index: 400;
        }
        ul.timeline > li {
            margin: 20px 0;
            padding-left: 60px;
        }

        ul.timeline > .comment:before {
            font-family: 'Material Icons';
            content: '\e0b7';
            background: #22c0e8;
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            border: 3px solid #22c0e8;
            left: 15px;
            width: 30px;
            height: 30px;
            z-index: 400;
            padding: 0px 5px;
            color: #fff;
        }

        ul.timeline > .pause:before {
            font-family: 'Material Icons';
            content: '\e034';
            background: #22c0e8;
            display: inline-block;
            position: absolute;
            border-radius: 50%;
            border: 3px solid #22c0e8;
            left: 15px;
            width: 30px;
            height: 30px;
            color: #fff;
            z-index: 400;
            padding: 0px 5px;
        }
        #circle {
            padding-top: 19px;
            width: 160px;
            float: right;
            position: relative;
        }

        .timer-num {
            position: absolute;
            top: 59px;
            text-align: center;
            left: 33px;
        }

        .timer-sm {
            font-size: 13px;
            color: #c1c1c1;

        }
        .elem, .elem * {
            box-sizing: border-box;
            margin: 0 !important;
        }
        .elem {
            display: inline-block;
            font-size: 0;
            /*width: 33%;*/
            /*border: 20px solid transparent;*/
            border-bottom: none;
            background: #fff;
            /*padding: 10px;*/
            height: auto;
            background-clip: padding-box;
        }
        .elem > span {
            display: block;
            cursor: pointer;
            padding-right: 5px;

        }
        /*timer information end*/
    </style>
    <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg"></script>
    <script src="{{asset("assets/timer.js")}}"></script>

    <script>
        $('#circle').circleProgress({
            startAngle: 4.7,
//            value: min,
            size: 160,
            fill: {
                gradient: ["#12b0b7"]
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            var element = document.getElementById('osm-map');
            element.style = 'height:300px;';
            var map = L.map(element);
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            var target = L.latLng('{{ $timer->latitude }}', '{{ $timer->longitude }}');
            map.setView(target, 17);
            L.marker(target).bindTooltip("{{ $timer->location }}").addTo(map);

            var element = document.getElementById('popup-map');
            element.style = 'height:300px;';
             mapPopup = L.map(element);
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapPopup);

            $('#myModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var targetPopup = L.latLng(button.data('lat'), button.data('lng'));
                mapPopup.setView(targetPopup, 17);
                L.marker(targetPopup).addTo(mapPopup);
                setTimeout(function(){ mapPopup.invalidateSize()}, 200);
            });
        });
    </script>
@endsection