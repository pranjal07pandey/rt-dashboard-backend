@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Employee Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('employeeManagement.index') }}">Employee Management</a></li>
            <li class="active">Edit</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')

    <div class="boxContent">
        <div class="boxHeader">
            <div>
                <strong>Update Profile</strong>
            </div>
        </div>

        <div class="boxBody" style="padding:0px 15px 15px;">

            {{ Form::open(['route' => array('employees.update',$employee->id), 'files' => true,'method'=> 'PATCH']) }}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group label-floating">
                        <label class="control-label" for="title">First Name</label>
                        <input type="text" name="firstName" class="form-control" required="required" value="{!! $employee->userInfo->first_name !!}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group label-floating">
                        <label class="control-label" for="title">Last Name</label>
                        <input type="text" name="lastName" class="form-control" required="required" value="{!! $employee->userInfo->last_name !!}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group label-floating">
                        <label class="control-label" required for="email">Email</label>
                        <input type="email" required="required" class="form-control" name="email" value="{!! $employee->userInfo->email !!}" disabled="disabled">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group ">
                                <input type="file" id="image" name="image">
                                <input type="text" readonly="" class="form-control" placeholder="Profile Image">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group ">
                                <center>
                                    <a href="{{ AmazoneBucket::url() }}{{ $employee->userInfo->image }}" target="_blank">
                                        <img src="{{ AmazoneBucket::url() }}{{ $employee->userInfo->image }}" height="32px">
                                        <figcaption style="font-size: 8px;margin-top: 2px;">Current Image</figcaption>
                                    </a>
                                </center>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group label-floating">
                        <label class="control-label" for="password">Password</label>
                        <input type="password"  class="form-control" name="password">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group label-floating">
                        <label class="control-label" required for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>
                <div class="col-md-12">
                    <br/>
                    <strong style="font-size: 16px;">User Permissions</strong>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="admin" @if($employee->is_admin==1) checked @endif> Admin?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Admin?" data-content="Tick this, if this employee is also an admin."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="employed" @if($employee->employed==1) checked @endif>  Currently Employed?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Currently Employed?" data-content="This enables/disables access to your employees."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="appearOnRecipient" @if($employee->appear_on_recipient==1) checked @endif>  Appear on Recipient?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover" title="Appear as Recipient?" data-content="When ticked, this user will appear as a recipient on the list of recipients."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="docket" @if($employee->docket==1) checked @endif>  Can Docket?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover" title="Can Docket?" data-content="This option allows your employee to create dockets from the mobile app."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="canSelfDocket" @if($employee->can_self_docket==1) checked @endif>  Can Self Docket?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover" title="Can Self Docket?" data-content="This option allows your employees to send dockets to themselves."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="docket_client" @if($employee->docket_client==1) checked @endif> Docket Client?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Docket Client?" data-content="This grants the ability to send dockets to your clients by your employees."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="invoice" @if($employee->invoice==1) checked @endif> Can Invoice?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Can Invoice?" data-content="If ticked, employees will be able to create invoices on the app."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group" >
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" value="1" name="timer" @if($employee->timer==1) checked @endif>  Can Timer?
                            </label>
                            <a tabindex="0" style="    padding: 0px 8px 5px 8px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Can Timer?" data-content="If ticked, employees will be able to use the timer feature."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                        </div>
                    </div>
                </div>
            </div>

            <div class="box-footer" style="padding: 0px;">
                <div class="row">
                    <div class="col-md-12">
                        <a href="{{ route('employeeManagement.index') }}" class="btn btn-xs btn-raised  btn-danger pull-left" id="addNew"><i class="fa fa-reply"></i> Back</a>
                        <button type="submit" class="btn btn-xs btn-raised btn-info pull-right"  id="addNew"><i class="fa fa-check"></i> Submit</button>
                    </div>
                </div>
            </div>

            {{ Form::close() }}
        </div>
    </div>
    <br/><br/>
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
@endsection
