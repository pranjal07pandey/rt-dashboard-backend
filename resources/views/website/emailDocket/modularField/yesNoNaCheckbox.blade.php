<tr>
    <td colspan="2">
        @php
            $yesno = unserialize($docketValue->label);
        @endphp
        <div style="width:100%;margin:0;">
            <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
            @if($docketValue->value == "N/a")
                <div style="width:50%; float:right;padding-left: 8px;"> N/a </div>
            @else
                @if(@$yesno['label_value'][$docketValue->value]['label_type']==1)
                    <div style="width:50%; float:right;   padding-left: 8px;">
                        <img style="width: 20px; height:20px; background-color:{{ $yesno['label_value'][$docketValue->value]['colour']}}; border-radius:20px;padding:4px;" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$docketValue->value]['label'] }}">
                    </div>
                @else
                    <div style="width:50%; float:right;  padding-left: 8px;">
                        {{ @$yesno['label_value'][$docketValue->value]['label']}}
                    </div>
                @endif
            @endif
        </div>
        @if(count($docketValue->SentEmailDocValYesNoValueInfo)==0)
        @else
            <table style="background: transparent; width: 100%;" class="table table-striped">
                <thead style="background: transparent; ">
                <tr>
                    <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                </tr>
                </thead>
                <tbody >
                @foreach($docketValue->SentEmailDocValYesNoValueInfo as $items)
                    @if($items->YesNoDocketsField->docket_field_category_id==5)
                        @php
                            $imageData=unserialize($items->value);
                        @endphp
                        <tr>
                            <td style="    width: 50%;">{{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                            <td>
                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                    @if(empty($imageData))
                                        <b>No Image Attached</b>
                                    @else
                                        @foreach($imageData as $rowData)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" style="height: 100px;">
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                        </tr>
                    @endif
                    @if($items->YesNoDocketsField->docket_field_category_id==1)
                        <tr>
                            <td> {{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
                            <td>{{$items->value }}</td>
                        </tr>
                    @endif
                    @if($items->YesNoDocketsField->docket_field_category_id==2)
                        <tr>
                            <td> {{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
                            <td>{{$items->value }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        @endif
    </td>
</tr><!--/.yesNoNaCheckbox-->