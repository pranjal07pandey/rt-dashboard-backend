<thead>
<tr>
    <th>Id</th>
    <th>Full  Name</th>
    <th >Period</th>
    <th>Total Hours</th>
    <th>Xero Timesheet Id</th>
    <th >Action</th>
</tr>
</thead>
<tbody>

@if(count($timeSheetdocketDetail)==0)
    <tr>
        <td  colspan="5"  style="text-align: center;">Empty Data</td>
    </tr>
@else
    @foreach($timeSheetdocketDetail as $rowData)
        <tr>
            <td>{{$rowData->id}}</td>
            <td>{{$rowData->UserId->first_name}} {{$rowData->UserId->last_name}}</td>
            <td> {{\Carbon\Carbon::parse(explode('|',$rowData->period)[0])->format('Y-m-d')}}  - {{\Carbon\Carbon::parse(explode('|',$rowData->period)[1])->format('Y-m-d')}}</td>
            <td>{{ round($rowData->total_hours, 2)}}</td>
            <td>{{$rowData->xero_timesheet_id}}</td>
            <td>  <a  data-toggle="modal" data-target="#updateDocument"  class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;" disabled=""><i class="fa fa-eye"></i></a>
            </td>
        </tr>
    @endforeach
@endif
</tbody>
<tfoot>
<tr id="folderAdvanceFilterFooterView">
    <td colspan="3" style="padding: 33px 0px 0px 9px;"><span>Showing  {{ $timeSheetdocketDetail->firstItem() }} to     {{ $timeSheetdocketDetail->lastItem() }} of {{ $timeSheetdocketDetail->total() }}entries</span></td>
    <td colspan="5" class="text-right">
        @if(@$searchKey)
            <div id="folderPagination">  {{ $timeSheetdocketDetail->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
        @else
            <div id="folderPagination"> {{ $timeSheetdocketDetail->appends(['items'=>$items])->links() }}</div>
        @endif
    </td>
</tr>

</tfoot>