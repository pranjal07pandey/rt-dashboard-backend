@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">{{ Session::get('pageTitle') }}</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="containerDiv">
        <div class="row">
            <div class="col-md-12">
                <div class="containerHeader">
                    <h3>All Docket Filed Category Info</h3>
                    <div class="nav">
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                            <i class="fa fa-plus-square"></i> Add New
                        </button>
                    </div>
                </div>
                <table class="table table-striped" id="datatable">
                    <thead>
                    <tr>
                        <th>SN</th>
                        <th>Title</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($datas as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->title }}</td>
                                <td>
                                    <button type="button"  class="btn btn-info" data-id="{{ $data->id }}" data-title="{{ $data->title }}" style="margin:0px;" data-toggle="modal" data-target="#updateCategory">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Add Docket Filed Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(['route' => 'docket.field.category.store', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="title">Title</label>
                                    <input type="text" class="form-control" name="title" required>
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

    <div class="modal fade " id="updateCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Update Docket Filed Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(['route' => 'docket.field.category.update', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="title">Title</label>
                                    <input type="hidden" name="id" id="id">
                                    <input type="text" class="form-control" id="title" name="title" required>
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
            $('#updateCategory').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var  title= $(e.relatedTarget).data('title');
                $("#id").val(id);
                $("#title").val(title)
            });
        });
    </script>
@endsection