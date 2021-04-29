@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Invoice Manager
            <small>Add/View Invoice</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Invoice Manager</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
                <div class="col-md-6 text-left">
                    <a  class="btn btn-default btn-sm" href="{{ url()->previous() }}" style="margin:0px;"><i class="fa fa-reply"></i> Back</a>
                </div>
                <div class="col-md-6 text-right">
                    <button  class="btn btn-default btn-sm"  onclick="location.href='{{url('dashboard/company/invoiceManager/invoice/downloadViewInvoice/'.$sentInvoice->id)}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download"></i> Download</button>
                    <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div class="container1" id="printContainer">
                <header class="clearfix headerInvoice">
                    <div id="logo">
                        @if(AmazoneBucket::fileExist(@$sentInvoice->senderCompanyInfo->logo))
                            <img src="{{ AmazoneBucket::url() }}{{ @$sentInvoice->senderCompanyInfo->logo }}" style="height: 80px;">
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                        @endif
                    </div>
                    <div id="company">
                        <h2 class="name">{{ @$sentInvoice->senderUserInfo->first_name }} {{ @$sentInvoice->senderUserInfo->last_name }}</h2>
                        <div>{{ @$sentInvoice->senderCompanyInfo->name }}</div> 
                        <div class="address">{{ @$sentInvoice->senderCompanyInfo->address }}</div>
                        <div><b>ABN:</b> {{ @$sentInvoice->senderCompanyInfo->abn }}</div>
                    </div>
                </header>
                <div class="container1">
                  <main>
                        <div id="details" class="clearfix">
                            <div id="client">
                                <div class="to">INVOICE TO:</div>
                                <h2 class="name">{{ $sentInvoice->receiverUserInfo->first_name }} {{ $sentInvoice->receiverUserInfo->last_name }}</h2>
                                <div > {{ @$sentInvoice->receiverCompanyInfo->name }}</div>
                                <div class="address">{{ @$sentInvoice->receiverCompanyInfo->address }}</div>
                            </div>
                            <div id="invoice">
                                <h1>Tax Invoice</h1>
                                <div class="date">Date of Invoice: {{ \Carbon\Carbon::parse($sentInvoice->created_at)->format('d-M-Y') }}</div>
                                <div class="date">Invoice ID: {{ $sentInvoice->id }}</div>
                            </div>
                        </div>
                        <table border="0" cellspacing="0" cellpadding="0">
                            <thead>
                            <tr>
                                <th class="no" width="10px">#</th>
                                <th class="desc" style="    background: #DDDDDD;">DESCRIPTION</th>
                                <th class="total" width="100px">VALUE/AMOUNT</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?php $i=1 ?>
                            @if($sentInvoiceValue)
                                @foreach($sentInvoiceValue as $item)
                                    @if($item["invoice_field_category_id"]!=9 && $item["invoice_field_category_id"]!=12 && $item["invoice_field_category_id"]!=5)
                                        <tr>
                                            <td class="no" ><?php echo $i++ ?></td>
                                            <td class="desc"><h3><strong>{{ $item["label"] }}</strong></h3>{{ $item["value"] }}</td>
                                            <td class="total"></td>
                                        </tr>

                                    @endif
                                @endforeach
                            @endif
                            @if($sentInvoice->isDocketAttached==1)
                                @if($sentInvoice->attachedDocketsInfo->count()>0)
                                    @foreach($sentInvoice->attachedDocketsInfo as $invoiceDocket)

                                    <tr>
                                        <td class="no" ><?php echo $i++ ?></td>
                                        <td class="desc">
                                            <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                                            <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                                            Docket :  #Doc{{ $invoiceDocket->docketInfo->id }}<br/>
                                            <?php $invoiceDescriptionQuery    =    \App\SentDocketInvoice::where('sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',1)->get(); ?>
                                            @foreach($invoiceDescriptionQuery as $description)
                                                {{  $description->sentDocketValueInfo->docketFieldInfo->label }} : {{ $description->sentDocketValueInfo->value }}<br/>
                                            @endforeach
                                        </td>
                                        <td class="total">
                                            <?php
                                            $invoiceAmount  =    0;
                                            $invoiceAmountQuery    =    \App\SentDocketInvoice::where('sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',2)->get();
                                            foreach($invoiceAmountQuery as $amount){
                                                $unitRate    =  $amount->sentDocketValueInfo->sentDocketUnitRateValue->toArray();
                                                $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                                            }
                                            ?>
                                            $ {{ $invoiceAmount}}
                                        </td>
                                    </tr>


                                    @endforeach
                                @endif
                              @endif
                            </tbody>
                           <tfoot>
                              <tr>
                                <td colspan="2">SUBTOTAL</td>
                                <td>$ {{ round($sentInvoice->amount,2) }}</td>
                              </tr>
                              <tr>
                                <td colspan="2">{{ $sentInvoice->invoiceInfo->gst_label }} {{ $sentInvoice->gst }}%</td>
                                <td>$ {{ round($sentInvoice->amount*$sentInvoice->gst/100,2)  }}</td>
                              </tr>
                              <tr>
                                <td colspan="2">GRAND TOTAL</td>
                                <td>
                                  @if($sentInvoice->gst!= 0)
                                    $ {{ $sentInvoice->amount + $sentInvoice->amount*$sentInvoice->gst/100 }}
                                  @else
                                    $ {{ round($sentInvoice->amount,2) }}
                                  @endif
                                </td>
                              </tr>
                            </tfoot>
                        </table>

                        @if($sentInvoiceValue)
                            @php $sn = 1; @endphp
                            @foreach($sentInvoiceValue as $item)
                                @if($item["invoice_field_category_id"]==9)
                                    <tr>
                                        <td colspan="2">
                                            @if($sn==1) <br/> @endif
                                            <strong>{{ $item["label"] }}</strong><br/>
                                            <?php $images   =   \App\SentInvoiceImageValue::where('sent_invoice_value_id',$item['id'])->get(); ?>
                                            @if($images=="")
                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                    @foreach($images as $signature)
                                                        <li style="margin-right:10px;float: left;">
                                                            <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                                <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                No Signature Attached
                                            @endif


                                        </td>
                                    </tr>
                                    @php $sn++; @endphp
                                @endif
                            @endforeach
                        @endif
                        @if($invoiceSetting)
                            <div id="next-docket">
                                <table border="0" cellspacing="0" cellpadding="0">
                                    <thead>
                                    <th ><h2>Payment Details</h2></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="desc">Bank Name</td>
                                        <td class="desc">{{ $invoiceSetting->bank_name }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="desc">Account Name</td>
                                        <td class="desc">{{ $invoiceSetting->account_name }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="desc">BSB Number</td>
                                        <td class="desc">{{ $invoiceSetting->bsb_number }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="desc">Account Number</td>
                                        <td class="desc">{{ $invoiceSetting->account_number }}</td>
                                        <td></td>
                                        <td></td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                        @endif
                      <?php $i++ ?>


                      <div id="notices " class="information">
                            <div class="notice">{{ @$invoiceSetting->instruction }}</div>
                        </div>
                        @if(@$invoiceSetting->additional_information)
                            <div id="notices" class="information">
                                <div><h4>{{ $invoiceSetting->additional_information }}</h4></div>
                            </div>
                        @endif
                        @if($sentInvoiceValue)
                            @php $sn = 1; @endphp
                            <table  class="table table-striped">

                                @foreach($sentInvoiceValue as $item)
                                    @if($item["invoice_field_category_id"]==5)
                                        <tr>
                                            <td colspan="2">
                                                @if($sn==1)  @endif
                                                <strong>{{ $item["label"] }}</strong><br/>
                                                <?php $images   =   \App\SentInvoiceImageValue::where('sent_invoice_value_id',$item["id"])->get(); ?>
                                                @if($images=="")
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
                            </table>
                        @endif
                  </main>
                  @if( $sentInvoice->isDocketAttached==1)
                    @if($sentInvoice->attachedDocketsInfo)
                        @foreach($sentInvoice->attachedDocketsInfo as $row)
                            <main class="doc-part">
                                <div class="header">
                                    <div class="float-left"><b>#Doc{{ $row->docketInfo->id }}</b></div>
                                    <div class="float-right"><b>Date :   {{ \Carbon\Carbon::parse($row->docketInfo->created_at)->format('d-M-Y') }}</b></div>
                                </div>
                                <header class="clearfix headerInvoice">
                                    <div id="company">
                                        <h2 class="name">{{  $row->docketInfo->senderCompanyInfo->name  }}</h2>
                                        <div>{{  $row->docketInfo->senderCompanyInfo->address }}</div>
                                    </div>
                                </header>
                                <div id="details" class="clearfix">
                                    <div id="client">
                                        <div class="to">TO:</div>

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
                                        </strong>
                                    </div>
                                    <div id="invoice">
                                        <div class="to">FROM:</div>
                                        <h2 class="name">{{ $row->docketInfo->senderUserInfo->first_name }} {{ $row->docketInfo->senderUserInfo->last_name }}</h2>
                                    </div>
                                </div>
                                <div id="next-docket">
                                    <table border="0" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <th><h2>Description</h2></th>
                                            <th><h2>Value/Amount</h2></th>
                                        </thead>
                                        <tbody>
                                            @if($row->docketInfo->sentDocketValue)
                                                @foreach($row->docketInfo->sentDocketValue as $item)
                                                    @if($item->docketFieldInfo->docket_field_category_id==5 || $item->docketFieldInfo->docket_field_category_id==9 )
                                                        <tr>
                                                            <td class="desc" colspan="2">
                                                                {{ $item->label }}<br/>
                                                                <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                                    @foreach($item->sentDocketImageValue as $signature)
                                                                        <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==8)
                                                        <tr>
                                                            <td class="desc">
                                                                {{ $item->label }}
                                                            </td>
                                                            <td class="desc">
                                                                @if($item->value==1)<img src="{{ asset('assets/dashboard/img/checked.png') }}" width="15px">
                                                                @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="15px">@endif
                                                            </td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==15)
                                                        <tr>
                                                            <td class="desc"> {{ $item->label }}
                                                                <ul class="pdf">
                                                                    @foreach($item->sentDocketAttachment as $document)
                                                                        <li><img src="{{ asset('assets/pdf.png') }}"><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            <td class="desc"> {{ $item->value }}</td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==7)
                                                        <tr>

                                                            <td class="desc">
                                                                @foreach($item->sentDocketUnitRateValue as $row)
                                                                    {{ $row->docketUnitRateInfo->label }}<br/>
                                                                @endforeach
                                                                <strong>Total</strong>
                                                            </td>

                                                            <td class="desc">
                                                                <?php $total    =    0; ?>
                                                                @foreach($item->sentDocketUnitRateValue as $row)
                                                                    @if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}<br/>
                                                                @endforeach
                                                                $ <strong>{{  $item->sentDocketUnitRateValue->first()->value*$item->sentDocketUnitRateValue->last()->value }}</strong>
                                                            </td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==12)
                                                        <tr>
                                                            <td  class="desc" colspan="2"> <strong>{{ $item->label }}</strong></td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==13)
                                                        @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
                                                    @elseif($item->docketFieldInfo->docket_field_category_id!=13)
                                                        <tr>
                                                            <td class="desc"> {{ $item->label }}</td>
                                                            <td  class="desc"> {{ $item->value }}</td>
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
                            </main>
                        @endforeach
                    @endif
                  @endif
                </div>
                <footer>
                  Invoice was created on Recordtime.
                </footer>
            </div>
        </div>
    </div>
