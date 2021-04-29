<table class="table" id="datatable">
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox " value="1"  name="employed[]"></th>
            <th>Invoice</th>
            <th>Info</th>
            <th>Invoice Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(@$receivedInvoice)
            @php $invoiceCheckbox = true @endphp
            @foreach($receivedInvoice as $row)
                @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
            @endforeach
        @endif
        @if(count(@$receivedInvoice)==0) <tr><td colspan="9"><center>Data Empty</center></td></tr> @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><span>Showing {{ $receivedInvoice->firstItem() }} to {{ $receivedInvoice->lastItem() }} of {{ $receivedInvoice->total() }} entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $receivedInvoice->appends(['search'=>$receivedInvoice,'items'=>$items])->links() }}
                @else {{ $receivedInvoice->appends(['items'=>$items]) ->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>