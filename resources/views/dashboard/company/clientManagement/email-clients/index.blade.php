@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header  rt-content-header">
        <h1>Client Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('clientManagement.index') }}">Client Management</a></li>
            <li class="active">Custom Emails Clients</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    @include('dashboard.company.clientManagement.partials.client-info')

    <div class="boxContent" style="min-height: 500px;">
        <ul class="horizontalMenuTab">
            <li ><a href="{{ route('clientManagement.index') }}" >Clients</a></li>
            <li class="active"><a href="{{ route('clients.emails.index') }}" >Custom Email Clients</a></li>
            <li class="pull-right" style="margin-right:8px">
                <button type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info"  data-toggle="modal" data-target="#addEmailClientModal" style="margin:8px 0px 0px;">
                    <i class="fa fa-plus-square"></i> Add Custom Email Client
                </button>
            </li>
        </ul><!--/.horizontalMenuTab-->
        <div class="boxBody">
            <table class="rtDataTable" id="emailClientDatatable">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Date Added</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @if($company->emailClients)
                    @foreach($company->emailClients as $row)
                        <tr>
                            <td>
                                {{$row->full_name }}<br/><br/>
                                <strong>{{$row->company_name }}</strong>
                                <span>{{$row->company_address }}</span>
                            </td>
                            <td>{{$row->emailUser->email }}</td>
                            <td>{{ $row->formattedCreatedDate() }}</td>
                            <td>
                                <a data-toggle="modal" data-target="#updateEmailClientModal" data-id="{{$row->id}}" data-email="{{$row->emailUser->email}}" data-fullname="{{$row->full_name}}" data-companyname="{{$row->company_name}}" data-companyaddress="{{$row->company_address}}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;"><i class="fa fa-eye"></i></a>
                                <a  data-toggle="modal" data-target="#deleteEmailClientModal" data-id="{{$row->id}}"  class="btn btn-danger btn-xs btn-raised" style="margin:0px 5px 0px;"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
            <div class="clearfix"></div>
        </div>
    </div>
    <br/><br/>

    @include('dashboard.company.clientManagement.modal-popup.add-email-client.add-email-client')
    @include('dashboard.company.clientManagement.modal-popup.delete-email-client.delete-email-client')
    @include('dashboard.company.clientManagement.modal-popup.update-email-client.update-email-client')
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script>
        $(document).ready(function() {
            $('#emailClientDatatable').DataTable({ "order": [[ 2, "desc" ]] });
        });
    </script>
@endsection