<table class="rtDataTable datatable" >
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
    @if(@$sentInvoice)
        @php $invoiceCheckbox = true @endphp
        @foreach($sentInvoice as $row)
            @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
        @endforeach
    @endif
    @if(count(@$sentInvoice)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><span>Showing {{ $sentInvoice->firstItem() }} to {{ $sentInvoice->lastItem() }} of {{ $sentInvoice->total() }} entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $sentInvoice->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                @else {{ $sentInvoice->appends(['items'=>$items]) ->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>
