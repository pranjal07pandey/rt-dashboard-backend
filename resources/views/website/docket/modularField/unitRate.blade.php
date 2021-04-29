<?php $sn = 1; $total = 0; ?>
@foreach($docketValue->sentDocketUnitRateValue as $row)
    <tr>
        <td>
            {{ $row->label }}</td>
        <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
        @if($sn == 1)
            <?php $total = floatval($row->value); ?>
        @else
            <?php $total    =   floatval($total)*floatval($row->value); ?>
        @endif
        <?php $sn++; ?>
    </tr>
@endforeach
<tr>
    <td>
        <strong>Total:</strong>
    </td>
    <td>
        <strong>$ {{ round($total,2) }}</strong>
    </td>
</tr><!--unit-rate-->