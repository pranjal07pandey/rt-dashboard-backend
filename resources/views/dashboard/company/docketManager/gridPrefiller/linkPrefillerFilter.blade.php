
@if($viewStatus == "false")
    @if($docketGridField->echowise_id == 0)
        <p style="font-size: 14px;   text-align: center; text-align: center;"> Please select Url</p>
    @else
            <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerFilterLinkPrefiller">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                <span class="sr-only">Loading...</span>
            </div>

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
                                    <option docketfieldId="{{$docketGridField->id}}" value="0">Please Select Filter Value</option>
                                    <option docketfieldId="{{$docketGridField->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Empty Data">Empty Data</option>
                                    <option docketfieldId="{{$docketGridField->id}}" linkprefillerfilterid="{{$data->id}}" @if( "Not Empty Data" == $data->link_prefiller_filter_value) selected @endif value="Not Empty Data">Not Empty Data</option>

                                @if($docketGridField->prefillerEcowise)
                                        @foreach(array_unique(json_decode($docketGridField->prefillerEcowise->data, true)[$data->link_prefiller_filter_label],SORT_REGULAR) as $keys=> $row)
                                            @if($row != [])
                                               <option docketfieldId="{{ $docketGridField->id }}" linkprefillerfilterid="{{$data->id}}"  @if(str_replace(array("\n", "\t"), '', $row) == $data->link_prefiller_filter_value) selected @endif value="{!! $row !!}">{!!  $row !!}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                            </div>


                            @if($key == 0)
                                <div class="col-md-1">
                                    <button type="button" name="add" class="btn btn-success addfilterLinkPrefiller" style="margin: 0;" data-girdfieldId="{{ $docketGridField->id }}">Add</button>
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
@else
    <select  class="form-control prefillerGridLinkFilter"  required  >
        <option docketfieldId="{{$docketGridField->id}}" value="0">Please Select Filter Value</option>
        @if($suggestionValue)
            @foreach($suggestionValue as $row)
                <option docketfieldId="{{$docketGridField->id}}" value="{!! $row !!}">{!! $row !!}</option>
            @endforeach
        @endif
    </select>
@endif




