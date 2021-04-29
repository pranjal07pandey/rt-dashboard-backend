@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            @if($docketValue->unit_rate_value)
                @foreach ($docket_field->subField as $subField)
                    @if($subField->type == 1)
                        {{ $subField->label }} : {{ @$docketValue->unit_rate_value->per_unit_rate }}<br>
                    @else
                        {{ $subField->label }} : {{ @$docketValue->unit_rate_value->total_unit }}<br>
                    @endif
                @endforeach
                Total : {{ @$docketValue->unit_rate_value->total }}
            @endif
        </td>
    </tr>
@endif