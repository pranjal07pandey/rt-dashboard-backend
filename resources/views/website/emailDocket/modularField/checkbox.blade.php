<tr>
    <td> {{ $docketValue->label }}</td>
    <td>
        @if($docketValue->value==1)
            <i class="fa fa-check-circle" style="color:green"></i>
        @else
            <i class="fa fa-close" style="color:red"></i>
        @endif
    </td>
</tr><!--checkbox-->