<!doctype html>
<html lang="en" xml:lang="en">
<head>
    <meta name="viewport" content="width=device-width">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Record Time</title>
    <style>
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }

            table[class=body] p,
            table[class=body] ul,
            table[class=body] ol,
            table[class=body] td,
            table[class=body] span,
            table[class=body] a {
                font-size: 16px !important;
            }

            table[class=body] .wrapper,
            table[class=body] .article {
                padding: 10px !important;
            }

            table[class=body] .content {
                padding: 0 !important;
            }

            table[class=body] .container {
                padding: 0 !important;
                width: 100% !important;
            }

            table[class=body] .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }

            table[class=body] .btn table {
                width: 100% !important;
            }

            table[class=body] .btn a {
                width: 100% !important;
            }

            table[class=body] .img-responsive {
                height: auto !important;
                max-width: 100% !important;
                width: auto !important;
            }
        }
        @media all {
            .ExternalClass {
                width: 100%;
            }

            .ExternalClass,
            .ExternalClass p,
            .ExternalClass span,
            .ExternalClass font,
            .ExternalClass td,
            .ExternalClass div {
                line-height: 100%;
            }

            .apple-link a {
                color: inherit !important;
                font-family: inherit !important;
                font-size: inherit !important;
                font-weight: inherit !important;
                line-height: inherit !important;
                text-decoration: none !important;
            }

            .btn-primary table td:hover {
                background-color: #34495e !important;
            }

            .btn-primary a:hover {
                background-color: #34495e !important;
                border-color: #34495e !important;
            }
        }
    </style>
