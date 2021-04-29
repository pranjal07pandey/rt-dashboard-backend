@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Comapny</a></li>
            <li><a href="#">{{ $company->name }}</a></li>
            <li><a href="#">Docket Templates</a></li>
            <li class="active">Reports</li>
        </ol>
    </section>
    <div class="containerDiv" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <strong>All Docket Templates</strong><br/><br/>
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>Tempate Name</th>
                            <th>Docket Fields</th>
                            <th>Invoicable</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($company)
                            @foreach ($docket_template as $row)
                                <tr>
                                    <td>{{ $row->title }}</td>
                                    <td>
                                        @php
                                            $docket_fields = @App\DocketField::where('docket_id', $row->id)->get(); 
                                        @endphp
                                        <ul>
                                            @foreach ($docket_fields as $docket_field)
                                                <li>{{ $docket_field->label }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>
                                        @if($row->invoiceable == 1) 
                                            <span class="label label-success">Yes</span>
                                        @else
                                            <span class="label label-danger">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection