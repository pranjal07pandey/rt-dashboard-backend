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
