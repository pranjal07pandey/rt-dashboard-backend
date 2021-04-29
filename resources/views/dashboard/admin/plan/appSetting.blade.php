@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> App Info
            <small>Add/View App Info</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">App Info Management</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All App Info</h3>
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
                    <th>Field Name</th>
                    <th>Field Slug</th>
                    <th>Value</th>
                    <th width="180px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $sn = 1; ?>
                @if($appInfo)
                    @foreach($appInfo as $row)
                <tr>
                    <td>{{ $sn }}</td>
                    <td>{{$row->field_name}}</td>
                    <td>{{$row->field_slug}}</td>
                    <td>{{$row->value}}</td>
                    <td><button type="button"  class="btn btn-raised btn-success  btn-xs btn-primary" data-id="{{$row->id}}" data-fieldname="{{$row->field_name}}" data-fieldslug="{{$row->field_slug}}"  data-value="{{$row->value}}" style="margin:0px;" data-toggle="modal" data-target="#updateAppInfo">
                            <i class="fa fa-edit"></i>&nbsp;&nbsp;Update
                        </button>
                    </td>
                </tr>
                <?php $sn++; ?>
                    @endforeach
                @endif
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
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add App Info</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/appSetting/saveAppInfo/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="maxUserLimit">Field Name</label>
                                    <input type="text" class="form-control" name="field_name" required>
                                </div>
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="title">Field Slug</label>
                                    <input type="text" class="form-control" name="field_slug" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="title">Value</label>
                                    <input type="text" class="form-control" name="value"  >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="margin-right: 20px;">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <!-- update appInfo -->

    <div class="modal fade " id="updateAppInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update App Info</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/appSetting/updateAppInfo/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <input type="hidden" id="id" name="id">
                            <div class="col-md-6">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="maxUserLimit">Field Name</label>
                                    <input type="text" class="form-control" name="field_name" id="fieldname" required>
                                </div>
                            </div>
                            <div class="col-md-6" >
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="title">Field Slug</label>
                                    <input type="text" class="form-control" name="field_slug" id="fieldslug" >
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="title">Value</label>
                                    <input type="text" class="form-control" name="value"  id="value"  required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" style="margin-right: 20px;">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

@endsection
@section('customScript')
    <script>
        $(document).ready(function() {
            $('#updateAppInfo').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var  fieldname= $(e.relatedTarget).data('fieldname');
                var fieldslug = $(e.relatedTarget).data('fieldslug');
                var value = $(e.relatedTarget).data('value');
                $("#id").val(id);
                $("#fieldname").val(fieldname);
                $("#fieldslug").val(fieldslug);
                $("#value").val(value);

            });
        });
    </script>
@endsection