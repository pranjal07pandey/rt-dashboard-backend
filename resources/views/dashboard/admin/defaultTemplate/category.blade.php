@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Category Manager
            <small>Add/View Category </small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Category</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Category</h3>
            <div class="pull-right">
                <a type="button" href="{{url('dashboard/defaultTemplate')}}" class="btn btn-xs btn-raised btn-block btn-primary"  >
                    <i class="fa fa-reply"></i> Back
                </a>
            </div>
            <div class="pull-right" style="margin-right: 10px;">
                <a type="button" data-toggle="modal" data-target="#category" class="btn btn-xs btn-raised btn-block btn-info"  >
                    <i class="fa fa-plus-square"></i> Add New
                </a>
            </div>

            <div class="clearfix"></div>
            <br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Icon</th>
                    <th>Date Added</th>
                    <th width="120">Action</th>
                </tr>
                </thead>
                <tbody>
                @if($category)
                    @foreach($category as $row)
                <tr>
                    <td>{{$row->title}}</td>
                    <td>@if($row->icon == "")

                        @else
                            <img src="{{ AmazoneBucket::url() }}{{ $row->icon }}" height="40" width="40">
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                    <td>
                        <a  data-toggle="modal" data-target="#updateCategory" data-id="{{$row->id}}" data-title="{{$row->title}}"  data-image="{{ AmazoneBucket::url() }}{{ $row->icon }}" class="btn btn-success btn-xs btn-raised"  ><i class="fa fa-eye"></i></a>
                        <a  data-toggle="modal" data-target="#deleteCategory" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs" style="    padding: 7px 14px;" ><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
                    </td>
                </tr>
                    @endforeach
                    @endif

                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>
    <div class="modal fade" id="category" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;New Category</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/defaultTemplate/saveDefaultCataegory' , 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group ">
                                <label class="control-label" for="title">Title</label>
                                <input type="text"  name="title" class="form-control" maxlength="20">
                            </div>
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" class="form-control" name="icon" >
                                <input type="text" readonly=""  class="form-control" placeholder="Icon">
                                <h5 style="color: #757575;"><b>.png & .jpeg image only</b></h5>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <div class="modal fade" id="deleteCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/defaultTemplate/deleteCategory' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Category</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="deletecategory_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this category?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    <div class="modal fade" id="updateCategory" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Category</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/defaultTemplate/updateDefaultCataegory' , 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="category_id" name="id">
                            <div class="form-group ">
                                <label class="control-label" for="title">Title</label>
                                <input type="text" id="title" name="title" class="form-control" maxlength="20">
                            </div>
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" class="form-control" name="icon" id="image">
                                <input type="text" readonly=""  class="form-control" placeholder="Icon">
                                <img height="200" width="100" class="img-responsive" src="" id="image-value" />
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>
@endsection
@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/dashboard/tour/bootstrap.css')}}">
    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatable').DataTable({
                "order": [ 2, 'desc' ]
            });
        } );
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteCategory').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#deletecategory_id").val(id);

            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#updateCategory').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var title = $(e.relatedTarget).data('title');
                var image = $(e.relatedTarget).data('image');
                $("#category_id").val(id);
                $("#title").val(title);
                if(image != "{{ asset('') }}"){
                    $("#image-value").attr("src",image);
                }


            });
        });
    </script>
@endsection