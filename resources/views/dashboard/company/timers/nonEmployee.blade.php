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
            <div style="    margin-top: 8px;margin-right: 5px;" class="pull-right">
                {{--<div class="btn-group" id="toggle_event_editing">--}}
                    {{--<button type="button"  class="btn btn-xs btn-warning locked_active">Inactive</button>--}}
                    {{--<button type="button"  class="btn btn-xs btn-info unlocked_inactive">Active</button>--}}
                {{--</div>--}}
                <div class="onoffswitch">
                    <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" checked>
                    <label class="onoffswitch-label" for="myonoffswitch">
                        <span class="onoffswitch-inner"></span>
                        <span class="onoffswitch-switch"></span>
                    </label>
                </div>
            </div>

        </ul><!--/.horizontalMenuTab-->
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Non Employee Timers</h3>
            <div class="pull-right">
                <button style="margin: 0;" type="button" class="btn btn-xs btn-raised btn-block btn-warning" data-toggle="modal" data-target="#exampleModal"><i class="fa fa-search" aria-hidden="true"></i> Search</button>
            </div>

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
                <tbody id="mobileviewHtml">
                @include('dashboard.company.timers.nonEmployeeTemplate')
                </tbody>
            </table>
            <div class="float-right">{{ $timers->links() }}</div>
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
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h5 class="modal-title" id="exampleModalLabel">Filter</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -20px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ Form::open(['url' => 'dashboard/company/timers/filterNonEmployeeTimer/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="col-md-12">
                        <strong>Date</strong>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:0px;">
                                    <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from"  id="fromDatePicker" placeholder="From" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:0px;">
                                    <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" value="" name="to" placeholder="To" >
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
                                        @if($uniqueTimersClient)
                                            @foreach($uniqueTimersClient as $row)
                                                <option value="{{$row->id}}">{{$row->first_name}}</option>
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
                                    <input type="text" class="form-control"   name="location" placeholder="Location" >

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong>Tags</strong>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:0px;">
                                    <input type="text" class="form-control"  name="tags" placeholder="tags">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <strong>Duration</strong>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:0px;">
                                    <input type="text" id="range_08" name="duration" value=""  />
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Filter</button>
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
        .pull-right .toggle{
            margin-top: -7px;
            width: 102px !important;
            height: 29px !important;
        }
     .toggle-group .toggle-off{
            font-size: 12px;

        }
        .toggle-group .toggle-on{
            font-size: 12px;

        }
        .toggle-group{
            /*top: 7px !important;*/
            /*bottom: 3px !important;*/
        }
        .pull-right .btn-group, .btn-group-vertical {
             margin: 0;
        }
        .onoffswitch {
            position: relative; width: 85px;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        }

        .onoffswitch-checkbox {
            display: none;
        }

        .onoffswitch-label {
            display: block; overflow: hidden; cursor: pointer;
            border: 1px solid #999999; border-radius: 20px;
        }

        .onoffswitch-inner {
            display: block; width: 200%; margin-left: -100%;
            -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
            -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
        }

        .onoffswitch-inner:before, .onoffswitch-inner:after {
            display: block;
            float: left;
            width: 50%;
            height: 24px;
            padding: 0;
            line-height: 23px;
            font-size: 11px;
            color: white;
            font-family: Trebuchet, Arial, sans-serif;
            font-weight: bold;
            -moz-box-sizing: border-box;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
        }

        .onoffswitch-inner:before {
            content: "Inactive";
            padding-left: 10px;
            background-color: #2FCCFF; color: #FFFFFF;
        }

        .onoffswitch-inner:after {
            content: "Active";
            padding-right: 10px;
            background-color: #EEEEEE; color: #999999;
            text-align: right;
        }

        .onoffswitch-switch {
            display: block; width: 18px; margin: 6px;
            background: #FFFFFF;
            border: 1px solid #999999; border-radius: 20px;
            position: absolute; top: 0; bottom: 0; right: 56px;
            -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
            -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s;
        }

        .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-inner {
            margin-left: 0;
        }

        .onoffswitch-checkbox:checked + .onoffswitch-label .onoffswitch-switch {
            right: 0px;
        }


        .onoffswitch1 {
            position: relative; width: 90px;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        }

        .onoffswitch1-checkbox {
            display: none;
        }

        .onoffswitch1-label {
            display: block; overflow: hidden; cursor: pointer;
            border: 2px solid #999999; border-radius: 30px;
        }

        .onoffswitch1-inner {
            display: block; width: 200%; margin-left: -100%;
            -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
            -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
        }

        .onoffswitch1-inner:before, .onoffswitch1-inner:after {
            display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
            font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
            -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
            border-radius: 30px;
            box-shadow: 0px 15px 0px rgba(0,0,0,0.08) inset;
        }

        .onoffswitch1-inner:before {
            content: "YES";
            padding-left: 10px;
            background-color: #2FCCFF; color: #FFFFFF;
            border-radius: 30px 0 0 30px;
        }

        .onoffswitch1-inner:after {
            content: "NO";
            padding-right: 10px;
            background-color: #EEEEEE; color: #999999;
            text-align: right;
            border-radius: 0 30px 30px 0;
        }

        .onoffswitch1-switch {
            display: block; width: 30px; margin: 0px;
            background: #FFFFFF;
            border: 2px solid #999999; border-radius: 30px;
            position: absolute; top: 0; bottom: 0; right: 56px;
            -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
            -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s;
            background-image: -moz-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%);
            background-image: -webkit-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%);
            background-image: -o-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%);
            background-image: linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 80%);
            box-shadow: 0 1px 1px white inset;
        }

        .onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-inner {
            margin-left: 0;
        }

        .onoffswitch1-checkbox:checked + .onoffswitch1-label .onoffswitch1-switch {
            right: 0px;
        }

        .onoffswitch2 {
            position: relative; width: 90px;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        }

        .onoffswitch2-checkbox {
            display: none;
        }

        .onoffswitch2-label {
            display: block; overflow: hidden; cursor: pointer;
            border: 2px solid #999999; border-radius: 5px;
        }

        .onoffswitch2-inner {
            display: block; width: 200%; margin-left: -100%;
            -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
            -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
        }

        .onoffswitch2-inner:before, .onoffswitch2-inner:after {
            display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 30px;
            font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
            -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
        }

        .onoffswitch2-inner:before {
            content: "YES";
            padding-left: 10px;
            background-color: #2FCCFF; color: #FFFFFF;
        }

        .onoffswitch2-inner:after {
            content: "NO";
            padding-right: 10px;
            background-color: #EEEEEE; color: #999999;
            text-align: right;
        }

        .onoffswitch2-switch {
            display: block; width: 18px; margin: 0px;
            background: #FFFFFF;
            border: 2px solid #999999; border-radius: 5px;
            position: absolute; top: 0; bottom: 0; right: 68px;
            -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
            -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s;
            background-image: -moz-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
            background-image: -webkit-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
            background-image: -o-linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
            background-image: linear-gradient(center top, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0) 100%);
        }

        .onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-inner {
            margin-left: 0;
        }

        .onoffswitch2-checkbox:checked + .onoffswitch2-label .onoffswitch2-switch {
            right: 0px;
        }

        .onoffswitch3
        {
            position: relative; width: 90px;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        }

        .onoffswitch3-checkbox {
            display: none;
        }

        .onoffswitch3-label {
            display: block; overflow: hidden; cursor: pointer;
            border: 0px solid #999999; border-radius: 0px;
        }

        .onoffswitch3-inner {
            display: block; width: 200%; margin-left: -100%;
            -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
            -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
        }

        .onoffswitch3-inner > span {
            display: block; float: left; position: relative; width: 50%; height: 30px; padding: 0; line-height: 30px;
            font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
            -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
        }

        .onoffswitch3-inner .onoffswitch3-active {
            padding-left: 10px;
            background-color: #EEEEEE; color: #FFFFFF;
        }

        .onoffswitch3-inner .onoffswitch3-inactive {
            padding-right: 10px;
            background-color: #EEEEEE; color: #FFFFFF;
            text-align: right;
        }

        .onoffswitch3-switch {
            display: block; width: 18px; margin: 0px; text-align: center;
            border: 0px solid #999999;border-radius: 0px;
            position: absolute; top: 0; bottom: 0;
        }
        .onoffswitch3-active .onoffswitch3-switch {
            background: #27A1CA; left: 0;
        }
        .onoffswitch3-inactive .onoffswitch3-switch {
            background: #A1A1A1; right: 0;
        }

        .onoffswitch3-active .onoffswitch3-switch:before {
            content: " "; position: absolute; top: 0; left: 18px;
            border-style: solid; border-color: #27A1CA transparent transparent #27A1CA; border-width: 15px 9px;
        }


        .onoffswitch3-inactive .onoffswitch3-switch:before {
            content: " "; position: absolute; top: 0; right: 18px;
            border-style: solid; border-color: transparent #A1A1A1 #A1A1A1 transparent; border-width: 15px 9px;
        }


        .onoffswitch3-checkbox:checked + .onoffswitch3-label .onoffswitch3-inner {
            margin-left: 0;
        }

        .onoffswitch4 {
            position: relative; width: 90px;
            -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;
        }

        .onoffswitch4-checkbox {
            display: none;
        }

        .onoffswitch4-label {
            display: block; overflow: hidden; cursor: pointer;
            border: 2px solid #27A1CA; border-radius: 0px;
        }

        .onoffswitch4-inner {
            display: block; width: 200%; margin-left: -100%;
            -moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;
            -o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;
        }

        .onoffswitch4-inner:before, .onoffswitch4-inner:after {
            display: block; float: left; width: 50%; height: 30px; padding: 0; line-height: 26px;
            font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;
            -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .onoffswitch4-inner:before {
            content: "Yes";
            padding-left: 10px;
            background-color: #FFFFFF; color: #27A1CA;
        }

        .onoffswitch4-inner:after {
            content: "No";
            padding-right: 10px;
            background-color: #FFFFFF; color: #666666;
            text-align: right;
        }

        .onoffswitch4-switch {
            display: block; width: 25px; margin: 0px;
            background: #27A1CA;
            position: absolute; top: 0; bottom: 0; right: 65px;
            -moz-transition: all 0.3s ease-in 0s; -webkit-transition: all 0.3s ease-in 0s;
            -o-transition: all 0.3s ease-in 0s; transition: all 0.3s ease-in 0s;
        }

        .onoffswitch4-checkbox:checked + .onoffswitch4-label .onoffswitch4-inner {
            margin-left: 0;
        }

        .onoffswitch4-checkbox:checked + .onoffswitch4-label .onoffswitch4-switch {
            right: 0px;
        }



        .cmn-toggle
        {
            position: absolute;
            margin-left: -9999px;
            visibility: hidden;
        }

        .cmn-toggle + label
        {
            display: block;
            position: relative;
            cursor: pointer;
            outline: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        input.cmn-toggle-round-flat + label
        {
            padding: 2px;
            width: 75px;
            height: 30px;
            background-color: #dddddd;
            -webkit-border-radius: 60px;
            -moz-border-radius: 60px;
            -ms-border-radius: 60px;
            -o-border-radius: 60px;
            border-radius: 60px;
            -webkit-transition: background 0.4s;
            -moz-transition: background 0.4s;
            -o-transition: background 0.4s;
            transition: background 0.4s;
        }

        input.cmn-toggle-round-flat + label:before, input.cmn-toggle-round-flat + label:after
        {
            display: block;
            position: absolute;
            content: "";
        }

        input.cmn-toggle-round-flat + label:before
        {
            top: 2px;
            left: 2px;
            bottom: 2px;
            right: 2px;
            background-color: #fff;
            -webkit-border-radius: 60px;
            -moz-border-radius: 60px;
            -ms-border-radius: 60px;
            -o-border-radius: 60px;
            border-radius: 60px;
            -webkit-transition: background 0.4s;
            -moz-transition: background 0.4s;
            -o-transition: background 0.4s;
            transition: background 0.4s;
        }

        input.cmn-toggle-round-flat + label:after
        {
            top: 4px;
            left: 4px;
            bottom: 4px;
            width: 22px;
            background-color: #dddddd;
            -webkit-border-radius: 52px;
            -moz-border-radius: 52px;
            -ms-border-radius: 52px;
            -o-border-radius: 52px;
            border-radius: 52px;
            -webkit-transition: margin 0.4s, background 0.4s;
            -moz-transition: margin 0.4s, background 0.4s;
            -o-transition: margin 0.4s, background 0.4s;
            transition: margin 0.4s, background 0.4s;
        }

        input.cmn-toggle-round-flat:checked + label
        {
            background-color: #27A1CA;
        }

        input.cmn-toggle-round-flat:checked + label:after
        {
            margin-left: 45px;
            background-color: #27A1CA;
        }

        div.switch5 { clear: both; margin: 0px 0px; }
        div.switch5 > input.switch:empty { margin-left: -999px; }
        div.switch5 > input.switch:empty ~ label { position: relative; float: left; line-height: 1.6em; text-indent: 4em; margin: 0.2em 0px; cursor: pointer; -moz-user-select: none; }
        div.switch5 > input.switch:empty ~ label:before, input.switch:empty ~ label:after { position: absolute; display: block; top: 0px; bottom: 0px; left: 0px; content: "off"; width: 3.6em; height: 1.5em; text-indent: 2.4em; color: rgb(153, 0, 0); background-color: rgb(204, 51, 51); border-radius: 0.3em; box-shadow: 0px 0.2em 0px rgba(0, 0, 0, 0.3) inset; }
        div.switch5 > input.switch:empty ~ label:after { content: " "; width: 1.4em; height: 1.5em; top: 0.1em; bottom: 0.1em; text-align: center; text-indent: 0px; margin-left: 0.1em; color: rgb(255, 136, 136); background-color: rgb(255, 255, 255); border-radius: 0.15em; box-shadow: 0px -0.2em 0px rgba(0, 0, 0, 0.2) inset; transition: all 100ms ease-in 0s; }
        div.switch5 > input.switch:checked ~ label:before { content: "on"; text-indent: 0.5em; color: rgb(102, 255, 102); background-color: rgb(51, 153, 51); }
        div.switch5 > input.switch:checked ~ label:after { margin-left: 2.1em; color: rgb(102, 204, 102); }
        div.switch5 > input.switch:focus ~ label { color: rgb(0, 0, 0); }
        div.switch5 > input.switch:focus ~ label:before { box-shadow: 0px 0px 0px 3px rgb(153, 153, 153); }







        .switch6 {  max-width: 17em;  margin: 0 auto; }
        .switch6-light > span, .switch-toggle > span {  color: #000000; }
        .switch6-light span span, .switch6-light label, .switch-toggle span span, .switch-toggle label {  color: #2b2b2b; }

        .switch-toggle a,
        .switch6-light span span { display: none; }

        .switch6-light { display: block; height: 30px; position: relative; overflow: visible; padding: 0px; margin-left:0px; }
        .switch6-light * { box-sizing: border-box; }
        .switch6-light a { display: block; transition: all 0.3s ease-out 0s; }

        .switch6-light label,
        .switch6-light > span { line-height: 30px; vertical-align: middle;}

        .switch6-light label {font-weight: 700; margin-bottom: px; max-width: 100%;}

        .switch6-light input:focus ~ a, .switch6-light input:focus + label { outline: 1px dotted rgb(136, 136, 136); }
        .switch6-light input { position: absolute; opacity: 0; z-index: 5; }
        .switch6-light input:checked ~ a { right: 0%; }
        .switch6-light > span { position: absolute; left: -100px; width: 100%; margin: 0px; padding-right: 100px; text-align: left; }
        .switch6-light > span span { position: absolute; top: 0px; left: 0px; z-index: 5; display: block; width: 50%; margin-left: 100px; text-align: center; }
        .switch6-light > span span:last-child { left: 50%; }
        .switch6-light a { position: absolute; right: 50%; top: 0px; z-index: 4; display: block; width: 50%; height: 100%; padding: 0px; }





    </style>
    <script src="{{asset("assets/rangeSlider/ion.rangeSlider.min.js")}}"></script>
<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAKDuU4TH-N69LxQr732WwHM2Jf5TWeMdg"></script>

    <script>
        {{--$('#toggle_event_editing button').click(function(e){--}}
            {{--if($(this).hasClass('locked_active') || $(this).hasClass('unlocked_inactive')){--}}

                {{--$.ajax({--}}
                    {{--type: "GET",--}}
                    {{--data: {type: 1},--}}
                    {{--url: "{{ url('dashboard/company/timers/nonEmployeeTemplate') }}",--}}
                    {{--success: function (response) {--}}
                        {{--$("#mobileviewHtml").html(response);--}}
                    {{--}--}}
                {{--});--}}

            {{--}else{--}}

                {{--$.ajax({--}}
                    {{--type: "GET",--}}
                    {{--data: {type: 0},--}}
                    {{--url: "{{ url('dashboard/company/timers/nonEmployeeTemplate') }}",--}}
                    {{--success: function (response) {--}}
                        {{--$("#mobileviewHtml").html(response);--}}

                    {{--}--}}


                {{--});--}}

            {{--}--}}

            {{--/* reverse locking status */--}}
            {{--$('#toggle_event_editing button').eq(0).toggleClass('locked_inactive locked_active  btn-warning btn-info');--}}
            {{--$('#toggle_event_editing button').eq(1).toggleClass('unlocked_inactive unlocked_active btn-info btn-warning');--}}
//        });
        $(document).ready(function(){
            $('#myonoffswitch').change(function() {
                if(this.checked != true){
                    $.ajax({
                        type: "GET",
                        data: {type: 1},
                        url: "{{ url('dashboard/company/timers/nonEmployeeActive') }}",
                        success: function (response) {
                            $("#mobileviewHtml").html(response);
                        }
                    });
                }else if(this.checked != false){
                    $.ajax({
                        type: "GET",
                        data: {type: 0},
                        url: "{{ url('dashboard/company/timers/nonEmployeeTemplate') }}",
                        success: function (response) {
                            $("#mobileviewHtml").html(response);

                        }


                    });

                }
            });
        });
    </script>
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