</head>
<body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
<table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #f6f6f6; width: 100%;" width="100%" bgcolor="#f6f6f6">
    <tr>
        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">&nbsp;</td>
        <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; max-width: 580px; padding: 10px; width: 580px; Margin: 0 auto;" width="580" valign="top">
            <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; background: #fff;">
                <table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                    <tr>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; line-height: 1.5;" valign="top">
                            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; height: 150px;" height="150">
                            @else
                                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%;">
                            @endif<br><br>
                            From:<br>
                            <strong>{{ @$sentDocket->sender_name }}</strong><br>
                            {{ @$sentDocket->company_name }}<br>
                            {{ @$sentDocket->company_address }}
                            <br><br>
                            @if($sentDocket->recipientInfo)
                                @foreach($sentDocket->recipientInfo as $recipient)
                                    @if($recipient->approval==1) * @endif To:<br>
                                    <strong>{{ @$recipient->emailUserInfo->email }}</strong> <br>
                                    @if($recipient->receiver_full_name!=""){{ $recipient->receiver_full_name }} <br>@endif
                                    @if($recipient->receiver_company_name!="") {{ $recipient->receiver_company_name }} <br>{{ $recipient->receiver_company_address }} @endif
                                    <br><br>
                                @endforeach
                            @endif
                        </td>
                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: left; width: 170px;" width="170" align="left" valign="top">
                            <strong>{{ $sentDocket->template_title}}</strong><br>
                            <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br>
                            <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>
                            {{ $sentDocket->formatted_id }}


                            <br>
                        </td>
                    </tr>

                </table>


                <!-- START CENTERED WHITE CONTAINER -->
                <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;"></span>
                <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: #fff; border-radius: 3px; width: 100%;" width="100%">

                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px 0px;" valign="top">
                            <table border="0" cellpadding="0" cellspacing="0" class="table-striped" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
                                <thead style="background: #eee;">
                                <tr style="text-align:left">
                                    <th style="padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3; border-bottom: 2px solid #ddd; width: 50%;" valign="top">Description</th>
                                    <th style="padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3; border-bottom: 2px solid #ddd; width: 50%;" valign="top">Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if($docketFields)

                                    @foreach($docketFields as $row)
                                        @if(!$row->is_hidden)
                                            @if($row->docketFieldInfo->docket_field_category_id==7)
                                                <?php $sn = 1; $total = 0; ?>
                                                @foreach($row->sentDocketUnitRateValue as $row)
                                                    <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $row->label }}</td>
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                        @if($sn == 1)
                                                            <?php $total = $row->value; ?>
                                                        @else
                                                            <?php $total    =   $total*$row->value; ?>
                                                        @endif
                                                        <?php $sn++; ?>
                                                    </tr>

                                                @endforeach
                                                <tr>
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        <strong>Total: $ {{ $total }}</strong>
                                                    </td>
                                                </tr>

                                            @elseif($row->docketFieldInfo->docket_field_category_id==24)
                                                <tr>
                                                    <td  colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"><strong>{{ $row->label }}</strong> </td>
                                                </tr>
                                                <?php $sn = 1; $total = 0; ?>
                                                @foreach($row->sentDocketTallyableUnitRateValue as $row)
                                                    <tr>
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $row->docketUnitRateInfo->label }}</td>
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                        @if($sn == 1)
                                                            <?php $total = $row->value; ?>
                                                        @else
                                                            <?php $total    =   $total*$row->value; ?>
                                                        @endif
                                                        <?php $sn++; ?>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        <strong>Total:</strong>
                                                    </td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        <strong>$ {{ $total }}</strong>
                                                    </td>
                                                </tr><!--tallyunit-rate-->
                                            @elseif($row->docketFieldInfo->docket_field_category_id==8)
                                                <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> @if($row->value==1)
                                                            <img src="{{ asset('assets/dashboard/img/checked.png') }}" width="20px" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%;">
                                                        @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="20px" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%;">@endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==5)
                                                <tr>
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $row->label }}<br>
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; margin: 0px; padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $rowImage)
                                                                    <li style="list-style-position: inside; display: inline-block; margin-left: 0px; margin-right: 5px;">
                                                                        <a href="{{ AmazoneBucket::url() }}{{ $rowImage->value }}" target="_blank" style="color: #3498db; text-decoration: underline;">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $rowImage->value }}" width="170" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%;">
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
                                                <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $row->label }}<br>
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; margin: 0px; padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $rowImage)
                                                                    <li style="list-style-position: inside; display: inline-block; margin-left: 0px; margin-right: 5px;">
                                                                        <img src="{{ AmazoneBucket::url() }}{{ $rowImage->value }}" width="170" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%;widh:170px; margin: 0px 15px;">
                                                                        <p style="font-weight: 500;color: #868d90;margin: 0px 15px">{{$rowImage->name}}</p>
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
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>
                                                    @if($row->value=="N/a")
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{$row->value}}</td>
                                                    @else
                                                        <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ \Carbon\Carbon::parse($row->value)->format('d-M-Y') }}</td>
                                                    @endif
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==14)
                                                <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>

                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        @if($row->sentDocketImageValue->count()>0)
                                                            <ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; list-style: none; margin: 0px; padding: 0px;">
                                                                @foreach($row->sentDocketImageValue as $sketchPad)
                                                                    <li style="list-style-position: inside; margin-left: 5px; margin-right: 10px; float: left;">
                                                                        <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank" style="color: #3498db; text-decoration: underline;">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" width="170" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; height: 100px;" height="100">
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
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $row->label }}</td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        @foreach($row->emailSentDocManualTimer as $rows)
                                                            <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                                        @endforeach
                                                        <br>
                                                        @foreach($row->emailSentDocManualTimerBrk as $items)
                                                            <strong>{{ $items->label }} :</strong>  {{ $items->value }}<br>
                                                            <strong>Reason for break :</strong>  {{ $items->reason }}<br>
                                                        @endforeach
                                                        <strong>Total time :</strong>  {{ $row->value }}<br>

                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==15)
                                                <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        @if($row->sentEmailAttachment->count()>0)
                                                            <ul class="pdf" style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; list-style-type: none; margin: 0; padding: 0; margin-top: 2px;">
                                                                @foreach($row->sentEmailAttachment as $document)
                                                                    <li style="list-style-position: inside; margin-left: 5px; display: inline-block; font-size: 12px; text-align: center; padding-right: 15px;"><img src="{{ asset('assets/pdf.png') }}" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; height: 12px; width: 12px;" width="12" height="12"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank" style="color: #3498db; text-decoration: underline; padding-left: 5px;">{{$document->document_name}}</a></b></li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            <strong>No Document Attached</strong>
                                                        @endif

                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==12)
                                                <tr>
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> <strong>{{ $row->label }}</strong></td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id==27)
                                                <tr>
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> <strong>{!! $row->label !!}</strong></td>
                                                </tr>

                                            @elseif($row->docketFieldInfo->docket_field_category_id==18)

                                                <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        <!--<table style="width:100%;">-->
                                                        <!--<tr>-->

                                                        @php
                                                            $yesno = unserialize($row->label);
                                                        @endphp
                                                        <div style="width:100%;margin:0;">
                                                            <div style="width:50%;float:left;">{{ $yesno['title']}}</div>
                                                            @if($row->value == "N/a")
                                                                <div style="width:50%; float:right;padding-left:8px;"> N/a </div>
                                                            @else
                                                                @if($yesno['label_value'][$row->value]['label_type']==1)
                                                                    <div style="width:50%; float:right;padding-left: 8px;"><img width="20px" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; height: 20px; padding: 4px; background-color: <!--HBS  $yesno['label_value'][$row->value]['colour'];" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$row->value]['label'] }}" height="20"></div>
                                                                @else
                                                                    <div style="width:50%; float:right;padding-left: 8px;">{{ $yesno['label_value'][$row->value]['label']}}</div>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        <!-- </tr>-->
                                                        <!--</table>-->
                                                        @if(count($row->SentEmailDocValYesNoValueInfo)==0)
                                                        @else
                                                            <table style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: transparent; width: 100%;" class="table table-striped" width="100%">
                                                                <thead style="background: transparent;">
                                                                <tr>
                                                                    <th colspan="2" style="padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3; border-bottom: 2px solid #ddd;" valign="top"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($row->SentEmailDocValYesNoValueInfo as $items)
                                                                    @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                                        @php
                                                                            $imageData=unserialize($items->value);
                                                                        @endphp
                                                                        <tr style="background-color: #f9f9f9;" bgcolor="#f9f9f9">
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                                                <ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; list-style: none; margin: 0px; padding: 0px;">
                                                                                    @if(empty($imageData))
                                                                                        <b>No Image Attached</b>
                                                                                    @else
                                                                                        @foreach($imageData as $rowData)
                                                                                            <li style="list-style-position: inside; margin-left: 5px; margin-right: 10px; float: left;">
                                                                                                <br>
                                                                                                <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank" style="color: #3498db; text-decoration: underline;">
                                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" width="150px" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; height: 100px;" height="100">
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
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{$items->value }}</td>
                                                                        </tr>
                                                                    @endif
                                                                    @if($items->YesNoDocketsField->docket_field_category_id==2)
                                                                        <tr>
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                            <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">{{$items->value }}</td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id == 22)
                                                <tr>
                                                    <td colspan="2"  >{{ $row->label }}
                                                        <div style="    width: 500px;overflow: auto;">
                                                            <table  class="table table-striped" width="100%" border="1" >
                                                                <thead>
                                                                <tr>
                                                                    @foreach($row->emailSentDocketFieldGridLabels as $gridFieldLabels)
                                                                        @if(!$gridFieldLabels->docketFieldGrid->is_hidden)
                                                                            <th class="printTh" style="min-width: 200px">
                                                                                <div class="printColorDark">{{ $gridFieldLabels->label}}</div>
                                                                            </th>
                                                                        @endif
                                                                    @endforeach
                                                                </tr>
                                                                </thead>
                                                                <tbody >
                                                                @php
                                                                    $gridMaxRow     =    $row->emailSentDocketFieldGridValues->max('index');
                                                                @endphp
                                                                @for($i = 0; $i<=$gridMaxRow; $i++)
                                                                    <tr>
                                                                        @foreach($row->emailSentDocketFieldGridLabels as $gridFieldLabels)
                                                                            @if(!$gridFieldLabels->docketFieldGrid->is_hidden)
                                                                                @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || $gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == 'N/a')
                                                                                        <td valign="top">N/a</td>
                                                                                    @else
                                                                                        @php
                                                                                            try{
                                                                                               $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value);
                                                                                              }
                                                                                               catch(Exception $e){
                                                                                                  $values = "N/a";
                                                                                               }
                                                                                        @endphp
                                                                                        @if($values == "N/a")
                                                                                            <td valign="top">N/a</td>
                                                                                        @else
                                                                                            <td valign="top">
                                                                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                                    @if(empty($values))
                                                                                                        <b>No Image Attached</b>
                                                                                                    @else
                                                                                                        @foreach($values as $value)
                                                                                                            <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                                <a href="{{ AmazoneBucket::url() }}{{ $value }}" target="_blank">
                                                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $value }}" style="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                                                </a>
                                                                                                            </li>
                                                                                                        @endforeach
                                                                                                    @endif
                                                                                                </ul>
                                                                                            </td>
                                                                                        @endif
                                                                                    @endif
                                                                                @elseif($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 9)

                                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == 'N/a')
                                                                                        <td valign="top">N/a</td>
                                                                                    @else
                                                                                        @php
                                                                                            try{
                                                                                              $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value);
                                                                                              }
                                                                                              catch(Exception $e){
                                                                                                 $values = "N/a";
                                                                                              }

                                                                                        @endphp

                                                                                        @if($values == "N/a")
                                                                                            <td valign="top">N/a</td>
                                                                                        @else
                                                                                            <td valign="top">
                                                                                                @if(!empty($values))
                                                                                                    <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                                        @foreach($values as $value)
                                                                                                            <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                                <a href="{{ AmazoneBucket::url() }}{{ $value['image'] }}" target="_blank">
                                                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $value['image'] }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">
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
                                                                                    @endif
                                                                                @else
                                                                                    @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 8)
                                                                                        <td valign="top">
                                                                                            @php
                                                                                                $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value;
                                                                                            @endphp
                                                                                            @if($value==1)
                                                                                                <i class="fa fa-check-circle" style="color:green"></i>
                                                                                            @else
                                                                                                <i class="fa fa-close" style="color:#ff0000 !important"></i>
                                                                                            @endif
                                                                                        </td>
                                                                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==29)
                                                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == null)
                                                                                            <td></td>
                                                                                        @else
                                                                                            <td  style="line-height: 2em;white-space: pre-wrap;">
                                                                                                @foreach(unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value) as $data)
                                                                                                    {!! $data['email'] !!}
                                                                                                @endforeach
                                                                                            </td>
                                                                                        @endif


                                                                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                                                                        <?php
                                                                                        $manualTimerGrid =   @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value;
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
                                                                                        <td valign="top">{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value }}</td>
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
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top">
                                                        @foreach(unserialize($row->value) as $email)
                                                            {!! $email['email'] !!}
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!=30)
                                                <tr>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {{ $row->label }}</td>
                                                    <td style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"> {!! $row->value !!}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                    @foreach($docketFields as $row)
                                        @if(!$row->docketFieldInfo->is_hidden)
                                            @if($row->docketFieldInfo->docket_field_category_id==13)
                                                <tr style="background-color: #f9f9f9; padding-top: 0px;" bgcolor="#f9f9f9">
                                                    <td colspan="2" style="font-family: sans-serif; font-size: 14px; padding: 8px; line-height: 1.6; vertical-align: top; border-top: 1px solid #ddd; line-height: 3;" valign="top"><b>{{ $row->label }}</b>
                                                        <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px; color: #7b7b7bd4; margin-top: 0;">{{ $row->value }}</p>
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
                @if($docketTimer->count()>0)
                    <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                        <thead style="background: #eee;">
                        <tr style="background:#ddd;text-align:left;width:100%;">
                            <th width="45%" style="padding: 10px;"> Timer Attachments</th>
                        </tr>
                        </thead>
                        <tbody style="background-color: #fff;">
                        <tr>
                            <td colspan="2" style="font-family: sans-serif; font-size: 14px; vertical-align: top;" valign="top">

                                <ul style="font-family: sans-serif; font-size: 14px; font-weight: normal; Margin-bottom: 15px; list-style: none; margin: 10px 0px 0px; padding: 0px;">
                                    @php
                                        $totalInterval = 0;
                                    @endphp
                                    @if($docketTimer->count())
                                        @foreach($docketTimer as $row)
                                            <li class="box-timer" style="list-style-position: inside; margin-left: 5px; text-align: center; box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); transition: 0.3s; background: #fff; margin-right: 10px; float: left; margin-bottom: 10px; width: 92px; height: 130px; border: 1px solid #eee; padding: 10px;">
                                                {{--<a href="{{ asset($sketchPad->value) }}" target="_blank">--}}
                                                {{--</a>--}}
                                                <img src="{{ asset('assets/clock.png') }}" style="border: none; -ms-interpolation-mode: bicubic; max-height: 14px; max-width: 21px; margin: 0px  auto; display: block;"><br>

                                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px; margin-top: -10px;"><strong>{{$row->timerInfo->total_time}}</strong></p>
                                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"><img src="{{ asset('assets/marker.png') }}" style="border: none; -ms-interpolation-mode: bicubic; max-height: 10px; max-width: 16px; margin: 0px;"> {!!  str_limit(strip_tags($row->timerInfo->location),20) !!}</p>
                                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;"> {{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>

                            </td>
                        </tr>
                        </tbody>

                    </table>
                @endif



                <center>

                    @if($receiverInfo->approval==1)
                        @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                            <a href="{{ url('approveDocket/'.$sentDocket->id.'/'.$receiverInfo->hashKey) }}" target="_blank" style="text-decoration: underline; background-color: #ffffff; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; color: #3498db; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #3498db; border-color: #3498db; color: #ffffff;">
                                Click here to Approve
                            </a>
                        @endif

                    @endif
                    <div class="button-two"><br>
                        <a href="{{ $downloadLink }}" class="secondButton" target="_blank" style="text-decoration: underline; background-color: #ffffff; border: solid 1px #3498db; border-radius: 5px; box-sizing: border-box; color: #3498db; cursor: pointer; display: inline-block; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-decoration: none; text-transform: capitalize; background-color: #3498db; border-color: #3498db; color: #ffffff; margin-left: 20px;">
                            Download
                        </a>
                    </div>
                </center>



            </div>
        </td>
    </tr>
    <!-- END MAIN CONTENT AREA -->
</table>
</body>
</html>
