@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> User Management
            <small>Add/View User</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">User Management</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Users</h3>
            <div class="pull-right">
                <a href="{{ url('dashboard/userManagement/create') }}" class="btn btn-xs btn-raised btn-block btn-info" id="addNew"><i class="fa fa-plus-square"></i> Add New</a>
            </div>
            <div class="clearfix"></div>
            <table class="table">
                <thead>
                <tr>
                    <th>Company Name</th>
                    <th>ABN</th>
                    <th>Address</th>
                    <th>Date Added</th>
                    <th>Email</th>
                    <th>Delete</th>
                </tr>
                </thead>
                <tbody>
                @for($i = 0 ; $i<10; $i++)
                    <tr>
                        <td>CBK CONSTRUCTIONS PTY LTD</td>
                        <td>21113490574</td>
                        <td>15 Geary Place, North Nowra NSW 2541</td>
                        <td>2016-04-27</td>
                        <td>tara.sullivan@cbk.com.au</td>
                        <td>
                            <a href="{{ url('#') }}" class="btn btn-success btn-xs btn-raised" title="Edit Item" style="margin:0px;"><i class="fa fa-eye"></i></a>
                            <a href="{{ url('#') }}" class="btn btn-raised btn-danger btn-xs" title="Edit Item" style="margin:0px;"><i class="glyphicon glyphicon-trash"></i></a>
                        </td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>
    <br/>
@endsection

@section('customScript')
    {{--<!-- DataTables -->--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('assets/dashboard/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>--}}
    {{--<script>--}}
        {{--$(function () {--}}
            {{--$('#datatable').DataTable();--}}
        {{--});--}}
    {{--</script>--}}
@endsection