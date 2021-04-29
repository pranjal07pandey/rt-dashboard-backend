
@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> TimeSheet
            <small>Add/View TimeSheet</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">TimeSheet</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 10px;font-weight: 500;display:inline-block">View Timesheet</h3>
            <h4 style="font-size: 15px;margin: 5px 0px 5px 19px;"><strong>Employee Name:  </strong>{{$timesheetDocketDetail->UserId->first_name}} {{$timesheetDocketDetail->UserId->last_name}}</h4>
            <h4 style="font-size: 15px;margin: 5px 0px 5px 19px;"><strong>Pay Period:  </strong>{{\Carbon\Carbon::parse(explode('|',$timesheetDocketDetail->period)[0])->format('Y-M-d')}}  - {{\Carbon\Carbon::parse(explode('|',$timesheetDocketDetail->period)[1])->format('Y-M-d')}}</h4>
            <h4 style="font-size: 15px;margin: 5px 0px 14px 19px;"><strong>TimeSheet Id:  </strong>{{$timesheetDocketDetail->xero_timesheet_id}}</h4>

            <?php
            $d = array();
            $sn = 1;
            foreach ($allSentDocket as $t) {
                array_push($d,new \Carbon\Carbon($t['docketTime']) );
            }
            ?>
            <div class="clearfix"></div>
            <table class="table" id="datatable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Sync Docket Detail</th>
                    <th >Hours</th>
                </tr>
                </thead>
                <tbody>
                <?php $totalHours=array()?>
                @foreach ($periodDates as $ityemsss)
                    <tr>

                        <td>
                            {{\Carbon\Carbon::parse($ityemsss)->format('Y-M-d')}}
                        </td>


                        <td>
                            <?php $subtotal=array()?>
                            @if (in_array($ityemsss,$d))


                                @foreach ($allSentDocket as $r)
                                    @if (new \Carbon\Carbon($r['docketTime'])==$ityemsss)
                                        @if(!in_array($r['docketId'],$timesheet_docket_detail_att))

                                            #doc {{$r['docketId']}}&nbsp;&nbsp;
                                            Hours: {{ round($r['totalHours']/60, 2)}}<br>
                                            <?php array_push($subtotal, round($r['totalHours']/60, 2)) ?>
                                        @endif
                                    @endif
                                @endforeach
                            @else
                                N/a
                            @endif

                        </td>



                        <td>
                            {{array_sum ($subtotal )}}
                            <?php array_push($totalHours, array_sum ($subtotal ))?>
                        </td>
                    </tr>
                @endforeach



                <tr>
                    <td colspan="2">
                        <strong style="text">Total Hours</strong>
                    </td>

                    <td>
                        <strong>{{array_sum ($totalHours )}}</strong>
                    </td>
                </tr>


                </tbody>
            </table>
        </div>
    </div>
    <br><br>





@endsection

@section('customScript')



@endsection