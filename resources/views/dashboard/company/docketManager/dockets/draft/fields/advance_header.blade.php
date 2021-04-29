@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->docket_field_category }}</strong></td>
        <td>{!! $docket_field->label !!}</td>
    </tr>
@endif