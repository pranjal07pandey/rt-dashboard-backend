@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active"><a href="#">Design Docket</a></li>
        </ol>
        <div class="clearfix"></div>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>

    @include('dashboard.company.include.flashMessages')
    <div class="dashboardFlashsuccess" style="display: none;">
        <div class="alert alert-success" style="padding: 5px 10px;font-size: 13px;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p class="messagesucess"></p>
        </div>
    </div>
    <div class="dashboardFlashdanger" style="display: none;">
        <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p class="messagedanger"></p>
        </div>
    </div>


    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 450px;">
        <div class="col-md-4">

            <div class="designDocketLeftSideView">

                <div class="leftSideHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Docket Elements
                    </h3>
                </div>

                <div class="leftSideBody">
                    <div class="elementAddingDiv">
                        <input type="hidden" id="templeteDocketId" value="{{ $tempDocket->id }}" >
                        <ul>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="shortTextAdd" fieldType="1">
                                    <span><i class="fa fa-plus-square"></i> Short Text </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Short Text" data-content="A text-box with a single line of entry. E.g. operator name, Job numbers."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary btn-xs themeSecondaryBgs withripple docketComponent" id="longTextAdd" fieldType="2">
                                    <span><i class="fa fa-plus-square"></i> Long Text </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Long Text" data-content="A multi-line textbox ideal for large amounts of text. E.g. description. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="locationAdd" fieldType="4">
                                    <span><i class="fa fa-plus-square"></i> Location  </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Location" data-content="Capture the user’s current location or ask them to manually enter it."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="imageAdd" fieldType="5">
                                    <span><i class="fa fa-plus-square"></i> Images </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Images" data-content="Allow users to take a photo or upload one from the device. This also allows the user to draw/sketch over photos."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="numAdd" fieldType="3">
                                    <span><i class="fa fa-plus-square"></i> Number </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Number" data-content="An input field to capture numbers. E.g. hours, hourly rate."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="dateAdd" fieldType="6">
                                    <span><i class="fa fa-plus-square"></i> Date </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Date" data-content="Captures dates. E.g. job date. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="unitRateAdd" fieldType="7">
                                    <span><i class="fa fa-plus-square"></i> Unit Rate </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Unit Rate" data-content="Unit rate allows the user to input a unit (i.e. total hours) and a unit rate (i.e. hourly rate). This field then calculates the total unit x unit rate (hours x hourly rate)."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="signatureAdd" fieldType="9">
                                    <span><i class="fa fa-plus-square"></i> Signature </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Signature" data-content="Allows the user to capture signatures and names. Multiple signatures can be captured. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <!-- only for rt user for testing pourpose -->

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="sketchPadAdd" fieldType="14">
                                    <span><i class="fa fa-plus-square"></i> SketchPad </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Sketch Pad" data-content="This field allows the user to draw/sketch on the screen."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="DocumentAdd" fieldType="15">
                                    <span><i class="fa fa-plus-square"></i> Document </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Document" data-content="This allows the admin to attach a pdf document to the docket."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                            </li>


                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="16" requires="0">
                                    <span><i class="fa fa-plus-square"></i> Barcode Scanner </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Barcode Scanner" data-content="This field allows the user to capture barcode / QR codes. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="headerAdd" fieldType="12">
                                    <span><i class="fa fa-plus-square"></i>  Header/Title </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 11px 4px 14px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Header/Title" data-content="This allows you to create headings for different tasks to keep your dockets organised or you can simply use it as instructions."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple" data-toggle="modal" data-target="#gridModal" data-field_type="22">
                                    <span><i class="fa fa-plus-square"></i> Grid </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Grid" data-content="You can create a table-like structure with the majority of the fields. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="13">
                                    <span><i class="fa fa-plus-square"></i> Terms And Conditions </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Terms and Conditions" data-content="This allows you to write your disclaimer or terms and conditions on all the dockets that get created. You can add a “disclaimer” from “Docket Book Manager >> Docket settings”. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="timeAdd" fieldType="26" requires="0">
                                    <span><i class="fa fa-plus-square" aria-hidden="true"></i> Time  </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Time" data-content="It allows the users to timestamp their docket with the desired time."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="18"  requires="0">
                                    <span><i class="fa fa-plus-square"></i> Yes/No-N/a Checkbox </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Yes/No-N/a Checkbox" data-content="A checkbox with the options “Yes” “No” and “Na”. You can also ask for an explanation if either of these fields is clicked."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>





                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="checkboxAdd" fieldType="8">
                                    <span><i class="fa fa-plus-square"></i> CheckBox </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Checkbox" data-content="Create a checkbox. For a simple yes/no question. E.g. Did you check this task?"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>


                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple" data-toggle="modal" data-target="#tallyable" data-field_type="23">
                                    <span><i class="fa fa-plus-square"></i> Tallyable </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Tallyable" data-content="This is similar to the unit rate field. Use this if you want to aggregate costs for a project."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>


                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent" id="checkboxAdd" fieldType="20">
                                    <span><i class="fa fa-plus-square"></i> Manual Timer </span>
                                </a>
                                <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Manual Timer" data-content="This allows the user to enter their start time, finish time, lunch break and automatically creates the total hours worked."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                            </li>

                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="27">
                                    <span><i class="fa fa-plus-square"></i> Advanced Header </span>
                                </a>

                            </li>


                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple" data-toggle="modal" data-target="#docketConstant"   data-field_type="30">
                                    <span><i class="fa fa-plus-square"></i> Docket Constant </span>
                                </a>

                            </li>
                            @if(Session::get('company_id')==1)
                                <li>
                                    <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="28">
                                        <span><i class="fa fa-plus-square"></i> Folder </span>
                                    </a>
                                </li>

                                <li>
                                    <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="29">
                                        <span><i class="fa fa-plus-square"></i> Email </span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="#" class="btn btn-primary themeSecondaryBgs  btn-xs withripple docketComponent"  fieldType="31">
                                    <span><i class="fa fa-plus-square"></i> Image Instruction </span>
                                </a>
                            </li>

                            <li class="clearfix"></li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div>

            </div>

            <div class="hidden-sm hidden-xs designDocketLeftSideView">
                <div  class="leftSideHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Docket Info
                        <a class="pull-right"  data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-plus-square"></i> Update
                        </a>
                    </h3>

                </div>
                <div class="leftSideBody">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Docket Name</strong>
                        </div>
                        <div class="col-md-6" style="word-break: break-word;padding-right: 25px">
                            {{ $tempDocket->title }}
                        </div>

                        <div class="clearfix"></div>
                        <hr>
                        <div class="col-md-6">
                            <strong>Invoiceable</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Invoiceable" data-content="If checked, this option will allow approved dockets, created with this template, to be attached to an invoice."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" id="invoiceableCheckboxInput" data="{{ $tempDocket->id }}"  dockettitle="{{$tempDocket->title }}" invoiceable="{{$tempDocket->invoiceable}}"   @if($tempDocket->invoiceable==1) checked @endif>
                        </div>


                        <div class="clearfix"></div>
                        <hr>
                        <div class="col-md-6">
                            <strong>Timer Attachment</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Timer Attachment" data-content="If checked, this option will allow you to attach the Timer/Bundy clock details to your dockets. "><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" id="timerAttachementChecked" datas="{{ $tempDocket->id }}"  dockettitles="{{$tempDocket->title }}" timer_attachement="{{$tempDocket->timer_attachement}}"  @if($tempDocket->timer_attachement==1) checked @endif value="0">
                        </div>

                        <div class="clearfix"></div>
                        <hr>
                        <div class="col-md-6">
                            <strong>Docket Timesheet</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Docket Timesheet" data-content="Check this if this template is a Xero timesheet template."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-6">
                            <input type="checkbox" id="docketTimesheet" datas="{{ $tempDocket->id }}"  dockettitles="{{$tempDocket->title }}" timer_attachement="{{$tempDocket->xero_timesheet}}"  @if($tempDocket->xero_timesheet==1) checked @endif value="0" disabled>
                        </div>
                        <br>
                        <hr>
                        <div class="col-md-6">
                            <strong>Docket Prefix</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Docket Prefix" data-content="It allows you to create a default prefix to keep track of dockets as per your company's requirements. For example, it changes RT-Doc-23-1 to RT-Your Prefix-23-1"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-6">
                            <span id="docketPrefix">
                            <a href="#"
                               class="editable"
                               data-type="text" data-pk="{{ $tempDocket->id }}"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateDocketPrefix') }}"
                               data-title="Enter Label Text">{{ $tempDocket->prefix }}</a>
                            </span>
                        </div>

                        <br>
                        <hr>
                        <div class="col-md-8">
                            <strong>Hide Docket Prefix</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Hide Docket Prefix" data-content="If checked, this option will hide a docket prefix and will only display numbers. For example, it changes RT-Doc-23-1 to 23-1 as it hides the prefix."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-3">
                            <span id="docketPrefix">
                               <input type="checkbox" id="hidePrefix" @if($tempDocket->hide_prefix == 1 ) checked @endif >
                            </span>
                        </div>

                        <br>
                        <hr>
                        <div class="col-md-7">
                            <strong>Show Deleted Elements</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Show Deleted Elements" data-content="Shows all elements that were deleted in this template. You can recover them if needed."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-4">
                                <span id="docketPrefix">
                                   <input type="checkbox" id="showDeletDocket" @if($isDeletedShow == 1 ) checked @endif >
                                </span>
                        </div>



                        <br>
                        <hr>
                        <div class="col-md-8">
                            <strong>View Docket Number</strong>
                            <a tabindex="0" style="margin-top: 0px;  position: absolute;  padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="View Docket Number" data-content="If this option is checked, you are able to view the docket number before the docket is sent. Useful for Purchase Orders."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                        <div class="col-md-3">
                            <span id="docketPrefix">
                               <input type="checkbox" id="showDocketNumber" @if($tempDocket->is_docket_number == 1 ) checked @endif @if($tempDocket->companyInfo->number_system ==1) disabled @endif>
                            </span>
                        </div>


                        <br>
                        <hr>

                        <div class="col-md-8">
                            <strong>Clear Export Rules</strong>
                        </div>

                        <div class="col-md-3">
                            <button class="btn btn-danger btn-xs btn-raised" data-toggle="modal" data-target="#clearExportRule">Clear All</button>
                        </div>

                        <br>
                        <hr>

                        <div class="col-md-6">
                            <strong>Docket Id Label</strong>
                        </div>

                        <div class="col-md-6">
                           <span id="docketPrefix">
                            <a href="#"
                               class="editable"
                               data-type="text" data-pk="{{ $tempDocket->id }}"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateDocketIdLabel') }}"
                               data-title="Enter Label Text">{{ $tempDocket->docket_id_label }}</a>
                            </span>
                        </div>





                    </div>
                </div>
            </div>


            <div id="forth" class="hidden-sm hidden-xs designDocketLeftSideView">
                <div class="leftSideHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Current Approval Setting
                        <a class="pull-right"  data-toggle="modal" data-target="#updateAprovalMethod">
                            <i class="fa fa-plus-square"></i> Update
                        </a>

                    </h3>
                </div>

                <div class="leftSideBody ">

                    <div class="col-md-12">
                        <span> @if($tempDocket->docketApprovalType == 0) Default Approval with button <input type="hidden" class="approvalMethod" value="{{$tempDocket->docketApprovalType}}"> @elseif($tempDocket->docketApprovalType== 1)  Require "Name" and "Signature" for approvals <input type="hidden" class="approvalMethod" value="{{$tempDocket->docketApprovalType}}"> @elseif($tempDocket->docketApprovalType== 2)  No approvals <input type="hidden" class="approvalMethod" value="{{$tempDocket->docketApprovalType}}"> @endif </span>

                    </div>

                </div>
            </div>


            <div id="forth" class="hidden-sm hidden-xs designDocketLeftSideView">
                <div class="leftSideHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Default Recipients
                        <a class="pull-right"  data-toggle="modal" data-target="#addDefaultRecipent">
                            <i class="fa fa-plus-square"></i> Add
                        </a>
                        <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px; margin-top: 0px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Default Recipients" data-content="You can nominate default “Record Time Users” or “Email Clients” on a template. This allows users to send dockets quickly without having to select the recipient every single time. You can also remove default user/email and add extra users."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                    </h3>
                </div>

                <div class="leftSideBody ">
                    @if(count($tempDocket->defaultRecipient)==null)
                        <p style="font-style: italic;    margin-left: 18px; ">Empty Data.</p>
                    @else
                        <span style="margin-left: 5px;font-weight: 600;"> Rt User</span> <hr style="margin: 5px;    border-top: 2px solid #eaeaea;">
                        <ul>

                            @foreach($tempDocket->defaultRecipient as $defaultRecipient)

                                @if(@$defaultRecipient->user_type== 1)
                                    <li  class="defaultlistrecipient"> {{ @$defaultRecipient->userInfo->first_name." ".@$defaultRecipient->userInfo->last_name }}
                                        <a class='delete '  data-toggle="modal" data-target="#deleteDefaultRecipient" data-templateid="{{ @$tempDocket->id }}"  data-usertype="1" data-type="1" data-recipientid="{{@$defaultRecipient->emailUser->id}}" href="#"><span  style="padding: 3px 0px 0px 0px;" class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <br>
                        <span style="margin-left: 5px;font-weight: 600;"> Email Client</span> <hr style="margin: 5px;    border-top: 2px solid #eaeaea;">
                        <ul>

                            @foreach($tempDocket->defaultRecipient as $defaultRecipient)

                                @if(@$defaultRecipient->user_type==2)
                                    <li  class="defaultlistrecipient"> {{ $defaultRecipient->emailUser->email}}
                                        <a class='delete '  data-toggle="modal" data-target="#deleteDefaultRecipient" data-templateid="{{ @$tempDocket->id }}"  data-usertype="2" data-type="1" data-recipientid="{{@$defaultRecipient->emailUser->id}}" href="#"><span  style="padding: 3px 0px 0px 0px;" class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>

                                    </li>
                                @endif
                            @endforeach
                        </ul>

                    @endif
                </div>
            </div>




            <div id="forth" class="hidden-sm hidden-xs mobileTemplatePreview designDocketLeftSideView">
                <div class="leftSideHeader">
                    <h3 class="active">
                        <i class="fa fa-th-list"></i> Mobile Template Preview
                    </h3>
                </div>
                <div class="leftSideBody" style="position: relative;min-height: 700px;">
                    <img class="mobileframe" style="height: 688px;"  src="{{asset("phone.png")}}">
                    {{--<div  style="background: url({{asset("phone.png")}}) no-repeat;width: 100%;min-height: 700px;background-size: 500px;background-position:  66px 67px; position: absolute; "></div>--}}
                    <div class="mobileContentWrapper">
                        <div class="mobilecontain" >
                            <div style=" position: -webkit-sticky; /* Safari */position: sticky;top: 0;background-color: #012f54; height: 40px;z-index: 1;"  class="mobile-bar">
                                <p style="padding: 11px;font-size: 12px;color: #fff; font-weight: 700; text-align: center;">
                                    <i style="float: left;font-size: 14px;     padding: 3px;" class="fa fa-chevron-left" aria-hidden="true"></i>
                                    {{ $tempDocket->title }}

                                    <i style="float: right;font-size: 14px;     padding: 3px;" class="fa fa-home" aria-hidden="true"></i></p>

                            </div>
                            <div class="main-size" id="mobileviewHtml">
                                @include('dashboard.company.docketManager.mobileView')
                            </div>
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


                @if($totalAssign==0)
                    <button  data-toggle="modal" data-target="#myModalAssignDelete" class="btn btn-xs btn-raised btn-danger eight" data-id="{{ $tempDocket->id }}"  style="margin: 0px;">
                        <i class="fa fa-trash"></i> Cancel<div class="ripple-container"></div>
                    </button>
                    <a href="#" data-toggle="modal" data-target="#myModalAssign" class="btn btn-xs btn-raised btn-success eight" id="addNew1" style="margin: 0px;">
                        <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                    </a>&nbsp;
                @else
                    <a id="addNew" href="{{route('dockets.template.index')}}"   class="btn btn-xs btn-raised btn-success eight tourModel"  style="margin: 0px;">
                        <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                    </a>&nbsp;
                @endif
            </div>


            <div class="clearfix"></div>
            <hr style="margin:5px 0px;"/>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.15.0/slimselect.min.js"></script>
            <span class="spinnerForDeleteField" style="font-size: 41px;position: absolute;display: none;z-index: 1;left: 50%;top: 50%;"><i class="fa fa-spinner fa-spin"></i></span>

            <div  class="componentScroll">
                <div class="row" id="sortable">

                    @if($tempDocketFields)
                        @foreach($tempDocketFields as $item)
                            @include('dashboard.company.docketManager.elementTemplate')
                        @endforeach
                    @endif
                </div>



                <div class="row" id="sortableFooter">
                    @if($tempDocketFields)
                        @foreach($tempDocketFields as $item)
                            @include('dashboard.company.docketManager.footerElementTemplate')
                        @endforeach
                    @endif
                </div>
                <div id="elementTemplateBottom"></div>

            </div>

        </div>

        <div class="pull-right footerButton"@if(count($tempDocketFields) >= 6) style="display: block;" @else style="display: none;" @endif >
            @if($totalAssign==0)
                <button  data-toggle="modal" data-target="#myModalAssignDelete" class="btn btn-xs btn-raised btn-danger eight" data-id="{{ $tempDocket->id }}"  style="margin: 0px;">
                    <i class="fa fa-trash"></i> Cancel<div class="ripple-container"></div>
                </button>
                <a href="#" data-toggle="modal" data-target="#myModalAssign" class="btn btn-xs btn-raised btn-success eight" id="addNew1" style="margin: 0px;">
                    <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                </a>&nbsp;
            @else
                <a id="addNew" href="{{route('dockets.template.index')}}"   class="btn btn-xs btn-raised btn-success eight tourModel"  style="margin: 0px;">
                    <i class="fa fa-save"></i> Save<div class="ripple-container"></div>
                </a>&nbsp;
            @endif
        </div>

    </div>


    <br/><br/><br/>
    <!-- Modal -->


    <div class="modal fade" id="gridModalUpdate" tabindex="-1" role="dialog" aria-labelledby="gridModalUpdate" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Add Column To Grid</h4>
                </div>
                {{ Form::open(['route' => 'grid.table.update','class'=>'form-horizontal']) }}
                <input type="hidden" name="docket_field_id" id="grid_dockets_field_id">
                <input type="hidden" name="docket_id" value="{{ $tempDocket->id }}">
                <div class="modal-body">
                    <div class=" col-md-12" style="margin: 0">
                        <label class="control-label" for="title">Number Of Column(s)</label>
                        <div class="gridSelection">
                            <select class="form-control gridUpdatefield" name="number_of_column" required id="columnSelect" onchange="changeEventHandler(event);">
                                <option disabled>Select Number Of Column</option>
                                <option selected value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                            </select>
                        </div>


                        <div class="grid_cell" style="width: 522px;" >
                            <label class="control-label" for="title" style="margin-bottom: 10px;">Grid Cell</label>
                            <div style="display: block;overflow-x: auto;white-space: nowrap;width: 522px;max-width: none;">
                                <table class="table" style="    border: 1px solid #ddd;" >
                                    <thead>
                                    <tr class="grid_table">
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary gridModalUpdateButton">Save changes</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade " id="gridModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Modular Grid</h4>
                </div>

                {{ Form::open(['route' => 'grid.table.save', 'files' => true]) }}
                <div class="modal-body">
                    <input type="hidden" name="grid_id" value="22">
                    <input type="hidden" name="docket_id" value="{{ $tempDocket->id }}">
                    <div class="row">
                        <div class="col-md-12" style="margin: 0">

                            <label class="control-label" for="title">Number Of Column(s)</label>
                            <div class="gridSelection">
                                <select class="form-control gridCreatefield" name="number_of_column" required id="columnSelect" onchange="changeEventHandler(event);">
                                    <option selected disabled>Select Number Of Column</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                </select>
                            </div>

                            <div class="grid_cell" style="width: 522px;" >
                                <label class="control-label" for="title" style="margin-bottom: 10px;">Grid Cell</label>
                                <div style="display: block;overflow-x: auto;white-space: nowrap;width: 522px;max-width: none;">
                                    <table class="table" style="    border: 1px solid #ddd;" >
                                        <thead>
                                        <tr class="grid_table">
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary gridModalButton">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>



    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Docket Info</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketBookManager/updateTempDocket', 'files' => true]) }}
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

    <div class="modal fade " id="myModalAssignDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Do you want to assign this Docket Template?</h4>
                </div>

                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin-top:0px;">
                                <label for="templateId" class="control-label">Docket Template</label>
                                <input type="hidden" name="templateId" value="{{ $tempDocket->id }}">
                                <input type="text" class="form-control" readonly value="{!! $tempDocket->title !!}">
                            </div>
                            <div class="form-group" style="margin-top:0px;">
                                <label for="employeeId" class="control-label">Employee</label>
                                <select id="employeeId" class="form-control" required name="employeeId">
                                    <option value="">Select Employee</option>
                                    @if($employees)
                                        @foreach($employees as $row)
                                            <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModalAssign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Do you want to assign this Docket Template?</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketManager/assignDocket/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" style="height: 460px;overflow: auto;">
                            <div style="margin-top: 4px;" class="form-group">
                                <label class="control-label" for="title">Docket Id</label>
                                <input type="hidden" name="templateId" value="{{ $tempDocket->id }}">
                                <input type="text" class="form-control" readonly value="{!! $tempDocket->title !!}">
                            </div>
                            <div class="fomr-group pull-right">
                                <button type="button" class="btn btn-xs btn-raised btn-block btn-info cloneEmployee">
                                    <i class="fa fa-plus-square"></i> Add Employee
                                </button>
                            </div><br>
                            <div class="cloneUnit" style="background-color: #f9faf9;padding: 0px 20px 170px 20px;">
                                <div class="form-group label-floating">
                                    <div class="col-md-12 designDocket">
                                        <div class="form-group" style="padding-bottom: 20px;margin: 27px 0px 0px -12px;">
                                            <label for="employeeId" class="control-label">Employee</label>
                                            <select class="form-control designDocket employeeList" required name="employeeId[]">
                                                <option value="">Select Docket Template</option>
                                                @if($employees)
                                                    @foreach($employees as $row)
                                                        <option value="{!! $row['id'] !!}">{!! $row['name'] !!}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-top: 0;">
                                    <div class="col-md-6">
                                        <label  style="margin-right: 10px;">Regular Assign </label>
                                        <input type="checkbox" name="assignType[]" class="assignType" checked value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label style="margin-right: 10px;">Date Range</label>
                                        <input type="checkbox" name="assignType[]" class="assignType" value="1">
                                        <input type="text" class="daterange form-control" name="daterange[]" value="{{\Carbon\Carbon::now()->format('m-d-Y')}} - {{\Carbon\Carbon::now()->addWeek()->format('m-d-Y')}}" style="display: none" />
                                    </div>
                                </div>
                            </div>
                            <div class="appendCloneUnit"></div>
                            <div class="col-md-4">
                                <div class="form-group" style="padding-bottom: 20px;margin: 24px 0 0 0;    float: left;">
                                    <button style="" type="submit" class="btn btn-primary">Save</button>
                                </div>
                                <div style="padding-bottom: 20px;margin: 24px 0 0 0;    float: right;">

                                    <a style=" text-transform: capitalize;" class="btn btn-primary" href="{{route('dockets.template.index')}}">Assign Later</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModal" aria-hidden="true">
        <div class="modal-dialog nine" role="document">
            <div style="    margin-top: 135px;" class="modal-content">
                <div class="modal-body">
                    <ol>
                        <li>You can delete a field by simply clicking the delete symbol. Please note: once a template has been used, any of its field cannot be deleted.</li>
                        <li style="margin-bottom: 4px;">Click a “Docket Preview” checkbox to preview the field on “Sent” and “Received” dockets on the backend. Please note: more field you preview will slow the page load time. Therefore, it is best to preview dockets with unique docket information, this way you do not have to go inside each and every docket.</li>
                        <img src="{{asset('assets/dashboard/tour/demo.png')}}">
                        <li style="margin-top: 4px;">Go to Docket Book Manager >> Docket Template to view all existing docket templates. You can preview, change the fields order, change label names and so on. You can also delete docket templates from here as long as it has not been used to send a docket.</li>
                        <li>Go to Docket Book Manager >> Assign Dockets Template to assign dockets to employees and un-assign them from a docket/s. </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <!--<div class="modal fade" id="prefillers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">-->
    <!--    <div id="second"  class="modal-dialog modal-lg" role="document">-->
    <!--        {{--<div id="model" data-target="#myModal"></div>--}}-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header themeSecondaryBg">-->
    <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    <!--                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefllers</h4>-->
    <!--            </div>-->
    <!--            {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/designDocket/saveprefiller', 'files' => true]) }}--}}-->
    <!--            <div class="modal-body">-->
    <!--                <div class="row">-->
    <!--                    <div class="form-group">-->

    <!--                        <div class="col-md-2">-->
    <!--                            <div style="margin-top: 4px;" class="form-group">-->
    <!--                                <h4 style="    font-size: 15px;margin-top: -23px;">Docket Field :-</h4>-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                        <div class="col-md-10">-->
    <!--                            <div style="margin-top: -21px;" class="form-group">-->
    <!--                                <input  style="border: transparent;     margin-left: -29px;"  id="docket_field_label" readonly >-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="form-group label-floating">-->
    <!--                        <div class="col-md-10">-->
    <!--                            <div style="    margin-top: 4px;" class="form-group">-->
    <!--                                <input type="hidden" name="docket_field_id" id="docket_field_id">-->
    <!--                                <label class="control-label" for="title" >Value</label>-->
    <!--                                <input  type="text"  name="value" class="form-control" id="valueprefiller">-->
    <!--                            </div>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="col-md-2">-->
    <!--                        <div class="form-group" style="margin: -8px 0 0 0">-->
    <!--                            <button style="" type="submit" class="btn btn-primary" id="saveprefillersdesign">Save</button>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="modal-footer">-->
    <!--            </div>-->
    <!--            {{--{{ Form::close() }}--}}-->
    <!--        </div>-->

    <!--    </div>-->
    <!--</div>-->

    <div class="modal fade" id="prefillers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefllers</h4>
                </div>
                {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/designDocket/saveprefiller', 'files' => true]) }}--}}
                <div class="modal-body">
                    <div class="row">

                        <input type="hidden" id="prefillerTypecheck">
                        <div class="form-group">
                            <div class="col-md-2">
                                <div style="margin-top: 4px;" class="form-group">
                                    <h4 style="    font-size: 15px;margin-top: -23px;">Docket Field :-</h4>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div style="margin-top: -21px;" class="form-group">
                                    <input  style="border: transparent;     margin-left: -29px;"  id="docket_field_label" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div style="margin:28px 0px -17px 12px;">
                                <input style="    float: left;margin-right: 12px;" type="checkbox" class="prefillerLinkCheck"  value="1">
                                <p style="float: left"> Check to link Prefiller </p>
                                <span class="spinnerCheck" style="padding:0 0px 0px 165px;font-size: 14px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="appenddatabytype">

                        </div>

                        <div class="clearfix"></div>

                        <div class="appendvaluetype">
                            <div class="col-md-1">
                                <div class="form-group float-left">
                                    <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                </div>

                            </div>
                            <div class="col-md-11">
                                <div style="    margin-top: 15px;" class="form-group">
                                    <input type="hidden" name="docket_field_id" id="docket_field_id">
                                    <input  type="text"  name="value" maxlength="50" class="form-control" id="valueprefiller">
                                    <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>

                                </div>
                            </div>
                        </div>


                        <div class="clearfix"></div>
                        <div class="col-md-12">
                            <div class="col-md-10">
                            </div>
                            <div class="col-md-2">
                                <div class="form-group " style="  margin: 0px 0 -15px 0;">
                                    <button style="" type="submit" class="btn btn-primary" id="saveprefillersdesign">Save</button>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
                <div class="modal-footer">
                </div>
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

    <div class="modal fade" id="deleteGridColumn" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Grid Column</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" class="form-control" id="grid_column_id_column">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this  Grid Field?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary deleteGridColumnButton">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="myModalAssignDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/deleteDocketTemplate' ,'method'=>'POST', 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Cancel Docket Template</h4>
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


    <div class="modal fade" id="linkPrefiller" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefllers</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/designDocket/saveLinkPrefiller', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">

                            <div class="col-md-2">
                                <div style="margin-top: 4px;" class="form-group">
                                    <h4 style="    font-size: 15px;margin-top: -23px;">Docket Field :-</h4>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div style="margin-top: -21px;" class="form-group">
                                    <input  style="border: transparent;     margin-left: -29px;"  id="linkPrefiller_label" readonly >
                                </div>
                            </div>
                        </div>
                        @if($docketPrefillerValue->count()==0)
                            <div class="form-group label-floating">
                                <div class="col-md-9">
                                    <p style="font-style: italic;">* A prefiller is a list of items that can be selected from a drop-down list by the end user. Please visit the prefiller manager to create pre-fillers.</p>
                                </div>
                                <div class="col-md-3">
                                    <a style="margin-top: -8px;text-transform: none;" href="{{ route('companyPrefillerManager') }}" class="btn btn-primary">Go to Prefiller Manager</a>
                                </div>
                            </div>
                        @else

                            <div class="form-group label-floating">
                                <div class="col-md-10">
                                    <div style="    margin-top: 4px;" class="form-group">
                                        <input type="hidden" name="dockets_field_id" id="dockets_field_id">
                                        <select name="docket_prefiller_id" class="form-control">
                                            <option >Select Prefiller</option>
                                            @if($docketPrefillerValue->count())
                                                @foreach($docketPrefillerValue as $docketPrefillerValues)
                                                    <option value="{{ $docketPrefillerValues->id }}">{{ $docketPrefillerValues->title }}</option>
                                                @endforeach
                                            @endif

                                        </select>
                                    </div>
                                    <i>* A prefiller is a list of items that can be selected from a drop-down list by the end user. Please visit the prefiller manager to create pre-fillers.</i>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group" style="margin: -8px 0 0 0">
                                    <button style="" type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        @endif


                    </div>
                </div>
                <div class="modal-footer">
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <div class="modal fade " id="addBookDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp; Docket Info</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input id="docket_id" type="hidden" >
                            <p> <i class="fa fa-exclamation-circle"></i> <span style="left: 33px;top: -1px;position: absolute;" id="dockettitle"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveinvoiceableCheckboxInput">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close" id="noinvoiceableCheckboxInput">No</button>
                </div>

            </div>
        </div>
    </div>

    <!--    <div class="modal fade" id="deleteLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">-->
    <!--    <div id="second"  class="modal-dialog modal-lg" role="document">-->
    <!--        {{--<div id="model" data-target="#myModal"></div>--}}-->
    <!--        {{ Form::open(['url' => 'dashboard/company/docketBookManager/designDocket/deletePreFiller' , 'files' => true]) }}-->
    <!--        <div class="modal-content">-->
    <!--            <div class="modal-header themeSecondaryBg">-->
    <!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
    <!--                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Template Label</h4>-->
    <!--            </div>-->
    <!--            <div class="modal-body">-->
    <!--                <div class="row">-->
    <!--                    <div class="col-md-12">-->
    <!--                        <input type="hidden" id="docketdelete_label_id" name="id">-->
    <!--                        <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to remove assigned label?</p>-->
    <!--                    </div>-->
    <!--                </div>-->
    <!--            </div>-->
    <!--            <div class="modal-footer">-->
    <!--                <button type="submit" class="btn btn-primary">Yes</button>-->
    <!--                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        {{ Form::close() }}-->
    <!--    </div>-->
    <!--</div>-->


    <div class="modal fade" id="deleteLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Template prefiller</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docketdelete_prefiler_id" name="id">
                            <input type="hidden" id="docketdelete_prefiler_field_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to remove assigned prefiller?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="deletePrefillerValue">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addPrefillerValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Template prefiller</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden"  id="prefillerindvsentdocketvalueid">
                    <input type="hidden"  id="prefillerindvsentdocketfieldid">
                    <input type="hidden"  id="prefillerindvsentdocketprefillerid">
                    <input type="hidden" id="prefillerindvindex">
                    <input type="hidden" id="prefillerindvintegertype" >


                    <div class="row">


                        <div class="form-group">
                            <div class="col-md-2">
                                <div style="margin-top: 4px;" class="form-group">
                                    <h4 style="    font-size: 15px;margin-top: -23px;">Docket Field :-</h4>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div style="margin-top: -21px;" class="form-group">
                                    <input  style="border: transparent;     margin-left: -29px;"  id="indvDocName" readonly>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div style="margin:28px 0px -17px 12px;">
                                <input style="    float: left;margin-right: 12px;" type="checkbox" class="prefillerLinkChecksingle"  value="1">
                                <p style="float: left"> Check to link Prefiller </p>
                                <span class="spinnerCheck" style="padding:0 0px 0px 165px;font-size: 14px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="appenddatabytypes"></div>

                        <div class="clearfix"></div>

                        <div class="appendvaluetypes">

                            <div class="col-md-1">
                                <div class="form-group float-left">
                                    <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                </div>
                            </div>
                            <div class="col-md-11">
                                <div style="    margin-top: 15px;" class="form-group">
                                    <input  type="text"  name="value" maxlength="50" class="form-control" id="prefillerInvValue">
                                    <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>
                                </div>
                            </div>

                        </div>

                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="addIndPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="updateTimerAttachement" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp; Docket Info</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input id="docket_id" type="hidden" >
                            <p> <i class="fa fa-exclamation-circle"></i> <span style="left: 33px;top: -1px;position: absolute;" id="docket_titles"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveTimerAttachementChecked">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close" id="saveTimerAttachementUnchecked">No</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade " id="subDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1111111;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content modalHeight" >
                <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>Yes/No/NA Explanation</h4>
                </div>

                <div style="max-height: calc(91vh - 100px);" class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="mobileviewHtmlSubDocket">


                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade " id="labelTypePopUP" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1111111;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="    height: 272px;" >
                <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                    <span class="sr-only">Loading...</span>
                </div>
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus" style="    margin-right: 9px;"></i>Select Label Type Icon</h4>
                </div>
                <div style="max-height: calc(100vh - 100px);overflow-y: hidden;" class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <div id="mobileviewHtmlLabeltype">


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade " id="addDefaultRecipent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 1111111;">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content "  style="min-height: 300px;">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Default Recipient</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/saveDefaultRecipient', 'files' => true]) }}
                <div style="max-height: calc(90vh - 100px);" class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" value="1" name="templateType">
                            <input type="hidden" value="{{ $tempDocket->id }}" name="templateId">

                            <select id="recipientsType" class="form-control" name="type">
                                <option value="1">Rt User</option>
                                <option value="2">Email Client</option>
                            </select>
                            <div style="position: absolute;right: 18px;top: 31px;"><i class="fa fa-angle-down"></i></div>
                        </div>

                        <div class="col-md-12">
                           <span id="wybierz1">
                            <select id="defaultRecipientsList" class="form-control" multiple="multiple" name="id[]">

                                @if(@$receiverDetail)
                                    @foreach(@$receiverDetail as $key=>$value)
                                        <optgroup  label="{{$key}}">
                                            @foreach(@$value as $keys)
                                                <option value="{{$keys['id']}}" data-chained="{{$keys['type']}}">{{$keys['name']}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                           </span>

                            <span id="wybierz2">
                            <select id="defaultEmailRecipientsList" class="form-control" multiple="multiple" name="emailId[]">
                                @if(@$emailRecepientsDetail)
                                    @foreach(@$emailRecepientsDetail as $key=>$value)
                                        <optgroup label="{{$key}}">
                                            @foreach($value as $keys)
                                                <option value="{{$keys['id']}}" data-chained="{{$keys['type']}}">{{$keys['name']}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endif
                            </select>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" >Submit</button>
                </div>

                {{ Form::close() }}

            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteDefaultRecipient" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/deleteDefaultRecipient' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Default Recipient</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="defaulttemplateid" name="template_id">
                            <input type="hidden" id="defaultusertype" name="user_type">
                            <input type="hidden" id="defaulttype" name="type">
                            <input type="hidden" id="defaultrecipientid" name="recipient_id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to remove this Default Recipient?</p>
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


    <div class="modal fade " id="tallyable" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Tallyable</h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <input type="hidden" id="talleyUnitRateValue" value="1">
                        <div class="col-md-12">
                            <p> <i class="fa fa-exclamation-circle"></i>  Do you have Unit Rate or one of $ Value?</p>
                        </div>
                        <div class="col-md-12">

                            <div class="col-md-6">
                                <label style="font-size: 14px;" >$ Value</label>
                                <input type="checkbox" id="talleyValue"  checked disabled value="1">
                            </div>

                            <div class="col-md-6">
                                <label style="font-size: 14px;" >Unit Rate</label>
                                <input type="checkbox" id="talleyUnitRate"  value="2" >
                            </div>

                        </div>



                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveTalleyable">Save</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="setFormula" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="    min-height: 377px;">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Set Formula</h4>
                </div>
                <input type="hidden" class="docketfieldidFormulasection">

                <span class="spinnergridformula" style="font-size: 31px; display:none;     position: absolute;left: 50%;top: 49%;"><i class="fa fa-spinner fa-spin"></i></span>


                <div class="formulaView">


                </div>

            </div>
        </div>
    </div>

    <!--start Newone -->
    <div class="modal fade" id="setgridPrefiller" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document" style="width:70%">
            <div class="modal-content" style="height:547px">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Set Grid Prefiller</h4>
                </div>

                <div class="gridPrefillerShow ">
                    <span style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 50%; "><i class="fa fa-spinner fa-spin"></i></span>

                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="addGridPrefillerModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add grid prefiller </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden"  id="parentDocketFieldId">
                    <input type="hidden"  id="parentGridId">
                    <input type="hidden" id="autoFiled">
                    <input type="hidden" id="gridprefillerindvintegertype" value="0">
                    <div class="row">
                        {{--                        <div class="col-md-12">--}}
                        {{--                            <div style="margin:28px 0px -17px 12px;">--}}
                        {{--                                <input style="    float: left;margin-right: 12px;" type="checkbox" class="gridsprefillerLinkChecksingle"  value="1">--}}
                        {{--                                <p style="float: left"> Check to link Prefiller </p>--}}
                        {{--                                <span class="spinnerCheckss" style="padding:0 0px 0px 165px;font-size: 14px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        <div class="clearfix"></div>
                        <div class="parentData">
                            <div class="gridsappenddatabytypes"></div>
                            <div class="clearfix"></div>
                            <div class="gridsappendvaluetypes">
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="50" class="form-control gridprefillerInvValue">
                                        <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveGridParentPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addGridPrefillerValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add child grid prefiller </h4>
                </div>
                <div class="modal-body">
                    <input type="hidden"  id="gridprefillerindvsentdocketvalueid">
                    <input type="hidden"  id="gridprefillerindvsentdocketfieldid">
                    <input type="hidden"  id="gridprefillerindvsentdocketprefillerid">
                    <input type="hidden" id="gridprefillerindvindex">
                    <input type="hidden" id="gridprefillerindvintegertype" >


                    <div class="row">

                        {{--                        <div class="col-md-12">--}}
                        {{--                            <div style="margin:28px 0px -17px 12px;">--}}
                        {{--                                <input style="    float: left;margin-right: 12px;" type="checkbox" class="gridsprefillerLinkChecksingle"  value="1">--}}
                        {{--                                <p style="float: left"> Check to link Prefiller </p>--}}
                        {{--                                <span class="spinnerCheckss" style="padding:0 0px 0px 165px;font-size: 14px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}

                        <div class="clearfix"></div>
                        <div class="gridchildData">
                            <div class="gridsappenddatabytypes"></div>

                            <div class="clearfix"></div>

                            <div class="gridsappendvaluetypes">
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="50" class="form-control gridprefillerInvValue">
                                        <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="gridaddIndPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clearAllGridPrefillerModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document" style="margin-top: 17%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Clear Grid Prefiller</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="clearGridFieldId">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to clear Grid Prefiller?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="clearAllGridPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addPrefillerManagerValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style=" z-index: 11111; display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Child label </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="childData">
                            <div class="gridsappendvaluetypes">
                                <input type="hidden" class="parentManagerId" >
                                <input type="hidden" class="prefillerIndexId">
                                <input type="hidden" class="prefillerRootId">
                                <input type="hidden" class="prefillergrid_field_id" >
                                <input type="hidden" class="prefillerisDependentData">
                                <input type="hidden" class="prefillergrid_docket_field_id">
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="50" class="form-control gridprefillerInvValue">
                                        <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveChildPrefiller" >Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="setPrefiller" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document" style="width:70%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Set Prefiller</h4>
                </div>

                <div class="prefillerShow">


                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="addNewParentPrefillermodel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Parent prefiller </h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="clearfix"></div>
                        <div class="prefillerParentData">
                            <input type="hidden" id="prefilleris_dependent_data" value="">
                            <input type="hidden" id="prefillerfield_id" value="">
                            <input type="hidden" id="prefillerdocket_id" value="">
                            <p class="prefillerErrorMessage" style="display: none;"></p>

                            <div class="col-md-1">
                                <div class="form-group float-left">
                                    <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                </div>
                            </div>
                            <div class="col-md-11">
                                <div style="    margin-top: 15px;" class="form-group">
                                    <input  type="text"  name="value" maxlength="50" class="form-control newValue">
                                    <h5 style="color: #757575;"><b class="messageForPrefiller">Maximum 50 characters </b></h5>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="addNewParentPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateNewPrefillerneValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Child prefiller </h4>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="clearfix"></div>
                        <div class="prefillerChildData">
                            <input type="hidden" id="childprefilleris_dependent_data" value="">
                            <input type="hidden" id="childprefillerfield_id" value="">
                            <input type="hidden" id="childprefillerdocket_id" value="">
                            <input type="hidden" id="childprefillerroot" value="">
                            <input type="hidden" id="childprefillerindex" value="">
                            <p class="prefillerErrorMessage" style="display: none;"></p>
                            <div class="col-md-1">
                                <div class="form-group float-left">
                                    <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                </div>
                            </div>
                            <div class="col-md-11">
                                <div style="    margin-top: 15px;" class="form-group">
                                    <input  type="text"  name="value" maxlength="50" class="form-control newValue">
                                    <h5 style="color: #757575;"><b class="messageForPrefiller">Maximum 50 characters </b></h5>
                                </div>
                            </div>

                        </div>
                        <div class="clearfix"></div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="addNewChildPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addChidPrefillerManager" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style=" z-index: 11111; display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Child label </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="managerChildData">
                            <div class="gridsappendvaluetypes">
                                <input type="hidden" class="parentManagerId" >
                                <input type="hidden" class="prefillerIndexId">
                                <input type="hidden" class="prefillerRootId">
                                <input type="hidden" class="prefiller_field_id">
                                <input type="hidden" class="isDependentData">
                                <p class="prefillerErrorMessage" style="display: none;"></p>
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="50" class="form-control gridprefillerInvValue">
                                        <h5 style="color: #757575;"><b class="messageForPrefiller">Maximum 50 characters </b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveChildManagerPrefiller" >Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <!--end Newone -->








    <div class="modal fade" id="duplicateGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Clone Docket Field</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="duplicate_docket_id">
                            <input type="hidden" id="duplicate_category_id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to clone this field?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveDuplicateGrid">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exportMapping" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Export Mapping </h4>
                </div>
                <div class="modal-body">
                    <span class="spinnerCheck" style="font-size: 41px;position: absolute;display: none;z-index: 1;left: 50%;top: 50%;"><i class="fa fa-spinner fa-spin"></i></span>
                    <div class="exportMappingView">

                    </div>


                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade " id="docketConstant" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Docket Constant </h4>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p> <i class="fa fa-exclamation-circle"></i> Select Docket Constant</p>
                        </div>
                        <div class="col-md-12">
                            <select class="form-control constantDocketValue">
                                <option value="1">Docket Number</option>
                                <option value="3">From User</option>
                                <option value="2">To User</option>
                                <option value="4">Blank</option>
                                <option value="5">Template Title</option>
                                <option value="6">From ABN</option>
                                <option value="7">To ABN</option>
                                <option value="8">From Company</option>
                                <option value="9">To Company</option>
                                <option value="10">Line Number</option>
                                <option value="11">Fixed Value</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveDocketConstant">Save</button>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="undoDocketField" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Restore Docket Field</h4>
                </div>
                <input type="hidden" class="undofieldId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to restore this Docket Field?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary"  id="submitUndoData" >Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="updateAprovalMethod" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/updateAprovalMethod' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Approval Method</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <br><br><br>
                        <div style="    padding-left: 37px;margin-top: -45px;" class="col-md-12">
                            <input type="hidden" name="docket_approval_type" id="docketApprovalValue" value="0">
                            <input type="hidden" name="docket_id"  value="{{$tempDocket->id}}">

                            <div class="option1">
                                <div class="row">
                                    <div class="col-md-7">
                                        <h5>Option 1: &nbsp<span style="font-weight: 400;">Default Approval with button</span></h5>
                                        <input type="checkbox"  style="float: left;margin: 3px 0px 0px 3px;" id="buttonApprovess" @if($tempDocket->docketApprovalType == 0) checked disabled  @endif  value="{{$tempDocket->docketApprovalType}}">
                                        <label style="padding: 0px 0px 0px 28px;font-size: 15px;     background-image: none;" class="form-control" for="buttonApprovess">Customers and Employees can approve dockets by clicking "Approve"
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
                                        <input style="float: left;margin: 3px 0px 0px 3px;" type="checkbox" id="buttonAuthorisess" @if($tempDocket->docketApprovalType == 1) checked disabled  @endif  value="{{$tempDocket->docketApprovalType}}">
                                        <label style="padding: 0px 0px 0px 28px;font-size: 15px;     background-image: none;" class="form-control" for="buttonAuthorisess">Customers and Employees are required to "Sign" the dockets to
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
                                        <input style="float: left;margin: 3px 0px 0px 3px;" type="checkbox" id="buttonNoApprovess" @if($tempDocket->docketApprovalType == 2) checked disabled  @endif  value="{{$tempDocket->docketApprovalType}}">
                                        <label style="padding: 0px 0px 0px 28px;font-size: 15px;     background-image: none;"  class="form-control" for="buttonNoApprovess">Customers and Employees are not required to "Sign" the dockets to
                                            authorise approval.</label>
                                    </div>
                                    <div class="col-md-5">
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="clearExportRule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document" style="margin-top: 17%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>Clear Export Rules</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to clear the export rules? This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="clearAllExportRule">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="gridManualTimeFormatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document" style="margin-top: 17%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>Time Format</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 girdTimeFormat">
                            <input type="hidden" class="timeFormatGridFieldId" >
                            <input type="hidden" class="timeFormatFieldId">
                            <strong>Time Format</strong><br><hr>
                            <input type="checkbox" name="timeformat" value="Hours&Minutes" onClick="checkTimeFormat(this)"> <span>Hours & minutes</span><br><br>
                            <input type="checkbox" name="timeformat" value="Decimal" onClick="checkTimeFormat(this)"> <span>Decimal</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updateGridManualTimeFormat">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="manualTimeFormatModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document" style="margin-top: 17%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>Time Format</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 timeFormat">
                            <input type="hidden" class="timeFormatFieldId" >
                            <strong>Time Format</strong><br><hr>
                            <input type="checkbox" name="normaltimeformat" value="Hours&Minutes" onClick="checkManualTimeFormat(this)"> <span>Hours & minutes</span><br><br>
                            <input type="checkbox" name="normaltimeformat" value="Decimal" onClick="checkManualTimeFormat(this)"> <span>Decimal</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updateManualTimeFormat">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="defaultFolderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document" style="margin-top: 17%">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>Default Folder</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <span class="folderSpinnerCheck" style="font-size: 40px;display: none;position: absolute;left: 50%;top: 50%; display:none;"><i class="fa fa-spinner fa-spin"></i></span>
                        <div class="col-md-12 defaultFolderView">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="updateDefaultFolder">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('customScript')
    <style>

        .popover-title{
            background: #2570ba;
            color: #ffffff;
        }
        .popover-content{
            color: #000000;
        }
        .popover.top {
            margin-top: -3px;
        }
        .btn:not(.btn-raised):not(.btn-link):hover{
            background-color: rgb(153 153 153 / 0%);
        }

        .delete{
            color: gray;
            display: none;
        }
        .defaultlistrecipient:hover .delete {
            display:block;
            float: right;
            margin-right: 15px;
            font-size: 11px;
            border-radius: 0 16px 16px 0;
            text-decoration: none;

        }
        .defaultlistrecipient:hover{
            background: #e2e0e0;
            border-radius: 0 16px 16px 0;
            text-decoration: none;
            color: #4c4949;
        }
        .ui-tooltip-content{
            display: none;
        }
        .designDocket .btn-group .multiselect{
            border-left: none;
            border-right: none;
            border-top: none;
        }
        .formElement{
            margin-top: 6px;
            padding-top: 20px;
            margin-right: -23px;
            padding-left: 24px;
            background: #f2f2f2;
            min-height: 76px;
            margin-left: -23px;
            margin-bottom: 26px;
        }
        .mobileviewCheckbox .checkbox-inline .checkbox-material .check{
            border-radius: 20px;
            float: left;
            margin-right: 9px;
            height: 17px;
            width: 17px;
        }
        .mobileviewCheckbox{
            margin-bottom: 4px;
        }

        .designDocketLeftSideView{
            margin: 0px 0px 30px 0px;
        }

        .designDocketLeftSideView .leftSideHeader h3{
            font-size: 15px;
            font-weight: 500;
            color: #ffffff;
            padding: 14px 0px 14px 18px;
            margin: 0;

        }
        .designDocketLeftSideView .leftSideHeader{
            background-color: #15B1B8;
            border-radius: 5px 5px 0px 0px;
        }
        .designDocketLeftSideView .leftSideBody {
            background-color: #F8F9FB;
            min-height: 50px;
            padding: 10px 0 10px 0px;
        }

        .designDocketLeftSideView .leftSideHeader h3 a{
            color: #ffffff;
            font-size: 14px;
            padding-right: 15px;
        }
        .designDocketLeftSideView .leftSideBody  hr{
            width: 92%;

        }
        .designDocketLeftSideView .leftSideBody  strong{
            margin-left: 21px;

        }

        .themeSecondaryBgs{
            background-color: #E8E8E8 !important;
            color: #333 !important;
            border: 1px solid #D9D9D9;
            border-radius: 2px;
        }

        .prefillerEmptyView{
            color: #adacac;
            text-align: center;

        }
        .prefilerLine{
            border-top: 1px solid #d7d7d8;
            margin-left: -15px;
            margin-top: 0px;
            margin-bottom: 5px;
        }


        .prefillercontent .editabledocketprefiller:after {
            display: none;
        }

        .prefillercontent .editabledocketprefiller {
            font-size: 12px;
        }
        .footerButton{
            position: absolute;
            right: 31px;
            bottom: 18px;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap-tour.min.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>
    <script src="{{asset('assets/dashboard/tour/bootstrap-tour.js')}}"></script>
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script src="{{ asset('assets/dashboard/js/docket/designDocket.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript">
        var appURL = "{{ url('/') }}/";
        $(document).ready(function(){
            $('#framework').multiselect({
                enableselectUrling: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%'
            });



        });

        $(document).ready(function(){
            $('[data-toggle="popover"]').popover({
                placement : 'top',
                trigger : 'hover'
            });
        });


    </script>



    <script>
        function changeEventHandler(event) {
            var number_of_column = event.target.value;
            $('.gridModalButton').addClass('disabled')
            getGrid(number_of_column);

        }

        function getGrid(number_of_column) {
            if (number_of_column != '') {
                $.ajax({
                    url: '{{ url('dashboard/company/docketBookManager/grid/table') }}'+'/'+ number_of_column ,
                    success: function(response)
                    {
                        jQuery('.grid_table').html(response);
                        $('.gridModalButton').removeClass('disabled')
                        $('.gridModalUpdateButton').removeClass('disabled')
                    }
                });
            }
        }

        // $(document).ready(function() {
        //     $('#prefillers').on('show.bs.modal', function(e) {
        //         var id = $(e.relatedTarget).data('id');
        //         var label = $(e.relatedTarget).data('label');
        //         var categoryId = $(e.relatedTarget).data('categoryid');
        //         $("#docket_field_id").val(id);
        //         $("#docket_field_label").val(label);
        //         if (categoryId == 3){
        //           $('#valueprefiller').attr('type','number')

        //         }else{
        //             $('#valueprefiller').attr('type','text')
        //         }
        //     });
        // });

        $(document).ready(function() {
            $('#prefillers').on('show.bs.modal', function(e) {
                $(this).find("input,textarea").val('').end()
                    .find("input[type=checkbox], input[type=radio]").prop("checked", "").end();
                $('.appendvaluetype').show();
                $(".appenddatabytype").hide();
                var id = $(e.relatedTarget).data('id');
                var label = $(e.relatedTarget).data('label');
                var categoryId = $(e.relatedTarget).data('categoryid');
                var prefillerType = $(e.relatedTarget).data('prefillertype');
                $.ajax({
                    type: "post",
                    data: {docket_field_id:id , 'is_integer':prefillerType},
                    url:"{{url('dashboard/company/checkParentPrefiller/') }}",
                    success:function (response) {
                        if (response.status == true){
                            $(".parentValue").html(response.finalSelectParentValue).show();
                        }
                    }
                });
                $("#docket_field_id").val(id);
                $("#docket_field_label").val(label);
                $("#prefillerTypecheck").val(prefillerType);
                if (prefillerType == 1){
                    $('#valueprefiller').attr('type','number')
                    $('#valueprefiller').attr({onkeydown : "return event.keyCode !== 69"})
                }else if (prefillerType == 0){
                    $('#valueprefiller').attr('type','text')
                }
            });
        });

        $(document).on('click','.prefillerLinkCheck',function () {
            $(".spinnerCheck").css('display','block')
            var is_integer =  $('#prefillerTypecheck').val();
            var checked = 0;

            if ($(this).is(':checked')){
                checked = 1;
            } else {
                checked = 0;
            }

            if (checked == 1){
                $('#valueprefiller').val('');
                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/prefillerCheckMark')}}",
                    data:{'is_integer':is_integer },
                    success: function (response) {
                        $(".spinnerCheck").css('display','none');
                        $(".appenddatabytype").html(response.finalView).show();
                        $('.appendvaluetype').hide()

                    }
                });
            }else if (checked == 0){
                $(".spinnerCheck").css('display','none');
                $('#valueprefiller').val('');
                $('.appendvaluetype').show()
                $(".appenddatabytype").hide()
            }
        });


        $(document).on('click','#deletePrefillerValue', function () {
            var prefiller =  $('#docketdelete_prefiler_id').val();
            var docketFieldId =  $('#docketdelete_prefiler_field_id').val();

            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/deletePreFiller')}}",
                data:{'prefiller_id':prefiller,'docket_field_id':docketFieldId , 'docket_id':'{{ $tempDocket->id }}'},
                success: function (response) {
                    if(response['status']==true) {
                        var wrapperId = "#prefillerValueWrapper"+$('#docketdelete_prefiler_field_id').val();
                        var wrappButton = ".prtefillerButtonSection"+$('#docketdelete_prefiler_field_id').val();
                        var prefillerData   =   "";
                        jQuery.each( response['finalPrefillerView'], function( i, val ) {
                            prefillerData = prefillerData + val['final'];
                        });

                        // console.log();
                        if (prefillerData == ""){
                            var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+$('#docketdelete_prefiler_field_id').val()+'">Empty</p>';
                            var button = " "
                        }else{
                            var finalView = '<table style="display: block;overflow-x: auto;white-space: nowrap;width: 709px;padding-bottom: 15px;">'+prefillerData+'</table>';
                        }

                        $(wrapperId).html(finalView);
                        $(wrappButton).html(button);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});
                        $('#deleteLabel').modal('hide');


                    }

                }

            });

            console.log(prefiller);

        })

        $(document).ready(function() {
            $('#linkPrefiller').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var label = $(e.relatedTarget).data('label');
                $("#dockets_field_id").val(id);
                $("#linkPrefiller_label").val(label);
            });
        });

        $(document).ready(function() {
            $('#gridModalUpdate').on('show.bs.modal', function(e) {
                $('.gridModalUpdateButton').addClass('disabled')
                var docket_field_id = $(e.relatedTarget).data('field_id');
                $("#grid_dockets_field_id").val(docket_field_id);
                $(".gridUpdatefield").val(1);
                var number_of_column = 1;
                getGrid(number_of_column);
            });
        });
    </script>
    <script src="{{asset('assets/dashboard/tour/scriptsecond.js')}}"></script>



    <link rel="stylesheet" type="text/css" href="{{ asset('assets/dashboard/css/bars.css') }}">
    <script src="{{ asset('assets/dashboard/js/bars.js') }}"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.15.0/slimselect.min.css" rel="stylesheet"></link>
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <!--<script  src="{{asset('assets/zepto-selector.chained.js')}}"></script>-->
    <!--<script  src="{{asset('assets/zepto.js')}}"></script>-->
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/jquery.steps.css') }}">
    <script src="{{asset('assets/dashboard/js/jquery.steps.min.js')}}"></script>
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>--}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>

