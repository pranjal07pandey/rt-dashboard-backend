@php
    $employeeIds    =   @@App\Employee::where('company_id',Session::get('company_id'))->pluck('user_id')->toArray();
    $employeeIds[]   =    App\Company::where('id',Session::get('company_id'))->first()->user_id;
    $timers = @@App\Timer::whereIn('user_id', $employeeIds)->orderBy('created_at', 'desc')->paginate(5);
@endphp
    @if($timers)
        @foreach($timers as $row)
            @php
                $timerLogs = @@\App\TimerLog::where('timer_id', $row->id)->get();
                $count = 1;
                $totalInterval = 0;
            @endphp
            <tr>
                <td style="    padding: 24px 14px 14px 13px;">
                    <div style="position: absolute;top: 137px;left: 62px;">
                        <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">Total</p>
                        @php
                            if($row->time_ended != NULL){
                                $datetime1 = \Carbon\Carbon::parse($row->time_started);
                                $datetime2 = \Carbon\Carbon::parse($row->time_ended);
                                $interval = $datetime2->diffInSeconds($datetime1);
                                $date = $interval - $totalInterval;

                               echo'<p style="margin: 0;margin-top: -5px;margin-bottom: -5px;">'.'<b style="font-size: 18px;">'. gmdate("H:i", $date).'</b>'.' <small style="font-weight: 700;font-size: 13px;">'.gmdate("s", $date).'</small>'.'</p>';
                            }
                        @endphp
                        <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">#{{ $count++ }}</p>
                    </div>
                    <svg style="float: left;    float: left;margin-left: -34px;margin-top: -36px;" class="circle-chart" viewbox="0 0 33.83098862 33.83098862" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                        <circle class="circle-chart__background"  stroke="#efefef" stroke-width="1" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
                        <circle class="circle-chart__circle" stroke="#00acc1" stroke-width="1" stroke-dasharray="{{ gmdate("s", $date)}},100" stroke-linecap="square" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
                    </svg>
                    <div style="float: right; margin-left: 0px;">
                        <p style="margin: 0;font-size: 15px;font-weight: 600;">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</p>
                        <p style="margin: 0;"><i style="font-size: 18px;" class="fa fa-map-marker" aria-hidden="true"></i>   {{ $row->location }}</p>
                        <p><i style="font-size: 18px;" class="fa fa-clock-o" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($row->time_started)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_started)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($row->time_ended)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_ended)->format('H:i:s') }}</p>

                        {{--<span># {{$items->tag}}</span>--}}
                        <h5 style="font-weight: 600;">Tags</h5>
                            <ul style="list-style-type: none;margin: 0; margin-top: -6px;padding: 0;overflow: hidden; width: 340px;" >
                                @foreach($row->timerAttachedTag as $items)
                                    <li style="    display: block; float: left;    margin-right: 8px;"><span>&#8226; {{$items->tag}}</span></li>
                                @endforeach
                            </ul>

                        <br>

                        <button type="button" class="btn btn-info btn-xs btn-raised" data-toggle="modal" data-target="#myModal" data-lat='{{ $row->latitude }}' data-lng='{{ $row->longitude }}'>
                            View Map
                        </button>
                    </div>
                </td>
                {{--<td>--}}

                {{--</td>--}}
                {{--<td>--}}
                    {{--<p><strong>In:</strong>{{ $row->time_started }}</p>  --}}
                    {{--<p><strong>Out:</strong>{{ $row->time_ended  }}</p>--}}
                {{--</td>--}}
                <td>
                    <div style="@if(count($timerLogs) > 1) height: 200px; overflow-y: scroll; @else @endif">
                    @if(count($timerLogs) > 0)
                        @foreach($timerLogs as $timerLog)
                            <p style="margin: 0;"><b># {{ $count++ }}. {{ $timerLog->reason }}</b></p>
                            <p style="padding: 0 0 0px 24px; margin: 0;">{{ $timerLog->location }}</p>
                            <p style="padding: 0 0 0px 24px; ">{{ $timerLog->time_started }} - {{ $timerLog->time_finished }}</p>
                        @endforeach
                    </div>

                    @endif
                </td>
                <td>
                    @if($row->status == 0)
                        <span style="border-radius: 10px;" class="label label-info">Started</span>
                    @endif
                    @if($row->status == 1)
                        <span style="border-radius: 10px;" class="label label-success">Finished</span>
                    @endif
                    @if($row->status == 2)
                        <span style="border-radius: 10px;" class="label label-info">Attached To Docket</span>
                    @endif

                </td>
                <td>
                </td>
            </tr>
        @endforeach
    @endif
    @if(count($timers)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif
</table>