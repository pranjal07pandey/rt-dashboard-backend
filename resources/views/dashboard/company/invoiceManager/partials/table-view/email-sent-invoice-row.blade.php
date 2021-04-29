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
                        @include('dashboard.company.invoiceManager.partials.table-view.invoice-label')
                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td>{{ $row->template_title }}</td>
    <td>{{ $row->formattedCreatedDate() }}</td>

    <td><span class="label label-primary">Sent</span></td>
    <td>
        <a href="{{ url('dashboard/company/invoiceManager/emailedInvoices/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
        <a href="{{url('dashboard/company/invoiceManager/invoice/downloadViewInvoiceEmail/'.$row->id)}}" class="btn btn-primary btn-xs btn-raised"><i class="fa fa-download"></i></a>


        <a  data-toggle="modal" data-target="#invoiceLabelModal" data-formatted-id="{{ $row->formattedInvoiceID() }}" data-id="{{$row->id}}" data-type="4" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

        @if($row->xero_invoice_id == "0")
            <a  href="{{ url('dashboard/company/xero/xeroEmailInvoice/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px; background-color: #15b1b8;">
                Xero Sync
            </a>
        @else
            <a  href="{{ url('dashboard/company/xero/xeroEmailInvoiceView/'.$row->id) }}" target="_blank" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;">
                <i class="fa fa-eye"></i>  Xero
            </a>
        @endif


    </td>
</tr>