{{--    <script src="{{asset('assets/scroll.js')}}"></script>--}}
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script>--}}

    {{--<!-- FLOT CHARTS -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.min.js') }}"></script>--}}
    {{--<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.resize.min.js') }}"></script>--}}
    {{--<!-- FLOT PIE PLUGIN - also used to draw donut charts -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.pie.min.js') }}"></script>--}}
    {{--<!-- FLOT CATEGORIES PLUGIN - Used to draw bar charts -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/flot/jquery.flot.categories.min.js') }}"></script>--}}




    <script>
        $(document).ready(function () {
            $("#wizard").steps({
                enableFinishButton:false,
            });
            $('#gridModal').on('shown.bs.modal', function () {
                //$('#myWizard').easyWizard();
                $('.gridModalButton').addClass('disabled')
                $(".gridCreatefield").val(1);
                var number_of_column = 1;
                getGrid(number_of_column);
            });


        });

    </script>
    <script type="text/javascript">
        $(function() {
            $('.editable').editable({
                success: function (response) {
                    $.ajax({
                        type: "GET",
                        url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                        success:function (response) {
                            $("#mobileviewHtml").html(response);
                        }
                    });
                },
                validate: function(value) {
                    if($.trim(value) == '') {
                        return 'The value field is required';
                    }
                }
            });
            $('.docketFieldNumbereditable').editable({});

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
                    // $('#addBookDialog').modal('show');
                    $("#addBookDialog").modal({
                        backdrop: 'static',
                    });
                    var dockettitle = $(this).attr("dockettitle");
                    var invoiceable = $(this).attr("invoiceable");
                    var docketId = $(this).attr("data");
                    $("#docket_id").val(docketId);
                    if (invoiceable==0){
                        $("#dockettitle").text('Are you sure you want to make '+dockettitle+' template invoiceable?');

                    }else {
                        $("#dockettitle").text('Are you sure you want to make '+dockettitle+' template non-invoiceable?');

                    }
                });

                $("#saveinvoiceableCheckboxInput").on("click", function () {
                    var templeteDocketId = document.getElementById("templeteDocketId").value;
//                    alert(docketId);
                    var checked = 0;
                    if ($("#invoiceableCheckboxInput").is(':checked')) {
                        checked = 1;
                    } else {
                        checked = 0;
                    }

                    $.ajax({
                        type: "POST",
                        url: '{{ url('dashboard/company/docketBookManager/designDocket/invoiceable/') }}',
                        data: {"invoiceable": checked, "docketId": templeteDocketId},
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
                    window.location.reload();
                });
                $("#noinvoiceableCheckboxInput").on("click", function () {
                    window.location.reload();
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
                        url: '{{ url('dashboard/company/docketBookManager/designDocket/docketInvoiceFiled/') }}',
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
                        url: '{{ url('dashboard/company/docketBookManager/designDocket/docketPreviewFiled/') }}',
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
                            url: "{{ url('dashboard/company/docketBookManager/designDocket/docketFieldUpdatePosition/'.$tempDocket->id) }}",
                            success: function (msg) {
                                $.ajax({
                                    type: "GET",
                                    url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                                    success:function (response) {
                                        $("#mobileviewHtml").html(response);
                                    }
                                });
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.docketComponent', function () {
                var filedType   =    $(this).attr('fieldtype');
                $.ajax({
                    type: "POST",
                    data: {fieldType: $(this).attr('fieldtype')},
                    url: "{{ url('dashboard/company/docketBookManager/designDocket/addDocketField/'.$tempDocket->id) }}",
                    success: function (response) {
                        $('.docketFieldNumbereditable').editable({});
                        document.getElementById('elementTemplateBottom').scrollIntoView({behavior: 'smooth'});
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                                $.material.init();
                                $(document).ready(function () {
                                    $( ".selectpicker" ).change(function() {
                                        var docketItemId = $(this).attr("yesnoSelectId");
                                        var labelTypeValue = $(this).val();
                                        $.ajax({
                                            type: "POST",
                                            data: {docket_field_id: docketItemId,label_type:labelTypeValue},
                                            url: "{{ url('dashboard/company/docketBookManager/updateLabelType/') }}",
                                            success: function (response) {
                                                if(response['status']==true) {
                                                    window.location.reload();
                                                }

                                            }
                                        });



                                    });

                                });
                                var colorPicker = $('.colorpicker').colorpicker({
                                    colorSelectors: {
                                        'black': '#000000',
                                        'red': '#FF0000',
                                        'default': '#777777',
                                        'primary': '#337ab7',
                                        'success': '#5cb85c',
                                        'info': '#5bc0de',
                                        'warning': '#f0ad4e',
                                        'danger': '#d9534f'
                                    },

                                });
                                $('.colorpicker-hue').css('display','none');
                                $('.colorpicker-saturation').css('display','none');
                                $('.colorpicker-alpha').css('display','none');
                                $('.colorpicker-color').css('display','none');
                                $("#cp10").colorpicker('disable')
                                $(".collourPallet").bind("change", function () {
                                    $.ajax({
                                        type: "POST",
                                        data: {id: $(this).attr('colorYesNoId'), colour: $(this).val()},
                                        url: "{{ url('dashboard/company/docketBookManager/UpdateSubDocketColour/') }}",
                                        success: function (response) {
                                            if (response['status'] == true) {
                                                $('.iconBackground' + response['id']).css('background', response['colour'])
                                            }

                                        }
                                    });

                                    var el = $('.colorpicker-with-alpha');
                                    el.addClass('colorpicker-hidden');
                                    el.removeClass('colorpicker-visible');
                                });



                            }
                        });
                        if (response == "Invalid attempt! Only one 'Footer field' can be added per template") {
                            alert(response);
                        } else {

                            if (filedType == 13) {
                                $.when($('#sortableFooter').append(response)).done(function () {
                                    $('.editable').editable(
                                        {
                                            success: function (response) {
                                                $.ajax({
                                                    type: "GET",
                                                    url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                                                    success:function (response) {
                                                        $("#mobileviewHtml").html(response);
                                                    }
                                                });
                                            },
                                            validate: function(value) {
                                                if($.trim(value) == '') {
                                                    return 'The value field is required';
                                                }
                                            }
                                        });
                                });
                            }
                            else {
                                $.when($('#sortable').append(response)).done(function () {
                                    $('.editable').editable(
                                        {
                                            success: function (response) {
                                                $.ajax({
                                                    type: "GET",
                                                    url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                                                    success:function (response) {
                                                        $("#mobileviewHtml").html(response);
                                                    }
                                                });
                                            },
                                            validate: function(value) {
                                                if($.trim(value) == '') {
                                                    return 'The value field is required';
                                                }
                                            }

                                        });
                                    $('.docketFieldNumbereditable').editable({});

                                });
                            }

                        }

                        if($('#sortable .docketField').length <= 6){
                            $('.footerButton').css('display','none')
                        }else{
                            $('.footerButton').css('display','block')

                        }

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


            $(".docketfieldrequired").on("click", function () {
                var requiredDocketFieldId = $(this).attr("data");
                var checked = 0;
                if ($(this).is(':checked')) {
                    checked = 1;
                } else {
                    checked = 0;
                }

                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/docketBookManager/designDocket/docketRequiredField/') }}',
                    data: {"data": checked, "requiredDocketFieldId": requiredDocketFieldId},
                    success: function (msg) {
                        if (msg == "Invalid attempt!") {
                            alert(msg);
                        }
                    }
                });
            });

            $(".docketSendCopy").on("click", function () {
                var requiredDocketFieldId = $(this).attr("data");
                var checked = 0;
                if ($(this).is(':checked')) {
                    checked = 1;
                } else {
                    checked = 0;
                }

                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/docketBookManager/designDocket/docketSendCopy/') }}',
                    data: {"data": checked, "requiredDocketFieldId": requiredDocketFieldId},
                    success: function (msg) {
                        if (msg == "Invalid attempt!") {
                            alert(msg);
                        }
                    }
                });
            });

            $(".docketIsEmailSubject").on("click", function () {
                var isEmailSubjectdDocketFieldId = $(this).attr("data");
                var checked = 0;
                if ($(this).is(':checked')) {
                    checked = 1;
                } else {
                    checked = 0;
                }

                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/docketBookManager/designDocket/isEmailSubjectdDocketFieldId/') }}',
                    data: {"data": checked, "requiredDocketFieldId": isEmailSubjectdDocketFieldId},
                    success: function (msg) {
                        if (msg == "Invalid attempt!") {
                            alert(msg);
                        }
                    }
                });
            });

            $(document).on('click', '.deleteInvoiceComponent', function(){
                var parentDiv   =   $("#activeTr");
                $.ajax({
                    type: "POST",
                    data: {fieldId:$(this).attr('fieldId'),'isShow':window.location.search.split('=')[1]},
                    url: "{{ url('dashboard/company/docketBookManager/designDocket/deleteDocketField/'.$tempDocket->id) }}",
                    success: function(response){
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                            }
                        });
                        if(response == "Cannot remove this field. This field is required to capture your employees timesheet/hours worked."){
                            alert(response);

                        }else{
                            // alert(response);
                            $('.componentScroll').html(response)
                            $('.docketFieldNumbereditable').editable({});
                            $('.editable').editable({});
                            $('.editableExport').editable({
                                mode:"inline"
                            })
                            $('.editabledocketprefiller').editable({
                                mode:"inline"
                            });
                            $('.editabledocketgridprefiller').editable({
                                mode:"inline"
                            });

                        }

                        if($('#sortable .docketField').length <= 6){
                            $('.footerButton').css('display','none')
                        }else{
                            $('.footerButton').css('display','block')

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
            $(document).on('click', '.deleteGridColumn', function() {
                $('#deleteGridColumn').modal('show');
                var fieldId = $(this).data('id');
                $('#grid_column_id_column').val(fieldId);
                //console.log(fieldId);

                /*var parentDiv   =   $(this).parents('.docketField');
                parentDiv.attr('id',"activeTr");

                id = $(this).attr('data-id');
                $('#invoice_field_id').attr("fieldId",id);*/
            });

            $(document).on('click', '.deleteGridColumnButton', function () {
                var column_id = $('#grid_column_id_column').val();
                $.ajax({
                    type: "POST",
                    url: '{{ route('grid.column.delete') }}',
                    data: {"column_id": column_id},
                    success: function (response) {

                        //location.reload();
                        if (response.status == true) {
                            location.reload();
                        }else{
                            $('#deleteGridColumn').modal('hide');
                            alert(response.message);
                        }
                    }
                });
            });




        });
    </script>

    <script>

        //          $(".multiple").on('change',function() {
        function updateAttachment(id,value) {
            $.ajax({
                type: "POST",
                data: {fieldId: id, dataId: value},
                url: "{{ url('/dashboard/company/docketBookManager/designDocket/addDocument') }}",
                success: function (response) {
                    if (response == "Invalid attempt! Already Added") {
                        alert(response);
                    } else {
                        window.location.reload();
                    }
                }

            });
        }

        //          });

        //          new SlimSelect({
        //              select: '#single',
        //              allowDeselect: true
        //          })

    </script>

    <script>

        $(document).on('click', '.deleteDocumentAttached', function(){
            $.ajax({
                type: "POST",
                data: {fieldId:$(this).attr('fieldId')},
                url: "{{ url('/dashboard/company/docketBookManager/designDocket/deleteDesigneDocumentAttached') }}",
                success: function(response){
//                    if (response == "Invalid attempt! Already Delete") {
//                        alert(response);
//                    } else {
//                        window.location.reload();
//                    }
                    window.location.reload();
                }

            });

        });
    </script>

    <script>
        $(document).ready(function() {
            $('.editabledocketprefiller').editable({
                mode:"inline",
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
    {{--<script>--}}
    {{--$(document).on('change', '#selectTerms', function(){--}}
    {{--var fieldId   =    $(this).attr('fieldid');--}}
    {{--var docketId  = $(this).attr('docketid');--}}
    {{--var values = $(this).val();--}}
    {{--document.getElementById("textareaTerms").value = values;--}}

    {{--});--}}
    {{--</script>--}}
    <script>
        $(document).on('click', '#addNew', function () {
            $.ajax({
                type: "POST",
                url: "{{ url('dashboard/company/docketBookManager/designDocket/saveDocketFieldFooter') }}", //process to mail
                data: $('#saveDocketFieldFooter').serialize(),
                success: function (msg) {
                    window.location.replace("{{ url('dashboard/company/docketBookManager/template') }}");
                },
            });
        });


    </script>
    <script>
        $(document).on('click', '#addNew1', function () {
            $.ajax({
                type: "POST",
                url: "{{ url('dashboard/company/docketBookManager/designDocket/saveDocketFieldFooter') }}", //process to mail
                data: $('#saveDocketFieldFooter').serialize(),
                success: function (msg) {
//                    window.location.replace("/dashboard/company/docketBookManager/template");
                },
            });
        });


    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteLabel').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var docket_field_id = $(e.relatedTarget).data('docketfieldid');
                $("#docketdelete_prefiler_id").val(id);
                $("#docketdelete_prefiler_field_id").val(docket_field_id);

            });
        });

        $(document).ready(function() {
            $('#addPrefillerValue').on('show.bs.modal', function(e) {
                $('.prefillerLinkChecksingle').prop("checked", "")
                $('#prefillerInvValue').val('');
                $('.appendvaluetypes').show();
                $(".appenddatabytypes").hide();
                var docket_id = $(e.relatedTarget).data('docket_id');
                var docketfieldid = $(e.relatedTarget).data('docketfieldid');
                var prefillerId = $(e.relatedTarget).data('id');
                var index = $(e.relatedTarget).data('index');
                var label = $(e.relatedTarget).data('labels');
                var prefillerType = $(e.relatedTarget).data('prefillertype');
                $("#prefillerindvsentdocketvalueid").val(docket_id);
                $("#prefillerindvsentdocketfieldid").val(docketfieldid);
                $("#prefillerindvsentdocketprefillerid").val(prefillerId);
                $("#prefillerindvindex").val(index);
                $("#prefillerindvintegertype").val(prefillerType);


                $("#indvDocName").val(label)
                if (prefillerType == 1){
                    $('#prefillerInvValue').attr('type','number')
                    $('#prefillerInvValue').attr({onkeydown : "return event.keyCode !== 69"})
                }else if (prefillerType == 0){
                    $('#prefillerInvValue').attr('type','text')
                }

            });
        });


        $('#addIndPrefiller').click(function () {
            var docket_id =   $("#prefillerindvsentdocketvalueid").val();
            var docketfieldid =  $("#prefillerindvsentdocketfieldid").val();
            var prefillerId = $("#prefillerindvsentdocketprefillerid").val();
            var prefillerValue = $("#prefillerInvValue").val();
            var valueCategoryId = $("#typeValueSingle").val();
            var index = $("#prefillerindvindex").val();
            $.ajax({
                type: "POST",
                url: "{{ url('dashboard/company/docketBookManager/docket/designDocket/addIndPrefiller') }}",
                data: {docket_id: docket_id,docketfieldid:docketfieldid,prefillerId:prefillerId,prefillerValue:prefillerValue,value_category_id:valueCategoryId,index:index},
                success: function (response) {
                    $('.dashboardFlashsuccess').css('display','none');
                    $('.dashboardFlashdanger').css('display','none');
                    if(response['status']==true) {
                        var wrapperId = "#prefillerValueWrapper"+$("#prefillerindvsentdocketfieldid").val();


                        var wrappButton = ".prtefillerButtonSection"+$("#prefillerindvsentdocketfieldid").val();

                        // var prefillerData = [];
                        // console.log(response['finalPrefillerView']);
                        var prefillerData   =   "";
                        jQuery.each( response['finalPrefillerView'], function( i, val ) {

                            prefillerData = prefillerData + val['final'];
                        });

                        if (prefillerData == ""){
                            var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+$('#docketdelete_prefiler_field_id').val()+'">Empty</p>';

                        }else{
                            var finalView = '<table style="display: block;overflow-x: auto;white-space: nowrap;width: 709px;padding-bottom: 15px;">'+prefillerData+'</table>';
                            var button  = '<button type="button" class="btn btn-danger btn-xs btn-raised pull-right clickToHideprefiller showHideButton{{ $item->id }}" docketFeildIdForPrefiller="{{ $item->id }}" style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;">hide</button><a href="{{ url("dashboard/company/docketBookManager/designDocket/deleteAllPreFiller/".$item->id) }}" class="btn btn-danger btn-xs btn-raised pull-right"   style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;  text-transform: lowercase;"><i class="fa fa-minus"></i>&nbsp;Clear All</a>'
                        }

                        $(wrapperId).html(finalView);
                        $(wrappButton).html(button);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});
                        var wrappermessage = ".messagesucess";
                        $(wrappermessage).html(response["message"]);
                        $('.dashboardFlashsuccess').css('display','block');
                        $(".showHideButton"+$("#prefillerindvsentdocketfieldid").val()).removeClass('clickToshowprefiller').addClass('clickToHideprefiller');
                        $(".showHideButton"+$("#prefillerindvsentdocketfieldid").val()).html('hide');
                        $('#addPrefillerValue').modal('hide');

                    }else{
                        var wrappermessagedanger = ".messagedanger";
                        $(wrappermessagedanger).html(response["message"]);
                        $('.dashboardFlashdanger').css('display','block');
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});
                        $('#addPrefillerValue').modal('hide');
                    }

                }
            })
        });



        $(document).on('click','.prefillerLinkChecksingle',function () {

            $(".spinnerCheck").css('display','block');
            var is_integer =  $('#prefillerindvintegertype').val();
            var checked = 0;
            if ($(this).is(':checked')){
                checked = 1;
            } else {
                checked = 0;
            }

            if (checked == 1){
                $('#prefillerInvValue').val('');
                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/prefillerCheckMarkSingle')}}",
                    data:{'is_integer':is_integer },
                    success: function (response) {
                        $(".spinnerCheck").css('display','none');
                        $(".appenddatabytypes").html(response.finalView).show();
                        $('.appendvaluetypes').hide()

                    }
                });
            }else if (checked == 0){
                $(".spinnerCheck").css('display','none');
                $('#prefillerInvValue').val('');
                $('.appendvaluetypes').show()
                $(".appenddatabytypes").hide()
            }

        });


        $(".docketGridFieldIsHidden").on("click", function () {
            var requiredDocketGridFieldId = $(this).attr("data");
            var requiredDocketFieldId = $(this).attr("data-docketfieldid");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/docketGridFieldIsHidden/') }}',
                data: {"data": checked, "requiredDocketFieldId": requiredDocketFieldId,'requiredDocketGridFieldId':requiredDocketGridFieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });



    </script>

    <script>
        // $('#saveprefillersdesign').click(function () {
        //     var  prefillerlabels = $("#valueprefiller").val();
        //     var  saveprefillerdocketprefillerid = $("#docket_field_id").val();
        //     $.ajax({
        //         type:"post",
        //         url:"{{url('dashboard/company/docketBookManager/docket/designDocket/saveprefiller')}}",
        //         data:{docket_field_id:saveprefillerdocketprefillerid,value:prefillerlabels},
        //         success:function (response) {
        //             $('.dashboardFlashsuccess').css('display','none');
        //             $('.dashboardFlashdanger').css('display','none');
        //             if(response['status']==true) {
        //                 var wrapperId = "#prefillerValueWrapper"+$("#docket_field_id").val();
        //                 $(wrapperId).append(response["label"]);
        //                 $('.editable').editable();
        //                 $('.docketFieldNumbereditable').editable({});
        //                 var wrappermessage = ".messagesucess";
        //                 $(wrappermessage).html(response["message"]);
        //                 $('.dashboardFlashsuccess').css('display','block');

        //             }else{
        //                 var wrappermessagedanger = ".messagedanger";
        //                 $(wrappermessagedanger).html(response["message"]);
        //                 $('.dashboardFlashdanger').css('display','block');

        //             }

        //             $('#prefillers').modal('hide');
        //             $('[name="value"]').val('');

        //         }


        //     });

        // });

        $('#saveprefillersdesign').click(function () {
            var  prefillerlabels = $("#valueprefiller").val();
            var  saveprefillerdocketprefillerid = $("#docket_field_id").val();
            // var parentId = $("#parentid").val();
            var parentId = 0;
            var valueCategoryId = $("#typeValue").val();
            var isInteger = $('#prefillerTypecheck').val();
            // var index = $("#parentid option:selected").attr('dataindex');
            var index = 0;
            console.log(index);
            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketBookManager/docket/designDocket/saveprefiller')}}",
                data:{docket_field_id:saveprefillerdocketprefillerid,value:prefillerlabels, parent_id : parentId ,value_category_id: valueCategoryId,isInteger:isInteger,index:index, docket_id:'{{ $tempDocket->id }}'},
                success:function (response) {
                    $('.dashboardFlashsuccess').css('display','none');
                    $('.dashboardFlashdanger').css('display','none');
                    if(response['status']==true) {
                        var wrapperId = "#prefillerValueWrapper"+$("#docket_field_id").val();
                        var wrappButton = ".prtefillerButtonSection"+$("#docket_field_id").val();

                        // var prefillerData = [];
                        // console.log(response['finalPrefillerView']);
                        var prefillerData   =   "";
                        jQuery.each( response['finalPrefillerView'], function( i, val ) {

                            prefillerData = prefillerData + val['final'];
                        });

                        if (prefillerData == ""){
                            var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+$('#docketdelete_prefiler_field_id').val()+'">Empty</p>';

                        }else{
                            var finalView = '<table style="display: block;overflow-x: auto;white-space: nowrap;width: 709px;padding-bottom: 15px;">'+prefillerData+'</table>';
                            var button  = '<button type="button" class="btn btn-danger btn-xs btn-raised pull-right clickToHideprefiller showHideButton{{ $item->id }}" docketFeildIdForPrefiller="{{ $item->id }}" style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;">hide</button><a href="{{ url("dashboard/company/docketBookManager/designDocket/deleteAllPreFiller/".$item->id) }}" class="btn btn-danger btn-xs btn-raised pull-right"   style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;  text-transform: lowercase;"><i class="fa fa-minus"></i>&nbsp;Clear All</a>'
                        }
                        $(wrapperId).html(finalView);
                        $(wrappButton).html(button);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});
                        var wrappermessage = ".messagesucess";
                        $(wrappermessage).html(response["message"]);
                        $('.dashboardFlashsuccess').css('display','block');
                        $(".showHideButton"+$("#docket_field_id").val()).removeClass('clickToshowprefiller').addClass('clickToHideprefiller');
                        $(".showHideButton"+$("#docket_field_id").val()).html('hide');
                        $('#prefillers').modal('hide');


                    }else{
                        var wrappermessagedanger = ".messagedanger";
                        $(wrappermessagedanger).html(response["message"]);
                        $('.dashboardFlashdanger').css('display','block');
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});
                        $('#prefillers').modal('hide');

                    }

                    $('[name="value"]').val('');

                }


            });

        });

    </script>

    <script>
        $(document).on('change', '#timerAttachementChecked', function () {
            $("#updateTimerAttachement").modal({
                backdrop: 'static',
            });
            var dockettitless = $(this).attr("dockettitles");
            var timer_attachements = $(this).attr("timer_attachement");
            var docketIdss = $(this).attr("datas");
            $("#docket_id").val(docketIdss);
            if (timer_attachements==0){
                $("#docket_titles").text('Are you sure you want to add the “Timer Attachment” feature for this template?');

            }else {
                $("#docket_titles").text('Are you sure you want to remove the “Timer Attachment” feature from this template?');

            }
        });
        $("#saveTimerAttachementChecked").on("click", function () {
            var templeteDocketId = document.getElementById("templeteDocketId").value;
            var checked = 0;
            if ($("#timerAttachementChecked").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/timerAttached/') }}',
                data: {"timer_attachement": checked, "docketId": templeteDocketId},
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
                    window.location.reload();

                }

            });

        });

        $("#saveTimerAttachementUnchecked").on("click", function () {
            window.location.reload();
        });
    </script>
    <script>
        $(document).on('click','.explanationClickEdit',function () {
            $('#subDocket').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
            $("#mobileviewHtmlSubDocket").html("");
            $(".spinerSubDocket").css("display", "block");
            var docketFieldIds = $(this).attr('data-fieldidsEdit');
            var yesNoFieldsId = $(this).attr('data-subDocketSelectedEdit');
            var explanationEdit= $(this).attr('data-explanationEdit');
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/yesNoExplanation') }}',
                data: {"docket_field_id": docketFieldIds, "id": yesNoFieldsId, "explanation":explanationEdit},
                success: function (response) {
                    $(".spinerSubDocket").css("display", "none");
                    $("#mobileviewHtmlSubDocket").html(response);
                    $('.editablesubdocket').editable({
                        placement: 'right'
                    });
                    $("#subdocketSorting").sortable({
                        stop: function (e, ui) {
                            if($('.tour-backdrop').length) {
                                tour.goTo(3);
                            }
                            var params = [];
                            $.map($("#subdocketSorting >div"), function (el) {
                                params[$(el).index()] = $(el).attr('subdocketingFieldId');
                            });
                            console.log(params);
                            $.ajax({
                                type: "POST",
                                data: {params: params ,yes_no_Subdocketing:yesNoFieldsId},
                                url: "{{ url('dashboard/company/docketBookManager/subDocketFieldUpdatePosition/') }}",
                                success: function (msg) {
                                }
                            });
                        }
                    });
                    $(document).on("change",'.SubDocketfieldrequired', function () {
                        var requiredSubDocketFieldId = $(this).attr("subDocketData");
                        $(".loadspin").css("display", "block");
                        var checked = 0;
                        if ($(this).is(':checked')) {
                            checked = 1;
                        } else {
                            checked = 0;
                        }
                        $.ajax({
                            type: "POST",
                            url: '{{ url('dashboard/company/docketBookManager/subDocketRequiredField/') }}',
                            data: {"data": checked, "requiredDocketFieldId": requiredSubDocketFieldId},
                            success: function (msg) {
                                $(".loadspin").css("display", "none");
                                if (msg == "Invalid attempt!") {
                                    alert(msg);
                                }
                            }
                        });
                    });
                }
            });

        });
        $(document).on('change', '.explanationClick', function () {
            if (this.checked) {
                $('#subDocket').modal({
                    backdrop: 'static',
                    keyboard: false,
                    show: true
                });
                $("#mobileviewHtmlSubDocket").html("");
                $(".spinerSubDocket").css("display", "block");
                var docketFieldIds = $(this).attr('data-fieldidss');
                var yesNoFieldsId = $(this).attr('data-subDocketSelected');

                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/docketBookManager/yesNoExplanation') }}',
                    data: {"docket_field_id": docketFieldIds, "id": yesNoFieldsId, "explanation": 1},
                    success: function (response) {
                        $(".spinerSubDocket").css("display", "none");
                        $("#mobileviewHtmlSubDocket").html(response);
                        $('.editablesubdocket').editable({
                            placement: 'right'
                        });

                        $("#subdocketSorting").sortable({
                            stop: function (e, ui) {
                                if($('.tour-backdrop').length) {
                                    tour.goTo(3);
                                }
                                var params = [];
                                $.map($("#subdocketSorting >div"), function (el) {
                                    params[$(el).index()] = $(el).attr('subdocketingFieldId');
                                });
                                console.log(params);
                                $.ajax({
                                    type: "POST",
                                    data: {params: params ,yes_no_Subdocketing:yesNoFieldsId},
                                    url: "{{ url('dashboard/company/docketBookManager/subDocketFieldUpdatePosition/') }}",
                                    success: function (msg) {

                                    }
                                });
                            }
                        });

                        $(document).on("change",'.SubDocketfieldrequired', function () {
                            var requiredSubDocketFieldId = $(this).attr("subDocketData");
                            $(".loadspin").css("display", "block");
                            var checked = 0;
                            if ($(this).is(':checked')) {
                                checked = 1;
                            } else {
                                checked = 0;
                            }

                            $.ajax({
                                type: "POST",
                                url: '{{ url('dashboard/company/docketBookManager/subDocketRequiredField/') }}',
                                data: {"data": checked, "requiredDocketFieldId": requiredSubDocketFieldId},
                                success: function (msg) {
                                    $(".loadspin").css("display", "none");
                                    if (msg == "Invalid attempt!") {
                                        alert(msg);
                                    }
                                }
                            });
                        });
                        $(".yesnoExplanationEdit"+yesNoFieldsId).css("display", "block");


                    }


                });
            } else {
                var docketFieldIdss = $(this).attr('data-fieldidss');
                var yesNoFieldsIds = $(this).attr('data-subDocketSelected');
                $(".yesnoExplanationEdit"+yesNoFieldsIds).css("display", "none");
                $.ajax({
                    type: "POST",
                    url: '{{ url('dashboard/company/docketBookManager/yesNoExplanationUncheck') }}',
                    data: {"docket_field_id": docketFieldIdss, "id": yesNoFieldsIds, "explanation": 0},
                    success: function (response) {
                    }
                });
            }

        });

        $(document).on('click', '.subDocketComponent', function () {
            var explanationFiledType = $(this).attr('explanationFieldType');
            var explanationrequired = $(this).attr('requires');
            var yesNOFieldId = $(this).attr('yesNOFieldId');
            $(".loadspin").css("display", "block");
            $.ajax({
                type: "POST",
                data: {
                    "fieldType": explanationFiledType,
                    "required": explanationrequired,
                    "yes_no_field_id": yesNOFieldId
                },
                url: "{{ url('dashboard/company/docketBookManager/addSubDocketField/') }}",
                success: function (response) {
                    $.when($('#subdocketSorting').append(response)).done(function () {
                        $('.editablesubdocket').editable({
                            placement: 'right'
                        });
                        $(".subdocketinghorizontalList:last").addClass("intro");
                        $( ".intro" ).animate({
                            backgroundColor: "#e3ebf5",
                        }, 1000 );
                        $("#subdocketSorting").sortable();
                        $(".loadspin").css("display", "none");
                        document.getElementById('bottom').scrollIntoView({behavior: 'smooth'});
                        $(".subdocketinghorizontalList").removeClass("intro");
                        $( ".subdocketinghorizontalList" ).animate({
                            backgroundColor: "#fff",
                        }, 1000 );
                    });

                }
            });
        });

        $(document).on('click', '.deleteSubDocketComponent', function () {
            var parentDiv = $(this).parents('.subdocketing');
            $(".loadspin").css("display", "block");
            $.ajax({
                type: "POST",
                data: {fieldId: $(this).attr('subDocketingfieldId') ,yesnoFieldId:$(this).attr('yesnofield')},
                url: "{{ url('dashboard/company/docketBookManager/deleteSubDocketField/') }}",
                success: function (response) {

                    if (response == "") {
                        $.when(parentDiv.fadeOut()).done(function () {
                            parentDiv.remove();
                            $( ".loadspin" ).fadeOut( "slow", function() {
                                // Animation complete.
                            });
//                            $(".loadspin").css("display", "none");
                        });
                    }else if (response.status == false) {
                        $(".loadspin").css("display", "none");
                        alert("Invalid action ! Please try with valid action.");

                    }
                }
            });

        });

        $(document).on('click','.closeSubdocket',function () {
            $(".spinerSubDocket").css("display", "block");
            setTimeout("$('#subDocket').modal('hide');",3000);
        });
        {{--$(".custom").spectrum({--}}
        {{--showPaletteOnly: true,--}}
        {{--showPalette:true,--}}
        {{--hideAfterPaletteSelect:true,--}}
        {{--color: 'blanchedalmond',--}}
        {{--palette: [--}}
        {{--['black', 'white', 'blanchedalmond',--}}
        {{--'rgb(255, 128, 0);', 'hsv 100 70 50'],--}}
        {{--['red', 'yellow', 'green', 'blue', 'violet']--}}
        {{--],--}}
        {{--change: function(color) {--}}
        {{--$.ajax({--}}
        {{--type: "POST",--}}
        {{--data: {id: $(this).attr('colorYesNoId'),colour:color.toHexString()},--}}
        {{--url: "{{ url('dashboard/company/docketBookManager/UpdateSubDocketColour/') }}",--}}
        {{--success: function (response) {--}}
        {{--if(response['status']==true) {--}}
        {{--//                            var colour = response['colour'];--}}
        {{--//                            var id = response['id']--}}


        {{--}--}}

        {{--}--}}
        {{--});--}}
        {{--}--}}
        {{--});--}}

    </script>

    <script>
        $(document).ready(function() {
            var colorPicker = $('.colorpicker').colorpicker({
                colorSelectors: {
                    'black': '#000000',
                    'red': '#FF0000',
                    'default': '#777777',
                    'primary': '#337ab7',
                    'success': '#5cb85c',
                    'info': '#5bc0de',
                    'warning': '#f0ad4e',
                    'danger': '#d9534f'
                },

            });
            $('.colorpicker-hue').css('display','none');
            $('.colorpicker-saturation').css('display','none');
            $('.colorpicker-alpha').css('display','none');
            $('.colorpicker-color').css('display','none');
            $("#cp10").colorpicker('disable')
            $(".collourPallet").bind("change", function() {

                $.ajax({
                    type: "POST",
                    data: {id: $(this).attr('colorYesNoId'),colour:$(this).val()},
                    url: "{{ url('dashboard/company/docketBookManager/UpdateSubDocketColour/') }}",
                    success: function (response) {
                        if(response['status']==true) {
                            $('.iconBackground'+response['id']).css('background',response['colour'])
                        }

                    }
                });
                var el = $('.colorpicker-with-alpha');
                el.addClass('colorpicker-hidden');
                el.removeClass('colorpicker-visible');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $( ".selectpicker" ).change(function() {
                var docketItemId = $(this).attr("yesnoSelectId");
                var labelTypeValue = $(this).val();
                $.ajax({
                    type: "POST",
                    data: {docket_field_id: docketItemId,label_type:labelTypeValue},
                    url: "{{ url('dashboard/company/docketBookManager/updateLabelType/') }}",
                    success: function (response) {
                        if(response['status']==true) {
                            window.location.reload();
                        }

                    }
                });



            });

        })
    </script>
    <script>
        $(document).on('click','.labelTypePop',function () {
            $('#labelTypePopUP').modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
            $("#mobileviewHtmlLabeltype").html("");
            $(".spinerSubDocket").css("display", "block");
            var popupyesnofieldids = $(this).attr('popupyesnofieldid');
            var labelpopupFieldids = $(this).attr('labelpopupFieldid');
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/yesNoIconImage') }}',
                data: {"docket_field_id": labelpopupFieldids, "id": popupyesnofieldids},
                success: function (response) {
                    $(".spinerSubDocket").css("display", "none");
                    $("#mobileviewHtmlLabeltype").html(response)
                }
            });
        });
        $(".expnanationType").on("click", function () {
            var expnanationTypeFieldId = $(this).attr("data");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/expnanationTypeFieldId/') }}',
                data: {"data": checked, "expnanationTypeFieldId": expnanationTypeFieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });
    </script>
    <script>
        $(document).on('click','#labelCheck',function () {
            var crossLabel = document.getElementById('labelCross');
            var naLabel = document.getElementById('labelNa');
            crossLabel.checked=false;
            naLabel.checked = false;
        });
        $(document).on('click','#labelCross',function () {
            var checkLabel1 = document.getElementById('labelCheck');
            var naLabel1 = document.getElementById('labelNa');
            checkLabel1.checked=false;
            naLabel1.checked = false;
        });
        $(document).on('click','#labelNa',function () {
            var checkLabel2 = document.getElementById('labelCheck');
            var crossLabel2 = document.getElementById('labelCross');
            checkLabel2.checked=false;
            crossLabel2.checked = false;
        });

        $(document).on("click",'.docketImageNamefieldrequired', function () {
            var docketImageNamefieldId = $(this).attr("data");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketImageNameFieldRequired') }}',
                data: {"value": checked, "requiredImageNameFieldId": docketImageNamefieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });

        $(document).on("click",'.timeRequired', function () {
            var docketDatefieldId = $(this).attr("data");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketDateTimeRequired') }}',
                data: {"time": checked, "docketDatefieldId": docketDatefieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });


        $(document).on('change','.defaultCheckMark',function () {
            var value = $(this).val();
            var docketFieldId = $(this).attr('items');
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/prefillerDefaultCheckMark')}}",
                data:{'value':value , 'docketFieldId':docketFieldId , 'docket_id':'{{ $tempDocket->id }}' ,'checked' :checked},
                success: function (response) {
                    if (response.status == true){
                        //   location.reload(true);
                    }else {

                    }
                }
            });

        });

        $(document).on("click",'.sumableValue', function () {
            var docfieldId = $(this).attr("docfieldId");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/updateDocketTotalStatus') }}',
                data: {"checked": checked, "docketfieldId": docfieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });

        $(document).on("click",'.gridSendDocket', function () {
            var docfieldId = $(this).attr("docfieldId");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/gridSendDocket') }}',
                data: {"checked": checked, "docketfieldId": docfieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });


    </script>
    <script>
        $("#defaultRecipientsList").chained("#recipientsType");

        $(document).ready(function(){
            $('#defaultRecipientsList').multiselect({
                enableFiltering: false,
                enableCaseInsensitiveFiltering: false,
                buttonWidth:'100%',
                enableClickableOptGroups: false,
                checkboxName: false,


            });

        });
        $(document).ready(function(){
            $('#defaultEmailRecipientsList').multiselect({
                enableFiltering: false,
                enableCaseInsensitiveFiltering: false,
                buttonWidth:'100%',
                enableClickableOptGroups: false,
                checkboxName: false,


            });

        });
        $(document).ready(function(){
            var type = $('#recipientsType').find(":selected").val();
            console.log(type);
            if (type==1){
                $('#wybierz2').hide();
            }

        });
        $(document).on('change','#recipientsType',function () {
            var type = $(this).find(":selected").val();
            if (type == 1){
                $('#wybierz2').hide();
                $('#wybierz1').show();

            }else if(type ==2){
                $('#wybierz1').hide();
                $('#wybierz2').show();
            }


        });

        $(document).ready(function() {

            $('#deleteDefaultRecipient').on('show.bs.modal', function(e) {
                var templateid = $(e.relatedTarget).data('templateid');
                var usertype = $(e.relatedTarget).data('usertype');
                var type = $(e.relatedTarget).data('type');
                var recipientid = $(e.relatedTarget).data('recipientid');
                $('#defaulttemplateid').val(templateid);
                $('#defaultusertype').val(usertype);
                $('#defaulttype').val(type);
                $('#defaultrecipientid').val(recipientid);


            });
        });

        $(function () {
            $( ".row_position" ).sortable({
                delay: 150,
                stop: function() {
                    var selectedData = new Array();
                    $('.row_position>th').each(function() {
                        selectedData.push($(this).attr("filed_id"));
                    });
                    console.log(selectedData);
                    updateOrder(selectedData);
                }
            });


            function updateOrder(data) {

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('grid.column.orderUpdate') }}",
                    data: {
                        order:data,
                        _token: '{{csrf_token()}}'
                    },
                    success: function (response) {
                        location.reload();
                    }
                });

            }
        });


        $(document).on('click','.clickToshowprefiller', function () {
            var prefiller_id =$(this).attr('docketFeildIdForPrefiller');
            var id = '#prefillerValueWrapper'+prefiller_id;
            var autoHeight = $(id).children('table').css('height', 'auto').height();
            $(id).children('table').css('height', '85px');
            // $(id).children('table').css('height','auto');
            if(autoHeight>300){
                autoHeight    =   300;
            }
            $(id).children('table').animate({
                height: autoHeight
            });
            if($(this).hasClass("clickToshowprefiller"))
                $(".showHideButton"+prefiller_id).removeClass("clickToshowprefiller").addClass("clickToHideprefiller");
            $(this).html('hide');


        });

        $(document).on('click','.clickToHideprefiller', function () {
            var prefiller_id =$(this).attr('docketFeildIdForPrefiller');
            var id = '#prefillerValueWrapper'+prefiller_id;
            var height = 85;
            $(id).children('table').animate({
                height: height
            });
            if($(this).hasClass("clickToHideprefiller"))
                $(".showHideButton"+prefiller_id).removeClass("clickToHideprefiller").addClass("clickToshowprefiller");
            $(this).html( 'show');


        });

    </script>

    <script>
        $(document).on('click','#talleyUnitRate', function () {
            var talleyUnitRate = document.getElementById('talleyUnitRate');
            var talleyValue = document.getElementById('talleyValue');
            talleyValue.disabled=false;
            talleyValue.checked = false;
            talleyUnitRate.disabled = true;
            $("#talleyUnitRateValue").val(2);

        });

        $(document).on('click','#talleyValue', function () {
            var talleyUnitRate = document.getElementById('talleyUnitRate');
            var talleyValue = document.getElementById('talleyValue');
            talleyUnitRate.disabled = false;
            talleyUnitRate.checked = false;
            talleyValue.disabled=true;
            $("#talleyUnitRateValue").val(1);

        });

        $(document).on('click','#saveTalleyable',function () {
            var talleyable = $("#talleyUnitRateValue").val();
            $.ajax({
                type: "Post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveTallyable')}}",
                data: {"talleyableType": talleyable, "docket_id":'{{ $tempDocket->id }}'},
                success: function (response) {
                    document.getElementById('elementTemplateBottom').scrollIntoView({behavior: 'smooth'});
                    $.when($('#sortable').append(response)).done(function () {
                        $('.editable').editable(
                            {
                                success: function (response) {
                                    $.ajax({
                                        type: "GET",
                                        url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                                        success:function (response) {
                                            $("#mobileviewHtml").html(response);
                                        }
                                    });
                                },
                                validate: function(value) {
                                    if($.trim(value) == '') {
                                        return 'The value field is required';

                                    }
                                }
                            });
                        $('.docketFieldNumbereditable').editable({});
                    });
                }
            });
            $('#tallyable').modal('hide');

        });

        $(document).ready(function()
        {
            $('#setFormula').on('show.bs.modal', function(e) {
                $('.formulaView').css('display','none')
                $('.spinnergridformula').css('display','block')
                var fieldId = $(e.relatedTarget).data('field_id');
                var grid_field_id =  $(e.relatedTarget).data('grid_field_id');
                $('.docketfieldidFormulasection').val(fieldId)

                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/gridFormulaSet')}}",
                    data:{'field_id':fieldId,'grid_field_id': grid_field_id },
                    success: function (response) {
                        $('.formulaView').css('display','block')
                        $(".formulaView").html(response);
                        $('.spinnergridformula').css('display','none')
                        // $(".formulaView").css('min-height','auto')
                        var lastList = $(".listFormula li:last-child");

                        if (lastList.hasClass("valueType")){
                            $('.cellFormula').addClass('disabled')
                            $('.numberFormula').addClass('disabled')
                            $('.timeDifference').addClass('disabled')
                            $('.operatorFormula').removeClass('disabled')



                        }else if(lastList.hasClass("operatorType")){

                            $('.cellFormula').removeClass('disabled')
                            $('.numberFormula').removeClass('disabled')
                            $('.timeDifference').addClass('disabled')
                            $('.operatorFormula').addClass('disabled')



                        }else if(lastList.hasClass("timeDifferenceType")) {
                            $('.cellFormula').addClass('disabled')
                            $('.numberFormula').addClass('disabled')
                            $('.timeDifference').addClass('disabled')
                            $('.operatorFormula').addClass('disabled')

                        }
                        else if(lastList.hasClass("cellType")){
                            $('.cellFormula').addClass('disabled')
                            $('.numberFormula').addClass('disabled')
                            $('.timeDifference').addClass('disabled')
                            $('.operatorFormula').removeClass('disabled')



                        }



                    },


                });

            });

        });


        $(document).on('click', '.formulaset', function (e) {
            e.preventDefault();

            var attributeValue = $(this).closest('.formulaView').find('#saveFormula').attr('removedCellId');
            var attributeGridfieldid = $(this).attr('gridfieldid');
            if(attributeGridfieldid != undefined){
                if(attributeValue != undefined){
                    if(attributeValue.includes(attributeGridfieldid+',')){
                        attributeValue = attributeValue.replace(attributeGridfieldid+',','');
                        $(this).closest('.formulaView').find('#saveFormula').attr('removedCellId',attributeValue);
                    }else{
                        var addedAttributeValue = $(this).closest('.formulaView').find('#saveFormula').attr('addedCellId');
                        if(addedAttributeValue == undefined){
                            addedAttributeValue = ''
                        }
                        $(this).closest('.formulaView').find('#saveFormula').attr('addedCellId',addedAttributeValue + attributeGridfieldid+',');
                    }
                }else{
                    var addedAttributeValue = $(this).closest('.formulaView').find('#saveFormula').attr('addedCellId');
                    if(addedAttributeValue == undefined){
                        addedAttributeValue = ''
                    }
                    $(this).closest('.formulaView').find('#saveFormula').attr('addedCellId',addedAttributeValue + attributeGridfieldid+',');
                }
            }

            var type = $(this).attr('valuetype');
            var id = $(this).attr('fieldid');
            var gridid = $(this).attr('gridfieldid');
            $('.spinnergridformulaset').css('display','block')


            $.ajax({
                type : "post",
                data : {type :type, id: id , gridid: gridid},
                url : "{{url('dashboard/company/docketBookManager/designDocket/formulaSet')}}",
                success: function (response) {
                    $('.spinnergridformulaset').css('display','none')
                    $('.listFormula').append(response)
                    var lastList = $(".listFormula li:last-child");
                    if (lastList.hasClass("valueType")){
                        $('.cellFormula').addClass('disabled')
                        $('.numberFormula').addClass('disabled')
                        $('.operatorFormula').removeClass('disabled')
                        $('.timeDifference').addClass('disabled')
                    }else if(lastList.hasClass("operatorType")){
                        $('.cellFormula').removeClass('disabled')
                        $('.numberFormula').removeClass('disabled')
                        $('.operatorFormula').addClass('disabled')
                        $('.timeDifference').addClass('disabled')

                    }else if(lastList.hasClass("cellType")){
                        $('.cellFormula').addClass('disabled')
                        $('.numberFormula').addClass('disabled')
                        $('.operatorFormula').removeClass('disabled')
                        $('.timeDifference').addClass('disabled')
                    }
                    else if(lastList.hasClass("timeDifferenceType")){
                        $('.cellFormula').addClass('disabled')
                        $('.numberFormula').addClass('disabled')
                        $('.timeDifference').addClass('disabled')
                        $('.operatorFormula').addClass('disabled')
                        $(".listFormula li:last-child").children("select").attr("index",$(".listFormula li:last-child").index());
                        startCell   =     $(".listFormula li:last-child").children("select.startTime").val();
                        endCell =  $(".listFormula li:last-child").children("select.endTime").val();
                        $(".listFormula li:last-child").children(".cellValue").val( "TDiff("+(startCell+','+endCell)+")");
                    }

                }

            });



        });

        $(document).on('change','.startTime', function () {
            var starttime = $(this).find(":selected").val();
            var value =  $(this).parents("li").children('.cellValue').val();
            var  remove = value.replace('TDiff(', '');
            var final =remove.replace(')', '')
            var arrayvalue = final.split(',')
            var endTimer = arrayvalue[1]
            $(this).parents("li").children('.cellValue').val("TDiff("+(starttime+','+endTimer)+")")
        })


        $(document).on('change','.endTime', function () {
            var endTimer = $(this).find(":selected").val();
            var value =  $(this).parents("li").children('.cellValue').val();
            var  remove = value.replace('TDiff(', '');
            var final =remove.replace(')', '')
            var arrayvalue = final.split(',')
            var startTimer = arrayvalue[0]
            $(this).parents("li").children('.cellValue').val("TDiff("+(startTimer+','+endTimer)+")")
        })





        $(document).on('click', '#saveFormula', function () {
            var lastList = $(".listFormula li:last-child");
            var event = this;
            if(lastList.hasClass("operatorType")){
                alert('Invalid Formula')

            }else{
                var docketGridFieldId = $("#docketFieldGridIds").val();
                var docketFieldId = $('.docketfieldidFormulasection').val()

                var cellValue = $('.cellValue')
                var arrayValue = [];
                for(var i = 0; i < cellValue.length; i++){
                    if ($(cellValue[i]).val() == ""){
                        arrayValue.push("0") ;
                    } else{
                        arrayValue.push($(cellValue[i]).val()) ;

                    }
                }
                $.ajax({
                    type : "post",
                    data : {arrayValue :arrayValue,docketGridFieldId: docketGridFieldId,docketFieldId:docketFieldId},
                    url : "{{url('dashboard/company/docketBookManager/designDocket/saveFormula')}}",
                    success: function (response) {
                        var addedcellid = $(event).attr('addedcellid');
                        if(addedcellid != undefined){
                            addedcellid = addedcellid.split(",");
                            addedcellid.forEach(cellId => {
                                $('.formulaButtonData'+cellId).hide();
                                $('.showhideprefillerbtn'+cellId).hide();
                            });
                        }

                        var removedcellid = $(event).attr('removedcellid');
                        if(removedcellid != undefined){
                            removedcellid = removedcellid.split(",");
                            removedcellid.forEach(cellId => {
                                $('.formulaButtonData'+cellId).show();
                                $('.showhideprefillerbtn'+cellId).show();
                            });
                        }

                        $("#setFormula").modal('hide');
                        var id = ".gridPrefillerShowasss"+docketGridFieldId;
                        $("#setFormula").modal('hide');
                        $(id).html(response);
                        if(cellValue.length == 0){
                            $('.showhideprefillerbtn'+docketGridFieldId).css('display','')
                        }else{
                            $('.showhideprefillerbtn'+docketGridFieldId).css('display','none')

                        }
                    }
                });
            }
        });

        $(document).on('click', '.btnremoveformula', function () {

            var cell = $(this).closest('li').find('.cellValue').val();
            if(cell.includes('cell')){
                var cellId = cell.replace('cell','');
                var attributeValue = $(this).closest('.formulaView').find('#saveFormula').attr('removedCellId');
                if(attributeValue == undefined){
                    attributeValue = '';
                }
                $(this).closest('.formulaView').find('#saveFormula').attr('removedCellId',attributeValue + cellId+',');
            }

            $(".listFormula li:last-child").remove();

            var lastList = $(".listFormula li:last-child");
            if (lastList.hasClass("valueType")){
                $('.cellFormula').addClass('disabled')
                $('.numberFormula').addClass('disabled')
                $('.timeDifference').addClass('disabled')
                $('.operatorFormula').removeClass('disabled')

            }else if(lastList.hasClass("operatorType")){

                $('.cellFormula').removeClass('disabled')
                $('.numberFormula').removeClass('disabled')
                $('.timeDifference').addClass('disabled')
                $('.operatorFormula').addClass('disabled')

            }else if(lastList.hasClass("cellType")){
                $('.cellFormula').addClass('disabled')
                $('.numberFormula').addClass('disabled')
                $('.timeDifference').addClass('disabled')
                $('.operatorFormula').removeClass('disabled')



            }else if(lastList.hasClass("timeDifferenceType")) {
                $('.cellFormula').addClass('disabled')
                $('.numberFormula').addClass('disabled')
                $('.timeDifference').addClass('disabled')
                $('.operatorFormula').removeClass('disabled')

            }

            else{
                $('.cellFormula').removeClass('disabled')
                $('.numberFormula').removeClass('disabled')
                $('.timeDifference').removeClass('disabled')
                $('.operatorFormula').addClass('disabled')
            }

        });

        // start newOne
        $(document).ready(function()
        {










            $('#ƒ').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });

            var loader = {};

            loader.content = function(element){
                var contentCount = $(element).children().length;
                $.ajax({
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependent')}}",
                    dataType: "json",
                    type: "post",
                    data:{
                        offset:contentCount
                    },
                    success:function(data){
                        var htmlString = '<div class="samplechild"><h4>'+data.title+'</h4><p>'+data.post+'</p></div>';
                        $(element).append(htmlString);
                    }
                });
            }

            // $(document).scroll(function() {
            //     console.log('Scrolled to ' + $(this).scrollTop());
            // })
            // $(".scrolling-pagination").scroll(function () {
            //     var $this = $(this);
            //     var height = this.scrollHeight - $this.height(); // Get the height of the div
            //     console.log(height);
            //     var scroll = $this.scrollTop(); // Get the vertical scroll position
            //
            //     var isScrolledToEnd = (scroll >= height);
            //
            //     $(".scroll-pos").text(scroll);
            //     $(".scroll-height").text(height);
            //
            //     if (isScrolledToEnd) {
            //         // var additionalContent = GetMoreContent(); // Get the additional content
            //         //
            //         // $this.append(additionalContent); // Append the additional content
            //
            //     }
            // });



            $('#setgridPrefiller').on('show.bs.modal', function(e) {
                var autoFieldData = $(e.relatedTarget).data('autofield')
                var fieldId = $(e.relatedTarget).data('field_id');
                var grid_field_id =  $(e.relatedTarget).data('grid_field_id');
                var grid_field_type =  $(e.relatedTarget).data('grid_field_type');
                var isDependentData = $(".showhideprefillerbtn"+grid_field_id).attr('data-is_dependent_data');
                var selectedUrl = $(e.relatedTarget).data('echowise_id');
                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependent')}}",
                    data:{'field_id':fieldId,'grid_field_id': grid_field_id,'type': grid_field_type,'isDependent':isDependentData,'isOpen':1},
                    success: function (response) {
                        $(".gridPrefillerShow").html(response);
                        var isLoading = false;
                        $(".scrolling-pagination").scroll(function () {
                            var $this = $(this);
                            var height = this.scrollHeight - $this.height();
                            var scroll = $this.scrollTop();
                            var isScrolledToEnd = (scroll >= height);
                            $(".scroll-pos").text(scroll);
                            $(".scroll-height").text(height);
                            if (isScrolledToEnd) {
                                $('.spinnerCheckgrid').css('display','block')
                                if (!isLoading) {
                                isLoading = true;
                                    var lastpage = $('.gridPrefillerPaginate').val()
                                    $.ajax({
                                        type: "post",
                                        url: "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependentView?page=')}}" + lastpage,
                                        data: {
                                            'field_id': fieldId,
                                            'grid_field_id': grid_field_id,
                                            'type': grid_field_type,
                                            'isDependent': isDependentData,
                                            'isOpen': 0,
                                            _token: '{{csrf_token()}}'
                                        },
                                        success: function (respo) {

                                            $('.gridPrefillerPaginate').remove()
                                            $('.prefillerpaginateData > tbody:last-child').append(respo)
                                            $('.spinnerCheckgrid').css('display','none')
                                            isLoading = false;
                                            $('.editabledocketgridprefiller').editable({
                                                mode:"inline"
                                            });
                                            $('.editabledocketprefiller').editable({
                                                mode:"inline"
                                            });
                                            if (autoFieldData == 1){
                                                $('.defaultgridCheckMark').css('display','none')
                                                $('.defaultAutoPrefiller').css('display','')
                                            }
                                            else{
                                                $('.defaultgridCheckMark').css('display','')
                                                $('.defaultAutoPrefiller').css('display','none')
                                            }
                                        },

                                    })


                                }
                            }
                        });
                        {{--$( ".scrolling-pagination" ).scroll(function() {--}}
                        {{--    --}}
                        {{--    var url =   $('.pagination li.active + li a').attr("href");--}}
                        {{--    var pagenumber = url.split('=')[1]--}}
                        {{--    $.ajax({--}}
                        {{--        type: "post",--}}
                        {{--        url: "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependentView?page=')}}" + pagenumber,--}}
                        {{--        data: {--}}
                        {{--            'field_id': fieldId,--}}
                        {{--            'grid_field_id': grid_field_id,--}}
                        {{--            'type': grid_field_type,--}}
                        {{--            'isDependent': isDependentData,--}}
                        {{--            'isOpen': 0,--}}
                        {{--            _token: '{{csrf_token()}}'--}}
                        {{--        },--}}
                        {{--        success: function (respo) {--}}
                        {{--            $('ul.pagination').remove()--}}
                        {{--            $('.prefillerpaginateData > tbody:last-child').append(respo)--}}
                        {{--        }--}}
                        {{--    })--}}
                        {{--})--}}
                        {{--$('ul.pagination').hide();--}}
                        {{--$(function() {--}}
                        {{--    $('.scrolling-pagination').jscroll(function(){--}}
                        {{--        $('.jscroll-added').remove()--}}
                        //          var url =   $('.pagination li.active + li a').attr("href");
                        //          var pagenumber = url.split('=')[1]
                        {{--        $.ajax({--}}
                        {{--            type: "post",--}}
                        {{--            url:  "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependentView?page=')}}"+pagenumber,--}}
                        {{--            data: {--}}
                        {{--                'field_id': fieldId,--}}
                        {{--                'grid_field_id': grid_field_id,--}}
                        {{--                'type': grid_field_type,--}}
                        {{--                'isDependent': isDependentData,--}}
                        {{--                'isOpen': 0,--}}
                        {{--                _token: '{{csrf_token()}}'--}}
                        {{--            },--}}
                        {{--            success: function (respo) {--}}
                        {{--                $('.jscroll-added').remove()--}}
                        {{--               $('ul.pagination').remove();--}}
                        {{--                $('.prefillerpaginateData > tbody:last-child').append(respo);--}}

                                        // $('.editabledocketgridprefiller').editable({
                                        //     mode:"inline"
                                        // });
                                        // $('.editabledocketprefiller').editable({
                                        //     mode:"inline"
                                        // });
                                        // if (autoFieldData == 1){
                                        //     $('.defaultgridCheckMark').css('display','none')
                                        //     $('.defaultAutoPrefiller').css('display','')
                                        // }
                                        // else{
                                        //     $('.defaultgridCheckMark').css('display','')
                                        //     $('.defaultAutoPrefiller').css('display','none')
                                        // }


                        {{--            },--}}

                        {{--        });--}}
                        {{--    });--}}
                        {{--});--}}

                        $('#gridprefillerTypechecks').val(grid_field_type)
                        $('#grid_docket_field_id').val(fieldId)
                        $('#grid_ids').val(grid_field_id)
                        if (autoFieldData == 1){
                            $('.defaultgridCheckMark').css('display','none')
                            $('.defaultAutoPrefiller').css('display','')
                        }else{
                            $('.defaultgridCheckMark').css('display','')
                            $('.defaultAutoPrefiller').css('display','none')
                        }

                        if(isDependentData ==1){
                            var type = $( ".saveDocketPrefillerManager option:selected" ).attr('datatype');
                            if(type == 0){
                                $('.editabledocketgridprefiller').editable({
                                    mode:"inline"
                                });
                                $('.editabledocketprefiller').editable({
                                    mode:"inline"
                                });
                            }else{
                                $('#deleteprefillerManagerLabel').remove()
                                $('.btnprefiller').remove()
                                $('.cellAutoFill').prop('disabled',true)
                            }
                        }else{
                            $('.editabledocketgridprefiller').editable({
                                mode:"inline"
                            });
                            $('.editabledocketprefiller').editable({
                                mode:"inline"
                            });
                        }

                        $(document).ready(function(){
                            $('.prefillerGridLinkFilter').multiselect({
                                enableFiltering: true,
                                enableCaseInsensitiveFiltering: false,
                                buttonWidth:'100%',
                                enableClickableOptGroups: false,
                                checkboxName: false,
                            });
                        });

                        var i = 0;

                        $(".addfilterLinkPrefiller").click(function(){
                            ++i;
                            var docketGridFieldId = $(this).attr('data-girdfieldId')
                            $.ajax({
                                type:'post',
                                url:"{{url('dashboard/company/docketBookManager/designDocket/gridDynamicFilterField')}}",
                                data:{docketGridFieldId:docketGridFieldId},
                                success:function(response){
                                    $('#dynamicPrefillerFilterField').append(response)
                                    // $("#dynamicPrefillerFilterField").append('<div style="margin: 0px 0px 30px 0px;"><br><div class="col-md-6"><select style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%"> </select> </div> <div class="col-md-5"> <select  class="form-control prefillerGridLinkFilter" > </select> </div> <div class="col-md-1"> <button type="button" name="add"  class="btn btn-danger removefilterLinkPrefiller" style="margin: 0;">Remove</button> </div> </div>');
                                }
                            })
                        });

                        $(document).on('click', '.removefilterLinkPrefiller', function(){
                            var linkprefillerfilter = $(this).attr('data-linkprefillerfilter');
                            $.ajax({
                                type:'post',
                                url:"{{url('dashboard/company/docketBookManager/designDocket/removeGridDynamicFilterField')}}",
                                data:{linkprefillerfilter:linkprefillerfilter},
                                success:function(response){
                                }
                            })
                            $(this).parents('#dynamicPrefillerFilterField div').remove();

                        });
                    },
                });
                // console.log($('.scrolling-pagination').scrollTop(0))
            });


        });

        $(document).ready(function() {
            $('#addGridPrefillerModel').on('show.bs.modal', function(e) {
                var docket_field_id = $(e.relatedTarget).data('docketfieldid');
                var gridId = $(e.relatedTarget).data('gridid');
                var autoFiled = $(e.relatedTarget).data('autofield');
                $('.gridprefillerInvValue').val('');
                $('#parentDocketFieldId').val(docket_field_id);
                $('#parentGridId').val(gridId);
                $('#autoFiled').val(autoFiled)
            });
        });

        $(document).on('click','#saveGridParentPrefiller',function () {
            console.log('Data');
            $(".spinnerCheckgrid").css('display','block');
            var docketFieldId  = $("#parentDocketFieldId").val();
            var gridId = $("#parentGridId").val();
            var parentId = 0;
            var valueCategoryId = $(".parentData .typeValueSingle").val();
            var isInteger = 0;
            var index = 0;
            var value = $('.parentData .gridprefillerInvValue').val()
            var autoFiled = $('#dynamicAutoFieldId').val()

            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridPrefiller')}}",
                data: {
                    docket_field_id: docketFieldId,
                    value: value,
                    parent_id: parentId,
                    value_category_id: valueCategoryId,
                    isInteger: isInteger,
                    index: index,
                    gridId: gridId,

                },
                success: function (response) {
                    $('#addGridPrefillerModel').modal('hide');
                    $(".setGridPrefillers").html(response);
                    $(".spinnerCheckgrid").css('display','none');
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });


                    if($('.disableFormulaButton'+gridId).val()== 1){
                        $('.formulaButtonData'+gridId).css('display','none')
                    }else{
                        $('.formulaButtonData'+gridId).css('display','')
                    }

                    if(autoFiled == 1){
                        var id = '#prefillerValueWrapper'+gridId;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','')
                        $('.defaultgridCheckMark').css('display','none')
                        $('.defaultAutoPrefiller').css('display','')
                    }else{
                        var id = '#prefillerValueWrapper'+gridId;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','none')
                        $('.defaultgridCheckMark').css('display','')
                        $('.defaultAutoPrefiller').css('display','none')
                    }

                }
            })
        });


         $(document).on('click','.toggle-password',function () {

                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $('.ecowisepassword');
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

        $(document).on('change','.cellAutoFill',function(){
            if ($(this).is(':checked')){
                var gridId = $(this).attr("gridId");
                var id = '#prefillerValueWrapper'+gridId;
                var autoHeight = $(id).children('table');
                autoHeight.children('tbody').children('tr:first-child').css('display','')
                $('.defaultgridCheckMark').css('display','none')
                $('.defaultAutoPrefiller').css('display','')

                $.ajax({
                    type: "post",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridAutoCellPrefiller')}}",
                    data: {
                        id: gridId,
                        value: 1
                    },
                    success: function (response) {
                        $('#dynamicAutoFieldId').val(1)
                    }

                })


            }else{
                var gridId = $(this).attr("gridId");
                var id = '#prefillerValueWrapper'+gridId;
                var autoHeight = $(id).children('table');
                autoHeight.children('tbody').children('tr:first-child').css('display','none')
                $('.defaultgridCheckMark').css('display','')
                $('.defaultAutoPrefiller').css('display','none')

                $.ajax({
                    type: "post",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridAutoCellPrefiller')}}",
                    data: {
                        id: gridId,
                        value: 0
                    },
                    success: function (response) {
                        $('#dynamicAutoFieldId').val(0)
                    }
                })
            }
        });

        $('#addGridPrefillerValue').on('show.bs.modal', function(e) {
            $('.gridsprefillerLinkChecksingle').prop("checked", "")
            $('.gridprefillerInvValue').val('');
            $('.gridsappendvaluetypes').show();
            $(".gridsappenddatabytypes").hide();
            var docket_id = $(e.relatedTarget).data('docket_id');
            var docketfieldid = $(e.relatedTarget).data('docketfieldid');
            var prefillerId = $(e.relatedTarget).data('id');
            var index = $(e.relatedTarget).data('index');
            var label = $(e.relatedTarget).data('labels');
            var prefillerType = $(e.relatedTarget).data('prefillertype');
            $("#gridprefillerindvsentdocketvalueid").val(docket_id);
            $("#gridprefillerindvsentdocketfieldid").val(docketfieldid);
            $("#gridprefillerindvsentdocketprefillerid").val(prefillerId);
            $("#gridprefillerindvindex").val(index);
            $("#gridprefillerindvintegertype").val(prefillerType);
            // if (prefillerType == 1){
            //     $('.gridprefillerInvValue').attr('type','number')
            //     $('.gridprefillerInvValue').attr({onkeydown : "return event.keyCode !== 69"})
            // }else if (prefillerType == 0){
            //     $('.gridprefillerInvValue').attr('type','text')
            // }
        });

        $('#gridaddIndPrefiller').click(function () {
            $(".spinnerCheckgrid").css('display','block');
            $("#addGridPrefillerValue").modal('hide');
            var docket_id =   $("#gridprefillerindvsentdocketvalueid").val();
            var docketfieldid =  $("#gridprefillerindvsentdocketfieldid").val();
            var prefillerId = $("#gridprefillerindvsentdocketprefillerid").val();
            var prefillerValue = $(".gridchildData .gridprefillerInvValue").val();
            var valueCategoryId = $(".gridchildData .typeValueSingle").val()
            var index = $("#gridprefillerindvindex").val();
            var autoFiled = $('#dynamicAutoFieldId').val();
            $.ajax({
                type: "POST",
                url: "{{ url('dashboard/company/docketBookManager/designDocket/gridaddIndPrefiller') }}",
                data: {docket_field_id: docket_id,docketGridId:docketfieldid,prefillerId:prefillerId,prefillerValue:prefillerValue,value_category_id:valueCategoryId,index:index},
                success: function (response) {
                    $(".setGridPrefillers").html(response);
                    $(".spinnerCheckgrid").css('display','none');
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });
                    console.log(autoFiled)

                    if(autoFiled == 1){
                        var id = '#prefillerValueWrapper'+docketfieldid;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','')
                        $('.defaultgridCheckMark').css('display','none')
                        $('.defaultAutoPrefiller').css('display','')
                    }else{
                        var id = '#prefillerValueWrapper'+docketfieldid;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','none')
                        $('.defaultgridCheckMark').css('display','')
                        $('.defaultAutoPrefiller').css('display','none')

                    }

                }
            })
        });

        $(document).on('click','#deleteGridLabel', function () {
            var docketGridFieldId = $(this).attr('data-docketfieldid');
            $(".spinnerCheckgrid").css('display','block');
            var id =  $(this).attr('data-id');
            var autoField = $('#dynamicAutoFieldId').val()
            var docketFieldId =  $('#grid_docket_field_id').val()

            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/deleteGridPrefiller')}}",
                data: {
                    prefiller_id : id,
                    docket_grid_field_id : docketGridFieldId,
                    docket_id : '{{ $tempDocket->id }}',
                    docket_field_id:docketFieldId
                },
                success: function (response) {
                    $(".setGridPrefillers").html(response);
                    $(".spinnerCheckgrid").css('display','none');
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });

                    if($('.disableFormulaButton'+docketGridFieldId).val()== 1){
                        $('.formulaButtonData'+docketGridFieldId).css('display','none')
                    }else{
                        $('.formulaButtonData'+docketGridFieldId).css('display','')
                    }
                    if(autoField == 1){
                        var id = '#prefillerValueWrapper'+docketGridFieldId;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','')
                        $('.defaultgridCheckMark').css('display','none')
                        $('.defaultAutoPrefiller').css('display','')
                    }else{
                        var id = '#prefillerValueWrapper'+docketGridFieldId;
                        var autoHeight = $(id).children('table');
                        autoHeight.children('tbody').children('tr:first-child').css('display','none')
                        $('.defaultgridCheckMark').css('display','')
                        $('.defaultAutoPrefiller').css('display','none')
                    }
                }
            })
        })

        $(document).on('click', '#prefillerInDependent', function () {
            $('.spinnerCheckgrid').css('display','')
            var depend = document.getElementById('prefillerDependent');
            var inDepend = document.getElementById('prefillerInDependent');
            var prefillerEcowise = document.getElementById('prefillerEcowise');
            var gridprefillerId = $(this).attr('gridprefillerId')
            var docketFieldId = $(this).attr('docketfieldid')
            depend.checked = false;
            depend.disabled=false;

            prefillerEcowise.checked = false;
            prefillerEcowise.disabled=false;

            inDepend.checked = true;
            inDepend.disabled = true;

            $("#prefillerInDependent").val(0);
            $.ajax({
                type: "post",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/saveIsDependent')}}',
                data:{'isDependent':0,'grid_field_id':gridprefillerId,'field_id':docketFieldId,'isOpen':0},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none')
                    $(".gridPrefillerShow").html(response);
                    var gridPrefillerUPdate = $('.showhideprefillerbtn'+gridprefillerId).attr('data-is_dependent_data', "0");

                    var data = $("#dynamicAutoFieldId").val();
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                    if(data == 1){
                        $('.defaultgridCheckMark').css('display','none')
                        $('.defaultAutoPrefiller').css('display','')

                    }else{
                        $('.defaultgridCheckMark').css('display','')
                        $('.defaultAutoPrefiller').css('display','none')
                    }

                }
            });

        });
        $(document).on('click', '#prefillerDependent', function () {
            $('.spinnerCheckgrid').css('display','')
            var depend = document.getElementById('prefillerDependent');
            var inDepend = document.getElementById('prefillerInDependent');
            var prefillerEcowise = document.getElementById('prefillerEcowise');
            var docketFieldId = $(this).attr('docketFieldId')

            depend.checked = true;
            depend.disabled=true;
            inDepend.checked = false;
            inDepend.disabled = false;
            prefillerEcowise.checked = false;
            prefillerEcowise.disabled=false;
            var gridprefillerId = $(this).attr('gridprefillerId')
            $("#prefillerDependent").val(1);
            $.ajax({
                type: "post",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/saveIsDependent')}}',
                data:{'isDependent':1,'grid_field_id':gridprefillerId,'field_id':docketFieldId,'isOpen':0},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none')
                    $(".gridPrefillerShow").html(response);
                    var gridPrefillerUPdate = $('.showhideprefillerbtn'+gridprefillerId).attr('data-is_dependent_data', "1");


                    var data = $("#dynamicAutoFieldId").val();
                    if(data == 1){
                        $('.defaultgridCheckMark').css('display','none')
                        $('.defaultAutoPrefiller').css('display','')
                    }else{
                        $('.defaultgridCheckMark').css('display','')
                        $('.defaultAutoPrefiller').css('display','none')
                    }

                    var type = $( ".saveDocketPrefillerManager option:selected" ).attr('datatype');
                    if(type == 0){
                        $('.editabledocketgridprefiller').editable({
                            mode:"inline"
                        });
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }else{
                        $('#deleteprefillerManagerLabel').remove()
                        $('.btnprefiller').remove()
                        $('.cellAutoFill').prop('disabled',true)
                    }
                }
            });
        });

        $(document).on('click', '#prefillerEcowise', function () {
            $('.spinnerCheckgrid').css('display','');
            var depend = document.getElementById('prefillerDependent');
            var inDepend = document.getElementById('prefillerInDependent');
            var prefillerEcowise = document.getElementById('prefillerEcowise');
            var docketFieldId = $(this).attr('docketFieldId')
            prefillerEcowise.checked = true;
            prefillerEcowise.disabled=true;
            depend.checked = false;
            depend.disabled=false;
            inDepend.checked = false;
            inDepend.disabled = false;
            var gridprefillerId = $(this).attr('gridprefillerId')
            var selectedUrl = $('#grid_echowise_id').val();
            $("#prefillerEcowise").val(2);
            $.ajax({
                type: "post",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/saveIsDependent')}}',
                data:{'isDependent':2,'grid_field_id':gridprefillerId,'field_id':docketFieldId,'isOpen':0,'selectedUrl':selectedUrl},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $(".gridPrefillerShow").html(response);
                    $('.showhideprefillerbtn'+gridprefillerId).attr('data-is_dependent_data', "2");

                    // var gridPrefillerUPdate = $('.showhideprefillerbtn'+gridprefillerId).attr('data-is_dependent_data', "1");
                    // var data = $("#dynamicAutoFieldId").val();
                    // if(data == 1){
                    //     $('.defaultgridCheckMark').css('display','none')
                    // }else{
                    //     $('.defaultgridCheckMark').css('display','')
                    // }
                    // var type = $( ".saveDocketPrefillerManager option:selected" ).attr('datatype');
                    // if(type == 0){
                    //     $('.editabledocketgridprefiller').editable({
                    //         mode:"inline"
                    //     });
                    //     $('.editabledocketprefiller').editable({
                    //         mode:"inline"
                    //     });
                    // }else{
                    //     $('#deleteprefillerManagerLabel').remove()
                    //     $('.btnprefiller').remove()
                    //     $('.cellAutoFill').prop('disabled',true)
                    // }

                }
            });


        });

        $(document).on('click','.connectEcowise', function () {
            $(this).html('<span class="spinner" style="padding: 0 10px 0px 0px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>Connect');
            $('.connectEcowise').addClass('disabled')
            $('.errormessage').css('display','none');
            $('.successmessage').css('display','none');
            var username = $('.ecowiseusername').val();
            var password = $('.ecowisepassword').val();
            var url = $('.ecowiseurl').val()
            var girdId = $('#grid_ids').val()
            $.ajax({
                type: "post",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/getEcowiseData')}}',
                data:{'username':username,'password':password,'url':url,'girdId':girdId},
                success:function(response){
                    if(response['status'] == false){
                        $('.connectEcowise').text('Connect');
                        $('.errormessage').css('display','block');
                        $('.errormessage').text(response['message'])
                        $('.connectEcowise').removeClass('disabled')
                    }else{
                        $('.ecowiseusername').val("");
                        $('.ecowisepassword').val("");
                        $('.ecowiseurl').val("");
                        $('.successmessage').css('display','block');
                        $('.successmessage').text("Link prefiller added successfully.")
                        $('.connectEcowise').text('Connect');
                        $('.connectEcowise').removeClass('disabled')
                        $('.changeSelectUrl').html(response)
                    }
                }
            })
        })

        $(document).on('change','.selectUrl', function() {
            $('.spinnerCheckgrid').css('display','');
            var id = $("option:selected", this).val();
            var fieldId = $('#grid_ids').val()
            var autoField = $('#dynamicAutoFieldId').val()
            var docket_field_id = $('#grid_docket_field_id').val()
            $.ajax({
                type: "post",
                url: '{{url('dashboard/company/docketBookManager/designDocket/ecowiseNormalDataUpdateUrl')}}',
                data: {id:id,field_id:fieldId,autoField:autoField,docket_field_id:docket_field_id},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updateedEcowiseData'+fieldId).html(response)
                    var value = 0;
                    var gridId = fieldId;
                    var fieldIds = docket_field_id;
                    var linkId = id;
                    var viewStatus = false;

                    $.ajax({
                        type:'post',
                        data: {value:value,gridId:gridId,linkId:linkId,fieldIds:fieldIds,viewStatus:viewStatus,linkprefillerfilterid:0},
                        url: '{{url('dashboard/company/docketBookManager/designDocket/linkGridPrefillerFilterView')}}',
                        success:function(response){
                            $('.ecowiseGridFieldFilterView'+gridId).html(response)
                            $(document).ready(function(){
                                $('.prefillerGridLinkFilter').multiselect({
                                    enableFiltering: true,
                                    enableCaseInsensitiveFiltering: false,
                                    buttonWidth:'100%',
                                    enableClickableOptGroups: false,
                                    checkboxName: false,
                                });
                            });
                        }
                    })

                }
            });



        });


        $(document).on('change','.ecowiseCellAutoFill',function(){
            if ($(this).is(':checked')){
                $('.spinnerCheckgrid').css('display','');
                var gridId = $(this).attr("gridId");
                var docket_field_id = $('#grid_docket_field_id').val();
                $('.addPrefillerEcowise'+gridId).removeClass('disabled')
                var id = '#prefillerValueWrapper'+gridId;
                var autoHeight = $(id).children('table');
                autoHeight.children('tbody').children('tr:first-child').css('display','')
                $('.defaultgridCheckMark').css('display','none')
                $.ajax({
                    type: "post",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridEcowiseAutoCellPrefiller')}}",
                    data: {
                        id: gridId,
                        value: 1,
                        docket_field_id:docket_field_id
                    },
                    success: function (response) {
                        $('.spinnerCheckgrid').css('display','none');
                        $('#dynamicAutoFieldId').val(1)
                        $('.updateedEcowiseData'+gridId).html(response)

                    }
                })


            }else{
                $('.spinnerCheckgrid').css('display','');
                console.log('unchecked')
                var gridId = $(this).attr("gridId");
                var docket_field_id = $('#grid_docket_field_id').val();
                $('.addPrefillerEcowise'+gridId).addClass('disabled')
                var id = '#prefillerValueWrapper'+gridId;
                var autoHeight = $(id).children('table');
                autoHeight.children('tbody').children('tr:first-child').css('display','none')
                $('.defaultgridCheckMark').css('display','')
                $.ajax({
                    type: "post",
                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridEcowiseAutoCellPrefiller')}}",
                    data: {
                        id: gridId,
                        value: 0,
                        docket_field_id:docket_field_id
                    },
                    success: function (response) {
                        $('.spinnerCheckgrid').css('display','none');
                        $('#dynamicAutoFieldId').val(0)
                        $('.updateedEcowiseData'+gridId).html(response)
                    }
                })
            }
        });



        $(document).on('change','.selectPrefilerEcowise', function () {
            $('.spinnerCheckgrid').css('display','');
            var fieldId = $('#grid_ids').val();
            var selectedId = $('option:selected',this).val();
            var type = $('option:selected',this).attr('type')
            var index = $('option:selected',this).attr('indexcell')
            var docket_field_id = $('option:selected',this).attr('docketfield')
            var autoField = $('#dynamicAutoFieldId').val();
            $.ajax({
                type: "post",
                url: '{{url('dashboard/company/docketBookManager/designDocket/saveSelectPrefilerEcowise')}}',
                data: {selectedId:selectedId,field_id:fieldId,type:type,index:index,docket_field_id:docket_field_id,autoField:autoField},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updateedEcowiseData'+fieldId).html(response)

                }
            });
        })


        $(document).on('change','.selectAutoCellEcowise',function(){
            $('.spinnerCheckgrid').css('display','');
            var element = $("option:selected", this);
            var index = element.attr('indexcell')
            var gridFieldId = element.attr('gridid')
            var docketFieldId = element.attr('docketField')
            var linkGridFieldId = $(this).val()
            var isDependent = $('#isDependentData').val()
            var prefiller_id = $( ".saveDocketPrefillerManager option:selected" ).val();


            $.ajax({
                type: "POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridAutoPrefillerEcowise')}}",
                data: {'index': index, 'gridFieldId': gridFieldId, 'linkGridFieldId': linkGridFieldId,'docketFieldId':docketFieldId,'isDependent':isDependent,'prefiller_id':prefiller_id},
                success: function (response) {
                    $('.spinnerCheckgrid').css('display','none');
                    var id = '.updateedEcowiseData'+gridFieldId;
                    // var autoHeight = $(id).children('table').children('tbody').children('tr:first-child');
                    // autoHeight.replaceWith(response);
                    $(id).html(response)

                    var showvalues = $("input[name^='showGridField']").map(function (idx, ele) {
                        return $(ele).val();
                    }).get();
                    console.log(showvalues);
                    var j;
                    for (j = 0; j < showvalues.length; j++) {
                        var data = '.showhideprefillerbtn'+showvalues[j]
                        $(data).css('display','')
                    }

                    var hidevalues = $("input[name^='hideGridField']").map(function (idx, ele) {
                        return $(ele).val();
                    }).get();
                    console.log(hidevalues);
                    var i;
                    for (i = 0; i < hidevalues.length; i++) {
                        var datas = '.showhideprefillerbtn'+hidevalues[i]
                        $(datas).css('display','none')
                    }

                }
            });
        })

        $(document).on('click','.normalConnectEcowise', function () {
            $(this).html('<span class="spinner" style="padding: 0 10px 0px 0px;font-size: 14px;"><i class="fa fa-spinner fa-spin"></i></span>Connect');
            $('.normalConnectEcowise').addClass('disabled')
            $('.errormessage').css('display','none');
            $('.successmessage').css('display','none');
            var username = $('.prefillerShow .ecowiseusername').val();
            var password = $('.prefillerShow .ecowisepassword').val();
            var url = $('.prefillerShow .ecowiseurl').val();
            var docket_field_id = $('#prefiller_docket_field_id').val()
            $.ajax({
                type: "post",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/getNormalEcowiseData')}}',
                data:{'username':username,'password':password,'url':url,'docket_field_id':docket_field_id},
                success:function(response){
                    if(response['status'] == false){
                        $('.normalConnectEcowise').text('Connect');
                        $('.errormessage').css('display','block');
                        $('.errormessage').text(response['message'])
                        $('.normalConnectEcowise').removeClass('disabled')
                    }else{
                        $('.normalConnectEcowise').text('Connect');
                        $('.ecowiseusername').val("");
                        $('.ecowisepassword').val("");
                        $('.ecowiseurl').val("");
                        $('.successmessage').css('display','block');
                        $('.successmessage').text("Link prefiller added successfully.")
                        $('.changeSelectUrl').html(response)
                        $('.normalConnectEcowise').removeClass('disabled')
                    }
                }
            })
        })

        $(document).on('change','.selectNormalUrl', function() {
            $('.spinnerCheckgrid').css('display','');
            var id = $("option:selected", this).val();
            var docket_field_id = $('#prefiller_docket_field_id').val()
            $.ajax({
                type: "post",
                url: '{{url('dashboard/company/docketBookManager/designDocket/ecowiseNormalDataUpdateUrl')}}',
                data: {id:id,docket_field_id:docket_field_id},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.ecowiseFieldFilterView'+docket_field_id).html(response)
                    var value = 0;
                    var ids = docket_field_id;
                    var linkId = $('.selectNormalUrl').val();
                    var viewStatus = false
                    $.ajax({
                        type:'post',
                        data: {value:value,ids:ids,linkId:linkId,viewStatus:viewStatus,linkprefillerfilterid:0},
                        url: '{{url('dashboard/company/docketBookManager/designDocket/linkPrefillerFilterView')}}',
                        success:function(response){
                            $('.ecowiseFieldFilterView'+ids).html(response)
                            $(document).ready(function(){
                                $('.prefillerLinkFilter').multiselect({
                                    enableFiltering: true,
                                    enableCaseInsensitiveFiltering: false,
                                    buttonWidth:'100%',
                                    enableClickableOptGroups: false,
                                    checkboxName: false,
                                });
                            });
                            $('.spinerFilterLinkPrefiller').css('display','none');
                        }
                    })



                }
            });
        });

        $(document).on('change','.selectNormalPrefilerEcowise', function () {
            $('.spinnerCheckgrid').css('display','');
            var selectedId = $('option:selected',this).val();
            var type = $('option:selected',this).attr('type')
            var index = $('option:selected',this).attr('indexcell')
            var docket_field_id = $('option:selected',this).attr('docketfield')
            var id = $('option:selected',this).attr('id')
            $.ajax({
                type: "post",
                url: '{{url('dashboard/company/docketBookManager/designDocket/selectNormalPrefilerEcowise')}}',
                data: {selectedId:selectedId,type:type,index:index,docket_field_id:docket_field_id,id:id},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updatedNormalEcowiseData'+id).html(response)

                }
            });
        });


        $('#clearAllGridPrefillerModal').on('show.bs.modal',function (e){
            var grid_ids = $('#grid_ids').val();
            $('#clearGridFieldId').val(grid_ids);
        });




        $(document).on('click','#clearAllGridPrefiller', function () {
            var grid_field_id = $('#grid_ids').val();
            var field_id = $('#grid_docket_field_id').val();
            $.ajax({
                type: "POST",
                data: {grid_field_id: grid_field_id, docket_id:'{{$tempDocket->id}}',field_id: field_id,isDependent:0,isOpen:0 },
                url: "{{ url('dashboard/company/docketBookManager/designDocket/clearAllGridPrefiller') }}",
                success: function (response) {
                    $(".gridPrefillerShow").html(response);

                    if($('.disableFormulaButton'+grid_field_id).val()== 1){
                        $('.formulaButtonData'+grid_field_id).css('display','none')
                    }else{
                        $('.formulaButtonData'+grid_field_id).css('display','')
                    }

                    $("#clearAllGridPrefillerModal").modal('hide');

                }
            });

        })
        $(document).on('change','.saveDocketPrefillerManager', function(){
            var gridId = $('#grid_ids').val()
            var docketprefillerid = $(this).val()
            var field_id = $('#grid_docket_field_id').val()
            var type = $("option:selected",this).attr('datatype')
            var autoField = $('#dynamicAutoFieldId').val();
            console.log(autoField)
            $.ajax({
                type:"POST",
                url:"{{url('dashboard/company/docketBookManager/designDocket/saveDocketPrefillerManager')}}",
                data:{'grid_field_id':gridId,'docketprefillerid':docketprefillerid,'isOpen':0,'type':0,'isDependent':1,'field_id':field_id},
                success: function(response){
                    $(".gridPrefillerShow").html(response);
                    var isLoading = false;
                    $(".scrolling-pagination").scroll(function () {
                        var $this = $(this);
                        var height = this.scrollHeight - $this.height();
                        var scroll = $this.scrollTop();
                        var isScrolledToEnd = (scroll >= height);
                        $(".scroll-pos").text(scroll);
                        $(".scroll-height").text(height);
                        if (isScrolledToEnd) {
                            $('.spinnerCheckgrid').css('display','block')
                            if (!isLoading) {
                                isLoading = true;
                                var lastpage = $('.gridPrefillerPaginate').val()
                                $.ajax({
                                    type: "post",
                                    url: "{{url('dashboard/company/docketBookManager/designDocket/saveIsDependentView?page=')}}" + lastpage,
                                    data: {
                                        'field_id': field_id,
                                        'grid_field_id': gridId,
                                        'type': type,
                                        'isDependent': 1,
                                        'isOpen': 1,
                                        _token: '{{csrf_token()}}'
                                    },
                                    success: function (respo) {

                                        $('.gridPrefillerPaginate').remove()
                                        $('.prefillerpaginateData > tbody:last-child').append(respo)
                                        $('.spinnerCheckgrid').css('display','none')
                                        isLoading = false;
                                        $('.editabledocketgridprefiller').editable({
                                            mode:"inline"
                                        });
                                        $('.editabledocketprefiller').editable({
                                            mode:"inline"
                                        });
                                        if (autoFieldData == 1){
                                            $('.defaultgridCheckMark').css('display','none')
                                            $('.defaultAutoPrefiller').css('display','')
                                        }
                                        else{
                                            $('.defaultgridCheckMark').css('display','')
                                            $('.defaultAutoPrefiller').css('display','none')
                                        }
                                    },

                                })


                            }
                        }
                    });
                    if($('.disableFormulaButton'+gridId).val()== 1){
                        $('.formulaButtonData'+gridId).css('display','none')
                    }else{
                        $('.formulaButtonData'+gridId).css('display','')
                    }
                    if(type == 0){
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        if(autoField == 1){
                            $('.defaultgridCheckMark').css('display','none')
                        }else{
                            $('.defaultgridCheckMark').css('display','')
                        }
                    }else{
                        $('#deleteprefillerManagerLabel').remove()
                        $('.btnprefiller').remove()
                        $('.cellAutoFill').prop('disabled',true)
                        if(autoField == 1){
                            $('.defaultgridCheckMark').css('display','none')
                        }else{
                            $('.defaultgridCheckMark').css('display','')
                        }
                    }
                }
            })
        })

        $('#addPrefillerManagerValue').on('show.bs.modal', function(e) {
            $(".childData .gridprefillerInvValue").val('')
            var prefillerManagerId = $(e.relatedTarget).data('docketprefillermanagerid');
            var root = $(e.relatedTarget).data('id');
            var index = $(e.relatedTarget).data('index');
            var grid_field_id = $("#grid_ids").val();
            var isDependentData = $("#isDependentData").val();
            var grid_docket_field_id = $("#grid_docket_field_id").val();
            var is_integer = $( ".saveDocketPrefillerManager option:selected" ).attr('isinteger')
            if(is_integer == 1){
                $(".childData .gridprefillerInvValue").prop("type", "number");
            }else{
                $(".childData .gridprefillerInvValue").prop("type", "text");
            }
            $('.childData .prefillerRootId').val(root)
            $('.childData .prefillerIndexId').val(index)
            $('.childData .parentManagerId').val(prefillerManagerId)
            $('.childData .prefillerisDependentData').val(isDependentData)
            $('.childData .prefillergrid_docket_field_id').val(grid_docket_field_id)
            $('.childData .prefillergrid_field_id').val(grid_field_id)

        });

        $('#saveChildPrefiller').click(function () {
            var  prefillerManagerId = $(".childData .parentManagerId").val();
            var index  = $(".childData .prefillerIndexId").val();
            var rootId = $(".childData .prefillerRootId").val();
            var value = $(".childData .gridprefillerInvValue").val();
            var isDependent = $('.childData .prefillerisDependentData').val();
            var field_id = $('.childData .prefillergrid_docket_field_id').val();
            var grid_field_id = $('.childData .prefillergrid_field_id').val();
            var autoFiled = $('#dynamicAutoFieldId').val();

            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketBookManager/designDocket/savePrefillerManagerChild')}}",
                data:{docketprefillerid:prefillerManagerId,index:index,rootId:rootId,value:value,isOpen:1,type:0,grid_field_id:grid_field_id,field_id:field_id,isDependent:isDependent},
                success:function (response) {
                    $(".gridPrefillerShow").html(response);
                    $("#addPrefillerManagerValue").modal('hide');
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                    console.log(autoFiled == 1)

                    if(autoFiled == 1){
                        $('.defaultgridCheckMark').css('display','none')
                    }else{
                        $('.defaultgridCheckMark').css('display','')
                    }

                }
            });
        });


        $(document).on('click','#deleteprefillerManagerLabel',function(){
            var id = $(this).attr('data-id');
            var grid_field_id = $("#grid_ids").val()
            var field_id = $("#grid_docket_field_id").val()
            var isDependent = $("#isDependentData").val();
            var docketPrefillerManagerid = $(this).attr('data-docketprefillermanagerid')
            var autoFiled = $('#dynamicAutoFieldId').val();
            $.ajax({
                type:'post',
                url:"{{url('dashboard/company/docketBookManager/designDocket/deleteprefillerManagerLabel')}}",
                data:{id:id,docketprefillermanagerid:docketPrefillerManagerid,type:0,isOpen:1,grid_field_id:grid_field_id,field_id:field_id,isDependent:isDependent},
                success:function (response) {
                    $(".gridPrefillerShow").html(response);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                    if(autoFiled == 1){
                        $('.defaultgridCheckMark').css('display','none')
                    }else{
                        $('.defaultgridCheckMark').css('display','')
                    }

                }

            })
        })


        $(document).on('change','.selectAutoCell',function(){
            var element = $("option:selected", this);
            var index = element.attr('indexcell')
            var gridFieldId = element.attr('gridid')
            var docketFieldId = element.attr('docketField')
            var linkGridFieldId = $(this).val()
            var isDependent = $('#isDependentData').val()
            var prefiller_id = $( ".saveDocketPrefillerManager option:selected" ).val();


            $.ajax({
                type: "POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridAutoPrefiller')}}",
                data: {'index': index, 'gridFieldId': gridFieldId, 'linkGridFieldId': linkGridFieldId,'docketFieldId':docketFieldId,'isDependent':isDependent,'prefiller_id':prefiller_id},
                success: function (response) {
                    var id = '#prefillerValueWrapper'+gridFieldId;
                    var autoHeight = $(id).children('table').children('tbody').children('tr:first-child');
                    autoHeight.replaceWith(response);

                    var showvalues = $("input[name^='showGridField']").map(function (idx, ele) {
                        return $(ele).val();
                    }).get();
                    console.log(showvalues);
                    var j;
                    for (j = 0; j < showvalues.length; j++) {
                        var data = '.showhideprefillerbtn'+showvalues[j]
                        $(data).css('display','')
                    }

                    var hidevalues = $("input[name^='hideGridField']").map(function (idx, ele) {
                        return $(ele).val();
                    }).get();
                    console.log(hidevalues);
                    var i;
                    for (i = 0; i < hidevalues.length; i++) {
                        var datas = '.showhideprefillerbtn'+hidevalues[i]
                        $(datas).css('display','none')
                    }

                }
            });
        })


        $(document).ready(function() {
            $('#setPrefiller').on('show.bs.modal', function(e) {
                var is_dependent_data = $(e.relatedTarget).data('is_dependent_data');
                var field_id = $(e.relatedTarget).data('field_id');
                var docket_id = $(e.relatedTarget).data('docket_id');
                var isopen = 1;
                $.ajax({
                    type:'post',
                    url:"{{url('dashboard/company/docketBookManager/designDocket/savePrefillerData')}}",
                    data:{field_id:field_id,is_dependent_data:is_dependent_data,docket_id:docket_id,isopen:isopen},
                    success:function(response){
                        $(".prefillerShow").html(response);
                        if(is_dependent_data ==1){
                            var type = $( ".saveDocketFieldPrefillerManager option:selected" ).attr('datatype');
                            if(type == 0){
                                $('.editabledocketgridprefiller').editable({
                                    mode:"inline"
                                });
                                $('.editabledocketprefiller').editable({
                                    mode:"inline"
                                });
                            }else{
                                $('#deleteprefillerManagerLabel').remove()
                                $('.btnprefiller').remove()
                            }
                        }else{
                            $('.editabledocketgridprefiller').editable({
                                mode:"inline"
                            });
                            $('.editabledocketprefiller').editable({
                                mode:"inline"
                            });
                        }

                        $(document).ready(function(){
                            $('.prefillerLinkFilter').multiselect({
                                enableFiltering: true,
                                enableCaseInsensitiveFiltering: true,
                                buttonWidth:'100%',
                                enableClickableOptGroups: false,
                                checkboxName: false,
                            });
                        });


                        $(".addNormalFilterLinkPrefiller").click(function(){
                            var docketFieldId = $(this).attr('data-fieldId')
                            $.ajax({
                                type:'post',
                                url:"{{url('dashboard/company/docketBookManager/designDocket/dynamicFilterField')}}",
                                data:{docketFieldId:docketFieldId},
                                success:function(response){
                                    $('#dynamicNormalPrefillerFilterField').append(response)
                                }
                            })
                        });

                        $(document).on('click', '.removeNormalFilterLinkPrefiller', function(){
                            var linkprefillerfilter = $(this).attr('data-linkprefillerfilter');

                                $.ajax({
                                    type:'post',
                                    url:"{{url('dashboard/company/docketBookManager/designDocket/removeDynamicFilterField')}}",
                                    data:{linkprefillerfilter:linkprefillerfilter},
                                    success:function(response){
                                    }
                                })
                                $(this).parents('#dynamicNormalPrefillerFilterField div').remove();


                        });

                    }



                })





            });


        });

        $(document).ready(function() {
            $('#addNewParentPrefillermodel').on('show.bs.modal', function(e) {
                $('.prefillerErrorMessage').css('display','none')
                var is_dependent_data = $(e.relatedTarget).data('is_dependent');
                var field_id = $(e.relatedTarget).data('docketfieldid');
                var docket_id = $(e.relatedTarget).data('docket_id');
                $('.prefillerParentData .newValue').val('')
                $('#prefillerdocket_id').val(docket_id)
                $('#prefilleris_dependent_data ').val(is_dependent_data)
                $('#prefillerfield_id').val(field_id)
                var prefillerIsInteger = $('.inDependentPrefillerDataType').val();
                if (prefillerIsInteger == 0){
                    $(".prefillerParentData .newValue").prop("type", "text");
                    $(".prefillerParentData .newValue").prop('maxlength',50);
                    $('.messageForPrefiller').text('Maximum 50 characters')
                }else if(prefillerIsInteger == 1){
                    $(".prefillerParentData .newValue").prop("type", "number");
                    $(".prefillerParentData .newValue").prop('maxlength',50);
                    $('.messageForPrefiller').text('Maximum 50 characters')
                }else if(prefillerIsInteger == 2){
                    $(".prefillerParentData .newValue").prop("type", "email");
                    $(".prefillerParentData .newValue").prop('maxlength',300);
                    $('.messageForPrefiller').text('Please enter valid Email')
                }


            });
        });

        $(document).on('click','#addNewParentPrefiller',function(){
            var is_dependent_data = $('#prefilleris_dependent_data ').val()
            var field_id = $('#prefillerfield_id').val();
            var docket_id = $('#prefillerdocket_id').val()
            var value = $('.prefillerParentData .newValue').val()
            var dataType = $('.prefillerShow .inDependentPrefillerDataType').val()

            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/addNewParentPrefiller')}}",
                data: {is_dependent_data:is_dependent_data,field_id:field_id,docket_id:docket_id,value:value,dataType:dataType},
                success: function (response) {
                    if(response['status'] == false){
                        $('.prefillerErrorMessage').css('display','')
                        $('.prefillerErrorMessage').text(response['message'])
                    }else{
                        $('#addNewParentPrefillermodel').modal('hide');
                        $(".prefillerShow").html(response);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }
                }
            })
        })

        $(document).ready(function() {
            $('#updateNewPrefillerneValue').on('show.bs.modal', function(e) {
                $('.prefillerErrorMessage').css('display','none')
                var root = $(e.relatedTarget).data('id');
                var field_id = $(e.relatedTarget).data('docketfieldid');
                var docket_id = $(e.relatedTarget).data('docket_id');
                var index = $(e.relatedTarget).data('index');
                var is_dependent_data = $(' #isDependentData').val()
                $('.prefillerChildData .newValue').val('')
                $('#childprefillerdocket_id').val(docket_id)
                $('#childprefilleris_dependent_data').val(is_dependent_data)
                $('#childprefillerfield_id').val(field_id)
                $('#childprefillerroot').val(root)
                $('#childprefillerindex').val(index)

                var prefillerIsInteger = $('.inDependentPrefillerDataType').val();
                if (prefillerIsInteger == 0){
                    $(".prefillerChildData .newValue").prop("type", "text");
                    $(".prefillerChildData .newValue").prop('maxlength',50);
                    $('.messageForPrefiller').text('Maximum 50 characters')
                }else if(prefillerIsInteger == 1){
                    $(".prefillerChildData .newValue").prop("type", "number");
                    $(".prefillerChildData .newValue").prop('maxlength',50);
                    $('.messageForPrefiller').text('Maximum 50 characters')
                }else if(prefillerIsInteger == 2){
                    $(".prefillerChildData .newValue").prop("type", "email");
                    $(".prefillerChildData .newValue").prop('maxlength',300);
                    $('.messageForPrefiller').text('Please enter valid Email')
                }

            });
        });
        $(document).on('click','#addNewChildPrefiller',function(){
            var is_dependent_data = $('#childprefilleris_dependent_data ').val()
            var field_id = $('#childprefillerfield_id').val();
            var docket_id = $('#childprefillerdocket_id').val()
            var value = $('.prefillerChildData .newValue').val()
            var root = $('#childprefillerroot').val()
            var index = $('#childprefillerindex').val()
            var dataType = $('.prefillerShow .inDependentPrefillerDataType').val()

            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/addNewChildPrefiller')}}",
                data: {is_dependent_data:is_dependent_data,field_id:field_id,docket_id:docket_id,value:value,root:root,index:index,dataType:dataType},
                success: function (response) {
                    if(response['status'] == false){
                        $('.prefillerErrorMessage').css('display','')
                        $('.prefillerErrorMessage').text(response['message'])
                    }else {
                        $('#updateNewPrefillerneValue').modal('hide');
                        $(".prefillerShow").html(response);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }
                }
            })
        })

        $(document).on('click','#deleteprefillerLabel', function(){
            var field_id = $(this).attr('data-docketfieldid')
            var id = $(this).attr('data-id')
            var is_dependent_data = $(' #isDependentData').val()
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/deletePrefillerLabels')}}",
                data: {is_dependent_data:is_dependent_data,field_id:field_id,docket_id:'{{$tempDocket->id}}',id:id},
                success: function (response) {
                    $(".prefillerShow").html(response);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });
                }
            })

        });

        $(document).on('click','#clearAllPrefiller', function(){
            var docketFieldId = $(this).attr('docketFieldId')
            var is_dependent_data = $(' #isDependentData').val()
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/clearAllPrefiller')}}",
                data: {is_dependent_data:is_dependent_data,field_id:docketFieldId,docket_id:'{{$tempDocket->id}}'},
                success: function (response) {
                    $(".prefillerShow").html(response);
                }
            })

        })


        $(document).on('click', '#normalPrefillerInDependent', function () {
            $('.spinnerCheckgrid').css('display','');
            var depend = document.getElementById('normalPrefillerDependent');
            var inDepend = document.getElementById('normalPrefillerInDependent');
            var prefillerEcowise = document.getElementById('normalPrefillerEcowise');
            var docket_id = $(this).attr('docket_id')
            var field_id = $(this).attr('docketfieldid')
            depend.checked = false;
            depend.disabled=false;
            inDepend.checked = true;
            inDepend.disabled = true;
            prefillerEcowise.checked = false;
            prefillerEcowise.disabled = false;
            $("#normalPrefillerInDependent").val(0);
            $.ajax({
                type: "post",
                url:"{{url('dashboard/company/docketBookManager/designDocket/savePrefillerData')}}",
                data:{field_id:field_id,is_dependent_data:0,docket_id:docket_id,isopen:0},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updateSetPrefiller').attr('data-is_dependent_data', '0');
                    $(".prefillerShow").html(response);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                }
            });

        });
        $(document).on('click', '#normalPrefillerDependent', function () {
            $('.spinnerCheckgrid').css('display','');
            var depend = document.getElementById('normalPrefillerDependent');
            var inDepend = document.getElementById('normalPrefillerInDependent');
            var prefillerEcowise = document.getElementById('normalPrefillerEcowise');
            var docket_id = $(this).attr('docket_id')
            var field_id = $(this).attr('docketfieldid')
            depend.checked = true;
            depend.disabled=true;
            prefillerEcowise.checked = false;
            prefillerEcowise.disabled = false;
            inDepend.checked = false;
            inDepend.disabled = false;
            $("#normalPrefillerDependent").val(1);
            $.ajax({
                type: "post",
                url:"{{url('dashboard/company/docketBookManager/designDocket/savePrefillerData')}}",
                data:{field_id:field_id,is_dependent_data:1,docket_id:docket_id,isopen:0},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updateSetPrefiller').attr('data-is_dependent_data', '1');
                    $(".prefillerShow").html(response);
                    var type = $( ".saveDocketFieldPrefillerManager option:selected" ).attr('datatype');
                    if(type == 0){
                        $('.editabledocketgridprefiller').editable({
                            mode:"inline"
                        });
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }else{
                        $('#deleteprefillerManagerLabel').remove()
                        $('.btnprefiller').remove()
                    }
                }
            });
        });

        $(document).on('click', '#normalPrefillerEcowise', function () {
            $('.spinnerCheckgrid').css('display','');
            var depend = document.getElementById('normalPrefillerDependent');
            var inDepend = document.getElementById('normalPrefillerInDependent');
            var prefillerEcowise = document.getElementById('normalPrefillerEcowise');
            var docket_id = $(this).attr('docket_id')
            var field_id = $(this).attr('docketfieldid')
            depend.checked = false;
            depend.disabled=false;
            inDepend.checked = false;
            inDepend.disabled = false;
            prefillerEcowise.checked = true;
            prefillerEcowise.disabled = true;
            $("#normalPrefillerInDependent").val(2);
            $.ajax({
                type: "post",
                url:"{{url('dashboard/company/docketBookManager/designDocket/savePrefillerData')}}",
                data:{field_id:field_id,is_dependent_data:2,docket_id:docket_id,isopen:0},
                success:function(response){
                    $('.spinnerCheckgrid').css('display','none');
                    $('.updateSetPrefiller').attr('data-is_dependent_data', '0');
                    $(".prefillerShow").html(response);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                }
            });

        });

        $(document).on('change','.saveDocketFieldPrefillerManager', function(){
            var id = $(this).val()
            var field_id = $('#prefiller_docket_field_id').val()
            var type = $("option:selected",this).attr('datatype')
            $.ajax({
                type:"POST",
                url:"{{url('dashboard/company/docketBookManager/designDocket/saveDocketFieldPrefillerManager')}}",
                data:{'prefillerManagerId':id,'docket_id':'{{ $tempDocket->id }}','isopen':0,'is_dependent_data':1,'field_id':field_id},
                success: function(response){
                    $(".prefillerShow").html(response);
                    if(type == 0){
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }else{
                        $('#deleteprefillerManagerLabel').remove()
                        $('.btnprefiller').remove()
                    }

                }
            })
        })

        $(document).on('click','#deleteprefillerManagerLabelchild',function () {
            var docketprefillermanagerid = $(this).attr('data-docketprefillermanagerid')
            var id = $(this).attr('data-id')
            var field_id = $('#prefiller_docket_field_id').val()
            var is_dependent_data = $('#isDependentData').val()
            $.ajax({
                type:'post',
                url:"{{url('dashboard/company/docketBookManager/designDocket/deleteprefillerManagerLabelchild')}}",
                data:{id:id,isopen:0,docketprefillermanagerid:docketprefillermanagerid,docket_id:'{{$tempDocket->id}}',field_id:field_id,is_dependent_data:is_dependent_data},
                success:function (response) {
                    $(".prefillerShow").html(response);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                }
            })
        });

        $(document).on('change','.defaultCheckMarkNormal',function(){
            var value = $(this).val();
            var docketFieldId = $(this).attr('items');
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/prefillerDefaultCheckMark')}}",
                data:{'value':value , 'docketFieldId':docketFieldId , 'docket_id':'{{ $tempDocket->id }}' ,'checked' :checked},
                success: function (response) {
                    if (response.status == true){
                        // location.reload(true);
                    }else {

                    }

                }
            });

        })

        $('#addChidPrefillerManager').on('show.bs.modal', function(e) {
            $('.prefillerErrorMessage').css('display','none')

            $(".managerChildData .gridprefillerInvValue").val('')
            var root = $(e.relatedTarget).data('id');
            var index = $(e.relatedTarget).data('index');
            var docketprefillermanagerid = $(e.relatedTarget).data('docketprefillermanagerid');
            var field_id = $('#prefiller_docket_field_id').val();
            var isDependentData = $('#isDependentData').val();

            $('.managerChildData .parentManagerId').val(docketprefillermanagerid)
            $('.managerChildData .prefillerIndexId').val(index)
            $('.managerChildData .prefillerRootId').val(root)
            $('.managerChildData .prefiller_field_id').val(field_id)
            $('.managerChildData .isDependentData').val(isDependentData)
            var prefillerIsInteger = $('.dependentPrefillerDataType').val();

            if (prefillerIsInteger == 0){
                $(".managerChildData .gridprefillerInvValue").prop("type", "text");
                $(".managerChildData .gridprefillerInvValue").prop('maxlength',50);
                $('.messageForPrefiller').text('Maximum 50 characters')
            }else if(prefillerIsInteger == 1){
                $(".managerChildData .gridprefillerInvValue").prop("type", "number");
                $(".managerChildData .gridprefillerInvValue").prop('maxlength',50);
                $('.messageForPrefiller').text('Maximum 50 characters')
            }else if(prefillerIsInteger == 2){
                $(".managerChildData .gridprefillerInvValue").prop("type", "email");
                $(".managerChildData .gridprefillerInvValue").prop('maxlength',300);
                $('.messageForPrefiller').text('Please enter valid Email')
            }

        });

        $('#saveChildManagerPrefiller').click(function(){
            var prefillerManagerId =  $('.managerChildData .parentManagerId').val()
            var index =  $('.managerChildData .prefillerIndexId').val()
            var root_id  =  $('.managerChildData .prefillerRootId').val()
            var field_id = $('.managerChildData .prefiller_field_id').val()
            var is_dependent_data =  $('.managerChildData .isDependentData').val()
            var value =  $('.managerChildData .gridprefillerInvValue').val()
            var dataType =  $('.dependentPrefillerDataType').val()


            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketBookManager/designDocket/saveChildManagerPrefiller')}}",
                data:{value:value,prefillerManagerId:prefillerManagerId,isopen:0,index:index,docket_id:'{{$tempDocket->id}}',rootId:root_id,field_id:field_id,is_dependent_data:is_dependent_data,dataType:dataType},
                success:function (response) {
                    if(response['status'] == false){
                        $('.prefillerErrorMessage').css('display','')
                        $('.prefillerErrorMessage').text(response['message'])
                    }else{
                        $(".prefillerShow").html(response);
                        $('#addChidPrefillerManager').modal('hide')
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }

                }
            })


        });



        //end newOne


        $(document).on('click','.gridPrefillerLinkCheck',function () {
            $(".spinnerCheck").css('display','block')
            var is_integer =  $('#gridprefillerTypechecks').val();
            console.log(is_integer);
            var checked = 0;

            if ($(this).is(':checked')){
                checked = 1;
            } else {
                checked = 0;
            }

            if (checked == 1){
                $('#gridvalueprefiller').val('');
                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/prefillerCheckMark')}}",
                    data:{'is_integer':is_integer },
                    success: function (response) {
                        $(".spinnerCheck").css('display','none');
                        $(".gridappenddatabytype").html(response.finalView).show();
                        $('.gridappendvaluetype').hide()

                    }
                });
            }else if (checked == 0){
                $(".spinnerCheck").css('display','none');
                $('#gridvalueprefiller').val(null);
                $('.gridappendvaluetype').show()
                $(".gridappenddatabytype").hide()
            }
        });

        $(document).on('click','#saveGridPrefiller',function () {
            $(".spinnerCheckgrid").css('display','block');
            var prefillerlabels = $("#gridvalueprefiller").val();
            var docketFieldId = $("#grid_docket_field_id").val();
            var parentId = 0;
            var valueCategoryId = $("#typeValue").val();
            var isInteger = $('#gridprefillerTypechecks').val();
            var index = 0;
            var gridId = $('#grid_ids').val()
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveGridPrefiller')}}",
                data: {
                    docket_field_id: docketFieldId,
                    value: prefillerlabels,
                    parent_id: parentId,
                    value_category_id: valueCategoryId,
                    isInteger: isInteger,
                    index: index,
                    gridId: gridId,

                },
                success: function (response) {
                    $(".setGridPrefillers").html(response);
                    $(".spinnerCheckgrid").css('display','none');
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });
                }
            })
        });


        $(document).ready(function() {
            $('.editabledocketgridprefiller').editable({
                mode:"inline",
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


        $(document).on('change','.defaultgridCheckMark',function () {
            var value = $(this).val();
            var docketFieldId = $(this).attr('items');
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/gridprefillerDefaultCheckMark')}}",
                data:{'value':value , 'docketFieldId':docketFieldId , 'docket_id':'{{ $tempDocket->id }}' ,'checked' :checked},
                success: function (response) {
                    if (response.status == true){
                        // location.reload(true);
                    }else {

                    }

                }
            });

        });

        $(document).on('change','.defaultAutoPrefiller',function () {
            var value = $(this).val();
            var docketFieldId = $(this).attr('items');
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/checkdefaultAutoFilledPrefiller')}}",
                data:{'value':value , 'docketGridFieldId':docketFieldId , 'docket_id':'{{ $tempDocket->id }}' ,'checked' :checked},
                success: function (response) {
                    if (response.status == true){
                        var data = document.getElementById("prefillerValueWrapper"+docketFieldId);
                        var defaultcheckMark = data.getElementsByClassName('defaultAutoPrefiller');
                        for (var i = 0; i < defaultcheckMark.length; i++) {
                            if(value != defaultcheckMark[i].defaultValue){
                                defaultcheckMark[i].checked = false;
                            }
                        }
                    }
                }
            });

        });



        $(document).on('click','.gridsprefillerLinkChecksingle',function () {

            $(".spinnerCheckss").css('display','block');
            var is_integer =  $('#gridprefillerindvintegertype').val();
            var checked = 0;
            if ($(this).is(':checked')){
                checked = 1;
            } else {
                checked = 0;
            }

            if (checked == 1){
                $('#gridprefillerInvValue').val('');
                $.ajax({
                    type:"POST",
                    url: "{{url('dashboard/company/prefillerCheckMarkSingle')}}",
                    data:{'is_integer':is_integer },
                    success: function (response) {
                        $(".spinnerCheckss").css('display','none');
                        $(".gridsappenddatabytypes").html(response.finalView).show();
                        $('.gridsappendvaluetypes').hide()

                    }
                });
            }else if (checked == 0){
                $(".spinnerCheckss").css('display','none');
                $('#gridprefillerInvValue').val('');
                $('.gridsappendvaluetypes').show()
                $(".gridsappenddatabytypes").hide()
            }

        });







        $(document).ready(function() {
            $('#duplicateGrid').on('show.bs.modal', function (e) {
                var fieldId = $(e.relatedTarget).data('id');
                var duplicate_category_id = $(e.relatedTarget).data('category_id');
                $('#duplicate_docket_id').val(fieldId)
                $('#duplicate_category_id').val(duplicate_category_id)


            });
        });


        $(document).on('click','#saveDuplicateGrid', function () {
            var fieldId = $('#duplicate_docket_id').val();
            var duplicate_category_id = $('#duplicate_category_id').val();
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketBookManager/designDocket/duplicateGrid')}}",
                data:{'docket_field_id':fieldId, 'docket_id': '{{$tempDocket->id}}' , 'category_id': duplicate_category_id },
                success: function (response) {

                    document.getElementById('elementTemplateBottom').scrollIntoView({behavior: 'smooth'});

                    $.ajax({
                        type: "GET",
                        url: "{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                        success: function (response) {
                            $("#mobileviewHtml").html(response);
                            $.material.init();

                            var colorPicker = $('.colorpicker').colorpicker({
                                colorSelectors: {
                                    'black': '#000000',
                                    'red': '#FF0000',
                                    'default': '#777777',
                                    'primary': '#337ab7',
                                    'success': '#5cb85c',
                                    'info': '#5bc0de',
                                    'warning': '#f0ad4e',
                                    'danger': '#d9534f',

                                },

                            });
                            $('.colorpicker-hue').css('display','none');
                            $('.colorpicker-saturation').css('display','none');
                            $('.colorpicker-alpha').css('display','none');
                            $('.colorpicker-color').css('display','none');
                            $(".collourPallet").bind("change", function () {

                                console.log($(this).val());
                                console.log($(this).attr('colorYesNoId'));
                                $.ajax({
                                    type: "POST",
                                    data: {id: $(this).attr('colorYesNoId'), colour: $(this).val()},
                                    url: "{{ url('dashboard/company/docketBookManager/UpdateSubDocketColour/') }}",
                                    success: function (response) {
                                        if (response['status'] == true) {
                                            $('.iconBackground' + response['id']).css('background', response['colour'])
                                        }

                                    }
                                });

                                var el = $('.colorpicker-with-alpha');
                                el.addClass('colorpicker-hidden');
                                el.removeClass('colorpicker-visible');
                            });

                        }


                    })

                    $.when($('#sortable').append(response)).done(function () {
                        $('.editable').editable({});
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('.docketFieldNumbereditable').editable({});

                    });

                    $("#duplicateGrid").modal('hide');

                },


            });

        });

        $(document).on('click','.editAdvanceHeader',function () {
            var id = $(this).attr('category_id');
            var matchid = '#editedAHView-'+id;
            var normal =  '#normalAHView-'+id;
            $(matchid).css({
                "opacity":"0",
                "display":"block",
            }).show().animate({opacity:1})
            $(normal).css({
                "opacity":"0",
                "display":"none",
            }).hide().animate({opacity:1})

            CKEDITOR.instances['editor'+id].setData(  $('#displayAdvanceHeaders'+id).html());
        });

        $(document).on('click','.saveAdvanceHeader', function () {
            var id = $(this).attr('id');
            var message =  CKEDITOR.instances['editor'+id].getData();
            console.log(message)

            var matchid = '#editedAHView-'+id;
            var normal =  '#normalAHView-'+id;
            $.ajax({
                type: "POST",
                data: {field_id: id, docket_id:'{{$tempDocket->id}}',message: message },
                url: "{{ url('dashboard/company/docketBookManager/saveAdvanceHeader/') }}",
                success: function (response) {
                    $('#displayAdvanceHeader'+id).html(response['data'])
                    $('#displayAdvanceHeaders'+id).html(response['data'])

                    $(matchid).css({
                        "opacity":"0",
                        "display":"none",
                    }).hide().animate({opacity:1});
                    $(normal).css({
                        "opacity":"0",
                        "display":"block",
                    }).show().animate({opacity:1});
                }
            });

        })

        $('#exportMapping').on('show.bs.modal', function (e) {
            $(".spinnerCheck").css('display','block');
            var fieldId = $(e.relatedTarget).data('id');
            var type = $(e.relatedTarget).data('type');
            $.ajax({
                type: "POST",
                data: {type:type,fieldId:fieldId},
                url: "{{ url('dashboard/company/docketBookManager/designDocket/exportMapping')}}",
                success: function (response) {
                    $(".exportMappingView").html(response);
                    $(".spinnerCheck").css('display','none');
                    $('.editableExport').editable({
                        mode:"inline"
                    })
                    // $("#clearAllGridPrefillerModal").modal('hide');
                }
            });
        })

        $(document).on('click','.exportMappingCheckbox', function () {
            var id = $(this).attr("fieldId");
            var type = $(this).attr("dataType");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/exportMappingCheckbox/') }}',
                data: {"value": checked,"id":id, "type": type},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });


        $('#saveExportMapping').on('show.bs.modal', function (e) {
            $(".spinnerCheck").css('display','block');
            var docketId = $(e.relatedTarget).data('docketid');
            $.ajax({
                type: "POST",
                data: {docketId:docketId},
                url: "{{ url('dashboard/company/docketBookManager/designDocket/viewExportMappingField/')}}",
                success: function (response) {
                    $(".designViewExportMapping").html(response);
                    $(".spinnerCheck").css('display','none');
                }
            });
        })


        $(document).on('click','.addExportMappingField', function () {
            var docketId = $(this).attr('docketId')
            var categoryId = $(this).attr('categoryId')
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/saveExportMappingField/') }}',
                data:{"docketId": docketId,"categoryId":categoryId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }else{
                        $(".designViewExportMapping").html(msg);
                    }
                }
            })


        })

        $(document).on('click','#saveDocketConstant',function () {
            var value = $( ".constantDocketValue option:selected" ).val();
            $.ajax({
                type: "Post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/saveDocketConstant')}}",
                data: {"value": value, "docket_id":'{{ $tempDocket->id }}'},
                success: function (response) {
                    document.getElementById('elementTemplateBottom').scrollIntoView({behavior: 'smooth'});
                    $.when($('#sortable').append(response)).done(function () {
                        $('.editable').editable(
                            {
                                success: function (response) {
                                    $.ajax({
                                        type: "GET",
                                        url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                                        success:function (response) {
                                            $("#mobileviewHtml").html(response);

                                        }
                                    });
                                },
                                validate: function(value) {
                                    if($.trim(value) == '') {
                                        return 'The value field is required';

                                    }
                                }
                            });
                        $('.docketFieldNumbereditable').editable({});
                    });
                    $('#docketConstant').modal('hide');
                }
            });


        });

        $(document).on('click','#showDeletDocket',function () {
            $(".spinnerForDeleteField").css('display','block');
            var checked = 0;
            if ($("#showDeletDocket").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            var url = window.location.href;
            if(typeof(url.split('#')[1]) != "undefined" ){
                var newUrl =  url.split('#')[1]+"?showDelete="+checked
                history.pushState({}, null, newUrl);
            }else{
                if(typeof(url.split('?')[1]) == "undefined"){
                    var newUrl = url.split('?')[0]+"?showDelete="+checked;
                    history.pushState({}, null, newUrl);
                }else {
                    var newUrl = url.split('?')[0] + "?showDelete=" + checked;
                    history.pushState({}, null, newUrl);
                }
            }

            $.ajax({
                type: "post",
                data: {'isShow':window.location.search.split('=')[1], 'docket_id':'{{$tempDocket->id}}'},
                url:  "{{url('dashboard/company/docketBookManager/designDocket/showHideDeletedDocketElement')}}",
                success: function (response) {
                    $('.componentScroll').html(response)
                    $(".spinnerForDeleteField").css('display','none');
                    $('.docketFieldNumbereditable').editable({});
                    $('.editable').editable({});
                    $('.editableExport').editable({
                        mode:"inline"
                    })
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });
                }
            });
        });

        $('#undoDocketField').on('show.bs.modal',function (e){
            var field_id = $(e.relatedTarget).data('id');
            $('.undofieldId').val(field_id);
        });

        $(document).on('click','#submitUndoData',function () {
            $(".spinnerForDeleteField").css('display','block');
            var fieldid =  $('.undofieldId').val();

            $.ajax({
                type: 'POST',
                url :  "{{url('dashboard/company/docketBookManager/designDocket/undoDocketField')}}",
                data: {"fieldid": fieldid, "docket_id":'{{ $tempDocket->id }}','isShow':window.location.search.split('=')[1]},
                success: function (response) {

                    $.ajax({
                        type: "GET",
                        url:"{{url('dashboard/company/docketBookManager/mobileView/'.$tempDocket->id) }}",
                        success:function (response) {
                            $("#mobileviewHtml").html(response);
                        }
                    });

                    $('.componentScroll').html(response)

                    $('.docketFieldNumbereditable').editable({});
                    $('.editable').editable({});
                    $('.editableExport').editable({
                        mode:"inline"
                    })
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });

                    $('#undoDocketField').modal('hide');
                    $(".spinnerForDeleteField").css('display','none');

                }
            })

        });


        $(document).on('click','#hidePrefix',function () {
            var checked = 0;
            if ($("#hidePrefix").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type: "post",
                data: {'isShow':checked, 'docket_id':'{{$tempDocket->id}}'},
                url:  "{{url('dashboard/company/docketBookManager/designDocket/showHideDocketPrefix')}}",
                success: function (response) {

                }
            });
        });

        $(document).on('click','#showDocketNumber',function () {
            var checked = 0;
            if ($("#showDocketNumber").is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "post",
                data: {'isShow':checked, 'docket_id':'{{$tempDocket->id}}'},
                url:  "{{url('dashboard/company/docketBookManager/designDocket/showHideDocketNumber')}}",
                success: function (response) {

                }
            });
        });


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

        $("#updateAprovalMethod").on("hidden.bs.modal", function () {
            if('{{$tempDocket->docketApprovalType}}' == 0){
                var yesDocket = document.getElementById('buttonAuthorisess');
                var yesDocketaa = document.getElementById('buttonApprovess');
                var noapproved = document.getElementById('buttonNoApprovess');
                noapproved.checked = false;
                yesDocket.checked = false;
                yesDocketaa.disabled=true;
                yesDocketaa.checked=true;
                yesDocket.disabled = false;
                noapproved.disabled = false;
                $("#docketApprovalValue").val(0);

            }else if('{{$tempDocket->docketApprovalType}}' == 1){
                var noDocket = document.getElementById('buttonApprovess');
                var noDocketaa = document.getElementById('buttonAuthorisess');
                var noapproved = document.getElementById('buttonNoApprovess');
                noapproved.checked = false;
                noDocket.checked = false;
                noDocketaa.disabled=true;
                noDocketaa.checked=true;
                noDocket.disabled = false;
                noapproved.disabled = false;
                $("#docketApprovalValue").val(1);
            } else{
                var noapprove = document.getElementById('buttonApprovess');
                var noapproved = document.getElementById('buttonNoApprovess');
                var noapproves = document.getElementById('buttonAuthorisess');
                noapprove.checked = false;
                noapproves.checked = false;
                noapproved.disabled=true;
                noapproved.checked=true;
                noapproves.disabled = false;
                noapprove.disabled = false;
                $("#docketApprovalValue").val(2);
            }

        });

        $(document).on('click','#clearAllExportRule', function(){
            $.ajax({
                type:"post",
                url: '{{url('dashboard/company/docketBookManager/clearAllExportRule')}}',
                data: {'docket_id':'{{$tempDocket->id}}'},
                success:function(response){
                    $('#clearExportRule').modal('hide');
                }
            });

        });


        $(".gridIsEmailSubject").on("click", function () {
            var isEmailSubjectdDocketFieldId = $(this).attr("data");
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/docketBookManager/designDocket/isEmailSubjectdDocketGridFieldId/') }}',
                data: {"data": checked, "requiredDocketFieldId": isEmailSubjectdDocketFieldId},
                success: function (msg) {
                    if (msg == "Invalid attempt!") {
                        alert(msg);
                    }
                }
            });
        });


        $(document).on('click','.gridManualTimeFormat',function () {

            $('#gridManualTimeFormatModal').modal('show');
            var fieldId = $(this).attr('data-fieldid');
            var gridFieldId = $(this).attr('data-fieldgridid');
            var time_format = $(this).attr('data-timeformat');
            console.log(time_format);

            if(time_format == "Hours&Minutes"){
                $('.girdTimeFormat input').eq(2).prop( "checked", true );
                $('.girdTimeFormat input').eq(2).prop( "disabled", true );
                $('.girdTimeFormat input').eq(2).val(time_format);
                $('.girdTimeFormat input').eq(3).prop( "checked", false );
                $('.girdTimeFormat input').eq(3).prop( "disabled", false );

            }else if(time_format == "Decimal"){
                $('.girdTimeFormat input').eq(3).prop( "checked", true );
                $('.girdTimeFormat input').eq(3).prop( "disabled", true );
                $('.girdTimeFormat input').eq(3).val(time_format);
                $('.girdTimeFormat input').eq(2).prop( "checked", false );
                $('.girdTimeFormat input').eq(2).prop( "disabled", false );
            }



            $('.timeFormatGridFieldId').val(gridFieldId);
            $('.timeFormatFieldId').val(fieldId);
        });


        function checkTimeFormat(data){
            var name = document.getElementsByName(data.name);
            for (var i = 0; i < name.length; i++) {
                if(name[i].checked){
                    name[i].disabled = false;
                    name[i].checked = false;
                }
            }
            data.disabled = true;
            data.checked = true;

        }

        $(document).on('click','#updateGridManualTimeFormat',function () {
            var docket_field_id = $('.girdTimeFormat .timeFormatFieldId').val();
            var docket_grid_field_id = $('.girdTimeFormat .timeFormatGridFieldId').val();
            var checked_value =  $('.girdTimeFormat input:checked').val()
            $.ajax({
                type:'post',
                url:'{{url('dashboard/company/docketBookManager/designDocket/updategridTimeFormat')}}',
                data: {docket_field_id:docket_field_id,docket_grid_field_id:docket_grid_field_id,checked_value:checked_value},
                success:function(response){
                    if(response['status'] == true){
                        $('#gridManualTimeFormatModal').modal('hide');
                        $('.gridManualTimeFormat').map(function (key, value) {

                            if(value.getAttribute('data-fieldgridid') == docket_grid_field_id){
                                value.setAttribute('data-timeformat',response['value'])

                            }


                        })

                    }

                }
            })



        })





        $(document).on('click','.manualTimeFormat',function () {

            $('#manualTimeFormatModal').modal('show');
            var fieldId = $(this).attr('data-fieldid');
            var time_format = $(this).attr('data-timeformat');
            console.log(time_format);

            if(time_format == "Hours&Minutes"){
                $('.timeFormat input').eq(1).prop( "checked", true );
                $('.timeFormat input').eq(1).prop( "disabled", true );
                $('.timeFormat input').eq(1).val(time_format);
                $('.timeFormat input').eq(2).prop( "checked", false );
                $('.timeFormat input').eq(2).prop( "disabled", false );
            }else if(time_format == "Decimal"){
                $('.timeFormat input').eq(2).prop( "checked", true );
                $('.timeFormat input').eq(2).prop( "disabled", true );
                $('.timeFormat input').eq(2).val(time_format);
                $('.timeFormat input').eq(1).prop( "checked", false );
                $('.timeFormat input').eq(1).prop( "disabled", false );
            }
            $('.timeFormatFieldId').val(fieldId);
        });


        function checkManualTimeFormat(data){
            var name = document.getElementsByName(data.name);
            for (var i = 0; i < name.length; i++) {
                if(name[i].checked){
                    name[i].disabled = false;
                    name[i].checked = false;
                }
            }
            data.disabled = true;
            data.checked = true;
        }

        $(document).on('click','#updateManualTimeFormat',function () {
            var docket_field_id = $('.timeFormat .timeFormatFieldId').val();
            var checked_value =  $('.timeFormat input:checked').val()
            $.ajax({
                type:'post',
                url:'{{url('dashboard/company/docketBookManager/designDocket/updateTimeFormat')}}',
                data: {docket_field_id:docket_field_id,docket_id:'{{$tempDocket->id}}',checked_value:checked_value},
                success:function(response){
                    if(response['status'] == true){
                        $('#manualTimeFormatModal').modal('hide');
                        $('.manualTimeFormat').map(function (key, value) {
                            if(value.getAttribute('data-fieldid') == docket_field_id){
                                value.setAttribute('data-timeformat',response['value'])
                            }
                        })
                    }
                }
            })



        })

        $(document).on('click','.updateGridRequired', function(){
            var docketFieldId = $(this).attr('data-docketfieldid')
            var gridFieldId = $(this).attr('data-id')
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }
            $.ajax({
                type:'post',
                url: "{{url('dashboard/company/docketBookManager/designDocket/updateGridRequired')}}",
                data:{docketFieldId:docketFieldId,id:gridFieldId, value: checked},
                success:function(response){

                }
            })


        })

        $(document).on('click','.updateGridPreview', function(){
            var docketFiledId = $(this).attr('data-docketfieldid');
            var gridFieldId = $(this).attr('data-id');
            var checked = 0;
            if($(this).is(':checked')){
                checked = 1;
            }else{
                checked = 0;
            }
            $.ajax({
                type:'POST',
                url:"{{url('dashboard/company/docketBookManager/designDocket/updateGridPreview')}}",
                data:{docketFiledId:docketFiledId,id:gridFieldId, value:checked },
                success:function (response) {
                   if(response.status == true){
                       // $(".updateGridPreview").each(function(key,value){
                       //     if(value.getAttribute('data-id') != gridFieldId){
                       //         $('.gridPreview'+gridFieldId).prop('checked',false);
                       //     }else{
                       //         $('.gridPreview'+gridFieldId).prop('checked',true);
                       //     }
                       // });
                   }else{
                       $(".updateGridPreview").each(function(key,value){
                           if(value.getAttribute('data-id') == gridFieldId){
                               $('.gridPreview'+gridFieldId).prop('checked',false);
                           }
                       });
                   }
                }
            });
        });

        $(document).on('click','.updateGridPdfName', function(){
            var docketFiledId = $(this).attr('data-docketfieldid');
            var gridFieldId = $(this).attr('data-id');
            var checked = 0;
            if($(this).is(':checked')){
                checked = 1;
            }else{
                checked = 0;
            }
            $.ajax({
                type:'POST',
                url:"{{url('dashboard/company/docketBookManager/designDocket/updateGridPdfName')}}",
                data:{docketFiledId:docketFiledId,id:gridFieldId, value:checked },
                success:function (response) {
                    if(response.status == true){

                    }else{
                        $(".updateGridPdfName").each(function(key,value){
                            if(value.getAttribute('data-id') == gridFieldId){
                                $('.gridPdfName'+gridFieldId).prop('checked',false);
                            }
                        });

                    }
                }

            });



        });

        $(document).on('click','.defaultFolder',function(){

            $(".folderSpinnerCheck").css('display','block');
            $('#defaultFolderModal').modal('show');
            var fieldId = $(this).attr('data-fieldid');
            var defaultValue = $(this).attr('data-default');
            $.ajax({
                type:"post",
                data: {fieldId:fieldId,defaultValue:defaultValue},
                url:'{{url('dashboard/company/docketBookManager/designDocket/showDefaultFolder')}}',
                success:function(response){
                    $('.defaultFolderView').html(response)
                    $(".folderSpinnerCheck").css('display','none');
                }
            });
        })

        $(document).on('click','#updateDefaultFolder',function(){
            $(".folderSpinnerCheck").css('display','block');
            var fieldId = $('.folderFieldId').val();
            var defaultValue = $( ".defaultFolderId option:selected" ).val();
            $.ajax({
                type:'post',
                data:{fieldId:fieldId,defaultValue:defaultValue},
                url:'{{url('dashboard/company/docketBookManager/designDocket/updateDefaultFolder')}}',
                success:function(response){
                    if(response['status'] == true){

                        $('.defaultFolder').map(function (key, value) {
                            if(value.getAttribute('data-fieldid') == fieldId){
                                value.setAttribute('data-default',response['value'])
                            }
                        })
                        $(".folderSpinnerCheck").css('display','none');
                        $('#defaultFolderModal').modal('hide');

                    }

                }

            })

        })
        $(document).on('change','.ecowiseNormalFieldFilter',function(){
            $('.spinerFilterLinkPrefiller').css('display','block');
            var value = $('option:selected',this).attr('value')
            var id = $('option:selected',this).attr('id');
            // var linkId = $('.selectNormalUrl').val();
            var linkprefillerfilterid =  $('option:selected',this).attr('linkprefillerfilterid')
            var viewStatus = false
            $.ajax({
                type:'post',
                data: {value:value,ids:id,viewStatus:viewStatus,linkprefillerfilterid:linkprefillerfilterid},
                url: '{{url('dashboard/company/docketBookManager/designDocket/linkPrefillerFilterView')}}',
                success:function(response){

                    $('.ecowiseFieldFilterView'+id).html(response)
                    $(document).ready(function(){
                        $('.prefillerLinkFilter').multiselect({
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: false,
                            buttonWidth:'100%',
                            enableClickableOptGroups: false,
                            checkboxName: false,
                        });
                    });

                    $(".addNormalFilterLinkPrefiller").click(function(){
                        var docketFieldId = $(this).attr('data-fieldId')
                        $.ajax({
                            type:'post',
                            url:"{{url('dashboard/company/docketBookManager/designDocket/dynamicFilterField')}}",
                            data:{docketFieldId:docketFieldId},
                            success:function(response){
                                $('#dynamicNormalPrefillerFilterField').append(response)
                            }
                        })
                    });

                    $('.spinerFilterLinkPrefiller').css('display','none');
                }
            })
        })

     $(document).on('change','.prefillerLinkFilter',function(){
         $('.spinerFilterLinkPrefiller').css('display','block');
         var docketFieldId = $('option:selected',this).attr('docketfieldId');
         var value = $('option:selected', this).attr('value')
         var linkprefillerfilterid = $('option:selected', this).attr('linkprefillerfilterid')

         $.ajax({
             type:'post',
             data:{docketFieldId:docketFieldId,value:value,linkprefillerfilterid:linkprefillerfilterid},
             url: '{{url('dashboard/company/docketBookManager/designDocket/updateLinkPrefillerValue')}}',
             success:function(response){
                 $('.spinerFilterLinkPrefiller').css('display','none');
             }
         })
     })

        $(document).on('change','.ecowiseGridFieldFilter',function(){
            $('.spinerFilterLinkPrefiller').css('display','block');
            var value = $('option:selected',this).attr('value')
            var gridId = $('option:selected',this).attr('gridid');
            var fieldId = $('option:selected',this).attr('fieldid');
            var linkprefillerfilterid = $('option:selected',this).attr('linkprefillerfilterid')
            var linkId = $('.selectUrl').val();
            var viewStatus = false
            $.ajax({
                type:'post',
                data: {value:value,gridId:gridId,linkId:linkId,fieldIds:fieldId,viewStatus:viewStatus,linkprefillerfilterid:linkprefillerfilterid},
                url: '{{url('dashboard/company/docketBookManager/designDocket/linkGridPrefillerFilterView')}}',
                success:function(response){
                    $('.ecowiseGridFieldFilterView'+gridId).html(response)
                    $(document).ready(function(){
                        $('.prefillerGridLinkFilter').multiselect({
                            enableFiltering: true,
                            enableCaseInsensitiveFiltering: false,
                            buttonWidth:'100%',
                            enableClickableOptGroups: false,
                            checkboxName: false,
                        });
                    });
                    $(".addfilterLinkPrefiller").click(function(){
                        var docketGridFieldId = $(this).attr('data-girdfieldId')
                        $.ajax({
                            type:'post',
                            url:"{{url('dashboard/company/docketBookManager/designDocket/gridDynamicFilterField')}}",
                            data:{docketGridFieldId:docketGridFieldId},
                            success:function(response){
                                $('#dynamicPrefillerFilterField').append(response)
                                // $("#dynamicPrefillerFilterField").append('<div style="margin: 0px 0px 30px 0px;"><br><div class="col-md-6"><select style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%"> </select> </div> <div class="col-md-5"> <select  class="form-control prefillerGridLinkFilter" > </select> </div> <div class="col-md-1"> <button type="button" name="add"  class="btn btn-danger removefilterLinkPrefiller" style="margin: 0;">Remove</button> </div> </div>');
                            }
                        })
                    });
                    $('.spinerFilterLinkPrefiller').css('display','none');
                }
            })
        })

        $(document).on('change','.prefillerGridLinkFilter',function(){
            $('.spinerFilterLinkPrefiller').css('display','block');
            var docketFieldId = $('option:selected',this).attr('docketfieldId');
            var value = $('option:selected', this).attr('value')
            var linkprefillerfilterid = $('option:selected', this).attr('linkprefillerfilterid')
            $.ajax({
                type:'post',
                data:{docketFieldId:docketFieldId,value:value,linkprefillerfilterid:linkprefillerfilterid},
                url: '{{url('dashboard/company/docketBookManager/designDocket/updateLinkGridPrefillerValue')}}',
                success:function(response){
                    $('.spinerFilterLinkPrefiller').css('display','none');
                }
            })
        })

        var i = [];
        var files = [];
        $(document).ready(function () {
            if (window.File && window.FileList && window.FileReader) {
                $(".imagePreview").on("change", function (e) {
                    var id = $(this).attr('data-id');
                    var j = 0;
                    var fileList = e.target.files;
                    if (!keyCheck(files,id)){
                        i[id] = 0;
                        files[id] = [];
                    }

                    for (var k = 0; k < $(this).get(0).files.length; k++) {
                        files[id].push($(this).get(0).files[k]);
                    }

                    add(id,this);
                    load(fileList, j,id,this);
                });
            }

            function keyCheck(files,id){
                var check = 0;
                files.forEach((file,index) => {
                    if(index == id){
                        check = 1;
                        return ;
                    }else{
                        check = 0;
                    }
                });
                if(check == 1){
                    return true;
                }else{
                    return false
                }
            }

            function load(fileList, j,id,event) {
                if (j < fileList.length) {
                    var fileReader = new FileReader();
                    fileReader.onload = (function (e) {
                        $(event).closest('.form-group').find('.pipcontent').append('<span class="pip" data-id="' + i[id] + '">' +
                            "<span class=\"badge badge-pill badge-danger remove\" onclick=\"remove(" + i[id] + ",this)\" data-id='"+id+"'><i class=\"fa fa-times\"></i></span>"+
                            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" />" +
                            "</span>");
                        i[id]++;
                        load(fileList, j,id,event);
                    });
                    fileReader.readAsDataURL(fileList[j]);
                    j++;
                }
            }
        });

        function add(id,event) {
            $(event).get(0).files = new FileListItem(files[id]);
        }

        function remove(data,event) {
            // alert(data);
            var id = $(event).attr('data-id');
            console.log(id);
            var filter = [];
            var removeArr = [];
            var keys = Object.keys($(event).closest('.image_instruction').find('.imagePreview').get(0).files);
            for (var key of keys) {
                removeArr.push(key);
            }
            removeArr.reverse();
            for (var j = 0; j < $(event).closest('.image_instruction').find('.imagePreview').get(0).files.length; j++) {
                if (j != data) {
                    filter.push($(event).closest('.image_instruction').find('.imagePreview').get(0).files[removeArr[j]]);
                }
            }
            files[id] = filter;
            $(event).closest('.image_instruction').find('.imagePreview').get(0).files = new FileListItem(files[id]);
            var k = 0;

            $(event).closest('.pipcontent').find('.pip').each(function () {
                var data_id = $(this).attr('data-id');
                console.log('data_id '+data_id);
                console.log('data '+data);
                if (data_id == data) {
                    $(this).remove();
                }
                else {
                    $(this).attr('data-id', k);
                    $(this).find('.remove').attr('onclick', "remove('" + k + "',this)");
                    k++;
                }
            });
            i[id] = k;
        }

        function FileListItem(a) {
            a = [].slice.call(Array.isArray(a) ? a : arguments)
            for (var c, b = c = a.length, d = !0; b-- && d;) d = a[b] instanceof File
            if (!d) throw new TypeError("expected argument to FileList is File or array of File objects")
            for (b = (new ClipboardEvent("")).clipboardData || new DataTransfer; c--;) b.items.add(a[c])
            return b.files
        }

        $('.saveImageInstruction').click(function(){
            $(this).closest('.image_form').submit();
        });

        $('.image_form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url : '{{ route("save.image.instruction") }}',
                type: "POST",
                data: new FormData(this),
                contentType: false,
                processData:false,
                success: function(response) {
                    if(response.status){
                        toastr.success('Image instruction added');
                    }else{
                        toastr.error('Error');
                    }
                },
                error: function(status){
                    toastr.error('Error');
                }
            });
        });

        $('.removeImageInstruction').click(function(){
            $(this).closest('.pip').remove();
        });

        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        $(document).on('click', '.assignType', function () {
            var value = $(this).val();
            // var assigntype = document.getElementsByClassName("assignType");
            var assigntype = $(this).closest('.cloneUnit').find('.assignType');
            for (var i = 0; i < assigntype.length; i++) {
                if(assigntype[i].value != value){
                    assigntype[i].checked = false;
                }else{
                    assigntype[i].checked = true;
                }
            }
            // if(value == 1){
            //     $('.daterange').css('display','');
            // }else{
            //     $('.daterange').css('display','none');
            // }
            if(value == 1){
                $(this).closest('.form-group').find('.daterange').css('display','');
            }else{
                $(this).closest('.form-group').find('.daterange').css('display','none');
            }
        });

        $('.cloneEmployee').click(function(){
            var clone = $('.cloneUnit:first').clone(true);
            $('.appendCloneUnit').append(clone.prepend('<a class="assignDocketClose" onclick="removeEmployee(this)"><i class="fa fa-close"></i></a>'));
            $('.daterange').daterangepicker({
                opens: 'left',
            }, function(start, end, label) {
                console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            });
        });

        function removeEmployee(event){
            $(event).closest('.cloneUnit').remove();
        }

        function dateDisable(event,user_id){
            var url = '{{ route("leave.management.view",":ID") }}';
            url = url.replace(":ID",user_id);
            $.ajax({
                type:'GET',
                url: url,
                success: function(response){
                    if(response.status){
                        var day = 1000*60*60*24;
                        if(response.employee_leave.length > 0){
                            var invalidDateList = [];
                            var employeeLeave = response.employee_leave;
                            employeeLeave.forEach(leave => {
                                date1 = new Date(leave.from_date);
                                date2 = new Date(leave.to_date);
                                var diff = (date2.getTime()- date1.getTime())/day;
                                for(var i=0;i<=diff; i++)
                                {
                                    var xx = date1.getTime()+day*i;
                                    var yy = new Date(xx);

                                    invalidDateList.push(yy.getFullYear()+"-"+(yy.getMonth()+1)+"-"+yy.getDate());
                                }
                            });

                            $('input[name="daterange"]').daterangepicker({
                                isInvalidDate: function(date,response) {
                                    if (invalidDateList.includes(date.format('YYYY-M-D'))) {
                                        return true;
                                    }
                                }
                            });
                        }
                    }
                }
            })
        }

        $('.employeeList').change(function(){
            dateDisable(this,$(this).val());
        });


    </script>

    <style>

        .multiselect-group label{
            margin: 0;
            padding: 0px 0px 0px 7px;
            height: 100%;
            font-weight: bold;
            color: #8e8e8e;
        }
        .open .multiselect-container{
            width: 100%;
        }

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
        #subdocketSorting .editable-click:after{
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -6px;
            font-weight: normal;
            font-size: 10px;
            color: red;
            padding: 0px 5px;
            border-radius: 5px;
            width: 40px;
        }
        .subdocketing a:focus{
            text-decoration: none;
        }

        .deleteSubDocketComponent {
            background: none !important;
            color: red !important;
            box-shadow: none !important;
            border: none !important;
            font-size: 16px !important;
            margin: 0px !important;
        }

        .unitRateEdit .editable-click:after{
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -6px;
            font-weight: normal;
            font-size: 10px;
            color: red;
            padding: 0px 5px;
            border-radius: 5px;
        }
        #unitRateEdit .editable-click:after{
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -6px;
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
        .prefillercontent .editabledocketgridprefiller:after {
            display: none;
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
            /*color: #fff;*/
            display: block;
        }

        /*.ss-main .ss-multi-selected .ss-values .ss-disabled{*/
        /*font-weight: 600;*/
        /*color: #c1c2c5;*/
        /*}*/
        .ss-main .ss-single-selected .ss-deselect{
            display: none !important;
        }
        .ss-main .ss-single-selected .placeholder{
            color: #000000;
        }
        .ss-main .ss-single-selected .ss-arrow {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            flex: 0 1 auto;
            margin: -4px 4px;
        }
        .ss-main .ss-single-selected .placeholder .ss-disabled {
            cursor: pointer;
            color: #929292;
        }
        .ss-main .ss-content .ss-list .ss-option {
            padding: 6px 10px;
            cursor: pointer;
            user-select: none;
            font-weight: 400;
        }
        .prefillercontent{
            height: 28px;
            position: relative;
            padding-right: 21px;
            padding-top: 8px;
            margin-right: 12px;
        }
        .main-size{
            padding:10px;
            background-color: #ffffff;
        }

        table.main-tb{
            width: 100%;
        }
        .main-size ul{
            padding:0;
            list-style: none;
        }
        .docket{
            font-weight: bold;
            font-size: 14px;
            color: #000000;
        }
        .button{
            font-size: 12px;
            padding: 5px;
            border-radius: 10px;
            color: #ffffff;
            float: right;
            background-color: #00bcd4;
        }
        table.main-name{
            padding-top: 5px;
            padding-bottom: 5px;
            width: 100%;
            border: 2px solid #9e9e9e63;
            border-right: none;
            border-left: none;
        }
        .main-name .fa{
            color: #9e9e9e;
            font-size: 14px;
            float: right;
            padding-left: 5px;
        }

        .button-name{
            font-size: 12px;
            padding: 5px;
            border-radius: 10px;
            color: #ffffff;
            float: left;
            background-color:#0b5889;
        }
        .input{
            border-top: none;
            border-right: none;
            border-left: none;
            width: 100%;
        }
        input.abc{
            width: 100%;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        input.abc{

        }
        input.abc[type=text]{
            color: gray;
            outline: none;
        }
        /*input.abc[value=Number]{
          padding-left: 5px;
          color: gray;
          outline: none;
        }*/

        table.docket-unit{
            width: 100%;
            /*border:2px solid #9e9e9e63;*/
            border-radius: 5px;
            margin-top:10px;
        }
        .docket-unit b{
            color: #000000;
        }
        table.docket-check{
            padding-right: 5px;
            padding-left: 5px;
            border-radius: 5px;
            margin-top: 10px;
            border:2px solid #9e9e9e63;
            width: 100%;
        }
        /*.checkbox{*/
        /*    float: right;*/
        /*}*/
        .docket-check p{
            color:#000000;
        }
        .docket-check input[type=checkbox] {
            -ms-transform: scale(2); /* IE */
            -moz-transform: scale(2); /* FF */
            -webkit-transform: scale(2); /* Safari and Chrome */
            -o-transform: scale(2); /* Opera */
            padding: 10px;
        }
        table{
            width: 100%;

        }
        table.docket-signature{
            border-radius: 5px;
            margin-top: 10px;
            border:2px solid #9e9e9e63;
            text-align: center;
        }

        table.add-signature{
            border-bottom:2px solid #9e9e9e63;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        table.add-signature p{
            text-align: left;
            font-style: italic;
        }

        table.sketch-pad{
            border-radius: 5px;
            margin-top: 10px;
            border:1px solid #9e9e9e63;
        }
        .sketch-pad p{
            text-align: center;
        }


        footer{
            text-align: center;
        }
        table.add-sketch {
            border-bottom:2px solid #9e9e9e63;
            padding-top: 10px;
            padding-bottom: 10px;
        }
        .add-sketch p{
            font-style: italic;
            text-align: left;
        }
        table.document p{
            font-weight: 300;
        }
        table.document{
            border-bottom:2px solid #9e9e9e63;
            padding-bottom: 15px;
            margin-top: 9px;
        }
        table.header-title p{
            color: #000000ba;
            font-weight: bold;
            /*border-bottom: 1px solid #9e9e9e4d;*/
        }
        .header-p{
            /*padding-bottom: 10px;*/
        }
        .xyz{
            /*line-height: 0px;*/
            color: gray;
            font-size: 10px;
        }
        .docket-image p{
            /*float: right;*/
            padding-right: 5px;
            padding-top: 7px;
            color: #012f54;
            font-size: 12px;
        }

        table.docket-image{
            border-radius: 2px;
            border: none;
            background: #fff;
            box-shadow: 0px 1px 3px 0px #888888;
            text-align: center;
        }
        .img-bc .fa{
            float: right;
            padding-right: 10px;
            font-size: 30px;
            color: #ffffff;

        }
        .add-image p{
            font-style: italic;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 34px;
            margin-top: 0px;
            height: 13px;
        }

        .switch input {display:none;}
        .yesnofield .editable-click:after{
            top: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: -4px;
            bottom: -2px;
            background-color: #dcdbdb;
            -webkit-transition: .4s;
            transition: .4s
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
        .mobileframe{
            overflow-x: scroll;
            position: absolute;
            width: calc( 100% - 20px);;
        }
        .mobilecontain{
            overflow-x: scroll;
            height: 492px;;
            position: relative;
        }
        .mobileContentWrapper{
            min-height: 490px;background: #fff;position: absolute;left: 28px;top: 110px;width: calc( 100% - 72px);
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
                overflow-x: scroll;
                height: 397px;
                position: relative;
            }
        }
        .colorpicker:before {
            content: none !important ;
            display: inline-block;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #ccc;
            border-bottom-color: rgba(0,0,0,.2);
            position: absolute;
            top: -7px;
            left: 6px;
        }
        .colorpicker:after {
            content: none !important;
            display: inline-block;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #fff;
            position: absolute;
            top: -6px;
            left: 7px;
        }
        .colorpicker-selectors i {
            cursor: pointer;
            float: left;
            height: 20px;
            width: 20px;
        }
        .colorpicker{
            z-index:0;
        }
        .label-type-icon{
            text-decoration: none;
            cursor: pointer;
        }
        .label-type-icon:hover{

            text-decoration: none;
        }
        .label-type-icon:after {
            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -12px;
            font-weight: normal;
            font-size: 10px;
            color: red;
            padding: 0px 0px;
            border-radius: 5px;
        }
        /* Base for label styling */
        /*.check [type="checkbox"]:not(:checked),*/
        /* .check [type="checkbox"]:checked {*/
        /*     position: absolute;*/
        /*     left: -9999px;*/
        /* }*/
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

        .docketFieldNumbereditable:after{
            content:none !important;
        }

        /* hover style just for information */
        label:hover:before {
            border: 2px solid #4778d9!important;
        }
        .modalHeight
        {
            height: 690px;
        }

        @media (min-width: 1281px) {

            .modalHeight
            {
                height: 690px;
            }

        }

        /*
          ##Device = Laptops, Desktops
          ##Screen = B/w 1025px to 1280px
        */

        @media (min-width: 1025px) and (max-width: 1280px) {

            .modalHeight
            {
                height: 690px;
            }

        }

        /*
          ##Device = Tablets, Ipads (portrait)
          ##Screen = B/w 768px to 1024px
        */

        @media (min-width: 994px) and (max-width: 1014px) {

            .modalHeight
            {
                height: 695px;
            }

        }
        @media (min-width: 758px) and (max-width: 990px) {

            .modalHeight
            {
                height: 736px;
            }

        }

        /*
          ##Device = Tablets, Ipads (landscape)
          ##Screen = B/w 768px to 1024px
        */

        @media (min-width: 506px) and (max-width: 757px)  {

            .modalHeight
            {
                height: 734px;
            }

        }
        @media (min-width: 488px) and (max-width: 756px)  {

            .modalHeight
            {
                height:734px;
            }

        }



        /*
          ##Device = Most of the Smartphones Mobiles (Portrait)
          ##Screen = B/w 320px to 479px
        */

        @media (min-width: 320px) and (max-width: 480px) {

            .modalHeight
            {
                height: 734px;
            }

        }


        .componentScroll{
            height: 1540px;
            /*overflow-y : scroll;*/
            /*overflow-x: hidden;*/
        }



        /*.componentScroll::-webkit-scrollbar-track*/
        /*{*/
        /*-webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);*/
        /*border-radius: 10px;*/
        /*background-color: #F5F5F5;*/
        /*}*/

        .componentScroll::-webkit-scrollbar
        {
            width: 0px;
            background-color: transparent;
        }

        .componentScroll::-webkit-scrollbar-thumb
        {
            border-radius: 20px;
            background-color: #ececec;
        }

        .gridbody .editable-container{
            margin-bottom: -23px;
        }
        .grid-row .deleteGridColumn {
            display: none;
        }

        .grid-row:hover {
            cursor: pointer;

        }

        .grid-row:hover  .deleteGridColumn{
            display: inline-block;
            cursor: pointer;

        }

        .grid-row{
            position: relative;
        }
        .gridadd-button button{
            margin: 0px;
            display: block;
            width: 100%;
            height: 201px;
        }
        .gridSelection .form-group{
            margin: 0;
        }
        .gridbody{
            padding: 0px 0px 15px 0px;
            margin: 0 -9px 0 -9px;
            min-width: 215px;
            min-height: 251px;
        }
        .textsie{
            font-size: 12px !important;
        }

        .gridbody>h5{
            padding: 0px 0px 6px 13px;
        }
        .gridbody .form-control{
            background-color: #fbfcfb !important;
            text-indent: 9px;
            color: #a9a9a9;
            font-size: 12px;
            font-weight: 300;
            margin: 11px 11px -6px 9px;
            border-bottom: none;

        }

        .gridadd-button{
            width: 50px;
            background-color: #12b0b7;
            margin: 15px 4px 0px 0px;
        }
        .grid-row{
            background: rgb(255, 255, 255);
        }
        .grid-row #removeModularGridColumn{
            position: absolute;
            top: -22px;
            right: 4px;
            padding: 0;
            background: transparent;
            box-shadow: none;
        }
        .gridSection{
            overflow-x: auto;
            white-space: nowrap;
            width: calc(100% - 54px);
        }

        .editable-inline div .form-group{
            margin-top: -15px;
        }

        .editable-inline div div .editable-input input{
            padding-right: 24px;
            font-size: 12px;
            padding: 10px 0 0 0;
        }
        .cloneDocketComponent{
            display: none;
        }
        .docketField:hover  .cloneDocketComponent{
            display: inline-block;

        }
        .deleteDocketComponent{
            display: none;
        }
        .docketField:hover  .deleteDocketComponent{
            display: inline-block;
            margin-bottom: 0px !important;
            padding-bottom: 0px;

        }

        .setGridPrefillers div table tbody th,td {
            padding: 0px 0px 0px 15px;
        }





        .exportMappingView .editable-click:after {
            top: unset;
            width: 42px;
        }

        #docketPrefix .editable-click:after{

            content: 'edit \f044';
            font-family: FontAwesome, Roboto,Helvetica,Arial,sans-serif;
            position: absolute;
            top: -7px;
            font-weight: normal;
            font-size: 10px;
            color: #15B1B8;
            padding: 0px 5px;
            border-radius: 5px;
        }
        .undofieldbutton {
            background: none !important;
            color: red !important;
            box-shadow: none !important;
            border: none !important;
            font-size: 16px !important;
            margin: 0px !important;
        }
        .horizontalList>div>ul>li {
            background: #e0e0e0;
            padding: 2px 0px 5px 5px;
            margin-right: 10px;
        }

        .horizontalList>div>ul>li>span{
            color: #0c0c0c;
        }

        .horizontalList>div>ul>li>span{
            color: #000000 !important;
        }
        .prefillerErrorMessage {
            background: #ea4c4c;
            padding: 3px 3px 3px 13px;
            color: #ffffff;
            border-radius: 3px;
        }
    </style>
@endsection
