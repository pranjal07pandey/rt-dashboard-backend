@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Filter</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">Filter</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')


    <div class="rtTab" style="background: #fff;margin: 0px;min-height: 400px;">
        <div class="rtTabHeader">
            <ul>
                <li><a href="{{ route('dockets.allDockets') }}">All Dockets</a></li>
                <li><a href="{{ route('dockets.sentDockets') }}">Sent Dockets</a></li>
                <li><a href="{{ route('dockets.receivedDockets') }}">Received Dockets</a></li>
                <li class="active"><a href="{{ route('dockets.emailedDockets') }}">Emailed Dockets</a></li>
            </ul>
        </div>
        <div class="rtTabContent">
            <div class="tableHeaderMenu col-md-12">
                <div class="filterDiv" style="margin-bottom: 20px;    padding-top: 34px;">
                    {{ Form::open(['url' => 'dashboard/company/docketBookManager/filterEmail/']) }}
                    <input type="hidden" name="type" value="{{ $request->type }}">
                    <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: 10px;padding-bottom: 5px;">Advanced
                        Filter </strong>
                    <button type="submit" class="btn btn-success"
                            style="position: absolute;top: 19px;right: 15px;margin: 0px;">Filter
                    </button>
                    <div class="row">
                        <div class="col-md-12">
                            <div style="border-bottom: 1px solid #ddd;    background-color: #f6f6f6;padding: 15px;">
                                <div class="row">

                                    <div class="col-md-12">
                                        <strong class="pull-left">&nbsp;</strong>
                                        <div class="pull-right">
                                            <strong>Filter for Invoicing</strong>&nbsp;
                                            <input type="checkbox" class="docketPreviewCheckboxInput" value="1"
                                                   name="invoiceable">
                                        </div>
                                        <div class="clearfix"></div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group" style="margin-top:0px;">
                                                    <label for="templateId" class="control-label">Receiver Email</label>
                                                    <input type="text" class="form-control" name="email">
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
                                                        <select id="docketId" class="form-control"
                                                                name="docketTemplateId">
                                                            <option value="">Select Docket Template</option>
                                                            @if($docketusedbyemail)
                                                                @foreach($docketusedbyemail as $row)
                                                                    <option value="{{ $row->docket_id }}">{{ $row->docketInfo->title }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div style="position: absolute;right: 0px;top: 10px;"><i
                                                                    class="fa fa-angle-down"></i></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group" style="margin-top:0px;">
                                                    <label for="templateId" class="control-label">Docket Id</label>
                                                    <input type="text" class="form-control" name="docketId"
                                                           value="{{ $request->docketId }}">
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <br/>
                                                <strong>By Date</strong>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group" style="margin-top:0px;">
                                                            <label for="templateId" class="control-label">Date
                                                                Type</label>
                                                            <div style="position:relative">
                                                                <select id="company" class="form-control" name="date">
                                                                    <option value="2"
                                                                            @if($request->date==2) selected @endif >
                                                                        Inside docket date (User Selected date)
                                                                    </option>
                                                                    <option value="1"
                                                                            @if($request->date==1) selected @endif >
                                                                        Outside docket date (docket creation date)
                                                                    </option>
                                                                </select>
                                                                <div style="position: absolute;right: 0px;top: 10px;"><i
                                                                            class="fa fa-angle-down"></i></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group" style="margin-top:0px;">
                                                            <label for="templateId" class="control-label">From</label>
                                                            <input type="text" class="form-control dateInput datepicker"
                                                                   dateType="docketCreated" name="from"
                                                                   value="{{ $request->from }}" id="fromDatePicker">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group" style="margin-top:0px;">
                                                            <label for="templateId" class="control-label">To</label>
                                                            <input type="text" class="form-control dateInput"
                                                                   dateType="docketCreated" id="toDatePicker"
                                                                   value="{{ $request->to }}" name="to">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <button style="position: absolute;left: 209px;margin-top: 44px;    background: none;border: 1px solid #15b1b8;height: 26px;font-size: 12px;border-radius: 13px;padding: 0px 15px;color: #797979;" class="rtMenuBtn" id="exportcsv">Export .csv</button>

                <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: -1px;padding-bottom: 5px;">Results</strong>
            </div>

            <table class="rtDataTable table"  id="datatable">
                <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox " value="1"  name="employed[]" >
                            <span class="checkbox-material"><span class="check"></span></span>
                        </label>
                    </th>
                    <th>Docket Id @if(strlen($request->docketId)>0) <i class="fa fa-check" style="color:green"></i> @endif</th>
                    <th>Info </th>
                    <th>Docket Name @if(strlen($request->docketTemplateId)>0) <i class="fa fa-check" style="color:green"></i> @endif</th>
                    <th>Date Added @if(strlen($request->from)>0 || strlen($request->to)>0) <i class="fa fa-check" style="color:green"></i> @endif </th>
                    <th>Status</th>
                    <th width="150px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn=1; ?>
                @if(@$sentEmailDockets)
                    @foreach($sentEmailDockets as $row)
                        <tr>
                            <td>
                                <label>
                                    <input type="checkbox" class="checkbox selectitem forEmailDocket" value="{{ $row->id }}"  name="emailDocketId[]"  >
                                    <span class="checkbox-material"><span class="check"></span>
                                        </span>
                                </label>
                            </td>
                            <td><span class="blackLabel"> {{ $row->id }}</span></td>
                            <td>
                                <span class="blackLabel">Sender</span>
                                <span class="userInfo"> {{ $row->sender_name }}<br/></span>
                                {{ @$row->company_name }}<br/><br>
                                <span class="blackLabel">Receiver</span>
                                <span class="userInfo">
                                                @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp
                                    @foreach($row->recipientInfo as $recipient)
                                        @php
                                            $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;
                                            $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;

                                            if($row->recipientInfo->count()>1){
                                               if($row->recipientInfo->last()->id!=$recipient->id){
                                                    $recipientNames =  $recipientNames.", ";
                                                    $recipientCompany = $recipientCompany.", ";
                                                }
                                            }
                                        @endphp
                                    @endforeach
                                    {{ $recipientNames }}<br></span>
                                {{ $recipientCompany }}



                                @if(count($row->sentEmailDocketLabels)>0)
                                    <div style="height: 30px;">
                                        <div style="position: absolute;" class="emailDocketLabelIdentify{{$row->id}}">
                                            @foreach($row->sentEmailDocketLabels as $sentDocLabel)
                                                {{--{{ $sentDocLabel->docketLabel->company_id }}--}}
                                                @if($sentDocLabel->docketLabel->company_id==Session::get('company_id'))
                                                    <span style=" background: {{$sentDocLabel->docketLabel->color}};display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;" class="badge badge-pill badge-primary emailDocketDelete{{$sentDocLabel->id}}">
                                                <img style="margin-right: 2px" src="{{ AmazoneBucket::url() }}{{ $sentDocLabel->docketLabel->icon }}" height="10" width="10">  {{ $sentDocLabel->docketLabel->title }}
                                                                    <button  data-toggle="modal" data-target="#deleteLabel" data-type="2" data-id="{{$sentDocLabel->id}}"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;">
                                                    <span  class="glyphicon glyphicon-remove" aria-hidden="true"  />
                                                </button>
                                            </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                @else
                                    <div style="height: 30px;">
                                        <div style="position: absolute;" class="emailDocketLabelIdentify{{$row->id}}">
                                        </div>
                                    </div>

                                @endif



                            </td>
                            <td>{{ $row->docketInfo->title }}
                                @if(@$row->docketInfo->previewFields->count()>0)
                                    <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                                        @foreach(@$row->docketInfo->previewFields as $previewField)
                                            @if($previewField->docket_filed_info->docket_field_category_id==5 || $previewField->docket_filed_info->docket_field_category_id == 9)
                                                @if(@count(\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                    <ul class="sentDocketImagePreview">
                                                        @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                            <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value}}"></a></li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @elseif($previewField->docket_filed_info->docket_field_category_id == 7)
                                                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                @if(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue)
                                                    <?php $sn = 1; $total = 0; ?>
                                                    @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue as $unitRate)
                                                        {{$unitRate->docketUnitRateInfo->label}} : @if($unitRate->docketUnitRateInfo->type==1) $ @endif {{ $unitRate->value }} &nbsp;&nbsp;&nbsp;
                                                        @if($sn == 1)
                                                            <?php $total = $unitRate->value; ?>
                                                        @else
                                                            <?php $total    =   $total*$unitRate->value; ?>
                                                        @endif
                                                        <?php $sn++; ?>
                                                    @endforeach
                                                    <strong>Total:</strong>
                                                    <strong>$ {{ $total }}</strong>
                                                @endif
                                            @elseif($previewField->docket_filed_info->docket_field_category_id == 8)

                                                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==0)
                                                    <span>No</span>
                                                @elseif(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==1)
                                                    <span>Yes</span>
                                                @endif
                                                {{--<span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>--}}
                                            @else
                                                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                <span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>

                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
                            </td>
                            <td>
                                @if($row->company_id==Session::get('company_id'))
                                    @if($row->status==0)
                                        <span class="label label-warning">Received</span>
                                    @endif
                                @else
                                    @if($row->status==0)
                                        <span class="label label-primary">Sent</span>
                                    @endif
                                @endif

                                @if($row->status==1)
                                    <span class="label label-success">Approved</span>
                                @endif

                            </td>
                            <td>

                                <a href="{{ url('dashboard/company/docketBookManager/docket/view/emailed/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
                                @if(count($sentDocketLabel)==0)
                                    <a   data-toggle="modal" data-target="#noFolderLabeling" data-id="{{$row->id}}" data-companyid="{{$row->company_docket_id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                                @else
                                    <a data-toggle="modal" data-target="#folderLabeling" data-id="{{$row->id}}" data-companyid="{{$row->company_docket_id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                                @endif
                            </td>
                        </tr>

                    @endforeach
                @endif
                @if(count(@$dockets)==0)
                    <tr>
                        <td colspan="9">
                            <center>Data Empty</center>
                        </td>
                    </tr>
                @endif
                </tbody>

            </table>
            <div class="clearfix"></div>



        </div>

    </div>



    <br/><br/>
    @include('dashboard.company.folder.folderPopupModal')

    <style>
        .sentDocketImagePreview {
            margin: 0px;
            padding: 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .sentDocketImagePreview li {
            display: inline-block;
            margin-right: 10px;
        }

        .sentDocketImagePreview {
            margin: 0px;
            padding: 0px;
            list-style-type: none;
            margin-bottom: 10px;
            margin-top: 5px;
        }

        .sentDocketImagePreview li {
            display: inline-block;
            margin-right: 10px;
        }

        .badge .btn {
            display: none;
        }

        .badge:hover .btn {
            display: inline-block;
            cursor: pointer;

        }

        .badge:hover {
            cursor: pointer;

        }

        .rtTab {
            background: #fff;
            margin-bottom: 20px;
        }

        .rtTab .rtTabHeader {

        }

        .rtTab .rtTabHeader ul {
            list-style-type: none;
            padding: 0px 0px;
            margin: 0px 0px;
            font-size: 14px;
            font-weight: 500;
        }

        .rtTabHeader ul li {
            display: inline-block;
        }

        .rtTabHeader ul li.active {
            color: #000;
            border-bottom: 2px solid #15B1B8;
        }

        .rtTabHeader ul li a {
            color: inherit;
            padding: 18px 30px;
            display: block;
            text-decoration: none;
        }

        .rtTabHeader ul li a:hover {
            color: #000;
        }

        .rtTabHeader ul li.advacedFilter {
            float: right;
        }

        .rtTabHeader ul li.advacedFilter i {
            font-size: 20px;
        }

        .rtTabHeader ul li.advacedFilter a {
            color: #15B1B8;
            padding-right: 15px;
        }

        .tableHeaderMenu {
            background: #FBFBFB;
            min-height: 66px;
            width: 100%;
            border-top: 1px solid #EEEEEE;
            border-bottom: 1px solid #EEEEEE;
            color: #797979;
            font-weight: normal;
        }

        .tableHeaderMenu ul {
            list-style-type: none;
            margin: 3px 0px;
            padding: 0px;
        }

        .tableHeaderMenu ul li {
            padding: 15px 5px 0px 15px;
            display: inline-block;
        }

        .tableHeaderMenu .rtMenuBtn {
            background: none;
            border: 1px solid #15B1B8;
            height: 26px;
            font-size: 12px;
            border-radius: 13px;
            padding: 0px 15px;
            color: #797979;
        }

        .tableHeaderMenu .rtMenuBtn:hover {
            color: #000;
        }

        .rtDataTable {
            width: 100%;
        }

        .rtDataTable th {
            padding: 16px;
            color: #000;
            font-weight: 500;
            background: #F7F7F7;
            border-bottom: 1px solid #EEEEEE;
        }

        .rtDataTable td {
            padding: 16px;
            border-bottom: 1px solid #EEEEEE;
            color: #000;
            vertical-align: top;
        }

        .rtDataTable td .btn {
            margin-top: 0px;
        }

        .rtDataTable td .label {
            border-radius: 10px;
            padding: 4px 15px;
            font-size: 10.5px;
        }

        .rtDataTable td span.invoiceAmount {
            font-weight: bold;
            color: #EF9500;
        }

        .rtDataTable td .userInfo {
            color: #15B1B8;
            display: block;
            font-weight: normal;
        }

        .rtDataTable td .blackLabel {
            color: #000;
            font-weight: normal;
            display: block;
            margin-bottom: 5px;
        }

        .rtDataTable tfoot td {
            border-bottom: none;
            color: #787878;
            font-weight: normal;
        }

        .rtTabContent {
        }

        .rtMenuSearch {
            border: 1px solid #E1E1E1;
            border-radius: 5px;
            margin-top: -2px;
            margin-left: 10px;
            margin-right: 10px;
        }

        .rtDataTable .pagination {
            margin: 0px;
            font-weight: normal;
        }

        .rtDataTable #pagination > li:first-child > a, .rtDataTable .pagination > li:first-child > span {
            border-bottom-left-radius: 18px;
            border-top-left-radius: 18px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .rtDataTable .pagination > li:last-child > a, .rtDataTable .pagination > li:last-child > span {
            border-bottom-right-radius: 18px;
            border-top-right-radius: 18px;
            padding-left: 15px;
            padding-right: 15px;
        }

        .rtDataTable .pagination > .active > a, .rtDataTable .pagination > .active > a:focus, .rtDataTable .pagination > .active > a:hover, .rtDataTable .pagination > .active > span, .rtDataTable .pagination > .active > span:focus, .rtDataTable .pagination > .active > span:hover {
            background: #15B1B8;
            color: #fff;
        }

        .rtDataTable .pagination > li > a, .rtDataTable .pagination > li > span {
            color: #787878;
        }
        .dataTables_filter{
            margin-top: 20px;
            margin-right: 10px;
        }
        #datatable_length{
            margin-top: 20px;
            margin-left: 10px;
        }
    </style>
@endsection

@section('customScript')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="{{ asset('V2') }}"/>
    <script src="{{  asset('V2') }}"></script>
    <script src="{{  asset('V2') }}"></script>
    {{--<script type="text/javascript">--}}
    {{--$(document).ready(function() {--}}
    {{--$( function() {--}}
    {{--$( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});--}}
    {{--$( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});--}}
    {{--} );--}}
    {{--$('#datatable').DataTable();--}}
    {{--} );--}}
    {{--</script>--}}
    <script type="text/javascript">
        $(document).ready(function () {
            $(function () {
                $("#toDatePicker").datepicker({dateFormat: 'dd-mm-yy'});
                $("#fromDatePicker").datepicker({dateFormat: 'dd-mm-yy'});
            });
            $.fn.dataTable.moment('D-MMM-YYYY');
            $('#datatable').dataTable({
                "order": [[4, "desc"]]
            });
        });
    </script>
    <script>
        $(document).on("change", "th label .checkbox", function () {
            if ($(this).is(":checked"))
            {
                $("td label .checkbox").prop('checked', true);
            }
            else
            {
                $("td label .checkbox").prop('checked', false);
            }
        })
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
