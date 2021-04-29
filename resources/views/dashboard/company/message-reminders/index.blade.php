@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Employee Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('employeeManagement.index') }}">Employee Management</a></li>
            <li class="active">Message/Reminders</li>
        </ol>
        <div class="clearfix"></div>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="boxContent" style="min-height: 400px;">
        <div class="boxHeader" style="padding-bottom: 0px;">
            <div class="pull-left">
                <strong>Messages/Reminders</strong>
            </div>
            <div class="pull-right" style="margin-top:-10px">
                <button class="btn btn-xs btn-raised  btn-info " data-toggle="modal" data-target="#newMessageModal">NEW MESSAGE</button>
                <a href="#" class="btn btn-xs btn-raised  btn-info " data-toggle="modal" data-target="#newGroupModal">New Group</a>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="boxBody" style="padding:0px 15px 5px;">
            <div  id="messageListWrapper">
                <div class="row">
                    <div class="col-md-3" style="height: 600px;">

                        @if($messageData)
                            <ul class="messageUserList">
                                @include('dashboard.company.message-reminders.partials.message-user-list')
                            </ul>
                            @else
                        @endif
                    </div>

                    <div class="col-md-9" style="height: 600px;padding-left:0px;">
                        @if($messageData)
                        <div class="viewChat"></div>
                        @else
                            <div class="viewChat">
                                <p class="emptymessage text-center" style="margin-top: 44px;
    margin-right: 48px;">Empty Data</p>

                            </div>
                        @endif
                    </div>


                </div>
            </div><!--/#messageListWrapper-->
        </div>
    </div>
    <br/>

    @include('dashboard.company.message-reminders.modal-popup.new-group')
    @include('dashboard.company.message-reminders.modal-popup.send-message')
@endsection

@section('customScript')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.26.0/slimselect.min.css" rel="stylesheet"></link>
@endsection
