<div class="modal-body" style="min-height: 547px;">
    <div class="row">
        <div class="col-md-12">

            <div class="headerSubDocket">
                <div class="row">
                    <div style="   margin-bottom: 5px;" class="col-md-2">
                        <strong> Label:</strong>
                    </div>
                    <div style="   margin-bottom: 5px;" class="col-md-10">
                        <span style="    padding: 5px 29px;font-size: 15px;color: black;font-weight: 300;border: 1px solid #CED4DA;background: #E9ECEF;">{{$docketFielddata->label}}</span>
                        {{--                        <span style="    padding: 5px 29px;font-size: 15px;color: black;font-weight: 300;border: 1px solid #CED4DA;background: #E9ECEF;">{!! @$docketGridField->label !!}</span>--}}
                    </div>
                </div>
            </div>
            @if($docketFielddata->docket_field_category_id == 29)
                <input class="inDependentPrefillerDataType" type="hidden" value="2">
            @elseif($docketFielddata->docket_field_category_id ==3)
                <input class="inDependentPrefillerDataType" type="hidden" value="1">
            @else
                <input class="inDependentPrefillerDataType" type="hidden" value="0">
            @endif

            <input type="hidden" id="prefiller_docket_field_id" value="{{$docketFielddata->id}}">
            <input type="hidden" id="isDependentData" value="{{$docketFielddata->is_dependent}}">
            <br>
            <div  class="formElement" style="margin-bottom:0px;">
                <div class="col-md-12">
                    <div class="col-md-12">
                        <p style="float: left;margin: 0px 27px 0px -26px;font-size: 16px; color: #000000;font-weight: normal;"> Prefiller Manager: </p>
                        <input style="    float: left;margin-right: 12px;" type="checkbox" id="normalPrefillerInDependent" docketFieldId="{{$docketFielddata->id}}" docket_id="{{$tempDocket->id}}" @if($docketFielddata->is_dependent == 0) value="{{$docketFielddata->is_dependent}}" checked disabled @else value="0" @endif>
                        <p style="float: left; margin: 0px 27px 0px 0px;font-size: 14px; color: #000000;font-weight: normal;"> Independent </p>
                        <input style="    float: left;margin-right: 12px;" type="checkbox" id="normalPrefillerDependent" docketFieldId="{{$docketFielddata->id}}"  docket_id="{{$tempDocket->id}}"  @if($docketFielddata->is_dependent == 1) value="{{$docketFielddata->is_dependent}}" checked disabled @else value="1" @endif>
                        <p style="float: left;font-size: 14px; color: #000000;font-weight: normal;"> Dependent </p>

                        <input style="float: left;margin-right: 12px; margin-left: 25px;" type="checkbox" id="normalPrefillerEcowise" docketFieldId="{{$docketFielddata->id}}"  docket_id="{{$tempDocket->id}}"  @if($docketFielddata->is_dependent == 2) value="{{$docketFielddata->is_dependent}}" checked disabled @else value="2" @endif>
                        <p style="float: left;font-size: 14px; color: #000000;font-weight: normal;"> Api Link </p>
                    </div>


                </div>
                <br>
                <br>
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
                </div>

                <div class="col-md-4">
                    <label class="control-label">API URL</label>
                    <input required  class="form-control ecowiseurl"  type="url" placeholder="URL" name="url">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-info btn-xs btn-raised normalConnectEcowise" style="background: #15B1B8;margin: 37px 0px 0px 0px;">CONNECT</button>
                </div>
                <br>

                <div class="col-md-11 changeSelectUrl" style="margin: 30px 0 30px 0px;" >
                    <select class="form-control selectNormalUrl">
                        <option value="0">Please Select Url</option>
                        @foreach($prefillerEcowise as $allUrls)
                            <option  value="{{$allUrls->id}}" @if($docketFielddata->echowise_id == $allUrls->id) selected @endif>{{$allUrls->url}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-11">
                    <strong>Filter</strong>
                    <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    <div class="filterNormalViewLink" style="   padding: 37px 0px 0px 0px;">
                        <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerFilterLinkPrefiller">
                            <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="col-md-12 ecowiseFieldFilterView{{$docketFielddata->id}}" id="dynamicNormalPrefillerFilterField">

                        @if($docketFielddata->echowise_id == 0)
                                 <p style="font-size: 14px;   text-align: center; text-align: center;"> Please select Url</p>
                             @else
                                    @if($docketFielddata->echowise_id != null)
                                        @if(count($docketFielddata->linkPrefillerFilter)!= 0)
                                            @foreach($docketFielddata->linkPrefillerFilter as $key=>$data)
                                                <div style="margin: 0px 0px 30px 0px;"><br>

                                                    <div class="col-md-6">
                                                        <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseNormalFieldFilter">
                                                            <option  value="0"    id="{{$docketFielddata->id}}"  docketId="{{$docketFielddata->docket_id}}" linkprefillerfilterid="{{$data->id}}">Select Index</option>
                                                            @foreach($keyValue as $keyValues)
                                                                <option  linkprefillerfilterid="{{$data->id}}"     id="{{$docketFielddata->id}}"  value="{{ $keyValues}}" @if($data->link_prefiller_filter_label ==  $keyValues) selected @endif  >{{$keyValues}}</option>
                                                            @endforeach

                                                        </select>
                                                    </div>
                                                    <div class="col-md-5" style="margin: -9px 0 0 0;">
                                                        <select  class="form-control ecowiseNormalFieldFilterView{{$docketFielddata->id}} prefillerLinkFilter"  required  >
                                                            <option docketfieldId="{{$docketFielddata->id}}" value="0">Please Select Filter Value</option>
                                                            <option docketfieldId="{{$docketFielddata->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Empty Data">Empty Data</option>
                                                            <option docketfieldId="{{$docketFielddata->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Not Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Not Empty Data">Not Empty Data</option>
                                                            @if($docketFielddata->prefillerEcowise)
                                                                @foreach(array_unique(json_decode($docketFielddata->prefillerEcowise->data, true)[$data->link_prefiller_filter_label], SORT_REGULAR) as $keys=> $row)
                                                                    @if($row != [])
                                                                        <option docketfieldId="{{ $docketFielddata->id }}" linkprefillerfilterid="{{$data->id}}"   @if(str_replace(array("\n", "\t"), '', $row) == $data->link_prefiller_filter_value) selected @endif value="{!! $row !!}">{!!  $row !!}</option>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                    </div>


                                                    @if($key == 0)
                                                        <div class="col-md-1">
                                                            <button type="button" name="add"  class="btn btn-success addNormalFilterLinkPrefiller" style="margin: 0;" data-fieldId="{{ $docketFielddata->id }}">Add</button>
                                                        </div>
                                                    @else
                                                        <div class="col-md-1">
                                                            <button type="button" name="add"  class="btn btn-danger removeNormalFilterLinkPrefiller" style="margin: 0;" data-linkprefillerfilter="{{$data->id}}">Remove</button>
                                                        </div>
                                                    @endif
                                                </div>


                                            @endforeach
                                        @else
                                            <div style="margin: 0px 0px 30px 0px;">
                                                <div class="col-md-6">
                                                    <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseNormalFieldFilter">
                                                        <option  value="0"   id="{{$docketFielddata->id}}"  docketId="{{$docketFielddata->docket_id}}" linkprefillerfilterid="0" >Select Index</option>
                                                        @foreach($keyValue as $keyValues)
                                                            <option    id="{{$docketFielddata->id}}" value="{{ $keyValues}}" linkprefillerfilterid="0"    >{{$keyValues}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6"  style="margin: -9px 0 0 0;">

                                                    <select  class="form-control ecowiseNormalFieldFilterView">
                                                        <option value="0">Please Select Filter Value</option>
                                                    </select>

                                                </div>
                                            </div>
                                        @endif
                                    @endif

{{--                                <div class="col-md-6">--}}
{{--                                  <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseNormalFieldFilter">--}}
{{--                                      <option  value="0" id="{{$docketFielddata->id}}"  docketId="{{$docketFielddata->docket_id}}" >Select Index</option>--}}
{{--                                      @foreach($keyValue as $keyValues)--}}
{{--                                          <option id="{{$docketFielddata->id}}"  docketId="{{$docketFielddata->docket_id}}" docketFieldId="{{$docketFielddata->id}}" value="{{ $keyValues}}" @if($docketFielddata->link_prefiller_filter_label ==  $keyValues) selected @endif  >{{$keyValues}}</option>--}}
{{--                                      @endforeach--}}
{{--                                  </select>--}}
{{--                                </div>--}}
{{--                                <div class="col-md-6 ecowiseNormalFieldFilterView{{$docketFielddata->id}}" style="    margin: -12px 0 0px 0;">--}}
{{--                                    <select  class="form-control prefillerLinkFilter"  required  >--}}
{{--                                        <option value="0">Please Select Filter Value</option>--}}
{{--                                        @if($suggestionValue)--}}
{{--                                            @foreach($suggestionValue as $row)--}}
{{--                                                <option docketfieldId="{{$docketFielddata->id}}" @if(str_replace(array("\n", "\t"), '', $row) === $docketFielddata->link_prefiller_filter_value) selected @endif value="{!! $row !!}">{!!  $row !!}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                </div>--}}

                             @endif
                        </div>
                    </div>
                </div>



                <div class="col-md-12">
                    <div style="float: left; margin-top: 25px;">
                        <strong>Prefillers</strong>
                        <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                <div class="ecoData" style="   overflow-y: auto; overflow-x: auto;width: 99%;    margin-top: 16px;">
                    <table class="updatedNormalEcowiseData{{$docketFielddata->id}}" style="overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">
                        <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
                            <td style="height: 61px; " >
                                @if($docketFielddata->echowise_id != 0)
                                    <div style="width: 150px; font-size: 14px;color: #000000;font-weight: 500; padding: 10px 0 8px 0px;">{!! @$docketFielddata->label !!}</div>
                                    <div class="custom-select" style="width:auto; margin-top: 17px;">
                                        <select class="selectNormalPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                <option  type="1" value="0" id="{{$docketFielddata->id}}" indexcell="1" docketId="{{$docketFielddata->docket_id}}" >Select Index</option>
                                            @foreach($keyValue as $keyValues)
                                                <option type="1" id="{{$docketFielddata->id}}" indexcell="1" docketId="{{$docketFielddata->docket_id}}"  value="{{str_replace(' ', '_', $keyValues)}}" @if($docketFielddata->selected_index == str_replace(' ', '_', $keyValues)) selected @endif  >{{$keyValues}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div style="font-size: 14px;     padding: 10px 0 8px 0px; text-align: center;"> Please select Url</div>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>


