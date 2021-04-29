@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            @foreach ($docket_field->subField as $subField)
                @if($subField->type == @$docketValue->yes_no_value->selected_type)
                    {{ $subField->label }} <br>
                    @if($docketValue->yes_no_value->explanation)
                        Explanation : <br>
                        @foreach ($docketValue->yes_no_value->explanation as $explanation)
                            @if($explanation->category_id == 1)
                                {{ $explanation->value }}
                            @elseif($explanation->category_id == 5)
                                @foreach ($explanation->image_value as $image_value)
                                    <img src="{{ $image_value }}" width="150">
                                @endforeach
                            @else
                                {{ $explanation->value }}
                            @endif
                        @endforeach
                    @endif
                @endif
            @endforeach
        </td>
    </tr>
@endif