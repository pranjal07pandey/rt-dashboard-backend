@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>{{ @$docketValue->unit_rate_value->per_unit_rate }} {{ @$docketValue->unit_rate_value->total_unit }}  {{ @$docketValue->value }}</td>
    </tr>
@endif