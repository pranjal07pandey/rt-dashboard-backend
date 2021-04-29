@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Bank</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Docket Bank</li>
        </ol>
        <div class="clearfix"></div>
{{--        <a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="row" style="min-height: 400px;">
        <div class="col-md-12">
            <div class="conta" data-ref="container">
                <div class="searchField" style="margin-top: -15px;margin-right: 4px;margin-bottom: 8px;">
                    <div style="float: right">
                        <input  style="border: solid 1px #ccc;border-radius: 5px;    border-radius: 5px;text-indent: 10px;" placeholder="Search" id="searchInput" placeholder="" @if(@$searchKey) value="{{ @$searchKey }}" @endif >
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="docketBankData">
                    <div class="row">
                        @if ($templateBank)
                            @php $sn=1; @endphp
                            @foreach($templateBank as $row)

                                @if($row->company || $row->user)
                                <div class="mix col-md-3" style="padding-bottom: 25px;margin: 0px -15px 0px 0px; width: calc( 25% + 10px) !important;" >
                                    <div style="    background: #ffffff;">
                                        <div class="overlay">
                                            <img src="{{asset('assets/bank.png')}}" style="width:100%">
                                        </div>
                                        <div class="price-detail" style="padding-left: 9px; padding-bottom: 15px; padding-right: 9px;">
                                            <h4 style="font-size: 14px; font-weight: 500;">{{@$row->docket->title}}</h4>
                                            <p style="color: #777777; font-size: 12px;margin-bottom: 6px;"> <i class="fa fa-user-circle" aria-hidden="true"></i> {{$row->user->first_name." ".$row->user->last_name }}</p>
                                            <p style="color: #777777; font-size: 12px;margin-bottom: 6px;"> <i class="fa fa-building" aria-hidden="true"></i> {{$row->company->name }}</p>
                                            <span style="color: #777777; font-size: 12px;" class="pull-left"><i class="fa fa-calendar" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</span>
                                            <span  style="color: #777777; font-size: 12px;"  class="pull-right"><i class="fa fa-download" aria-hidden="true"></i> {{$row->downloads}}</span>
                                            <div class="clearfix"></div>
                                            <hr style="margin: 10px 0px; padding: 0px;"/>
                                            <button class="btn btn-info pull-left previewTemplate"  style="padding:3px 8px 3px 8px;border: 1px solid #15B1B8;color: #15B1B8;font-size: 12px;font-weight: 400;margin: 0px;"  data-url="{{ url('dashboard/company/templateBank/preview',$row->id) }}"> Preview</button>
                                            <button class="btn btn-info pull-right" class="installTemplate" data-toggle="modal" data-target="#installTemplate" style="padding:3px 8px 3px 8px;border: 1px solid #15B1B8;color: #15B1B8;font-size: 12px;font-weight: 400;margin: 0px;" data-id="{{$row->id}}" data-title="{{@$row->docket->title}}"> Install</button>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($sn==4) <div class="clearfix"></div>
                                @php $sn = 0; @endphp @endif
                                @php $sn++; @endphp
                            @endforeach
                        @endif
                    </div>
                    <div>
                        <span style="padding-top: 29px; color: #757575;" class="pull-left">Showing  {{ $templateBank->firstItem() }} to     {{ $templateBank->lastItem() }} of {{ $templateBank->total() }} entries</span>
                        <span class="pull-right">{{ $templateBank->appends(['items'=>10]) ->links() }}</span>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br/><br/>
    <!-- Modal -->

    <div class="modal fade" id="installTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/installDocketTemplate','method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Install Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docket_id" name="docket_id">
                            <span> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to add</span>  <strong id="titleTemplate"> </strong> <span>to your docket templates?</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="unpublishDocketTemplate">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close" >No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <div class="modal fade" id="previewTemplate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content" >
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                    <h4 class="modal-title" id="myModalLabel"> <i class="fa fa-plus"></i> Docket Template Preview</h4>
                </div>
                <span class="spinnerCheck" style="position:absolute;left:50%;bottom:50%;font-size: 51px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>

                <div class="modal-body mobilecontain" >

                </div>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <style>
        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: #15B1B8;
            border-color: #15B1B8;
        }
        ul{
            padding:0;
            list-style: none;
        }
        .input{
            border-top: none;
            border-right: none;
            border-left: none;
            width: 100%;
        }

        .mobilecontain {
            overflow-x: scroll;
            height: 600px;
            position: relative;
            padding: 8px;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/jquery.steps.css') }}">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    <script src="{{asset('assets/dashboard/js/jquery.steps.min.js')}}"></script>
    {{--<script src="{{asset('assets/dashboard/tour/jquery.min.js')}}"></script>--}}
    {{--<script src="{{asset('assets/dashboard/tour/bootstrap.js')}}"></script>--}}
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{asset('assets/dashboard/tour/script.js')}}"></script>

    <script>
        $(document).ready(function() {
            $('#installTemplate').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var title = $(e.relatedTarget).data('title');
                $("#titleTemplate").html(title)
                $("#docket_id").val(id);
            });
            $('.previewTemplate').on('click', function(){
                $('.spinnerCheck').css('display','block')
                $('#previewTemplate').modal('show')
                $('.mobilecontain').empty()
                var url = $(this).data("url");
                $.ajax({type: "GET",url: url,
                    success: function(res) {
                        $('.spinnerCheck').css('display','none')
                        var data = res;
                        $('#previewTemplate .modal-body').append(data);


                    },
                    error:function(request, status, error) {
                        console.log("ajax call went wrong:" + request.responseText);
                    }
                });
            });
        });

        $(document).ready(function() {

            var timer = null;
            $('#searchInput').keydown(function(){
                clearTimeout(timer);
                timer = setTimeout(doStuff, 1000)
            });

            function doStuff() {
                $(".docketBankData").html('<div style="position: absolute;left: 50%;top: 64%;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
                if($('#searchInput').val().length>0){
                    $.ajax({
                        type: "GET",
                        url: "{{ url('dashboard/company/searchDocketTemplate?search=') }}" + $('#searchInput').val(),
                        success: function(response){
                            if(response == ""){

                            }else{

                                $(".docketBankData").html(response).show();
                                $('.previewTemplate').on('click', function(){
                                    $('#previewTemplate').modal('show')
                                    $('.mobilecontain').empty()
                                    var url = $(this).data("url");
                                    $.ajax({type: "GET",url: url,
                                        success: function(res) {
                                            var data = res;
                                            $('#previewTemplate .modal-body').append(data);

                                        },
                                        error:function(request, status, error) {
                                            console.log("ajax call went wrong:" + request.responseText);
                                        }
                                    });
                                });
                            }
                        }
                    });
                }else{
                    $.ajax({
                        type: "GET",
                        data:{data:"all"},
                        url: "{{ url('dashboard/company/searchDocketTemplate?search=') }}",
                        success: function(response){
                            if(response == ""){

                            }else{
                                $(".docketBankData").html(response).show();
                                $('.previewTemplate').on('click', function(){
                                    $('#previewTemplate').modal('show')
                                    $('.mobilecontain').empty()
                                    var url = $(this).data("url");
                                    $.ajax({type: "GET",url: url,
                                        success: function(res) {
                                            var data = res;
                                            $('#previewTemplate .modal-body').append(data);

                                        },
                                        error:function(request, status, error) {
                                            console.log("ajax call went wrong:" + request.responseText);
                                        }
                                    });
                                });
                            }
                        }
                    });
                }
            }


            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
            $.fn.dataTable.moment( 'D-MMM-YYYY' );
            // $('#datatable').dataTable( {
            //     "order": [[ 0, "desc" ]]
            // } );
        } )
    </script>
@endsection