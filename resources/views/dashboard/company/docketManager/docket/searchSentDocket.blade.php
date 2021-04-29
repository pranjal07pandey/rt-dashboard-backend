<table class="rtDataTable datatable" >
    <thead>
        <tr>
            <th>
                <input type="checkbox" class="checkbox " value="1"  name="employed[]" >
            </th>
            <th>Docket Id</th>
            <th>Info</th>
            <th>Docket Name</th>
            <th>Date Added</th>
            <th>Status</th>
            <th width="200px">Action</th>
        </tr>
    </thead>
    <tbody>
        @if(@$sentDockets)
            @php $docketCheckbox = true @endphp
            @foreach($sentDockets as $row)
                @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
            @endforeach
        @endif
        @if(count(@$sentDockets)==0)
            <tr><td colspan="9"><center>Data Empty</center></td></tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3"><span>Showing  {{ $sentDockets->firstItem() }} to {{ $sentDockets->lastItem() }} of {{ $sentDockets->total() }} entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $sentDockets->appends(['search'=>$searchKey])->links() }}
                @else {{ $sentDockets->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>
