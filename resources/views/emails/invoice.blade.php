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
            padding: 15px;
        }
        table td{
            padding: 15px;
            color: #636b6f;
        }
        table td strong{
            margin-bottom: 5px;
            color: #2e3052;
            font-weight: bold;
            font-size: 14px;
            display: block;
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
            width: 580px;
        }
        /* This should also be a block element, so that it will fill 100% of the .container */
        .content {
            box-sizing: border-box;
            display: block;
            Margin: 0 auto;
            max-width: 580px;
            padding: 10px;
            border: 1px solid #eee;
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
<body>
<table border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td class="container">
            <div class="content">
                <table>
                    <tr>
                        <td style="line-height:1.5;text-align:center;color:#636b6f;font-size: 14px;">
                            <img src="https://files.stripe.com/files/f_live_AV2RAgQYWSgqO1Try5UjQuhZ" style="height:80px;margin:0px auto;display:block;border-radius: 50%;padding: 10px;">
                            <strong style="font-size: 18px;  margin-top: 0px;margin-bottom: 0px;text-transform: uppercase;font-weight: 500;color: #2b2d50;">RECORD TIME PTY LTD.</strong>
                            Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA<br/>
                            ABN : 99 604 582 649, Tel : +61 421 955 630, Email : info@recordtime.com.au
                        </td>
                    </tr>
                </table>
                <table border="0" cellspacing="0" style="background: #f7fafc;border-bottom: 1px solid #e8e8e8;margin: 15px 0px 0px;">
                    <tbody>
                    <tr>
                        <td style="width: 60%;">
                            <ul style="list-style:none;margin:0px;padding:0px;line-height: 1.5em;color:#525471;">
                                <li><strong>BILLING TO:</strong></li>
                                <li style="font-weight: 500;">{{ $company->name }}</li>
                                <li>{{ $company->address }}</li>
                                <li>{{ $company->userInfo->email }}</li>
                                <li>ABN : {{ $company->abn }}</li>
                            </ul>
                        </td>
                        <td>
                            <ul style="list-style:none;margin:0px;padding:0px;line-height: 1.5em;color:#525471;">
                                <li><strong>TAX INVOICE</strong></li>
                                <li>Invoice Number: #{{ $stripeInvoice->number }}</li>
                                <li>Date:  {{  \Carbon\Carbon::createFromTimestamp($stripeInvoice->date,'Australia/Canberra')->format('d M, Y') }}</li>
                            </ul>
                        </td>
                    </tbody>
                </table>

                <!-- START CENTERED WHITE CONTAINER -->
                <span class="preheader"></span>
                <table class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table border="0" cellspacing="0" >
                                <thead>
                                <tr style="padding:10px; background:#0c3355;text-align:left;color: #fff;text-transform:uppercase;font-size: 12px;">
                                    <th>Description</th>
                                    <th>QTY</th>
                                    <th style="text-align:right;">Amount</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($stripeInvoice->lines['data'] as $data)
                                    <tr style="border-bottom:1px solid #ddd;">
                                        <td style="border-bottom: 1px solid #eee;">
                                            <strong>{{ $data->plan["nickname"] }}</strong>
                                            @php
                                                $periodStart =   \Carbon\Carbon::createFromTimestamp($data->period['start'],'Australia/Canberra');
                                                $periodEnd  =   \Carbon\Carbon::createFromTimestamp($data->period['end'],'Australia/Canberra');
                                            @endphp

                                            {{ $periodStart->format('M d') }} @if($periodStart->format('Y')!=$periodEnd->format('Y')) , {{ $periodStart->format('Y') }} @endif
                                            - {{ $periodEnd->format('M d')  }} @if($periodStart->format('Y')!=$periodEnd->format('Y')), {{ $periodEnd->format('Y') }}
                                            @else , {{  \Carbon\Carbon::createFromTimestamp($data->period['end'],'Australia/Canberra')->format('Y') }}
                                        @endif
                                        <td style="border-bottom: 1px solid #eee;">
                                            1
                                        </td>
                                        <td style="border-bottom: 1px solid #eee;text-align:right;">
                                            <strong>$ {{ sprintf('%0.2f',$data->amount/110) }}</strong>
                                        </td>
                                    </tr>
                                @endforeach

                                <tr style="text-align:left;width:100%;">
                                    <td></td>
                                    <td style="background:#f7fafc;border-bottom: 1px solid #eee;">Subtotal</td>
                                    <td style="background:#f7fafc;border-bottom: 1px solid #eee;color:#2e3052;font-weight:bold;text-align:right;">$ {{ sprintf('%0.2f',round($stripeInvoice->total/110,2)) }}</td>
                                </tr>

                                <tr style="text-align:left;width:100%;">
                                    <td></td>
                                    <td style="border-bottom: 1px solid #eee;">GST</td>
                                    <td style="border-bottom: 1px solid #eee;color:#2e3052;font-weight:bold;text-align:right;">$ {{ sprintf('%0.2f',$stripeInvoice->total/100-round($stripeInvoice->total/110,2)) }}</td>
                                </tr>

                                <tr style="text-align:left;width:100%;">
                                    <td></td>
                                    <td style="background: #dae4ea;color:#2e3052;font-weight:bold;">Total</td>
                                    <td style="background: #dae4ea;color:#2e3052;font-weight:bold;text-align:right;">{{  sprintf('%0.2f',round($stripeInvoice->total/100),2) }}</td>
                                </tr>

                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <hr>

                <strong style="display: inline-block;">Payment Method</strong>
                <img src="https://www.recordtimeapp.com.au/backend/assets/dashboard/images/stripeLogo.png" width="40px" style="margin-bottom: -2px;margin-left: 7px;">
                <p style="margin-top: 10px;color:#636b6f;">If you have any questions, Contact <b>Record TIme pty ltd</b> at <b>info@recordtime.com.au</b> or call at <b>+61 421 955 630</b></p>

                <center>
                    <a href="{{ @$downloadLink }}" target="_blank" style="text-decoration:underline;background-color:#ffffff;border:solid 1px #3498db;border-radius:5px;box-sizing:border-box;color:#3498db;cursor:pointer;display:inline-block;font-size:14px;font-weight:bold;margin:0;padding:12px 25px;text-decoration:none;text-transform:capitalize;background-color:#3498db;border-color:#3498db;color:#ffffff;margin-left:20px;">
                        Download
                    </a>
                </center>
            </div>
        </td>
    </tr>
    <!-- END MAIN CONTENT AREA -->
</table>
</body>
</html>