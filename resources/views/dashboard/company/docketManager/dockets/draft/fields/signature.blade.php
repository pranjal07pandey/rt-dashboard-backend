@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td class="row">
            @foreach ($docketValue->signature_value as $signature_value)
                <div class="col-lg-3 col-md-3">
                    <label>{{ $signature_value->name }}</label><br>
                    <img src="{{ $signature_value->image }}" width="150">
                </div>
            @endforeach
        </td>
    </tr>
@endif