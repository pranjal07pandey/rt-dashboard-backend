@if($docketGridField->auto_field == 0)
    <tr  style="background: #E9ECEF;height: 120px;     border: 1px solid #CED4DA;"  >
        <td style="height: 61px; " >
          @if($docketGridField->echowise_id != 0)
                <div style="width: 150px;font-size: 14px; font-weight: 500;     padding: 10px 0 8px 0px;">{!! @$docketGridField->label !!}</div>
                <div class="custom-select" style="width:200px; margin-top: 17px;">
                    <select class="selectPrefilerEcowise" style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA;">
                        <option  type="1" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}" >Select Index</option>
                        @foreach($keyValue as $keyValues)
                            <option  type="1" gridid="{{$docketGridField->id}}" indexcell="1" docketField="{{$docketGridField->docket_field_id}}" value="{{str_replace(' ', '_', $keyValues)}}" @if($docketGridField->selected_index_value == str_replace(' ', '_', $keyValues)) selected @endif  >{{$keyValues}}</option>
                        @endforeach
                    </select>
                </div>
           @else
                <div style="font-size: 14px;     padding: 10px 0 8px 0px; text-align: center;"> Please select Url</div>
           @endif

        </td>
    </tr>
@endif
