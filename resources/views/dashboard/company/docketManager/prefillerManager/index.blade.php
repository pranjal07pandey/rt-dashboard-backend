@extends('layouts.companyDashboard')
@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/jszip.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.8.0/xlsx.js"></script>
    <script>
        var ExcelToJSON = function() {
            this.parseExcel = function(file) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    var data = e.target.result;
                    console.log(data)

                    try {

                        var workbook = XLSX.read(data, {
                            type: 'binary'
                        });

                        workbook.SheetNames.forEach(function(sheetName) {
                            // Here is your object
                            var XL_row_object = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[sheetName]);

                            var json_object = JSON.stringify(XL_row_object);
                            var parseJson = JSON.parse(json_object);
                            $.each(parseJson, function(key, value) {
                                if ( $("#firstArryExcelValue").val() !=""){
                                    var wrapper = $('.field_wrapper');
                                    var fieldHTML ='<div  class="form-group" style="    margin-top: 8px;    margin-left: -14px;"><input type="text"  name="text_label[]" class="form-control labelText" value="'+value["label"]+'" placeholder="Label"/><a href="javascript:void(0);" class="remove_button" style="  position: absolute;right: -12px;display: block;bottom: 20px;background: red;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;"><i class="fa fa-minus" aria-hidden="true"></i></a></div>'; //New input field html
                                    $(wrapper).append(fieldHTML);
                                }else{
                                    if (key==0){
                                        console.log(value);
                                        $("#firstArryExcelValue").val(value["label"]);
                                    }else{
                                        var wrapper = $('.field_wrapper');
                                        var fieldHTML ='<div  class="form-group" style="    margin-top: 8px;    margin-left: -14px;"><input type="text"  name="text_label[]" class="form-control labelText" value="'+value["label"]+'" placeholder="Label"/><a href="javascript:void(0);" class="remove_button" style="  position: absolute;right: -12px;display: block;bottom: 20px;background: red;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;"><i class="fa fa-minus" aria-hidden="true"></i></a></div>'; //New input field html
                                        $(wrapper).append(fieldHTML);
                                    }
                                }


                            });
                        })
                    }
                    catch (err) {
//                        $("li:last-child").addClass('disabled');
                        $('.flashsuccessErrorFile').show().delay(5000).fadeOut(400);
//                        $('.demo1').css( "pointer-events", "none" );
                    }

                };

                reader.onerror = function(e) {
                    console.log(e);
                    alert("please Select Correct File")
                };

                reader.readAsBinaryString(file);
            };
        };

        function handleFileSelect(evt) {

            var files = evt.target.files; // FileList object
            var xl2json = new ExcelToJSON();
            xl2json.parseExcel(files[0]);
        }



    </script>


    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Text Manager</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="dashboardFlashsuccess" style="display: none;">
        <div class="alert alert-success" style="padding: 5px 10px;font-size: 13px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p class="messagesucess"></p>
        </div>
    </div>

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="clearfix"></div>
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 29px;font-weight: 500;display:inline-block">All Text Manager</h3>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <a style="margin-top: -1px;"  id="first" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#addPrefillerManager" >
                    <i class="fa fa-plus-square"></i> Add New
                    <div class="ripple-container"></div></a>

                @if(Session::get('company_id') == 1 )
                    <button data-toggle="modal" data-target="#uploadExcelFileModal"   class="btn btn-danger clearbutton"  style="background: #f44336;color: #ffffff; padding: 2px 16px;  border-radius: 2px; font-size: 12px;"> Upload Excel File</button>
            @endif

            <!-- <button style="margin-top: -1px;" type="button" id="first" class="btn btn-xs btn-raised btn-block btn-sucess" data-toggle="modal" data-target="#myModal" >-->
                <!--<i class="fa fa-plus-square"></i> Add New-->
                <!--<div class="ripple-container"></div></button>-->
            </div>
        </div>
        <div class="col-md-12">
            <div class="datatable" >
                <table class="table" id="datatable">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th >Title</th>
                        <th>Added By</th>
                        <th width="200px">Date Added</th>
                        <th width="150">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(@$docketPrefiller)
                        @foreach($docketPrefiller as $row)
                            @if($row->type!=1 && $row->type!=2)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td >{{$row->title}}<br></td>
                                    <td>{{ @$row->userInfo->first_name }} {{ @$row->userInfo->last_name }}</td>
                                    <td>

                                        @php

                                            $format = 'Y-m-d H:i:s';
                                            $value = $row->created_at;
                                            $company =  @@App\Company::where('id',Session::get('company_id'))->first();
                                            if(!is_null($company->time_zone)){
                                                $canberra = \DateTime::createFromFormat($format, $value, $eb = new \DateTimeZone('Australia/Canberra'));
                                                $sydney = \DateTime::createFromFormat($format, $value, $as = new \DateTimeZone($company->time_zone));
                                                $offset = \DateTime::createFromFormat($format, $value, $eb)->setTimezone($as);
                                                echo $offset->format('d-M-Y H:i:s');
                                            }else{
                                                 echo \Carbon\Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
                                            }

                                        @endphp

                                    </td>
                                    <td>
                                        <div class="btnWrapper">
                                            <a  data-toggle="modal" data-target="#addPrefillerLabel" data-id="{{$row->id}}" data-title="{{$row->title}}" data-isinteger="{{$row->is_integer}}" data-type="{{$row->type}}"  class="btn btn-success btn-xs btn-raised " style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                            <a   data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs deleteDocumentModal" id="deletebuttonprefiller{{$row->id}}"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        @foreach($docketPrefiller as $row)
                            @if($row->type==1 || $row->type ==2)
                                <tr>
                                    <td>{{$row->id}}</td>
                                    <td >{{$row->title}}<br>

                                        {{--                                        <span class="docketprefiller" style="font-size: 12px;">--}}
                                        {{--                                        <div>--}}
                                        {{--                                            <b style="float: left"> Label</b>--}}
                                        {{--                                            --}}{{--<button  data-toggle="modal" data-target="#prefillers" data-id="{{$row->id}}" data-title="{{$row->title}}" id="{{$row->id}}" onclick="prefillerslabels(this.id)" class="btn btn-info btn-xs btn-raised prefillerslabels-class"   style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px; float: right;"><i class="fa fa-plus"></i></button>--}}
                                        {{--                                        </div><br>--}}
                                        {{--                                        <div id="prefillerValueWrapper{{ $row->id }}">--}}
                                        {{--                                        @if($row->docketPrefillerValue)--}}
                                        {{--                                                @foreach($row->docketPrefillerValue as $docketprefiller)--}}
                                        {{--                                                    <div class="prefillercontent" id="divResult" style="background: #0000000a;">--}}
                                        {{--                                                    <h5 style="margin-left: 12px; margin-bottom: 0; margin-top: 0;    padding-bottom: 4px;padding-top: 4px;"id="shortText" class="" data-type="text" data-pk="{{ $docketprefiller->id }}" data-url="{{ url('dashboard/company/docketManager/prefillerManager/editPrefillerLabel') }}" data-title="Enter Label Text">{{$docketprefiller->label}}</h5>--}}
                                        {{--                                                </div>--}}
                                        {{--                                                @endforeach--}}
                                        {{--                                            @endif--}}
                                        {{--                                        </div>--}}
                                        {{--                                     </span>--}}
                                    </td>
                                    <td>{{ @$row->userInfo->first_name }} {{ @$row->userInfo->last_name }}</td>
                                    <td>   @php
                                            $format = 'Y-m-d H:i:s';
                                            $value = $row->created_at;
                                            $company =  @@App\Company::where('id',Session::get('company_id'))->first();
                                            if(!is_null($company->time_zone)){
                                                $canberra = \DateTime::createFromFormat($format, $value, $eb = new \DateTimeZone('Australia/Canberra'));
                                                $sydney = \DateTime::createFromFormat($format, $value, $as = new \DateTimeZone($company->time_zone));
                                                $offset = \DateTime::createFromFormat($format, $value, $eb)->setTimezone($as);
                                                echo $offset->format('d-M-Y H:i:s');
                                            }else{

                                                 echo \Carbon\Carbon::parse($row->created_at)->format('d-M-Y H:i:s');
                                            }
                                        @endphp
                                    </td>
                                    <td>
                                        <a  data-toggle="modal" data-target="#addPrefillerLabel" data-id="{{$row->id}}" data-title="{{$row->title}}" data-isinteger="{{$row->is_integer}}"  data-type="{{$row->type}}"  class="btn btn-success btn-xs btn-raised " style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>

            </div>
            <div class="datatableSearchResult"></div>
        </div>
    </div>
    <br>
    <br>

    {{--    modal--}}

    <div class="modal fade" id="addPrefillerManager" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefiller Manager</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div id="errorMessage" style="background: red"><p></p></div>
                        <div class="form-group label-floating">
                            <div class="col-md-12">

                                <div style="    margin-top: 4px; " class="form-group">
                                    <label class="control-label"  for="title">Title</label>
                                    <input  type="text"  name="label" id="label" class="form-control">
                                </div>

                                <div class="form-group  is-empty">
                                    <label class="control-label" for="title">Type</label>
                                    <select style="    margin-bottom: -9px;" class="form-control" name="isIntegerType" id="isIntegerType">
                                        <option value="0">Text</option>
                                        <option value="1">Number</option>
                                        <option value="2">Email</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button  type="submit" class="btn btn-primary" id="saveManager">Save</button>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="addPrefillerLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="    height: 544px;">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close reloadPrerfillermanager" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefiller Label</h4>
                </div>
                <div class="modal-body">
                    <div style="display: none;position: absolute;right: 50%;bottom: 0%; z-index: 10000;" class="prefillermanager">
                        <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                        <span class="sr-only">Loading...</span>
                    </div>
                    <div class="row">
                        <div class="col-md-12" >
                            <h4 class="inlineEditPrefiller" style="    font-size: 15px;    float: left;"></h4>
                            <input type="hidden" id="prefillerIsInteger">
                            <input type="hidden" id="prefillerManagerId">
                            <div class="clearfix"></div>
                            <br>
                            <br>
                            <div style="float: left;">
                                <strong>Label</strong>
                                <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                            </div>
                            <div style="float: right">
                                <button type="button"  class="btn btn-primary addbutton"  data-toggle="modal" data-target="#addGridPrefillerModel"  style="background: #03A9F4;color: #ffffff;padding: 2px 16px; border-radius: 2px;font-size: 12px;">Add</button>
                                <button data-toggle="modal" data-target="#clearAllPrefillerManager"   class="btn btn-danger clearbutton"  style="background: #f44336;color: #ffffff; padding: 2px 16px;  border-radius: 2px; font-size: 12px;"> Clear All</button>
                            </div>
                            <div class="clearfix"></div>
                            <div id="prefillerValueWrapper">

                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div id="prefillerValueWrapper">

                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="position: absolute; bottom: 0px;right: 0px;">
                    <button  class="btn btn-xs btn-raised  btn-success" data-dismiss="modal" aria-label="Close"> <i class="fa fa-plus-square"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addGridPrefillerModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style=" z-index: 11111; display: none; padding-top: 8%;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Add Label </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="parentData">
                            <div class="gridsappendvaluetypes">
                                <p class="prefillerErrorMessage" style="display: none;"></p>
                                <input type="hidden" class="parentManagerId" >
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="50" class="form-control gridprefillerInvValue">
                                        <h5  style="color: #757575;"><b class="messageForPrefiller">Maximum 50 characters </b></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveParentPrefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addPrefillerValue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style=" z-index: 11111; display: none; padding-top: 8%;">
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
                    <button type="submit" class="btn btn-primary" id="saveChildPrefiller" >Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="clearAllPrefillerManager" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 11111;padding-top: 8%;display: none;padding-right: 15px;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Clear all prefiller label</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="emptyMessage" style="background: red"><p></p></div>
                            <input type="hidden" class="form-control" id="clearPrefillerManagerId">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to clear all prefiller labels?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="saveclearallprefiller">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/deletePrefillerManager' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Text Manager Category</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <input type="hidden" id="deleteDocument_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this category?</p>
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


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/deletePrefillerManager' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Text Manager Category</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">

                            <label class="excelImport" id="#bb"> Enter Your File
                                <input type="file" id="excelImportFile"  name="files[]">
                            </label>
                            <script>
                                document.getElementById('excelImportFile').addEventListener('change', handleFileSelect, false);

                            </script>

                            <button type="button" class="btn btn-primary saveData">Save</button>
                            <div class="field_wrapper">
                                <div class="form-group" style="    margin-top: 8px;   margin-left: -14px;">
                                    <input type="text" id="firstArryExcelValue" name="text_label[]" class="form-control labelText" value="" placeholder="Label"/>
                                    <a style="position: absolute;right: -12px;display: block;bottom: 20px;background: green;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;" href="javascript:void(0);" class="add_button" type="0" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                    {{--                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>--}}
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="uploadExcelFileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 11111;padding-top: 8%;display: none;padding-right: 15px;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Upload Excel File</h4>
                </div>
                {{--                <form id="data" method="post" enctype="multipart/form-data">--}}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="emptyMessage" style="background: red"><p></p></div>
                            <input type="file" id="excelFiles" class="form-control" name="excelFile">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="uploadExcelFile">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
                {{--                </form>--}}

            </div>
        </div>
    </div>


    {{--    <div class="modal fade" id="updateDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">--}}
    {{--        <div id="second"  class="modal-dialog modal-lg" role="document">--}}
    {{--            <div class="modal-content" style="    height: 513px;">--}}
    {{--                <div class="modal-header themeSecondaryBg">--}}
    {{--                    <button type="button" class="close reloadPrerfillermanager" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
    {{--                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Prefiller Label</h4>--}}
    {{--                </div>--}}
    {{--                <div class="modal-body">--}}
    {{--                    <div class="row">--}}
    {{--                        <div class="col-md-12" >--}}
    {{--                            <h4 class="inlineEditPrefiller" style="    font-size: 15px;    float: left;"></h4>--}}
    {{--                            <input type="hidden" id="viewprefillerIsInteger">--}}
    {{--                            <input type="hidden" id="viewprefillerManagerId">--}}
    {{--                            <div class="clearfix"></div>--}}
    {{--                            <br>--}}
    {{--                            <br>--}}
    {{--                            <div style="float: left;">--}}
    {{--                                <strong>Label</strong>--}}
    {{--                                <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">--}}
    {{--                            </div>--}}
    {{--                            <div style="float: right">--}}
    {{--                                <button type="button"  class="btn btn-primary"  data-toggle="modal" data-target="#addGridPrefillerModel"  style="background: #03A9F4;color: #ffffff;padding: 2px 16px; border-radius: 2px;font-size: 12px;">Add</button>--}}
    {{--                                <button data-toggle="modal" data-target="#clearAllPrefillerManager"   class="btn btn-danger"  style="background: #f44336;color: #ffffff; padding: 2px 16px;  border-radius: 2px; font-size: 12px;"> Clear All</button>--}}
    {{--                            </div>--}}
    {{--                            <div class="clearfix"></div>--}}
    {{--                            <div id="prefillerValueWrapper">--}}

    {{--                            </div>--}}
    {{--                            <div class="clearfix"></div>--}}
    {{--                        </div>--}}
    {{--                        <div class="clearfix"></div>--}}
    {{--                        <div id="prefillerValueWrapper">--}}

    {{--                        </div>--}}
    {{--                    </div>--}}
    {{--                </div>--}}
    {{--                <div class="modal-footer">--}}
    {{--                </div>--}}
    {{--            </div>--}}
    {{--        </div>--}}
    {{--    </div>--}}

    <style>
        .prefillercontent:hover {
            cursor: pointer;
            border-color: #fafafa;
        }

        .prefillercontent {
            height: 28px;
            position: relative;
            padding-right: 21px;
            padding-top: 8px;
            margin-right: 12px;
            min-width: 99px;
        }
        .prefillercontent:hover .btnprefiller{
            display: inline-block;
            cursor: pointer;

        }
        .prefillercontent .btnprefiller {
            display: none;
        }
        #errorMessage{
            background: red;
            margin: 0px 12px 0 14px;
            border-radius: 4px;
        }
        #errorMessage p {
            margin: 0 0 1px 14px;
            color: #ffffff;
            font-size: 14px;
        }
        #emptyMessage{
            background: red;
            margin: 0px 12px 10px 14px;
            border-radius: 4px;
        }
        #emptyMessage p {
            margin: 0 0 1px 14px;
            color: #ffffff;
            font-size: 14px;
        }
        .editableform .control-group {
            margin: 0;
        }

        .prefillerErrorMessage {
            background: #ea4c4c;
            padding: 3px 3px 3px 13px;
            color: #ffffff;
            border-radius: 3px;
        }
    </style>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
    <script>

        $(document).ready(function() {
            $('#addPrefillerLabel').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
        })
        $('#addPrefillerManager').on('show.bs.modal', function(e) {
            $('#errorMessage').css('display','none');
            $("#label").val('')
        })
        $("#addPrefillerLabel").on('show.bs.modal',function (e) {
            $('.prefillermanager').css('display','block');
            var id = $(e.relatedTarget).data('id');
            var title = $(e.relatedTarget).data('title');
            var isinteger = $(e.relatedTarget).data('isinteger');
            var type = $(e.relatedTarget).data('type');
            var url = "{{url('dashboard/company/docketManager/prefillerManager/updatePrifillerTitle')}}";
            var data = "Prefiller Title&nbsp;&nbsp;&nbsp;<a href='#' id='viewprefillerTitle' class='titleeditable' data-type='text' data-pk='"+id+"' data-url='"+url+"' data-title='Enter Label Text'>"+title+"</a>"
            $('#prefillerManagerId').val(id)
            $('.inlineEditPrefiller').html(data)
            $('#prefillerIsInteger').val(isinteger)
            $('.titleeditable').editable({
                mode:"inline"
            });

            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketManager/prefillerManager/checkPrefillerManager')}}",
                data:{prefillerManagerId:id},
                success:function (response) {
                    var wrapperId = "#prefillerValueWrapper";
                    var prefillerData   =   "";
                    jQuery.each( response['finalPrefillerView'], function( i, val ) {
                        prefillerData = prefillerData + val['final'];
                    });
                    if (prefillerData == ""){
                        var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+prefillerManagerId+'">Empty</p>';
                    }else{
                        var finalView = '<table  style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: auto; height: 300px;margin-bottom: 10px;">'+prefillerData+'</table>';
                    }
                    $(wrapperId).html(finalView);

                    if (type == 0){
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                    }else{
                        $(".addbutton").remove();
                        $(".clearbutton").remove();
                        $("#deleteprefillerManagerLabel").remove();
                        $(".btnprefiller").remove();
                    }
                    $('.prefillermanager').css('display','none');

                },
                error: function(){
                    $('.prefillermanager').css('display','none');
                }

            });
        })

        // $(document).ready(function() {
        //     $('#deleteDocument').on('show.bs.modal', function(e) {
        //         var id = $(e.relatedTarget).data('id');
        //         $("#deleteDocument_id").val(id);

        //     });
        // });
        $(document).on('click','.deleteDocumentModal',function () {
            $('#deleteDocument').modal('show')
            var id = $(this).attr('data-id')
            $("#deleteDocument_id").val(id);

        });
        $('#clearAllPrefillerManager').on('show.bs.modal', function(e) {
            var prefillerManagerId = $('#prefillerManagerId').val();
            $('#clearPrefillerManagerId').val(prefillerManagerId)
            $('#emptyMessage').css('display','none');
        })
        $(document).on('click','.reloadPrerfillermanager',function () {
            $('#addPrefillerLabel').modal('hide');
            location.reload();
        })
        $(document).on('click','#saveclearallprefiller', function () {
            var prefillerManagerId =  $('#clearPrefillerManagerId').val();
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketManager/prefillerManager/clearAllPrefillerManager')}}",
                data: {prefillerManagerId:prefillerManagerId},
                success: function (response) {
                    if (response['status'] == true){
                        $('#clearAllPrefillerManager').modal('hide');
                        var wrapperId = "#prefillerValueWrapper";
                        var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+prefillerManagerId+'">Empty</p>';
                        $(wrapperId).html(finalView);
                    }else{
                        $('#emptyMessage').css('display','block');
                        $("#emptyMessage p").html("You cannot perform This action.")
                    }
                }
            })
        })


        $(document).on('click','#saveManager', function () {
            var prefillerManagerTitle =  $('#label').val();
            var isIntegerType  = $("#isIntegerType").val();
            $.ajax({
                type:"POST",
                url: "{{url('dashboard/company/docketManager/prefillerManager/savePrefillerManager')}}",
                data: {title:prefillerManagerTitle,is_integer:isIntegerType},
                success: function (response) {
                    if (response['status'] == true){
                        $('#addPrefillerManager').modal('hide');
                        $('#addPrefillerLabel').modal('show')
                        $('#prefillerManagerId').val(response['prefillerManagerId']);
                        $('#prefillerIsInteger').val(response['isInteger']);
                        var wrapperId = "#prefillerValueWrapper";
                        var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+response['prefillerManagerId']+'">Empty</p>';
                        $(wrapperId).html(finalView);
                        var url = "{{url('dashboard/company/docketManager/prefillerManager/updatePrifillerTitle')}}";
                        var data = "Prefiller Title&nbsp;&nbsp;&nbsp;<a href='#' id='viewprefillerTitle' class='titleeditable' data-type='text' data-pk='"+response['prefillerManagerId']+"' data-url='"+url+"' data-title='Enter Label Text'>"+response['label']+"</a>"
                        $(".inlineEditPrefiller").html(data);
                        $('.titleeditable').editable({
                            mode:"inline"
                        });




                    }else{

                        $('#errorMessage').css('display','block');
                        var wrappermessage = "#errorMessage";
                        $("#errorMessage p").html(response['message'])

                    }
                }
            })
        })

        $('#addGridPrefillerModel').on('show.bs.modal', function(e) {
            $('.prefillerErrorMessage').css('display','none')
            $(".parentData .gridprefillerInvValue").val('')
            var prefillerManagerId = $('#prefillerManagerId').val();
            var prefillerIsInteger = $('#prefillerIsInteger').val();
            var id = $('.parentData .parentManagerId').val(prefillerManagerId)
            if (prefillerIsInteger == 0){
                $(".parentData .gridprefillerInvValue").prop("type", "text");
            }else if(prefillerIsInteger == 1){
                $(".parentData .gridprefillerInvValue").prop("type", "number");
            }else if(prefillerIsInteger == 2){
                $(".parentData .gridprefillerInvValue").prop("type", "email");
                $(".parentData .gridprefillerInvValue").prop('maxlength',300)
                $('.messageForPrefiller').text('Please enter valid Email')

            }
        });

        $('#addPrefillerValue').on('show.bs.modal', function(e) {
            $('.prefillerErrorMessage').css('display','none')
            $(".childData .gridprefillerInvValue").val('')
            var prefillerManagerId = $('#prefillerManagerId').val();
            var prefillerIsInteger = $('#prefillerIsInteger').val();
            var root = $(e.relatedTarget).data('id');
            var index = $(e.relatedTarget).data('index');
            $('.childData .prefillerRootId').val(root)
            $('.childData .prefillerIndexId').val(index)
            $('.childData .parentManagerId').val(prefillerManagerId)
            var id = $('.childData .parentManagerId').val(prefillerManagerId)
            if (prefillerIsInteger == 0){
                $(".childData .gridprefillerInvValue").prop("type", "text");
            }else if(prefillerIsInteger == 1){
                $(".childData .gridprefillerInvValue").prop("type", "number");
            }else if(prefillerIsInteger == 2){
                $(".childData .gridprefillerInvValue").prop("type", "email");
                $(".childData .gridprefillerInvValue").prop('maxlength',300);
                $('.messageForPrefiller').text('Please enter valid Email')
            }
        });

        $('#saveParentPrefiller').click(function () {
            var  prefillerManagerId = $(".parentData .parentManagerId").val();
            var index  = 0;
            var rootId = 0;
            var value = $(".parentData .gridprefillerInvValue").val();
            var fieldType = $("#prefillerIsInteger").val();
            console.log(value)
            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketManager/prefillerManager/saveParentPrefillerLabel')}}",
                data:{prefillerManagerId:prefillerManagerId,index:index,rootId:rootId,value:value,fieldType:fieldType},
                success:function (response) {
                    if(response['status'] == false){
                        $('.prefillerErrorMessage').css('display','')
                        $('.prefillerErrorMessage').text(response['message'])


                    }else{
                        var wrapperId = "#prefillerValueWrapper";
                        var prefillerData   =   "";
                        jQuery.each( response['finalPrefillerView'], function( i, val ) {
                            prefillerData = prefillerData + val['final'];
                        });
                        if (prefillerData == ""){
                            var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+prefillerManagerId+'">Empty</p>';
                        }else{
                            var finalView = '<table  style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: auto; height: 300px; margin-bottom: 10px;">'+prefillerData+'</table>';
                        }
                        $(wrapperId).html(finalView);
                        $('.editabledocketprefiller').editable({
                            mode:"inline"
                        });
                        $('#addGridPrefillerModel').modal('hide');
                    }
                }
            });
        });


        $('#saveChildPrefiller').click(function () {
            var  prefillerManagerId = $(".childData .parentManagerId").val();
            var index  = $(".childData .prefillerIndexId").val();
            var rootId = $(".childData .prefillerRootId").val();
            var value = $(".childData .gridprefillerInvValue").val();
            var fieldType = $("#prefillerIsInteger").val();

            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketManager/prefillerManager/saveParentPrefillerLabel')}}",
                data:{prefillerManagerId:prefillerManagerId,index:index,rootId:rootId,value:value,fieldType:fieldType},
                success:function (response) {

                    if(response['status'] == false){
                        $('.prefillerErrorMessage').css('display','')
                        $('.prefillerErrorMessage').text(response['message'])
                    }else {
                        var wrapperId = "#prefillerValueWrapper";
                        var prefillerData = "";
                        jQuery.each(response['finalPrefillerView'], function (i, val) {
                            prefillerData = prefillerData + val['final'];
                        });
                        if (prefillerData == "") {
                            var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView' + prefillerManagerId + '">Empty</p>';
                        } else {
                            var finalView = '<table  style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: auto; height: 300px; margin-bottom: 10px;">' + prefillerData + '</table>';
                        }
                        $(wrapperId).html(finalView);
                        $('.editabledocketprefiller').editable({
                            mode: "inline"
                        });
                        $('#addPrefillerValue').modal('hide');
                    }


                }
            });
        });

        $(document).on('click','#deleteprefillerManagerLabel', function () {
            var prefillerManagerId = $(this).attr('data-docketprefillermanagerid');
            $(".spinnerCheckgrid").css('display','block');
            var id =  $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketManager/prefillerManager/deletePrefillerManagerlabel')}}",
                data: {
                    label_id : id,
                    prefiller_manager_id : prefillerManagerId,
                },
                success: function (response) {
                    var wrapperId = "#prefillerValueWrapper";
                    var prefillerData   =   "";
                    jQuery.each( response['finalPrefillerView'], function( i, val ) {
                        prefillerData = prefillerData + val['final'];
                    });
                    if (prefillerData == ""){
                        var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+prefillerManagerId+'">Empty</p>';
                    }else{
                        var finalView = '<table  style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: auto; height: 300px; margin-bottom: 10px;">'+prefillerData+'</table>';
                    }
                    $(wrapperId).html(finalView);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });

                }
            })
        })

        $(document).on('click','.saveData', function () {
            var titlabelles = $('input[name^=text_label]').map(function (idx, elem) {
                return $(elem).val();
            }).get();
            $.ajax({
                type:"post",
                url:'{{url('dashboard/company/docketManager/prefillerManager/prefillerDataUpdate')}}',
                data: {data: titlabelles},
                success:function (response) {

                }

            })

        });

        $(document).on('click','#uploadExcelFile',function () {
            var formData = new FormData();

            var files =document.getElementById('excelFiles').files[0];
            formData.append('fileid', files);

            $.ajax({
                url: '{{url('dashboard/company/docketManager/prefillerManager/uploadExcelFile')}}',
                type: 'POST',
                data: formData,
                success: function (data) {

                },
                cache: false,
                contentType: false,
                processData: false,

            });

        })
    </script>





@endsection


