@if($sentInvoice->invoiceValue)
    @php $sn = 1; @endphp
    @foreach($sentInvoice->invoiceValue as $item)
        @if($item->invoiceFieldInfo->invoice_field_category_id==9)
            <tr style="background:#fff;">
                <td colspan="2">
                    @if($sn==1) <br/> @endif
                    <strong>{{ $item["label"] }}</strong><br/>
                    <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                    @if($images->count()>0)
                        <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                            @foreach($images as $signature)
                                <li style="margin-right:10px;float: left;">
                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 70px;border: 1px solid #ddd;">
                                </li>
                            @endforeach
                        </ul>
                    @else
                        No Signature Attached
                    @endif
                    <div style="clear:both;"></div>
                </td>
            </tr>
            @php $sn++; @endphp
        @endif
    @endforeach
@endif
