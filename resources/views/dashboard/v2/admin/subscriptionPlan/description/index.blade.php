@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ url('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="{{ url('dashboard/subscriptionPlan') }}">Subscription Plans</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="containerDiv">
        <div class="row">
            <div class="col-md-12">
                <div class="containerHeader">
                    <h3>{{ $subscriptionPlan->name }}</h3>
                    <div class="nav">
                        <a href="{{ url('dashboard/subscriptionPlan') }}" class="btn btn-xs btn-raised btn-info">
                            <i class="fa fa-reply"></i> Back
                        </a>
                        <button type="button" class="btn btn-xs btn-raised btn-success" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-plus-square"></i> Add Description
                        </button>
                    </div>
                </div>
                <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>SN</th>
                    <th>Description</th>
                    <th width="150px">Action</th>
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
                                <div class="nav">
                                    <a href="#" class="btn  btn-xs btn-primary" style="margin:0px;">
                                        <i class="fa fa-pencil"></i>&nbsp;&nbsp;Edit
                                    </a>
                                    <a href="#" class="btn btn-success  btn-xs btn-danger" style="margin:0px;">
                                        <i class="fa fa-times"></i>&nbsp;&nbsp;Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php $sn++; ?>
                    @endforeach
                @endif
                <tr>
                    <td colspan="7">
                        @if(count(@$subscriptionPlan->description)==0)
                            <center>Data Empty</center>
                        @endif
                    </td>
                </tr>
                </tbody>
            </table>
            </div>
        </div><!--/.row-->
    </div>
    <!-- Modal -->
    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h5 class="modal-title">Add Plan Description</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection