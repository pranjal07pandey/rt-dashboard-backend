@extends('layouts.companyDashboard')
@section('content')

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Invoice Manager
            <small>Filter</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Manager</a></li>
            <li class="active">Filter</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="rtTab">
        <div class="rtTabHeader">
            <ul>
                <li @if($request->type=="all") class="active" @endif ><a href="{{route('companyAllInvoices')}}" >All Invoices</a></li>
                <li  @if($request->type=="sent") class="active" @endif ><a href="{{ route('companyInvoiceManagerIndex') }}" >Sent Invoices</a></li>
                <li  @if($request->type=="received") class="active" @endif><a href="{{ route('companyReceivedInvoices') }}" >Received Invoices</a></li>
                <li><a href="{{ route('companyEmailedInvoices') }}" >Emailed Invoices</a></li>

            </ul>
        </div>
        <div class="clearfix"></div>
        <div class="rtTabContent">
            <div class="tableHeaderMenu" style="    min-height: 500px;    padding-top: 20px;">
             <div class="col-md-12" style="background-color: #fbfbfb;">
                 <div class="filterDiv" style="margin-bottom: 20px;">
                     {{ Form::open(['url' => 'dashboard/company/invoiceManager/filterInvoice/']) }}
                     <input type="hidden" name="type" value="{{ $request->type }}">
                     <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: 10px;padding-bottom: 5px;">Advanced Filter</strong>
                     <button type="submit" class="btn btn-success" style="position: absolute;top: -15px;right: 15px;margin: 0px;">Filter</button>
                     <div class="row">
                         <div class="col-md-12">
                             <div  style="border-bottom: 1px solid #ddd;    background-color: #f6f6f6;padding: 15px;">

                                 <div class="row">

                                     {{--<div class="col-md-12">--}}
                                     {{--<strong>Date</strong>--}}
                                     {{--<div class="row">--}}
                                     {{--<div class="col-md-6">--}}
                                     {{--<div class="form-group" style="margin-top:0px;">--}}
                                     {{--<label for="templateId" class="control-label">From</label>--}}
                                     {{--<input type="text" class="form-control datepicker"  name="from" value="{{ $request->from }}"  id="fromDatePicker" >--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--<div class="col-md-6">--}}
                                     {{--<div class="form-group" style="margin-top:0px;">--}}
                                     {{--<label for="templateId" class="control-label">To</label>--}}
                                     {{--<input type="text" class="form-control" id="toDatePicker" value="{{ $request->to }}" name="to">--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--<div class="col-md-12">--}}
                                     {{--<br/><br/>--}}
                                     {{--<strong>Invoice Info</strong>--}}
                                     {{--<div class="row">--}}
                                     {{--<div class="col-md-6">--}}
                                     {{--<div class="form-group" style="margin-top:0px;">--}}
                                     {{--<label for="docketTemplateId" class="control-label">Invoices</label>--}}
                                     {{--<select id="docketTemplateId" class="form-control" name="invoiceTemplateId">--}}
                                     {{--<option value="">Select Invoice Template</option>--}}
                                     {{--@if($invoices)--}}
                                     {{--@foreach($invoices as $row)--}}
                                     {{--<option value="{{ $row->id }}" @if($request->invoiceTemplateId==$row->id) selected @endif >{{ $row->title }}</option>--}}
                                     {{--@endforeach--}}
                                     {{--@endif--}}
                                     {{--</select>--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--<div class="col-md-6">--}}
                                     {{--<div class="form-group" style="margin-top:0px;">--}}
                                     {{--<label for="templateId" class="control-label">Invoice Id</label>--}}
                                     {{--<input type="text" class="form-control"  name="invoiceId" value="{{ $request->invoiceId }}">--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--</div>--}}
                                     {{--</div>--}}

                                     <div class="col-md-6">
                                         <strong>Company</strong>
                                         <div class="row">
                                             <div class="col-md-12">
                                                 <div class="form-group" style="margin-top:0px;">
                                                     <label for="templateId" class="control-label">Company</label>
                                                     <div style="position:relative">
                                                         <select id="company" class="form-control" name="company">
                                                             <option value="">Select Company</option>
                                                             @if($clients)
                                                                 @foreach($clients as $row)
                                                                     <?php
                                                                     if($row->company_id==Session::get('company_id'))
                                                                         $companyDetails   =     $row->requestedCompanyInfo;
                                                                     else
                                                                         $companyDetails   =     $row->companyInfo;
                                                                     ?>
                                                                     <option value="{{ $companyDetails->id }}"  @if($request->company==$companyDetails->id) selected @endif>{{ $companyDetails->name }} </option>
                                                                 @endforeach
                                                             @endif

                                                         </select>
                                                         <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="col-md-6">
                                         <strong class="pull-left">&nbsp;</strong>
                                         {{--<div class="pull-right">--}}
                                         {{--<strong>Filter for Invoicing</strong>&nbsp;--}}
                                         {{--<input type="checkbox" class="docketPreviewCheckboxInput" value="1" name="invoiceable" >--}}
                                         {{--</div>--}}
                                         <div class="clearfix"></div>

                                         <div class="row">
                                             <div class="col-md-12">
                                                 <div class="form-group" style="margin-top:0px;">
                                                     <label for="templateId" class="control-label">Employee</label>
                                                     <div style="position:relative">
                                                         <select id="empolyees" class="form-control" name="empolyees">
                                                             <option value="">Select Employee</option>
                                                             @if($totalCompany)
                                                                 @foreach($totalCompany as $company)
                                                                     <option value="{{ $company->user_id }}" data-chained="{{ $company->id}}" >{{ $company->userInfo->first_name}} {{ $company->userInfo->last_name}} </option>
                                                                     @if($company->employees->count()>0)
                                                                         @foreach( $company->employees as $employee)
                                                                             <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}"  >{{ $employee->userInfo->first_name}} {{ $employee->userInfo->last_name}} </option>
                                                                         @endforeach
                                                                     @endif
                                                                 @endforeach
                                                             @endif
                                                         </select>
                                                         <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                     <div class="col-md-12">
                                         <br/>
                                         <strong>Invoice Info</strong>
                                         <div class="row">
                                             <div class="col-md-6">
                                                 <div class="form-group" style="margin-top:0px;">
                                                     <label for="docketId" class="control-label">Invoices</label>
                                                     <div style="position:relative">
                                                         <select id="invoiceId" class="form-control" name="invoiceTemplateId">
                                                             <option value="">Select Invoice Template</option>
                                                             @if($invoices)
                                                                 @foreach($invoices as $row)
                                                                     <option value="{{ $row->id }}">{{ $row->title }}</option>
                                                                 @endforeach
                                                             @endif
                                                         </select>
                                                         <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                                     </div>
                                                 </div>
                                             </div>
                                             <div class="col-md-6">
                                                 <div class="form-group" style="margin-top:0px;">
                                                     <label for="templateId" class="control-label">Invoice Id</label>
                                                     <input type="text" class="form-control"  name="invoiceId" value="{{$request->invoiceId}}">
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
                                                                     {{--<option value="2" selected="selected">Inside docket date (User Selected date)</option>--}}
                                                                     <option value="1">Outside invoiced date (invoice creation date)</option>
                                                                 </select>
                                                                 <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                                             </div>
                                                         </div>
                                                     </div>
                                                     <div class="col-md-3">
                                                         <div class="form-group" style="margin-top:0px;">
                                                             <label for="templateId" class="control-label">From</label>
                                                             <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from" value="{{$request->from}}" id="fromDatePicker" >
                                                         </div>
                                                     </div>
                                                     <div class="col-md-3">
                                                         <div class="form-group" style="margin-top:0px;">
                                                             <label for="templateId" class="control-label">To</label>
                                                             <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" value="{{$request->to}}" name="to">
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>

                                 </div>



                                 {{ Form::close() }}
                             </div>
                         </div>
                     </div>
                 </div>
                 <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: 20px;padding-bottom: 5px;">Results</strong>




            <table class="rtDataTable table"  id="datatable">
                <thead>
                <tr>
                    <th>Invoice @if(strlen($request->invoiceId)>0) <i class="fa fa-check" style="color:green"></i>@endif</th>
                    <th>Info</th>
                    <th>Invoice Name @if(strlen($request->invoiceTemplateId)>0) <i class="fa fa-check" style="color:green"></i> @endif </th>
                    <th>Date Added @if(strlen($request->from)>0 || strlen($request->to)>0) <i class="fa fa-check" style="color:green"></i> @endif</th>
                    {{--<th>Amount</th>--}}
                    <th>Status</th>
                    <th width="100px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn=1;?>
                @if(@$sentInvoice)
                    @foreach($sentInvoice as $row)
                    <tr>
                        <td><span class="blackLabel">Inv {{ $row->id }}</span></td>
                        <td>
                            <span class="blackLabel">Sender</span>
                            <span class="userInfo"> {{ $row->senderUserInfo->first_name }} {{ $row->senderUserInfo->last_name }}<br></span>
                            {{ @$row->senderCompanyInfo->name }}
                            <br/><br/>
                            <span class="blackLabel">Receiver</span>
                            <span class="userInfo"> {{ $row->receiverUserInfo->first_name }} {{ $row->receiverUserInfo->last_name }}<br></span>
                            {{ @$row->receiverCompanyInfo->name }}
                        </td>
                        <td>{{ $row->invoiceInfo->title }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                        {{--<td><span class="invoiceAmount">$134.99</span></td>--}}
                        <td>
                            @if($row->company_id==Session::get('company_id'))
                                @if($row->status==0)
                                    <span class="label label-warning">Received</span>
                                @endif
                            @else
                                @if($row->status==0)
                                    <span class="label label-warning">Sent</span>
                                @endif
                            @endif

                            @if($row->status==1)
                                <span class="label label-success">Approved</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ url('dashboard/company/invoiceManager/invoice/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>

                        </td>
                    </tr>
                    <?php $sn++; ?>
                    @endforeach
                @endif
                @if(count(@$sentInvoice)==0)
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
        </div>
    </div>






    <style>
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
        .rtDataTable{
            width: 100%;
        }
        .rtDataTable th{
            padding: 16px;
            color: #000;
            font-weight: 500;
            background: #F7F7F7;
            border-bottom: 1px solid #EEEEEE;
        }
        .rtDataTable td{
            padding: 16px;
            border-bottom: 1px solid #EEEEEE;
            color: #000;
            vertical-align: top;
        }
        .rtDataTable td .btn{
            margin-top: 0px;
        }
        .rtDataTable td .label{
            border-radius: 10px;
            padding: 4px 15px;
            font-size: 10.5px;
        }
        .rtDataTable td span.invoiceAmount{
            font-weight: bold;
            color: #EF9500;
        }
        .rtDataTable td .userInfo{
            color: #15B1B8;
            display: block;
            font-weight: normal;
        }
        .rtDataTable td .blackLabel{
            color: #000;
            font-weight: normal;
            display: block;
            margin-bottom: 5px;
        }
        .rtDataTable tfoot td{
            border-bottom: none;
            color: #787878;
            font-weight: normal;
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
        .rtDataTable .pagination{
            margin: 0px;
            font-weight: normal;
        }
        .rtDataTable .pagination>li:first-child>a, .rtDataTable .pagination>li:first-child>span{
            border-bottom-left-radius: 18px;
            border-top-left-radius: 18px;
            padding-left: 15px;
            padding-right: 15px;
        }
        .rtDataTable .pagination>li:last-child>a, .rtDataTable .pagination>li:last-child>span{
            border-bottom-right-radius: 18px;
            border-top-right-radius: 18px;
            padding-left: 15px;
            padding-right: 15px;
        }
        .rtDataTable .pagination>.active>a, .rtDataTable .pagination>.active>a:focus, .rtDataTable .pagination>.active>a:hover, .rtDataTable .pagination>.active>span, .rtDataTable .pagination>.active>span:focus, .rtDataTable .pagination>.active>span:hover{
            background: #15B1B8;
            color: #fff;
        }
        .rtDataTable .pagination>li>a, .rtDataTable .pagination>li>span{
            color: #787878;
        }
    </style>
@endsection

@section('customScript')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $( function() {
                $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
            } );
            $.fn.dataTable.moment( 'D-MMM-YYYY' );
            $('#datatable').dataTable( {
                "order": [[ 3, "desc" ]]
            } );
        } );
    </script>
    <script type="text/javascript">
        $("#empolyees").chained("#company");
    </script>
@endsection