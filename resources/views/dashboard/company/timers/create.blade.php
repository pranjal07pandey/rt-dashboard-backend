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
            <li class="active">Add</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;    margin-bottom: 53px;">
        <div class="col-md-12">
            <h3 style="padding-left: 10px;font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">New Timer</h3>

            {{ Form::open(['route' => 'timer.store', 'files' => true]) }}
                <!-- /.box-header -->
                <div class="box-body" style="padding-top:0px;min-height: 250px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                <label class="control-label" for="title">Employee</label>
                                <select class="form-control" required name="user_id">
                                    <option disabled selected value>Select Employee</option>
                                    @foreach($employees as $row)
                                        <option value="{{ $row->user_id }}">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</option>
                                    @endforeach
                                </select>
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
                            <div>
                                <label class="control-label pull-elft" for="title">Location</label>
                                <a id="getLocation"  href="#" class="btn btn-xs btn-raised btn-info pull-right" style="margin-bottom:0px;">Get Current Location</a>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" name="location" id="location_name" value=""  required  placeholder="Address">
                                    </div>
                                    <div class="col-md-12">
                                        <div id="map"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="hidden" class="form-control"  name="latitude" id="latitude" value="-35.3138194" onkeypress="return false;"  required  placeholder="Latitude">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="hidden" class="form-control"  name="longitude" id="longitude" value="149.059572" onkeypress="return false;" required  placeholder="Longitude">
                                    </div>
                                </div>
                                <p id="error"></p>
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
    <script src="{{ asset('assets/dashboard/js/timer.create.js') }}" type="text/javascript" ></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <link href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" rel="stylesheet"/>
    <script type="text/javascript">
        $(document).ready(function() {
            var element = document.getElementById('map');
            element.style = 'height:300px;';
            mapPopup = L.map(element);
            L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(mapPopup);
            var targetPopup = L.latLng(-35.3138194,149.059572);
            var marker =     L.marker(targetPopup,{draggable:'true'}).addTo(mapPopup);
            marker.on('dragend', function(event){
                var marker = event.target;
                var position = marker.getLatLng();
                marker.setLatLng(new L.LatLng(position.lat, position.lng),{draggable:'true'});

                $("#latitude").val(position.lat);
                $("#longitude").val(position.lng);
            });


            mapPopup.setView(targetPopup, 14);
            getCurrentLocation();

            $("#getLocation").on("click",function(e){
                e.preventDefault();
                getCurrentLocation();
            })

            function getCurrentLocation(){
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(location) {
                        var targetPopup = new L.LatLng(location.coords.latitude, location.coords.longitude);
                        marker.setLatLng(targetPopup);
                        mapPopup.setView(targetPopup, 14);

                        $("#latitude").val(location.coords.latitude);
                        $("#longitude").val(location.coords.longitude);
                    });
                }
                else {
                    x.innerHTML = "Geolocation is not supported by this browser.";
                }
            }


            // navigator.geolocation.getCurrentPosition(function(location) {
            //     var latlng = new L.LatLng(location.coords.latitude, location.coords.longitude);
            //     // var mymap = L.map(element).setView(latlng, 13)
            //     L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
            //         attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://mapbox.com">Mapbox</a>',
            //         maxZoom: 18,
            //         id: 'mapbox.streets',
            //         accessToken: 'pk.eyJ1IjoiYmJyb29rMTU0IiwiYSI6ImNpcXN3dnJrdDAwMGNmd250bjhvZXpnbWsifQ.Nf9Zkfchos577IanoKMoYQ'
            //     }).addTo(mymap);
            //
            //     var marker = L.marker(latlng).addTo(mymap);
            // });
        });
    </script>
@endsection
