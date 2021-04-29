<table class="rtDataTable datatable">
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox " value="1"  name="employed[]" ></th>
            <th>Invoice</th>
            <th>Info</th>
            <th>Invoice Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(@$result)
            @php $invoiceCheckbox = true @endphp
            @foreach($result->sortByDesc('created_at')  as $row)
                @if($row instanceof App\SentInvoice)
                    @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
                @endif
                @if($row instanceof App\EmailSentInvoice)
                    @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
                @endif
            @endforeach
        @endif
        @if(count(@$result)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><span>Showing {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                @else {{ $result->appends(['items'=>$items]) ->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>
