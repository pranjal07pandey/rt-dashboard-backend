@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Docket Template</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <div class="rtTab">
                <div class="rtTabHeader">
                    <ul class="pull-left">
                        <li ><a href="{{ route('companyDocketTemplates') }}" >Dockets Templates</a></li>
                        <li class="active"><a href="{{ route('companyDocketTemplatesArchive') }}" >Archived Dockets Templates</a></li>
                    </ul>
                    {{--<ul class="pull-right" style="    margin-top: 7px;">--}}
                        {{--<li><button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info popupsecond" data-toggle="modal" data-target="#myModal3"  >--}}
                                {{--<i class="fa fa-plus-square"></i> Add New--}}
                            {{--</button>--}}
                        {{--</li>--}}
                        {{--<li><button type="button" data-toggle="modal" data-target="#importExport" class="btn btn-xs btn-raised btn-block btn-warning"  >--}}
                                {{--<i class="fa fa-plus-square"></i> Import/Export--}}
                            {{--</button>--}}

                        {{--</li>--}}
                    {{--</ul>--}}
                </div>
            </div>
            {{--<h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Dockets Template</h3>--}}
            <div class="pull-right">
                <!-- Button trigger modal -->



            </div>
            <div class="pull-right" style="margin-right: 10px;">

            </div>

            <div class="clearfix"></div>
            <br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    {{--<th>Id</th>--}}
                    <th>Docket Name</th>
                    <th width="200px">Created By</th>
                    <th width="130px">Date Added</th>
                    <th width="100px">Assigned</th>
                    <th width="100">Action</th>
                </tr>
                </thead>
                <tbody>
                @if(@$dockets)
                    <?php
                    $docket    =    array();
                    $nonAssigned    =   array();
                    foreach ($dockets as $row){


                        if(count($row->assignedDockets)>0){
                            $docket[] =   $row;
                        }else{
                            $nonAssigned[]    =   $row;
                        }
                    }
                    ?>
                @endif
                @if(@$docket)
                    @foreach($docket as $row)
                        <tr @if($row->assignedDockets->count()==0)style="background: #f6f6f6" @endif>
                            {{--<td>{{ $row->id }}</td>--}}
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                            <td>@if($row->assignedDockets->count()==0)No @else Yes @endif</td>
                            <td>
                                <a href="#" class="btn btn-danger btn-xs btn-raised"  style="margin:0px 5px 0px;">Deleted</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                @if(@$nonAssigned)
                    @foreach($nonAssigned as $row)
                        <tr @if($row->assignedDockets->count()==0)style="background: #f6f6f6" @endif>
                            {{--<td>{{ $row->id }}</td>--}}
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                            <td>@if($row->assignedDockets->count()==0)No @else Yes @endif</td>
                            <td>
                                <a href="#" class="btn btn-danger btn-xs btn-raised"  style="margin:0px 5px 0px;">Deleted</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                @if(count(@$dockets)==0)
                    <tr>
                        <td colspan="6">

                            <center>Data Empty</center>

                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>
    <!-- Modal -->
    @if(Session::get('company_id')==1)
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
            <div id="second" class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div style="   padding-top: 0px;padding: 0 !important;" class="modal-body">
                        {{ Form::open(['url' => 'dashboard/company/docketBookManager/saveTempDocket','class'=>'form-horizontal','id'=>"stepForm"]) }}
                        <button style="    color: #fff;position: absolute;right: 13px;z-index: 100000;top: 17px;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <div id="wizard">
                            <h1>Docket Info</h1>
                            <div>
                                <div class="form-group col-md-12  " style="margin: -24px 0px 0px -13px;">
                                    <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Docket Name</label>
                                    <div class="gorm-group is-empty">
                                        <input type="text" name="docketTitle" class="form-control" required="required" value="{!! old('docketTitle') !!}">

                                    </div>
                                    <input type="hidden" name="helpFlag" id="helpFlag" value="false">
                                </div>
                                <div class="form-group col-md-12 " style="margin: 0; border-bottom: 1px solid #d2d2d2;    width: 97%;">
                                    <div class="col-md-6" style=" padding: 0;     margin-left: -17px;">
                                        <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Timer Attachement</label>
                                    </div>
                                    <div class="col-md-6">
                                        {{--<select class="select" name="timer_attachement" id="timer_attachement">--}}
                                        {{--<option value="0">No</option>--}}
                                        {{--<option value="1">Yes</option>--}}
                                        {{--</select>--}}
                                        <div style="position:relative; float: right;margin-top: 23px;    margin-right: -40px;">
                                            <select style="    margin-bottom: -9px;" class="form-control" name="timer_attachement" id="timer_attachement">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>

                                        {{--<input style="float: right;margin-top: 23px;    margin-right: -39px;" type="checkbox" name="timer_attachement" id="timer_attachement" value="1">--}}
                                    </div>

                                </div>

                                <div class="form-group col-md-12 " style="margin: 0; border-bottom: 1px solid #d2d2d2;    width: 97%;     margin-top: 16px;">
                                    <div class="col-md-6" style=" padding: 0;     margin-left: -17px;">
                                        <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Xero Timesheet</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="position:relative; float: right;margin-top: 23px;    margin-right: -40px;">
                                            <select style="    margin-bottom: -9px;" class="form-control" name="xero_timesheet" id="xero_timesheet">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div style="position: absolute; bottom: 19px;">
                                    <i style="font-size: 15px;color: #a1a2a1;">* Timer attachement allows you to attach timer data to your dockets.</i>

                                </div>

                            </div>

                            <h1>Method of Dockets Approval</h1>
                            <div>
                                <section class="step" data-step-title="Method of Dockets Approval" style="    height: 1px;    float: left;">
                                    <h3 style="    font-size: 20px;margin-top: -9px;" class="inco1"><i class="fa fa-info-circle" aria-hidden="true"></i>  How would you
                                        like your Customers/employees to approve your docket?</h3>
                                    <div class="row">
                                        <br><br><br>
                                        <div style="    padding-left: 37px;margin-top: -45px;" class="col-md-12">
                                            <input type="hidden" name="docketApprovalType" id="docketApprovalValue" value="0">
                                            <input type="hidden" name="invoiceable" id="invoicable1">
                                            <div class="option1">
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <h5>Option 1: &nbsp<span style="font-weight: 400;">Default Approval with button</span></h5>
                                                        <input type="checkbox"   style="float: left;margin-right: 12px;" id="buttonApprovess"  checked disabled value="0">
                                                        <p>Customers and Employees can approve dockets by clicking "Approve"
                                                            button.</p>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <img src="{{asset("assets/dashboard/images/recordtimeApproval.jpg")}}" width="100%" style="border: 1px solid #eee;border-radius: 5px;margin-bottom: 35px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="option2">
                                                <div class="row">
                                                    <div class="col-md-7">
                                                        <h5>Option 2: &nbsp<span style="font-weight: 400;">Require "Name" and "Signature" for approvals</span></h5>
                                                        <input style="    float: left;margin-right: 12px;" type="checkbox" id="buttonAuthorisess"  value="1">
                                                        <p>Customers and Employees are required to "Sign" the dockets to
                                                            authorise approval.</p>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <img src="{{asset("assets/dashboard/images/recordtimeApprovalType2.jpg")}}" width="100%" style="border: 1px solid #eee;border-radius: 5px;">
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <h1>Do you want to make this Template Invoiceable?</h1>
                            <div>
                                <section style="    height: 1px;    float: left;" class="step" data-step-title="Do you want to make this Template Invoiceable?">
                                    <h3 style="    margin-top: -19px;font-size: 20px;" class="inco"><i style="margin-left: -19px;" class="fa fa-info-circle" aria-hidden="true"></i> <span>Do you want to make this Template Invoiceable? </span></h3>
                                    <h5 class="inco"> <span>Invoiceable
                                    dockets can be attached/used to create an invoice and send it to a Record Time
                                        application user.</span></h5>
                                    <div class="row">
                                        <div style="    padding-left: 37px;margin-top: 0px; height: 409px;" class="col-md-12">
                                            <div class="form-group col-md-2" style=" padding-left: 18px;">

                                                <a style="padding-left: 20px;padding-right: 30px;padding-bottom: 5px;padding-top: 7px;font-size: 12px;border-radius: 5px;background: #00b6bc;border: 1px solid #00b6bc;" class="btn btn-xm btn-raised btn-success invoiceableYesButton " data-id="1"><i class="fa fa-check" aria-hidden="true"></i> Yes</a>
                                            </div>
                                            <div class="form-group col-md-2" style=" padding-left: 50px;">
                                                <a style="padding-left: 20px;padding-right: 30px;padding-bottom: 5px;padding-top: 7px;font-size: 12px;border-radius: 5px;background: #f50000;border: 1px solid #f50000;" type="submit" class="btn btn-xm btn-raised btn-danger invoiceableNoButton"  data-id="0"><i class="fa fa-times" aria-hidden="true"></i> No</a>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12" style="overflow: hidden">


                                {{ Form::close() }}
                            </div>
                        </div>
                    </div><!--/.modal-body-->


                </div>
            </div>
        </div>
    @else
        <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
            <div id="second"  class="modal-dialog modal-lg" role="document">
                {{--<div id="model" data-target="#myModal"></div>--}}
                <div class="modal-content">
                    <div class="modal-header themeSecondaryBg">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Docket</h4>
                    </div>
                    {{ Form::open(['url' => 'dashboard/company/docketBookManager/saveTempDocket', 'files' => true]) }}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group label-floating">
                                    <label class="control-label" for="title">Docket Name</label>
                                    <input type="text" name="docketTitle" class="form-control" required="required" value="{!! old('docketTitle') !!}">
                                    <input type="hidden" name="helpFlag" id="helpFlag" value="false">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Next</button>
                    </div>
                    {{ Form::close() }}
                </div>

            </div>
        </div>
    @endif






    <div class="modal fade" id="designDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/designDocket','method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="design_docket" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="importExport" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Export/Import Dockets Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-cloud-upload" aria-hidden="true"></i> Export</a></li>
                                <li><a data-toggle="tab" href="#menu1"><i class="fa fa-cloud-download" aria-hidden="true"></i> Import</a></li>

                            </ul>
                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active">
                                    <h5 style="margin-top: 20px;">Export File Name</h5>
                                    {{ Form::open(['url' => 'dashboard/company/downloadJSONFile' , 'files' => true]) }}

                                    <select name="docket_id" class="form-control">
                                        <option >Select Dockets Template </option>
                                        @if(@$dockets)
                                            @foreach($dockets as $row)
                                                <option value="{{ $row->id }}">{{ $row->title }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <button type="submit" style=" color: #fff;font-weight: 700;border-radius: 0px;"  class="btn btn-primary importexport">Export Dockets Template </button>
                                    {{ Form::close() }}
                                </div>
                                <div id="menu1" class="tab-pane fade">
                                    <h5 style="margin-top: 20px;">Import Docket Template File</h5>
                                    {{ Form::open(['url' => 'dashboard/company/uploadJSONFile' , 'files' => true]) }}
                                    <div class="from-group">
                                        <input type="text" class="form-control" name="docket_title" placeholder="Docket Name" required>
                                    </div>
                                    <div class="form-group ">
                                        <input type="file" id="image" name="files">
                                        <input type="text" readonly="" class="form-control" placeholder="Select File To Import">
                                        <i style="font-size:12px;color:#999;">File Type : txt only</i>
                                    </div>
                                    <button type="submit" style=" color: #fff;font-weight: 700;border-radius: 0px;"   class="btn btn-primary importexport" >Import Dockets Template </button>
                                    {{ Form::close() }}
                                </div>


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="archiveDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/archiveDocket','method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Archive Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="archive_docket" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to Archive this template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <style>
        .modal .popover{
            top: 139px !important;
            left: 20px !important;
        }
        .templet-trash{
            padding: 2px 0px 2px 0px;
        }
        .modal-body .nav-tabs{
            margin-top: -24px;
            background: #0d3454;
            border: 1px solid #00000045;
            margin-left: -24px;
            width: 598px;
        }
        .importexport{
            width: 100%;
            background: #0d3453;

        }
        .btn:not(.btn-raised):not(.btn-link):hover{
            background-color: rgb(13, 52, 83);
            border-color: transparent;
        }
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus {
            color: #555;
            cursor: default;
            background-color: #15b1b8 !important;
            border: 1px solid #ddd;
            border-radius: 0px;
            border-bottom-color: transparent;
        }
        .rtTab{

            margin-bottom: 20px;
        }
        .rtTab .rtTabHeader{
            background-color: #fff;

        }
        .rtTab .rtTabHeader ul{
            list-style-type: none;
            padding: 0px 0px;
            margin: 0px 0px;
            font-size: 14px;
            font-weight: 500;
        }
        .rtTabHeader ul li{
            display: inline-block;
        }
        .rtTabHeader ul li.active{
            color: #000;
            border-bottom: 2px solid #15B1B8;
        }
        .rtTabHeader ul li a{
            color: inherit;
            padding: 18px 30px;
            display: block;
            text-decoration: none;
        }
        .rtTabHeader ul li a:hover{
            color: #000;
        }
        .rtTabHeader ul li.advacedFilter{
            float: right;
        }
        .rtTabHeader ul li.advacedFilter i{
            font-size: 20px;
        }
        .rtTabHeader ul li.advacedFilter a{
            color: #15B1B8;
            padding-right:15px;
        }
        .btn-group-raised .btn.btn-warning, .btn-group-raised .input-group-btn .btn.btn-warning, .btn.btn-fab.btn-warning, .btn.btn-raised.btn-warning, .input-group-btn .btn.btn-fab.btn-warning, .input-group-btn .btn.btn-raised.btn-warning {
            background-color: #fc7c5f;
            color: rgba(255,255,255,.84);
        }

    </style>
@endsection

@section('customScript')
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/jquery.steps.css') }}">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    <script src="{{asset('assets/dashboard/js/jquery.steps.min.js')}}"></script>
    {{--<script src="{{asset('assets/dashboard/tour/jquery.min.js')}}"></script>--}}
    {{--<script src="{{asset('assets/dashboard/tour/bootstrap.js')}}"></script>--}}
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{asset('assets/dashboard/tour/script.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatable').DataTable({
                "order": [ 3, 'desc' ]
            });
        } );
    </script>
    <script>
        $(document).ready(function() {
            $('#designDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#design_docket").val(id);
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $("#wizard").steps({
                enableFinishButton:false,

            });


            $('#myModal3').on('shown.bs.modal', function () {
                $('#myWizard').easyWizard();
            });
        });
    </script>
    <script>
        $(document).on('click', '#buttonApprovess', function () {
            var yesDocket = document.getElementById('buttonAuthorisess');
            var yesDocketaa = document.getElementById('buttonApprovess');
            yesDocket.checked = false;
            yesDocketaa.disabled=true;
            yesDocket.disabled = false;
            $("#docketApprovalValue").val(0);
        });
        $(document).on('click', '#buttonAuthorisess', function () {
            var noDocket = document.getElementById('buttonApprovess');
            var noDocketaa = document.getElementById('buttonAuthorisess');
            noDocket.checked = false;
            noDocketaa.disabled=true;
            noDocket.disabled = false;
            $("#docketApprovalValue").val(1);
        });

    </script>


    <script>
        $(document).on("click",".invoiceableYesButton",function () {
            var docketTitles = $('input[name=docketTitle]').val();
            var docketApprovalTypes = $('input[name=docketApprovalType]').val();
            var timer = $(this).find(":checked").val();
            $("#invoicable1").val($(this).attr('data-id'));
            $( "#stepForm" ).submit();
        });
        $(document).on("click",".invoiceableNoButton",function () {
            var docketTitles = $('input[name=docketTitle]').val();
            var docketApprovalTypes = $('input[name=docketApprovalType]').val();
            var timer = $(this).find(":checked").val();
            $("#invoicable1").val($(this).attr('data-id'));
            $( "#stepForm" ).submit();
        });
    </script>



    <script>
        $(document).ready(function() {
            $('#archiveDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#archive_docket").val(id);
            });
        });
    </script>
@endsection