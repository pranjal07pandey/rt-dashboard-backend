@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Document Theme - {{ $theme->name }}</a></li>
            <li class="active">Edit</li>
        </ol>
    </section>
    <div class="containerDiv">
        <div class="row">
            <div class="col-md-12">
                <h3>Edit Document Theme {{ $theme->name }}</h3><br />
                {{ Form::open(['url' => 'dashboard/documentTheme/update/', 'files' => true]) }}
                <input type="hidden" name="id" value="{{ $theme->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="maxUserLimit">Name</label>
                            <input type="text" class="form-control" name="name" required value="{{ $theme->name }}">
                        </div>
                    </div>
                    <div class="col-md-12" >
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="title">Description</label>
                            <textarea class="form-control" name="description" required>{{ $theme->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-12" >
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="title">Type</label>
                            <select class="form-control" name="type">
                                <option value="1" <?=$theme->type == 1 ? ' selected="selected"' : '';?>>Invoice</option>
                                <option value="2" <?=$theme->type == 2 ? ' selected="selected"' : '';?>>Docket</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" >
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="title">Paid/Free</label>
                            <select class="form-control" name="paid_free">
                                <option value="0" <?=$theme->paid_free == 0 ? ' selected="selected"' : '';?>>Free</option>
                                <option value="1" <?=$theme->paid_free == 1 ? ' selected="selected"' : '';?>>Paid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="maxUserLimit">Price</label>
                            <input type="text" class="form-control" name="price" value="{{ $theme->price }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="title">Preview</label>
                            <input type="file" class="form-control" name="preview">
                        </div>
                        <br />
                        <img src="{{ AmazoneBucket::url() }}{{ $theme->preview }}" height="50px" width="50px"/>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="maxUserLimit">Web View Path</label>
                            <input type="text" class="form-control" name="web_view_path" value="{{ $theme->web_view_path }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="maxUserLimit">Mobile View Path</label>
                            <input type="text" class="form-control" name="mobile_view_path" required value="{{ $theme->mobile_view_path }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <label class="control-label" for="maxUserLimit">Pdf View Path</label>
                            <input type="text" class="form-control" name="pdf_view_path" required value="{{ $theme->pdf_view_path }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="margin: 0px;">
                            <button type="submit" class="btn btn-primary" style="margin-right: 20px;">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection