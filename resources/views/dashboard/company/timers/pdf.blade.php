<div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
    <div class="col-md-12">
        <section class="timer-info">
            {{--<div class="container">--}}
            <div class="timer-title">
                <p style="font-size: 23px;">Timer Info</p>
            </div>
            <div class="row">

                <div class="col-md-12 " style="font-size:18px;">
                    @if(AmazoneBucket::fileExist(@$companyDetail->logo))
                        <img src="{{ AmazoneBucket::url() }}{{ @$companyDetail->logo }}" style="height:150px;     margin-bottom: 10px;"><br>
                    @else
                        <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo" style="    margin-bottom: 10px;"><br>
                    @endif
                    <strong >{{ @$timer->userInfo->first_name." ".@$timer->userInfo->last_name }}</strong><br>
                    {{ @$companyDetail->name }}<br>
                    {{ @$companyDetail->address }}<br>
                    <b>ABN:</b> {{ @$companyDetail->abn }}
                    <br/><br/>

                </div>

                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <div class="session-time">
                        <ul style="font-size: 18px;">
                            <li style="margin-bottom: 10px;"><b>Session Time :</b></li>
                            <li>
                                <a href="#">
                                    {{ \Carbon\Carbon::parse( $timer->time_started )->format('d-M-Y H:i:s') }}
                                    - {{ \Carbon\Carbon::parse( $timer->time_ended  )->format('d-M-Y H:i:s') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="clients-parts">
                        <div class="client-title">
                            <b style="font-size: 18px; ">Clients :</b>
                        </div>
                        <ul style="font-size:18px; margin-top:9px;">
                            @php $sns = 1; @endphp
                            @if($timer_clients)
                                @foreach($timer_clients as $row)
                                    {{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}
                                    @if($sns!=$timer_clients->count())
                                        ,
                                    @endif
                                    <?php $sns++; ?>
                                @endforeach
                            @endif
                            <br>
                            @php
                                $recipientIds   =   $timer->timerClient->pluck('user_id');
                                $companyEmployeeQuery   =    \App\Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                                $empCompany    =    \App\Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                                $adminCompanyQuery   =    \App\Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                                $company    =   \App\Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                            @endphp
                            <?php $sns = 1; ?>
                            @foreach($company as $companys)
                                <li class="web-deam" style="margin-top: 3px;">{{$companys->name}}</li>
                                @if($sns!=$company->count())
                                    ,
                                @endif
                                <?php $sns++; ?>
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
                            <p style="margin-top:5px;">{{ gmdate("H:i:s", $break_time) }}</p>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6"></div>
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div style="margin-top: 5px;" class="map-title">
                        <b style="font-size: 23px;">Location</b>
                    </div>
                    <div style=" width: 100%;   height: 290px;background-position: unset;background-repeat: no-repeat;margin-bottom: 22px;margin-top: 10px; margin-left: -18px;" id="map" class="google-map">
                    </div>
                </div>
                @if(count($timerTimeline) > 0)
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="timeline-box">
                            <div class="timeline-title">
                                <b style="font-size: 23px;">Timeline</b>
                            </div>
                            {{--<a href="#">--}}
                            {{--<button class="date-button">10 Feb 2018</button>--}}
                            {{--</a>--}}
                            <div style="clear:both;"></div>
                            @foreach($timerTimeline as $row)
                                <ul class="timeline" style="position: relative">
                                    @if($row["type"]==1)
                                        <li class="comment" >
                                            <!--<i>-->
                                        <!--    <img style="width: 20px;position: absolute;top: 6px;left: 4px;" src="{{asset("assets/comment.png")}}">-->
                                            <!--</i>-->
                                            <div class="location-box">
                                                <div class="left-box">
                                                    <p style="font-size: 18px;"><i class="fa fa-map-marker" aria-hidden="true"></i> {{$row["location"]}}</p>
                                                    <p style="font-size: 18px;">{{$row["message"]}}</p>
                                                    <div  id="map">
                                                        <p style="font-size:18px;"><b>Location</b></p>
                                                        <div id="commentMap{{$row['id']}}" style="background-position: center;background-repeat: no-repeat; height: 290px;"></div>
                                                        <style>
                                                            #commentMap{{$row['id']}}{
                                                                background-image: url("https://maps.googleapis.com/maps/api/staticmap?zoom=15&size=1100x250&maptype=roadmap&markers=color:red%7Clabel:{{preg_replace('/\s+/', '_', $row["location"])}}%7C{{$row["latitude"]}},{{$row["longitude"]}}&key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg");
                                                            }
                                                        </style>
                                                    </div>
                                                    <br>
                                                    @if(count($row["images"]) > 0)
                                                        <b style="font-size:14px;">Images</b><br><br>
                                                        @foreach($row["images"] as $item)
                                                            <img style="width: 200px;height: 150px;    margin-right: 5px;" src="{{$item}}">
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="right-box">
                                                    <a href="#" style="font-size: 18px;">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ \Carbon\Carbon::parse( $row["time"] )->format(' g:i A') }}
                                                    </a>
                                                </div>
                                                <div style="clear: both;" class="clearfix"></div>

                                            </div>
                                        </li>
                                    @else
                                        <li class="pause">
                                            <!--<i>-->
                                        <!--  <img style="width: 20px;position: absolute;top: 6px;left: 4px;" src="{{asset("assets/pause.png")}}">-->
                                            <!--</i>-->
                                            <div class="location-box">
                                                <div class="left-box">
                                                    <p style="font-size: 18px;"><i class="fa fa-map-marker" aria-hidden="true"></i> {{$row["location"]}}</p>
                                                    <p style="font-size: 18px;">{{$row["reason"]}}</p>
                                                    <p style="font-size: 18px;"> {{ \Carbon\Carbon::parse( $row["time_started"] )->format('d-M-Y H:i:s') }}
                                                        - {{ \Carbon\Carbon::parse( $row["time_finished"] )->format('d-M-Y H:i:s') }}</p>
                                                    @php
                                                        $datetime1 = \Carbon\Carbon::parse($row["time_started"]);
                                                        $datetime2 = \Carbon\Carbon::parse($row["time_finished"]);
                                                        $break = $datetime2->diffInSeconds($datetime1);
                                                    @endphp
                                                    <p style="font-size: 18px;">Break Session <b>{{ gmdate("H:i:s", $break) }}</b></p>
                                                    <div  id="map">
                                                        <p style="font-size:18px;"><b>Location</b></p>
                                                        <div id="commentMap{{$row['id']}}" style="background-position: center;background-repeat: no-repeat; height: 290px;"></div>
                                                        <style>
                                                            #commentMap{{$row['id']}}{
                                                                background-image: url("https://maps.googleapis.com/maps/api/staticmap?zoom=15&size=1100x250&maptype=roadmap&markers=color:red%7Clabel:{{preg_replace('/\s+/', '_', $row["location"])}}%7C{{$row["latitude"]}},{{$row["longitude"]}}&key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg");
                                                            }
                                                        </style>
                                                    </div>
                                                    <br>
                                                    @if(count($row["images"]) > 0)
                                                        <b style="font-size:14px;">Images</b><br><br>
                                                        @foreach($row["images"] as $items)
                                                            <img style="width: 200px;height: 150px;margin-right: 5px;" src="{{$items}}">
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="right-box">
                                                    <a href="#" style="font-size: 18px;">
                                                        <i class="fa fa-clock-o" aria-hidden="true"></i>&nbsp;{{ \Carbon\Carbon::parse( $row["time_started"] )->format(' g:i A') }}
                                                    </a>
                                                </div>
                                                <div style="clear: both;" class="clearfix"></div>

                                            </div>
                                        </li>
                                    @endif
                                    @endforeach
                                </ul>
                        </div>
                    </div>
                @endif
            </div>
            {{--</div>--}}
        </section>
    </div>
