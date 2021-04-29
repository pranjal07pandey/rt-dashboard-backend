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
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">{{ $subscriptionPlan->name }}</h3>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <a href="{{ url('dashboard/subscriptionPlan') }}" class="btn btn-xs btn-raised btn-info">
                    <i class="fa fa-reply"></i> Back
                </a>
                <button type="button" class="btn btn-xs btn-raised btn-success" data-toggle="modal" data-target="#myModal">
                    <i class="fa fa-plus-square"></i> Add Description
                </button>
            </div>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Description</th>
                    <th width="200px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn = 1; ?>
                @if(@$subscriptionPlan)
                    @foreach($subscriptionPlan->description as $row)
                        <tr>
                            <td>{{ $sn }}</td>
                            <td>{{ $row->description }}</td>
                            <td>
                                <a href="#" class="btn  btn-xs btn-primary" style="margin:0px;">
                                    <i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit
                                </a>
                                <a href="#" class="btn btn-success  btn-xs btn-danger" style="margin:0px;">
                                    <i class="fa fa-times"></i>&nbsp;&nbsp;Delete
                                </a>
                            </td>
                        </tr>
                        <?php $sn++; ?>
                    @endforeach
                @endif
                <tr>
                    <td colspan="7">
                        @if(count(@$plans)==0)
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
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Plan Description</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/subscriptionPlan/description/'.$subscriptionPlan->id, 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="form-group" style="margin-top:0px;">
                            <label class="control-label" for="description">Description</label>
                            <input type="text" class="form-control" name="description"  required>
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