@foreach($sentInvoiceLabels as $sentInvoiceLabel)
        <li style="background: {{$sentInvoiceLabel->invoiceLabel->color}}" class="invoice-label-{{$sentInvoiceLabel->id}}">
            @if($sentInvoiceLabel->invoiceLabel->icon)
                <img src="{{ AmazoneBucket::url() }}{{ $sentInvoiceLabel->invoiceLabel->icon }}" height="10" width="10">
            @endif
            {{ $sentInvoiceLabel->invoiceLabel->title }}
        </li>
@endforeach