</div>

<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<style>
    .timer-info {

    }

    .timer-title {
        border-bottom: 5px solid #eeeeee;
        font-weight: bold;
        font-size: 18px;
        color: #0d2b86;
        margin-bottom: 15px;
    }

    .timer-title p {
        margin-bottom: 0;
        padding: 10px 0px 10px 0px;
    }

    .session-time {
        padding-top: 10px;
        margin-bottom: 20px;
    }

    .session-time ul {
        padding-left: 0;
        margin: 0;
        list-style: none;
    }

    .session-time a {
        text-decoration: none;
        color: #000000;

    }

    .web-deam {
        color: #bdbdbd;
        display: inline-block;

    }

    .clients-parts {
        padding-top: 10px;
    }

    .clients-parts ul {
        list-style: none;
        padding-left: 0;

    }

    .testTimer {
        padding-top: 15px;
    }

    .google-map {
        background-image: url("https://maps.googleapis.com/maps/api/staticmap?zoom=15&size=1100x250&maptype=roadmap&markers=color:red%7Clabel:<?php $timer->location ?>%7C<?php echo $timer->latitude ?>,<?php echo $timer->longitude ?>&key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg");

    }

    .location-box {
        border-radius: 5px;
        border: 2px solid #eeeeee;
        /*height: 150px;*/
        padding: 10px;
    }

    .left-box {
        line-height: 12px;
        width: 50%;
        float: left;
    }

    .right-box {
        width: 50%;
        float: right;
    }

    .right-box a {
        text-decoration: none;
        color: #757575;
        padding-top: 20px;
        float: right;
    }

    .date-button {
        margin-top: 10px;
        color: #ffffff;
        background-color: #116275;
        font-size: 12px;
        border: none;
        border-radius: 5px;
    }

    ul.timeline {
        list-style-type: none;
        position: relative;
    }

    /*ul.timeline:before {*/
    /*    content: ' ';*/
    /*    background: #d4d9df;*/
    /*    display: inline-block;*/
    /*    position: absolute;*/
    /*    left: 29px;*/
    /*    width: 2px;*/
    /*    height: 100%;*/
    /*    z-index: 0;*/
    /*    top: 0;*/
    /*    bottom: 0;*/
    /*    border-radius: 2px;*/
    /*}*/

    ul.timeline > li {
        margin: 28px 0;
        padding-left: 10px;
        position: relative;
    }



    ul.timeline > .comment>i {
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 15px;
        top: 0px;
        width: 30px;
        height: 30px;
        background-size: 55%;
        z-index: 333333333333;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #22c0e8;
        /*background-image: url({{asset("/assets/comment.png")}})*/
    }

    ul.timeline > .pause>i {
        position: absolute;
        border-radius: 50%;
        border: 3px solid #22c0e8;
        left: 15px;
        top: 0px;
        width: 30px;
        height: 30px;
        background-size: 55%;
        z-index: 333333333333;
        background-position: center;
        background-repeat: no-repeat;
        background-color: #22c0e8;
        /*background-image:url( {{asset("/assets/pause.png")}})*/
    }

    .timeline {
        position: relative;
        margin: 0 0 30px 0;
        padding: 0;
        list-style: none;
    }

    .timeline>li:before, .timeline>li:after {
        content: " ";
        display: table;
    }
    /*timer information end*/
</style>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg"></script>
<script>
    $(document).ready(function () {
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
        $('#myModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            initializeGMap(button.data('lat'), button.data('lng'));
            $("#location-map").css("width", "100%");
            $("#map_canvas").css("width", "100%");
        });

        // Trigger map resize event after modal shown
        $('#myModal').on('shown.bs.modal', function () {
            google.maps.event.trigger(map, "resize");
            map.setCenter(myLatlng);
        });

        // Re-init map before show modal
        $('#myModal1').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            initializeGMap2(button.data('lat'), button.data('lng'));
            $("#location-map1").css("width", "100%");
            $("#map_canvas1").css("width", "100%");
        });

        // Trigger map resize event after modal shown
        $('#myModal1').on('shown.bs.modal', function () {
            google.maps.event.trigger(map, "resize");
            map.setCenter(myLatlng);
        });

    });
</script>