<style type="text/css">
    .container1{
        position: relative;
        width: 100%;
        min-height: 1500px;
        margin: 0 auto;
        color: #555555;
        background: #FFFFFF;]
        font-size: 14px;
    }

    .clearfix:after {
        content: "";
        display: table;
        clear: both;
    }

    a {
        color: #0087C3;
        text-decoration: none;
    }

    .headerInvoice {
        padding: 20px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
    }

    #logo {
        float: left;
        margin-top: 0px;
    }

    #logo img {
        height: 70px;
    }

    #company {
        float: right;
        text-align: right;
    }


    #details {
        margin-bottom: 50px;
    }

    #client {
        padding-left: 6px;
        border-left: 6px solid #0087C3;
        float: left;
    }

    #client .to {
        color: #777777;
    }

    h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
    }

    #invoice {
        float: right;
        text-align: right;
    }

    #invoice h1 {
        color: #0087C3;
        font-size: 2.4em;
        line-height: 1em;
        font-weight: normal;
        margin: 0  0 10px 0;
    }

    #invoice .date {
        font-size: 1.1em;
        color: #777777;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }

    table th,
    table td {
        padding: 20px;
        background: #EEEEEE;
        text-align: center;
        border-bottom: 1px solid #FFFFFF;
    }

    table th {
        white-space: nowrap;
        font-weight: normal;
    }

    table td {
        text-align: right;
    }

    table td h3{
        color: #57B223;
        font-size: 1.2em;
        font-weight: normal;
        margin: 0 0 0.2em 0;
    }

    table .no {
        color: #FFFFFF;
        font-size: 1.6em;
        background: #217e8c;
    }

    table .desc {
        text-align: left;
    }

    table .unit {
        background: #DDDDDD;
    }

    table .qty {
    }

    table .total {
        background: #217e8c;
        color: #FFFFFF;
    }

    table td.unit,
    table td.qty,
    table td.total {
        font-size: 1.2em;
    }

    table tbody tr:last-child td {
        border: none;
    }

    table tfoot td {
        padding: 10px 20px;
        background: #FFFFFF;
        border-bottom: none;
        font-size: 1.2em;
        white-space: nowrap;
        border-top: 1px solid #AAAAAA;
    }

    table tfoot tr:first-child td {
        border-top: none;
    }

    table tfoot tr:last-child td {
        color: #57B223;
        font-size: 1.4em;
        border-top: 1px solid #57B223;

    }

    table tfoot tr td:first-child {
        border: none;
    }

    #thanks{
        font-size: 2em;
        margin-bottom: 50px;
    }

    #notices{
        padding-left: 6px;
        border-left: 6px solid #0087C3;
    }

    #notices .notice {
        font-size: 1.2em;
    }

    footer {
        color: #777777;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 0;
        border-top: 1px solid #AAAAAA;
        padding: 8px 0;
        text-align: center;
    }
    #next-docket{
        margin-top: 30px;
        clear: both;

    }
    #next-docket thead h2{
        font-size: 24px;
        text-transform: uppercase;
        text-align: left;
    }
    .list{
        margin-top: 20px;
    }
    #images{
        margin-top: 40px;
        width: 100%;
        /*min-height: 300px;*/
        clear: both;
    }
    .list-unstyled{
        list-style: circle;
    }
    .list-inline{
        display: inline-block;
    }
    .img-box-titiled{
        width: 100%;
    }
    .img-box-titiled ul{
        list-style: none;
        margin-left: 0px;
        padding-left: 0px;
    }
    .img-box-titiled ul li{
        float: left;
        margin-right: 20px;
    }
    .box-img-thubmail img{
        height: 100px;
    }
    .doc-part{
        margin-top: 60px;
        /*min-height: 800px;*/
    }
    .header{
        background: #217e8c;
        min-height: 47px;
        padding: 15px 10px;
        color: #fff;
        font-size: 17px;
    }
    #notices-1{
        margin-bottom: 30px;
        min-height: 150px;
    }
    .signature{
        margin-bottom: 10px;
        padding-left: 10px;
    }
    .signature-doct{
        padding-left: 10px;
    }
    #next-docket{
        margin-bottom: 50px;
        display: block;
    }
    .information{
        min-height: 80px;
        margin-bottom: 20px;
        display: block;
    }
    .float-left{
        float: left;
    }
    .float-right{
        float: right;
    }

</style>
@endsection

@section('customScript')
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
    <script>
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
        })
    </script>
@endsection

@section('customScript')
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
    <script>
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
        })
    </script>
@endsection