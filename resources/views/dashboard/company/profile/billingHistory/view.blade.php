@extends('layouts.companyDashboard')

@section('content')
    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> Billing
            <small>History</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ route('companyProfile') }}">Profile</a></li>
            <li class="active">Billing History</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;border: 1px solid #ddd;min-height: 400px;">

        <div class="col-md-12">
            <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
                <div class="col-md-6 text-left">
                    <a class="btn btn-default btn-sm" href="{{ route('Company.billingHistory') }}" style="margin:0px;"><i class="fa fa-reply"></i> Back</a>
                </div>
                <div class="col-md-6 text-right">
                    <button  class="btn btn-default btn-sm" onclick="location.href='{{url('dashboard/company/profile/billingHistory/downloadInvoice/'.$stripeInvoice->id)}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download" aria-hidden="true"></i></i> Download</button>
                    <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>

            <div id="printContainer">
                <section class="box-email-part">
                    <div class="main-logo-part">
                        <div class="logo-header-toppart">
                            <img src="{{ asset('assets/dashboard/images/rtLogo.png') }}">
                        </div>
                    </div>
                    <div class="content-header-partrt">
                        <h4>Record Time PTY LTD.</h4>
                        <p>Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA</p>
                        <p>ABN : 99 604 582 649, Tel : +61 421 955 630, Email : info@recordtime.com.au</p>
                    </div>
                    <div class="box-afterheaderrt">
                        <div class="left-head">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="tax-invoice-bgrt">
                                        <h4>Billing To:</h4>
                                    </div>
                                </li>
                                <li style="font-weight:font-weight: 500;">{{ $company->name }}</li>
                                <li>{{ $company->address }}</li>
                                <li>{{ $company->userInfo->email }}</li>
                                <li>ABN : {{ $company->abn }}</li>
                            </ul>
                        </div>

                        <div class="right-head">
                            <ul class="list-unstyled">
                                <li>
                                    <div class="tax-invoice-bgrt">
                                        <h4>Tax Invoice</h4>
                                    </div>
                                </li>
                                <li>Invoice Number: #{{ $stripeInvoice->number }}</li>
                                <li>Date:  {{  \Carbon\Carbon::createFromTimestamp($stripeInvoice->date,'Australia/Canberra')->format('d M, Y') }}</li>
                            </ul>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="box-table-centervertilign">
                        <table class="table-rtinvocie">
                            <thead>
                            <tr>
                                <td style="color: #FFF !important;">Description</td>
                                <td style="color: #FFF !important;">qty</td>
                                <td ><span class="pull-right" style="color: #FFF !important;">Amount</span></td>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stripeInvoice->lines['data'] as $data)

                                <tr class="tr-rt-inovice">
                                    <td>
                                        <h4>{{ $data->plan["nickname"] }}</h4>
                                        <p>
                                            @php
                                                $periodStart =   \Carbon\Carbon::createFromTimestamp($data->period['start'],'Australia/Canberra');
                                                $periodEnd  =   \Carbon\Carbon::createFromTimestamp($data->period['end'],'Australia/Canberra');
                                            @endphp

                                            {{ $periodStart->format('M d') }} @if($periodStart->format('Y')!=$periodEnd->format('Y')) , {{ $periodStart->format('Y') }} @endif
                                            - {{ $periodEnd->format('M d')  }} @if($periodStart->format('Y')!=$periodEnd->format('Y')), {{ $periodEnd->format('Y') }}
                                            @else
                                                , {{  \Carbon\Carbon::createFromTimestamp($data->period['end'],'Australia/Canberra')->format('Y') }}
                                            @endif
                                        </p>
                                    <td>
                                        1
                                    </td>
                                    <td>
                                        <span class="pull-right amount">$ {{ sprintf('%0.2f',$data->amount/110) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td class="border-rt-inovice" style="background: #f7fafc !important;-webkit-print-color-adjust: exact;">Subtotal</td>
                                <td class="border-rt-inovice" style="background: #f7fafc !important;-webkit-print-color-adjust: exact;">
                                    <span class="pull-right amount">$ {{ sprintf('%0.2f',round($stripeInvoice->total/110,2)) }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td class="border-rt-inovice">GST</td>
                                <td class="border-rt-inovice">
                                    <span class="pull-right amount">$ {{ sprintf('%0.2f',$stripeInvoice->total/100-round($stripeInvoice->total/110,2)) }}</span>
                                </td>
                            </tr>

                            <tr>
                                <td></td>
                                <td class="amount" style="background: #dae4ea !important;-webkit-print-color-adjust: exact;">Total</td>
                                <td style="background: #dae4ea !important;-webkit-print-color-adjust: exact;">
                                    <span class="pull-right amount"> $ {{  sprintf('%0.2f',round($stripeInvoice->total/100),2) }}</span>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <div class="aftertable-rt">
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Payment Method</h4>
                                <img src="{{ asset('assets/dashboard/images/stripeLogo.png') }}" class="payment-logort">
                            </div>
                            <div class="col-md-6">
                                <p>If you have any questions, Contact <b>Record TIme pty ltd</b> at <b>info@recordtime.com.au</b> or call at <b>+61 421 955 630</b></p>
                            </div>
                        </div>

                    </div>
                </section>
            </div>
        </div>
    </div><br/><br/>

    <style>
        .box-email-part{
            min-height: 800px;
            position: relative;
        }
        .main-logo-part{
            width: 100px;
            position: relative;
            margin: 0px auto;
        }
        .logo-header-toppart{
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: #1a364e !important;
            -webkit-print-color-adjust: exact;
            padding: 1px;
        }
        .logo-header-toppart img{
            margin: 20px auto;
            display: block;
            width: 60%;
        }
        .content-header-partrt{
            width: 100%;
            margin-top: 5px;
            min-height: 80px;
            text-align: center;
        }
        .content-header-partrt h4{
            font-size: 20px;
            margin-top: 0px;
            margin-bottom: 0px;
            text-transform: uppercase;
            font-weight: 500;
            color: #2b2d50;
        }
        .content-header-partrt p{
            margin: 3px 0px;
        }
        .box-afterheaderrt{
            width: 100%;
            padding: 10px 15px 10px;
            background: #f7fafc!important;
            -webkit-print-color-adjust: exact;
            margin-top: 20px;
            border-bottom: 1px solid #e8e8e8;
        }
        .tax-invoice-bgrt{
            color: #1a364e;
            clear: both;
            font-weight: bold;
        }
        .tax-invoice-bgrt h4{
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
            font-weight: bold;
            line-height: 2em;
        }
        .left-head{
            width: 50%;
            float: left;
        }
        .right-head{
            float: right;
        }
        .list-unstyled{
            list-style: none;
        }
        .list-unstyled li{
            color: #2b2d50;
            line-height: 1.7em;
            font-size: 14px;

        }
        .list-unstyled li h3,h4,p {
            margin: 0px;
        }

        .table-rtinvocie{
            width: 100%;
            margin: 0px auto;
        }
        .table-rtinvocie thead{
            font-weight: bold;
            color: #FFF !important;
            background: #0c3355 !important;
            -webkit-print-color-adjust: exact;
        }
        .box-table-centervertilign{
            padding-top: 20px;
        }

        table {
            border-collapse: collapse;
            border-spacing: 0;
            margin-bottom: 20px;
        }
        table thead>tr>td{
            border-bottom: 1px solid #e4e4e4;
            text-transform: uppercase;
            padding: 15px 15px !important;
        }
        table th,
        table td {
            padding: 10px;
        }
        .pull-right{
            float: right;
        }
        .tr-rt-inovice td{
            border-bottom: 1px solid #e4e4e4;
            padding: 24px 10px;
        }
        .tr-rt-inovice>td>h4{
            margin-bottom: 10px;
            color: #2e3052;
            font-weight: 500;
            font-size: 14px;
        }
        .border-rt-inovice{
            border-bottom: 1px solid #e4e4e4;
        }
        .invoice-code{
            float: right;
            padding-top: 13px;
        }
        .aftertable-rt{
            width: 100%;
            height: 60px;
            border-top: 1px solid #e4e4e4;
            margin-top: 60px;
            padding: 15px 0px;
        }
        .aftertable-rt h4{
            font-weight: 500;
            color: #000000;
            display: inline-block;
            font-size: 14px;
        }
        .payment-logort{
            height: 20px;
            margin-left: 10px;
        }
        .footer-last{
            padding: 20px 30px;
            border-top: 1px solid #000;
            margin-top: 35px;
        }
        .footer-last p{
            width: 80%;
            margin: 0px auto;
            text-align: center;

        }
        .amount{
            font-weight: 500;
            color: #2b2d50;
        }
    </style>
@endsection

@section('customScript')
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
        });
    </script>
@endsection