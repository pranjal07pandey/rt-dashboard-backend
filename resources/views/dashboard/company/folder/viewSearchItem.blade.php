<table class="rtDataTable datatable" >
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox " value="1"  name="employed[]" ></th>
            <th>Docket Id</th>
            <th>Info</th>
            <th>Docket Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
    @if(@$result)
        @php $docketCheckbox = true @endphp
        @php $invoiceCheckbox = true @endphp
        @php $shareableFolder = true  @endphp

        @php $checktrashFolder = $trashFolder @endphp
        @foreach($result->sortByDesc('created_at') as $row)
            @if($row instanceof App\SentDockets)
                @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
            @endif
            @if($row instanceof App\EmailSentDocket)
                @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
            @endif
            @if($row instanceof App\SentInvoice)
                @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
            @endif
            @if($row instanceof App\EmailSentInvoice)
                @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
            @endif
        @endforeach
    @endif



       <tr id="folderAdvanceFilterFooterView">
           <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
           <td colspan="5" class="text-right">
               @if(@$searchKey) <div id="searchFolderPagination">  {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
               @else <div id="searchFolderPagination"> {{ $result->appends(['items'=>$items])->links() }}</div>@endif
           </td>
       </tr>

    </tbody>
</table>
