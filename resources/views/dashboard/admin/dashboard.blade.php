@extends('layouts.adminDashboard')

@section('content')
    <section class="content-header">
        <h1>
            Docket
            <small>Management</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Docket Management</li>
        </ol>
    </section>

    <section class="content">
        <div class="row" style="background: #fff;">
        <div class="col-md-4">
            <div class="sideMenu">
                <div class="menuHeader">
                    <a href="#" class="active">
                        <i class="fa fa-th-list"></i> Docket Templates <i class="fa fa-sort-down pull-right"></i>
                    </a>
                </div>
                <div class="menuContent">
                    <div class="form-group label-placeholder" style="margin-top:0px;">
                        <label for="i5p" class="control-label">Search</label>
                        <input type="email" class="form-control" id="i5p">
                    </div>

                    <div>
                        <button type="button" class="btn btn-raised btn-xs themeSecondaryBg">Contractors</button>
                        <button type="button" class="btn btn-raised btn-xs themeSecondaryBg">Admins</button>
                        <button type="button" class="btn btn-raised btn-xs pull-right themeSecondaryBg" >+</button>
                    </div>

                    <div class="docketBtnList">
                        <button type="button" class="btn btn-raised themePrimaryBg btn-block">Default Docket <span class="pull-right">Admin</span></button>
                        <button type="button" class="btn btn-raised btn-block">Construction <span class="pull-right">Contractors</span></button>
                        <button type="button" class="btn btn-raised btn-block">Trade <span class="pull-right">Contractors</span></button>
                        <button type="button" class="btn btn-raised btn-block">Manager <span class="pull-right">Admins</span></button>
                    </div>
                    <br/>
                    <strong>Elements</strong>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="ch1" class="pull-left">Short text</label>
                                <div class="pull-right">
                                    <div class="checkbox">
                                        <label>
                                            <input id="shortTextCheckbox" type="checkbox" data="checked" checked="checked" >
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="ch1" class="pull-left">Long Text</label>
                                <div class="pull-right">
                                    <div class="checkbox">
                                        <label>
                                            <input id="longTextCheckbox" type="checkbox" checked="" data="checked">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="ch1" class="pull-left">Location</label>
                                <div class="pull-right">
                                    <div class="checkbox">
                                        <label>
                                            <input id="locationCheckbox" type="checkbox" checked="" data="checked">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="col-md-6">--}}
                        {{--<div class="form-group">--}}
                        {{--<label  for="ch1" class="pull-left">Hours</label>--}}
                        {{--<div class="pull-right">--}}
                        {{--<div class="checkbox">--}}
                        {{--<label>--}}
                        {{--<input id="hoursCheckbox" type="checkbox" data="checked" checked="">--}}
                        {{--</label>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="ch1" class="pull-left">Images</label>
                                <div class="pull-right">
                                    <div class="checkbox">
                                        <label>
                                            <input id="imagesCheckbox" type="checkbox" data="checked" checked="">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label  for="ch1" class="pull-left">Num</label>
                                <div class="pull-right">
                                    <div class="checkbox">
                                        <label>
                                            <input id="numCheckbox" type="checkbox" data="checked" checked="">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{--<div class="col-md-6">--}}
                        {{--<div class="form-group">--}}
                        {{--<label  for="ch1" class="pull-left">Rate</label>--}}
                        {{--<div class="pull-right">--}}
                        {{--<div class="checkbox">--}}
                        {{--<label>--}}
                        {{--<input id="rateCheckbox" data="checked" type="checkbox" checked="">--}}
                        {{--</label>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </div>

                <div class="menuHeader">
                    <a href="{{ url('dashboard/userManagement') }}">
                        <i class="fa fa-user"></i> User Management
                    </a>
                </div>
                <div class="menuHeader">
                    <a href="#">
                        <i class="fa fa-user"></i> Invoice Management
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="content" style="width:100%;height:500px;padding: 15px 0px;">

                <div class="contentHeader clearfix">
                    <h4 class="pull-left">Default Docket</h4>
                    <div class="pull-right">
                        <a href="#" class="btn btn-primary themeSecondaryBg  subBtn withripple">Reset to Default</a>
                        <a href="#" class="btn btn-primary themeSecondaryBg  subBtn withripple">Make Default</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="elementAddingDiv">
                    <ul>
                        <li>
                            <a href="#" class="btn btn-primary themeSecondaryBg  btn-sm withripple" id="shortTextAdd">
                                <span><i class="fa fa-plus-square"></i> Short Text </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-primary btn-sm themeSecondaryBg withripple" id="longTextAdd">
                                <span><i class="fa fa-plus-square"></i> Long Text </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-primary themeSecondaryBg  btn-sm withripple" id="locationAdd">
                                <span><i class="fa fa-plus-square"></i> Location  </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-primary themeSecondaryBg  btn-sm withripple" id="imageAdd">
                                <span><i class="fa fa-plus-square"></i> Images </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="btn btn-primary themeSecondaryBg  btn-sm withripple" id="numAdd">
                                <span><i class="fa fa-plus-square"></i> Num </span>
                            </a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <form >
                    <div class="row" id="sortable">
                        <div class="col-md-12 shortTextDiv" id="shortTextDiv">
                            <div class="horizontalList">
                                    <span>
                                          <a href="#" id="shortText" class="editable" data-type="text" data-pk="1" data-url="{{ url('dashboard/submitLabel') }}" data-title="Enter Label Text">Short Text</a>
                                        </span>

                                <div class="form-group">
                                    <input id="title" type="text" class="form-control" name="title" placeholder="Title" value="{{ old('title') }}" required autofocus>
                                </div>
                                <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger" >x</button>
                            </div>
                        </div>
                        {{--<div class="col-md-12" id="hoursDiv">--}}
                        {{--<div class="row">--}}
                        {{--<div class="col-md-6">--}}
                        {{--<div class="horizontalList">--}}
                        {{--<span>Hours</span>--}}
                        {{--<div class="form-group" style="min-width: 150px;width:150px;">--}}
                        {{--<input id="hours" type="text" class="form-control" name="hours" placeholder="Hours" value="{{ old('hours') }}" required autofocus>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-6">--}}
                        {{--<div class="horizontalList">--}}
                        {{--<span>To</span>--}}
                        {{--<div class="form-group" style="min-width: 150px;width:150px;">--}}
                        {{--<input id="to" type="text" class="form-control" name="to" placeholder="To" value="{{ old('to') }}" required autofocus>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        <div class="col-md-12 locationDiv" id="locationDiv">
                            <div class="horizontalList">
                                    <span>
                                         <a href="#" id="shortText" class="editable" data-type="text" data-pk="1" data-url="{{ url('dashboard/submitLabel') }}" data-title="Enter Label Text">Location</a>
                                        </span>
                                <div class="form-group">
                                    <input id="location" type="text" class="form-control" name="location" placeholder="Location" value="{{ old('location') }}" required autofocus>
                                </div>
                                <button type="button" id="removeLocation" class="btn btn-raised btn-xs btn-danger" >x</button>
                            </div>
                            <div class="horizontalList" style="margin-left: 120px;">
                                <span>Allow GPS</span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                                <span>Multiple</span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>&nbsp;&nbsp;
                                <span>Prefill Option</span>
                                <div class="form-group" style="min-width:100px;width:100px;">
                                    <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="7 Address st" value="{{ old('hourlyRate') }}" required autofocus>
                                </div>
                                <button type="button" class="btn btn-raised btn-xs themeSecondaryBg" >+</button>

                            </div>
                        </div>
                        <div class="col-md-12 longTextDiv" id="longTextDiv">
                            <div class="horizontalList">
                                      <span>
                                          <a href="#" id="longText" class="editable" data-type="text" data-pk="1" data-url="{{ url('dashboard/submitLabel') }}" data-title="Enter Label Text">Long Text</a>
                                        </span>
                                <div class="form-group">
                                    <input id="description" type="text" class="form-control" name="description" placeholder="Description" value="{{ old('description') }}" required autofocus>
                                </div>
                                <button type="button" id="removeLongText" class="btn btn-raised btn-xs btn-danger" >x</button>
                            </div>
                        </div>
                        {{--<div class="col-md-12" id="rateDiv">--}}
                        {{--<div class="horizontalList">--}}
                        {{--<span>Hourly Rate</span>--}}
                        {{--<div class="form-group">--}}
                        {{--<input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="Hourly Rate" value="{{ old('hourlyRate') }}" required autofocus>--}}
                        {{--</div>--}}
                        {{--</div>--}}
                        {{--</div>--}}

                        <div class="col-md-12" id="numDiv">
                            <div class="horizontalList">
                                      <span>
                                          <a href="#" id="numText" class="editable" data-type="text" data-pk="1" data-url="{{ url('dashboard/submitLabel') }}" data-title="Enter Label Text">Num</a>
                                        </span>
                                <div class="form-group">
                                    <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="Num Label" value="{{ old('hourlyRate') }}" required autofocus>
                                </div>
                                <button type="button" id="removeNum" class="btn btn-raised btn-xs btn-danger" >x</button>
                            </div>
                        </div>
                        <div class="col-md-12 imageDiv" id="imagesDiv">
                            <div class="horizontalList">
                                    <span>
                                          <a href="#" id="imageText" class="editable" data-type="text" data-pk="1" data-url="{{ url('dashboard/submitLabel') }}" data-title="Enter Label Text">Images</a>
                                        </span>
                                <div class="form-group">
                                    <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="Image Text" value="{{ old('hourlyRate') }}" required autofocus>
                                </div>
                                <button type="button" id="removeImage" class="btn btn-raised btn-xs btn-danger" >x</button>
                            </div>
                            <div class="horizontalList" style="margin-left: 120px;">
                                <span>Upload</span>
                                <div class="checkbox" style="display: inline-block;">
                                    <label>
                                        <input id="ch1" type="checkbox" checked="">
                                    </label>
                                </div>
                                <span>Max Images</span>
                                <div class="form-group" style="min-width:50px;width:50px;">
                                    <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="" value="{{ old('hourlyRate') }}" required autofocus>
                                </div>
                                <span>Min Images</span>
                                <div class="form-group" style="min-width:50px;width:50px;">
                                    <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="" value="{{ old('hourlyRate') }}" required autofocus>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <br/>
                    <div class="docketTemplateUserAccess">
                        <span style=>Apply to:</span>
                        <span>Admin  </span>
                        <div class="checkbox" style="display: inline-block;">
                            <label>
                                <input id="ch1" type="checkbox" checked="">
                            </label>
                        </div>

                        <span>Manager  </span>
                        <div class="checkbox" style="display: inline-block;">
                            <label>
                                <input id="ch1" type="checkbox" checked="">
                            </label>
                        </div>

                        <span>Contractor  </span>
                        <div class="checkbox" style="display: inline-block;">
                            <label>
                                <input id="ch1" type="checkbox" checked="">
                            </label>
                        </div>
                        <button type="button" class="btn btn-raised btn-xs pull-right themeSecondaryBg" >+</button>
                        <div class="form-group label-placeholder pull-right" style="margin-top:0px;">
                            <label for="i5p" class="control-label">Search</label>
                            <input type="email" class="form-control" id="i5p">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </section>
    <!-- Content Header (Page header) -->


@endsection

@section('customScript')
    <script type="text/javascript" src="{{ url('assets/dashboard/js/docket.js') }}"></script>
@endsection