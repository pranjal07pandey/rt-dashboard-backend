<table class="rtDataTable datatable" >
    <thead>
        <tr>
            <th><input type="checkbox" class="checkbox " value="1"  name="employed[]"></th>
            <th>Docket Id</th>
            <th>Info</th>
            <th>Docket Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(@$dockets)
            @php $docketCheckbox = true @endphp
            @foreach($dockets as $row)
                @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
            @endforeach
        @endif
        @if(count(@$dockets)==0)
            <tr><td colspan="9"><center>Data Empty</center></td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2"><span>Showing  {{ $dockets->firstItem() }} to {{ $dockets->lastItem() }} of {{ $dockets->total() }} entries</span></td>
            <td colspan="6" class="text-right">
                @if(@$searchKey) {{ $dockets->appends(['search'=>$searchKey])->links() }}
                @else {{ $dockets->links() }}
                @endif
            </td>
        </tr>
    </tfoot>
</table>
