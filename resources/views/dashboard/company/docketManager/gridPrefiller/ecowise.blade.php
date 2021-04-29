<div class="modal-body" style="min-height: 547px;">
    <div class="row">
        <div class="col-md-12">

            <div class="headerSubDocket">
                <div class="row">
                    <div style="   margin-bottom: 5px;" class="col-md-2">
                        <strong>Grid Label:</strong>
                    </div>
                    <div style="   margin-bottom: 5px;" class="col-md-10">
                        <span style="    padding: 5px 29px;font-size: 15px;color: black;font-weight: 300;border: 1px solid #CED4DA;background: #E9ECEF;">{!! $docketGridField->label !!}</span>
                    </div>
                </div>
            </div>
            {{--            <input type="hidden" value="{{$docketGridField->id}}" id="docketFieldGridIds">--}}
            <input type="hidden" id="gridprefillerTypechecks">
            <input type="hidden" id="grid_echowise_id" value="{{$docketGridField->echowise_id}}">
            <input type="hidden" id="grid_docket_field_id" value="{{$docketGridField->docket_field_id}}">
            <input type="hidden" id="grid_ids" value="{{$docketGridField->id}}">
            <input type="hidden" id="dynamicAutoFieldId" value="{{$docketGridField->auto_field}}">
            <input type="hidden" id="isDependentData" value="{{$docketGridField->is_dependent}}">

            <br>
            <div  class="formElement" style="margin-bottom:0px;">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <p style="float: left;margin: 0px 27px 0px -26px;font-size: 16px; color: #000000;font-weight: normal;"> Prefiller Manager: </p>
                        <input style="    float: left;margin-right: 12px;" type="checkbox" id="prefillerInDependent" docketFieldId="{{$docketField->id}}" gridprefillerId="{{$docketGridField->id}}" @if($docketGridField->is_dependent == 0) value="{{$docketGridField->is_dependent}}" checked disabled @else value="0" @endif>
                        <p style="float: left; margin: 0px 27px 0px 0px;font-size: 14px; color: #000000;font-weight: normal;"> Independent </p>
                        <input style="    float: left;margin-right: 12px;" type="checkbox" id="prefillerDependent" docketFieldId="{{$docketField->id}}"  gridprefillerId="{{$docketGridField->id}}"  @if($docketGridField->is_dependent == 1) value="{{$docketGridField->is_dependent}}" checked disabled @else value="1" @endif>
                        <p style="float: left;font-size: 14px; color: #000000;font-weight: normal;"> Dependent </p>
                        <input style="    float: left;margin-right: 12px;    margin-left: 25px;" type="checkbox" id="prefillerEcowise" docketFieldId="{{$docketField->id}}"  gridprefillerId="{{$docketGridField->id}}"  @if($docketGridField->is_dependent == 2) value="{{$docketGridField->is_dependent}}" checked disabled @else value="2" @endif>
                        <p style="float: left;font-size: 14px; color: #000000;font-weight: normal;"> Ecowise </p>
                    </div>

                </div>
                <div class="clearfix"></div>
                <br>
                <p class="label-danger errormessage" style="display: none"></p>
                <p class="label-success successmessage" style="display: none"></p>
                <div class="col-md-3">
                    <label class="control-label">Username</label>
                    <input  required class="form-control ecowiseusername"  type="text" placeholder="Username"  name="username">
                </div>

                <div class="col-md-3">
                    <label class="control-label">Password</label>
                    <input required class="form-control ecowisepassword" type="password" placeholder="Password" name="password" >
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password" style="    position: absolute;top: 42px;right: 15px;background: #e9ecef;"></span>
                </div>

                <div class="col-md-4">
                    <label class="control-label">API URL</label>
                    <input required  class="form-control ecowiseurl"  type="url" placeholder="URL" name="url">
                </div>
                <div class="col-md-2">
                     <button class="btn btn-info btn-xs btn-raised connectEcowise" style="background: #15B1B8;margin: 37px 0px 0px 0px;">CONNECT</button>
                </div>
                <br>



                <div class="col-md-11 changeSelectUrl" style="margin: 30px 0 30px 0px;" >
                    <select class="form-control selectUrl">
                        <option value="0">Please Select Url</option>
                        @foreach($prefillerEcowise as $allUrls)
                            <option  value="{{$allUrls->id}}" @if($docketGridField->echowise_id == $allUrls->id) selected @endif>{{$allUrls->url}}</option>
                        @endforeach
                    </select>
                </div>


                <br>
                <div class="clearfix"></div>
                <br>

                <div class="col-md-11">
                    <strong>Filter</strong>
                    <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    <div class="filterViewLink" style="padding: 20px 0px 60px 0px; ">
                        <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerFilterLinkPrefiller">
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                            <span class="sr-only">Loading...</span>
                        </div>



                            <div class="col-md-12 ecowiseGridFieldFilterView{{$docketGridField->id}}" id="dynamicPrefillerFilterField">
                                @if($docketGridField->echowise_id == 0)
                                    <p style="font-size: 14px;   text-align: center; text-align: center;"> Please select Url</p>
                                @else
                                     @if($docketGridField->echowise_id != null)
                                      @if(count($docketGridField->linkPrefillerFilter)!= 0)
                                        @foreach($docketGridField->linkPrefillerFilter as $key=>$data)
                                            <div style="margin: 0px 0px 30px 0px;">

                                                <div class="col-md-6">
                                                    <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseGridFieldFilter">
                                                        <option  value="0" gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" >Select Index</option>
                                                        @foreach($keyValue as $keyValues)
                                                            <option gridId="{{ $docketGridField->id }}" linkprefillerfilterid="{{$data->id}}"   fieldId="{{$docketField->id}}" value="{{ $keyValues}}" @if($data->link_prefiller_filter_label ==  $keyValues) selected @endif  >{{$keyValues}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-5" style="margin: -9px 0 0 0;">
                                                    <select  class="form-control prefillerGridLinkFilter"  required  >
                                                        <option docketfieldId="{{$docketField->id}}" value="0">Please Select Filter Value</option>
                                                        <option docketfieldId="{{$docketGridField->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Empty Data">Empty Data</option>
                                                        <option docketfieldId="{{$docketGridField->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Not Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Not Empty Data">Not Empty Data</option>

                                                        @if($docketGridField->prefillerEcowise)
                                                            @foreach(array_unique(json_decode($docketGridField->prefillerEcowise->data, true)[$data->link_prefiller_filter_label], SORT_REGULAR) as $keys=> $row)
                                                                @if($row != [])
                                                                   <option docketfieldId="{{ $docketGridField->id }}" linkprefillerfilterid="{{$data->id}}"   @if(str_replace(array("\n", "\t"), '', $row) == $data->link_prefiller_filter_value) selected @endif value="{!! $row !!}">{!!  $row !!}</option>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </div>
                                                @if($key == 0)
                                                    <div class="col-md-1">
                                                        <button type="button" name="add"  class="btn btn-success addfilterLinkPrefiller" style="margin: 0;" data-girdfieldId="{{ $docketGridField->id }}">Add</button>
                                                    </div>
                                                @else
                                                    <div class="col-md-1">
                                                        <button type="button" name="add"  class="btn btn-danger removefilterLinkPrefiller" style="margin: 0;" data-linkprefillerfilter="{{$data->id}}">Remove</button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach



                                       @else
                                            <div style="margin: 0px 0px 30px 0px;">
                                                <div class="col-md-6">
                                                    <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseGridFieldFilter">
                                                        <option  value="0" gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" linkprefillerfilterid="0" >Select Index</option>
                                                        @foreach($keyValue as $keyValues)
                                                            <option gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" value="{{ $keyValues}}" linkprefillerfilterid="0"    >{{$keyValues}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6"  style="margin: -9px 0 0 0;">

                                                    <select  class="form-control prefillerGridLinkFilter">
                                                        <option value="0">Please Select Filter Value</option>
                                                    </select>

                                                </div>
                                            </div>
                                       @endif
                                     @endif
                                @endif

                            </div>


                    </div>
                </div>




                <div>
                    <br>
                    <div class="clearfix"></div>
                    <br>
                    <div style="float: left; margin-top: 25px;">
                        <strong>Prefillers</strong>
                        <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    </div>
                    <div style="float: right">
                        <input style="    float: left;margin-right: 12px;" type="checkbox" class="ecowiseCellAutoFill" gridId="{{ $docketGridField->id }}" @if($docketGridField->auto_field == 1) checked @endif value="0"  >
                        <p style="float: left; margin: 0px 27px 0px 0px;font-size: 14px; color: #000000;font-weight: normal;"> Cell Autofill </p>
{{--                        <button  class="btn btn-primary addPrefillerEcowise{{$docketGridField->id}} @if($docketGridField->auto_field == 0) disabled @endif"  data-docketFieldId="{{$docketField->id}}" data-gridId="{{$docketGridField->id}}" data-autofield="{{$docketGridField->auto_field}}"  style="background: #03A9F4;color: #ffffff;padding: 2px 16px; border-radius: 2px;margin: 0 10px 22px 0px;font-size: 12px;">Add</button>--}}
{{--                        <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#clearAllGridPrefillerModal"  style="background: #f44336;color: #ffffff;padding: 2px 16px;  border-radius: 2px;    margin: 0 10px 22px 0px; font-size: 12px;"> Clear All</button>--}}
                    </div>
                    <div class="clearfix"></div>
                </div>
                <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                <div class="ecoData" style="   overflow-y: auto; overflow-x: auto;width: 99%;    margin-top: 16px;">
                    <table class="updateedEcowiseData{{$docketGridField->id}}" style="overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">

                          @if($docketGridField->auto_field == 0)
                            @if($docketGridField->auto_field == 0)
                                <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
                                    <td style="height: 61px; " >
                                        @if($docketGridField->echowise_id != 0)
                                            <div style="width: 150px; font-size: 14px;color: #000000;font-weight: 500; padding: 10px 0 8px 0px;">{!! @$docketGridField->label !!}</div>
                                            <div class="custom-select" style="width:auto; margin-top: 17px;">
                                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                    @if(count($docketGridField->gridFieldAutoPreFiller) == 0)
                                                        <option  type="1" value="0" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}" >Select Index</option>
                                                    @endif
                                                    @foreach($keyValue as $keyValues)
                                                        <option type="1" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}" @if($docketGridField->selected_index_value == str_replace(' ', '_', $keyValues)) selected @endif  >{{$keyValues}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @else
                                            <div style="font-size: 14px;     padding: 10px 0 8px 0px; text-align: center;"> Please select Url</div>
                                        @endif

                                    </td>
                                </tr>
                            @endif
                          @else
                            @if($docketGridField->auto_field == 1)
                                @if($docketGridField->echowise_id != 0)
                                    <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
                                        <td>
                                            <div style="width: 150px; font-size: 14px;color: #000000;font-weight: 500; padding: 10px 0 8px 0px;">{!! @$docketGridField->label !!}</div>
                                            <div class="custom-select" style="width:auto; margin-top: 17px;">
                                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                    @if(count($docketGridField->gridFieldAutoPreFiller) == 0)
                                                        <option  type="1" value="0" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}" >Select Index</option>
                                                    @endif
                                                    @foreach($keyValue as $keyValues)
                                                        <option type="1" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}" @if($docketGridField->selected_index_value == str_replace(' ', '_', $keyValues)) selected @endif  >{{$keyValues}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>

                                        @for($sn = 0; $sn < count($finaldata); $sn++)
                                            @if($docketGridField->selected_index_value == null)
                                                <td style="height: 61px;">
                                                    <div class="custom-select" style="width:auto;">
                                                        <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                            <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                            @foreach ($finaldata as $allDocketGridFieldss)
                                                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                        <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                            <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                            @foreach($keyValue as $keyValues)
                                                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </td>
                                            @else
                                                @if(count($docketGridField->gridFieldAutoPreFiller) == 0)
                                                    @if($sn+2 <= 2)
                                                        <td style="height: 61px;">
                                                            <div class="custom-select" style="width:auto;">
                                                                <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                    @foreach ($finaldata as $allDocketGridFieldss)
                                                                        <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                    <option type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                    @foreach($keyValue as $keyValues)
                                                                        <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td style="height: 61px;">
                                                            <div class="custom-select" style="width:auto;">
                                                                <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                    @foreach ($finaldata as $allDocketGridFieldss)
                                                                        <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                    <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                    @foreach($keyValue as $keyValues)
                                                                        <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    @endif
                                                @else
                                                    <?php
                                                        $data = @\App\DocketGridAutoPrefiller::where('grid_field_id',$docketGridField->id)->where('index',$sn+2)->first();
                                                    ?>

                                                        @if(@$data)
                                                                <td style="height: 61px;">
                                                                    <div class="custom-select" style="width:auto;">

                                                                        <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" >
                                                                            @if(max($maxIndex)<= $sn+2)
                                                                        <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                            @endif
                                                                        @foreach ($finaldata as $allDocketGridFieldss)
                                                                            @if($allDocketGridFieldss['isdisabled'] == true)
                                                                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" disabled @if($allDocketGridFieldss['id'] == @$data->link_grid_field_id) selected @endif  >{{$allDocketGridFieldss['label']}}</option>
                                                                            @else
                                                                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" @if($allDocketGridFieldss['id'] == @$data->link_grid_field_id) selected @endif  >{{$allDocketGridFieldss['label']}}</option>
                                                                            @endif
                                                                        @endforeach
                                                                </select>

                                                            </div>
                                                            <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                    <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" >
                                                                        @if(max($maxIndex)<= $sn+2)
                                                                        <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                        @endif
                                                                        @foreach($keyValue as $keyValues)
                                                                            <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" @if(@$data->selected_index == str_replace(' ', '_', $keyValues)) selected @endif  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                        @else
                                                            @if(max($maxIndex)> $sn)
                                                                <?php
                                                                  $data = @\App\DocketGridAutoPrefiller::where('grid_field_id',$docketGridField->id)->where('index',max($maxIndex))->first();
                                                                ?>
                                                               @if($data->selected_index != '' && $data->selected_index !=  null)
                                                                <td style="height: 61px;">
                                                                    <div class="custom-select" style="width:auto;">
                                                                        <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" >
                                                                            <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                            @foreach ($finaldata as $allDocketGridFieldss)
                                                                                @if($allDocketGridFieldss['isdisabled'] == true)
                                                                                    <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  disabled >{{$allDocketGridFieldss['label']}}</option>
                                                                                @else
                                                                                    <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"   >{{$allDocketGridFieldss['label']}}</option>

                                                                                @endif
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                    <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                        <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" >
                                                                            <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                            @foreach($keyValue as $keyValues)
                                                                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                </td>
                                                                   @else
                                                                        <td style="height: 61px;">
                                                                            <div class="custom-select" style="width:auto;">
                                                                                <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                                    @foreach ($finaldata as $allDocketGridFieldss)
                                                                                        <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"   >{{$allDocketGridFieldss['label']}}</option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>
                                                                            <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                                    <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                                    @foreach($keyValue as $keyValues)
                                                                                        <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                                    @endforeach
                                                                                </select>

                                                                            </div>
                                                                        </td>
                                                                @endif

                                                            @else

                                                                <td style="height: 61px;">
                                                                    <div class="custom-select" style="width:auto;">
                                                                        <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                            <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>
                                                                            @foreach ($finaldata as $allDocketGridFieldss)
                                                                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"   >{{$allDocketGridFieldss['label']}}</option>
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                    <div class="custom-select" style="width:auto;  margin-top: 17px;">
                                                                        <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>
                                                                            <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>
                                                                            @foreach($keyValue as $keyValues)
                                                                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn+2}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>
                                                                            @endforeach
                                                                        </select>

                                                                    </div>
                                                                </td>
                                                            @endif
                                                        @endif

                                                @endif


                                            @endif

                                         @endfor






                                    </tr>
                                @else
                                    <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
                                        <td>
                                            <div style="font-size: 14px;     padding: 10px 0 8px 0px; text-align: center;"> Please select Url</div>

                                        </td>
                                    </tr>
                                @endif


                            @endif
                          @endif
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>


