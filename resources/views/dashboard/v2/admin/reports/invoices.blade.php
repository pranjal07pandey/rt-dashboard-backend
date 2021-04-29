@extends('layouts.v2.adminDashboard')
@section('content')
    <section class="content-header">
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="#">Stripe Invoice</a></li>
            <li class="active">Reports</li>
        </ol>
    </section>
    <div class="row">
        <div class="col-md-6">
            <div class="containerDiv">
                <small>Total Amount : </small>A$&nbsp; {{ array_sum($total_amount) }}
                <canvas id="myChart" ></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="containerDiv">
                <strong> Invoice</strong><br/>
                <small>&nbsp;</small>
                <canvas id="myChart2" ></canvas>
            </div>
        </div>
    </div>
    <div class="containerDiv" style="margin-top: 20px;">
        <div class="row">
            <div class="col-md-12">
                <strong>All Invoices</strong><br/><br/>
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>Invoice Number</th>
                            <th>Customer</th>
                            <th>Date of Payment</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stripe_inovices as $row)
                            <tr>
                                <td>#{{ $row->number }}</td>
                                <td>
                                    <strong>Name:</strong>{{ @@App\Company::where('stripe_user', $row->customer)->first()->name }}<br />
                                    <strong>Address:</strong>{{ @@App\Company::where('stripe_user', $row->customer)->first()->address }}
                                </td>
                                <td>{{ \Date('d M Y',intval($row->date)) }}</td>
                                <td>A$&nbsp;{{ $row->total/100 }}</td>
                                <td>@if($row->paid == true) <span class="label label-success">Paid</span> @else span class="label label-danger">Un-paid</span> @endif</td>
                                <td>
                                    <a href="{{ $row->hosted_invoice_url }}" target="_blank"><i class="material-icons">remove_red_eye</i></a>
                                    <a href="{{ $row->invoice_pdf }}" target="_blank"><i class="material-icons">picture_as_pdf</i></a>
                                </td>
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
                    label: 'Invoices',
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
                    label: 'Amount',
                    data: [<?php echo '"'.implode('","', $grapData).'"' ?>],
                    borderWidth: 1,
                    backgroundColor: [
                        'rgba(21, 177, 184, 0.2)',
                    ]
                },
                {
                        label: 'Unpaid',
                        data: [<?php echo '"'.implode('","', $data['unpaid_count']).'"' ?>],
                        borderWidth: 1,
                        backgroundColor: [
                            'rgba(1, 46, 84, 0.2)'
                        ]
                    },
                ]
            },
        });
    </script>
@endsection