
@if($sentDocket->status != 3)
        @if($sentDocket->docketApprovalType==0)
            <hr class='dotted' />
            <div class="col-md-12">
               @if($sentDocket->status == 3)
                    <div class="col-md-6">
                        @if($sentDocket->status==1)
                        <h5 style="font-weight: 800;">Approved By:</h5>
                        @foreach($approval_type as $row)
                            @if($row['status']==1)
                                {{--<img style="width: 63px;float: left;margin-right: 16px;" src="{{$row->signature)}}">--}}
                                <p style="padding-top: 8px;">{{$row['full_name']}}  on {{\Carbon\Carbon::parse($row['approval_time'])->format('d-M-Y h:i a T')}}</p>
                            @endif
                            <div class="clearfix"></div>
                            <br>
                        @endforeach
                       @endif
                    </div>

                    <div class="col-md-6">
                        @if($sentDocket->status==0)
                            <h5 style="font-weight: 800;">Pending Approval:</h5>
                            @foreach($approval_type as $row)
                                @if($row['status']==0)
                                    <p >{{$row['full_name']}} </p>
                                @endif
                            @endforeach
                        @else


                        @endif
                    </div>

               @else
                    <div class="col-md-6">
                        <h5 style="font-weight: 800;">Approved By:</h5>
                        @foreach($approval_type as $row)
                            @if($row['status']==1)
                                {{--<img style="width: 63px;float: left;margin-right: 16px;" src="{{$row->signature)}}">--}}
                                <p style="padding-top: 8px;">{{$row['full_name']}}  on {{\Carbon\Carbon::parse($row['approval_time'])->format('d-M-Y h:i a T')}}</p>
                            @endif
                            <div class="clearfix"></div>
                            <br>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        @if($sentDocket->status==0)
                            <h5 style="font-weight: 800;">Pending Approval:</h5>
                            @foreach($approval_type as $row)
                                @if($row['status']==0)
                                    <p >{{$row['full_name']}} </p>
                                @endif
                            @endforeach
                        @else

                        @endif
                    </div>

               @endif


            </div>
        @else
            <hr class='dotted' />
            <div class="col-md-12">
                @if($sentDocket->status == 3)
                    <div class="col-md-6">
                        <h5 style="font-weight: 800;">Approved By:</h5>
                        @foreach($approval_type as $row)
                            @if($row['status']==1)
                                <img style="width: 84px;float: left;margin-right: 16px;" src="{{$row['signature']}}">
                                <p style="padding-top: 8px;">{{$row['name']}} on {{\Carbon\Carbon::parse($row['approval_time'])->format('d-M-Y h:i a T')}}</p>
                            @endif
                            <div class="clearfix"></div>
                            <br>
                        @endforeach

                    </div>
                    <div class="col-md-6">
                        @if($sentDocket->status==0)
                            <h5 style="font-weight: 800;">Pending Approval:</h5>
                            @foreach($approval_type as $row)
                                @if($row['status']==0)
                                    <p >{{$row['full_name']}} </p>
                                @endif
                            @endforeach
                        @else

                        @endif
                    </div>
                @else
                <div class="col-md-6">
                    <h5 style="font-weight: 800;">Approved By:</h5>
                    @foreach($approval_type as $row)
                        @if($row['status']==1)
                            <img style="width: 84px;float: left;margin-right: 16px;" src="{{$row['signature']}}">
                            <p style="padding-top: 8px;">{{$row['name']}} on {{\Carbon\Carbon::parse($row['approval_time'])->format('d-M-Y h:i a T')}}</p>
                        @endif
                        <div class="clearfix"></div>
                        <br>
                    @endforeach

                </div>
                <div class="col-md-6">
                    @if($sentDocket->status==0)
                        <h5 style="font-weight: 800;">Pending Approval:</h5>
                        @foreach($approval_type as $row)
                            @if($row['status']==0)
                                <p >{{$row['full_name']}} </p>
                            @endif
                        @endforeach
                    @else

                    @endif
                </div>
           @endif

            </div>
        @endif
    @else
       <hr class='dotted' />
    @endif