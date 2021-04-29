@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active">Document Themes</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="containerDiv" style="margin-top: 20px;">
            <div class="row">
                <div class="col-md-12">
                    <div class="containerHeader">
                            <h3>All Document Themes</h3>
                            <div class="nav">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-plus-square"></i> Add New
                                </button>
                            </div>
                        </div>
                    <table class="table" id="datatable">
                        <thead>
                            <tr>
                                <th>S.N</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Paid / Free</th>
                                <th>Price</th>
                                <th>Purchase Count</th>
                                <th>Status</th>
                                <th>Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $sn = 1; ?>
                            @foreach ($document_themes as $row )
                                <tr>
                                    <td>{{ $sn++ }}</td>
                                    <td>{{ $row->name }}</td>
                                    <td>
                                        @if($row->type == 1) 
                                            <span class="label label-success">Invoice</span>
                                        @endif
                                        @if($row->type == 2) 
                                            <span class="label label-success">Docket</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->paid_free == 0) 
                                            <span class="label label-success">Free</span>
                                        @else
                                            <span class="label label-success">Paid</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->paid_free == 1) 
                                            A$&nbsp;{{ $row->price }}
                                        @else
                                            A$&nbsp;0
                                        @endif
                                    </td>
                                    <td>
                                        {{ count($row->themePurchase) }}
                                    </td>
                                    <td>
                                        @if($row->is_active == 1) 
                                            <span class="label label-success">Active</span>
                                        @else
                                            <span class="label label-danger">Deleted</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="nav">
                                            <a href="{{ url('dashboard/documentTheme/edit/'.$row->id) }}" class="btn btn-raised btn-primary btn-xs"><i class="material-icons">create</i></a></i></a>
                                            @if($row->is_active == 1)
                                                <a href="{{ url('dashboard/documentTheme/delete/'.$row->id) }}" class="btn btn-raised btn-danger btn-xs"><i class="material-icons">delete</i></a></i></a>
                                            @else
                                                <a href="{{ url('dashboard/documentTheme/restore/'.$row->id) }}" class="btn btn-raised btn-primary btn-xs"><i class="material-icons">restore</i></a></i></a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
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
                <h4 class="modal-title" id="myModalLabel">Add Document Theme</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            {{ Form::open(['url' => 'dashboard/documentTheme/store/', 'files' => true]) }}
            <div class="modal-body">
                <div class="search-box">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="maxUserLimit">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-12" >
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="title">Description</label>
                                <textarea class="form-control" name="description" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-12" >
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="title">Type</label>
                                <select class="form-control" name="type">
                                    <option value="1">Invoice</option>
                                    <option value="2">Docket</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" >
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="title">Paid/Free</label>
                                <select class="form-control" name="paid_free">
                                    <option value="0">Free</option>
                                    <option value="1">Paid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="maxUserLimit">Price</label>
                                <input type="text" class="form-control" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="title">Preview</label>
                                <input type="file" class="form-control" name="preview" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="title">Screenshot</label>
                                <input type="file" class="form-control" name="screenshot[]" required multiple>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="maxUserLimit">Web View Path</label>
                                <input type="text" class="form-control" name="web_view_path" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="maxUserLimit">Mobile View Path</label>
                                <input type="text" class="form-control" name="mobile_view_path" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group" style="margin: 0px;">
                                <label class="control-label" for="maxUserLimit">Pdf View Path</label>
                                <input type="text" class="form-control" name="pdf_view_path" required>
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