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

                                                    @endif                                                                            @endforeach
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


        {{--          --><?php //$sn = 2;?> --}}

{{--        @for($x = 2; $x < count($finaldata); $x++)--}}
{{--            {{$x}}--}}

{{--        @endfor--}}

{{--        @foreach($finaldata as $finaldatas)--}}
{{--            @if($docketGridField->selected_index_value == null)--}}
{{--                <td style="height: 61px;">--}}
{{--                    <div class="custom-select" style="width:auto;">--}}
{{--                        <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                            <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                            @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}
{{--                        <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                            <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                            @foreach($keyValue as $keyValues)--}}
{{--                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </td>--}}
{{--            @else--}}
{{--                 @if(count($docketGridField->gridFieldAutoPreFiller) == 0)--}}
{{--                      @if($sn <= 2)--}}
{{--                            <td style="height: 61px;">--}}
{{--                                <div class="custom-select" style="width:auto;">--}}
{{--                                    <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                        <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                                        @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                            <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                                <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}
{{--                                    <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                        <option type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                                        @foreach($keyValue as $keyValues)--}}
{{--                                            <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </td>--}}
{{--                      @else--}}

{{--                        <td style="height: 61px;">--}}
{{--                            <div class="custom-select" style="width:auto;">--}}
{{--                                <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                                    <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                                    @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                        <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                            <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}
{{--                                <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                                    <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                                    @foreach($keyValue as $keyValues)--}}
{{--                                        <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </td>--}}
{{--                     @endif--}}
{{--                 @else--}}

{{--                    @if($sn <= max($maxIndex)+1)--}}
{{--                        @if($finaldatas['same'] == true)--}}

{{--                            <td style="height: 61px;">--}}
{{--                                <div class="custom-select" style="width:auto;">--}}
{{--                                    <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                      @if($sn > max($maxIndex)-1)--}}
{{--                                        <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                                      @endif--}}

{{--                                        @if($sn == $finaldatas['index'])--}}
{{--                                          @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                              <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif @if(@\App\DocketGridAutoPrefiller::where('index',$sn)->where('grid_field_id',$docketGridField->id)->where('link_grid_field_id',$docketGridField->docket_field_id)->first()->selected_index != null) selected @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                          @endforeach--}}
{{--                                        @else--}}
{{--                                            @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                                <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                                <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}

{{--                                    <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                        @if($sn > max($maxIndex)-1)--}}
{{--                                            <option type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                                        @endif--}}

{{--                                        @if($sn = $finaldatas['index'])--}}
{{--                                            @foreach($keyValue as $keyValues)--}}
{{--                                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   @if($finaldatas['selected_index'] == str_replace(' ', '_', $keyValues)) selected @endif  >{{$keyValues}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @else--}}
{{--                                            @foreach($keyValue as $keyValues)--}}
{{--                                                <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}

{{--                                </div>--}}
{{--                            </td>--}}
{{--                        @else--}}

{{--                                <td style="height: 61px;">--}}
{{--                                        <div class="custom-select" style="width:auto;">--}}

{{--                                            <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                                <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                                                @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                                    <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}

{{--                                        </div>--}}
{{--                                        <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}

{{--                                            <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">--}}
{{--                                                <option type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                                                @foreach($keyValue as $keyValues)--}}
{{--                                                    <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                                                @endforeach--}}
{{--                                            </select>--}}

{{--                                        </div>--}}
{{--                                    </td>--}}

{{--                        @endif--}}
{{--                    @else--}}

{{--                            <td style="height: 61px;">--}}
{{--                                <div class="custom-select" style="width:auto;">--}}
{{--                                    <select class="selectAutoCellEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                                        <option value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}" >Select Grid Cell</option>--}}
{{--                                        @foreach ($finaldata as $allDocketGridFieldss)--}}
{{--                                            <option  value="{{$allDocketGridFieldss['id']}}" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  @if($allDocketGridFieldss['isdisabled'] == true) disabled @endif >{{$allDocketGridFieldss['label']}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}

{{--                                </div>--}}
{{--                                <div class="custom-select" style="width:auto;  margin-top: 17px;">--}}
{{--                                    <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;" disabled>--}}
{{--                                        <option  type="2" value="0"  gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  >Select Index</option>--}}
{{--                                        @foreach($keyValue as $keyValues)--}}
{{--                                            <option type="2" gridid="{{$docketGridField->id}}" indexcell="{{$sn}}" docketField="{{$docketGridField->docket_field_id}}"  value="{{str_replace(' ', '_', $keyValues)}}"   >{{$keyValues}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}

{{--                                </div>--}}
{{--                            </td>--}}
{{--                    @endif--}}
{{--                 @endif--}}
{{--            @endif--}}
{{--            <?php $sn++ ?>--}}
{{--        @endforeach--}}




    </tr>
  @else
      <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
          <td>
              <div style="font-size: 14px;     padding: 10px 0 8px 0px; text-align: center;"> Please select Url</div>

          </td>
      </tr>
    @endif


@endif

