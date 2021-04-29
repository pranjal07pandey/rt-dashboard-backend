@foreach($sentInvoiceLabels as $sentInvoiceLabel)
    @if($sentInvoiceLabel->invoiceLabel->company_id==Session::get('company_id'))
        <li style="background: {{$sentInvoiceLabel->invoiceLabel->color}}" class="invoice-label-{{$sentInvoiceLabel->id}}">
            @if($sentInvoiceLabel->invoiceLabel->icon)
                <img src="{{ AmazoneBucket::url() }}{{ $sentInvoiceLabel->invoiceLabel->icon }}" height="10" width="10">
            @endif
            {{ $sentInvoiceLabel->invoiceLabel->title }}
            <button  data-toggle="modal" data-target="#deleteInvoiceLabelModal" data-type="{{$type}}"  data-id="{{$sentInvoiceLabel->id}}"  class="btn btn-raised btn-xs">
                <span  class="glyphicon glyphicon-remove" aria-hidden="true"/>
            </button>
        </li>
    @endif
@endforeach