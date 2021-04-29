@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Client Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Client Management</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    @include('dashboard.company.clientManagement.partials.client-info')

    <div class="boxContent" style="min-height: 500px;">
        <ul class="horizontalMenuTab">
            <li class="active"><a href="{{ route('clientManagement.index') }}" >Clients</a></li>
            <li><a href="{{ route('clients.emails.index') }}" >Custom Email Clients</a></li>
            <li class="pull-right" style="margin-right:8px">
                <button type="button" class="btn btn-xs btn-raised btn-block btn-info" data-toggle="modal" data-target="#findClientModal" style="margin:8px 0px 0px;">
                    <i class="fa fa-plus-square"></i> Find Client
                </button>
            </li>
        </ul>
        <div class="boxBody">
            <table class="rtDataTable" id="clientDatatable">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>ABN</th>
                    <th>Address</th>
                    <th>Email</th>
                    <th>Date Added</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @if($clients)
                    @foreach($clients as $row)
                        @php
                            if($row->company_id==Session::get('company_id')) $companyDetails   =     $row->requestedCompanyInfo;
                            else $companyDetails   =     $row->companyInfo;
                        @endphp
                        <tr>
                            <td>{{ $companyDetails->name }}</td>
                            <td>{{ $companyDetails->abn }}</td>
                            <td>{{ $companyDetails->address }}</td>
                            <td>{{ $companyDetails->userInfo->email }}</td>
                            <td>{{  \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}</td>
                            <td>
                                <a data-toggle="modal" data-target="#deleteRequestModal" data-id="{{$row->id}}"  class="btn btn-success  btn-xs btn-danger" ><i class="fa fa-times"></i>&nbsp;&nbsp;Delete</a>
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

    @include('dashboard.company.clientManagement.modal-popup.delete-request.delete-request')
    @include('dashboard.company.clientManagement.modal-popup.find-client.find-client')
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script>
        $(document).ready(function() {
            $('#clientDatatable').DataTable({ "order": [[ 4, "asc" ]] });
        });
    </script>
@endsection