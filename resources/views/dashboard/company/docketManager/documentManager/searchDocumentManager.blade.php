<table class="table" id="datatable">
    <thead>
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Added By</th>
        <th width="200px">Date Added</th>
        <th width="150">Action</th>
    </tr>
    </thead>
    <tbody>
        @if(@$docketDocument)
            @foreach($docketDocument as $row)
                <tr>
                    <td>{{$row->id}}</td>
                    <td>{{$row->name}}</td>
                    <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                    <td>
                        <a  data-toggle="modal" data-target="#updateDocument" data-id="{{$row->id}}" data-name="{{$row->name}}"  data-files="{{$row->files}}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                        <a  data-toggle="modal" data-target="#deleteDocument" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
<div class="dataTables_info pull-left" id="datatable_info" role="status" aria-live="polite">
    Showing  {{ $docketDocument->firstItem() }} to     {{ $docketDocument->lastItem() }} of {{ $docketDocument->total() }}  entries
</div>
<div class="pull-right">
    {{ $docketDocument->appends(['search' => $searchKey])->links() }}
</div>