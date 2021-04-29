<div class="isvoiceabledocket">
    @if(count($rangeandDocketTemplate)!=0)
        <div id="show-hidden-menu" style="    float: right;margin-top: -23px;cursor: pointer;background: red;padding: 1px 11px;color: #fff;border-radius: 4px;font-size: 13px;">Filter</div>
        <div class="clearfix"></div>
        <input type="hidden" id="record_time_user" value="{{$record_time_user}}">

        <div class="hidden-menu" style="display: none; background: whitesmoke;padding: 12px 30px 16px 15px;margin-bottom: 9px;">
            <div class="row">
                <!--<div class="col-md-12">-->
                <!--    <strong>Date</strong>-->
                <!--    <input type="date" class="form-control" id="filterDateFrom" >-->
                <!--    <strong>Amount</strong>-->
            <!--        <input data-addui='slider' data-min='{{$rangeandDocketTemplate["range"]["min"]}}' data-max='{{$rangeandDocketTemplate["range"]["max"]}}' data-range='true' value='{{$rangeandDocketTemplate["range"]["min"]}},{{$rangeandDocketTemplate["range"]["max"]}}' id="filterPriceRange"/>-->
                <!--    <strong>Docket Template</strong>-->
                <!--    <div class="filterSelect" style="margin-bottom: 132px;">-->
                <!--        <select id="frameworkFilter" class="form-control " multiple>-->
            <!--            @if(@$rangeandDocketTemplate["docket_template"])-->
            <!--                @foreach ($rangeandDocketTemplate["docket_template"] as $row)-->
            <!--                    <option value="{!! $row["id"] !!}" >{!! $row["title"] !!}</option>-->
                <!--                @endforeach-->
                <!--            @endif-->
                <!--        </select>-->
                <!--    </div>-->

                <!--     <br>-->
                <!--    <strong>Docket Id</strong>-->
                <!--    <input type="number" class="form-control" id="filterDocketId">-->
                <!--</div>-->

                <div class="col-md-12">
                    <div class="pull-left">
                        <strong>Date</strong>
                    </div>
                    <div class="pull-right">
                        <strong>Date Range</strong>
                        <label class="dateToggle">
                            <input type="checkbox" id="dateSwitch">
                            <span class="dateToggle--slider round"></span>
                        </label>
                    </div>
                    <div class="clearfix"></div>

                    <div class="row">
                        <div class="col-md-12" id="dateFrom">
                            <label for="from" style="font-size: 12px;">From</label>
                            <input type="text" class="form-control" id="filterDateFrom">
                        </div>
                        <div class="col-md-6" id="dateTo">
                            <label for="to" style="font-size: 12px;">To</label>
                            <input type="text" class="form-control" id="filterDateTo">
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div class="col-md-6 amountSlider">
                            <strong>Amount</strong>
                            <input data-addui='slider' data-min='{{$rangeandDocketTemplate["range"]["min"]}}' data-max='{{$rangeandDocketTemplate["range"]["max"]}}' data-range='true' value='{{$rangeandDocketTemplate["range"]["min"]}},{{$rangeandDocketTemplate["range"]["max"]}}' id="filterPriceRange"/>
                        </div>
                        <div class="col-md-6">
                            <strong>Docket Id</strong>
                            <input type="number" class="form-control" id="filterDocketId">
                        </div>
                    </div>
                    <strong>Docket Template</strong>
                    <div class="filterSelect" style="margin-bottom: 132px;">
                        <select id="frameworkFilter" class="form-control " multiple>
                            @if(@$rangeandDocketTemplate["docket_template"])
                                @foreach ($rangeandDocketTemplate["docket_template"] as $row)
                                    <option value="{!! $row["id"] !!}" >{!! $row["title"] !!}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <br>
                </div>
            </div>
            <a class="btn btn-info" style="background-color: #4ea6d6;    float: right;color: #ffffff;padding: 4px 11px;font-size: 13px;" id="applyFilter" > Apply Filter</a>
            <div class="clearfix"></div>
        </div>
    @endif



    <strong>Dockets</strong>
    <br>
    <br>
    <div class="row" style="    height: 540px;overflow-x: auto; position: relative">
        <input type='hidden' class="invoiceableDocketType" value ="1">
        <div id="filterDocketAttacheable">
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
                    <!--<p style="color: #93A8B5;    margin-top: 24px;">You have not sent any invoiceable docket to Arjun Dangal</p>-->

                </div>
            @endif
        </div>
        <span class="spinnerChecker" style="position:absolute;left:50%;bottom:50%;font-size: 51px; display: none;">
            <i class="fa fa-spinner fa-spin"></i>
        </span>
    </div>
</div>
<style>

    #ui-datepicker-div{
        z-index: 111111 !important;
    }
</style>

<link href=" {{asset('assets/slider/addSlider.css')}}" rel="stylesheet" type="text/css" />
<script src="{{asset('assets/slider/addSlider.js')}}"></script>
<script src="{{asset('assets/slider/Obj.min.js')}}"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

