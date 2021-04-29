<div class="attachedTimer">
    <div class="row">
        <div class="col-md-12">
            <p>
                <b>
                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                    Timer Attachments
                </b>
            </p>
        </div>
        @php $totalInterval = 0; @endphp
        @foreach($emailDocket->attachedTimer() as $row)
            <div class="col-md-2">
                <div class="box-timer">
                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                    <p>
                        <strong>
                            @php
                                if($row->timerInfo->time_ended != NULL){
                                    $datetime1 = \Carbon\Carbon::parse($row->timerInfo->time_started);
                                    $datetime2 = \Carbon\Carbon::parse($row->timerInfo->time_ended);
                                    $interval = $datetime2->diffInSeconds($datetime1);
                                    $date = $interval - $totalInterval;
                                    echo gmdate("H:i:s", $date);
                                }
                            @endphp
                        </strong>
                    </p>
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    <span>{!!  str_limit(strip_tags($row->timerInfo->location),35) !!}</span>
                    <p>{{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div><!--/.attachedTimer-->