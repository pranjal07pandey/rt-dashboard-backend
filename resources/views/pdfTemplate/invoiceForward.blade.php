<style>
    table>thead>tr>th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd;
        background: #ddd;
        padding: 13px;
    }
    table>tbody>tr>td, table>tbody>tr>th, table>tfoot>tr>td, table>tfoot>tr>th, table>thead>tr>td, table>thead>tr>th {

    }
    table>tbody>tr>td, table>tbody>tr>th, table>tfoot>tr>td, table>tfoot>tr>th, table>thead>tr>td, table>thead>tr>th {
        padding: 13px;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }
    table>tbody>tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    td {
        vertical-align: top;
    }
</style>
<div  style="background: #fff;margin: 0px;min-height: 400px;font-size:20px;line-height:1.6em">

    <div style="width:50%;float:left;">
        @if(AmazoneBucket::fileExist(@$sentInvoice->company_logo))
            <img src="{{ AmazoneBucket::url() }}{{ @$sentInvoice->company_logo }}" style="height:150px;">
        @else
            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
        @endif
        <br/>
        From:<br/>
        <strong>{{ @$sentInvoice->senderUserInfo->first_name }} {{ @$sentInvoice->senderUserInfo->last_name }}</strong><br>
        {{ @$sentInvoice->senderCompanyInfo->name }}<br/>
        {{ @$sentInvoice->senderCompanyInfo->address }}<br>
        <b>ABN:</b> {{ @$sentInvoice->senderCompanyInfo->abn }}
        <br/><br/>
        To:<br/>
        <strong>{{ $sentInvoice->receiverUserInfo->first_name }} {{ $sentInvoice->receiverUserInfo->last_name }}</strong> <br>
        {{ @$sentInvoice->receiverCompanyInfo->name }}<br/>
        {{ @$sentInvoice->receiverCompanyInfo->address }}
    </div>


    <div style="float:right;width:200px;">
        <strong>Tax Invoice</strong><br/>
        <b>Date:</b> {{ \Carbon\Carbon::parse($sentInvoice->created_at)->format('d-M-Y') }}<br/>
        <b>Invoice ID:</b> {{ $sentInvoice->formatted_id }}<br>
    </div>
    <div style="clear:both"></div>

    <div style="padding-top:20px;">
        <table width="100%">
            <thead>
            <tr>
                <th width="50%">Description</th>
                <th width="50%">Value/Amount</th>
            </tr>
            </thead>
            <tbody>
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
            @if($sentInvoice->isDocketAttached==1)
                @if($sentInvoice->attachedDocketsInfo->count()>0)
                    @foreach($sentInvoice->attachedDocketsInfo as $invoiceDocket)
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
            <tfoot>
            <tr style="background: #eee;">
                <th >Sub Total</th>
                <th>$ {{ round($sentInvoice->amount,2) }}</th>
            </tr>
            @if($sentInvoice->gst!= 0)
                <tr  style="background: #eee;">
                    <th>{{ $sentInvoice->invoiceInfo->gst_label }}</th>
                    <th>{{ $sentInvoice->gst }} %</th>
                </tr>
            @endif
            <tr  style="background: #eee;">
                <th>Total</th>
                <th>
                    $
                    @if($sentInvoice->gst!= 0)
                        {{ $sentInvoice->amount + $sentInvoice->amount*$sentInvoice->gst/100 }}
                    @else
                        {{ round($sentInvoice->amount,2) }}
                    @endif
                </th>
            </tr>

            @if($sentInvoiceValue)
                @php $sn = 1; @endphp
                @foreach($sentInvoiceValue as $item)
                    @if($item["invoice_field_category_id"]==9)
                        <tr style="background:#fff;">
                            <td colspan="2"> <strong>{{ $item["label"] }}</strong><br/>
                                <?php $images   =   \App\SentInvoiceImageValue::where('sent_invoice_value_id',$item["id"])->get(); ?>
                                @if($images->count()>0)
                                    <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                        @foreach($images as $signature)
                                            <li style="margin-right:10px;float: left;">
                                                <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 70px;border: 1px solid #ddd;">
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
            </tfoot>
        </table>

        @if($invoiceSetting)
            <br/>
            <table  width="100%">
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
                                                <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="height: 120px;border: 1px solid #ddd;">
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