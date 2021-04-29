@if($sentInvoice->invoiceValue)
    @foreach($sentInvoice->invoiceValue as $item)
        @if($item->invoiceFieldInfo->invoice_field_category_id!=9 && $item->invoiceFieldInfo->invoice_field_category_id!=12 && $item->invoiceFieldInfo->invoice_field_category_id!=5)
            <tr>
                <td colspan="2">
                    <strong>{{ $item["label"] }}</strong><br/>
                    {{ $item["value"] }}
                    <div style="clear:both"></div>
                </td>
            </tr>
        @endif
    @endforeach
@endif