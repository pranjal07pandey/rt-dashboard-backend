<tr>
    @if(@$invoiceCheckbox)
        <td>
            <input type="checkbox" class="checkbox selectitem forInvoice" value="{{ $row->id }}" name="invoiceId[]">
        </td>
    @endif
    <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
    <td>
        <span class="blackLabel">Sender</span>
        <span class="userInfo">{{ $row->sender_name }}</span>
        {{ @$row->company_name }}<br/><br>

        <span class="blackLabel">Receiver</span>
        <span class="userInfo">{{ $row->receiverUserInfo->first_name }} {{ $row->receiverUserInfo->last_name }}</span>
        {{ @$row->receiverCompanyInfo->name }}

        <div class="invoice-label-container">
            <div class="item-wrapper" id="invoiceLabelIdentify{{$row->id}}">
                <ul>
                    @if(count($row->sentInvoiceLabels)>0)
                        @php $sentInvoiceLabels  =   $row->sentInvoiceLabels; $type = 3;@endphp
                        @include('shareable-folder.folder.partials.table-view.label.invoice-label')

                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td>{{ $row->invoiceInfo->title }}</td>
    <td>{{ $row->formattedCreatedDate() }}</td>
    <td>
    </td>
    <td>
        <a href="{{ url('folder/invoice/view/'.$row->encryptedID()) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
        <a  href="{{url('folder/invoice/download/'.$row->encryptedID())}}" class="btn btn-success btn-xs btn-raised"  style="background-color: #15b1b8;"><i class="fa fa-download"></i></a>
    </td>
</tr>