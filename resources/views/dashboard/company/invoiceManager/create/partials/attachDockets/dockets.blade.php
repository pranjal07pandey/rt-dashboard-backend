<div class="isvoiceabledocket">

        <div id="show-hidden-menu" style="    float: right;margin-top: -23px;cursor: pointer;background: red;padding: 1px 11px;color: #fff;border-radius: 4px;font-size: 13px;">Filter</div>
        <div class="clearfix"></div>
        <input type="hidden" id="record_time_user" value="{{$request->recipient}}">
        <input type="hidden" id="submitURL" value="{{url('dashboard/company/sentInvoice/filterInvoiceableDocket')}}">
        <div class="hidden-menu" style="display: none; background: whitesmoke;padding: 12px 30px 16px 15px;margin-bottom: 9px;">
            <div class="row">
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
                            @if(array_key_exists('amount', $advanceFilterData))
                                <input data-addui='slider' data-min='{{min($advanceFilterData['amount'])}}' data-max='{{max($advanceFilterData['amount'])}}' data-range='true' value='{{min($advanceFilterData['amount'])}},{{max($advanceFilterData['amount'])}}' id="filterPriceRange"/>
                            @else
                                <input data-addui='slider' data-min='0' data-max='0' data-range='true' value='0,0' id="filterPriceRange"/>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Docket Id</strong>
                            <input type="number" class="form-control" id="filterDocketId" name="filterDocketId">
                        </div>
                    </div>
                    <strong>Docket Template</strong>
                    <div class="filterSelect" style="margin-bottom: 132px;">
                        <select id="frameworkFilter" class="form-control " multiple>
                            @if(@$advanceFilterData["dockets"])
                                @foreach ($advanceFilterData["dockets"] as $row)
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

    <strong>Dockets</strong><br><br>
    <div class="row" style="height: 540px;overflow-x: auto; position: relative">
        <input type='hidden' class="invoiceableDocketType" value ="1">
        <div id="filterDocketAttacheable">
            @if($dockets->count()>0)
                @php $sn = 1; @endphp
                @foreach ($dockets as $row)
                    <div class="mix col-md-4" style="padding-bottom: 25px;" >
                        <div style="background: #F7F7F7;">
                            <div>
                                <div class="col-md-9">
                                    @if(AmazoneBucket::fileExist($row->senderUserInfo->image))
                                        <img src="{{ AmazoneBucket::url() }}{{ $row->senderUserInfo->image }}" class="pull-left" style="height: 40px;width: 40px; border-radius: 25px;margin: 7px 8px 0px 0px;">
                                    @endif
                                    <div class="pull-left" style="margin-right: 36px;">
                                        <div >
                                            <h4 style="font-size: 14px; font-weight: 500; margin-bottom: 0px;"  >{!! $row->sender_name !!}</h4>
                                            <span  style="color: #777777; font-size: 12px;"> {!! $row->company_name !!}</span>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="docketDetail" style="margin-top: 12px;">
                                        <p style="font-size: 12px;margin-bottom: 2px;">Docket Id : {{$row->formated_id}} </p>
                                        <p style="font-size: 12px;margin-bottom: 2px; font-weight: 500;" >{!! $row->docketInfo->title !!} </p>
                                    </div>
                                </div>

                                @php
                                    $totalRecipientApprovals    =   $row->sentDocketRecipientApproval()->count();
                                    $totalRecipientApproved     =   $row->sentDocketRecipientApproved()->count();
                                    if($totalRecipientApproved == $totalRecipientApprovals ){
                                        $approvalText               =  "Approved";
                                    }else{
                                        $approvalText               =   $totalRecipientApproved."/".$totalRecipientApprovals." Approved";
                                    }
                                @endphp

                                <div class="col-md-3 ">
                                    <input type="checkbox" name="invoiceablechecked" value="{{ $row->id }}"  class="invoiceablechecked pull-right" style="margin-top: 19px;"><br><br>
                                    <span style="font-size: 13px;color: #D78C10;font-weight: 500;margin-top: 18px;" class="pull-right" >$ {!! sprintf('%0.2f',$row->invoiceAmount()) !!}</span>
                                </div>
                                <div class="clearfix"></div>
                                <hr style="margin: 5px 0 0px 0px;">
                                <div style="padding: 7px 15px 10px 15px;">
                                    <span style="color: #777777; font-size: 12px;" class="pull-left">{{ $approvalText }}</span>
                                    <span style="color: #777777; font-size: 12px;" class="pull-right">{{$row->formattedCreatedDate()}}</span>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($sn%3==0) <div class="clearfix"></div> @endif
                    @php $sn++ @endphp
                @endforeach
            @else
                <div style="text-align: center">
                    <img style="height: 321px;" src="{{asset('assets/empty.svg')}}">
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
