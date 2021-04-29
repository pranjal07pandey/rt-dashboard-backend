<!doctype html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width" />

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

        .table-striped>tbody>tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
        .table-striped>tbody>tr>td,.table-striped>thead>tr>th{
            padding: 8px;
            line-height: 1.6;
            vertical-align: top;
            border-top: 1px solid #ddd;
            line-height: 3;
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
        .box-timer{
            text-align: center;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            transition: 0.3s;
            width: 20%;
            padding: 20px 0 20px 0px;
            border: 1px solid #c1bcbc;
        }
        .box-timer:hover {
            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
        }

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
                            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="height:150px;">
                            @else
                                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                            @endif<br/><br/>
                            From:<br>
                            <strong>{{ @$sentDocket->sender_name }}</strong><br>
                            {{ @$sentDocket->company_name }}<br/>
                            {{ @$sentDocket->company_address }}
                            <br><br>
                            To:<br/>
                            @if($receiverDetail)
                                <?php $sns = 0; ?>
                                @foreach($receiverDetail as $key=>$value)
                                    @foreach($value as $keys)
                                        <?php $sns++; ?>
                                        @if($sns<=count($sentDocket->recipientInfo) && $sns!=1)
                                            ,
                                        @endif
                                        {{$keys}}
                                    @endforeach

                                @endforeach
                                <br>
                                <?php $sn = 0; ?>
                                <b>Company Name:</b>

                                @foreach($receiverDetail as $key=>$value)
                                    <?php $sn++; ?>
                                    @if($sn<=count($receiverDetail) && $sn!=1)
                                        ,
                                    @endif
                                    {{$key}}
                                @endforeach
                            @endif
                        </td>
                        <td style="text-align: left;width:170px;">
                            <strong>{{ $sentDocket->template_title}}</strong><br/>
                            <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>
                            <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>


                            {{ $sentDocket->formatted_id }}

                        </td>
                    </tr>
                </table>

                <!-- START CENTERED WHITE CONTAINER -->
                <span class="preheader"></span>
                <table class="main">
                    <tr>
                        <td class="wrapper">
                            <table border="0" cellpadding="0" cellspacing="0" class="table-striped">
                                <thead>
                                <tr style="text-align:left">
                                    <th style="width:50%">Description</th>
                                    <th style="width:50%">Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($sentDocket->sentDocketValue)
                                    @foreach($sentDocket->sentDocketValue as $row)
                                        @if((!$row->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))

                                            @if($row->docketFieldInfo->docket_field_category_id==7)
                                                <?php $sn = 1; $total = 0; ?>
                                                @foreach($row->sentDocketUnitRateValue as $row)
                                                    <tr>
                                                        <td>{{ $row->label }}</td>
                                                        <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                        @if($sn == 1)
                                                            <?php $total = $row->value; ?>
                                                        @else
                                                            <?php $total    =   $total*$row->value; ?>
                                                        @endif
                                                        <?php $sn++; ?>
                                                    </tr>
                                                @endforeach
                                                <tr >
                                                    <td colspan="2">
                                                        <strong>Total: $ {{ $total }}</strong>
                                                    </td>
                                                </tr>

                                            @elseif($row->docketFieldInfo->docket_field_category_id==24)
                                                <tr>
                                                    <td colspan="2"><strong>{{ $row->label }}</strong> </td>
                                                </tr>
                                                <?php $sn = 1; $total = 0; ?>
                                                @foreach($row->sentDocketTallyableUnitRateValue as $row)
                                                    <tr>
                                                        <td>{{ $row->docketUnitRateInfo->label }}</td>
                                                        <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                        @if($sn == 1)
                                                            <?php $total = $row->value; ?>
                                                        @else
                                                            <?php $total    =   $total*$row->value; ?>
                                                        @endif
                                                        <?php $sn++; ?>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td>
                                                        <strong>Total:</strong>
                                                    </td>
                                                    <td>
                                                        <strong>$ {{ $total }}</strong>
                                                    </td>
                                                </tr><!--tallyunit-rate-->
                                            @elseif($row->docketFieldInfo->docket_field_category_id==8)
                                                <tr>
                                                    <td> {{ $row->label }}</td>
                                                    <td> @if($row->value==1)
                                                            <img src="{{ asset('assets/dashboard/img/checked.png') }}" width="20px">
                                                        @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="20px">@endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==5)
                                                <tr>
                                                    <td colspan="2">{{ $row->label }}<br/>
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="margin:0px;padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $rowImage)
                                                                    <li style="display:inline-block;margin-left:0px;margin-right:5px;">
                                                                        <a href="{{ AmazoneBucket::url() }}{{ $rowImage->value }}" target="_blank">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $rowImage->value }}"   width="170px">
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <strong>No Image Attached</strong>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==9)
                                                <tr>
                                                    <td colspan="2">{{ $row->label }}<br/>
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="margin:0px;padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $rowImage)
                                                                    <li style="display:inline-block;margin-left:0px;margin-right:5px;">
                                                                        <img src="{{ AmazoneBucket::url() }}{{ $rowImage->value }}" width="170px" style="margin: 0px 15px">
                                                                        <p style="font-weight: 500;color: #868d90;margin: 0px 15px;">{{$rowImage->name}}</p>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <strong>No Signature Attached</strong>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==6)
                                                <tr>
                                                    <td> {{ $row->label }}</td>
                                                    @if($row->value=="N/a")
                                                        <td>{{$row->value}}</td>
                                                    @else
                                                        <td> {{ \Carbon\Carbon::parse($row->value)->format('d-M-Y') }}</td>
                                                    @endif
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==14)
                                                <tr>
                                                    <td> {{ $row->label }}</td>

                                                    <td>
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $sketchPad)
                                                                    <li style="margin-right:10px;float: left;">
                                                                        <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}"  width="170px" style="height: 100px;">
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <strong>No Sketch Attached</strong>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==20)
                                                <tr>
                                                    <td>{{ $row->label }}</td>
                                                    <td>
                                                        @foreach($row->sentDocketManualTimer as $rows)
                                                            <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                                        @endforeach
                                                        <br>
                                                        @foreach($row->sentDocketManualTimerBreak as $items)
                                                            <strong>{{ $items->label }} :</strong>  {{ $items->value }}<br>
                                                            <strong>Reason for break :</strong>  {{ $items->reason }}<br>
                                                        @endforeach
                                                        <strong>Total time :</strong>  {{ $row->value }}<br>

                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==15)
                                                <tr>
                                                    <td> {{ $row->label }}</td>
                                                    <td>
                                                        @if($row->attachedDocument->count()>0)
                                                            <ul class="pdf">
                                                                @foreach($row->attachedDocument as $document)
                                                                    <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></b></li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <strong>No Document Attached</strong>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==12)
                                                <tr>
                                                    <td  colspan="2"> <strong>{{ $row->label }}</strong></td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==27)
                                                <tr>
                                                    <td  colspan="2"> <strong>{!! $row->label !!}</strong></td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==18)
                                                <tr>
                                                    <td colspan="2">
                                                        @php
                                                            $yesno = unserialize($row->label);
                                                        @endphp
                                                        <div style="width:100%;margin:0;">
                                                            <div style="width:50%;float:left;">{{ $yesno['title']}}</div>
                                                            @if($row->value == "N/a")
                                                                <div style="width:50%; float:right;padding-left:8px;"> N/a </div>
                                                            @else
                                                                @if($yesno['label_value'][$row->value]['label_type']==1)
                                                                    <div style="width:50%; float:right;padding-left: 8px;"><img  width="20px" style=" height:20px; padding:4px; background-color:{{ $yesno['label_value'][$row->value]['colour']}}; border-radius:20px;" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$row->value]['label'] }}"></div>
                                                                @else
                                                                    <div style="width:50%; float:right;padding-left: 8px;">{{ $yesno['label_value'][$row->value]['label']}}</div>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        @if(collect($row->SentEmailDocValYesNoValueInfo)->count()==0)
                                                        @else
                                                            <table style="background: transparent; width: 100%;" class="table table-striped">
                                                                <thead style="background: transparent; ">
                                                                <tr>
                                                                    <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody >
                                                                @foreach($row->SentEmailDocValYesNoValueInfo as $items)
                                                                    @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                                        @php
                                                                            $imageData=unserialize($items->value);
                                                                        @endphp
                                                                        <tr>
                                                                            <td>{{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td>
                                                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                    @if(empty($imageData))
                                                                                        <b>No Image Attached</b>
                                                                                    @else
                                                                                        @foreach($imageData as $rowData)
                                                                                            <li style="margin-right:10px;float: left;">
                                                                                                <br>
                                                                                                <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank">
                                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $rowData }}"  width="150px" style="height: 100px;">
                                                                                                </a>
                                                                                            </li>
                                                                                        @endforeach
                                                                                    @endif
                                                                                </ul>
                                                                            </td>
                                                                            <br>  <br>
                                                                        </tr>
                                                                    @endif
                                                                    @if($items->YesNoDocketsField->docket_field_category_id==1)
                                                                        <tr>
                                                                            <td> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td>{{$items->value }}</td>
                                                                        </tr>
                                                                    @endif
                                                                    @if($items->YesNoDocketsField->docket_field_category_id==2)
                                                                        <tr>
                                                                            <td> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td>{{$items->value }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    </td>
                                                </tr>

                                            @elseif($row->docketFieldInfo->docket_field_category_id==22)
                                                <tr>
                                                    <td colspan="2">{{ $row->label }}
                                                        <div style="    width: 500px;overflow: auto;">
                                                            <table  class="table table-striped" width="100%" border="1">
                                                                <thead>
                                                                <tr>
                                                                    @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)
                                                                        @if((!$gridFieldLabels->docketFieldGrid->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                                                            <th class="printTh" style="min-width: 200px">
                                                                                <div class="printColorDark">{{ $gridFieldLabels->label}}</div>
                                                                            </th>
                                                                         @endif
                                                                    @endforeach
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @php
                                                                    $gridMaxRow     =    $row->sentDocketFieldGridValues->max('index');
                                                                @endphp
                                                                @for($i = 0; $i<=$gridMaxRow; $i++)
                                                                    <tr>
                                                                        @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)
                                                                            @if((!$gridFieldLabels->docketFieldGrid->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                                                                @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || $gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                                        <td>N/a</td>
                                                                                    @else
                                                                                        @php
                                                                                            $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                                                        @endphp
                                                                                        <td>
                                                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                                @if(empty($values))
                                                                                                    <b>No Image Attached</b>
                                                                                                @else
                                                                                                    @foreach($values as $value)
                                                                                                        <li sstyle="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                            <a href="{{ AmazoneBucket::url() }}{{ $value }}" target="_blank">
                                                                                                                <img src="{{ AmazoneBucket::url() }}{{ $value }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                                            </a>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </ul>
                                                                                        </td>
                                                                                    @endif
                                                                                @elseif($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 9)
                                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                                        <td>N/a</td>
                                                                                    @else
                                                                                        @php
                                                                                            $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                                                        @endphp
                                                                                        <td>
                                                                                            @if(!empty($values))
                                                                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                                    @foreach($values as $value)
                                                                                                        <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                            <a href="{{ AmazoneBucket::url() }}{{ $value['image'] }}" target="_blank">
                                                                                                                <img src="{{ AmazoneBucket::url() }}{{ $value['image'] }}" tyle="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                                            </a>
                                                                                                            <p style="font-weight: 500;color: #868d90;">{{$value['name']}}</p>
                                                                                                        </li>
                                                                                                    @endforeach
                                                                                                </ul>
                                                                                            @else
                                                                                                <b>No Signature Attached</b>
                                                                                            @endif
                                                                                        </td>
                                                                                    @endif
                                                                                @else
                                                                                    @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 8)
                                                                                        <td>
                                                                                            @php
                                                                                                $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                                                                            @endphp
                                                                                            @if($value==1)
                                                                                                <i class="fa fa-check-circle" style="color:green"></i>
                                                                                            @else
                                                                                                <i class="fa fa-close" style="color:#ff0000 !important"></i>
                                                                                            @endif
                                                                                        </td>

                                                                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==29)
                                                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == null)
                                                                                            <td></td>
                                                                                        @else
                                                                                            <td  style="line-height: 2em;white-space: pre-wrap;">
                                                                                                @foreach(unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value) as $data)
                                                                                                    {!! $data['email'] !!}
                                                                                                @endforeach
                                                                                            </td>
                                                                                        @endif


                                                                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                                                                        <?php
                                                                                        $manualTimerGrid =   @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                                                                        ?>

                                                                                        @if($manualTimerGrid != "")

                                                                                            <?php
                                                                                            $totalDuration = json_decode($manualTimerGrid , true)['totalDuration'];
                                                                                            $breakDuration =json_decode($manualTimerGrid , true)['breakDuration'];
                                                                                            ?>
                                                                                            <td>
                                                                                                <strong>From :</strong>  {{   json_decode($manualTimerGrid , true)['from'] }}<br>
                                                                                                <strong>To :</strong>  {{ json_decode($manualTimerGrid , true)['to'] }}
                                                                                                <br>
                                                                                                <strong>Total Break :</strong>   {{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($breakDuration) }}<br>
                                                                                                <strong>Reason for break :</strong>  {{ json_decode($manualTimerGrid , true)['explanation'] }}<br>
                                                                                                <strong>Total time :</strong>  {{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($totalDuration) }}<br>
                                                                                            </td>
                                                                                        @else
                                                                                            <td>N/a</td>

                                                                                        @endif
                                                                                    @else
                                                                                        <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value }}</td>
                                                                                    @endif
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    </tr>
                                                                @endfor
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </td>

                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id == 29)
                                                <tr>
                                                    <td> {{ $row->label }}</td>
                                                    <td style="line-height: 1.5em;white-space: pre-line;">
                                                        @foreach(unserialize($row->value) as $email)
                                                            {!! $email['email'] !!}
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endif

                                        @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!=30)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                <td> {!! $row->value !!}</td>
                                            </tr>
                                        @endif
                                        @endif
                                    @endforeach
                                    @foreach($sentDocket->sentDocketValue as $row)
                                        @if(!$row->docketFieldInfo->is_hidden)
                                            @if($row->docketFieldInfo->docket_field_category_id==13)
                                                <tr style="padding-top: 0px;">
                                                    <td  colspan="2"><b>{{ $row->label }}</b>
                                                        <p style="color:#7b7b7bd4;margin-top: 0;">{{ $row->value }}</p>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>

                <br>
                @if(@$sentDocket->sentDocketTimerAttachment->count()>0)
                    <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable">
                        <thead>
                        <tr style="background:#ddd;text-align:left;width:100%;">
                            <th width="45%"> Timer Attachments</th>
                        </tr>
                        </thead>
                        <tbody style="background-color: #fff;">
                        <tr>
                            <td colspan="2">
                                <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                    @php
                                        $totalInterval = 0;
                                    @endphp
                                    @if(@$sentDocket->sentDocketTimerAttachment->count())
                                        @foreach(@$sentDocket->sentDocketTimerAttachment as $row)
                                            <li class="box-timer" style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 92px; height: 130px;border:1px solid #eee;padding: 10px;">
                                                <img src="{{ asset('assets/clock.png') }}" style="max-height:14px; max-width:21px;margin:0px  auto; display: block"><br>
                                                <p style="margin-top:-10px"><strong>{{$row->timerInfo->total_time}}</strong></p>
                                                <p><img src="{{ asset('assets/marker.png') }}" style="max-height:10px; max-width:16px;margin:0px ;"> {!!  str_limit(strip_tags($row->timerInfo->location),20) !!}</p>
                                                <p> {{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                @endif
                @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                    <center>
                    </center>
                @endif
            </div>

        </td>
    </tr>
    <!-- END MAIN CONTENT AREA -->
</table>
</div>
</td>
<td>&nbsp;</td>
</tr>
</table>
</body>
</html>
