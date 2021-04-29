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
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
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
                <button style="margin-top: -1px;" type="button" id="first" class="btn btn-xs btn-raised btn-block btn-sucess" data-toggle="modal" data-target="#myModal" >
                    <i class="fa fa-plus-square"></i> Add New
                    <div class="ripple-container"></div></button>
            </div>
        </div>
        <div class="col-md-12">

            {{--<div class="dataTables_length" id="datatable_length"><label>Show <select name="datatable_length" aria-controls="datatable" class=""><option value="10">10</option></select> entries</label></div>--}}
            {{--<div id="datatable_filter" class="dataTables_filter">--}}
            {{--<label>Search:<input type="search" class="" id="searchInput" placeholder="" aria-controls="datatable" @if(@$searchKey) value="{{ $searchKey }}" @endif ></label>--}}
            {{--</div>--}}
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
                                <td >{{$row->title}}<br>

{{--                                    <span class="docketprefiller" style="font-size: 12px;">--}}
{{--                                        <div>--}}
{{--                                            <b style="float: left"> Label</b>--}}
{{--                                           <button  data-toggle="modal" data-target="#prefillers" data-id="{{$row->id}}" data-title="{{$row->title}}" id="{{$row->id}}" data-prefillertype="{{$row->is_integer}}" onclick="prefillerslabels(this.id)" class="btn btn-info btn-xs btn-raised prefillerslabels-class"   style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px; float: right;"><i class="fa fa-plus"></i></button>--}}
{{--                                        </div><br>--}}
{{--                                        <div id="prefillerValueWrapper{{ $row->id }}" style="height: 300px; overflow: scroll;">--}}
{{--                                        @if($row->docketPrefillerValue)--}}
{{--                                            @foreach($row->docketPrefillerValue as $docketprefiller)--}}
{{--                                                <div class="prefillercontent" id="divResult" style="background: #0000000a;position:relative">--}}
{{--                                                    <a style="margin-left: 12px;" href="#" id="shortText" class="editabledocketprefiller" data-type="text" data-pk="{{ $docketprefiller->id }}" data-url="{{ url('dashboard/company/docketManager/prefillerManager/editPrefillerLabel') }}" data-title="Enter Label Text">{{$docketprefiller->label}}</a>--}}
{{--                                                    <a  data-toggle="modal" data-target="#deletePrefillerLabel" data-id="{{$docketprefiller->id}}"  class="btn btn-raised btn-danger btn-xs btnprefiller"  style=" font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 10px 4px;background: #4395bb;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a>--}}
{{--                                                </div>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
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
                                    @endphp</td>
                                <td>
                                    <div class="btnWrapper">
                                        <a  data-toggle="modal" data-target="#updateDocument" data-id="{{$row->id}}" data-title="{{$row->title}}"   class="btn btn-success btn-xs btn-raised updatebuttonprefiller1" id="{{$row->id}}" onclick="updatebuttonprefiller(this.id)" style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                        <a  data-toggle="modal" data-target="#deleteDocument" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs deletebuttonprefiller1" id="deletebuttonprefiller{{$row->id}}"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
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

                                            <span class="docketprefiller" style="font-size: 12px;">
                                        <div>
                                            <b style="float: left"> Label</b>
                                            {{--<button  data-toggle="modal" data-target="#prefillers" data-id="{{$row->id}}" data-title="{{$row->title}}" id="{{$row->id}}" onclick="prefillerslabels(this.id)" class="btn btn-info btn-xs btn-raised prefillerslabels-class"   style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px; float: right;"><i class="fa fa-plus"></i></button>--}}
                                        </div><br>
                                        <div id="prefillerValueWrapper{{ $row->id }}">
                                        @if($row->docketPrefillerValue)
                                                @foreach($row->docketPrefillerValue as $docketprefiller)
                                                    <div class="prefillercontent" id="divResult" style="background: #0000000a;">
                                                    <h5 style="margin-left: 12px; margin-bottom: 0; margin-top: 0;    padding-bottom: 4px;padding-top: 4px;"id="shortText" class="" data-type="text" data-pk="{{ $docketprefiller->id }}" data-url="{{ url('dashboard/company/docketManager/prefillerManager/editPrefillerLabel') }}" data-title="Enter Label Text">{{$docketprefiller->label}}</h5>
                                                        {{--<a  data-toggle="modal" data-target="#deletePrefillerLabel" data-id="{{$docketprefiller->id}}"  class="btn btn-raised btn-danger btn-xs btnprefiller"  style=" font-size: 9px;color: #ffffffff;border-radius: 17px;padding: 0px 2px;margin: 10px 4px;background: #4395bb;position: absolute;box-shadow: none;"><span class="glyphicon glyphicon-remove" aria-hidden="true"  /></a>--}}
                                                </div>
                                                @endforeach
                                            @endif
                                        </div>
                                     </span>
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
                                            {{--<div class="btnWrapper">--}}
                                            {{--<a  data-toggle="modal" data-target="#updateDocument" data-id="{{$row->id}}" data-title="{{$row->title}}"   class="btn btn-success btn-xs btn-raised updatebuttonprefiller1" id="{{$row->id}}" onclick="updatebuttonprefiller(this.id)" style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>--}}
                                            {{--<a  data-toggle="modal" data-target="#deleteDocument" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs deletebuttonprefiller1" id="deletebuttonprefiller{{$row->id}}"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>--}}
                                            {{--</div>--}}
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

    <br/><br/>

    {{--<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">--}}
        {{--<div id="second"  class="modal-dialog modal-lg" role="document">--}}
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{--<div class="modal-content">--}}
                {{--<div class="modal-header themeSecondaryBg">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                    {{--<h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Text Manager Category</h4>--}}
                {{--</div>--}}
                {{--{{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/addPrefillerManager', 'files' => true]) }}--}}
                {{--<div class="modal-body">--}}
                    {{--<div class="row">--}}
                        {{--<div class="col-md-12">--}}
                            {{--<div class="form-group label-floating is-empty">--}}
                                {{--<label class="control-label" for="title">Title</label>--}}
                                {{--<input type="text" name="title" class="form-control" required="required" value="" maxlength="20">--}}
                                {{--<h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}

                    {{--<div class="modal-footer">--}}
                        {{--<button type="submit"  class="btn btn-primary">Add</button>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--{{ Form::close() }}--}}
            {{--</div>--}}

        {{--</div>--}}
    {{--</div>--}}


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
                                <input type="hidden" class="parentManagerId" >
                                <div class="col-md-1">
                                    <div class="form-group float-left">
                                        <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>
                                    </div>
                                </div>
                                <div class="col-md-11">
                                    <div style="    margin-top: 15px;" class="form-group">
                                        <input  type="text"  name="value" maxlength="20" class="form-control gridprefillerInvValue">
                                        <h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>
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

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div style="   padding-top: 0px;padding: 0 !important;" class="modal-body">
                    {{ Form::open(['id'=>'stepForm']) }}
                    <button style="    color: #fff;position: absolute;right: 13px;z-index: 100000;top: 17px;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div id="wizard">
                        <h1>Add Text Manager Category</h1>
                        <div>
                            <div class="form-group col-md-12  " style="margin: -24px 0px 0px -13px;">
                                <p style="    color: #fff;background: #ff0000;padding-left: 13px;border-radius: 4px;" class="messageErrortext_manager_category"></p>
                                <p style="display: none; color: #fff;background: #ff0000;padding-left: 13px;border-radius: 4px;" class="flashsuccessText"><i>*Title Required</i> </p>

                                <div class="form-group  is-empty">
                                    <label class="control-label" for="title">Type</label>
                                    <select style="    margin-bottom: -9px;" class="form-control" name="isIntegerType" id="isIntegerType">
                                        <option value="0">Text</option>
                                        <option value="1">Number</option>
                                    </select>
                                </div>

                                <div class="form-group label-floating is-empty">
                                    <label class="control-label" for="title">Title</label>
                                    <input type="text" name="title_text_manager_category" class="form-control text_manager_category" required="required" value="" maxlength="20">
                                    <h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>
                                </div>
                            </div>
                        </div>
                        <h1>Add New Item</h1>
                        <div>
                            <section style="    height: 1px; " class="step" data-step-title="Add New Item">
                                <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerPrefiller">
                                    <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p style="display: none; color: #fff;background: #ff0000;padding-left: 13px;border-radius: 4px;  margin-left: -18px;margin-right: 20px;" class="wrappermessageLabel"><i>*Label Required</i> </p>
                                <p style="display: none; color: #fff;background: #ff0000;padding-left: 13px;border-radius: 4px;  margin-left: -18px;margin-right: 20px;" class="flashsuccessErrorFile"><i>*Please Select Valid File</i> </p>
                                <p style="display: none; color: #fff;background: #ff0000;padding-left: 13px;border-radius: 4px;  margin-left: -18px;margin-right: 20px;" class="flashsuccessText1"><i>*Atleast one Label Required</i> </p>

                                <div class="col-md-12" style="margin:-8px 0px 24px -28px">
                                    <h4 style="    font-size: 15px;    float: left;">Prefiller Title&nbsp;&nbsp;&nbsp;   <span id="text_manager_category_titles" style="padding: 5px 29px;font-size: 15px;color: black;font-weight: 300;border: 1px solid #CED4DA;background: #E9ECEF;"></span></h4>
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
                                            <button type="button"  class="btn btn-primary"  data-toggle="modal" data-target="#addGridPrefillerModel"  style="background: #03A9F4;color: #ffffff;padding: 2px 16px; border-radius: 2px;font-size: 12px;">Add</button>
                                            <button  class="btn btn-danger" id="saveGridPrefiller" style="background: #f44336;color: #ffffff;    margin-right: -69px;padding: 2px 16px;  border-radius: 2px; font-size: 12px;"> Clear All</button>
                                        </div>
                                        <div class="clearfix"></div>
                                     <div id="prefillerValueWrapper">

                                     </div>

{{--                                    <label class="excelImport" id="#bb"> Enter Your File--}}
{{--                                        <input type="file" id="excelImportFile"  name="files[]">--}}
{{--                                    </label>--}}
{{--                                    <script>--}}
{{--                                        document.getElementById('excelImportFile').addEventListener('change', handleFileSelect, false);--}}

{{--                                    </script>--}}
                                    <div class="clearfix"></div>
                                </div>
                                <div class="col-md-12" >
{{--                                    <div class="field_wrapper">--}}
{{--                                        <div class="form-group" style="    margin-top: 8px;     margin-left: -14px;">--}}
{{--                                            <input type="text" id="firstArryExcelValue" name="text_label[]" class="form-control labelText" value="" placeholder="Label"/>--}}
{{--                                            <a style="position: absolute;right: -12px;display: block;bottom: 20px;background: green;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;" href="javascript:void(0);" class="add_button" type="0" title="Add field"><i class="fa fa-plus" aria-hidden="true"></i></a>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                </div>
                            </section>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div><!--/.modal-body-->
            </div>
        </div>
    </div>


    <div class="modal fade" id="updateDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Text Manager Category</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/updatePrefillerManager' , 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="document_id" name="id">
                            <div class="form-group" style="    padding-bottom: 7px;margin:-15px 0 0 0">
                                <label class="control-label" for="title">title</label>
                                <input type="text" id="title" name="title" class="form-control" maxlength="20">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{ Form::close() }}
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
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this Text Manager Category?</p>
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
    <div class="modal fade" id="prefillers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add New Text Manager</h4>
                </div>
                {{--{{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/savePrefillerLabel', 'files' => true]) }}--}}
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group">

                            <div class="col-md-2">
                                <div style="margin-top: 4px;" class="form-group">
                                    <h4 style="    font-size: 15px;margin-top: -23px;">Prefiller Title :-</h4>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div style="margin-top: -21px;" class="form-group">
                                    <input  style="border: transparent;     margin-left: -29px;"  id="docket_prefiller_label" readonly >
                                </div>
                            </div>
                        </div>
                        <div class="form-group label-floating">
                            <div class="col-md-10">
                                <div style="    margin-top: 4px; " class="form-group">
                                    <input type="hidden" name="docket_prefiller_id" id="docket_prefiller_id">
                                    <label class="control-label"  for="title">Label</label>
                                    <input  type="text"  name="label" id="label" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin: -8px 0 0 0">
                                <button style="" type="submit" class="btn btn-primary" id="saveprefillers">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                </div>
                {{--{{ Form::close() }}--}}
            </div>

        </div>
    </div>

    <div class="modal fade" id="deletePrefillerLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['url' => 'dashboard/company/docketManager/prefillerManager/deletePrefillerLabel' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Text Manager Label</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="deletePrefiller_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this Text Manager Label?</p>
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
@endsection
@section('customScript')
    <style>
        .btnWrapper{
            display: none;
        }
        .prefillerslabels-class{
            display: none;
        }
        .actions > ul > li:nth-child(3) {
            float: right !important;
        }

        .excelImport{
            padding: 6px 6px 6px 6px;
            background: #002a67;
            float: right;
            font-size: 12px;
            border-radius: 5px;
            color: #fff;
            display: block;
            margin-right: -23px;
        }

        .excelImport input[type="file"] {
            display: none !important;
        }
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
        }
        .prefillercontent:hover .btnprefiller{
            display: inline-block;
            cursor: pointer;

        }
        .prefillercontent .btnprefiller {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="{{ asset('assets/dashboard/css/jquery.steps.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script  src="{{asset('assets/jquery.chained.js')}}"></script>
    <!-- <script  src="{{asset('assets/zepto-selector.chained.js')}}"></script> -->
    <script  src="{{asset('assets/zepto.js')}}"></script>
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
    <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
    <script src="{{asset('assets/dashboard/js/jquery.steps.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            $('#updateDocument').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var title = $(e.relatedTarget).data('title');
                $("#document_id").val(id);
                $("#title").val(title);

            });
        });
        $(document).ready(function() {
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: false
            });
            $('#addGridPrefillerModel').on('show.bs.modal', function(e) {
                $(".parentData .gridprefillerInvValue").val('')
                var prefillerManagerId = $('#prefillerManagerId').val();
                var prefillerIsInteger = $('#prefillerIsInteger').val();
                var id = $('.parentData .parentManagerId').val(prefillerManagerId)
                if (prefillerIsInteger == 0){
                    $(".parentData .gridprefillerInvValue").prop("type", "text");
                }else if(prefillerIsInteger == 1){
                    $(".parentData .gridprefillerInvValue").prop("type", "number");
                }
            });
        });
        $('#saveParentPrefiller').click(function () {
            var  prefillerManagerId = $(".parentData .parentManagerId").val();
            var  index = 0;
            var rootId = 0;
            var value = $(".parentData .gridprefillerInvValue").val();
            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketManager/prefillerManager/saveParentPrefillerLabel')}}",
                data:{prefillerManagerId:prefillerManagerId,index:index,rootId:rootId,value:value},
                success:function (response) {
                    var wrapperId = "#prefillerValueWrapper";
                    var prefillerData   =   "";
                    jQuery.each( response['finalPrefillerView'], function( i, val ) {
                        prefillerData = prefillerData + val['final'];
                    });
                    if (prefillerData == ""){
                        var finalView = '<p style="color: #adacac;text-align: center;" class="prefillerEmptyView'+prefillerManagerId+'">Empty</p>';
                    }else{
                        var finalView = '<table  style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">'+prefillerData+'</table>';
                    }
                    $(wrapperId).html(finalView);
                    $('.editabledocketprefiller').editable({
                        mode:"inline"
                    });
                    $('#addGridPrefillerModel').modal('hide');
                }
            });
        });


        $(document).on('click','#deleteprefillerManagerLabel', function () {
            var prefillerManagerId = $(this).attr('data-docketprefillermanagerid');
            $(".spinnerCheckgrid").css('display','block');
            var id =  $(this).attr('data-id');
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketBookManager/designDocket/deletePrefillerManagerlabel')}}",
                data: {
                    label_id : id,
                    prefiller_manager_id : prefillerManagerId,
                },
                success: function (response) {
                    $(".setGridPrefillers").html(response);
                    $(".spinnerCheckgrid").css('display','none');
                    $('.editabledocketgridprefiller').editable({
                        mode:"inline"
                    });

                }
            })
        })



            $(document).on('change', '#isIntegerType', function () {
                var PrefillerType = $(this).val();
                console.log(PrefillerType);
                if (PrefillerType == 0) {
                    //text
                    // text_manager_category
                    $('.labelText').prop({type: "text" })
                    $('.add_button').attr({'type': 0 })


                } else if (PrefillerType == 1) {
                    //number
                    $('.labelText').prop({type: "number"})
                    $('.labelText').attr({onkeydown : "return event.keyCode !== 69"})
                    $('.add_button').attr({'type': 1})

                }

            });

    </script>
    <script>
        $(document).ready(function (e, anchorObject, stepNumber, stepDirection) {
                $("#wizard").steps({
                    startIndex: 0,
                    enableFinishButton: false,
                    labels: {
                        finish: "Save"
                    },

                    onStepChanged: function (event, currentIndex, priorIndex) {
                        var titleCat = $(".text_manager_category").val();
                        var is_integer = $("#isIntegerType").val();
                        if ( titleCat == ""){
                            $('.flashsuccessText').css('display','block');
                            $("#wizard").steps("previous");
                        }else{
                            $("#text_manager_category_titles").text(titleCat);
                            $('.flashsuccessText').css('display','none');
                            if (currentIndex == 1){
                                $.ajax({
                                    type: "post",
                                    url: "{{url('dashboard/company/docketManager/prefillerManager/addPrefillerManager')}}",
                                    data: {title: titleCat,is_integer:is_integer},
                                    success: function (response) {
                                        if (response['status'] == false) {
                                            var wrappermessage = ".messageErrortext_manager_category";
                                            $(wrappermessage).html(response["message"]);
                                            $(".spinerPrefiller").css("display", "none");
                                            $("#wizard").steps("previous");
                                            $(wrappermessage).show().delay(5000).fadeOut(400);
                                        } else if (response['status'] == true){

                                            $("#wizard").steps("next");
                                            $("#wizard-t-0").removeAttr("href");
                                            // $("#wizard-t-0").parent('li').remove();
                                            $("#prefillerIsInteger").val(response['isInteger'] )
                                            $("#prefillerManagerId").val(response['prefillerManagerId'] )
                                        }
                                    }
                                });
                            }
                        }
                    },
                    onFinished: function (event, currentIndex,startIndex) {
                        $(".spinerPrefiller").css("display", "block");
                        var titleCats = $(".text_manager_category").val();
                        var is_integer = $("#isIntegerType").val();
                        var titleCat = $("#firstArryExcelValue").val();
//                        var allFilled=true;
//                        $('input[name="text_label[]"]').each(function() {
//                            if ($(this).val() == "") {
//                            $(".spinerPrefiller").css("display", "none");
//                            $('.flashsuccessText1').show().delay(5000).fadeOut(400);
//
//                        }else{
                        if (titleCat==""){
                            $(".spinerPrefiller").css("display", "none");
                            $('.flashsuccessText1').show().delay(5000).fadeOut(400);
                        }else {
                            $('.flashsuccessText1').css('display','none');
                            var titlabelles = $('input[name^=text_label]').map(function (idx, elem) {
                                return $(elem).val();
                            }).get();
                            $.ajax({
                                type: "post",
                                url: "{{url('dashboard/company/docketManager/prefillerManager/addPrefillerManager')}}",
                                data: {title: titleCats, label: titlabelles,is_integer:is_integer},
                                success: function (response) {
                                    if (response['status'] == false) {
                                        var wrappermessage = ".messageErrortext_manager_category";
                                        $(wrappermessage).html(response["message"]);
                                        $(".spinerPrefiller").css("display", "none");
                                        $("#wizard").steps("previous");
                                        $(wrappermessage).show().delay(5000).fadeOut(400);
                                    } else {
                                        window.location.replace(response["url"]);

                                    }
                                }
                            });
                        }
//                        }
//                        });
                    }
                });

            $("a[href$='previous']").remove()
            $('#myModal3').on('shown.bs.modal', function () {
                $('#myWizard').easyWizard();
            });

        });
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteDocument').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#deleteDocument_id").val(id);

            });
        });
        $(document).ready(function() {
            $('#deletePrefillerLabel').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#deletePrefiller_id").val(id);

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#prefillers').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var title = $(e.relatedTarget).data('title');
                var prefillerType = $(e.relatedTarget).data('prefillertype');
                $("#docket_prefiller_id").val(id);
                $("#docket_prefiller_label").val(title);

                if (prefillerType == 0){
                    $('#label').prop({type: "text"})

                } else if (prefillerType == 1) {
                  $('#label').prop({type: "number"})
                  $('#label').attr({onkeydown : "return event.keyCode !== 69"})
                }




            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.editabledocketprefiller').editable({
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
            $(".btnWrapper").show();

            function  updatebuttonprefiller(clickedID){
                alert(clickedID);

            }
            $(".prefillerslabels-class").show();

            function  prefillerslabels(clickedIDs){
                alert(clickedIDs);

            }
        });
    </script>
    {{--<script>--}}
        {{--$('#addprefillercategory').click(function () {--}}
            {{--var prefillertitle = $("#prefillertitle").val();--}}
            {{--$.ajax({ type: "POST",--}}
            {{--url: "{{ url('dashboard/company/docketManager/prefillerManager/addPrefillerManager') }}",--}}
            {{--data: { title: prefillertitle},--}}
            {{--success: function(response){--}}
{{--//                if (response == "Prefiller added successfully") {--}}
{{--//                    $('#myModal').modal('hide');--}}
{{--//                    alert(response);--}}
{{--//--}}
{{--//                }else--}}
{{--//--}}
                {{--if(response == "Invalid attempt! Already Added") {--}}
                    {{--$.when($('#sortabless').append(response)).done(function () {--}}

                    {{--});--}}
                    {{--$('#myModal').modal('hide');--}}

                {{--}else {--}}
                    {{--window.location.reload();--}}
                    {{--$.when($('#sortabless').append(response)).done(function () {--}}

                    {{--});--}}

                {{--}--}}
            {{--}--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}

    <script>
        $('#saveprefillers').click(function () {
            var  prefillerlabels = $("#label").val();
            var  saveprefillerdocketprefillerid = $("#docket_prefiller_id").val();
            $.ajax({
                type:"post",
                url:"{{url('dashboard/company/docketManager/prefillerManager/savePrefillerLabel')}}",
                data:{docket_prefiller_id:saveprefillerdocketprefillerid,label:prefillerlabels},
                success:function (response) {
                    $('.dashboardFlashsuccess').css('display','none');
                    if(response['status']==true) {
                        var wrapperId = "#prefillerValueWrapper"+$("#docket_prefiller_id").val();
                        $(wrapperId).append(response["label"]);
                        $('.editable').editable();
                        var wrappermessage = ".messagesucess";
                        $(wrappermessage).html(response["message"]);
                        $('.dashboardFlashsuccess').css('display','block');
                    }
                    $('#prefillers').modal('hide');
                    $('[name="label"]').val('');

                }


            });

//            $("#prefillers")[0].reset();
        });


    </script>
    <script type="text/javascript">
        $(document).ready(function(){
            var maxField = 1000; //Input fields increment limitation
            var addButton = $('.add_button'); //Add button selector
            var wrapper = $('.field_wrapper'); //Input field wrapper
            var fieldHTML = '<div  class="form-group" style="    margin-top: 8px;    margin-left: -14px;"><input type="text" name="text_label[]" class="form-control labelText" value="" placeholder="Label"/><a href="javascript:void(0);" class="remove_button" style="  position: absolute;right: -12px;display: block;bottom: 20px;background: red;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;"><i class="fa fa-minus" aria-hidden="true"></i></a></div>'; //New input field html


            var fieldHTMLnumber = '<div  class="form-group" style="    margin-top: 8px;    margin-left: -14px;"><input type="number" name="text_label[]" onkeydown="return event.keyCode !== 69" class="form-control labelText" value="" placeholder="Label"/><a href="javascript:void(0);" class="remove_button" style="  position: absolute;right: -12px;display: block;bottom: 20px;background: red;padding: 1px 6px;color: #fff;height: 23px;border-radius: 34px;"><i class="fa fa-minus" aria-hidden="true"></i></a></div>'; //New input field html

            var x = 0; //Initial field counter is 1

            //Once add button is clicked
            $(addButton).click(function(){
                //Check maximum number of input fields
               if ($(this).attr('type')==0){
                   if(x < maxField){
                       x++; //Increment field counter
                       $(wrapper).append(fieldHTML); //Add field html
                   }
               }else if($(this).attr('type')==1){
                   if(x < maxField){
                       x++; //Increment field counter
                       $(wrapper).append(fieldHTMLnumber); //Add field html
                   }
               }
            });

            //Once remove button is clicked
            $(wrapper).on('click', '.remove_button', function(e){
                e.preventDefault();
                $(this).parent('div').remove(); //Remove field html
                x--; //Decrement field counter
            });
        });

    </script>






@endsection