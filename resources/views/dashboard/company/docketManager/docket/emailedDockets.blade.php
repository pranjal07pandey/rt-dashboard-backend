@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li  class="active">Docket Book Manager</li>
        </ol>
        <div class="clearfix"></div>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="rtTab" style="margin: 0px;min-height: 400px;">
        <div class="row">

            <div class="col-md-3" >
                <div class="col-md-12" style="background-color: #fff; border:0px solid #000000;color: #505050;  padding-left: 0px;padding-right: 0px;">
                    @include('dashboard.company.docketManager.partials.dockets-side-nav')
                </div>
                <br>
                <div class="col-md-12" style="background-color: #fff;margin-top: 18px;  border:0px solid #000000; padding-left: 0px;padding-right: 0px;" >
                    @include('dashboard.company.folder-management.index')
                </div>
            </div>

            <div class="col-md-9 " style="padding-left: 0;">
                <div class="viewFolder" style="padding-left: 0;min-height: 529px;background: #fbfbfb;">

                    <div class="rtTabHeader">
                        <ul>
                            <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;">Emailed Dockets</h4></li>
                            <li class="advacedFilter"><a href = "#close" class='forum-title'  data-toggle="modal" data-target="#myModalFilter"><i class="material-icons">filter_list</i> Advanced Filter</a></li>
                        </ul>
                    </div>

                    <div class="rtTabContent">
                        @php $docketDraft = false  @endphp
                        @include('dashboard.company.docketManager.partials.table-view.table-header.table-header-menu')

                        <table class="rtDataTable datatable" >
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="checkbox" value="1"  name="employed[]"></th>
                                    <th>Docket Id</th>
                                    <th>Info</th>
                                    <th>Docket Name</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                    <th width="200px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(@$dockets)
                                    @php $docketCheckbox = true @endphp
                                    @foreach($dockets as $row)
                                        @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
                                    @endforeach
                                @endif
                                @if(count(@$dockets)==0)
                                    <tr><td colspan="9"><center>Data Empty</center></td></tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2"><span style="font-size:12px;">Showing  {{ $dockets->firstItem() }} to {{ $dockets->lastItem() }} of {{ $dockets->total() }} entries</span></td>
                                    <td colspan="6" class="text-right">
                                        @if(@$searchKey) {{ $dockets->appends(['search'=>$searchKey])->links() }}
                                        @else {{ $dockets->links() }} @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div><!--/.rtTabContent-->
                </div><!--/.viewFolder-->
            </div>
        </div>
    </div>
    <br/><br/>


    <div class="modal fade " id="myModalFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span style="color: #ffffff;" aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Advanced Filter</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketBookManager/filterEmail/']) }}
                {{--<input type="hidden" name="type" value="received">--}}
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-12">
                            <strong class="pull-left">&nbsp;</strong>
                            <div class="pull-right">
                                <strong>Filter for Invoicing</strong>&nbsp;
                                <input type="checkbox" class="docketPreviewCheckboxInput" value="1" name="invoiceable" >
                            </div>
                            <div class="clearfix"></div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top:0px;">
                                        <label for="templateId" class="control-label">Receiver Email</label>
                                        <input type="text" class="form-control"  name="email" >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <br/>
                            <strong>Docket Info</strong>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-top:0px;">
                                        <label for="docketId" class="control-label">Dockets</label>
                                        <div style="position:relative">

                                            <select id="docketId" class="form-control" name="docketTemplateId">
                                                <option value="">Select Docket Template</option>
                                                @if($docketusedbyemail)
                                                    @foreach($docketusedbyemail as $row)
                                                        <option value="{{ $row->docket_id }}">{{ $row->docketInfo->title }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" style="margin-top:0px;">
                                        <label for="templateId" class="control-label">Docket Id</label>
                                        <input type="text" class="form-control"  name="docketId">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <br/>
                                    <strong>By Date</strong>
                                    <div class="row">
                                        <div class="col-md-6" >
                                            <div class="form-group" style="margin-top:0px;">
                                                <label for="templateId" class="control-label">Date Type</label>
                                                <div style="position:relative">
                                                    <select id="company" class="form-control" name="date">
                                                        <option value="2" selected="selected">Inside docket date (User Selected date)</option>
                                                        <option value="1">Outside docket date (docket creation date)</option>
                                                    </select>
                                                    <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top:0px;">
                                                <label for="templateId" class="control-label">From</label>
                                                <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from"  id="fromDatePicker" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top:0px;">
                                                <label for="templateId" class="control-label">To</label>
                                                <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" value="" name="to">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>

    <style>
        .sentDocketImagePreview{
            margin: 0px;
            padding : 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .sentDocketImagePreview li{
            display: inline-block;
            margin-right: 10px;
        }
        .badge .btn {
            display: none;
        }
        .badge:hover .btn{
            display: inline-block;
            cursor: pointer;

        }
        .badge:hover{
            cursor: pointer;

        }

        .sentDocketImagePreview{
            margin: 0px;
            padding : 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        .sentDocketImagePreview li{
            display: inline-block;
            margin-right: 10px;
        }
        .badge .btn {
            display: none;
        }
        .badge:hover .btn{
            display: inline-block;
            cursor: pointer;

        }
        .badge:hover{
            cursor: pointer;

        }

        .shell{
            width:100%;


        }
        .shell a, shell a:link, shell a:visited, shell td a, shell td a:link,  shell td a:visited
        {
            text-decoration:none;
            color:#666666;
        }
        .shell a:hover{
            text-decoration:none;
            color:#000000;
        }
        .head {
            border-bottom:1px solid #eae9e9;
            font-size:14px;
            height: 51px;

        }
        .menu{
            margin-left:10px;
            padding-top:5px;
            float:left;
        }
        .menu a {
            margin-left: 5px;
        }
        .sign{
            float:right;
            margin-right: 20px;
        }
        .content {
            clear: both;
            margin-right:50px;
            padding:20px 20px 20px 20px;
            font-size: 12px;
        }


        .rtTab .menu ul{
            list-style-type: none;
            padding: 0px 0px;
            margin: 0px 0px 0 -10px;
            font-size: 14px;
            font-weight: 500;
        }
        .menu ul li{
            display: inline-block;
            width: 100%;
        }
        .menu ul li.active{
            color: #000;
            border-left: 4px solid #15B1B8;
            width: 100%;
        }
        .menu ul li a{
            color: inherit;
            padding: 12px 15px;
            display: block;
            text-decoration: none;
            border-top: 1px solid #ececec;
        }
        .menu ul li a:hover{
            color: #000;
        }
    </style>
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"></link>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <!-- <script  src="{{asset('assets/zepto-selector.chained.js')}}"></script> -->
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('V2') }}"/>
    <script src="{{  asset('V2') }}"></script>
{{--    <script src="{{  asset('assets/folder/V2/function.js') }}"></script>--}}

    <script type="text/javascript">
        $(document).ready(function() {
            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
        });
    </script>
    <script>
        $(document).on('click','#exportcsv',function () {
            if ($('.selectitem:checked').serialize()==""){
                alert("Please Select Docket");

            }else {
                window.open("{{ url('dashboard/company/docketBookManager/docket/exportEmailDocket')}}" +"?"+$('.selectitem:checked').serialize() ,"_blank");
            }
        });

    </script>
            <script type="text/javascript">
                var base_url = "{{ url('') }}";

            </script>
@endsection
