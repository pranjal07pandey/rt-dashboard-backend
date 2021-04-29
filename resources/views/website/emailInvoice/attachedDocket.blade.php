<hr>
<div class="row">
    @include('website.emailDocket.partial.docketInfo')
</div>
<div class="docket-body">
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="printTh" style="width:50%"><div class="printColorDark">Description</div></th>
                    <th class="printTh" style="width:50%"><div class="printColorDark">Value</div></th>
                </tr>
                </thead>
                <tbody>
                @if($emailDocket->sentDocketValue()->count())
                    @foreach($emailDocket->sentDocketValue()->get() as $docketValue)
                        @if($docketValue->docketFieldInfo->docket_field_category_id==5)
                            @include('website.emailDocket.modularField.image')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==7)
                            @include('website.emailDocket.modularField.unitRate')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==8)
                            @include('website.emailDocket.modularField.checkbox')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==9)
                            @include('website.emailDocket.modularField.signature')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==12)
                            @include('website.emailDocket.modularField.headerTitle')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==14)
                            @include('website.emailDocket.modularField.sketchPad')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==15)
                            @include('website.emailDocket.modularField.document')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==18)
                            @include('website.emailDocket.modularField.yesNoNaCheckbox')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==20)
                            @include('website.emailDocket.modularField.manualTimer')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==22)
                            @include('website.emailDocket.modularField.grid')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==24)
                            @include('website.emailDocket.modularField.tallyableUnitRate')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id==27)
                            @include('website.emailDocket.modularField.advanceHeader')

                        @elseif($docketValue->docketFieldInfo->docket_field_category_id!=13)
                            @include('website.emailDocket.modularField.default')
                        @endif
                    @endforeach
                    @foreach($emailDocket->sentDocketValue()->get() as $docketValue)
                        @if($docketValue->docketFieldInfo->docket_field_category_id==13)
                            @include('website.emailDocket.modularField.termsAndConditions')
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table><!--/.docket-table-value-->

            @if($emailDocket->attachedTimer()->count()>0)
                @include('website.emailDocket.modularField.attachedTimer')
            @endif
            @if($emailDocket->docketApprovalType == 1 || $emailDocket->docketApprovalType == 0 )
                <br/><br/>
                <div class="row">
                    @if($emailDocket->docketApprovalType==0)
                        <div class="col-md-6">
                            <strong>Approved By:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->status==1)
                                    <p style="padding-top: 8px;">{{ $row->emailUserInfo->email }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                    <div class="clearfix"></div>
                                    <br>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="col-md-6">
                            <strong>Approved By:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->status==1)
                                    <img src="{{ AmazoneBucket::url() }}{{ $row->signature }}" class="d-block" style="width:100px">
                                    <p style="padding-top: 8px;">{{ $row->emailUserInfo->email }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                @endif
                                <div class="clearfix"></div>
                                <br>
                            @endforeach
                        </div>
                    @endif
                    <div class="col-md-6">
                        @if($emailDocket->status==0)
                            <strong>Pending Approval:</strong>
                            @foreach($emailDocket->recipientInfo as $row)
                                @if($row->approval==1 && $row->status==0)
                                    <span class="d-block">{{ $row->emailUserInfo->email }}</span>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>