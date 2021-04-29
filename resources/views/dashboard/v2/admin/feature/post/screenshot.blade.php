@extends('layouts.v2.adminDashboard')
@section('content')
	<section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">{{ Session::get('pageTitle') }}</a></li>
            <li><a href="#">{{ $post->name }}</a></li>
            <li><a href="#">Screenshots</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
     <div class="containerDiv">
    	<div class="row">
            <div class="col-md-12">
                <div class="containerHeader">
                	 <h3>All Post Info</h3>
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
		                    <th>Post Name</th>
		                    <th>Image</th>
		                    <th width="150px">Action</th>
		                </tr>
	                </thead>
	                <tbody>
		                <?php $sn = 1; ?>
		                @if($screenshots)
		                    @foreach($screenshots as $row)
		                        <tr>
		                            <td>{{ $sn }}</td>
		                            <td>{{ $post->name }}</td>
		                            <td>{{ $row->name }}</td>
		                            <td>
		                            	<img src="{{ AmazoneBucket::url() }}{{ $row->image }}" height="100px" width="100px">
		                            </td>
		                            <td>
		                                <div class="nav">
		                                    <button type="button"  class="btn btn-info" data-id="{{$row->id}}" data-name="{{ $row->name }}" data-image="{{ AmazoneBucket::url() }}{{ $row->image }}" style="margin:0px;" data-toggle="modal" data-target="#updateScreenshot">
		                                        <i class="fa fa-edit"></i>
		                                    </button>
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

    <!-- update appInfo -->

    <div class="modal fade " id="updateScreenshot" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <h4 class="modal-title" id="myModalLabel">Update Post</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                {{ Form::open(['url' => 'dashboard/feature/updateScreenshot/', 'files' => true]) }}
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
	                            <div class="form-group" style="margin-top:0px;">
	                            	<input type="hidden" name="post_id" id="post_id">
				                    <input type="file" name="screenshot">
				                    <input type="text" readonly="" class="form-control" placeholder="Screenshot">
				                    <i style="font-size:12px;color:#999;">File Type : jpeg, bmp, png only</i>
				                </div>
                                <br>
                                <strong>Current Featured Image</strong><br/>
                                <img src="" id="image" width="100px">
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
            $('#updateScreenshot').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var  name= $(e.relatedTarget).data('name');
                var image = $(e.relatedTarget).data('image');
                $("#id").val(id);
                $("#name").val(name);
                if(image != "{{ asset('') }}"){
                    $("#image").attr("src",image);
                }
                //$("#image").src(image);
            });
        });
    </script>
@endsection