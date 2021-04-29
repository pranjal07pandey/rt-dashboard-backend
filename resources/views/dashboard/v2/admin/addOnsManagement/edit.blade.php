@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">{{ Session::get('pageTitle') }}</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="containerDiv">
        <div class="row">
            <div class="col-md-12">
                <div class="containerHeader">
                    <h3>Edit Add-ons | {{ $addons->title }}</h3>
                </div>
            </div>
            <div class="col-md-12">
            {{ Form::open(['url' => 'dashboard/addOnsManagement/'.$addons->id, 'files' => true,'method'=> 'PATCH']) }}
            <!-- /.box-header -->
                <div class="box-body" style="margin-top:20px;min-height: 260px;padding: 0px 15px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Title</label>
                                <input type="text" name="title" class="form-control" required="required" value="{{ $addons->title }}">
                            </div><br/>

                            <div class="form-group" style="margin-top:0px;">
                                <label class="control-label" for="title">Product Id(Stripe)</label>
                                <input type="text" class="form-control" value="{{ $addons->stripe_product_id }}" disabled="disabled">
                            </div><br/>

                            <div class="form-group" style="margin-top:0px;">
                                <label class="control-label" for="amount">Amount/Month</label>
                                <input type="text" class="form-control" value="{{ $addons->rate }}" name="amount" disabled="disabled">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ url('dashboard/addOnsManagement') }}" class="btn btn-xs btn-raised  btn-danger pull-left" id="addNew"><i class="fa fa-reply"></i> Back</a>
                            <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>

                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection