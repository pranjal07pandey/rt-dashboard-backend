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
                        @include('dashboard.company.invoiceManager.partials.table-view.invoice-label')
                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td>{{ $row->invoiceInfo->title }}</td>
    <td>{{ $row->formattedCreatedDate() }}</td>
    <td>
        @if($row->status==1)
            <span class="label label-success">Approved</span>
        @else
            @if(\Illuminate\Support\Facades\Auth::user()->id==$row->user_id)
                <span class="label label-primary">Sent</span>
            @else
                @if($row->receiver_user_id ==\Illuminate\Support\Facades\Auth::user()->id)
                    <span class="label label-warning">Received</span>
                @else
                    <span class="label label-primary">Sent</span>
                @endif
            @endif
        @endif


    </td>
    <td>
        <a href="{{ url('dashboard/company/invoiceManager/invoice/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
        <a  href="{{url('dashboard/company/invoiceManager/invoice/downloadViewInvoice/'.$row->id)}}" class="btn btn-success btn-xs btn-raised"  style="background-color: #15b1b8;"><i class="fa fa-download"></i></a>

        <a  data-toggle="modal" data-target="#invoiceLabelModal" data-formatted-id="{{ $row->formattedInvoiceID() }}" data-id="{{$row->id}}" data-type="3" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

    </td>
</tr>