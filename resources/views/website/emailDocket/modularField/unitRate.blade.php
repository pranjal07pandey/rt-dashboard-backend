@php $sn = 1; $total = 0; @endphp
@foreach($docketValue->sentDocketUnitRateValue as $unitRate)
    <tr>
        <td>{{ $unitRate->docketUnitRateInfo->label }}</td>
        <td>
            @if($unitRate->docketUnitRateInfo->type==1) $ @endif {{ $unitRate->value }}
        </td>
        @if($sn == 1) @php $total = $unitRate->value @endphp
        @else @php $total    =   $total*$unitRate->value @endphp @endif
        @php $sn++; @endphp
    </tr>
@endforeach
<tr>
    <td>
        <strong>Total:</strong>
    </td>
    <td>
        <strong>$ {{ $total }}</strong>
    </td>
</tr><!--unit-rate-->