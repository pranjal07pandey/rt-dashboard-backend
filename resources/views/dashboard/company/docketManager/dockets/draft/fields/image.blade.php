<tr>
    @if($docket_field->id == $docketValue->form_field_id)
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            @foreach ($docketValue->image_value as $image)
                <img src="{{ $image }}" width="150">
            @endforeach
        </td>
    @endif
</tr>