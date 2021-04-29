@if(@$finalPrefillerView)
    @if(count($docketGridField->gridFieldPreFiller) == 0)
        <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="0">
    @else
        <input type="hidden" class="disableFormulaButton{{$docketGridField->id}}" value="1">
    @endif
    <div id="prefillerValueWrapper{{ $docketGridField->id }}"   >
        @if(in_array($docketGridField->id, array_column($finalPrefillerView, 'id')) == true)
            <table style="overflow-x: auto;white-space: nowrap;padding-bottom: 15px; overflow-y: hidden; margin-bottom: 10px;">
                <tr style="height: 61px; background: #E9ECEF;    border: 1px solid #CED4DA; display: none; ">
                    <td style="font-size: 14px;color: #000000;font-weight: 500;"><div style="width: 150px;">{!! $docketGridField->label !!}</div></td>
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
