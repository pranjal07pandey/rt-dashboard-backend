@extends('layouts.companyDashboard')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Client Management</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="{{ route('companyDashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('clientManagement.index') }}">Client Management</a></li>
            <li class="active">Unapproved Clients</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')
    @include('dashboard.company.clientManagement.partials.client-info')

    <div class="boxContent" style="min-height: 500px;">
        <div class="boxHeader">
            <div class="pull-left">
                <strong>Unapproved Client Request</strong>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="boxBody">
            <table class="rtDataTable" id="unapprovedClientDatatable">
                <thead>
                    <tr>
                        <th>Company Name</th>
                        <th>ABN</th>
                        <th>Address</th>
                        <th>Email</th>
                        <th>Date Requested</th>
                        <th width="200px">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if($company->unapprovedClientRequest)
                    @foreach($company->unapprovedClientRequest as $row)
                        @php
                            if($row->company_id==Session::get('company_id')) $companyDetails   =     $row->requestedCompanyInfo;
                            else $companyDetails   =     $row->companyInfo;
                        @endphp
                        <tr>
                            <td>{{ $companyDetails->name }}</td>
                            <td>{{ $companyDetails->abn }}</td>
                            <td>{{ $companyDetails->address }}</td>
                            <td>{{ $companyDetails->userInfo->email }}</td>
                            <td>{{ $row->formattedCreatedDate() }}</td>
                            <td>
                                <a href="{{ route('clients.request.action',array('cancel',$row->id)) }}" class="btn btn-success  btn-xs btn-danger" style="margin:0px;">
                                    <i class="fa fa-close"></i>&nbsp;&nbsp;Cancel
                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/plug-ins/1.10.16/sorting/date-dd-MMM-yyyy.js"></script>
    <script>
        $(document).ready(function() {
            $('#unapprovedClientDatatable').DataTable({ "order": [[ 4, "asc" ]] });
        });
    </script>
@endsection