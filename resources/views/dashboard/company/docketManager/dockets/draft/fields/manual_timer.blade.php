@if($docket_field->id == $docketValue->form_field_id )
    <tr>
        <td><strong>{{ $docket_field->label }}</strong></td>
        <td>
            totalDuration : {{ $docketValue->value }} <br>
            from : {{ $docketValue->manual_timer_value->from }} <br>
            to : {{ $docketValue->manual_timer_value->to }} <br>
            breakDuration : {{ $docketValue->manual_timer_value->breakDuration }} <br>
            @isset($docketValue->manual_timer_value->explanation)
                explanation : {{ $docketValue->manual_timer_value->explanation }} <br>
            @endisset 
        </td>
    </tr>
@endif