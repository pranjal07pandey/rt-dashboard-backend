@extends('layouts.shareableMaster')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Invoice Manager
            <small>Add/View Invoice</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-folder"></i> Folder</a></li>
            <li class="active">View</li>
        </ol>
    </section>
    @include('dashboard.company.include.flashMessages')
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
                <div class="col-md-6 text-left">
                    <button  class="btn btn-default btn-sm" onclick="goBack()" style="margin:0px;"><i class="fa fa-reply"></i> Back</button>
                </div>
                <div class="col-md-6 text-right">
                    <button  class="btn btn-default btn-sm"  onclick="location.href='{{url('folder/invoice/download/emailed/'.$invoice->encryptedID())}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download"></i> Download</button>
                    <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>
            <div id="printContainer">
                <div class="row invoice-info">
                    <div class="col-md-4 invoice-col">
                        @if(AmazoneBucket::fileExist($invoice->company_logo))
                            <img src="{{ AmazoneBucket::url() }}{{ $invoice->company_logo }}" style="height:150px;">
                        @else
                            <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                        @endif

                        <br/>
                        From:<br/>
                        <strong>{{ $invoice->senderUserInfo->first_name }} {{ $invoice->senderUserInfo->last_name }}</strong><br>
                        {{ @$invoice->senderCompanyInfo->name }}<br/>
                        {{ @$invoice->senderCompanyInfo->address }}<br>
                        <b>ABN:</b> {{ @$invoice->senderCompanyInfo->abn }}
                        <br/><br/>
                        To:<br/>
                        <strong>{{ $invoice->receiverInfo->email }}</strong> <br>
                        @if($invoice->receiver_full_name!="")<span>{{ $invoice->receiver_full_name }}</span><br/> @endif
                        @if($invoice->receiver_company_name!="")
                            <span>{{ $invoice->receiver_company_name }}</span><br/>
                            @if($invoice->receiver_company_address!="")<span>{{ $invoice->receiver_company_address }}</span><br/> @endif
                        @endif
                    </div>
                    <!-- /.col -->

                    <div class="pull-right" style="text-align:left;width:140px;">
                        <strong>Tax Invoice</strong><br/>
                        <strong>Date</strong>: {{ \Carbon\Carbon::parse($invoice->created_at)->format('d-M-Y') }}<br/>
                        <strong>Invoice</strong>: {{ $invoice->formatted_id }}<br/>
                    </div>
                    <!-- /.col -->
                </div>

                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <br/>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="printTh"><div class="printColorDark">Description</div></th>
                                <th class="printTh"><div class="printColorDark">Value/Amount</div></th>
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

                            @if($invoice->isDocketAttached==1)
                                @if($invoice->attachedEmailDocketsInfo->count()>0)
                                    @foreach($invoice->attachedEmailDocketsInfo as $invoiceDocket)
                                        <tr>
                                            <td>
                                                <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                                                <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                                                Docket :  {{ $invoiceDocket->docketInfo->formatted_id }}<br/>

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
                            <tfoot>
                            <tr>
                                <th class="printTh"><div class="printColor">Sub Total</div></th>



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



                                <th class="printTh"><div class="printColor">$ {{ round($subtotal,2) }}</div></th>
                            </tr>
                            @if($invoice->gst!= 0)
                                <tr>
                                    <th class="printTh"><div class="printColor">{{ $invoice->invoiceInfo->gst_label }}</div></th>
                                    <th class="printTh"><div class="printColor">{{ $invoice->gst }} %</div></th>
                                </tr>
                            @endif
                            <tr>
                                <th class="printTh"><div class="printColor">Total</div></th>
                                <th class="printTh"><div class="printColor">
                                        $
                                        @if($invoice->gst!= 0)
                                            {{$subtotal + $subtotal*$invoice->gst/100 }}
                                        @else
                                            {{ round($subtotal,2) }}
                                        @endif
                                    </div>
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
                                        @php $sn++; @endphp
                                    @endif
                                @endforeach
                            @endif
                            </tfoot>
                        </table>

                        @if($invoiceSetting)
                            <br/>
                            <table  class="table table-striped">
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
                    </div>
                </div>




                @if( $invoice->isDocketAttached==1)

                    @if($invoice->attachedEmailDocketsInfo)
                        @foreach($invoice->attachedEmailDocketsInfo as $row)
                            <hr>
                            <div class="pageBreak"></div>
                            <div class="docket">
                                <strong class="docketId">{{ $row->docketInfo->formatted_id }}</strong>
                                <div class="divWrapper">
                                    <div style="width:65%;float:left;margin-bottom: 10px;">
                                        <strong>{{  $row->docketInfo->senderCompanyInfo->name  }}</strong><br/>
                                        <span>{{  $row->docketInfo->senderCompanyInfo->address }}</span><br/><br/>

                                        <strong>From</strong> : <span style="text-decoration: dotted">
                                            {{ @$row->docketInfo->senderUserInfo->first_name }} {{ @$row->docketInfo->senderUserInfo->last_name }}
                                        </span>
                                    </div>
                                    <div style="width:35%;float:left;font-size: 12px;text-align: right;">
                                        <strong>Date</strong>: {{ \Carbon\Carbon::parse($row->docketInfo->created_at)->format('d-M-Y') }}<br/>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div>
                                        <br/>
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
                                        <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable table table-striped">
                                            <thead>
                                            <tr style="background:#ddd;text-align:left;width:100%;">
                                                <th width="50%">Description</th>
                                                <th width="50%">Value/Amount</th>
                                            </tr>
                                            </thead>
                                            <tbody style="background-color: #fff;">
                                            @if($row->docketInfo->sentDocketValue)
                                                @foreach($row->docketInfo->sentDocketValue as $item)
                                                    @if($item->docketFieldInfo->docket_field_category_id==5 || $item->docketFieldInfo->docket_field_category_id==9 )
                                                        <tr>
                                                            <td colspan="2">
                                                                {{ $item->label }}<br/>
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
                                                                {{ $item->label }}
                                                            </td>
                                                            <td>
                                                                @if($item->value==1)<img src="{{ asset('assets/dashboard/img/checked.png') }}" width="15px">
                                                                @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="15px">@endif
                                                            </td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==15)
                                                        <tr>
                                                            <td> {{ $item->label }}
                                                                <ul class="pdf">
                                                                    @foreach($item->sentDocketAttachment as $document)
                                                                        <li><img src="{{ asset('assets/pdf.png') }}"><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></li>
                                                                    @endforeach
                                                                </ul>
                                                            </td>
                                                            <td> {{ $item->value }}</td>
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
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==12)
                                                        <tr>
                                                            <td  colspan="2"> <strong>{{ $item->label }}</strong></td>
                                                        </tr>
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==18)
                                                        <tr>
                                                            <td colspan="2">
                                                                <!--<table style="width:100%;">-->
                                                                <!--<tr>-->
                                                                @php
                                                                    $yesno = unserialize($item->label);
                                                                @endphp
                                                                <div style="width:100%;margin:0;">
                                                                    <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
                                                                    @if(@$item->value == "N/a")
                                                                        <div style="width:50%; float:right;margin-right: -9px;"> N/a </div>
                                                                    @else
                                                                        @if(@$yesno['label_value'][$item->value]['label_type']==1)
                                                                            <div style="width:50%; float:right;margin-right: -9px;"><img style="width: 20px; height:20px;padding:4px; background-color:{{ $yesno['label_value'][$item->value]['colour']}}; border-radius:20px;" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$item->value]['label'] }}"></div>
                                                                        @else
                                                                            <div style="width:50%; float:right;margin-right: -9px;">{{ @$yesno['label_value'][$item->value]['label']}}</div>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <!-- </tr>-->
                                                                <!--</table>-->

                                                                @if($item->SentDocValYesNoValueInfo)
                                                                    <table style="background: transparent; width: 100%;" class="table table-striped">
                                                                        <thead style="background: transparent; ">
                                                                        <tr>
                                                                            <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody >

                                                                        @foreach($item->SentDocValYesNoValueInfo as $items)
                                                                            @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                                                @php
                                                                                    $imageData=unserialize($items->value);
                                                                                @endphp
                                                                                <tr>
                                                                                    <td style="width:50%;">{{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
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
                                                    @elseif($item->docketFieldInfo->docket_field_category_id==13)
                                                        @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
                                                    @elseif($item->docketFieldInfo->docket_field_category_id!=13 && $item->docketFieldInfo->docket_field_category_id!=18)
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
                                {{--</div><!--/.divWrapper-->--}}
                            </div>
                        @endforeach
                    @endif
                @endif
            </div>
        </div>
    </div>
    <br>
    <br>






    <style>
        .printColorDark{ padding: 8px;display:block;background-color: #ddd !important;-webkit-print-color-adjust: exact;}
        .printTh{ padding: 0px !important; }
        .printColor{padding: 8px;display:block;background-color: #eee !important;-webkit-print-color-adjust: exact;}
    </style>
@endsection()

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
    <script>
        function goBack() {
            if (document.referrer == "") {
                window.location = "{{ url('dashboard/company/invoiceManager/allInvoice') }}";
            } else {
                history.back()
            }
        }
    </script>
@endsection
