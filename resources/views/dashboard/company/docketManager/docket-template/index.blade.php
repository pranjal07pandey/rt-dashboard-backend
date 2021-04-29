@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('docketBookManager') }}">Docket Book Manager</a></li>
            <li class="active">Docket Templates</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')

    <div class="rtTab" style="background: #fff;margin: 0px;min-height: 400px;">
        <div class="rtTabHeader">
            <ul class="pull-left">
                <li class="active"><a href="{{ route('dockets.template.index') }}">Dockets Templates</a></li>
                <li><a href="{{ route('companyDocketTemplatesArchive') }}" >Archived Dockets Templates</a></li>
            </ul>
            <ul class="pull-right" style="margin-top: 7px;margin-right: 15px;">
                <li>
                    <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info popupsecond" data-toggle="modal" data-target="#tempOption">
                        <i class="fa fa-plus-square"></i> Add New
                    </button>
                </li>
                <li>
                    <button type="button" data-toggle="modal" data-target="#importExport" class="btn btn-xs btn-raised btn-block btn-warning"  >
                        <i class="fa fa-plus-square"></i> Import/Export
                    </button>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
        <div class="rtTabContent" style="padding: 15px;">
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Sn.</th>
                    <th>Docket Name</th>
                    <th width="200">Assigned Folder</th>
                    <th width="200px">Created By</th>
                    <th width="100px">Assigned</th>
                    <th width="130px">Date Added</th>
                    <th width="280">Action</th>
                </tr>
                </thead>
                <tbody>
                @if(@$dockets)
                    <?php $sn = count($dockets); ?>
                    @foreach($dockets as $row)
                        <tr>
                            <td>{{ $sn }}</td>
                            <td>{{ $row->title }}</td>
                            <td>
                                <div class="assignedFolderLink{{ $row->id }}">
                                    @if($row->docketFolderAssign)
                                        @foreach($data as $items)
                                            @if($items['id'] == $row->docketFolderAssign->folder_id)
                                                <i style="color: #EFCE4A;" class="fa fa-folder" aria-hidden="true"></i> {{ $items['value']}}
                                            @endif
                                        @endforeach
                                    @else
                                        <i style="font-size: 12px;">Not assigned yet.</i>
                                    @endif
                                </div>
                            </td>
                            <td>{{ @$row->userInfo->first_name }} {{ @$row->userInfo->last_name }}</td>
                            <td>
                                @if($row->assignedDockets->count()==0) <i class="fa fa-times" style="color:red;" aria-hidden="true"></i>
                                @else <i class="fa fa-check"  style="color:green;"  aria-hidden="true"></i> @endif
                            </td>
                            <td>{{ $row->formattedCreatedDate() }}</td>
                            <td>
                                <a href="{{ url('dashboard/company/docketBookManager/designDocket/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                @if($row->docketFolderAssign)
                                    <a  data-toggle="modal" data-target="#unassignFolderModal" data-id="{{$row->id}}" data-name="{{$row->title}}" data-folder="{{$row->docketFolderAssign->folder_id}}"  data-folderid="{{$row->docketFolderAssign->id}}" style="background-color: #ff5722;" class="btn btn-raised btn-info btn-xs buttonChanger{{$row->id}}" >
                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                        UnAssign Folder
                                    </a>
                                @else
                                    <a  data-toggle="modal" data-target="#assignFolderModal" data-id="{{$row->id}}" data-name="{{$row->title}}" style="background-color: #ff9b00;" class="btn btn-raised btn-info btn-xs buttonChanger{{$row->id}}" >
                                        <i class="fa fa-folder-o" aria-hidden="true"></i>
                                        Assign Folder
                                    </a>
                                @endif
                                <div class="dropdown" style="display: inline-block">
                                    <button class="btn btn-raised btn-info btn-xs dropdown-toggle"  type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" style="background-color: #03a9f4;">
                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                        &nbsp;<span class="caret" style="display: inline-block" ></span>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                        <li><a  data-toggle="modal" data-target="#docketDuplicate" data-id="{{$row->id}}"   >
                                                <i class="fa fa-files-o" aria-hidden="true"></i> Clone
                                            </a>
                                        </li>
                                        <li>
                                            <a title="Archive"  data-toggle="modal" data-target="#archiveDocket" data-id="{{$row->id}}"  >
                                                <i class="fa fa-trash" aria-hidden="true"></i> Delete
                                            </a>
                                        </li>

                                        <li class="publishDocket{{$row->id}}">
                                            @if ($row->templateBank)
                                                <?php
                                                $docketUpdateTime = \Carbon\Carbon::parse($row->updated_at)->format('d-M-Y H:i');
                                                $templateBankUpdateTime = \Carbon\Carbon::parse($row->templateBank->updated_at)->format('d-M-Y H:i');
                                                if ($row->templateBank->tag){
                                                    $tag = implode(',',json_decode($row->templateBank->tag,true));
                                                }else{
                                                    $tag = "";
                                                }


                                                ?>

                                                @if ($docketUpdateTime == $templateBankUpdateTime)
                                                    <a title="Archive"  class="unpublishDocket" data-toggle="modal" data-target="#unpublishDocket" data-id="{{$row->id}}"  >
                                                        <i class="fa fa-share" aria-hidden="true"></i> Unpublish
                                                    </a>
                                                @else
                                                    <a title="Archive"  class="publishDocket" data-toggle="modal" data-target="#republishDocket" data-type="2" data-id="{{$row->id}}" data-templatebank="{{$row->templateBank->id}}" data-tag="{{$tag}}" >
                                                        <i class="fa fa-share" aria-hidden="true"></i> Republish
                                                    </a>
                                                @endif
                                            @else
                                                <a title="Archive"  class="publishDocket" data-toggle="modal" data-target="#publishDocket" data-type="1" data-id="{{$row->id}}"  >
                                                    <i class="fa fa-share" aria-hidden="true"></i> Publish
                                                </a>
                                            @endif



                                        </li>
                                    </ul>
                                    <style>
                                        .dropdown li a{
                                            cursor: pointer;
                                        }
                                    </style>
                                </div>
                            </td>
                        </tr>
                        @php $sn--; @endphp
                    @endforeach
                @endif
                @if(count(@$dockets)==0)
                    <tr>
                        <td colspan="7">
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
    <!-- Modal -->

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
                                    <input type="text" name="docketTitle" class="form-control" placeholder="Docket Name" required="required" value="{!! old('docketTitle') !!}">

                                </div>
                                <input type="hidden" name="helpFlag" id="helpFlag" value="false">
                            </div>

                            <div class="form-group col-md-12 " style="margin: 0; border-bottom: 1px solid #d2d2d2;    width: 97%;     margin-top: 16px;">
                                <div class="col-md-6" style=" padding: 0;     margin-left: -17px;">
                                    <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Xero Timesheet</label>
                                </div>
                                <div class="col-md-6">
                                    <div style="position:relative; float: right;margin-top: 23px;    margin-right: -40px;">
                                        <select style="    margin-bottom: -9px;padding: 0 45px 0px 45px;" class="form-control" name="xero_timesheet" id="xero_timesheet">
                                            <option value="0">No</option>
                                            <option value="1">Yes</option>
                                        </select>
                                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-12 " style="margin: 0; border-bottom: 1px solid #d2d2d2;    width: 97%;">
                                <div class="col-md-6" style=" padding: 0;     margin-left: -17px;">
                                    <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Timer Attachement</label>
                                </div>
                                <div class="col-md-6">
                                    <div style="position:relative; float: right;margin-top: 23px;    margin-right: -40px;">
                                        <select style="    margin-bottom: -9px;padding: 0 45px 0px 45px;" class="form-control" name="timer_attachement" id="timer_attachement">
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
                                                    <input type="checkbox"  style="    float: left;margin: 3px 0px 0px 3px;" id="buttonApprovess"  checked disabled value="0">
                                                    <label class="form-control" style="font-size: 15px;background-image: none;margin: -24px 0px 0px 26px;text-align: left;" for="buttonApprovess" >Customers and Employees can approve dockets by clicking "Approve"
                                                        button.</label>
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
                                                    <input  style="    float: left;margin: 3px 0px 0px 3px;" type="checkbox" id="buttonAuthorisess"  value="1">
                                                    <label class="form-control" style="font-size: 15px;background-image: none;margin: -24px 0px 0px 26px;text-align: left;" for="buttonAuthorisess">Customers and Employees are required to "Sign" the dockets to
                                                        authorise approval.</label>
                                                </div>
                                                <div class="col-md-5">
                                                    <img src="{{asset("assets/dashboard/images/recordtimeApprovalType2.jpg")}}" width="100%" style="border: 1px solid #eee;border-radius: 5px;">
                                                </div>
                                            </div>


                                        </div>

                                        <div class="option3">
                                            <div class="row">
                                                <div class="col-md-7">
                                                    <h5>Option 3: &nbsp<span style="font-weight: 400;">No approvals</span></h5>
                                                    <input style="    float: left;margin: 3px 0px 0px 3px;" type="checkbox" id="buttonNoApprovess"  value="2">
                                                    <label class="form-control" style="font-size: 15px;background-image: none;margin: -24px 0px 0px 26px;text-align: left;" for="buttonNoApprovess">Customers and Employees are not required to "Sign" the dockets to
                                                        authorise approval.</label>
                                                </div>
                                                <div class="col-md-5">
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


    <div class="modal fade" id="designDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/deleteDocketTemplate','method'=>'POST', 'files' => true]) }}
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
                                    <button type="submit" style=" color: #fff;font-weight: 700;border-radius: 0px;"  class="btn btn-primary importexport">Export Docket Template </button>
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
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/archiveDocketTemplate','method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="archive_docket" name="id">
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


    <div class="modal fade" id="tempOption" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp; Docket Template</h4>
                </div>
                <div class="modal-body">
                    <br/>
                    <div class="row">
                        <input type="hidden" id="tempalteStatus" value="1">
                        <div class="col-md-6">
                            <strong><input type="checkbox" id="bankTemplate" checked value="1"> <label style="cursor: pointer;color: #333333;font-weight: 600;    font-size: 14px;" for="bankTemplate">Blank Template</label> </strong>
                            <p >Start with a blank template and customise it.</p>
                        </div>
                        <div class="col-md-6">
                            <strong><input type="checkbox" id="addFromTemplateBank" value="2"> <label style="cursor: pointer;color: #333333;font-weight: 600;    font-size: 14px;"  for="addFromTemplateBank">Add from Template Bank </label></strong>
                          <p> Pick a template from our template bank and customise it.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="docketTempContinue">Continue</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="publishDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--            {{ Form::open(['url' => 'dashboard/company/docketBookManager/publishDocketTemplate','method'=>'POST', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"  style="font-size: 15px;"><i class="fa fa-plus"></i>&nbsp;Publish your templates to the template library so that other people using Record TIME can view and use them.</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="publish_docket" name="id">
                            <input type="hidden" id="publish_docket_type" name="id">
                            <strong>Enter tags to help users search for this template e.g. Earthmoving, pre-start checklist</strong><br><br>
                            <input type="text" name="color" class="templateBankSugg" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="publishDocketTemplate">Publish</button>
                </div>
            </div>
            {{--            {{ Form::close() }}--}}
        </div>
    </div>


    <div class="modal fade" id="republishDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--            {{ Form::open(['url' => 'dashboard/company/docketBookManager/publishDocketTemplate','method'=>'POST', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel" style="font-size: 15px;"><i class="fa fa-plus"></i>&nbsp;Republish your templates to the template library so that other people using Record TIME can view and use them.</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="republish_docket" name="id">
                            <input type="hidden" id="republish_docket_type" name="id">
                            <input type="hidden" id="republishtemplatebank" name="id">

                            <strong>Enter tags to help users search for this template e.g. Earthmoving, pre-start checklist</strong><br><br>
                            <input type="text" name="republishtag" id="republish_docket_tag" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="republishDocketTemplate">Republish</button>
                </div>
            </div>
            {{--            {{ Form::close() }}--}}
        </div>
    </div>

    <div class="modal fade" id="unpublishDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--            {{ Form::open(['url' => 'dashboard/company/docketBookManager/publishDocketTemplate','method'=>'POST', 'files' => true]) }}--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Unpublish Docket Template</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="un_publish_docket" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to unpublish this template?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" id="unpublishDocketTemplate">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close" >No</button>
                </div>
            </div>
            {{--            {{ Form::close() }}--}}
        </div>
    </div>

    <div class="modal fade" id="docketDuplicate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/docketDuplicate','method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Docket Template Duplicate</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docketDuplicateId" name="id">
                            <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">Docket Name</label>
                            <div class="gorm-group is-empty">
                                <input type="text" name="tempate_name" class="form-control" required="required" value="{!! old('docketTitle') !!}">

                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>


    @include('dashboard.company.docketManager.docket-template.modal-popup.assign-folder.assign-folder')
    @include('dashboard.company.docketManager.docket-template.modal-popup.unassign-folder.unassign-folder')

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

        .wizard > .content {

            min-height: 49em !important;

        }
        .modal {
            overflow-y:auto;
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
    <link rel="stylesheet" href="{{ asset('assets/suggestion/css/amsify.suggestags.css') }}">
    <script src="{{asset('assets/suggestion/js/jquery.amsify.suggestags.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatable').DataTable({
                "order": [ 0, 'desc' ]
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
            var noapproved = document.getElementById('buttonNoApprovess');
            noapproved.checked = false;
            yesDocket.checked = false;
            yesDocketaa.disabled=true;
            yesDocket.disabled = false;
            noapproved.disabled = false;
            $("#docketApprovalValue").val(0);
        });
        $(document).on('click', '#buttonAuthorisess', function () {
            var noDocket = document.getElementById('buttonApprovess');
            var noDocketaa = document.getElementById('buttonAuthorisess');
            var noapproved = document.getElementById('buttonNoApprovess');

            noapproved.checked = false;
            noDocket.checked = false;
            noDocketaa.disabled=true;
            noDocket.disabled = false;
            noapproved.disabled = false;
            $("#docketApprovalValue").val(1);
        });
        $(document).on('click','#buttonNoApprovess', function () {
            var noapprove = document.getElementById('buttonApprovess');
            var noapproved = document.getElementById('buttonNoApprovess');
            var noapproves = document.getElementById('buttonAuthorisess');
            noapprove.checked = false;
            noapproves.checked = false;
            noapproved.disabled=true;
            noapproves.disabled = false;
            noapprove.disabled = false;
            $("#docketApprovalValue").val(2);
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

        $(document).ready(function () {
            $('#docketDuplicate').on('show.bs.modal',function (e) {
                var id = $(e.relatedTarget).data('id');
                $("#docketDuplicateId").val(id);
            });
        });

        $(document).ready(function() {
            $('#publishDocket').on('show.bs.modal', function(e) {
                $('.templateBankSugg span').remove();
                var id = $(e.relatedTarget).data('id');
                var type = $(e.relatedTarget).data('type');
                $("#publish_docket").val(id);
                $("#publish_docket_type").val(type);

            });
        });

        $(document).ready(function() {
            $('#republishDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var templatebank = $(e.relatedTarget).data('templatebank')
                var tag = $(e.relatedTarget).data('tag');
                var type = $(e.relatedTarget).data('type');
                $("#republish_docket").val(id);
                $("#republish_docket_type").val(type);
                $("#republishtemplatebank").val(templatebank);
                $("#republish_docket_tag").val(tag);
                $('#republish_docket_tag').amsifySuggestags({
                    type: 'amsify',
                });

            });
        });

        $(document).ready(function() {
            $('#unpublishDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#un_publish_docket").val(id);
            });
        });


        $(document).on('keyup change', '#datatable_filter label input', function () {
            var base_url = "{{ url('') }}";
            var currentUrl = base_url+$(location)[0].pathname;
            window.history.pushState('page1', 'RecordTime | Dashboard', currentUrl);

            var value = $(this).val();
            if (value == ""){
                var newUrl =currentUrl;
                window.history.pushState('page1', 'RecordTime | Dashboard', newUrl);
            }else {
                var newUrl =currentUrl+'?search='+value;
                window.history.pushState('page2', 'RecordTime | Dashboard', newUrl);
            }

        });

        $(document).ready(function() {
            var input = $(location).attr('search').split("=")[1];
            if (input != null) {
                var finalValue = decodeURI(input);
                $("#datatable_filter label input").val(finalValue);
                $('#datatable').DataTable().search(
                    $("#datatable_filter label input").val()
                ).draw();
            }
        });


        $(document).on('click','#publishDocketTemplate',function () {

            var type = $('#publish_docket_type').val();
            var tag = $(".templateBankSugg").val();
            var id =  $("#publish_docket").val();


            $.ajax({
                type:"Post",
                url: '{{ url('/dashboard/company/publishDocketTemplate')}}',
                data:{'docket_id':id,publish_docket_type:type,tag:tag},
                success: function (response) {
                    if (response['status']== true){
                        $('#publishDocket').modal('hide');
                        $('.publishDocket'+id).html(response['data']);
                        window.location.assign("{{ url('dashboard/company/templateBank') }}");
                    }
                }
            });
        });


        $(document).on('click','#republishDocketTemplate',function () {
            var type = $('#republish_docket_type').val();
            var tag = $("#republish_docket_tag").val();
            var id =  $("#republish_docket").val();
            var republishtemplatebank = $("#republishtemplatebank").val();
            $.ajax({
                type:"Post",
                url: '{{ url('/dashboard/company/publishDocketTemplate')}}',
                data:{'docket_id':id,publish_docket_type:type,tag:tag,templateBankId: republishtemplatebank},
                success: function (response) {
                    if (response['status']== true){
                        $('#republishDocket').modal('hide');
                        $('.publishDocket'+id).html(response['data'])

                    }
                }
            });
        });

        $(document).on('click','#unpublishDocketTemplate',function () {
            var id =  $("#un_publish_docket").val();

            $.ajax({
                type:"Post",
                url: '{{ url('/dashboard/company/unpublishDocketTemplate')}}',
                data:{'docket_id':id},
                success: function (response) {
                    if (response['status']== true){
                        $('#unpublishDocket').modal('hide');
                        $('.publishDocket'+id).html(response['data'])

                    }

                }
            });
        });

        $(document).ready(function() {

            $('input[name="color"]').amsifySuggestags({
                type: 'amsify',
            });



        })


        $(document).on('change','#bankTemplate',function () {
            var addFromTemplateBank = document.getElementById('addFromTemplateBank');
            var bankTemplate = document.getElementById('bankTemplate');
            if(addFromTemplateBank.checked) {
                addFromTemplateBank.checked = false;
                var selectedValue = $(this).val();
                $('#tempalteStatus').val(selectedValue);
            }else {
                bankTemplate.checked = true;
                var selectedValue = $(this).val();designDocket
                $('#tempalteStatus').val(selectedValue);
            }
        });


        $(document).on('change','#addFromTemplateBank',function () {
            var addFromTemplateBank = document.getElementById('addFromTemplateBank');
            var bankTemplate = document.getElementById('bankTemplate');

            if(bankTemplate.checked) {
                bankTemplate.checked = false;
                var selectedValue = $(this).val();
                $('#tempalteStatus').val(selectedValue);
            }else {
                addFromTemplateBank.checked = true;
                var selectedValue = $(this).val();
                $('#tempalteStatus').val(selectedValue);
            }
        });

        $(document).on('click','#docketTempContinue',function () {
            var value = $('#tempalteStatus').val()
            if (value == 1){

                $('#tempOption').modal('hide');
                $('#myModal3').modal('show',function () {
                    $('#wizard').easyWizard();
                });


            }else if(value == 2){
                window.location.assign("{{ url('dashboard/company/templateBank') }}")
            }
        });

    </script>
@endsection
