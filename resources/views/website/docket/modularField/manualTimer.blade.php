<tr>
    <td>{{ $docketValue->label }}</td>
    <td>
        @foreach($docketValue->sentDocketManualTimer as $rows)
            <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
        @endforeach
        <br>
        @foreach($docketValue->sentDocketManualTimerBreak as $item)
            <strong>{{ $item->label }} :</strong>  {{ $item->value }}<br>
            <strong>Reason for break :</strong>  {{ $item->reason }}<br>
        @endforeach
        <strong>Total time :</strong>  {{ $docketValue->value }}<br>
    </td>
</tr>