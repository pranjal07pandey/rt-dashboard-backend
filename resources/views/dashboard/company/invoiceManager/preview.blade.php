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
    .pdf {

        list-style-type: none;
        margin: 0;
        padding: 0;
        margin-top: 2px;
    }

    .pdf li {
        display: inline-block;
        font-size: 12px;
        text-align: center;
        padding-right: 15px;

    }
    .pdf li img{
        height: 12px;
        width: 12px;
    }
    .pdf li a{
        padding-left: 5px;
    }

</style>

<div class="divWrapper">
    <div style="width:65%;float:left;margin-bottom: 10px;">

        @if(AmazoneBucket::fileExist($invoice->company_logo))
            <img src="{{ AmazoneBucket::url() }}{{ $invoice->company_logo }}" style="height:150px;">
        @else
            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
        @endif
        <br/>
    </div>
    <div style="width:35%;float:left;font-size: 12px;">
        <strong>Tax Invoice</strong><br/>
        <strong>Date</strong>: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y') }}<br/>
        <strong>Invoice</strong>: {{ $invoice->id }}<br/>
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
                @if($invoice->attachedDocketsInfo->count()>0)
                    @foreach($invoice->attachedDocketsInfo as $invoiceDocket)
                        <tr>
                            <td>
                                <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                                <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                                Docket :  {{ $invoiceDocket->docketInfo->formatted_id }}<br/>

                                <?php $invoiceDescriptionQuery    =    \App\SentDocketInvoice::where('sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',1)->get(); ?>
                                @foreach($invoiceDescriptionQuery as $description)
                                    {{  $description->sentDocketValueInfo->docketFieldInfo->label }} : {{ $description->sentDocketValueInfo->value }}<br/>
                                @endforeach
                            </td>
                            <td>
                                <?php
                                $invoiceAmount  =    0;
                                $invoiceAmountQuery    =    \App\SentDocketInvoice::where('sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',2)->get();
                                foreach($invoiceAmountQuery as $amount){
                                    $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
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
                            $ {{ $item["amount"] }}
                        </td>
                    </tr>
                @endforeach
            @endif
            </tbody>
        </table>
        <table width="100%">
            <tr style="background:#ddd;;text-align:left;width:100%;">
                <th width="60%">Sub Total</th>
                <th width="40%">$ {{ round($invoice->amount,2) }}</th>
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
                        {{ $invoice->amount + $invoice->amount*$invoice->gst/100 }}
                    @else
                        {{ round($invoice->amount,2) }}
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
                                <?php $images   =   \App\SentInvoiceImageValue::where('sent_invoice_value_id',$item["id"])->get(); ?>
                                @if($images->count()>0)
                                    <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                        @foreach($images as $signature)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}"><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 70px;border: 1px solid #ddd;"></a>
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
                                <?php $images   =   \App\SentInvoiceImageValue::where('sent_invoice_value_id',$item["id"])->get(); ?>
                                @if($images->count()>0)
                                    <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                        @foreach($images as $image)
                                            <li style="margin-right:10px;float: left;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $image->value }}"> <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="height: 70px;border: 1px solid #ddd;"></a>
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
    @if($invoice->attachedDocketsInfo)
        @foreach($invoice->attachedDocketsInfo as $row)
            <div class="pageBreak"></div>
            <div class="docket">
                <strong class="docketId">{{ $row->docketInfo->formatted_id }}</strong>
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

                            @if($row->docketInfo->recipientInfo)
                                <?php $sn = 1; ?>
                                @foreach($row->docketInfo->recipientInfo as $recipient)
                                    {{ $recipient->userInfo->first_name }} {{ $recipient->userInfo->last_name }}
                                    @if($sn!=$row->docketInfo->recipientInfo->count())
                                        ,
                                    @endif
                                    <?php $sn++; ?>
                                @endforeach
                            @endif
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
                                    @if($item->docketFieldInfo->docket_field_category_id==5)
                                        <tr>
                                            <td colspan="2">
                                                {{ $item->label }}<br/>
                                                @if($item->sentDocketImageValue->count()>0)
                                                    <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                        @foreach($item->sentDocketImageValue as $image)
                                                            <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                                {{--<a href="{{ asset($image->value) }}" target="_blank">--}}
                                                                <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                                {{--</a>--}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No Image Attached
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif( $item->docketFieldInfo->docket_field_category_id==9 )
                                        <tr>
                                            <td colspan="2">
                                                {{ $item->label }}<br/>
                                                @if($item->sentDocketImageValue->count()>0)
                                                    <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                        @foreach($item->sentDocketImageValue as $signature)
                                                            <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                                {{--<a href="{{ asset($signature->value) }}" target="_blank">--}}
                                                                <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                                {{--</a>--}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No Signature Attached
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif( $item->docketFieldInfo->docket_field_category_id==14)
                                        <tr>
                                            <td colspan="2">
                                                {{ $item->label }}<br/>
                                                @if($item->sentDocketImageValue->count()>0)
                                                    <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                        @foreach($item->sentDocketImageValue as $sketchPad)
                                                            <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                                {{--<a href="{{ asset($sketchPad->value) }}" target="_blank">--}}
                                                                <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                                {{--</a>--}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No Sketch Pad Attached
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif($item->docketFieldInfo->docket_field_category_id==8)
                                        <tr>
                                            <td>
                                                {{ $item->label }}
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
                                    @elseif($item->docketFieldInfo->docket_field_category_id==15)
                                        <tr>
                                            <td> {{ $item->label }}
                                                @if($item->sentDocketAttachment->count()>0)
                                                    <ul class="pdf">
                                                        @foreach($item->sentDocketAttachment as $document)
                                                            <li><img src="{{ asset('assets/pdf.png') }}"><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    No Document Attached
                                                @endif
                                            </td>
                                            <td> {{ $item->value }}</td>
                                        </tr>
                                    @elseif($item->docketFieldInfo->docket_field_category_id==20)
                                        <tr>
                                            <td>{{ $item->label }}</td>
                                            <td>
                                                @foreach($item->sentDocketManualTimer as $rows)
                                                    <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                                @endforeach
                                                <br>
                                                @foreach($item->sentDocketManualTimerBreak as $items)
                                                    <strong>{{ $items->label }} :</strong>  {{ $items->value }}<br>
                                                    <strong>Reason for break :</strong>  {{ $items->reason }}<br>
                                                @endforeach
                                                <strong>Total time :</strong>  {{ $item->value }}<br>

                                            </td>
                                        </tr>



                                    @elseif($item->docketFieldInfo->docket_field_category_id==17)
                                        <tr>
                                            @php
                                                $yesno = unserialize($item->label);
                                            @endphp
                                            <td>{{ $yesno[0]}}</td>
                                            <td>{{ $yesno[1][$item->value]}}</td>
                                            <table style=" margin-left: 11px;width: 98%;" class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th colspan="2"><h5 style="   margin-top: -23px;margin-bottom: 0px;font-size: 15px;color: #929292;margin-left: -9px;">Explanation</h5></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($item->SentDocValYesNoValueInfo as $items)
                                                    @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                        @php
                                                            $imageData=unserialize($items->value);
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $items->label }}</td>
                                                            <td>
                                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                    @if(empty($imageData))
                                                                        <b>No Image Attached</b>
                                                                    @else
                                                                        @foreach($imageData as $rowData)
                                                                            <li style="margin-right:10px;float: left;">
                                                                                <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank">
                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" style="height: 100px;">
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    @endif
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                    @if($items->YesNoDocketsField->docket_field_category_id==1)
                                                        <tr>
                                                            <td> {{ $items->label }}</td>
                                                            <td>{{$items->value }}</td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                                </tbody>
                                            </table>

                                        </tr>
                                    @elseif($item->docketFieldInfo->docket_field_category_id==12)
                                        <tr>
                                            <td  colspan="2"> <strong>{{ $item->label }}</strong></td>
                                        </tr>

                                    @elseif($item->docketFieldInfo->docket_field_category_id==13)
                                        @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
                                    @elseif($item->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=17)
                                        <tr>
                                            <td> {{ $item->label }}</td>
                                            <td> {{ $item->value }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(@$footerValue)
                                    <tr>
                                        <td  colspan="2"> <strong>{{ $footerLabel }}</strong><br>
                                            {{ $footerValue }}
                                        </td>
                                    </tr>
                                @endif
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