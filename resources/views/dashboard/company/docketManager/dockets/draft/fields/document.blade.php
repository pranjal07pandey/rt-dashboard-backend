@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            @foreach ($docket_field->subField as $subField)
                <a href="{{ $subField->url }}" style="color: blue" target="_blank">{{ $subField->name }}</a> , 
            @endforeach
        </td>
    </tr>
@endif