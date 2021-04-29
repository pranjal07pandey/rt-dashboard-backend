<table class="rtDataTable datatable" >
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox " value="1" name="employed[]" ></th>
            <th>Invoice</th>
            <th>Info</th>
            <th>Invoice Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(@$emailedInvoice)
            @php $invoiceCheckbox = true @endphp
            @foreach($emailedInvoice as $row)
                @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
            @endforeach
        @endif
        @if(count(@$emailedInvoice)==0)<tr><td colspan="9"><center>Data Empty</center></td></tr>@endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><span>Showing  {{ $emailedInvoice->firstItem() }} to {{ $emailedInvoice->lastItem() }} of {{ $emailedInvoice->total() }} entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $emailedInvoice->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                @else {{ $emailedInvoice->appends(['items'=>$items]) ->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>