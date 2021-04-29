@extends('layouts.companyDashboard')
@section('css')
    <link href="{{ asset('assets/calendar/rescalendar.css') }}" rel="stylesheet">
@endsection
@section('content')
    <section class="content-header rt-content-header">
        <h1>Machine</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('leave_management.index') }}">Machine Management</a></li>
            <li class="active">Machine</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    <div class="boxContent">
        <div class="boxHeader">
            <div>
                <strong>New Employee</strong>
            </div>
        </div>

        <div class="boxBody" style="padding:0px 15px 15px;">
            {{ Form::open(['route' => 'machine_management.update', 'files' => true]) }}
            <input type="hidden" name="machine_id" value="{{ $machine->id }}">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" placeholder="Name" value="{{ $machine->name }}">
                    @if($errors->has('name'))
                        <span class="error" role="alert">{{ $errors->first('name') }}</span>
                    @endif
                </div>
                <div class="form-group col-md-6">
                    <label>Registration</label>
                    <input type="text" class="form-control" name="registration" placeholder="Registration" value="{{ $machine->registration }}">
                    @if($errors->has('registration'))
                        <span class="error" role="alert">{{ $errors->first('registration') }}</span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label>Image</label>
                    <input type="file" class="fileUpload" placeholder="Image" name="image_value" style="opacity: unset;position: unset">
                    <img src="{{ AmazoneBucket::url() }}{{ $machine->image }}" class="fileUploadAppend" alt="{{ $machine->name }}" style="width: 100px"/>
                </div>
            </div>
            <div class="box-footer" style="padding: 0px;">
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
    <br/><br/>
@endsection
@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('.fileUploadAppend').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $(".fileUpload").change(function() {
            readURL(this);
        });
    </script>
@endsection