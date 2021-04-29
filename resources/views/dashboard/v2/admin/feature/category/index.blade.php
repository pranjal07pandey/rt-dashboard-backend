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
                	 <h3>All Category Info</h3>
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
		                    <th>Name</th>
		                    <th>Description</th>
                            <th>No. of Post</th>
		                    <th>Status</th>
		                    <th width="150px">Action</th>
		                </tr>
	                </thead>
	                <tbody>
		                <?php $sn = 1; ?>
		                @if($catgories)
		                    @foreach($catgories as $row)
		                        <tr>
		                            <td>{{ $sn }}</td>
		                            <td>{{ $row->name }}</td>
		                            <td>{{ $row->description }}</td>
                                    <td>{{ count(@$row->post) }}</td>
		                            <td>
	                            	 	@if($row->is_active == 1) 
                                            <span class="label label-success">Active</span>
                                         @else
                                         	<span class="label label-danger">Deleted</span>
                                        @endif
		                            </td>
		                            <td>
		                                <div class="nav">
		                                	@if($row->is_active == 1)
			                                    <button type="button"  class="btn btn-info" data-id="{{$row->id}}" data-name="{{$row->name}}" data-description="{{$row->description}}" style="margin:0px;" data-toggle="modal" data-target="#updateCategory">
			                                        <i class="fa fa-edit"></i>
			                                    </button>
			                                    <a href="{{ url('dashboard/feature/category/post/'.$row->id.'/view') }}" class="btn btn-info" style="margin:0px;">
			                                        <i class="fa fa-eye"></i>
			                                    </a>
			                                    <a href="{{ url('dashboard/feature/category/'.$row->id.'/delete') }}" class="btn btn-danger" style="margin:0px;">
			                                        <i class="fa fa-trash"></i>
			                                    </a>
			                                @else
												<a href="{{ url('dashboard/feature/category/'.$row->id.'/restore') }}" class="btn btn-info" style="margin:0px;">
			                                        <i class="fa fa-undo"></i>
			                                    </a>
			                                @endif
		                                </div>
		                            </td>
		                        </tr>
		                        <?php $sn++; ?>
		                    @endforeach
		                @endif
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
                    <h4 class="modal-title" id="myModalLabel">Add Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(['url' => 'dashboard/feature/saveCategory/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="Name">Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="Description">Description</label>
                                    <textarea class="form-control" name="description" required></textarea>
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

    <div class="modal fade " id="updateCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Update Category</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(['url' => 'dashboard/feature/updateCategory/', 'files' => true]) }}
                <div class="modal-body">
                    <div class="search-box">
                        <div class="row">
                            <input type="hidden" id="id" name="id">
                            <div class="col-md-12">
                                <div class="form-group" style="margin: 0px;">
                                    <label class="control-label" for="name">Name</label>
                                    <input type="text" class="form-control" name="name" id="name" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label" for="Description">Description</label>
                                    <textarea class="form-control" name="description" id="description" required></textarea>
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
                var  name= $(e.relatedTarget).data('name');
                var description = $(e.relatedTarget).data('description');
                $("#id").val(id);
                $("#name").val(name);
                $("#description").val(description);

            });
        });
    </script>
@endsection