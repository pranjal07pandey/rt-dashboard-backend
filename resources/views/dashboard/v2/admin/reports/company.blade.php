@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Company</a></li>
            <li class="active">Reports</li>
        </ol>
    </section>
    <div class="row">
        <div class="col-md-6">
            <div class="containerDiv">
                <strong> New Companies</strong><br/>
                <small>Total Company : </small> {{ count($company) }}
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
                <strong>All Companies</strong><br/><br/>
                <a href="{{ route('dashboard.reports.company.excel') }}">Excel Reports</a><br/>
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Contact Number</th>
                            <th>No. Of Employees</th>
                            <th>Superadmin Info</th>
                            <th>Subscription Status</th>
                            <th>Registered At</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($company)
                            @foreach ($company as $row)
                                <tr>
                                    <td>@if($row->name) {{ $row->name }} @else {{ '-' }} @endif</td>
                                    <td>@if($row->address) {{ $row->address }} @else {{ '-' }} @endif</td>
                                    <td>@if($row->contactNumber) {{ $row->contactNumber }} @else {{ '-' }} @endif</td>
                                    <td>
                                        @php
                                            $count = @App\Employee::where('company_id', $row->id)->count();
                                            echo $count;
                                        @endphp
                                    </td>
                                    <td>
                                        <p><stong>Full Name:</stong>{{  @$row->userInfo->first_name }}&nbsp; {{  @$row->userInfo->last_name }}</p>
                                        <p><strong>Email Address:</strong>{{  @$row->userInfo->email }}</p>
                                    </td>
                                    <td>
                                        @if($row->trial_period == 1) 
                                            <span class="label label-success">On Trail</span>
                                        @endif
                                        @if($row->trial_period == 2) 
                                            <span class="label label-success">On Subscription</span>
                                        @endif
                                        @if($row->trial_period == 3) 
                                            <span class="label label-danger">Trail Expired/ <br />Subscrption Expired</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</td>
                                    <td>
                                        @if($count> 0) <a href="{{ url('dashboard/reports/company/view/'.$row->id.'/employees') }}">View Employees</a> @else -@endif
                                        <br />
                                        <a href="{{ url('dashboard/reports/company/view/'.$row->id.'/docket/templates') }}">View Docket Templates</a>
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
                    label: 'Companies',
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