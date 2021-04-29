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
        <?php $sn=count($result); ?>
        @if(@$result)
            @php $docketCheckbox = true @endphp
            @foreach($result->sortByDesc('created_at') as $row)
            @if($row instanceof App\SentDockets)
                @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
            @endif
            @if($row instanceof App\EmailSentDocket)
                @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
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
            <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }}entries</span></td>
            <td colspan="5" class="text-right">
                @if(@$searchKey) {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}
                @else {{ $result->appends(['items'=>$items]) ->links() }} @endif
            </td>
        </tr>
    </tfoot>
</table>
