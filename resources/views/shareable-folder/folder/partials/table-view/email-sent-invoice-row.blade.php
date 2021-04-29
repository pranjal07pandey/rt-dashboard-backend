<tr>
    @if(@$invoiceCheckbox)
        <td>
            <input type="checkbox" class="checkbox selectitem forEmailInvoice" value="{{ $row->id }}"  name="emailInvoiceId[]"  >
        </td>
    @endif
    <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
    <td>
        <span class="blackLabel">Sender</span>
        <span class="userInfo"> {{ $row->sender_name }}<br/></span>
        {{ $row->company_name }}<br/><br>

        <span class="blackLabel">Receiver</span>
        <span class="userInfo">{{ $row->receiverInfo->email }}<br></span>

        <div class="invoice-label-container">
            <div class="item-wrapper" id="emailInvoiceLabelIdentify{{$row->id}}">
                <ul>
                    @if(count($row->emailSentInvoiceLabels)>0)
                        @php $sentInvoiceLabels  =   $row->emailSentInvoiceLabels; $type = 4;@endphp
                        @include('shareable-folder.folder.partials.table-view.label.invoice-label')

                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td>{{ $row->template_title }}</td>
    <td>{{ $row->formattedCreatedDate() }}</td>

    <td></td>
    <td>

        <a href="{{ url('folder/invoice/emailed/view/'.$row->encryptedID()) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
        <a href="{{url('folder/invoice/download/emailed/'.$row->encryptedID())}}" class="btn btn-primary btn-xs btn-raised"><i class="fa fa-download"></i></a>


    </td>
</tr>
