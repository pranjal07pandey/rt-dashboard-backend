<tbody style="background-color: #fff;">
@if($row->docketInfo->sentDocketValue)
    @foreach($row->docketInfo->sentDocketValue as $item)
        @if($item->docketFieldInfo->docket_field_category_id==5 || $item->docketFieldInfo->docket_field_category_id==9 )
            <tr>
                <td colspan="2">
                    {{ $item->label }}<br/>
                    <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                        @foreach($item->sentDocketImageValue as $signature)
                            <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="max-height:90px; max-width:90px;margin:0px auto; display: block">
                            </li>
                        @endforeach
                    </ul>
                    @if($item->sentDocketImageValue)  <br/><br/><br/><br/><br/> @endif
                </td>
            </tr>
            @if($item->docketFieldInfo->docket_field_category_id==14)
                <tr>
                    <td colspan="2">
                        {{ $item->label }}<br/>
                        <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                            @foreach($item->sentDocketImageValue as $sketchPad)
                                <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                    {{--<a href="{{ asset($sketchPad->value) }}" target="_blank">--}}
                                    <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="max-height:90px; max-width:90px;margin:0px auto; display: block">
                                    {{--</a>--}}
                                </li>
                            @endforeach
                        </ul>
                        @if($item->sentDocketImageValue)  <br/><br/><br/><br/><br/>@endif
                    </td>
                </tr>

            @elseif($item->docketFieldInfo->docket_field_category_id==8)
                <tr>
                    <td>
                        {{ $item->label }}
                    </td>
                    <td>
                        @if($item->value==1)<img src="{{ asset('assets/dashboard/img/checked.png') }}" width="15px">
                        @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="15px">@endif
                    </td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id==15)
                <tr>
                    <td> {{ $item->label }}
                        <ul class="pdf">
                            @foreach($item->sentDocketAttachment as $document)
                                <li><img src="{{ asset('assets/pdf.png') }}"><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></li>
                            @endforeach
                        </ul>
                    </td>
                    <td> {{ $item->value }}</td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id==7)
                <tr>

                    <td>
                        @foreach($item->sentDocketUnitRateValue as $row)
                            {{ $row->docketUnitRateInfo->label }}<br/>
                        @endforeach
                        <strong>Total</strong>
                    </td>

                    <td>
                        <?php $total    =    0; ?>
                        @foreach($item->sentDocketUnitRateValue as $row)
                            @if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}<br/>
                        @endforeach
                        $ <strong>{{  $item->sentDocketUnitRateValue->first()->value*$item->sentDocketUnitRateValue->last()->value }}</strong>
                    </td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id==12)
                <tr>
                    <td  colspan="2"> <strong>{{ $item->label }}</strong></td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id==18)
                <tr>
                    <td colspan="2">
                        <!--<table style="width:100%;">-->
                        <!--<tr>-->
                        @php
                            $yesno = unserialize($item->label);
                        @endphp
                        <div style="width:100%;margin:0;">
                            <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
                            @if(@$item->value == "N/a")
                                <div style="width:50%; float:right;margin-right: -9px;"> N/a </div>
                            @else
                                @if(@$yesno['label_value'][$item->value]['label_type']==1)
                                    <div style="width:50%; float:right;margin-right: -9px;"><img style="width: 20px; height:20px;padding:4px; background-color:{{ $yesno['label_value'][$item->value]['colour']}}; border-radius:20px;" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$item->value]['label'] }}"></div>
                                @else
                                    <div style="width:50%; float:right;margin-right: -9px;">{{ @$yesno['label_value'][$item->value]['label']}}</div>
                                @endif
                            @endif
                        </div>
                        <!-- </tr>-->
                        <!--</table>-->

                        @if($item->SentDocValYesNoValueInfo)
                            <table style="background: transparent; width: 100%;" class="table table-striped">
                                <thead style="background: transparent; ">
                                <tr>
                                    <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                </tr>
                                </thead>
                                <tbody >

                                @foreach($item->SentDocValYesNoValueInfo as $items)
                                    @if($items->YesNoDocketsField->docket_field_category_id==5)
                                        @php
                                            $imageData=unserialize($items->value);
                                        @endphp
                                        <tr>
                                            <td style="width:50%;">{{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
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
                                            <td> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                            <td>{{$items->value }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id==13)
                @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
            @elseif($item->docketFieldInfo->docket_field_category_id==27)
                <tr>
                    <td colspan="2">
                        <div style="width: 100%">{!! $item->label !!}</div>
                    </td>
                </tr>
            @elseif($item->docketFieldInfo->docket_field_category_id!=13 && $item->docketFieldInfo->docket_field_category_id!=18)
                <tr>
                    <td> {{ $item->label }}</td>
                    <td> {{ $item->value }}</td>
                </tr>
            @endif
            @endforeach
            @if(@$footerValue)
                <tr>
                    <td  colspan="2"> <strong>{{ $footerLabel }}</strong><br>
                        {{ $footerValue }}
                    </td>
                </tr>
            @endif
        @endif
</tbody>
