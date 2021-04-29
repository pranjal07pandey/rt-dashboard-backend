@if($row->sentDocketValue)
    @foreach($row->sentDocketValue as $sentDocketValue)
        @if(@$sentDocketValue->docketFieldInfo->docket_field_category_id == 22)
            @php
                $docketGridField = @\App\DocketFieldGrid::where('docket_field_id',$sentDocketValue->docket_field_id)->get();
            @endphp

            @foreach($docketGridField as $docketGridFields)
                @php
                    $sentDocketGridValue  = @\App\DocketFieldGridValue::where('docket_field_grid_id',$docketGridFields->id)->where('docket_id',$row->id)->where('is_email_docket',0)->get()->first();
                @endphp
                @if ($docketGridFields->preview_value == 1)
                    {{@$sentDocketGridValue->value}}
                @endif


            @endforeach



        @endif
    @endforeach
@endif
