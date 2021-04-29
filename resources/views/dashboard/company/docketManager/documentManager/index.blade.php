@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header rt-content-header">
        <h1>Document Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><i class="fa fa-dashboard"></i>&nbsp;Dashboard</li>
            <li class="active">Document Manager</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="clearfix"></div>
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 29px;font-weight: 500;display:inline-block">All Document Manager</h3>
        <div class="pull-right">
            <!-- Button trigger modal -->
            <button style="margin-top: -1px;" type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                <i class="fa fa-plus-square"></i> Add New
                <div class="ripple-container"></div></button>
        </div>
        </div>
        <div class="col-md-12">
            <div class="dataTables_length" id="datatable_length"><label>Show <select name="datatable_length" aria-controls="datatable" class=""><option value="10">10</option></select> entries</label></div>
            <div id="datatable_filter" class="dataTables_filter">
                <label>Search:<input type="search" class="" id="searchInput" placeholder="" aria-controls="datatable" @if(@$searchKey) value="{{ $searchKey }}" @endif ></label>
            </div>
            <div class="datatable">
                <table class="table" id="datatable">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Added By</th>
                        <th width="200px">Date Added</th>
                        <th width="150">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(@$docketDocument)
                        @foreach($docketDocument as $row)
                            <tr>
                                <td>{{$row->id}}</td>
                                <td>{{$row->name}}</td>
                                <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                                <td>
                                    <a  data-toggle="modal" data-target="#updateDocument" data-id="{{$row->id}}" data-name="{{$row->name}}"  data-files="{{$row->files}}" data-filespdf="{{ AmazoneBucket::url() }}{{ $row->files }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                    <a  data-toggle="modal" data-target="#deleteDocument" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  style="margin:0px 5px 0px;"><span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  /></a>
                                </td>
                            </tr>
                            @endforeach
                    @endif


                    </tbody>
                </table>
                <div class="dataTables_info pull-left" id="datatable_info" role="status" aria-live="polite">
                    Showing  {{ $docketDocument->firstItem() }} to     {{ $docketDocument->lastItem() }} of {{ $docketDocument->total() }}  entries
                </div>
                <div class="pull-right">
                    @if(@$searchKey)
                        {{ $docketDocument->appends(['search'=>$searchKey])->links() }}
                    @else
                        {{ $docketDocument->links() }}
                    @endif
                </div>
            </div>
            <div class="datatableSearchResult"></div>
        </div>
    </div>

    <br/><br/>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Add Document </h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketManager/documentManager/addCompanyDocumentManager', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating is-empty">
                                <label class="control-label" for="title">Name</label>
                                <input type="text" name="name" class="form-control" required="required" value="" maxlength="50">
                                <h5 style="color: #757575;"><b>Maximum 50 characters </b></h5>
                            </div>
                            <div class="form-group is-empty is-fileinput">
                                <input type="file" id="image" name="files" accept="application/pdf">
                                <input type="text" readonly="" class="form-control" placeholder="Files">
                                <h5 style="color: #757575;"><b>.pdf only </b></h5>
                                <br>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit"  class="btn btn-primary">Add</button>
                    </div>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>
    <div class="modal fade" id="updateDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Document</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/docketManager/documentManager/updateCompanyDocumentManager' , 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="document_id" name="id">
                            <div class="form-group" style="    padding-bottom: 7px;margin:-15px 0 0 0">
                                <label class="control-label" for="title">Name</label>
                                <input type="text" id="name" name="name" class="form-control" maxlength="50">
                            </div>

                            <div class="form-group is-empty is-fileinput" style="margin: 18px 0 0 0;">
                                {{--<label class="control-label" for="title">Select pdf</label>--}}
                                <input type="file" class="form-control" name="files" >
                                <input type="text" readonly=""  class="form-control" name="files" placeholder="Select pdf">
                                <input type="hidden" readonly=""  class="form-control" name="files" id="files">
                                {{--<img height="200" width="100" class="img-responsive" src="" />--}}
                                <br>
                                {{--<strong>Icon</strong><br>--}}
                                {{--<img src="http://localhost/recordtime-laravel/public/assets/dashboard/images/logoAvatar.png" width="50px">--}}
                            </div>
                            <a href="" id="link_combo" target="_blank">
                                {{--<img height="100" width="30" class="img-responsive" src="https://www.zamzar.com/images/filetypes/pdf.png" id="image-value" />--}}
                                {{--<h5 id="files_name"></h5>--}}
                                <strong>Current Pdf</strong>
                            </a>

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

    <div class="modal fade" id="deleteDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/docketManager/documentManager/deleteCompanyDocumentManager' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Document</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="deleteDocument_id" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this Document?</p>
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
    <style>

    </style>
            <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
            <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
            <script  src="{{asset('assets/jquery.chained.js')}}"></script>
            <!-- <script  src="{{asset('assets/zepto-selector.chained.js')}}"></script> -->
            <script  src="{{asset('assets/zepto.js')}}"></script>
            <script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap-multiselect.js') }}"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
            <script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>
    <script>
        $(document).ready(function() {
            $('#updateDocument').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var name = $(e.relatedTarget).data('name');
                var files = $(e.relatedTarget).data('files');
                var filespdf = $(e.relatedTarget).data('filespdf');
                $("#document_id").val(id);
                $("#link_combo").attr('href',filespdf);
                $("#files_name").text(name);
                $("#name").val(name);
                $("#files").val(files);
            });
        });
    </script>
    <script type="text/javascript">

        $(document).ready(function() {
            $('#deleteDocument').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#deleteDocument_id").val(id);

            });
        });
    </script>
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#searchInput').bind('input', function() {
                        $(".datatable").html('<div style="position: absolute;left: 50%;top: 150px;font-weight: bold;text-align:center;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="margin-bottom:10px;"></i></div>');
                        if($(this).val().length>0){
                            $.ajax({
                                type: "GET",
                                url: "{{ url('dashboard/company/docketManager/documentManager?search=') }}" + $(this).val(),
                                success: function(response){
                                    if(response == ""){

                                    }else{
                                        $(".datatable").html(response).show();
                                    }
                                }
                            });
                        }else{
                            $.ajax({
                                type: "GET",
                                data:{data:"all"},
                                url: "{{ url('dashboard/company/docketManager/documentManager?search=') }}",
                                success: function(response){
                                    if(response == ""){

                                    }else{
                                        $(".datatable").html(response).show();
                                    }
                                }
                            });
                        }
                    });

                    $( function() {
                        $( "#toDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                        $( "#fromDatePicker" ).datepicker({ dateFormat: 'dd-mm-yy'});
                    } );
                    $.fn.dataTable.moment( 'D-MMM-YYYY' );
                    // $('#datatable').dataTable( {
                    //     "order": [[ 0, "desc" ]]
                    // } );
                } );

            </script>


 @endsection
