<div class="modal-body" style="height: 547px;">
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

{{--                <div class="col-md-12">--}}
{{--                    <div>--}}
{{--                        <input style="    float: left;margin-right: 12px;" type="checkbox" class="gridPrefillerLinkCheck"  value="1">--}}
{{--                        <p style="float: left"> Check to link Prefiller </p>--}}
{{--                        <span class="spinnerCheck" style="padding:0 0px 0px 165px;font-size: 14px; display:none;"><i class="fa fa-spinner fa-spin"></i></span>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="clearfix"></div>--}}
{{--                <div style="height: 95px;">--}}
{{--                    <div class="gridappenddatabytype" >--}}
{{--                    </div>--}}
{{--                    <div class="clearfix"></div>--}}
{{--                    <div class="gridappendvaluetype" >--}}
{{--                        <div class="col-md-1">--}}
{{--                            <div class="form-group float-left">--}}
{{--                                <label style="margin: 9px 0 0 0;font-size: 15px;" class="control-label" for="title" >Value:</label>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="col-md-11">--}}
{{--                            <div style="    margin-top: 15px;" class="form-group">--}}
{{--                                <input  type="text"  name="value" maxlength="20" class="form-control" id="gridvalueprefiller">--}}
{{--                                <h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="clearfix"></div>
{{--                <div class="modal-footer">--}}
{{--                    <button type="submit" class="btn btn-primary" id="saveGridPrefiller">Save</button>--}}
{{--                </div>--}}
                <br>
                <div>
                    <div style="float: left;">
                        <strong>Prefillers</strong>
                        <hr style="border: 1px solid #13aeb5;height: 0; width: 80px; margin: 5px 0px 10px 1px;">
                    </div>
                    <div style="float: right">
                        <input style="    float: left;margin-right: 12px;" type="checkbox" class="cellAutoFill" gridId="{{ $docketGridField->id }}" @if($docketGridField->auto_field == 1) checked @endif value="0" @if($autoCheckStatus == true) disabled @endif >
                        <p style="float: left; margin: 0px 27px 0px 0px;font-size: 14px; color: #000000;font-weight: normal;"> Cell Autofill </p>
                        <button type="submit" class="btn btn-primary"  data-toggle="modal" data-target="#addGridPrefillerModel" data-docketFieldId="{{$docketField->id}}" data-gridId="{{$docketGridField->id}}" data-autofield="{{$docketGridField->auto_field}}" style="background: #03A9F4;color: #ffffff;padding: 2px 16px; border-radius: 2px;margin: 0 10px 22px 0px;font-size: 12px;">Add</button>
                        <button type="submit" class="btn btn-danger" data-toggle="modal" data-target="#clearAllGridPrefillerModal"  style="background: #f44336;color: #ffffff;padding: 2px 16px;  border-radius: 2px;    margin: 0 10px 22px 0px; font-size: 12px;"> Clear All</button>
                    </div>
                    <div class="clearfix"></div>
                </div>

                <span class="spinnerCheckgrid" style="font-size: 30px;position: absolute;z-index: 11;left: 50%;bottom: 22%; display: none;"><i class="fa fa-spinner fa-spin"></i></span>
                <div class="setGridPrefillers" style="    height: 324px;overflow-y: auto; overflow-x: auto;width: 99%; margin-top: 16px;">
                    @if(@$finalPrefillerView)
                        @if(count($docketGridField->gridFieldPreFiller) == 0)
                            <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="0">
                        @else
                            <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="1">
                        @endif
                        <div id="prefillerValueWrapper{{ $docketGridField->id }}" >
                            @if(in_array($docketGridField->id, array_column($finalPrefillerView, 'id')) == true)
                                <table style="overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">

                                    <tr  @if($docketGridField->auto_field == 1) style="height: 61px; background: #E9ECEF;    border: 1px solid #CED4DA; " @else style="height: 61px; background: #E9ECEF; border: 1px solid #CED4DA;  display: none" @endif >
                                        <td style="font-size: 14px;color: #000000;font-weight: 500; "><div style="width: 150px;">{!! $docketGridField->label !!}</div></td>
                                        @for($x = 0; $x < $finalPrefilMaxIndex-1; $x++)
                                            <?php
                                            $data =   \App\DocketGridAutoPrefiller::where('index',$x+2)->where('grid_field_id',$docketGridField->id)->first();
                                            ?>

                                            @if(count($autoCheckFieldArray) == 0)
                                                    @if($x == 0)
                                                        <td style="height: 61px;">
                                                            <div class="custom-select" style="width:200px;">
                                                                <select class="selectAutoCell" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if(@$data)@else selected @endif >Select Grid Cell</option>
                                                                    @foreach($finalSelectBox as $finalSelectBoxs)
                                                                        <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status']) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id'] ) selected @endif>{{$finalSelectBoxs['label']}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td style="height: 61px;">
                                                            <div class="custom-select" style="width:200px;">
                                                                <select class="selectAutoCell" disabled style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if(@$data)@else selected @endif >Select Grid Cell</option>
                                                                    @foreach($finalSelectBox as $finalSelectBoxs)
                                                                        <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status']) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id'] ) selected @endif>{{$finalSelectBoxs['label']}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                    @endif
                                                @else
                                                  @if(count($autoCheckFieldArray)+1 > $x)
                                                      @if(@$data->index == $x+2 )
                                                            @if($x+1 == count($autoCheckFieldArray) )
                                                                <?php
                                                                $newarray =array_values(array_diff($autoCheckFieldArray, array($data->link_grid_field_id)));
                                                                ?>
                                                                <td style="height: 61px;">
                                                                    <div class="custom-select" style="width:200px;">
                                                                        <select class="selectAutoCell" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                            <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if(@$data)@else selected @endif >Select Grid Cell</option>
                                                                            @foreach($finalSelectBox as $finalSelectBoxs)
                                                                                <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status'] || in_array($finalSelectBoxs['id'],$newarray)) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id']  ) selected  @endif>{{$finalSelectBoxs['label']}} </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                                @else

                                                                <?php
                                                                $newarray =array_values(array_diff($autoCheckFieldArray, array($data->link_grid_field_id)));
                                                                ?>

                                                                    <td style="height: 61px;">
                                                                        <div class="custom-select" style="width:200px;">
                                                                            <select class="selectAutoCell" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                                @foreach($finalSelectBox as $finalSelectBoxs)
                                                                                    <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status'] || in_array($finalSelectBoxs['id'],$newarray)) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id']  ) selected  @endif>{{$finalSelectBoxs['label']}} </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </td>
                                                            @endif
                                                        @else
                                                            <td style="height: 61px;">
                                                                <div class="custom-select" style="width:200px;">
                                                                    <select class="selectAutoCell" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                        <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if(@$data)@else selected @endif >Select Grid Cell</option>
                                                                        @foreach($finalSelectBox as $finalSelectBoxs)
                                                                            <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status'] || in_array($finalSelectBoxs['id'],$autoCheckFieldArray)) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id']  ) selected  @endif>{{$finalSelectBoxs['label']}} </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </td>
                                                      @endif
                                                     @else
                                                        <td style="height: 61px;">
                                                            <div class="custom-select" style="width:200px;">
                                                                <select class="selectAutoCell" disabled style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                                                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if(@$data)@else selected @endif >Select Grid Cell</option>
                                                                    @foreach($finalSelectBox as $finalSelectBoxs)
                                                                        <option value="{{$finalSelectBoxs['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$x+2}}" docketField="{{$docketField->id}}" @if($finalSelectBoxs['status']) disabled  @endif @if(@$data->link_grid_field_id == $finalSelectBoxs['id'] ) selected @endif>{{$finalSelectBoxs['label']}} </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </td>
                                                   @endif
                                                @endif
                                        @endfor
                                    </tr>

                                    @foreach($finalPrefillerView as $data)
                                        @if($docketGridField->id == $data['id'])
                                            {!! $data['final'] !!}
                                        @endif
                                    @endforeach

                                </table>
                                <div>
                            @else
                                 <p style="color: #adacac;text-align: center;" class="prefillerEmptyView{{ $docketGridField->id }}">Empty</p>
                            @endif
                                </div>
                        </div>
                    @else
                        @if(count($docketGridField->gridFieldPreFiller) == 0)
                            <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="0">
                        @else
                            <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="1">
                        @endif
                        <div id="prefillerValueWrapper{{ $docketGridField->id }}"   >
                            <p style="color: #adacac;text-align: center;" class="prefillerEmptyView{{ $docketGridField->id }}">Empty</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


