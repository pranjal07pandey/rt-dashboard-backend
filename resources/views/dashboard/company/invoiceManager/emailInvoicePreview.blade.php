<meta name="format-detection" content="telephone=no">
<style>
    * {
        -webkit-touch-callout: none;
        -webkit-user-select: none; /* Disable selection/copy in UIWebView */
    }

    body { margin: 0; padding: 0; font-family:Helvetica Neue, Helvetica, Arial, sans-serif; }
    .divWrapper{
        padding:10px 10px;
        background: #f9f9f9;
        border: 1px dashed #eaeaea;
        font-size: 12px
    }
    .pageBreak{
        height: 20px;
        width: 100%;
        background: #5B6366;
        display: block;
        padding: 0px -50px;
    }
    table{
        font-size:12px;
    }
    th, td{
        padding:5px 0px 5px 10px;
        text-align: left;
        line-height: 1.5em;
    }
    td span{
        float:right;
        width: calc( 40% - 10px);
        text-align: left;
        display:block;
        padding-left: 10px;
    }

    .docketDetailsTable tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;

    }
    .docketDetailsTable tbody tr td{
        padding: 8px;
        vertical-align: top;
        border-top: 1px solid #ddd;
        font-size: 12px;
    }


    div{
        line-height: 1.5em;
    }
    strong{
        font-size: 12px;
    }
    .docketId{
        padding-left: 10px;
        padding-top: 10px;
        display: block;
    }

</style>

