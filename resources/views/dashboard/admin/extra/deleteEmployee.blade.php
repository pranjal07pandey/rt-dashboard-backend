@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            Client Management
            <small>Add/View Clients</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Client Management</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;">All Clients</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>Company Name</th>
                    <th>ABN</th>
                    <th>Company</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                @if($employee)
                    @foreach($employee as $row)
                    <tr>
                        <td>{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}</td>
                        <td>{{ $row->companyInfo->abn }}</td>
                        <td>{{ $row->companyInfo->name }}</td>
                        <td>{{ $row->companyInfo->address }}</td>
                        <td>{{ $row->userInfo->email }}</td>
                        <td>
                            {{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/deleteEmployee', $row->id], 'style'=>'display:inline-block;']) }}
                            {{ Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"  />', array(
                                             'type' => 'submit',
                                             'class' => 'btn btn-raised btn-danger btn-xs',
                                             'onclick'=>'return confirm("Are you sure to delete this docket?")'
                                         ))
                                     }}
                            {{ Form::close() }}
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('customScript')
    <!-- DataTables -->
    <script src="{{ asset('assets/dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
        $(function () {
            $('#datatable').DataTable();
        });
    </script>
@endsection