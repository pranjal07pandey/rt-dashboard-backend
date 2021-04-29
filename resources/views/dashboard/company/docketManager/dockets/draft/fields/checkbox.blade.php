@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>{{ ($docketValue->value == 1) ? "checked" : "unchecked"  }}</td>
    </tr>
@endif