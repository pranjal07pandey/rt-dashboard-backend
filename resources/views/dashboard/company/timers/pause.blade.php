@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> Timers Management
            <small>Add/View Timer</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Timer Management</a></li>
            <li><a href="#">Timer Id - {{ $timer->id }}</a></li>
            <li class="active">Pause</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;">
        <div class="col-md-12">
            <h3 style="padding-left: 10px;font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Pause Timer</h3>

            {{ Form::open(['route' => 'timer.pause.store', 'files' => true]) }}
                <!-- /.box-header -->
                <div class="box-body" style="padding-top:0px;min-height: 250px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <input type="hidden" name="timer_id" value="{{ $timer->id }}">
                                <a id="getLocation"  href="#" class="btn btn-xs btn-raised btn-info pull-left">Get Location</a>
                                <input type="text" class="form-control" name="location" id="location_name" value="" readonly>
                                <input type="text" class="form-control" name="latitude" id="latitude" value="" readonly>
                                <input type="text" class="form-control" name="longitude" id="longitude" value="" readonly>
                                <p id="error"></p>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Time Started</label>
                                @php 
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                <input type="text" name="time_started" class="form-control" value="{{ $now }}" required readonly/>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Reason</label>
                                <textarea name="reason" required class="form-control"></textarea>
                            </div>
                        </div>

                    </div>
                </div>
        
                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('timers') }}" class="btn btn-xs btn-raised  btn-danger pull-left" id="addNew"><i class="fa fa-reply"></i> Back</a>
                            <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>
    
            {{ Form::close() }}
        </div>
    </div>
@endsection
@section('customScript')
<script src="https://maps.google.cn/maps/api/js?key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg" type="text/javascript"></script>
    <script>

        $(document).ready(function(){

            $("#getLocation").on("click",function(e){
                e.preventDefault();
                getLocation()
                
            })
        })

        var x = document.getElementById("error");
        
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition, showError);
            } else { 
                x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }
        
        function showPosition(position) {

            lat=position.coords.latitude;
            lon=position.coords.longitude;

            $('#latitude').val(lat);
            $('#longitude').val(lon);
            displayLocation(lat,lon);
        }
        
        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    x.innerHTML = "User denied the request for Geolocation."
                    break;
                case error.POSITION_UNAVAILABLE:
                    x.innerHTML = "Location information is unavailable."
                    break;
                case error.TIMEOUT:
                    x.innerHTML = "The request to get user location timed out."
                    break;
                case error.UNKNOWN_ERROR:
                    x.innerHTML = "An unknown error occurred."
                    break;
            }
        }

        function displayLocation(latitude,longitude){
            var geocoder;
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(latitude, longitude);

            geocoder.geocode(
                {'latLng': latlng}, 
                function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            var add= results[0].formatted_address ;
                            var  value=add.split(",");
                            $('#location_name').val(value[0]+","+value[1]+","+value[2]);
                        }
                        else  {
                            x.innerHTML = "address not found";
                        }
                    }
                    else {
                        x.innerHTML = "Geocoder failed due to: " + status;
                    }
                }
            );
        }
        
    </script>
@endsection  