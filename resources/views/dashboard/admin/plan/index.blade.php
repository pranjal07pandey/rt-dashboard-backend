@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Subscription Management
            <small>Add/View Subscription</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Subscription Management</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Subscription</h3>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-square"></i> Add New
                </button>
            </div>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Name</th>
                    <th>Plan Id</th>
                    <th>Amount(AUD)</th>
                    <th>Maximum Users Limit</th>
                    <th>Added</th>
                    <th width="180px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn = 1; ?>
                @if($plans)
                    @foreach($plans as $row)
                        <tr>
                            <td>{{ $sn }}</td>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->plan_id }}</td>
                            <td>$ {{ $row->amount."/".$row->interval }}</td>
                            <td>{{ $row->max_user }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                            <td>
                                {{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/subscriptionPlan', $row->id], 'style'=>'display:inline-block;']) }}
                                {{ Form::button('<span class="glyphicon glyphicon-trash" aria-hidden="true"  />', array(
                                                 'type' => 'submit',
                                                 'class' => 'btn btn-raised btn-danger btn-xs',
                                                 'onclick'=>'return confirm("Are you sure to delete this subscription plan?")'
                                             ))
                                         }}
                                {{ Form::close() }}
                                <a href="{{ url('dashboard/subscriptionPlan/description',$row->id) }}" class="btn btn-raised btn-success  btn-xs btn-primary" style="margin:0px;">
                                    <i class="fa fa-edit"></i>&nbsp;&nbsp;Description
                                </a>
                            </td>
                        </tr>
                        <?php $sn++; ?>
                    @endforeach
                @endif
                <tr>
                    <td colspan="7">
                        @if(count($plans)==0)
                            <center>Data Empty</center>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Subscription</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/subscriptionPlan/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                            <div class="form-group" style="margin-top:0px;">
                                <label class="control-label" for="title">Name</label>
                                <input type="text" class="form-control" name="name"  required>
                            </div>
                            <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="title">Amount</label>
                                    <div class="input-group">
                                    <div class="input-group-addon">$</div>
                                    <input type="number" step="0.01" placeholder="" class="form-control" name="amount" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="maxUserLimit">Maximum User Limit</label>
                                    <input type="number" class="form-control" name="maxUserLimit" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="title">Interval</label>
                                    <input type="text" readonly autocomplete="on" placeholder="" class="form-control" name="interval" value="Monthly">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label" for="title">Currency</label>
                                    <input type="text" readonly autocomplete="on" placeholder="" class="form-control" name="currency" value="AUD">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal" style="margin-left: 20px;">Close</button>
                    <button type="submit" class="btn btn-primary" style="margin-right: 20px;">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection