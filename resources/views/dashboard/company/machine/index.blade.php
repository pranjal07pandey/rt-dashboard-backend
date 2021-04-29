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
            <div class="pull-left">
                <strong>All Machine</strong>
            </div>
            <div class="pull-right">
                <ul class="boxHeaderActionList">
                    <li>
                        <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#addMachineModal">
                            <i class="fa fa-plus-square"></i> Add New
                        </button>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="boxBody">
            <table class="rtDataTable" id="employeeListDatatable">
                <thead>
                <tr>
                    <th>S.N.</th>
                    <th>Name</th>
                    <th>Registration</th>
                    <th>Image</th>
                    <th width="50px">Action</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($machines as $key => $machine)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $machine->name }}</td>
                            <td>{{ $machine->registration }}</td>
                            <td><img src="{{ AmazoneBucket::url() }}{{ $machine->image }}" alt="{{ $machine->name }}" style="width: 100px"></td>
                            <td>
                                <div style="display: inline-flex">
                                    <a href="{{ route('machine_management.edit',$machine->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i><div class="ripple-container"></div></a>
                                    <a class="btn btn-warning btn-xs btn-raised machineDelete" data-id="{{ $machine->id }}"><i class="fa fa-trash"></i><div class="ripple-container"></div></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div><!--/.boxBody-->
    </div>
    <br/>

    <div class="modal fade rt-modal @if($errors->any())in @endif" id="addMachineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" @if($errors->any()) style="display: block;" @else style="display: none;" @endif >
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['method'=>'POST','route' => 'machine_management.store' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Machine</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name">
                            @if($errors->has('name'))
                                <span class="error" role="alert">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group col-md-6">
                            <label>Registration</label>
                            <input type="text" class="form-control" name="registration" placeholder="Registration">
                            @if($errors->has('registration'))
                                <span class="error" role="alert">{{ $errors->first('registration') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label>Image</label>
                            <input type="file" placeholder="Image" class="fileUpload" name="image_value" style="opacity: unset;position: unset">
                            <img src="" alt="" class="fileUploadAppend" style="width: 100px">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade rt-modal" id="deleteMachineModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{ Form::open(['method'=>'POST','route' => 'machine_management.delete']) }}
                <div class="modal-content">
                    <div class="modal-header themeSecondaryBg">
                        <button type="button" class="close closeModal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Delete Employee</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <input type="hidden" id="machineId" name="id">
                                <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to delete this machine?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Yes</button>
                        <button type="button" class="btn btn-primary closeModal" data-dismiss="modal" aria-label="Close">No</button>
                    </div>
                </div>
            {{ Form::close() }}
        </div>
    </div>

@endsection
@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script>
        $('.machineDelete').click(function(){
            $('#machineId').val($(this).attr('data-id'));
            $('#deleteMachineModal').addClass('in').show();
        });

        $('.closeModal').click(function(){
            $(this).closest('.modal').removeClass('in').hide();
        })

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