@if($sentInvoice->invoiceDescription)
    @foreach($sentInvoice->invoiceDescription as $item)
        <tr>
            <td>
                {{ $item["description"] }}
            </td>
            <td>
                $ {{ round($item["amount"],2) }}
            </td>
        </tr>
    @endforeach
@endif