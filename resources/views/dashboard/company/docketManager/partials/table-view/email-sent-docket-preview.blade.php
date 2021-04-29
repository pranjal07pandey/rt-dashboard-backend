@if(@$row->docketInfo->previewFields->count()>0)
<div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
    @foreach(@$row->docketInfo->previewFields as $previewField)

        @if($previewField->docket_filed_info->docket_field_category_id==5 || $previewField->docket_filed_info->docket_field_category_id == 9)
            @if(@count(\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                <ul class="sentDocketImagePreview">
                    @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                        <li> <a href="{{ AmazoneBucket::url() }}{{  $images->value  }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                    @endforeach
                </ul>
            @endif
        @elseif($previewField->docket_filed_info->docket_field_category_id == 7)
            <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
            @if(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue)
                <?php $sn = 1; $total = 0; ?>
                @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue as $unitRate)
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
            <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
            @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==0)
                <span>No</span>
            @elseif(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==1)
                <span>Yes</span>
            @endif
            {{--<span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>--}}

        @elseif($previewField->docket_filed_info->docket_field_category_id == 18)
            @include('dashboard.company.docketManager.partials.table-view.email-docket.preview.yesnona')
        @else
            <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
            <span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>

        @endif
        <br/>
    @endforeach
</div>
@endif