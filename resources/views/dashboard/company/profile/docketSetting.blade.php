@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-user-circle-o"></i> My Account
            <small>Update Account</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Profile</a></li>
            <li class="active">Update Account</li>

        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">
        <div class="col-md-4">
            <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header themePrimaryBg">
                    <div class="widget-user-image">
                        <img class="img-circle" src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}" alt="User Avatar">
                    </div>
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username">
                        @if(auth()->user()->first_name!='')
                            {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                        @else
                            {{ auth()->user()->email }}
                        @endif
                    </h3>
                    <h5 class="widget-user-desc">@if(Session::get('adminType')==1) Super Admin @else Admin @endif</h5>
                </div>
                <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                        <li><a href="{{ url('dashboard/company/profile/subscription') }}"><i class="fa fa-cogs"></i> My Subscription</a></li>
                        <li><a href="{{ route('companyProfile') }}"><i class="fa fa-id-card"></i> My Profile</a></li>
                        <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}"><i class="fa fa-money"></i> Invoice Settings</a></li>
                        <li class="active"><a href="{{ url('dashboard/company/profile/docketSetting') }}"><i class="fa fa-money"></i> Docket Settings</a></li>
                        <li><a href="{{ url('dashboard/company/profile/changePassword') }}"><i class="fa fa-cog"></i>&nbsp;&nbsp;Change Password </a></li>
                        <li><a href="{{ route('Company.CreditCard.Update') }}"><i class="fa fa-credit-card-alt"></i> Update Credit Card </a></li>
                        <li><a href="{{ route('Company.billingHistory') }}"><i class="fa fa-usd"></i>&nbsp;&nbsp;Billing History</a></li>
                        <li><a href="{{ url('dashboard/company/profile/xeroSetting') }}"><i class="fa fa-cogs"></i>&nbsp;&nbsp;Xero Setting</a></li> 
                        <li><a href="{{ route('Company.timezone') }}"><i class="fa fa-clock-o"></i>&nbsp;&nbsp;Timezone</a></li>
                    </ul>
                </div>
            </div>
        </div>
            <div class="col-md-8">
                <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block"><i class="fa fa-money"></i> Docket Settings</h3>




                <div class="pull-right">
                    <!-- Button trigger modal -->
                    <button type="button" data-toggle="modal" data-target="#docketSetting" class="btn btn-xs btn-raised btn-block btn-info popupsecond"  >
                        <i class="fa fa-plus-square"></i> Add New
                    </button>
                </div>

                <div class="clearfix"></div>
                <br/>
                <table class="table" id="datatable">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Terms And Conditions</th>
                        <th>Created By</th>
                        <th>Date Added</th>
                        {{--<th>Assigned</th>--}}
                        <th width="120">Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if(@$docketSetting)
                        @foreach($docketSetting as $row)
                             <tr>
                                 <td>{{$row->title}}</td>
                                <td> {!! str_limit(strip_tags($row->term_condition),100) !!}</td>
                                <td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                                {{--<td> Yes </td>--}}
                                <td>
                                    <a data-toggle="modal" data-target="#updateSettingDocket" data-id="{{$row->id}}" data-term_condition="{{$row->term_condition}}" data-title="{{$row->title}}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                    <a  data-toggle="modal" data-target="#settingDocket" data-id="{{$row->id}}"  class="btn btn-raised btn-danger btn-xs"  >
                                        <span class="glyphicon glyphicon-trash templet-trash" aria-hidden="true"  />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    @if(count(@$docketSetting)==0)
                        <tr>
                            <td colspan="5">
                                <center>Data Empty</center>
                            </td>
                        </tr>
                    @endif


                    </tbody>
                </table>

                <div class="col-md-12" style="background: #e8e8e8; padding: 15px 0 15px 0">
                    <div class="col-md-10">
                        <strong>Docket numbers (id) increment by 1 irrespective of template.</strong>
                    </div>
                    <div class="col-md-2">
                        <input type="checkbox" class="checkNumberSystem" @if ($companyProfile->number_system == 1) checked @endif >
                    </div>
                </div>
            </div>
        </div>

    <br/><br/>

    <div class="modal fade" id="docketSetting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;New Terms And Conditions</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/profile/docketSetting/saveDocketSetting', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="term_condition">Title</label>
                                <input type="text" name="title" class="form-control" required="required" value="{!! old('title') !!}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group label-floating">
                                <label class="control-label" for="term_condition">Terms and Conditions</label>
                                <textarea name="term_condition" class="form-control" required="required">{!! old('term_condition') !!}</textarea>
                                {{--<input type="text" name="term_condition" class="form-control" required="required" value="{!! old('term_condition') !!}">--}}
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

    <div class="modal fade" id="settingDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            {{ Form::open(['url' => 'dashboard/company/profile/docketSetting/deleteDocketSetting' , 'files' => true]) }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete Terms And Conditions</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="design_docket" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to delete this Terms And Conditions?</p>
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

    <div class="modal fade" id="updateSettingDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            {{--<div id="model" data-target="#myModal"></div>--}}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Update Terms And Conditions</h4>
                </div>
                {{ Form::open(['url' => 'dashboard/company/profile/docketSetting/updateDocketSetting', 'files' => true]) }}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="hidden" id="docket_id" name="id">
                                <label class="control-label" for="term_condition">Title</label>
                                <input type="text" name="title" class="form-control" id="title" required="required" >
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="control-label" for="term_condition">Terms and Conditions</label>
                                {{--<input type="text" name="term_condition" class="form-control" id="term_condition" required="required" >--}}
                                <textarea class="form-control" name="term_condition" id="term_condition"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>

        </div>
    </div>
@endsection
@section('customScript')
    <script>
        $(document).ready(function() {
            $('#settingDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                $("#design_docket").val(id);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#updateSettingDocket').on('show.bs.modal', function(e) {
                var id = $(e.relatedTarget).data('id');
                var term_condition = $(e.relatedTarget).data('term_condition');
                var title = $(e.relatedTarget).data('title');
                $("#docket_id").val(id);
                $("#term_condition").text(term_condition);
                $("#title").val(title);
            });
        });

        $(document).on('click','.checkNumberSystem', function () {
            var checked = 0;
            if ($(this).is(':checked')) {
                checked = 1;
            } else {
                checked = 0;
            }

            
            $.ajax({
                type: "POST",
                url: '{{ url('dashboard/company/profile/docketSetting/submitNumberSystem') }}',
                data: {"value": checked,"company_id": "{{Session::get('company_id')}}"},
                success: function (response){
                  alert(response)
                }
            });



        });




    </script>
@endsection
