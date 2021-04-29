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

    <div class="rtTab" style="margin: 0px;min-height: 400px; background: none; ">
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
                <div class="viewFolder" style="padding-left: 0;     min-height: 529px;background: #ffffff;">

                    <div class="rtTabHeader">
                        <ul>
                            <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;">All Draft</h4></li>
                        </ul>
                    </div>

                    <div class="rtTabContent" style="padding: 10px 10px 40px 10px;">
                        @php $docketDraft = true  @endphp
                        <table class="table" id="datatable" >
                            <thead>
                                <tr>
                                    <th>Draft Id</th>
                                    <th>Draft Title</th>
                                    <th>Docket Name</th>
                                    <th>Recipient</th>
                                    <th>Date Added</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(@$docketDrafts)
                                @foreach($docketDrafts as $row)
                                    @include('dashboard.company.docketManager.partials.table-view.docket-draft-row')
                                @endforeach
                            @endif
                            @if(count(@$docketDrafts)==0)
                                <tr><td colspan="9"><center>Data Empty</center></td></tr>
                            @endif
                            </tbody>
                        </table>
                    </div><!--/.rtTabContent-->
                </div>
            </div>
        </div>
    </div>
    <br/><br/>
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
        .rtTab{
            background: #fff;
            margin-bottom: 20px;
        }
        .rtTab .rtTabHeader{

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
        .tableHeaderMenu{
            background: #FBFBFB;
            min-height: 66px;
            width: 100%;
            border-top: 1px solid #EEEEEE;
            border-bottom: 1px solid #EEEEEE;
            color: #797979;
            font-weight: normal;
        }
        .tableHeaderMenu ul{
            list-style-type: none;
            margin: 3px 0px;
            padding: 0px;
        }
        .tableHeaderMenu ul li{
            padding: 15px 5px 0px 15px;
            display: inline-block;
        }
        .tableHeaderMenu .rtMenuBtn{
            background:none;
            border: 1px solid #15B1B8;
            height: 26px;
            font-size: 12px;
            border-radius: 13px;
            padding: 0px 15px;
            color: #797979;
        }
        .tableHeaderMenu .rtMenuBtn:hover{
            color: #000;
        }
        .rtTabContent{
        }
        .rtMenuSearch{
            border: 1px solid #E1E1E1;
            border-radius: 5px;
            margin-top: -2px;
            margin-left: 10px;
            margin-right: 10px;
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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('V2') }}"/>
    <script src="{{  asset('V2') }}"></script>
    {{--        <script src="{{  asset('assets/folder/v2/function.js') }}"></script>--}}

    <script tV2e="text/javascript">
        $(document).ready(function() {

            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
        } );
        $("#empolyees").chained("#company");


        $('#datatable').dataTable({ 
            "ordering": false,
        });

    </script>
@endsection
