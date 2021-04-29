<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Record Time</title>
    <style>
        /* -------------------------------------
            GLOBAL RESETS
        ------------------------------------- */
        img {
            border: none;
            -ms-interpolation-mode: bicubic;
            max-width: 100%; }
        body {
            background-color: #f6f6f6;
            font-family: sans-serif;
            -webkit-font-smoothing: antialiased;
            font-size: 14px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%; }
        table {
            border-collapse: separate;
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            width: 100%; }
        table td {
            font-family: sans-serif;
            font-size: 14px;
            vertical-align: top; }
        table thead{
            background: #eee;
        }
        table thead th{
            padding: 10px;
        }
        table td{
            padding: 10px;
        }

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .table-striped>tbody>tr>td,.table-striped>thead>tr>th{
            padding: 8px;
            line-height: 1.6;
            vertical-align: top;
            border-top: 1px solid #ddd;
        }
        .table-striped>thead>tr>th{
            border-bottom: 2px solid #ddd;
        }


        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */
        .body {
            background-color: #f6f6f6;
            width: 100%; }
        /* Set a max-width, and make it display as block so it will automatically stretch to that width, but will also shrink down on a phone or something */
        .container {
            display: block;
            Margin: 0 auto !important;
            /* makes it centered */
            max-width: 580px;
            padding: 10px;
            width: 580px; }
        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            Margin: 0 auto;
            max-width: 580px;
            padding: 10px;
            background: #fff }
        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #fff;
            border-radius: 3px;
            width: 100%; }
        .wrapper {
            box-sizing: border-box;
            padding: 20px 0px;
        }
        .footer {
            clear: both;
            padding-top: 10px;
            text-align: center;
            width: 100%; }
        .footer td,
        .footer p,
        .footer span,
        .footer a {
            color: #999999;
            font-size: 12px;
            text-align: center; }
        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1,
        h2,
        h3,
        h4 {
            color: #000000;
            font-family: sans-serif;
            font-weight: 400;
            line-height: 1.4;
            margin: 0;
            Margin-bottom: 15px; }
        h1 {
            font-size: 35px;
            font-weight: 300;
            text-align: center;
            text-transform: capitalize; }
        p,
        ul,
        ol {
            font-family: sans-serif;
            font-size: 14px;
            font-weight: normal;
            margin: 0;
            Margin-bottom: 15px; }
        p li,
        ul li,
        ol li {
            list-style-position: inside;
            margin-left: 5px; }
        a {
            color: #3498db;
            text-decoration: underline; }
        /* -------------------------------------
            BUTTONS
        ------------------------------------- */
        .btn {
            box-sizing: border-box;
            width: 100%; }
        .btn > tbody > tr > td {
            padding-bottom: 15px; }
        .btn table {
            width: auto; }
        .btn table td {
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center; }
        .btn a {
            background-color: #ffffff;
            border: solid 1px #3498db;
            border-radius: 5px;
            box-sizing: border-box;
            color: #3498db;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 0;
            padding: 12px 25px;
            text-decoration: none;
            text-transform: capitalize; }
        .btn-primary table td {
            background-color: #3498db; }
        .btn-primary a {
            background-color: #3498db;
            border-color: #3498db;
            color: #ffffff; }
        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0; }
        .first {
            margin-top: 0; }
        .align-center {
            text-align: center; }
        .align-right {
            text-align: right; }
        .align-left {
            text-align: left; }
        .clear {
            clear: both; }
        .mt0 {
            margin-top: 0; }
        .mb0 {
            margin-bottom: 0; }
        .preheader {
            color: transparent;
            display: none;
            height: 0;
            max-height: 0;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            mso-hide: all;
            visibility: hidden;
            width: 0; }
        .powered-by a {
            text-decoration: none; }
        hr {
            border: 0;
            border-bottom: 1px solid #f6f6f6;
            Margin: 20px 0; }
        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important; }
            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important; }
            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important; }
            table[class=body] .content {
                padding: 0 !important; }
            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important; }
            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important; }
            table[class=body] .btn table {
                width: 100% !important; }
            table[class=body] .btn a {
                width: 100% !important; }
            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important; }}
        /* -------------------------------------
            PRESERVE THESE STYLES IN THE HEAD
        ------------------------------------- */
        @media all {
            .ExternalClass {
                width: 100%; }
            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%; }
            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important; }
            .btn-primary table td:hover {
                background-color: #34495e !important; }
            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important; } }
    </style>
