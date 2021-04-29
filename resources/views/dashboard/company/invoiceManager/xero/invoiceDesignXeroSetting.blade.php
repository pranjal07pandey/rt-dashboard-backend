<input type="hidden" value="1" class="xeroStatus">
@foreach($xeroField as $item)
    {{--@if($item->id ==1)--}}
        {{--<div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">--}}
            {{--<h5 style="margin-top: 20px;">{{$item->title}}</h5>--}}
            {{--<select id="accountTypess" name="1" class="form-control">--}}
                {{--<option value="ACCREC">A sales invoice - commonly known as an Accounts Receivable or customer invoice</option>--}}
                {{--<option value="ACCPAY">A bill - commonly known as a Accounts Payable or supplier invoice</option>--}}
            {{--</select>--}}
        {{--</div>--}}
    {{--@endif--}}
    @if($item->id ==2)
        <div class="form-group col-md-12" style="margin: -14px 0px 16px -27px;">
            <h5 style="margin-top: 20px;">{{$item->title}}</h5>
            <select id="line" name="2" class="form-control">
                <option value="Exclusive">Exclusive</option>
                <option value="Inclusive">Inclusive</option>
                <option value="NoTax">NoTax</option>
            </select>
        </div>
    @endif
    @if($item->id ==3)
        <div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">
            <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">{{$item->title}}</label>
            <div class="gorm-group is-empty">
                <input type="number" name="3" class="form-control" palceholder="Enter number of days " min="1"  value="1">
            </div>
        </div>
    @endif
    @if($item->id ==4)
        <?php
        $test = $account;
        ?>
        <div class="form-group col-md-6" style="margin: -23px 0px 16px -27px;">
            <h5 style="margin-top: 20px;">{{$item->title}}</h5>
            <select name="4" class="form-control">
                @php $tempType = ""; @endphp

                @foreach($test as $accounts)
                    @if($test[0]->code==$accounts->code)
                        <optgroup label="{{$accounts->Class}}">
                            @elseif($tempType!=$accounts->Class)
                        </optgroup>
                        <optgroup label="{{$accounts->Class}}">
                            @endif
                            <option value="{{$accounts->Code}}-{{$accounts->Name}}">{{$accounts->Name}}</option>
                            @php $tempType = $accounts->Class; @endphp

                            @endforeach
                        </optgroup>
            </select>
        </div>
    @endif
    @if($item->id ==5)
        <input type="hidden" value="NONE-0-Tax Exempt" id="hiddenTaxrate" disabled name="5">

        <div class="form-group col-md-6" style="margin: -23px 14px 20px -27px">
            <h5 style="margin-top: 20px;">{{$item->title}}</h5>
            <select id="tax" name="5" class="form-control">
                <?php
                $taxRate = $taxRates;
                ?>
                @foreach($taxRate as $row)
                    <option value="{{$row->TaxType}}-{{$row->DisplayTaxRate}}-{{$row->Name}}"  data-tag='{{$row->TaxType}}-{{$row->DisplayTaxRate}}-{{$row->Name}}'>{{$row->Name}}</option>
                @endforeach
            </select>
        </div>
    @endif
    @if($item->id ==6)
        <div class="form-group col-md-6" style="margin: -14px 14px 16px -27px;">
            <label class="control-label" for="title" style="font-weight: 600;color: #464545;font-size: 13px;">{{$item->title}}</label>
            <div class="gorm-group is-empty">
                <input type="text" name="6" class="form-control" id="discountRate" value="0">
            </div>
        </div>
    @endif

@endforeach
<div class="col-md-12" style="    margin-left: -30px;">
    <h5 style="margin-top: -9px;"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp; Are you syncing upon sending Invoice </h5>

    <ul style="list-style: none !important;    padding: 8px 0px 0px 19px;">
        <li style="float: left;    margin-right: 28px;">
            <p>
                <input style="position: absolute;left: -9999px;" type="checkbox" name="xero_syn_invoice"   value="1" checked id="sendingyes" >
                <label for="sendingyes">
                    Yes
                </label>
            </p>
        </li>
        <li style="  float: left;">
            <p>
                <input style="position: absolute;left: -9999px;" type="checkbox" name="xero_syn_invoice" value="0" id="sendingno" >
                <label for="sendingno">
                    No
                </label>
            </p>
        </li>
    </ul>
</div>