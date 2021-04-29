<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Record Time</title>
    <link rel="shortcut icon" type="image/png" href="https://www.recordtimeapp.com.au/images/favicon.png"/>


    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    {{ Html::style('assets/website/emailDocket/view.css') }}

    <script>
        window.Laravel = {!! json_encode([ 'csrfToken' => csrf_token()]) !!};
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
    <style type="text/css">
        .signature-pad{
            border: 1px solid #e8e8e8;
            background-color: #fff;
            border-radius: 4px;

        }
        @media only screen and (max-width: 620px) {
            .docket-details{
                text-align: left !important;
            }
            .docket-details>div{
                text-align: left!important;
                float: left !important;
                margin-top: 20px;
            }
        }
        .printColor {
            padding: 12px;
            display: block;
            background-color: #eee !important;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="preview">
            <div class="docket-header">
                <div class="row">
                    <div class="col-md-12">
                        <div style="border-bottom: 1px solid #ddd;padding-bottom: 20px;margin-bottom: 15px;text-align: right;">
                            <a class="btn btn-primary btn-sm" href="{{ url('invoice/emailed/'.$id.'/download') }}">
                                <i class="fa fa-download" aria-hidden="true"></i> Download
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        @if(AmazoneBucket::fileExist($sentInvoice->company_logo))
                            <img src="{{ AmazoneBucket::url() }}{{ $sentInvoice->company_logo }}" style="max-width: 100%;max-height:150px;" class="company-logo">
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=logo" class="company-logo">
                        @endif
                        <br/>From:<br/>
                            <strong>{{ $sentInvoice->sender_name }}</strong><br>
                            {{ @$sentInvoice->company_name }}<br/>
                            {{ @$sentInvoice->company_address }}<br>
                            <b>ABN:</b> {{ @$sentInvoice->abn }}
                            <br/><br/>
                            To:<br/>
                            <strong>{{ $sentInvoice->receiverInfo->email }}</strong> <br>
                            @if($sentInvoice->receiver_full_name!="")<span>{{ $sentInvoice->receiver_full_name }}</span><br/> @endif
                            @if($sentInvoice->receiver_company_name!="")
                                <span>{{ $sentInvoice->receiver_company_name }}</span><br/>
                                @if($sentInvoice->receiver_company_address!="")<span>{{ $sentInvoice->receiver_company_address }}</span><br/> @endif
                            @endif
                    </div>
                    <div class="col-md-6 text-right docket-details">
                        <div class="text-left float-right">
                            <strong>{{ $sentInvoice->template_title }}</strong><br/>
                            <b>Date:</b> {{ $sentInvoice->formattedCreatedDate() }}<br/>
                            <b>Invoice ID:</b>  {{ $sentInvoice->formatted_id }}<br>
                        </div>
                    </div>
                </div>
            </div>
            <div class="docket-body">
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Description</div></th>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Amount</div></th>
                            </tr>
                            </thead>
                            <tbody>
                                @include('website.emailInvoice.modularField.default')
                                @include('website.emailInvoice.modularField.attachedDocket')
                                @include('website.emailInvoice.modularField.invoiceDescription')
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th class="printTh p-0"><div class="printColor">Sub Total</div></th>
                                    @php
                                        $totalAmount=0;
                                        foreach($sentInvoice->invoiceDescription as $item){ $totalAmount += $item["amount"]; }
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
                                    <th class="printTh p-0"><div class="printColor">$ {{ round($subtotal,2) }}</div></th>
                                </tr>
                                @if($sentInvoice->gst!= 0)
                                    <tr>
                                        <th class="printTh p-0"><div class="printColor">{{ $sentInvoice->invoiceInfo->gst_label }}</div></th>
                                        <th class="printTh p-0"><div class="printColor">{{ $sentInvoice->gst }} %</div></th>
                                    </tr>
                                @endif
                                <tr>
                                    <th class="printTh p-0"><div class="printColor">Total</div></th>
                                    <th class="printTh p-0"><div class="printColor">
                                        $ @if($sentInvoice->gst!= 0) {{$subtotal + $subtotal*$sentInvoice->gst/100 }}
                                            @else {{ round($subtotal,2) }} @endif</div>
                                    </th>
                                </tr>

                                @include('website.emailInvoice.modularField.signature')
                            </tfoot>
                        </table><!--/.docket-table-value-->

                        @if($sentInvoice->paymentDetails)
                            @include('website.emailInvoice.paymentDetails')
                        @endif
                        @include('website.emailInvoice.modularField.image')

                        @if( $sentInvoice->isDocketAttached==1)
                            <strong>Dockets</strong>
                            @if($sentInvoice->attachedEmailDocketsInfo)
                                @foreach($sentInvoice->attachedEmailDocketsInfo as $row)
                                    @php $emailDocket = $row->docketInfo @endphp
                                    @include('website.emailInvoice.attachedDocket')
                                @endforeach
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>