</head>
<body class="">
<table border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <table>
                    <tr>
                        <td style="line-height:1.5">
                            @if(AmazoneBucket::fileExist(@$sentInvoice->company_logo))
                                <img src="{{ AmazoneBucket::url() }}{{ @$sentInvoice->company_logo }}" style="height:150px;">
                            @else
                                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                            @endif<br/><br/>
                            From:<br>
                            <strong>{{ @$sentInvoice->senderUserInfo->first_name }} {{ @$sentInvoice->senderUserInfo->last_name }}</strong><br>
                            {{ @$sentInvoice->senderCompanyInfo->name }}<br/>
                            {{ @$sentInvoice->senderCompanyInfo->address }}<br/>
                            <strong>ABN</strong>: {{ @$sentInvoice->senderCompanyInfo->abn }}
                            <br><br>
                            To:<br>
                            <strong>{{ $sentInvoice->receiverInfo->email }}</strong> <br>
                            @if($sentInvoice->receiver_full_name!="") <strong>{{ $sentInvoice->receiver_full_name }}</strong> <br/>@endif
                            @if($sentInvoice->receiver_company_name!="") {{ $sentInvoice->receiver_company_name }} <br/>{{ $sentInvoice->receiver_company_address }} @endif
                        </td>
                        <td>
                            <div style="float:right;width: 150px;text-align: left">
                                <strong>Tax Invoice</strong><br/>
                                <b>Date:</b> {{ \Carbon\Carbon::parse($sentInvoice->created_at)->format('d-M-Y') }}<br/>
                                <b>Invoice ID:</b> e-inv {{ $sentInvoice->id }}<br>
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- START CENTERED WHITE CONTAINER -->
                <span class="preheader"></span>
                <table class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table border="0" >
                                <thead>
                                <tr style="background:#ddd;text-align:left;">
                                    <th>Description</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($sentInvoiceValue)
                                    @foreach($sentInvoiceValue as $item)
                                        @if($item["invoice_field_category_id"]!=9 && $item["invoice_field_category_id"]!=12 && $item["invoice_field_category_id"]!=5)
                                            <tr>
                                                <td style="border-top: 2px solid #ddd;">
                                                    <strong> {{ strip_tags($item["label"]) }}</strong><br/>
                                                    {{ $item["value"] }}
                                                </td>
                                                <td style="border-top: 2px solid #ddd;">

                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                                @if($sentInvoice->isDocketAttached==1)
                                    @if($sentInvoice->attachedEmailDocketsInfo->count()>0)
                                        @foreach($sentInvoice->attachedEmailDocketsInfo as $invoiceDocket)
                                            <tr>
                                                <td>
                                                    <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                                                    <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                                                    Docket :   #e-Doc{{ $invoiceDocket->docketInfo->id }}<br/>

                                                    <?php $invoiceDescriptionQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',1)->get(); ?>
                                                    @foreach($invoiceDescriptionQuery as $description)
                                                        {{  $description->sentEmailDocketValueInfo->docketFieldInfo->label }} : {{ $description->sentEmailDocketValueInfo->value }}<br/>
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
                                            <td style="border-top: 2px solid #ddd;">
                                                {{ $item["description"] }}
                                            </td>
                                            <td style="border-top: 2px solid #ddd;">
                                                $ {{ $item["amount"] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif

                                <tr style="background:#ddd;text-align:left;width:100%;">
                                    <td width="60%">Sub Total</td>
                                    @php
                                        $totalAmount=0;
                                        foreach($invoiceDescription as $item){
                                             $totalAmount += $item["amount"];
                                        }
                                        $test=array();
                                        foreach($sentInvoice->attachedEmailDocketsInfo as $invoiceDocket){
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
                                    <td width="40%">$ {{ round($totalAmount->amount,2) }}</td>
                                </tr>
                                @if($sentInvoice->gst!= 0)
                                    <tr style="background:#ddd;text-align:left;width:100%;">
                                        <td width="60%">{{ $sentInvoice->invoiceInfo->gst_label }}</td>
                                        <td width="40%">{{ $sentInvoice->gst }} %</td>
                                    </tr>
                                @endif
                                <tr style="background:#ddd;;text-align:left;width:100%;">
                                    <td width="60%">Total</td>
                                    <td width="40%">
                                        $
                                        @if($sentInvoice->gst!= 0)
                                            {{ $totalAmount + $totalAmount*$sentInvoice->gst/100 }}
                                        @else
                                            {{ round($totalAmount,2) }}
                                        @endif
                                    </td>
                                </tr>

                                @if($sentInvoiceValue)
                                    @php $sn = 1; @endphp
                                    @foreach($sentInvoiceValue as $item)
                                        @if($item["invoice_field_category_id"]==9)
                                            <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                                            @if(count($images)>0)
                                                <tr>
                                                    <td style="padding:0px;">
                                                        @if($sn==1) <br/> @endif
                                                        <strong>{{ $item["label"] }}</strong>
                                                    </td>
                                                    <td></td>
                                                </tr>
                                                <tr style="background:#fff;">
                                                    <td colspan="2" style="padding:0px;">
                                                        @if($images=="")
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
                                            @endif


                                            @php $sn++; @endphp
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                @if($invoiceSetting)
                    <br/>
                    <strong style="border-top: 2px solid #ddd;display: block;padding:10px;">Payment Details</strong>
                    <table>
                        <tbody style="background-color: #fff;">
                        <tr>
                            <td style="border-top: 2px solid #ddd;">
                                Bank Name
                            </td>
                            <td style="border-top: 2px solid #ddd;">
                                {{ $invoiceSetting->bank_name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 2px solid #ddd;">
                                Account Name
                            </td>
                            <td style="border-top: 2px solid #ddd;">
                                {{ $invoiceSetting->account_name }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 2px solid #ddd;">
                                BSB Number
                            </td>
                            <td style="border-top: 2px solid #ddd;">
                                {{ $invoiceSetting->bsb_number }}
                            </td>
                        </tr>
                        <tr>
                            <td style="border-top: 2px solid #ddd;">
                                Account Number
                            </td>
                            <td style="border-top: 2px solid #ddd;">
                                {{ $invoiceSetting->account_number }}
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <strong style="border-top: 2px solid #ddd;display: block;padding:10px;">{{ $invoiceSetting->instruction }}</strong>
                    @if($invoiceSetting->additional_information)
                        <strong style="border-top: 2px solid #ddd;display: block;padding:10px;">{{ $invoiceSetting->additional_information }}</strong>
                    @endif
                @endif
                @if($sentInvoiceValue)
                    @php $sn = 1; @endphp
                    <table class="table table-striped">
                        @foreach($sentInvoiceValue as $item)
                            @if($item["invoice_field_category_id"]==5)
                                <tr>
                                    <td colspan="2">
                                        @if($sn==1) <br/> @endif
                                        <strong>{{ $item["label"] }}</strong><br/>
                                        <?php $images   =   \App\EmailSentInvoiceImage::where('email_sent_invoice_value_id',$item["id"])->get(); ?>
                                        @if($images=="")
                                            <ul style="list-style: none;margin:5px 0px 0px;padding: 0px;">
                                                @foreach($images as $image)
                                                    <li style="margin-right:10px;float: left;">
                                                        <a href="{{ AmazoneBucket::url() }}{{ $image->value }}" target="_blank">
                                                            <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="height: 120px;border: 1px solid #ddd;">
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

                <center>
                    <a href="{{ $downloadLink }}" target="_blank" style="text-decoration:underline;background-color:#ffffff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize;background-color:#3498db;border-color:#3498db;color:#ffffff;margin-left:20px;">
                        Download
                    </a>
                </center>
                <div style="background:#012e55;color:#fff;padding:20px;margin-top:25px;position:absolute;bottom:0px;">
                    <div style="max-width: 600px;margin: 0px auto;">
                        <a href="http://recordtime.com.au/" target="_blank" style="float:left;"><img src="{{ asset('assets/beta/images/logoWhiteStrok.png') }}" height="40px" ></a>
                        <span style="float:right;font-size:12px;line-height: 3.8em;">Invoice Created Using Record TIME</span>
                        <div style="clear:both"></div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
        </table>
    </body>
</html>