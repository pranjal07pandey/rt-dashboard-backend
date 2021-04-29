@if(count($getInvoicealeList)>0)
    @foreach ($getInvoicealeList as $row)
        <div class="mix col-md-4" style="padding-bottom: 25px;" >
            <div style="    background: #F7F7F7;">

                <div  style=" padding-bottom: 15px; ">
                    <div class="col-md-9">
                        <img src="{{$row['senderImage']}}" style="height: 40px; width: 40px; border-radius: 25px;     margin: 6px 0px 0px 0px;" >
                        <div class="pull-right" style="margin-right: 36px;">
                            <div >
                                <h4 style="font-size: 14px; font-weight: 500; margin-bottom: 0px;"  >{!! $row['sender'] !!}</h4>
                                <span  style="color: #777777; font-size: 12px;"> {!! $row['company'] !!}</span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="docketDetail" style="margin-top: 12px;">
                            <p style="font-size: 12px;margin-bottom: 2px;">Docket Id : {{$row["companyDocketId"]}} </p>
                            <p style="font-size: 12px;margin-bottom: 2px; font-weight: 500;" >{!! $row['docketName'] !!} </p>
                            <p style="font-size: 12px;margin-bottom: 2px;" >Recipents: {!! $row['recipient'] !!} </p>
                        </div>
                    </div>


                    <div class="col-md-3 ">
                        <input type="checkbox" name="invoiceablechecked" value="{{$row["id"]}}" @if ($row["isApproved"] ==0) disabled @endif class="invoiceablechecked pull-right" style="    margin-top: 19px;"><br><br>
                        <span style="font-size: 13px;color: #D78C10;font-weight: 500;    margin-top: 18px;" class="pull-right" >{!! $row['invoiceAmount'] !!}</span>
                    </div>


                    <div class="clearfix"></div>
                    <hr style="margin: 5px 0 0px 0px;">
                    <div style="margin: 7px 10px 16px 10px;">
                        <span style="color: #777777; font-size: 12px;" class="pull-left">{{$row['status']}}</span>
                        <span style="color: #777777; font-size: 12px;" class="pull-right">{{$row["dateAdded"]}}</span>
                    </div>


                    {{--                                    <h4 style="font-size: 14px; font-weight: 500; width: 75%;" class=" pull-left">Arjun Dangal</h4>--}}
                    {{--                                    <span>Web And App Pvt. Ltd.</span>--}}
                    {{--                                    <button class="btn btn-info pull-right" class="installTemplate" data-toggle="modal" data-target="#installTemplate" style="padding:3px 8px 3px 8px;border: 1px solid #15B1B8;color: #15B1B8;font-size: 12px;font-weight: 400;     margin: 14px 0px 0px 0px;" data-id=""> Install</button>--}}
                    {{--                                    <div class="clearfix"></div>--}}
                    {{--                                    <p style="color: #777777; font-size: 12px;margin-bottom: 6px;"> <i class="fa fa-user-circle" aria-hidden="true"></i> ashik</p>--}}
                    {{--                                    <span style="color: #777777; font-size: 12px;" class="pull-left"><i class="fa fa-calendar" aria-hidden="true"></i>sfaf</span>--}}
                    {{--                                    <span  style="color: #777777; font-size: 12px;"  class="pull-right"><i class="fa fa-download" aria-hidden="true"></i> asf</span>--}}
                    {{--                                    <div class="clearfix"></div>--}}
                </div>
            </div>
        </div>








        {{--            <div class="col-md-12" style="border: 1px solid #e8e6e6;padding: 15px;margin-bottom: 12px;">--}}
        {{--                <p>{!! $row['docketName'] !!}</p>--}}
        {{--                <h1>{!! $row['invoiceAmount'] !!}</h1>--}}
        {{--                <input type="checkbox" name="invoiceablechecked" value="{{$row["id"]}}" class="invoiceablechecked">--}}
        {{--            </div>--}}
    @endforeach
@else
    <div style="text-align: center">
        <img style="height: 321px;" src="{{asset('assets/empty.svg')}}">
        <p style="color: #93A8B5;    margin-top: 24px;">You have not sent any invoiceable docket to Arjun Dangal</p>

    </div>
@endif