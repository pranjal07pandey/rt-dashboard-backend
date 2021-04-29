@if($timers)
    @foreach($timers as $row)
        @php
            $timerLogs = @@\App\TimerLog::where('timer_id', $row->id)->get();
            $count = 1;
            $totalInterval = 0;
        @endphp
        <tr>
            <td style="    padding: 24px 14px 14px 13px;">


                <div style="margin-left: 0px;">
                    <p style="margin: 0;font-size: 15px;font-weight: 600;">{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</p>
                    <p style="margin: 0;"><i style="font-size: 18px;" class="fa fa-map-marker" aria-hidden="true"></i>   {{ $row->location }}</p>
                    <p><i style="font-size: 18px;" class="fa fa-clock-o" aria-hidden="true"></i> {{ \Carbon\Carbon::parse($row->time_started)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_started)->format('H:i:s') }} - {{ \Carbon\Carbon::parse($row->time_ended)->format('d-M-Y') }} {{ \Carbon\Carbon::parse($row->time_ended)->format('H:i:s') }}</p>

                    <h5 style="font-weight: 600;">Clients</h5>

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
                @if($row->time_ended == NULL)
                    {{--<a href="{{ url('/dashboard/company/timers/pause/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Pause</a>--}}
                    {{--<a href="{{ url('/dashboard/company/timers/stop/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Stop</a>--}}
                @endif
                <a href="{{ url('/dashboard/company/timers/'.$row->id.'/view') }}" class="btn btn-xs btn-raised btn-block btn-success">View</a>
                <a href="{{ url('/dashboard/company/timers/'.$row->id.'/download') }}" class="btn btn-xs btn-raised btn-block btn-success">Download</a>
            </td>
        </tr>
    @endforeach
@endif
@if(count($timers)==0)
    <tr>
        <td colspan="4">
            <center>Data Empty</center>
        </td>
    </tr>
@endif


<style>
    .circle-chart__circle {
        animation: circle-chart-fill 2s reverse;
        transform: rotate(-90deg);
        transform-origin: center;
    }

    @keyframes circle-chart-fill {
        to { stroke-dasharray: 0 100; }
    }
</style>