<div class="divWrapper">
    <div style="width:65%;float:left;margin-bottom: 10px;">

        @if(AmazoneBucket::fileExist($invoice->company_logo))
            <img src="{{ AmazoneBucket::url() }}{{ $invoice->company_logo }}" style="height:100px;">
        @else
            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
        @endif
        <br/>
    </div>
    <div style="width:35%;float:left;font-size: 12px;">
        <strong>Tax Invoice</strong><br/>
        <strong>Date</strong>: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y') }}<br/>
        <strong>Invoice</strong>: e-inv {{ $invoice->id }}<br/>
    </div>
    <div style="clear:both"></div>

    <div>
        <span>From</span> : <br/>
        <strong style="text-decoration: dotted">{{ $invoice->senderUserInfo->first_name }} {{ $invoice->senderUserInfo->last_name }}</strong><br/>
        {{ @$invoice->senderCompanyInfo->name }}<br/>
        {{ @$invoice->senderCompanyInfo->address }}<br>
        <b>ABN:</b> {{ @$invoice->senderCompanyInfo->abn }}

        <br/><br/>
    </div>
    <div class="col-md-12"><br/><br/>
        <span>To :</span><br/>
        <strong>{{ $data["full_name"] }}</strong><br/>
        <span class="dotted">{{ $data["company_name"] }}</span><br/>
        <span class="dotted">{{ $data["address"]  }}</span>
        <br/><br/>
    </div>

    <div>
        <table width="100%">
            <thead>
            <tr style="background:#ddd;text-align:left;width:100%;">
                <th width="60%">Description</th>
                <th width="40%">Value/Amount</th>
            </tr>
            </thead>
            <tbody style="background-color: #fff;">
            @if($sentInvoiceValue)
                @foreach($sentInvoiceValue as $item)
                    @if($item["invoice_field_category_id"]!=9 && $item["invoice_field_category_id"]!=12 && $item["invoice_field_category_id"]!=5)
                        <tr>
                            <td colspan="2">
                                <strong>{{ $item["label"] }}</strong><br/>
                                {{ $item["value"] }}
                                <div style="clear:both"></div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @endif
            @if($invoice->isDocketAttached==1)
                @if($invoice->attachedEmailDocketsInfo->count()>0)
                    @foreach($invoice->attachedEmailDocketsInfo as $invoiceDocket)
                        <tr>
                            <td>
                                <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                                <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                                Docket :   {{ $invoiceDocket->docketInfo->formatted_id }}<br/>

                                <?php $invoiceDescriptionQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',1)->get(); ?>
                                @foreach($invoiceDescriptionQuery as $description)
                                    {{  $description->sentEmailDocketValueInfo->docketFieldInfo->label }} : {{ $description->sentDocketValueInfo->value }}<br/>
                                @endforeach
                            </td>
                            <td>
                                <?php
                                $invoiceAmount  =    0;
                                $invoiceAmountQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',2)->get();
                                foreach($invoiceAmountQuery as $amount){
                                    $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                                    $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                                }
                                ?>
                                $ {{ $invoiceAmount }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endif
            @if($invoiceDescription)
                @foreach($invoiceDescription as $item)
                    <tr>
                        <td>
                            {{ $item["description"] }}
                        </td>
                        <td>
                            $ {{ round($item["amount"],2) }}
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        <table width="100%">
            <tr style="background:#ddd;;text-align:left;width:100%;">
                <th width="60%">Sub Total</th>
                @php
                    $totalAmount=0;
                    foreach($invoiceDescription as $item){
                         $totalAmount += $item["amount"];
                    }
                    $test=array();
                    foreach($invoice->attachedEmailDocketsInfo as $invoiceDocket){
                        $invoiceAmount  = 0;
                        $invoiceAmountQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',2)->get();
                        foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                        }
                       $test[] =$invoiceAmount;
                    }
                    $subtotal = array_sum($test) +$totalAmount
                @endphp
                <th width="40%">$ {{ round($subtotal,2) }}</th>
            </tr>
            @if($invoice->gst!= 0)
                <tr style="background:#ddd;;text-align:left;width:100%;">
                    <th width="60%">{{ $invoice->invoiceInfo->gst_label }}</th>
                    <th width="40%">{{ $invoice->gst }} %</th>
                </tr>
            @endif
            <tr style="background:#ddd;;text-align:left;width:100%;">
                <th width="60%">Total</th>
                <th width="40%">
                    $
                    @if($invoice->gst!= 0)
                        {{ round($subtotal + $subtotal*$invoice->gst/100,2) }}
                    @else
                        {{ round($subtotal,2) }}
                    @endif
                </th>
            </tr>

            @if($sentInvoiceValue)
                @php $sn = 1; @endphp
                @foreach($sentInvoiceValue as $item)
                    @if($item["invoice_field_category_id"]==9)
                        <tr style="background:#fff;">
                            <td colspan="2">
                                @if($sn==1) <br/> @endif
                                <strong>{{ $item["label"] }}</strong><br/>
                                <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                                @if($images->count()>0)
                                    <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                        @foreach($images as $signature)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 70px;border: 1px solid #ddd;">
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    No Signature Attached

                                @endif

                                <div style="clear:both;"></div>
                            </td>
                        </tr>
                        @php $sn++; @endphp
                    @endif
                @endforeach
            @endif
        </table>
        @if($invoiceSetting)
            <br/>
            <table>
                <tbody style="background-color: #fff;">
                <tr style="font-weight: bold;">
                    <td colspan="2">
                        Payment Details
                    </td>
                </tr>
                <tr>
                    <td>
                        Bank Name
                    </td>
                    <td>
                        {{ $invoiceSetting->bank_name }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Account Name
                    </td>
                    <td>
                        {{ $invoiceSetting->account_name }}
                    </td>
                </tr>
                <tr>
                    <td>
                        BSB Number
                    </td>
                    <td>
                        {{ $invoiceSetting->bsb_number }}
                    </td>
                </tr>
                <tr>
                    <td>
                        Account Number
                    </td>
                    <td>
                        {{ $invoiceSetting->account_number }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>{{ $invoiceSetting->instruction }}</strong>
                    </td>
                </tr>
                @if($invoiceSetting->additional_information)
                    <tr>
                        <td colspan="2">
                            <strong>{{ $invoiceSetting->additional_information }}</strong>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        @endif

        @if($sentInvoiceValue)
            @php $sn = 1; @endphp
            <table class="table table-striped" width="100%">
                <tbody >
                @foreach($sentInvoiceValue as $item)
                    @if($item["invoice_field_category_id"]==5)
                        <tr>
                            <td colspan="2">
                                @if($sn==1) @endif
                                <strong>{{ $item["label"] }}</strong><br/>
                                <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                                @if($images->count()>0)
                                    <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                        @foreach($images as $image)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $image->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="height: 70px;border: 1px solid #ddd;">
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    No Image Attached

                                @endif
                                <div style="clear:both;"></div>
                            </td>
                        </tr>
                        @php $sn++; @endphp
                    @endif
                    @if($item["invoice_field_category_id"]==12)
                        <tr>
                            <td  colspan="2"> <strong>{{ $item["value"] }}</strong></td>
                        </tr>
                    @endif

                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if($invoice->isDocketAttached==1)
    @if($invoice->attachedEmailDocketsInfo)
        @foreach($invoice->attachedEmailDocketsInfo as $row)
            <div class="pageBreak"></div>
            <div class="docket">
                <strong class="docketId">#e-Doc{{ $row->docketInfo->id }}</strong>
                <div class="divWrapper">
                    <div style="width:65%;float:left;margin-bottom: 10px;">
                        <strong>{{  $row->docketInfo->senderCompanyInfo->name  }}</strong><br/>
                        <span>{{  $row->docketInfo->senderCompanyInfo->address }}</span><br/><br/>

                        <strong>From</strong> : <span style="text-decoration: dotted">
                            {{ $row->docketInfo->senderUserInfo->first_name }} {{ $row->docketInfo->senderUserInfo->last_name }}
                        </span>
                    </div>
                    <div style="width:35%;float:left;font-size: 12px;text-align: right;">
                        <strong>Date</strong>: {{ \Carbon\Carbon::parse($row->docketInfo->created_at)->format('d-M-Y') }}<br/>
                    </div>
                    <div style="clear:both"></div>

                    <div><br/>
                        <strong>To :</strong><br/>
                        <strong>

                            @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp
                            @foreach($row->docketInfo->recipientInfo as $recipient)
                                @php
                                    $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;
                                    $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;

                                    if($row->docketInfo->recipientInfo->count()>1){
                                       if($row->docketInfo->recipientInfo->last()->id!=$recipient->id){
                                            $recipientNames =  $recipientNames.", ";
                                            $recipientCompany = $recipientCompany.", ";
                                        }
                                    }
                                @endphp
                            @endforeach

                            <strong>{{ $recipientNames }}</strong> <br>
                            {{ $recipientCompany }}
                        </strong><br/>
                        {{--<span class="dotted">{{ $row->docketInfo->companyInfo->name }}</span><br/>--}}
                        {{--<span class="dotted">{{ $row->docketInfo->companyInfo->address }}</span>--}}
                        <br/><br/>
                    </div>

                    <div>
                        <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable">
                            <thead>
                            <tr style="background:#ddd;text-align:left;width:100%;">
                                <th width="45%">Description</th>
                                <th width="55%">Value/Amount</th>
                            </tr>
                            </thead>
                            <tbody style="background-color: #fff;">
                            @if($row->docketInfo->sentDocketValue)
                                @foreach($row->docketInfo->sentDocketValue as $item)
                                    @if($item->docketFieldInfo->docket_field_category_id==5 || $item->docketFieldInfo->docket_field_category_id==9 )
                                        <tr>
                                            <td colspan="2">
                                                {{ $item->docketFieldInfo->label }}<br/>
                                                <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                    @foreach($item->sentDocketImageValue as $signature)
                                                        <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                            {{--<a href="{{ asset($signature->value) }}" target="_blank">--}}
                                                            <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                            {{--</a>--}}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @elseif($item->docketFieldInfo->docket_field_category_id==8)
                                        <tr>
                                            <td>
                                                {{ $item->docketFieldInfo->label }}
                                            </td>
                                            <td>
                                                @if($item->value==1)<img src="{{ asset('assets/dashboard/img/checked.png') }}" width="15px">
                                                @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="15px">@endif
                                            </td>
                                        </tr>
                                    @elseif($item->docketFieldInfo->docket_field_category_id==7)
                                        <tr>

                                            <td>
                                                @foreach($item->sentDocketUnitRateValue as $row)
                                                    {{ $row->docketUnitRateInfo->label }}<br/>
                                                @endforeach
                                                <strong>Total</strong>
                                            </td>

                                            <td>
                                                <?php $total    =    0; ?>
                                                @foreach($item->sentDocketUnitRateValue as $row)
                                                    @if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}<br/>
                                                @endforeach
                                                $ <strong>{{  $item->sentDocketUnitRateValue->first()->value*$item->sentDocketUnitRateValue->last()->value }}</strong>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>
                                                {{ $item->docketFieldInfo->label }}
                                            </td>
                                            <td>
                                                {{ $item->value }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><!--/.divWrapper-->
            </div>
        @endforeach
    @endif
@endif