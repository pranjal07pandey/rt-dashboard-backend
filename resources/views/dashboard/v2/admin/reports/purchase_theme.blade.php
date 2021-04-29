@extends('layouts.v2.adminDashboard')
@section('content')
 	<section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active">Purchased Themes</li>
        </ol>
    </section>
     <div class="containerDiv" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <strong>All Purchased Themes</strong><br/><br/>
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>SN</th>
                            <th>Theme Name</th>
                            <th>Company Name</th>
                            <th>Theme Type</th>
                            <th>Stripe Charge Id</th>
                            <th>Purchased On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $sn = 1; ?>
                        @if($theme_purchases)
                            @foreach ($theme_purchases as $row)
                                <tr>
                                    <td>{{ $sn++ }}</td>
                                    <td>{{ $row->themeInfo->name }}</td>
                                    <td>{{ $row->companyInfo->name }}</td>
                                    <td>
                                    	@if($row->themeInfo->type == 1) 
                                            <span class="label label-success">Invoice</span>
                                        @endif
                                        @if($row->themeInfo->type == 2) 
                                            <span class="label label-success">Docket</span>
                                        @endif
                                    </td>

                                    <td>{{ $row->charge_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection