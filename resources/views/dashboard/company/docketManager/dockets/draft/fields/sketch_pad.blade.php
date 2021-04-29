@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            @foreach ($docketValue->image_value as $image_value)
                <img src="{{ $image_value }}" width="150">
            @endforeach
        </td>
    </tr>
@endif