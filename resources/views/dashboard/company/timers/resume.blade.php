@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> Timers Management
            <small>Add/View Timer</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Timer Management</a></li>
            <li><a href="#">Timer Id - {{ $timer_log->timer_id }}</a></li>
            <li class="active">Resume</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;">
        <div class="col-md-12">
            <h3 style="padding-left: 10px;font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">Resume Timer</h3>

            {{ Form::open(['route' => 'timer.resume.store', 'files' => true]) }}
                <!-- /.box-header -->
                <div class="box-body" style="padding-top:0px;min-height: 250px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                               
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Time Started</label>
                                <input type="hidden" name="timer_log_id" value="{{ $timer_log->id }}">
                                @php 
                                    $now = \Carbon\Carbon::now();
                                @endphp
                                <input type="text" name="time_finished" class="form-control" value="{{ $now }}" required readonly/>
                            </div>
                        </div>

                    </div>
                </div>
        
                <div class="box-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{ route('timers') }}" class="btn btn-xs btn-raised  btn-danger pull-left" id="addNew"><i class="fa fa-reply"></i> Back</a>
                            <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-check"></i> Submit</button>
                        </div>
                    </div>
                </div>
    
            {{ Form::close() }}
        </div>
    </div>
@endsection