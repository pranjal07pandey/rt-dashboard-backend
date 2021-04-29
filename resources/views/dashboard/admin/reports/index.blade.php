@extends('layouts.adminDashboard')
@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-users"></i> Activity Reports
            <small>View</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Activity Reports</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;">
        <div class="col-md-12">
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-6">
                    <strong> New Users</strong><br/>
                    <small>Total User : </small> {{ count($user) }}
                    <canvas id="myChart" ></canvas>

                </div>
                <div class="col-md-6">
                    <strong> Dockets/Invoice</strong><br/>
                    <small>&nbsp;</small>
                    <canvas id="myChart2" ></canvas>
                </div>
            </div>
            <hr>
            <strong>All Active Users</strong><br/><br/>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Email</th>
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
                            <td>
                                <strong>Dockets</strong><br/> Sent/Received :  {{ $row->totalDocketSent->count() }}/{{ $row->totalDocketReceived->count() }}<br/><br/>
                                <strong>Invoices</strong><br/> Sent/Received :  {{ $row->totalInvoiceSent->count() }}/{{ $row->totalInvoiceReceived->count() }}<br/>
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
    <br/>
@endsection

@section('customScript')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js" ></script>
    <script src="{{ asset('assets/dashboard/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/dashboard/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script>
            $(function () {
            $('#datatable').DataTable({"order": [[ 4, "desc" ]]});
            });
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