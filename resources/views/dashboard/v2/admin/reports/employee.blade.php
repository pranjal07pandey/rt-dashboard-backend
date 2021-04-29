@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Comapny</a></li>
            <li><a href="#">{{ $company->name }}</a></li>
            <li><a href="#">Employee</a></li>
            <li class="active">Reports</li>
        </ol>
    </section>
    <div class="row">
        <div class="col-md-6">
            <div class="containerDiv">
                <strong> New Employees</strong><br/>
                <small>Total Employees : </small> {{ count($employee) }}
                <canvas id="myChart" ></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="containerDiv">
                <strong> Dockets/Invoice</strong><br/>
                <small>&nbsp;</small>
                <canvas id="myChart2" ></canvas>
            </div>
        </div>
    </div>
    <div class="containerDiv" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <strong>All Employees</strong><br/><br/>
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email Address</th>
                            <th>Activity</th>
                            <th>Last Sent Docket</th>
                            <th>Type</th>
                            <th>Member Since</th>
                            <th>Device Type</th>
                            <th width="120px">Last Active</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{  $company->userInfo->first_name }}&nbsp; {{  $company->userInfo->last_name }}</td>
                            <td>{{  $company->userInfo->email }}</td>
                            <td>
                                <strong>Dockets</strong><br/> Sent/Received :  {{ $company->userInfo->totalDocketSent->count() }}/{{ $company->userInfo->totalDocketReceived->count() }}<br/>
                                <strong>Emailed Dockets</strong><br/> Sent :  {{ $company->userInfo->totalEmailedDocketSent->count() }}<br/>
                                <strong>Invoices</strong><br/> Sent/Received :  {{ $company->userInfo->totalInvoiceSent->count() }}/{{ $company->userInfo->totalInvoiceReceived->count() }}<br />
                                <strong>Emailed Invoices</strong><br/> Sent :  {{ $company->userInfo->totalEmailedInvoiceSent->count() }}<br/>
                            </td>
                            <td>{{ @$company->userInfo->lastSentDocket->docketInfo->title }}</td>
                            <td><span class="label label-success">Company Admin</span></td>
                            <td>{{ \Carbon\Carbon::parse($company->userInfo->created_at)->format('d M Y') }}</td>
                            <td>
                                @if(@$company->userInfo->device_type == 1) 
                                    <span class="label label-success">Android</span>
                                @endif
                                @if(@$company->userInfo->device_type == 2) 
                                    <span class="label label-success">Ios</span>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($company->userInfo->updated_at) }}</td>
                        </tr>
                        @foreach ($employee as $row)
                            <tr>
                                <td>{{ $row->userInfo->first_name }}&nbsp; {{ $row->userInfo->last_name }}</td>
                                <td>{{ $row->userInfo->email }}</td>
                                <td>
                                    <strong>Dockets</strong><br/> Sent/Received :  {{ $row->userInfo->totalDocketSent->count() }}/{{ $row->userInfo->totalDocketReceived->count() }}<br />
                                    <strong>Emailed Dockets</strong><br/> Sent :  {{ $row->userInfo->totalEmailedDocketSent->count() }}<br/>
                                    <strong>Invoices</strong><br/> Sent/Received :  {{ $row->userInfo->totalInvoiceSent->count() }}/{{ $row->userInfo->totalInvoiceReceived->count() }}<br />
                                    <strong>Emailed Invoices</strong><br/> Sent :  {{ $row->userInfo->totalEmailedInvoiceSent->count() }}<br/>
                                </td>
                                <td>{{ @$row->userInfo->lastSentDocket->docketInfo->title }}</td>
                                <td>@if($row->userInfo->user_type == 2) <span class="label label-success">Admin</span> @else <span class="label label-success">Normal Employee</span> @endif</td>
                                <td>{{ \Carbon\Carbon::parse($row->userInfo->created_at)->format('d M Y') }}</td>
                                <td>
                                    @if(@$row->userInfo->device_type == 1) 
                                        <span class="label label-success">Android</span>
                                    @endif
                                    @if(@$row->userInfo->device_type == 2) 
                                        <span class="label label-success">Ios</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($row->userInfo->updated_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js" ></script>
    <script>
        var ctx = document.getElementById("myChart").getContext('2d');
        var ctx2 = document.getElementById("myChart2").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels:[<?php echo '"'.implode('","', $data['month']).'"' ?>],
                datasets: [{
                    label: 'Employees',
                    data: [<?php echo '"'.implode('","', $data['count']).'"' ?>],
                    borderWidth: 1,
                    backgroundColor: ['rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)','rgba(255, 159, 64, 0.2)']
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
        });
        var myChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: [<?php echo '"'.implode('","', $data['month']).'"' ?>],
                datasets: [{
                    label: 'Dockets',
                    data: [<?php echo '"'.implode('","', $data['docketCount']).'"' ?>],
                    borderWidth: 1,
                    backgroundColor: [
                        'rgba(21, 177, 184, 0.2)',
                    ]
                },
                    {
                        label: 'Invoices',
                        data: [<?php echo '"'.implode('","', $data['invoiceCount']).'"' ?>],
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(1, 46, 84, 0.2)'
                        ]
                    },
                    {
                        label: 'Email Dockets',
                        data: [<?php echo '"'.implode('","', $data['emailDocketCount']).'"' ?>],
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.2)'
                        ]
                    },
                    {
                        label: 'Email Invoices',
                        data: [<?php echo '"'.implode('","', $data['emailInvoiceCount']).'"' ?>],
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)',
                        ]
                    }
                ]
            },
        });
    </script>
@endsection