@php
    $yesno = unserialize(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->label);
@endphp
<span style="font-weight: 500;">{{ @$yesno['title']}}</span><br/>
@php $yesnonaValue = @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value @endphp
@if($yesnonaValue == "N/a")
    <span>N/a</span><br/>
@else
    @if(@$yesno['label_value'][$yesnonaValue]['label_type']==1)
        <span><img style="width: 23px; background-color:{{ $yesno['label_value'][$yesnonaValue]['colour']}}; border-radius:20px;" src="{{ AmazoneBucket::url() }}{{ @$yesno['label_value'][$yesnonaValue]['label'] }}"></span><br/>
    @else
        <span>{{ @$yesno['label_value'][$yesnonaValue]['label']}}</span><br/>
    @endif
@endif

@if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id) != null)
    @if(count($row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->SentEmailDocValYesNoValueInfo)!=0)
        <span style="font-weight: 500;">Explanation</span><br/>
        @foreach($row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->SentEmailDocValYesNoValueInfo as $items)
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
@endif
