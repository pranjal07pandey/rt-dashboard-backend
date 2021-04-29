@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>Invoice Manager<small>Add/View Invoice</small></h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success" id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-4">
            <div class="sideMenu">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Invoice Elements
                    </h3>
                </div>
                <div class="menuContent">
                    <div class="elementAddingDiv">
                        <ul>
                            {{--<li>--}}
                                {{--<a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple invoiceComponent" id="shortTextAdd" fieldType="1">--}}
                                    {{--<span><i class="fa fa-plus-square"></i> Short Text </span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            {{--<li>--}}
                                {{--<a href="#" class="btn btn-primary btn-xs themeSecondaryBg withripple invoiceComponent" id="longTextAdd" fieldType="2">--}}
                                    {{--<span><i class="fa fa-plus-square"></i> Long Text </span>--}}
                                {{--</a>--}}
                            {{--</li>--}}
                            <li>
                            <a href="#" class="btn btn-primary btn-xs themeSecondaryBg withripple invoiceComponent" id="headerTextAdd" fieldType="12">
                            <span><i class="fa fa-plus-square"></i> Header </span>
                            </a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary btn-xs themeSecondaryBg withripple invoiceComponent" id="imageTextAdd" fieldType="5">
                                    <span><i class="fa fa-plus-square"></i> Image </span>
                                </a>
                            </li>

                            <!-- only for rt user for testing pourpose -->
                            @if(Session::get('company_id')==1)
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple invoiceComponent" id="signatureAdd" fieldType="9">
                                    <span><i class="fa fa-plus-square"></i> Signature </span>
                                </a>
                            </li>
                            @endif
                            <li class="clearfix"></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <div class="sideMenu">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Invoice Info
                        <a class="pull-right"  data-toggle="modal" data-target="#updateInvoice">
                            <i class="fa fa-plus-square"></i> Update
                        </a>
                    </h3>

                </div>
                <div class="menuContent">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Invoice Name</strong>
                        </div>
                        <div class="col-md-8">
                            {{ $invoice->title }}
                        </div>
                        <div class="col-md-4">
                            {{--<strong>Invoice Title</strong>--}}
                        </div>
                        <div class="col-md-8">
                            {{ $invoice->subTitle }}
                        </div>
                        <hr>
                        <div class="clearfix"></div>
                        <div class="col-md-4">
                            <strong>Tax</strong>
                        </div>
                        <div class="col-md-8">
                            <input type="checkbox" id="gstCheckboxInput" data="{{ $invoice->id }}"  @if($invoice->gst==1) checked @endif>
                        </div>
                        <hr>
                        <div class="col-md-4">
                            <strong>Invoice Prefix</strong>
                        </div>
                        <div class="col-md-8">
                            <span id="docketPrefix">
                                <a href="#"
                                   class="editable"
                                   data-type="text" data-pk="{{ $invoice->id }}"
                                   data-url="{{ url('dashboard/company/invoiceManager/updateInvoicePrefix') }}"
                                   data-title="Enter Label Text">{{ $invoice->prefix }}</a>
                            </span>
                        </div>

                        <hr>
                        <div class="col-md-8">
                            <strong>Hide Invoice Prefix</strong>
                        </div>
                        <div class="col-md-3">
                            <span id="docketPrefix">
                               <input type="checkbox" id="hidePrefix" @if($invoice->hide_prefix == 1 ) checked @endif >
                            </span>
                        </div>

                        <hr>
                        <div class="col-md-4">
                            <strong>Preview</strong>
                        </div>
                        <div class="col-md-8">
                            <input type="checkbox" id="previewCheckboxInput" invoicepreview="{{ $invoice->id }}"  @if($invoice->preview==1) checked @endif>
                        </div>


                    </div>
                </div>
            </div>

            <div class="sideMenu">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Recently Assigned
                        <a class="pull-right invoicefour"  data-toggle="modal" data-target="#myModalAssignInvoice">
                            <i class="fa fa-plus-square"></i> Assign
                        </a>
                    </h3>
                    <ul style="list-style-type: none;margin-top:15px;margin-left:0px;padding-left:15px;">
                        @if(@$invoice)
                            @foreach($invoice->assignedInvoice as $row)
                                <li>{{ @$row->userInfo->first_name." ".@$row->userInfo->last_name }}</li>
                            @endforeach
                        @endif
                        @if(count(@$invoice->assignedInvoice)==0)
                            <strong>Invoice not assigned yet.</strong>
                        @endif
                    </ul>

                </div>
            </div>
            <div class="sideMenu">
                <div class="menuHeader">
                <h3 class="active">
                    <i class="fa fa-th-list"></i> Document Theme
                    <a class="pull-right"  data-toggle="modal" data-target="#documentTheme">
                        <i class="fa fa-plus-square"></i> Update
                    </a>
                </h3>
                <div class="menuContent">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Theme Name</strong>
                        </div>
                        <div class="col-md-8">
                            {{ @$invoice->themeInfo->name }}
                        </div>
                    </div>
                </div>

            </div>
            </div>



            <div id="forth" class="sideMenu mobileTemplatePreview hidden-sm hidden-xs">
                <div class="menuHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Mobile Template Preview
                    </h3>
                </div>
                <div class="menuContent" style="position: relative;min-height: 700px;">
                    <img class="mobileframe"  src="{{asset("phone.png")}}">
                    <div class="mobileContentWrapper">
                        <div class="mobilecontain" >
                            <div style=" position: -webkit-sticky; /* Safari */position: sticky;top: 0;background-color: #012f54; height: 40px;z-index: 1;"  class="mobile-bar">
                                <p style="padding: 11px;font-size: 12px;color: #fff; font-weight: 700; text-align: center;">
                                    <i style="float: left;font-size: 14px;     padding: 3px;" class="fa fa-chevron-left" aria-hidden="true"></i>
                                    {{ $invoice->title }}
                                    {{--<i style="float: right;font-size: 14px;     padding: 3px;" class="fa fa-home" aria-hidden="true"></i>--}}
                                </p>
                            </div>


                            <div class="main-size" id="mobileviewHtml">
                                @include('dashboard.company.invoiceManager.mobileView')
                            </div>

                            <div style=" position: -webkit-sticky; /* Safari */position: absolute;bottom: 0; height: auto; width: 100%; z-index: 1;"  class="mobile-bar">
                                <div style="background-color: #fafafa; min-height:100px;margin: 0px 8px 0px 8px; font-size: 13px; " >
                                    <div style="padding: 6px 8px 0px 8px ">
                                    <div class="row">
                                            <div class="col-md-6" style="font-weight: 400;color: #000;" ><i style="color: #31c4c6;     font-size: 15px;" class="fa fa-plus-circle" aria-hidden="true"></i>  Add New Field</div>
                                            <div class="col-md-6" style="font-weight: 400;color: #000;     text-align: right;" > <i style="color: #d4001c;     font-size: 15px;" class="fa fa-minus-circle" aria-hidden="true"></i>  Remove Field</div>
                                           <div class="col-md-12" style="border: 1px solid #ededed;    width: 95%;margin: 5px 0px 4px 7px;"></div>
                                            <div class="col-md-6" style="font-size: 12px;">Subtotal</div>
                                            <div class="col-md-6" style="font-size: 12px; text-align: right;">$00.00</div>
                                            <div class="col-md-6" style="font-size: 12px;">Tax({{ $invoice->gst_value }}%)</div>
                                            <div class="col-md-6" style="font-size: 12px;text-align: right;">$00.00</div>
                                            <div class="col-md-6"><strong>Total</strong> </div>
                                            <div class="col-md-6" style="text-align: right;"><strong>$00.00</strong></div>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-xs btn-raised btn-info pull-left draftButton">Draft</button>
                                <button class="btn btn-xs btn-raised btn-info pull-right sendButton">Send Now</button>
                                <div class="clearfix"></div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


            @if(\App\InvoiceXeroSetting::where('invoice_id',$invoice->id)->count()>0)
                <div class="sideMenu">
                    <div class="menuHeader">
                        <h3 class="active">
                            <i class="fa fa-th-list"></i> Xero Invoice Detail
                            @if(\App\CompanyXero::where('company_id',Session::get('company_id'))->where('status',1)->count()==1)
                                <a class="pull-right"  data-toggle="modal" data-target="#XeroInvoiceUpdate">
                                    <i class="fa fa-plus-square"></i> Update
                                </a>
                            @endif


                        </h3>


                        <div class="col-md-12">
                           <div style="margin: 11px 0 11px 0;">
                               @if(\App\CompanyXero::where('company_id',Session::get('company_id'))->where('status',1)->count()==0)
                                   <div class="alert alert-danger" style="padding: 10px 10px;font-size: 13px;">
                                       <p>This Template already use by another xero Account.</p>
                                       <a href="{{url("dashboard/company/profile/xeroSetting")}}">Click here!</a>
                                       <div class="clearfix"></div>
                                   </div>
                               @endif

                           @foreach($invoiceXeroSettings->xeroInvoiceValue as $row)
                                {{--@if($row->xero_field_id ==1)--}}
                                    {{--<span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$row->value}} </span><br>--}}
                                {{--@endif--}}
                                @if($row->xero_field_id ==2)
                                    <span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$row->value}} </span><br>
                                @endif
                                @if($row->xero_field_id ==3)
                                    <span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$row->value}} Days </span><br>
                                @endif
                                @if($row->xero_field_id ==4)
                                    <?php
                                        $test=explode( '-', $row->value);

                                            ?>
                                    <span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$test[1]}} </span><br>
                                @endif
                                @if($row->xero_field_id ==5)
                                        <?php
                                        $test=explode( '-', $row->value);

                                        ?>
                                    <span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$test[2]}} </span><br>
                                @endif
                                @if($row->xero_field_id ==6)
                                    <span> <strong>{{$row->xeroFieldInfo->title}}</strong> : {{$row->value}}%</span><br>
                                @endif
                            @endforeach
                                <span> <strong> Sync upon sending:</strong> : @if($invoice->invoiceXeroSetting->first()->xero_syn_invoice == 1)<i style="color: green;" class="fa fa-check" aria-hidden="true"></i> @else <i style="color: red;" class="fa fa-times" aria-hidden="true"></i>
                                    @endif </span><br>

                           </div>
                        </div>
                    </div>
                </div>
                @else
            @endif




        </div>
        <div class="col-md-8">
            <h3 style="font-size: 20px; margin: 10px 0px 10px;font-weight: 500;display:inline-block" class="pull-left">Preview</h3>
            {{--            <a href="{{ url('dashboard/company/docketBookManager/designDocket/'.$tempDocket->id.'/save') }}" class="btn btn-xs btn-raised  btn-success pull-right" id="addNew" style="margin: 0px;margin-left: 10px;"><i class="fa fa-check"></i> Save<div class="ripple-container"></div></a>--}}
            {{--            &nbsp;&nbsp;<a href="{{ url('dashboard/company/docketBookManager/designDocket/'.$tempDocket->id.'/cancel') }}" class="btn btn-xs btn-raised btn-danger pull-right" id="addNew" style="margin: 0px;"><i class="fa fa-times"></i> Cancel<div class="ripple-container"></div></a>--}}
            <div class="pull-right">
                @if(count(@$invoice->assignedInvoice)==0)
                    <a href="#" data-toggle="modal" data-target="#myModalAssignDelete" class="btn btn-xs btn-raised btn-danger eight" data-id=" {{ $invoice->id }}" id="addNew" style="margin: 0px;">
                        <i class="fa fa-trash"></i> Cancel<div class="ripple-container"></div>
                    </a>
                    <a href="#" data-toggle="modal" data-target="#myModalAssignInvoice" class="btn btn-xs btn-raised btn-success"  style="margin: 0px;">
                        <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                    </a>&nbsp;
                @else
                <a href="{{ url('dashboard/company/invoiceManager') }}" data-toggle="modal" class="btn btn-xs btn-raised btn-success" id="addNew" style="margin: 0px;">
                    <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                </a>&nbsp;
                @endif
            </div>


            <div class="clearfix"></div>
            <div class="invoiceDescription" style="padding:10px 10px;margin-bottom: 20px;background: #f9f9f9;    border: 1px dashed #eaeaea;">
                <div class="row">
                    <div class="col-md-6">
                        <img src="">
                            <img src="https://dummyimage.com/200x100/f0f0f0/ffffff.jpg&text=your+logo">
                            <h4>{{ $companyDetails->name }}</h4>
                            <span>{{ $companyDetails->address }}</span><br/>
                            <strong>From</strong>: <span style="text-decoration: dotted">Sender Name</span>
                    </div>
                    <div class="col-md-6 text-right">
                        <strong>Date</strong>: Current date<br/>
                        <strong>Invoice</strong>: Invoice Id<br/>
                    </div>
                    <div class="col-md-12"><br/><br/>
                        <strong>To</strong><br/>
                        <h4 class="dotted" style="display: inline-block">Receiver Name</h4><br/>
                        <span class="dotted">Receiver Address</span><br/><br/><br/>
                    </div>
                    {{--<div class="col-md-12 text-center">--}}
                        {{--<strong>For : </strong>--}}
                        {{--<input type="text">--}}
                    {{--</div>--}}

                    <div class="col-xs-12 table-responsive" style="overflow: hidden">
                        <table class="table table-striped   ">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th width="45%">Value/Amount</th>
                            </tr>
                            </thead>
                            <tbody id="sortable">
                                @if($invoiceFields)
                                    @foreach($invoiceFields as $item)
                                        @include('dashboard.company.invoiceManager.elementTemplate')
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot style="    background-color: #f9f9f9;">
                                <tr>
                                    <th>Subtotal</th>
                                    <th>$ ...................</th>
                                </tr>
                                @if(\App\InvoiceXeroSetting::where('invoice_id',$invoice->id)->count()==1)
                                    <tr  class="gstTableList " @if($invoice->gst==0) style="display:none" @endif >
                                        <th colspan="2" style="padding: 0px;">
                                            <div class="invoicethird">
                                                <div style="width: 45%; float: left;">
                                                    <span style="position: relative;color: #9f9f9f;">{{ $invoice->gst_label }}</span>

                                                </div>
                                                <div style="width: 53%; float: right;">
                                                    <span style="position: relative;color: #9f9f9f;">{{ $invoice->gst_value }} </span>%
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </th>

                                    </tr>
                                    @else
                                    <tr  class="gstTableList " @if($invoice->gst==0) style="display:none" @endif >
                                        <th colspan="2" style="padding: 0px;">
                                            <div class="invoicethird">
                                                <div style="width: 45%; float: left;">
                                                    <a style="position: relative;"  href="#" id="longText" class="editable" data-type="text"  data-pk="{{ $invoice->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceGSTLabelUpdate/1') }}" data-title="Enter Label Text">{{ $invoice->gst_label }}</a>

                                                </div>
                                                <div style="width: 53%; float: right;">
                                                    <a style="position: relative;"  href="#" id="longText" class="editable" data-type="text" data-pk="{{ $invoice->id }}" data-url="{{ url('dashboard/company/invoiceManager/designInvoice/invoiceGSTLabelUpdate/2') }}" data-title="Enter Value">{{ $invoice->gst_value }} </a>%
                                                </div>
                                                <div class="clearfix"></div>
                                            </div>
                                        </th>

                                    </tr>
                                    @endif



                                <tr>
                                    <th>Total</th>
                                    <th>$ ...................</th>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <table>
                                            <tbody id="sortableSignature">
                                            @if($invoiceFields)
                                                @foreach($invoiceFields as $item)
                                                    @include('dashboard.company.invoiceManager.signatureElementTemplate')
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </th>
                                </tr>
                                <tr>
                                    <th colspan="2">
                                        <table>
                                            <tbody id="sortableImage">
                                            @if($invoiceFields)
                                                @foreach($invoiceFields as $item)
                                                    @include('dashboard.company.invoiceManager.headerImageElementTemplate')
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row" id="sortable">

                {{--@if($tempDocketFields)--}}
                    {{--@foreach($tempDocketFields as $item)--}}
                        {{--@include('dashboard.company.docketManager.elementTemplate')--}}
                    {{--@endforeach--}}
                {{--@endif--}}
            </div>
        </div>
    </div>
    <br/><br/><br/>
    <div class="modal fade " id="myModalAssignInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Do you want to assign this Invoice Template?</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/assignInvoice/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                <label for="templateId" class="control-label">Invoice Template</label>
                                <input type="hidden" name="templateId" value="{{ $invoice->id }}">
                                <input type="text" class="form-control" readonly value="{!! $invoice->title !!}">
                            </div>
                            {{--<div class="form-group" style="margin-top:0px;">--}}
                                {{--<label for="employeeId" class="control-label">Employee</label>--}}
                                {{--<select id="employeeId" class="form-control" required name="employeeId">--}}
                                    {{--<option value="">Select Employee</option>--}}
                                    {{--@if($employees)--}}
                                        {{--@foreach($employees as $row)--}}
                                            {{--<option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>--}}
                                        {{--@endforeach--}}
                                    {{--@endif--}}
                                {{--</select>--}}
                            {{--</div>--}}

                            <div class="form-group label-floating">
                                <div class="col-md-9 designDocket">
                                    <div class="form-group" style="padding-bottom: 20px;margin: 27px 0px 0px -12px;">
                                        <label for="employeeId" class="control-label">Employee</label>
                                        <select id="framework" class="form-control designDocket" multiple required name="employeeId[]">
                                            {{--<option value="">Select Employee</option>--}}
                                            @if($employees)
                                                @foreach($employees as $row)
                                                    <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <i style="font-size:12px;color:#999;">Note: Command click on mac or control click on PC to select multiple users</i>

                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="padding-bottom: 20px;margin: 24px 0 0 0;    float: left;">
                                    <button style="" type="submit" class="btn btn-primary">Save</button>

                                </div>
                                <div style="padding-bottom: 20px;margin: 24px 0 0 0;    float: right;">
                                <a style=" text-transform: capitalize;" class="btn btn-primary" href="{{ route('companyInvoiceManager') }}">Assign Later</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{--<div class="modal-footer">--}}
                    {{--<button type="submit" class="btn btn-primary">Assign</button>--}}
                {{--</div>--}}
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
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Invoice Field</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            {{--<input type="hidden" class="form-control" id="invoice_field_id">--}}
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this  Invoice Field?</p>
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


    <div class="modal fade " id="updateInvoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Invoice Info</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/updateInvoiceDocket', 'files' => true]) }}
                <div class="modal-body">
                    <input type="hidden" name="invoiceId" value="{{ $invoice->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Invoice Name</label>
                                <input type="text" name="invoiceName" class="form-control" required="required" value="{!! $invoice->title !!}">
                            </div>
                        </div>
                        {{--<div class="col-md-12">--}}
                        {{--<div class="form-group label-floating">--}}
                        {{--<label class="control-label" for="title">Docket Title</label>--}}
                        {{--<input type="hidden" name="docketTitle" class="form-control"  value="subtitle">--}}
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

    <!-- Modal -->
    {{--<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">--}}
        {{--<div class="modal-dialog modal-md" role="document">--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header themeSecondaryBg">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                    {{--<h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Docket Info</h4>--}}
                {{--</div>--}}

                {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/updateTempDocket', 'files' => true]) }}--}}
                {{--<div class="modal-body">--}}
                    {{--<input type="hidden" name="docketId" value="{{ $invoice->id }}">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-12">--}}
                            {{--<div class="form-group label-floating">--}}
                                {{--<label class="control-label" for="title">Invoice Name</label>--}}
                                {{--<input type="text" name="docketName" class="form-control" required="required" value="{!! $invoice->title !!}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-12">--}}
                            {{--<div class="form-group label-floating">--}}
                                {{--<label class="control-label" for="title">Invoice Title</label>--}}
                                {{--<input type="text" name="docketTitle" class="form-control" required="required" value="{!! $invoice->subTitle !!}">--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div class="modal-footer">--}}
                    {{--<button type="submit" class="btn btn-primary">Update</button>--}}
                {{--</div>--}}
                {{--{{ Form::close() }}--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
    <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModal" aria-hidden="true">
        <div class="modal-dialog nine" role="document">
            <div style="    margin-top: 135px;" class="modal-content">
                <div class="modal-body">
                    <ol>
                        <li>You can delete a field by simply clicking the delete symbol. Please note: once a template has been used, any of its field cannot be deleted.</li>
                        <li>Go to Invoice Manager >> Invoice Template to view all existing invoice templates. You can preview, change the fields order, change label names and so on. You can also delete invoice templates from here as long as it has not been used to send a docket.</li>
                        <li>Go to Invoice Manager >> Assign Invoice Template to assign dockets to employees and un-assign them from a docket/s. </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalAssignDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/invoiceManager/designInvoice' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Cancel Invoice Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="design_docket" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to cancel this template?</p>
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

    <div class="modal fade " id="documentTheme" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Document Theme</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/updateTempThemeInvoice', 'files' => true]) }}
                <div class="modal-body">
                    <input type="hidden" name="invoiceId" value="{{ $invoice->id }}">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Document Name</label>
                                <select name="theme_document_id" class="form-control"required="required" >
                                    @if($themes)
                                        @foreach ($themes as $row )
                                            @if($row->type==1)
                                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                            @endif

                                        @endforeach
                                    @endif
                                </select>
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

    @if(\App\InvoiceXeroSetting::where('invoice_id',$invoice->id)->count()>0)
    <div class="modal fade " id="XeroInvoiceUpdate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Xero Detail</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/invoiceManager/XeroInvoiceUpdate', 'files' => true]) }}
                <input type="hidden" name="invoice_id" value="{{$invoice->id}}">
                <input type="hidden" name="syn_xero" value="1">


                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5 style="margin-top: -9px;"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp; Are you syncing to Xero?</h5>
                            <div>
                                <ul style="list-style: none !important;    padding: 8px 0px 0px 19px;">
                                    <li style="float: left;    margin-right: 28px;">
                                        <p>
                                            <input style="position: absolute;left: -9999px;" type="checkbox" name="syn_xero" disabled  value="1"  @if($invoice->invoiceXeroSetting->first()->syn_xero==1)checked @endif  id="labelCheckyes" >
                                            <label for="labelCheckyes">
                                                Yes
                                            </label>
                                        </p>
                                    </li>
                                </ul>
                            </div>

                            <div class="xeroWant" style="margin-left: 31px;">
                                @foreach($invoiceXeroSettings->xeroInvoiceValue as $item)
                                    {{--@if($item->xero_field_id ==1)--}}
                                        {{--<div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">--}}
                                            {{--<h5 style="margin-top: 20px;">{{$item->xeroFieldInfo->title}}</h5>--}}
                                            {{--<select name="1" class="form-control">--}}
                                                {{--<option @if($item->value=="ACCPAY") selected @endif value="ACCPAY">A bill - commonly known as a Accounts Payable or supplier invoice</option>--}}
                                                {{--<option @if($item->value=="ACCREC") selected @endif value="ACCREC">A sales invoice - commonly known as an Accounts Receivable or customer invoice</option>--}}
                                            {{--</select>--}}
                                        {{--</div>--}}
                                    {{--@endif--}}
                                    @if($item->xero_field_id ==2)
                                        <div class="form-group col-md-12" style="margin: -14px 0px 16px -27px;">
                                            <h5 style="margin-top: 20px;">{{$item->xeroFieldInfo->title}}</h5>
                                            <select id="line" name="2" class="form-control">
                                                <option @if($item->value=="Exclusive") selected @endif value="Exclusive">Exclusive</option>
                                                <option @if($item->value=="Inclusive") selected @endif value="Inclusive">Inclusive</option>
                                                <option @if($item->value=="NoTax") selected @endif value="NoTax" >NoTax</option>
                                            </select>
                                        </div>
                                    @endif
                                    @if($item->xero_field_id ==3)
                                        <div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">
                                            <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">{{$item->xeroFieldInfo->title}}</label>
                                            <div class="gorm-group is-empty">
                                                <input type="number" name="3" class="form-control" palceholder="Enter number of days " min="1" value="{{$item->value}}">
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->xero_field_id ==4)
                                        <?php
                                        $test = $account;
                                            $test1=explode( '-', $item->value);
                                        ?>
                                        <div class="form-group col-md-6" style="margin: -23px 0px 16px -27px;">
                                            <h5 style="margin-top: 20px;">{{$item->xeroFieldInfo->title}}</h5>
                                            <select name="4" class="form-control">
                                                @php $tempType = ""; @endphp
                                                @foreach($test as $accounts)
                                                    @if($test[0]->code==$accounts->code)
                                                        <optgroup label="{{$accounts->type}}">
                                                            @elseif($tempType!=$accounts->type)
                                                        </optgroup>
                                                        <optgroup label="{{$accounts->type}}">
                                                    @endif
                                                        <option @if($accounts->Code == $test1[0]) selected @endif  value="{{$accounts->Code}}-{{$accounts->Name}}">{{$accounts->Name}}</option>
                                                    @php $tempType = $accounts->type; @endphp
                                                @endforeach
                                          </optgroup>
                                            </select>
                                        </div>
                                    @endif
                                    @if($item->xero_field_id ==5)
                                            <input type="hidden" value="NONE-0-Tax Exempt" id="hiddenTaxrate" disabled name="5">

                                            <div class="form-group col-md-6" style="margin: -23px 14px 20px -27px">
                                            <h5 style="margin-top: 20px;">{{$item->xeroFieldInfo->title}}</h5>
                                            <select id="tax"  name="5" class="form-control">
                                                <?php
                                                $taxRate = $taxRates;
                                                $test=explode( '-', $item->value);
                                                ?>
                                                @foreach($taxRate as $row)
                                                    <option @if($row->TaxType == $test[0]) selected @endif data-chained="NoTax" value="{{$row->TaxType}}-{{$row->DisplayTaxRate}}-{{$row->Name}}" data-tag='{{$row->TaxType}}-{{$row->DisplayTaxRate}}-{{$row->Name}}'>{{$row->Name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    @endif
                                    @if($item->xero_field_id ==6)
                                        <div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">
                                            <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">{{$item->xeroFieldInfo->title}}</label>
                                            <div class="gorm-group is-empty">
                                                <input type="text" name="6" class="form-control" value="{{$item->value}}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                    <div class="col-md-12" style="    margin-left: -30px;">
                                        <h5 style="margin-top: -9px;"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp; Are you syncing upon sending Invoice </h5>

                                        <ul style="list-style: none !important;    padding: 8px 0px 0px 19px;">
                                            <li style="float: left;    margin-right: 28px;">
                                                <p>
                                                    <input style="position: absolute;left: -9999px;" type="checkbox" name="xero_syn_invoice"   value="1"  @if($invoice->invoiceXeroSetting->first()->xero_syn_invoice==1) checked @endif id="sendingyes"  >
                                                    <label for="sendingyes">
                                                        Yes
                                                    </label>
                                                </p>
                                            </li>
                                            <li style="  float: left;">
                                                <p>
                                                    <input style="position: absolute;left: -9999px;" type="checkbox" name="xero_syn_invoice" @if($invoice->invoiceXeroSetting->first()->xero_syn_invoice==0)checked @endif value="0" id="sendingno" >
                                                    <label for="sendingno">
                                                        No
                                                    </label>
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
        @else
    @endif
@endsection

@section('customScript')
    <style>
        [type="checkbox"]:not(:checked) + label,
        [type="checkbox"]:checked + label {
            position: relative;
            padding-left: 1.95em;
            cursor: pointer;
        }

        /* checkbox aspect */
        [type="checkbox"]:not(:checked) + label:before,
        [type="checkbox"]:checked + label:before {
            content: '';
            position: absolute;
            left: 0; top: 0;
            width: 1.25em; height: 1.25em;
            border: 2px solid #ccc;
            background: #fff;
            border-radius: 4px;
            box-shadow: inset 0 1px 3px rgba(0,0,0,.1);
        }
        /* checked mark aspect */
        [type="checkbox"]:not(:checked) + label:after,
        [type="checkbox"]:checked + label:after {
            content: '\2713\0020';
            position: absolute;
            top: .15em; left: .22em;
            font-size: 1.3em;
            line-height: 0.8;
            color: #09ad7e;
            transition: all .2s;
            font-family: 'Lucida Sans Unicode', 'Arial Unicode MS', Arial;
        }
        /* checked mark aspect changes */
        [type="checkbox"]:not(:checked) + label:after {
            opacity: 0;
            transform: scale(0);
        }
        [type="checkbox"]:checked + label:after {
            opacity: 1;
            transform: scale(1);
        }
        /* disabled checkbox */
        [type="checkbox"]:disabled:not(:checked) + label:before,
        [type="checkbox"]:disabled:checked + label:before {
            box-shadow: none;
            border-color: #bbb;
            background-color: #ddd;
        }
        [type="checkbox"]:disabled:checked + label:after {
            color: #999;
        }
        [type="checkbox"]:disabled + label {
            color: #aaa;
        }
        /* accessibility */
        [type="checkbox"]:checked:focus + label:before,
        [type="checkbox"]:not(:checked):focus + label:before {
            border: none;
        }

        /* hover style just for information */
        label:hover:before {
            border: 2px solid #4778d9!important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    {{--<script src="{{asset('assets/dashboard/tour/jquery.min.js')}}"></script>--}}
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/css/bars.css') }}">
    <script src="{{ asset('assets/dashboard/js/bars.js') }}"></script>

    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <!-- <script  src="{{asset('assets/zepto-selector.chained.js')}}"></script> -->
    <script  src="{{asset('assets/zepto.js')}}"></script>

    <!-- FLOT CHARTS -->
    <script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.min.js') }}"></script>
    <!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
    <script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.resize.min.js') }}"></script>
    <!-- FLOT PIE PLUGIN - also used to draw donut charts -->
    <script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.pie.min.js') }}"></script>
    <!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->
    <script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.categories.min.js') }}"></script>
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
    <script type="text/javascript">
        $("#5").chained("#2");
    </script>
    <script>
        $(document).on('click','#labelCheckyes',function () {
            var checkLabel1 = document.getElementById('labelCheckno');
            checkLabel1.checked=false;
            $(".xeroWant").css("display", "block");

        });
        $(document).on('click','#labelCheckno',function () {
            var checkLabel2 = document.getElementById('labelCheckyes');
            checkLabel2.checked=false;
            $(".xeroWant").css("display", "none");
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.editable').editable({
                    success: function (response) {
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                            }
                        });
                    },
                    'placement': 'right',
                    validate: function(value) {
                        if($.trim(value) == '') {
                            return 'The value field is required';
                        }
                    }
                }

                    {{--type: 'text',--}}
                    {{--title: 'Enter username',--}}
                    {{--url: '{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}',--}}
                    {{--success: function(response) {--}}
                    {{--console.log(response);--}}
                    {{--}--}}
            );
        });

         $(function() {
             var tourdragable = false;
             // define tour
             var tour = new Tour({
                 debug: true,
                 // template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div><div class='popover-navigation'><button class='btn btn-default' data-role='prev'>« Prev</button><span data-role='separator'></span><button class='btn btn-default' data-role='next'>Next »</button></div><button class='btn btn-default' data-role='end'>End tour</button></div>",
                 // basePath: location.pathname.slice(0, location.pathname.lastIndexOf('/')),
                 steps: [
                     {
                         element: ".invoicethird",
                         title: "<span>3/4</span>",
                         placement: "bottom",
                         backdrop: true,
                         content: "● Change the tax value if your company is registered for GST (in Australia it’s 10%)<br> ● If the company is not registered for GST, please uncheck the Tax checkbox.",
                         backdropPadding: 5,
                         template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn"  data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>'
                     },
                     {
                         element: ".invoicefour",
                         title: "<span>4/4</span>",
                         placement: "bottom",
                         backdrop: true,
                         content: "● You can also add <b> “Long Text” OR “Short Text”</b> field listed under <b> Invoice Elements</b><br>● Once a template is created, you can assign it by clicking “+Assign” button on the left side of the page. <b>Note:</b> If a docket is not assigned, your users/employees will not be able to access it on their mobile application.",
                         backdropPadding: 5,
                         template: '<div class="popover fade top in" role="tooltip"> <div class="arrow"></div> <h3  class="popover-title"></h3> <div style="color: #000000" class="popover-content"></div> <div class="popover-navigation"> <div class="bootstro-nav-wrapper"><button class="btn btn-info btn-xs bootstro-finish-btn pull-left" data-role="end">Skip</button><button class="btn btn-info btn-xs bootstro-prev-btn" data-toggle="modal" data-target="#noteModal"  data-role="next">Next →</button> <button style="margin-left: 41px;margin-bottom: 9px;" class="btn btn-info btn-xs bootstro-next-btn" data-role="prev">← Back</button>  <button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> </div>  </div> </div>'
                     },{
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
                     if ($(this)[0]["prevEditable"]) {
//                        $('.firstEditElement').editable();
//                        $('.firstEditElement').editable('toggle')
                     }
                     else {
                         $($(this)[0]["modalId"]).modal('hide');

                     }
                     if ($(this)[0]["hideElement"]) {
                         $('.docketField').css('z-index', '');
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

                 onNext: function (tour) {
                     if ($(this)[0]["tourModel"]) {
                         $('#myModalAssign').editable('show');
                     }
                     else {
                         $($(this)[0]["modalId"]).modal('hide');

                     }
                     if ($(this)[0]["hideElement"]) {
                         $('.docketField').css('z-index', '');
                     }
                 }
             });

             // init tour
             @if(Session::get('helpFlaginvoice')=="true")
               tour.restart();
             @endif

             // start tour
             $('#start-tour').click(function () {
                 tour.restart();
                 $('.docketField').css('z-index', '');
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



        $(document).ready(function (){
            $("#gstCheckboxInput").on("click",function(){
                var invoiceId   =   $(this).attr("data");
                var checked     =    0;
                if($("#gstCheckboxInput").is(':checked')){ checked = 1; $(".gstTableList").fadeIn();}else{ $(".gstTableList").fadeOut();checked = 0;}

                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/invoiceManager/designInvoice/gst/') }}',
                    data: {"gst": checked, "invoiceId": invoiceId},
                    success: function( msg ) {
                        if(msg!="true"){
                            alert(msg);
                        }
                    }
                });
            });

            $(document).on('click', '.invoiceComponent', function(){
                var filedType   =    $(this).attr('fieldtype');
                $.ajax({
                    type: "POST",
                    data: {fieldType:$(this).attr('fieldtype')},
                    url: "{{ url('dashboard/company/invoiceManager/designInvoice/addInvoiceField/'.$invoice->id) }}",
                    success: function(response){
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                            }
                        });

                        if(filedType==9){
                            $.when($('#sortableSignature').append(response)).done(function() {
                                $('.editable').editable({placement:'right'});
                            });
                        }else if(filedType==5 || filedType==12){
                            $.when($('#sortableImage').append(response)).done(function() {
                                $('.editable').editable({placement:'right'});
                            });
                        }
                        else{
                            $.when($('#sortable').append(response)).done(function() {
                                $('.editable').editable({placement:'right'});
                            });
                        }
                    }
                });
            });

            $( "#sortable" ).sortable({
                stop: function(e, ui) {

                    var param = [];
                    $.map($("#sortable >tr"), function(el) {
                        param[$(el).index()] = $(el).attr('fieldId');
                    });
                    console.log(param);
                    $.ajax({

                        type: "POST",
                        data: {param:param},
                        url: "{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldUpdatePosition/'.$invoice->id) }}",
                        success: function(msg){
                            console.log(msg);

                            $.ajax({
                                type: "GET",
                                url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                                success:function (response) {
                                    $("#mobileviewHtml").html(response);
                                }
                            });
                        }
                    });
                }
            });
            $( "#sortableImage" ).sortable({
                stop: function(e, ui) {

                    var param = [];
                    $.map($("#sortableImage >tr"), function(el) {
                        param[$(el).index()] = $(el).attr('fieldId');
                    });
                    console.log(param);
                    $.ajax({
                        type: "POST",
                        data: {param:param},
                        url: "{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldUpdatePosition/'.$invoice->id) }}",
                        success: function(msg){
                            console.log(msg);
                            $.ajax({
                                type: "GET",
                                url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                                success:function (response) {
                                    $("#mobileviewHtml").html(response);
                                }
                            });
                        }
                    });
                }
            });
            $( "#sortableSignature" ).sortable({
                stop: function(e, ui) {

                    var param = [];
                    $.map($("#sortableSignature >tr"), function(el) {
                        param[$(el).index()] = $(el).attr('fieldId');
                    });
                    console.log(param);
                    $.ajax({

                        type: "POST",
                        data: {param:param},
                        url: "{{ url('dashboard/company/invoiceManager/designInvoice/invoiceFieldUpdatePosition/'.$invoice->id) }}",
                        success: function(msg){
                            console.log(msg);
                            $.ajax({
                                type: "GET",
                                url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                                success:function (response) {
                                    $("#mobileviewHtml").html(response);
                                }
                            });
                        }
                    });
                }
            });


            $(document).on('click', '.deleteInvoiceComponent', function(){
                var parentDiv   =   $("#activeTr");
                    $.ajax({
                        type: "POST",
                        data: {fieldId:$(this).attr('fieldId')},
                        url: "{{ url('dashboard/company/invoiceManager/designInvoice/deleteInvoiceField/'.$invoice->id) }}",
                        success: function(response){
                            $.ajax({
                                type: "GET",
                                url:"{{url('dashboard/company/invoiceManager/mobileView/'.$invoice->id) }}",
                                success:function (response) {
                                    $("#mobileviewHtml").html(response);
                                }
                            });

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

            $(document).on('click', '.deleteInvoiceComponentField', function() {
                $('#deleteInvoiceField').modal('show');

                var parentDiv   =   $(this).parents('.docketField');
                parentDiv.attr('id',"activeTr");

                id = $(this).attr('fieldId');
                $('#invoice_field_id').attr("fieldId",id);
            });

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

    <script>
        $(document).on('click','#sendingyes',function () {
            var sendingno = document.getElementById('sendingno');
            sendingno.checked=false;
        });
        $(document).on('click','#sendingno',function () {
            var sendingyes = document.getElementById('sendingyes');
            sendingyes.checked=false;
        });
    </script>

    <script>
            $(document).on('change', '#line', function () {
                var labelTypeValue = $(this).val();
                if (labelTypeValue == "NoTax") {
                    $("#tax option").each(function (item) {
                        var element = $(this);
                        if (element.data("tag") == "NONE-0-Tax Exempt") {
                            $("#tax > [value = 'NONE-0-Tax Exempt']").prop('selected', 'selected');
                            $("#tax").prop('disabled', 'disabled');
                            $("#hiddenTaxrate").prop('disabled', false);
                        }
                    });
                } else {
                    $("#tax").prop('disabled', false);
                    $("#hiddenTaxrate").prop('disabled', 'disabled');
                }
            });
    </script>
    <script>
        $(document).ready(function () {
            var data = $('#tax').val();
            if(data=="NONE-0-Tax Exempt"){
                $("#tax").prop('disabled', 'disabled');
                $("#hiddenTaxrate").prop('disabled', false);
            }
        })
    </script>

    <script>
        $(document).on('click', '#previewCheckboxInput', function(){
            var invoiceId   =   $(this).attr("invoicepreview");
            var checked = 0;
            if ($("#previewCheckboxInput").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                data: {invoiceId:invoiceId, isPreview:checked },
                url: "{{ url('dashboard/company/invoiceManager/designInvoice/previewDescription/')}}",
                success: function(response){
                    console(response);
                }
            });
        });


        $(document).on('click', '#hidePrefix', function(){
            var checked = 0;
            if ($("#hidePrefix").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                data: {invoice_id:'{{$invoice->id}}', isShow:checked },
                url: "{{ url('dashboard/company/invoiceManager/designInvoice/showHideInvoicePrefix/')}}",
                success: function(response){
                    console(response);
                }
            });
        });
    </script>



    <style>

        .mobile-bar .draftButton{
            margin-left: 8px;
            padding: 7px 48px;
            font-size: 12px;
            background-color: #4fa4da;
        }
        .mobile-bar .sendButton{
            margin-right: 8px;
            padding: 7px 35px;
            font-size: 12px;
            background-color: #4fa4da;
        }
        div.collapse {
            overflow:visible;
            /*or*/
            position:static;
        }
        .invoicethird{
            display: block;
            background: #fff;
            padding: 20px 10px;
        }
        .editable-click:after{
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -12px;
            font-weight: normal;
            font-size: 10px;
            color: red;
            padding: 0px 5px;
            width: 48px;
            border-radius: 5px;
        }
        .designDocket .btn-group .multiselect{
            border-left: none;
            border-right: none;
            border-top: none;
        }

        .mobileframe{
            overflow-x: scroll;
            position: absolute;
            width: calc( 100% - 20px);;
        }
        .mobilecontain{
            /*overflow-x: scroll;*/
            height: 492px;;
            /*position: relative;*/
        }

        #mobileviewHtml{
            height: 297px;
            position: relative;
            overflow-x: scroll;
        }
        .mobileContentWrapper{
            min-height: 490px;background: #fff;position: absolute;left: 39px;top: 110px;width: calc( 100% - 74px);
        }
        @media (max-width: 1200px) {
            .mobileContentWrapper{
                min-height: 392px;
                background: #fff;
                position: absolute;
                left: 33px;
                top: 90px;
                width: calc( 100% - 62px);
            }

            .mobilecontain{
                /*overflow-x: scroll;*/
                height: 397px;
                /*position: relative;*/
            }

            .mobile-bar .draftButton{
                margin-left: 8px;
                padding: 7px 35px;
                font-size: 12px;
                background-color: #4fa4da;
            }
            .mobile-bar .sendButton{
                margin-right: 8px;
                padding: 7px 23px;
                font-size: 12px;
                background-color: #4fa4da;
            }
            #mobileviewHtml {
                height: 188px;
                position: relative;
                overflow-x: scroll;
            }



        }

        .docket-image p {
            /* float: right; */
            font-size: 12px;
            height: 14px;
            margin-top: 5px;
        }
        table.docket-image {
            border-radius: 2px;
            border: none;
            background: #4fa4da;
            box-shadow: 0px 1px 3px 0px #888888;
            text-align: center;
        }



    </style>
@endsection

@section('customScript')
@endsection
