@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Docket Label</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Label</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">All Docket Label</h3>
            <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px; margin: 0;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Docket Label" data-content="Docket labels are like tags or post-it notes. They help you mark off or highlight a docket to keep track of its status. You can colour code it, use an icon or name it as required. Example: processed, invoiced, entered in MYOB"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
            <div class="pull-right">
                <!-- Button trigger modal -->
                <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info"  data-toggle="modal" data-target="#myModal"  >
                    <i class="fa fa-plus-square"></i> Add New
                </button>

            </div>
            <div class="clearfix"></div>
            <br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    {{--<th>Id</th>--}}
                    <th>Title</th>
                    <th>Color</th>
                    <th>Icon</th>
                    <th width="120">Action</th>
                </tr>
                @if(count($docketlabel)!= 0)
                    @foreach($docketlabel as $row)
                <tr>
                    <td>{{$row->title}}</td>
                    <td>
                        <p style=" background: {{$row->color}};height: 30px;width: 40px;"></p>
                    </td>
                    <td>
                        @if($row->icon == "")

                            @else
                            <img src="{{ AmazoneBucket::url() }}{{ $row->icon }}" height="40" width="40">
                            @endif

                    </td>
                    <td>
                        <a  data-toggle="modal" data-target="#upDateLabel" data-id="{{$row->id}}" data-title="{{$row->title}}" data-color="{{$row->color}}" data-image="{{ AmazoneBucket::url() }}{{ $row->icon }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                        <a  data-toggle="modal" data-target="#deleteLabel" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>

                        {{--{{ Form::open(['method'=>'DELETE', 'url'=>['dashboard/company/docketBookManager/docketLabel/deleteDocketLabel', $row->id], 'style'=>'display:inline-block;']) }}--}}
                        {{--{{ Form::button('<span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  />', array(--}}
                                         {{--'type' => 'submit',--}}
                                         {{--'class' => 'btn btn-raised btn-danger btn-xs',--}}
                                     {{--))--}}
                                 {{--}}--}}
                        {{--{{ Form::close() }}--}}
                    </td>
                </tr>
                    @endforeach

                    @else
                    <tr>
                        <td colspan="4" class="text-center">Empty Data</td>

                    </tr>
                @endif
                </thead>
                <tbody>


                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Docket Label</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketBookManager/saveDocketlabel','id'=>'theform', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Title</label>
                                <input type="text" name="title" class="form-control" required="required" value="{!! old('title') !!}" maxlength="20">
                                <h5 style="color: #757575;"><b>Maximum 20 characters </b></h5>
                            </div>
                            <div class="form-group label-floating">
                                <label class="control-label" for="title">Color</label>
                                <div id="cp2" class="input-group colorpicker colorpicker-component">
                                    <input type="text" value="#00AABB" id="color" name="color" class="form-control" />
                                    <span  class="input-group-addon"><i style="height: 41px;width: 67px;border: 2px solid #000000;margin-top: -11px;"></i></span>
                                </div>
                            </div>
                            {{--<div class="form-group label-floating">--}}
                                {{--<label class="control-label" for="title">Icon</label>--}}
                                {{--<input type="file" name="icon" class="form-control" required="required" value="{!! old('icon') !!}">--}}
                            {{--</div>--}}
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" id="image" name="icon">
                                <input type="text" readonly="" class="form-control" placeholder="Icon">
                                <h5 style="color: #757575;"><b>.png & .jpeg image only</b></h5>
                                <br>
                                {{--<strong>Icon</strong><br>--}}
                                {{--<img src="http://localhost/recordtime-laravel/public/assets/dashboard/images/logoAvatar.png" width="50px">--}}
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
    <div class="modal fade" id="upDateLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Docket Label</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketBookManager/updateDocketlabel' , 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docket_label_id" name="id">
                            <div class="form-group ">
                                <label class="control-label" for="title">Title</label>
                                <input type="text" id="title" name="title" class="form-control" maxlength="20">
                            </div>
                            <div class="form-group ">
                                <label class="control-label" for="title">Color</label>
                                <div id="cp2" class="input-group colorpicker colorpicker-component">
                                    <input type="text" id="color" name="color" class="form-control" />
                                    <span    class="input-group-addon"><i id="color"  style="height: 41px;width: 67px;border: 2px solid #000000;margin-top: -11px;"></i></span>
                                </div>
                            </div>
                            {{--<div class="form-group label-floating">--}}
                            {{--<label class="control-label" for="title">Icon</label>--}}
                            {{--<input type="file" name="icon" class="form-control" required="required" value="{!! old('icon') !!}">--}}
                            {{--</div>--}}
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" class="form-control" name="icon" id="image">
                                <input type="text" readonly=""  class="form-control" placeholder="Icon">
                                {{--<img height="200" width="100" class="img-responsive" src="" />--}}
                                <img height="200" width="100" class="img-responsive" src="" id="image-value" />
                                <br>
                                {{--<strong>Icon</strong><br>--}}
                                {{--<img src="http://localhost/recordtime-laravel/public/assets/dashboard/images/logoAvatar.png" width="50px">--}}
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
    <div class="modal fade" id="deleteLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketBookManager/docketLabel/deleteDocketLabel' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Docket Label</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="docketdelete_label_id" name="id">
                             <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this docket label?</p>
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

    <style>
        .colorpicker:before {
            content: none !important ;
            display: inline-block;
            border-left: 7px solid transparent;
            border-right: 7px solid transparent;
            border-bottom: 7px solid #ccc;
            border-bottom-color: rgba(0,0,0,.2);
            position: absolute;
            top: -7px;
            left: 6px;
        }
        .colorpicker:after {
            content: none !important;
            display: inline-block;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #fff;
            position: absolute;
            top: -6px;
            left: 7px;
        }
        .popover-title{
            background: #2570ba;
            color: #ffffff;
        }
        .popover-content{
            color: #000000;
        }
        .popover.top {
            margin-top: -3px;
        }
    </style>

@endsection
@section('customScript')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.min.js"></script>

    {{--<link rel="stylesheet" type="text/css" href="{{asset('jscolor.js')}}">--}}
    {{--<script src="{{asset('assets/dashboard/jscolor.js')}}"></script>--}}
    <script>
        $(document).ready(function() {
            var colorPicker     =    $('.colorpicker').colorpicker();

            $('#upDateLabel').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var title = $(e.relatedTarget).data('title');
                var color = $(e.relatedTarget).data('color');
                var image = $(e.relatedTarget).data('image');
                $("#docket_label_id").val(id);
                $("#title").val(title);
                $(".colorpicker input").val(color);
                if(image != "{{ asset('') }}"){
                    $("#image-value").attr("src",image);
                }
                $(".colorpicker i").css("background", color);


            });
        });
        $(document).ready(function(){
            $('[data-toggle="popover"]').popover({
                placement : 'top',
                trigger : 'hover'
            });
        });
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteLabel').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#docketdelete_label_id").val(id);

            });
        });
    </script>
    <script>
        $(function()
        {
            $('#theform').submit(function(){
                $("button[type='submit']", this)
                    .attr('disabled', 'disabled');
                return true;
            });
        });
    </script>
@endsection
