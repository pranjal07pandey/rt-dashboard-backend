@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Employee Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Employee Management</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')

    <div class="companyProfileBox">
        <div class="companyProfileImage" style="background-image: url('@if(!AmazoneBucket::fileExist($company->logo)) {{ asset('assets/dashboard/images/logoAvatar.png') }} @else {{ AmazoneBucket::url() }}{{ @$company->logo }} @endif');"></div>
        <div class="companyInfo">
            <strong>{{ $company->name }}</strong>
            <span>{{ $company->address }}</span>
            <span>ABN : {{ @$company->abn }}</span>
        </div>
        <a href="{{ route('companyProfile') }}" class="btn btn-xs companyProfileEditBtn"><i class="fa fa-edit"></i></a>
    </div>

    <div class="boxContent" style="min-height: 857px;">
        <div class="boxHeader">
            <div class="pull-left">
                <strong>All Employee</strong>
            </div>
            <div class="pull-right">
                <ul class="boxHeaderActionList">
                    <li>
                        <?php $maxSubscriptionUser  =   0; ?>
                        @if($company->subscription()->count())
                            <?php $maxSubscriptionUser  =   $company->subscription->max_user; ?>
                            @if($company->subscription->isCancel!=1)
                                @if($company->subscription->max_user>($activeUser->count()+1))
                                    <a href="{{ route('employees.create') }}" class="btn btn-xs btn-raised btn-block btn-info" id="addNew"><i class="fa fa-plus-square"></i> Add New</a>
                                @else
                                    <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#upgradeAccountModal"><i class="fa fa-plus-square"></i> Add New</button>
                                @endif
                            @endif
                        @else
                            <?php $maxSubscriptionUser  =   $company->max_user; ?>
                            @if($company->max_user>($activeUser->count()+1))
                                <a href="{{ route('employees.create') }}" class="btn btn-xs btn-raised btn-block btn-info" id="addNew"><i class="fa fa-plus-square"></i> Add New</a>
                            @else
                                <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#upgradeAccountModal">
                                    <i class="fa fa-plus-square"></i> Add New
                                </button>
                            @endif
                        @endif
                    </li>
                    <li>
                        <a href="{{ route('message-reminder.index') }}" class="btn btn-xs btn-raised btn-block btn-info ">
                            <i class="fa fa-comments"></i> Message/Reminders
                        </a>
                    </li>
                </ul>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="boxBody">
            <table class="rtDataTable" id="employeeListDatatable">
                <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Admin</th>
                    <th>Currently Employed</th>
                    <th>Status</th>
                    <th>Receive Email(Copy of Docket) <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Receive Email (Copy of Docket)" data-content="If ticked, you will receive an email copy of any dockets sent to you."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a></th>
                    <th width="50px">Action</th>
                </tr>
                </thead>
                <tbody>
                @if($company)
                    <tr>
                        <td>{{ @$company->userInfo->first_name }} {{ @$company->userInfo->last_name }}</td>
                        <td>{{ $company->userInfo->email }}</td>
                        <td>Yes</td>
                        <td>Yes</td>
                        <td><span class="label label-success">Active</span></td>
                        <td>
                            <input type="checkbox" class="receiveDocketCopy" @if($company->userInfo->receive_docket_copy==1) checked="checked" @endif data="{{  $company->userInfo->id }}"  @if($company->userInfo->id!=Auth::user()->id) disabled="disabled"  @endif>
                        </td>
                        <td>
                            @if($company->user_id!=Auth::user()->id)
                                <button class="btn btn-success btn-xs btn-raised"  style="margin:0px;" disabled><i class="fa fa-upload"></i>&nbsp;&nbsp;Update</button>
                            @else
                                <a href="{{ route('employees.admin.edit',$company->userInfo) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px;"><i class="fa fa-upload"></i>&nbsp;&nbsp;Update</a>
                            @endif
                        </td>
                    </tr>
                @endif
                @if($company->employees)
                    @foreach($company->employees as $row)
                        <tr>
                            <td>{{ @$row->userInfo->first_name }} {{ @$row->userInfo->last_name }}</td>
                            <td>{{ @$row->userInfo->email }}</td>
                            <td>@if($row->is_admin==1)Yes @else No @endif</td>
                            <td>@if($row->employed==1)Yes @else No @endif</td>
                            @if(@$row->userInfo->isActive)
                                <td><span class="label label-success">Active</span></td>
                            @else
                                <td><span class="label label-danger">Inactive</span></td>
                            @endif
                            <td><input type="checkbox" @if(@$row->userInfo->receive_docket_copy==1) checked="checked" @endif class="receiveDocketCopy" data="{{  @$row->userInfo->id }}"   @if(!@$row->userInfo->isActive) disabled @endif> </td>
                            <td>
                                @if(@$row->userInfo->isActive)
                                    <a href="{{ route('employees.edit',$row) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px;"><i class="fa fa-upload"></i>&nbsp;&nbsp;Update</a>
                                @else
                                    <a style="display: inline-block; margin-top: 4px;  padding: 4px 10px;"  data-toggle="modal" data-target="#activateEmployeeModal" data-id="{{@$row->id}}"  class="btn btn-xs btn-raised" ><i class="fa fa-upload"></i>&nbsp;&nbsp;Activate</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div><!--/.boxBody-->
    </div>
    <br/>
    <script>

        $(document).ready(function(){
            $('[data-toggle="popover"]').popover({
                placement : 'top',
                trigger : 'hover'
            });
        });
    </script>
    <style>
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
        .btn:not(.btn-raised):not(.btn-link):hover{
            background-color: rgb(153 153 153 / 0%);
        }

    </style>

    @include('dashboard.company.employeeManagement.modal-popup.activate-employee')
    @include('dashboard.company.employeeManagement.modal-popup.upgrade-account')
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
@endsection
