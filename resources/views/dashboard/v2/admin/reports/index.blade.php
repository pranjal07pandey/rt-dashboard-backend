@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Activity Reports</a></li>
            <li class="active">View</li>
        </ol>
    </section>

    <div class="row">
        <div class="col-md-6">
            <div class="containerDiv">
                <strong> New Users</strong><br/>
                <small>Total User : </small> {{ count($user) }}
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
            <strong>All Active Users</strong><br/><br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Activity</th>
                    <th>Last Sent Docket</th>
                    <th width="120px">Last Active</th>
                </tr>
                </thead>
                <tbody>
                @if($user)
                    @foreach($user as $row)
                        <tr>
                            <td>{{ $row->first_name }} {{ $row->last_name }}</td>
                            <td>{{ $row->email }}</td>
                            <td>{{ @$row->employeeInfo->companyInfo->name }}</td>
                            <td>
                                <strong>Dockets</strong><br/> Sent/Received :  {{ $row->totalDocketSent->count() }}/{{ $row->totalDocketReceived->count() }}<br/>
                                <strong>Emailed Dockets</strong><br/> Sent :  {{ $row->totalEmailedDocketSent->count() }}<br/>
                                <strong>Invoices</strong><br/> Sent/Received :  {{ $row->totalInvoiceSent->count() }}/{{ $row->totalInvoiceReceived->count() }}<br/>
                                <strong>Emailed Invoices</strong><br/> Sent :  {{ $row->totalEmailedInvoiceSent->count() }}<br/>
                            </td>
                            <td>{{ @$row->lastSentDocket->docketInfo->title }}</td>
                            <td>{{ \Carbon\Carbon::parse($row->updated_at) }}</td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    </div>
    <br/>
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
                    label: 'Users',
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