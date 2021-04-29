<tr>
    <td colspan="2"><strong>{{ $docketValue->label }}</strong> </td>
</tr>

<?php $sn = 1; $total = 0; ?>
@foreach($docketValue->sentDocketTallyableUnitRateValue as $row)
    <tr>
        <td>{{ $row->docketUnitRateInfo->label }}</td>
        <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
        @if($sn == 1)
            <?php $total = $row->value; ?>
        @else
            <?php $total    =   $total*$row->value; ?>
        @endif
        <?php $sn++; ?>
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