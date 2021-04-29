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
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box themePrimaryBg">
                    <div class="inner">
                        <h3>5300</h3>

                        <p>All Clients</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box themePrimaryBg">
                    <div class="inner">
                        <h3>44</h3>

                        <p>Client Request</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box themePrimaryBg">
                    <div class="inner">
                        <h3>5300</h3>

                        <p>Approved Clients</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box themePrimaryBg">
                    <div class="inner">
                        <h3>150</h3>

                        <p>Unapproved Clients</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-trash-a"></i>
                    </div>
                    <a href="#" class="small-box-footer">
                        More info <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

        </div>
        <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;">
            <div class="col-md-12">
                <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;">All Clients</h3>
                <table class="table" id="datatable">
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
                        <td>ACT GEOTECHNICAL ENGINEERS</td>
                        <td>19063673530</td>
                        <td>5/9 Beaconsfield Street, Fyshwick, ACT, 2609</td>
                        <td>2015-12-02</td>
                        <td>jeremy.murray@actgeoeng.com.au</td>
                        <td>
                            <a href="{{ url('#') }}" class="btn btn-success btn-xs btn-raised" title="Edit Item" style="margin:0px;"><i class="fa fa-eye"></i></a>&nbsp;&nbsp;
                            <a href="{{ url('#') }}" class="btn btn-raised btn-danger btn-xs" title="Edit Item" style="margin:0px;"><i class="glyphicon glyphicon-trash"></i></a>
                        </td>
                    </tr>
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