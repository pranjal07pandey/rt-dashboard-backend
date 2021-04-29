@php $sn = 1; $total = 0; @endphp
@foreach($docketValue->sentDocketTallyableUnitRateValue as $tallyableUnitRate)
    <tr>
        <td>{{ $tallyableUnitRate->docketUnitRateInfo->label }}</td>
        <td>@if($tallyableUnitRate->docketUnitRateInfo->type==1) $ @endif {{ $tallyableUnitRate->value }}</td>
        @if($sn == 1) @php $total = $tallyableUnitRate->value @endphp
        @else @php  $total    =   $total*$tallyableUnitRate->value @endphp @endif
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
</tr><!--tallyunit-rate-->