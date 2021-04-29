<div class="modal-body" style="height: 547px;">
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
                    <input class="dependentPrefillerDataType" type="hidden" value="2">
            @elseif($docketFielddata->docket_field_category_id ==3)
                    <input class="dependentPrefillerDataType" type="hidden" value="1">
            @else
                <input class="dependentPrefillerDataType" type="hidden" value="0">
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
                <div class="col-md-12">

                    <select class="form-control saveDocketFieldPrefillerManager" style="margin: 16px 4px 22px -15px;">
                        <option value="0" @if($docketFielddata->docket_prefiller_id == 0)selected @endif>None</option>
                        @foreach($docketPrefillerManager as $data)
                            @if($docketFielddata->docket_field_category_id == 29)
                                @if($data->is_integer == 2)
                                    <option value="{{$data->id}}" isInteger="{{$data->is_integer}}" datatype="{{$data->type}}" @if($docketFielddata->docket_prefiller_id == $data->id)selected @endif>{{$data->title}}</option>
                                @endif
                            @elseif($docketFielddata->docket_field_category_id ==3)
                                @if($data->is_integer == 1)
                                    <option value="{{$data->id}}" isInteger="{{$data->is_integer}}" datatype="{{$data->type}}" @if($docketFielddata->docket_prefiller_id == $data->id)selected @endif>{{$data->title}}</option>
                                @endif
                            @else
                                <option value="{{$data->id}}" isInteger="{{$data->is_integer}}" datatype="{{$data->type}}" @if($docketFielddata->docket_prefiller_id == $data->id)selected @endif>{{$data->title}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <br>
                <div>
                    <div style="float: left;">
                        <strong>Prefillers</strong>
                        <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                <div class="setGridPrefillers" style="    height: 324px;overflow-y: auto; overflow-x: auto;width: 99%;">
                    @if(@$finalPrefillerView)
                        <div id="prefillerValueWrapper{{ $docketFielddata->id }}"    >
                            <table style="display: block;overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">
                                @foreach($finalPrefillerView as $data)
                                    {!! $data['final'] !!}
                                @endforeach
                            </table>
                            <div>
                            </div>
                        </div>
                    @else
                        <div id="prefillerValueWrapper{{ $docketFielddata->id }}"   >
                            <p style="color: #adacac;text-align: center;" class="prefillerEmptyView{{ $docketFielddata->id }}">Empty</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


