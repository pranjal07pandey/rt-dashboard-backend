@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Docket</small>
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
        <div class="col-md-4">
            <div id="third" class="sideMenu">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Docket Elements
                    </h3>
                </div>
                <div class="menuContent">
                    <div class="elementAddingDiv">
                        <ul>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="shortTextAdd" fieldType="1">
                                    <span><i class="fa fa-plus-square"></i> Short Text </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary btn-xs themeSecondaryBg withripple docketComponent" id="longTextAdd" fieldType="2">
                                    <span><i class="fa fa-plus-square"></i> Long Text </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="locationAdd" fieldType="4">
                                    <span><i class="fa fa-plus-square"></i> Location  </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="imageAdd" fieldType="5">
                                    <span><i class="fa fa-plus-square"></i> Images </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="numAdd" fieldType="3">
                                    <span><i class="fa fa-plus-square"></i> Number </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="dateAdd" fieldType="6">
                                    <span><i class="fa fa-plus-square"></i> Date </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="unitRateAdd" fieldType="7">
                                    <span><i class="fa fa-plus-square"></i> Unit Rate </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="checkboxAdd" fieldType="8">
                                    <span><i class="fa fa-plus-square"></i> Check Box </span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="signatureAdd" fieldType="9">
                                    <span><i class="fa fa-plus-square"></i> Signature </span>
                                </a>
                            </li>

                            <!-- only for rt user for testing pourpose -->
                            @if(Session::get('company_id')==1)
                                <li>
                                    <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple docketComponent" id="sketchPadAdd" fieldType="14">
                                        <span><i class="fa fa-plus-square"></i> Sketch Pad </span>
                                    </a>
                                </li>
                            @endif

                            <li class="clearfix"></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div id="forth" class="sideMenu">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Docket Info
                        <a class="pull-right"  data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-plus-square"></i> Update
                        </a>
                    </h3>

                </div>
                <div class="menuContent">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Docket Name</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $tempDocket->title }}
                        </div>
                        {{--<div class="col-md-4">--}}
                        {{--<strong>Docket Title</strong>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-8">--}}
                        {{--{{ $tempDocket->subTitle }}--}}
                        {{--</div>--}}
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <strong>Invoiceable</strong>
                        </div>
                        <div class="col-md-8">
                            <input type="checkbox" id="invoiceableCheckboxInput" data="{{ $tempDocket->id }}"  @if($tempDocket->invoiceable==1) checked @endif>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-8">
            <h3 style="font-size: 20px; margin: 10px 0px 10px;font-weight: 500;display:inline-block" class="pull-left">{{ $tempDocket->title }}</h3>
            {{--            <a href="{{ url('dashboard/company/docketBookManager/designDocket/'.$tempDocket->id.'/save') }}" class="btn btn-xs btn-raised  btn-success pull-right" id="addNew" style="margin: 0px;margin-left: 10px;"><i class="fa fa-check"></i> Save<div class="ripple-container"></div></a>--}}
            {{--            &nbsp;&nbsp;<a href="{{ url('dashboard/company/docketBookManager/designDocket/'.$tempDocket->id.'/cancel') }}" class="btn btn-xs btn-raised btn-danger pull-right" id="addNew" style="margin: 0px;"><i class="fa fa-times"></i> Cancel<div class="ripple-container"></div></a>--}}
            <div class="pull-right">

                    <a href="{{ url('dashboard/defaultTemplate/designDefaultDocket/defaultTemplate') }}"  class="btn btn-xs btn-raised btn-success eight tourModel" id="addNew" style="margin: 0px;">
                        <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                    </a>&nbsp;
            </div>


            <div class="clearfix"></div>
            <hr style="margin:5px 0px;"/>


            <div class="row" id="sortable">

                @if($tempDocketFields)
                    @foreach($tempDocketFields as $item)
                        @include('dashboard.admin.defaultTemplate.defaultElementTemplate')
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    <br/><br/><br/>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Docket Info</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/defaultTemplate/designDefaultDocket/updateTempDocket', 'files' => true]) }}
                <div class="modal-body">
                    <input type="hidden" name="docketId" value="{{ $tempDocket->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Docket Name</label>
                                <input type="text" name="docketName" class="form-control" required="required" value="{!! $tempDocket->title !!}">
                            </div>
                        </div>
                        {{--<div class="col-md-12">--}}
                        {{--<div class="form-group label-floating">--}}
                        {{--<label class="control-label" for="title">Docket Title</label>--}}
                        <input type="hidden" name="docketTitle" class="form-control"  value="subtitle">
                        {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>




    <div class="modal fade" id="deleteInvoiceField" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Docket Field</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{--<input type="hidden" class="form-control" id="invoice_field_id">--}}
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this  Docket Field?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary deleteInvoiceComponent"  id="invoice_field_id" >Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('customScript')

    <style>
        .ui-tooltip-content{
            display: none;
        }
        .designDocket .btn-group .multiselect{
            border-left: none;
            border-right: none;
            border-top: none;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap.css')}}">
    {{--<script src="{{ asset('assets/dashboard/js/jquery-3.1.1.js') }}"></script>--}}

    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">


    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    {{--<script src="{{asset('assets/dashboard/tour/jquery.min.js')}}"></script>--}}
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#framework').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%'
            });

        });
    </script>
    <script>
        $(document).ready(function() {
            $('#prefillers').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var label = $(e.relatedTarget).data('label');
                $("#docket_field_id").val(id);
                $("#docket_field_label").val(label);
            });
        });
    </script>
    <script src="{{asset('assets/dashboard/tour/scriptsecond.js')}}"></script>

    <script>
        $(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(".generateTicketForm").hide();
            $(".generateTicketButton").click(function(){
                $(".generateTicketForm").slideToggle();
            });
        });

    </script>

    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/css/bars.css') }}">
    <script src="{{ asset('assets/dashboard/js/bars.js') }}"></script>

    {{--<!-- FLOT CHARTS -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.min.js') }}"></script>--}}
    {{--<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.resize.min.js') }}"></script>--}}
    {{--<!-- FLOT PIE PLUGIN - also used to draw donut charts -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.pie.min.js') }}"></script>--}}
    {{--<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.categories.min.js') }}"></script>--}}

    <script type="text/javascript">
        $(function() {
            $('.editable').editable();
            var tourdragable = false;
            // define tour
            var tour = new Tour({
                debug: true,
                // template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Prev</button><span data-role='separator'></span><button class='btn btn-default' data-role='next'>Next »</button></div><button class='btn btn-default' data-role='end'>End tour</button></div>",
                // basePath: location.pathname.slice(0, location.pathname.lastIndexOf('/')),
                steps: [
                    {
                        element: "#third",
                        title: "<span>3/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        content: "Depending on the fields you need on your custom docket template, please click the “Docket Elements” from the highlighted box.",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"  data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',
                    },
                    {
                        element: "#forth",
                        title: "<span>4/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        editable: true,
                        content: "If you want your “Docket Template” to be “Invoiceable”, click the invoiceable checkbox from the highlighted box. <br><br><small><b>Note:</b> Once the “Invoiceable” checkbox is ticked, you will be able to include previously approved dockets by a client and it's unit rate/ total dollar value to create an invoice.</small>",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn editable"   data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',
                    },
                    {
                        element: ".fifth",
                        title: "<span>5/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        edit: true,
                        reposition: true,
                        content: "Any elements “Label Title” you add to the template is  <b>editable</b>.  This way you can customise what labels you want to display on the docket templates you send to your clients.",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"   data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',
                    },
                    {
                        element: ".six",
                        title: "<span>6/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        prevEditable: true,
                        hideElement: true,
                        content: "You can move the fields in any order you would like. Just <b> “Click”, “Hold” and “Drag”.</b> .",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"   data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn editable" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',
                    },
                    {
                        element: ".eight",
                        title: "<span>7/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        prevEditable: true,
                        hideElement: true,
                        tourModel:true,
                        content: "Once you have finished creating your “Docket Template”, hit save and it will allow you to assign your employees from list of employees you have created.",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"  data-toggle="modal" data-target="#myModalAssign"  data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn editable" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',
                    },
                    {
                        element: "#seven",
                        title: "<span>8/9</span>",
                        placement: "bottom",
                        backdrop: true,
                        modalId: "#myModalAssign",
                        content: "Click the employee/s from the list to assign the docket. “Control + Click” or “Command + Click” to add multiple employees and click “Assign”",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn" data-toggle="modal" data-target="#noteModal"   data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn closeModal" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',

                    },
                    {
                        element: ".nine",
                        title: "<span>9/9</span>",
                        placement: "top",
                        backdrop: true,
                        modalId: "#noteModal",
                        content: "<b>Note</b>",
                        backdropPadding: 5,
                        template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Done</button><button class="btn btn-info btn-xs bootstro-prev-btn"    data-role="next">Next →</button> <button style="margin-left:36px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-toggle="modal" data-target="#myModalAssign"  data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>',

                    }],

                onPrev: function (tour) {
                    if($(this)[0]["prevEditable"]){
//                        $('.firstEditElement').editable();
//                        $('.firstEditElement').editable('toggle')
                    }
                    else{
                        $($(this)[0]["modalId"]).modal('hide');

                    }
                    if($(this)[0]["hideElement"]){
                        $('.docketField').css('z-index','');
                    }
                },

                onShown: function (tour) {
                    if ($(this)[0]["edit"]) {

                        $('.firstEditElement').editable('show');
                    }
                },

                onEnd: function (tour) {
                    $("#myModalAssign").modal('hide');
                    $("#noteModal").modal('hide');
                },

                onNext: function(tour){
                    if($(this)[0]["tourModel"]){
                        $('#myModalAssign').editable('show');
                    }
                    else{
                        $($(this)[0]["modalId"]).modal('hide');

                    }
                    if($(this)[0]["hideElement"]){
                        $('.docketField').css('z-index','');
                    }
                }
            });

            // init tour
            @if(Session::get('helpFlag')=="true")
               tour.restart();
            @endif

            // start tour
            $('#start-tour').click(function() {
                tour.restart();
                $('.docketField').css('z-index','');
                tourdragable = true;
                $("#myModalAssign").modal({
                    show: false,
                    backdrop: 'static'
                });
                $("#noteModal").modal({
                    show: false,
                    backdrop: 'static'
                });



            });

            $('.closeModal').on('click',function(){
                alert("test");

            });


            function labelFormatter(label, series) {
                return '<div style="font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;">'
                    + label
                    + "<br>"
                    + Math.round(series.percent) + "%</div>";
            }

            $(function () {

                $("#invoiceableCheckboxInput").on("click", function () {
                    var docketId = $(this).attr("data");
                    var checked = 0;
                    if ($("#invoiceableCheckboxInput").is(':checked')) {
                        checked = 1;
                    } else {
                        checked = 0;
                    }

                    $.ajax({
                        type: "POST",
                        url: '{{ url('dashboard/defaultTemplate/designDefaultDocket/invoiceable/') }}',
                        data: {"invoiceable": checked, "docketId": docketId},
                        success: function (msg) {
                            if (msg == "Invalid attempt!") {
                                alert(msg);
                            } else {
                                if (msg == 1) {
                                    $(".horizontalList span span.pull-right").fadeIn();
                                } else {
                                    $(".horizontalList span span.pull-right").fadeOut();
                                }
                            }
                        }
                    });
                });

                $(".docketInvoiceCheckboxInput").on("click", function () {
                    var docketFieldId = $(this).attr("data");
                    var checked = 0;
                    if ($(this).is(':checked')) {
                        checked = 1;
                    } else {
                        checked = 0;
                    }

                    $.ajax({
                        type: "POST",
                        url: '{{ url('dashboard/defaultTemplate/designDefaultDocket/docketInvoiceFiled/') }}',
                        data: {"data": checked, "docketFieldId": docketFieldId},
                        success: function (msg) {
                            if (msg == "Invalid attempt!") {
                                alert(msg);
                            }
                        }
                    });
                });

                $(".docketPreviewCheckboxInput").on("click", function () {
                    var docketFieldId = $(this).attr("data");
                    var order = $(this).attr("data");
                    var checked = 0;
                    if ($(this).is(':checked')) {
                        checked = 1;
                    } else {
                        checked = 0;
                    }

                    $.ajax({
                        type: "POST",
                        url: '{{ url('dashboard/defaultTemplate/designDefaultDocket/docketPreviewFiled/') }}',
                        data: {"data": checked, "docketFieldId": docketFieldId, "order": order},
                        success: function (msg) {
                            if (msg == "Invalid attempt!") {
                                alert(msg);
                            }
                        }
                    });
                });

                $("#sortable").sortable({
                    stop: function (e, ui) {
                        if($('.tour-backdrop').length) {
                            tour.goTo(3);
                        }
                        var param = [];
                        $.map($("#sortable >div"), function (el) {
                            param[$(el).index()] = $(el).attr('fieldId');
                        });
                        console.log(param);
                        $.ajax({
                            type: "POST",
                            data: {param: param},
                            url: "{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldUpdatePosition/'.$tempDocket->id) }}",
                            success: function (msg) {
                                console.log(msg);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.docketComponent', function () {
                $.ajax({
                    type: "POST",
                    data: {fieldType: $(this).attr('fieldtype')},
                    url: "{{ url('dashboard/defaultTemplate/designDefaultDocket/addDocketField/'.$tempDocket->id) }}",
                    success: function (response) {
                        $.when($('#sortable').append(response)).done(function () {
                            $('.editable').editable();
                        });
                    }
                });
            });


            {{--$(document).on('click', '.deleteDocketComponent', function () {--}}
                {{--if (confirm("Are you sure to delete this docket field?")) {--}}
                    {{--var parentDiv = $(this).parents('.docketField');--}}
                    {{--$.ajax({--}}
                        {{--type: "POST",--}}
                        {{--data: {fieldId: $(this).attr('fieldId')},--}}
                        {{--url: "{{ url('dashboard/company/docketBookManager/designDocket/deleteDocketField/'.$tempDocket->id) }}",--}}
                        {{--success: function (response) {--}}

                            {{--if (response == "") {--}}
                                {{--$.when(parentDiv.fadeOut()).done(function () {--}}
                                    {{--parentDiv.remove();--}}
                                {{--});--}}
                            {{--} else {--}}
                                {{--alert(response);--}}
                            {{--}--}}
                        {{--}--}}
                    {{--});--}}
                {{--}--}}
            {{--});--}}

           $(document).on('click', '.deleteInvoiceComponent', function(){
                var parentDiv   =   $("#activeTr");
                $.ajax({
                    type: "POST",
                    data: {fieldId:$(this).attr('fieldId')},
                    url: "{{ url('dashboard/defaultTemplate/designDefaultDocket/deleteDocketField/'.$tempDocket->id) }}",
                    success: function(response){

                        if(response == ""){
                            $.when(parentDiv.fadeOut()).done(function() {
                                parentDiv.remove();
                            });
                        }else{
                            alert(response);
                        }
                    }
                });

                $('#deleteInvoiceField').modal('hide')
            });

            $(document).on('click', '.deleteDocketComponent', function() {
                $('#deleteInvoiceField').modal('show');

                var parentDiv   =   $(this).parents('.docketField');
                parentDiv.attr('id',"activeTr");

                id = $(this).attr('data-id');
                $('#invoice_field_id').attr("fieldId",id);
            });




        });
    </script>

    <script>
        $(document).ready(function() {
            $('.editabledocketprefiller').editable({
                params: function(params) {
                    params.name = $(this).editable().data('name');
                    return params;
                },
                error: function(response, newValue) {
                    if(response.status === 500) {
                        return 'Server error.';
                    } else {
                        return response.responseText;
                        // return "Error.";
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#myModalAssignDelete').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#design_docket").val(id);
            });
        });
    </script>
    <style>
        .editable-click:after{
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: 20px;
            font-weight: normal;
            font-size: 10px;
            color: red;
            padding: 0px 5px;
            border-radius: 5px;
        }
        .docketprefiller .editable-click:after {
            content: normal;

        }
        .horizontalList .popover.top{
            margin-top: -35px !important;
        }
        .horizontalList span{
            display:inline-block !important;
        }
        .prefillercontent .btnprefiller {
            display: none;
        }
        .prefillercontent:hover .btnprefiller{
            display: inline-block;
            cursor: pointer;

        }
        .prefillercontent:hover{
            cursor: pointer;
            border-color: #fafafa;

        }
        .btn-danger:hover, .btn-danger:focus, .btn-danger:active, .btn-danger.active, .open > .dropdown-toggle.btn-danger {
            color: #fff;
            background-color: #c9302c;
            border-color: transparent;
        }
        .horizontalList span:first-child {
            color: #fff;
            display: block;
        }
    </style>
@endsection