{{--@if($timers)--}}
    {{--@foreach($timers as $row)--}}
        {{--<tr>--}}
            {{--<td>{{ $row->userInfo->first_name }} {{ $row->userInfo->last_name }}</td>--}}
            {{--<td>--}}
                {{--<p><strong>Location:-&nbsp;</strong>{{ $row->location }}</p>--}}
                {{--<button type="button" class="btn btn-info btn-xs btn-raised" data-toggle="modal" data-target="#myModal" data-lat='{{ $row->latitude }}' data-lng='{{ $row->longitude }}'>--}}
                    {{--View Map--}}
                {{--</button>--}}

            {{--</td>--}}
            {{--<td>--}}
                {{--<p><strong>In:</strong>{{ $row->time_started }}</p>--}}
                {{--<p><strong>Out:</strong>{{ $row->time_ended  }}</p>--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--@php--}}
                    {{--$timerLogs = @@\App\TimerLog::where('timer_id', $row->id)->get();--}}
                    {{--$count = 1;--}}
                    {{--$totalInterval = 0;--}}
                {{--@endphp--}}
                {{--@if(count($timerLogs) > 0)--}}
                    {{--<div style="@if(count($timerLogs) > 1) height: 200px; overflow-y: scroll; @else @endif">--}}
                        {{--<table class="table table-striped" width="100%">--}}
                            {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>#</th>--}}
                                {{--<th><i class="icon ion-ios-clock"></i> Start</th>--}}
                                {{--<th>Location</th>--}}
                                {{--<th>Reason</th>--}}
                                {{--<th><i class="icon ion-ios-clock"></i> End</th>--}}
                                {{--<th>Option</th>--}}
                            {{--</tr>--}}
                            {{--</thead>--}}
                            {{--<tbody>--}}
                            {{--@foreach($timerLogs as $timerLog)--}}
                                {{--<tr>--}}
                                    {{--<td><strong>Break:</strong>  {{ $count++ }}</td>--}}
                                    {{--<td>{{ $timerLog->time_started }}</td>--}}
                                    {{--<td>--}}
                                        {{--<p>{{ $timerLog->location }}</p>--}}
                                        {{--<button type="button" class="btn btn-info btn-xs btn-raised" data-toggle="modal" data-target="#myModal1" data-lat='{{ $timerLog->latitude }}' data-lng='{{ $timerLog->longitude }}'>--}}
                                            {{--View Map--}}
                                        {{--</button>--}}
                                    {{--</td>--}}
                                    {{--<td>{{ $timerLog->reason }}</td>--}}
                                    {{--<td>{{ $timerLog->time_finished }}</td>--}}
                                    {{--@php--}}
                                        {{--$datetime1 = \Carbon\Carbon::parse($timerLog->time_started);--}}
                                        {{--if($timerLog->time_finished == NULL){--}}
                                            {{--$totalInterval += 0;--}}
                                        {{--}else{--}}
                                            {{--$datetime2 = \Carbon\Carbon::parse($timerLog->time_finished);--}}
                                            {{--$break = $datetime2->diffInSeconds($datetime1);--}}
                                            {{--$totalInterval += $break;--}}
                                        {{--}--}}
                                    {{--@endphp--}}
                                    {{--<td>--}}
                                        {{--@if($timerLog->time_finished == NULL)--}}
                                            {{--<a href="{{ url('/dashboard/company/timers/resume/'.$timerLog->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Resume</a>--}}
                                        {{--@endif--}}
                                    {{--</td>--}}
                                {{--</tr>--}}
                            {{--@endforeach--}}
                            {{--</tbody>--}}
                        {{--</table>--}}
                    {{--</div>--}}
                {{--@endif--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--@php--}}
                {{--if($row->time_ended != NULL){--}}
                {{--$datetime1 = \Carbon\Carbon::parse($row->time_started);--}}
                {{--$datetime2 = \Carbon\Carbon::parse($row->time_ended);--}}
                {{--$interval = $datetime2->diffInSeconds($datetime1);--}}
                {{--$date = $interval - $totalInterval;--}}
                {{--echo gmdate("H:i:s", $date);--}}
                {{--}--}}
                {{--@endphp--}}
                {{--{{$row->total_time}}--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--@if($row->status == 0)--}}
                    {{--<span class="label label-info">Started</span>--}}
                {{--@endif--}}
                {{--@if($row->status == 1)--}}
                    {{--<span class="label label-success">Finished</span>--}}
                {{--@endif--}}
                {{--@if($row->status == 2)--}}
                    {{--<span class="label label-info">Attached To Docket</span>--}}
                {{--@endif--}}
            {{--</td>--}}
            {{--<td>--}}
                {{--@if($row->time_ended == NULL)--}}
                    {{--<a href="{{ url('/dashboard/company/timers/pause/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Pause</a>--}}
                    {{--<a href="{{ url('/dashboard/company/timers/stop/'.$row->id) }}" class="btn btn-xs btn-raised btn-block btn-info">Stop</a>--}}
                {{--@endif--}}
                {{--<a href="{{ url('/dashboard/company/timers/'.$row->id.'/view') }}" class="btn btn-xs btn-raised btn-block btn-success">View</a>--}}
                {{--<a href="{{ url('/dashboard/company/timers/'.$row->id.'/download') }}" class="btn btn-xs btn-raised btn-block btn-success">Download</a>--}}
            {{--</td>--}}
        {{--</tr>--}}
    {{--@endforeach--}}
{{--@endif--}}
{{--@if(count($timers)==0)--}}
    {{--<tr>--}}
        {{--<td colspan="9">--}}
            {{--<center>Data Empty</center>--}}
        {{--</td>--}}
    {{--</tr>--}}
{{--@endif--}}
@if($timers)
    @foreach($timers as $row)
        @php
            $timerLogs = @@\App\TimerLog::where('timer_id', $row->id)->get();
            $count = 1;
            $totalInterval = 0;
        @endphp
        <tr>
            <td style="    padding: 24px 14px 14px 13px;">
                <div style="position: absolute;    margin-top: 26px; margin-left: 35px;" >
                    <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">Total</p>
                    <p style="margin: 0;margin-top: -5px;margin-bottom: -5px;"><b style="font-size: 18px;">{{$row->total_time}}</b></p>

                    <p style="margin: 0;text-align: center;font-size: 12px;font-weight: 500;color: #9e9e9e;">#{{$row->id}}</p>
                </div>
                <svg style="float: left;    float: left;margin-left: -34px;margin-top: -36px;" class="circle-chart" viewbox="0 0 33.83098862 33.83098862" width="200" height="200" xmlns="http://www.w3.org/2000/svg">
                    <circle class="circle-chart__background"  stroke="#efefef" stroke-width="1" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
                    <circle class="circle-chart__circle" stroke="#00acc1" stroke-width="1" stroke-dasharray="{{ gmdate("s", @$date)}},100" stroke-linecap="square" fill="none" cx="16.91549431" cy="16.91549431" r="9.69999" />
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