@if($sentInvoice->invoiceValue)
    @php $sn = 1; @endphp
    <table class="table table-striped">
        @foreach($sentInvoice->invoiceValue as $item)
            @if($item->invoiceFieldInfo->invoice_field_category_id==5)
                <tr>
                    <td colspan="2">
                        <strong>{{ $item["label"] }}</strong><br/>
                        <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                        @if($images->count()>0)
                            <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                @foreach($images as $image)

                                    <li style="margin-right:10px;float: left;">
                                        <a href="{{ AmazoneBucket::url() }}{{ $image->value }}" target="_blank">
                                            <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="height: 70px;border: 1px solid #ddd;">
                                        </a>
                                    </li>

                                @endforeach
                            </ul>
                        @else
                            No Image Attached
                        @endif

                        <div style="clear:both;"></div>
                    </td>
                </tr>
                @php $sn++; @endphp
            @endif
            @if($item["invoice_field_category_id"]==12)
                <tr>
                    <td  colspan="2"> <strong>{{ $item["value"] }}</strong></td>
                </tr>
            @endif

        @endforeach

    </table>
@endif