@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Default Templates Manager
            <small>Add/View Docket </small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Default Templates Book Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Dockets Template</h3>

            {{--<div class="pull-right">--}}
            {{--<button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info popupsecond"  >--}}
            {{--<i class="fa fa-plus-square"></i> Import/Export--}}
            {{--</button>--}}
            {{--</div>--}}

            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" data-toggle="modal" data-target="#saveDefaultTemplates" class="btn btn-xs btn-raised btn-block btn-info"  >
                    <i class="fa fa-plus-square"></i> Add New
                </button>
            </div>
            <div class="pull-right" style="margin-right: 10px;">

                <button type="button" onclick="window.location.href='{{url('dashboard/defaultTemplate/category')}}'" class="btn btn-xs btn-raised btn-block btn-warning"  >
                    <i class="fa fa-plus-square"></i> Add Category
                </button>
            </div>
            <div class="clearfix"></div>
            <br/>
            <table class="table" id="datatable">
                <thead>
                <tr>

                    <th>Docket Name</th>
                    <th>Category</th>
                    <th>Date Added</th>
                    <th width="120">Action</th>
                </tr>
                </thead>
                <tbody>
                @if($defaultTemplate)
                    @foreach($defaultTemplate as $row)

                        <tr>
                            <td>{{$row->title}}</td>
                            <td>
                                @foreach($row->getDefaultDocketCategory as $category)
                                {{$category->default_category_id}}
                                @endforeach
                            </td>
                            {{--@foreach( unserialize($row->category_id) as  $rowData)--}}
                                {{--<td>{{$rowData}}</td>--}}
                            {{--@endforeach--}}
                            <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                            <td>
                                <a  href="{{ url('dashboard/defaultTemplate/designDefaultDocket/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  ><i class="fa fa-eye"></i></a>
                                <a  data-toggle="modal" data-target="#deleteDefaultDocket" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs" style="    padding: 7px 14px;" ><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
                            </td>
                        </tr>
                       @endforeach
                    @endif

                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>
    <div class="modal fade" id="saveDefaultTemplates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Docket</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/defaultTemplate/saveDefaultTemplates', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group label-floating" style="padding-bottom: 0px; margin: 16px 0 0 0;">
                                <label class="control-label" for="title">Category</label>
                                <select id="framework" class="form-control" required name="category_id[]" multiple>
                                    @if($defaultCategory)
                                        @foreach($defaultCategory as $row)
                                            <option value="{!! $row['id'] !!}">  {!! $row['title'] !!}</option>
                                        @endforeach
                                    @endif

                                </select>
                                <input type="hidden" name="helpFlag" id="helpFlag" value="false">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Docket Name</label>
                                <input type="text" name="title" class="form-control" required="required" value="{!! old('title') !!}">
                                <input type="hidden" name="helpFlag" id="helpFlag" value="false">
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Next</button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>

    <div class="modal fade" id="deleteDefaultDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/defaultTemplate/deleteDefaultDocket' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Default Docket</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="defaultDocket_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this  default docket?</p>
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


@endsection
@section('customScript')
    {{--<style>--}}
       {{--.btn-default{--}}
            {{--border-top: transparent;--}}
            {{--border-left: transparent;--}}
            {{--border-right: transparent;--}}
        {{--}--}}
    {{--</style>--}}
    <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
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
        $(document).ready(function(){
            $('#framework').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                buttonWidth:'100%',
                nonSelectedText: 'Select Category',

            });

        });
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteDefaultDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#defaultDocket_id").val(id);

            });
        });
    </script>
@endsection