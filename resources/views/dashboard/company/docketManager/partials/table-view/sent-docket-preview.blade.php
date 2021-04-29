@if(@$row->docketInfo->previewFields->count()>0)
    <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
        @foreach(@$row->docketInfo->previewFields as $previewField)
            @if($previewField->docket_filed_info->docket_field_category_id==5 || $previewField->docket_filed_info->docket_field_category_id == 9)
                @if(@count(\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label }}</span>
                    <ul class="sentDocketImagePreview">
                        @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                            <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                        @endforeach
                    </ul>
                @endif
            @elseif($previewField->docket_filed_info->docket_field_category_id == 7)
                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label }}</span>
                @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->sentDocketUnitRateValue)
                    <?php $sn = 1; $total = 0; ?>
                    @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue as $unitRate)
                        {{$unitRate->docketUnitRateInfo->label}} : @if($unitRate->docketUnitRateInfo->type==1) $ @endif {{ $unitRate->value }} &nbsp;&nbsp;&nbsp;
                        @if($sn == 1)
                            <?php $total = $unitRate->value; ?>
                        @else
                            <?php $total    =   $total*$unitRate->value; ?>
                        @endif
                        <?php $sn++; ?>
                    @endforeach
                    <strong>Total:</strong>
                    <strong>$ {{ $total }}</strong>
                @endif
            @elseif($previewField->docket_filed_info->docket_field_category_id == 8)
                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label }}</span>
                @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==0)
                    <span>No</span>
                @elseif(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==1)
                    <span>Yes</span>
                @endif
            @elseif($previewField->docket_filed_info->docket_field_category_id==18 )
                @php
                    $yesno = unserialize(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label);
                @endphp
                <span style="font-weight: 500;">{{ @$yesno['title']}} : </span>
                @php $yesnonaValue = @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value @endphp
                @if($yesnonaValue == "N/a")
                    <span>N/a</span><br/>
                @else
                    @if(@$yesno['label_value'][$yesnonaValue]['label_type']==1)
                        <span><img style="width: 23px; background-color:{{ $yesno['label_value'][$yesnonaValue]['colour']}}; border-radius:20px;" src="{{ AmazoneBucket::url() }}{{ @$yesno['label_value'][$yesnonaValue]['label'] }}"></span><br/>
                    @else
                        <span style="color: {{ @$yesno['label_value'][$yesnonaValue]['colour'] }};font-weight:bold">{{ @$yesno['label_value'][$yesnonaValue]['label']}}</span><br/>
                    @endif
                @endif
                @if(@count(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->SentDocValYesNoValueInfo)!=0)
                    <span style="font-weight: 500;">Explanation</span><br/>
                    @foreach($row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->SentDocValYesNoValueInfo as $items)
                        @if($items->YesNoDocketsField->docket_field_category_id==5)
                            @php
                                $imageData=unserialize($items->value);
                            @endphp
                            <span style="font-weight: 500;">{{ $items->label }}</span>
                            @if(empty($imageData))
                                <b>No Image Attached</b>
                            @else
                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                            @foreach($imageData as $rowData)
                                        <li style="margin-right:10px;float: left;">
                                    <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank">
                                        <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" style="height: 100px;">
                                    </a>
                                </li>
                                    @endforeach
                        </ul>
                                <div class="clearfix"></div>
                            @endif
                        @endif
                        @if($items->YesNoDocketsField->docket_field_category_id==1)
                            <span style="font-weight: 500;">{{ $items->label }}</span><br/>
                            <span>{{$items->value }}</span><br/>
                        @endif
                    @endforeach
                @endif
            @else
                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label }}</span>
                <span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span><br/>
            @endif
        @endforeach
    </div>
@endif