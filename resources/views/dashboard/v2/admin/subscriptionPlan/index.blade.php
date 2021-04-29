@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">{{ Session::get('pageTitle') }}</a></li>
            <li class="active">Subscriptions</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="containerDiv">
        <div class="row">
        <div class="col-md-12">
            <div class="containerHeader">
                <h3>All Subscription</h3>
                <div class="nav">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-xs btn-raised btn-info" data-toggle="modal" data-target="#myModal">
                        <i class="fa fa-plus-square"></i> Add New
                    </button>
                </div>
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
                                <div class="nav">
                                <a href="{{ url('dashboard/subscriptionPlan/description',$row->id) }}" class="btn btn-raised btn-success  btn-xs btn-primary">
                                    <i class="fa fa-edit"></i>&nbsp;&nbsp;Description
                                </a>
                                {{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/subscriptionPlan', $row->id], 'style'=>'display:inline-block;']) }}
                                {{ Form::button('<i class="material-icons">delete</i>', array(
                                                 'type' => 'submit',
                                                 'class' => 'btn btn-raised btn-danger btn-xs',
                                                 'onclick'=>'return confirm("Are you sure to delete this subscription plan?")'
                                             ))
                                         }}
                                {{ Form::close() }}
                                </div>
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
    </div>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Add Subscription</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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