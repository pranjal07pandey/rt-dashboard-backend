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
        <div id="printContainer">
          <header class="table-header">
            <table class="table-1">
              <tr>
                <td>
                  @if(AmazoneBucket::fileExist(@$sentInvoice->senderCompanyInfo->logo))
                      <img src="{{ AmazoneBucket::url() }}{{ @$sentInvoice->senderCompanyInfo->logo }}">
                  @else
                      <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                  @endif
                </td>

                <td>
                  <p class="bold">{{ @$sentInvoice->senderCompanyInfo->name }}</p>
                  <p class="small">{{ @$sentInvoice->senderCompanyInfo->address }}</p>
                </td>
                  <ul class="list-unstyled">
                    <li></li>
                    <li><b>ABN:</b> {{ @$sentInvoice->senderCompanyInfo->abn }}</li>
                    <li><span>{{ @$sentInvoice->senderUserInfo->email }}</span></li>
                  </ul>
              </tr>
            </table>
          </header>

          <div class="box-a">
              <div class="box-b">
                <p>INVOICE</p>
              </div>
          </div>
          <div class="invoice-header">
            <table class="invoice-r">
              <tr>
                <td class="invoice-one">
                  <p class="invoice-to">Invoice To :</p>
                  <p class="invoice-bold">{{ $sentInvoice->receiverUserInfo->first_name }} {{ $sentInvoice->receiverUserInfo->last_name }}</p>
                  <p class="invoice-p">{{ @$sentInvoice->receiverCompanyInfo->address }}</p>
                  <p>{{ $sentInvoice->receiverUserInfo->email }}</p>
                </td>

                <!-- <td class="invoice-td"></td> -->
                
                <td class="invoice-b">
                  <p>Invoice :</p>
                  <p>Date :</p>
                </td>
                <td class="invoice-c">
                  <p>{{ $sentInvoice->id }}</p>
                  <p>{{ \Carbon\Carbon::parse($sentInvoice->created_at)->format('d-M-Y') }}</p>
                </td>
              </tr>
            </table>
          </div>

          <div class="invoice-table">
            <table class="responsive-table">
              <tr class="head-black">
                <th>SN</th>
                <th class="description-th">Description</th>
                <th>Value/Amount</th>
              </tr>
              <?php $i=1 ?>
                @if($sentInvoiceValue)
                  @foreach($sentInvoiceValue as $item)
                    @if($item["invoice_field_category_id"]!=9 && $item["invoice_field_category_id"]!=12 && $item["invoice_field_category_id"]!=5)
                      <tr>
                        <td><?php echo $i++ ?></td>
                        <td>
                          <p class="docket"> {{ $item["label"] }}</p> 
                          <p>{{ $item["value"] }}</p></td>
                        </td>
                        <td></td>
                      </tr>
                    @endif
                  @endforeach
                @endif
                @if($sentInvoice->isDocketAttached==1)
                  @if($sentInvoice->attachedDocketsInfo->count()>0)
                    @foreach($sentInvoice->attachedDocketsInfo as $invoiceDocket)
                      <tr>
                        <td><?php echo $i++ ?></td>
                        <td>
                          <p class="docket">{{ $invoiceDocket->docketInfo->docketInfo->title }}</p>
                          <p>Docket :  #Doc{{ $invoiceDocket->docketInfo->id }}</p>
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
                          $ {{ $invoiceAmount}}
                        </td>
                      </tr>
                    @endforeach
                  @endif
                @endif
            </table>
          </div>


          <table class="sub">
            <tr>
              <td class="bold-same">Thank you for your business</td>
              <td>
                <table class="sub-one">
                  <tr>
                    <td class="bold-same">Sub Total:</td>
                    <td class="sub-r">$ {{ round($sentInvoice->amount,2) }}</td>
                  </tr>
                  <tr>
                    <td class="bold-same">{{ $sentInvoice->invoiceInfo->gst_label }}:</td>
                    <td class="sub-r">$ {{ round($sentInvoice->amount*$sentInvoice->gst/100,2)  }}</td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

          <table class="terms-condition">
            <tr>
              <td>
                {{-- <p class="bold-same">Terms & Conditions</p>
                <p>Lorem ipsum dolor sit amet, consectur adipiscing elit.<br>d Fusce dignissim pretium consectur. </p> --}}
              </td>

              <td>
                <table class="yellow">
                  <tr>
                    <td>Total:</td>
                    <td class="yello-r">
                      @if($sentInvoice->gst!= 0)
                        $ {{ $sentInvoice->amount + $sentInvoice->amount*$sentInvoice->gst/100 }}
                      @else
                        $ {{ round($sentInvoice->amount,2) }}
                      @endif
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          @if($invoiceSetting)
          <div class="payment" style="padding-top: 10px">
            <table class="payment-table ">
              <tr>
                <th colspan="2">PAYMENT DETAILS</th>
              </tr>
              <tr>
                  <td>Bank Name</td>
                  <td>{{ $invoiceSetting->bank_name }}</td>
              </tr>
              <tr>
                  <td>Account Name</td>
                  <td>{{ $invoiceSetting->account_name }}</td>
              </tr>
              <tr>
                  <td>BSB Number</td>
                  <td>{{ $invoiceSetting->bsb_number }}</td>
              </tr>
              <tr>
                  <td>Account Number</td>
                  <td>{{ $invoiceSetting->account_number }}</td>
              </tr>
            </table>
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
          <div class="phone-address">
            <div class="signature">
              <p class="bold-samenot"></p>
            </div>
          </div>
          <!-- attached-doc -->
          @if( $sentInvoice->isDocketAttached==1)
            @if($sentInvoice->attachedDocketsInfo)
              @foreach($sentInvoice->attachedDocketsInfo as $row)
              <div class="attached-doc">
                <div class="docket-bar">
                  <p>#Doc{{ $row->docketInfo->id }}<span>Date:{{ \Carbon\Carbon::parse($row->docketInfo->created_at)->format('d-M-Y') }}</span></p>
                </div>
                <div class="logo-bar">
                  @if(AmazoneBucket::fileExist(@$sentInvoice->senderCompanyInfo->logo))
                      <img src="{{ AmazoneBucket::url() }}{{ @$sentInvoice->senderCompanyInfo->logo }}">
                  @else
                      <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                  @endif
                  <ul class="list-item">
                    <li class="web-app">{{  $row->docketInfo->senderCompanyInfo->name  }}</li>
                    <li>{{  $row->docketInfo->senderCompanyInfo->address  }}</li>
                    <li><b>ABN:</b>{{  $row->docketInfo->senderCompanyInfo->abn  }}</li>
                    <li class="mail-arjun">{{ $row->docketInfo->senderUserInfo->email }}</li>
                  </ul>
                </div>
                <div class="line-bar">
                </div>
                <div class="docket-r">
                  <ul class="invoice-t">
                    <li class="invoice-arjun">DOCKET TO:</li>
                    @if($row->docketInfo->recipientInfo)
                        <?php $sn = 1; ?>
                        @foreach($row->docketInfo->recipientInfo as $recipient)
                            <li class="from-arjun">{{ $recipient->userInfo->first_name }} {{ $recipient->userInfo->last_name }}</li>
                            @if($sn!=$row->docketInfo->recipientInfo->count())
                                ,
                            @endif
                            <?php $sn++; ?>
                        @endforeach
                    @endif
                    {{-- <li class="from-arjun">Arjun Dangal</li>
                    <li>Pepsicola, kathmandu</li>
                    <li class="mail-arjun">arjundangal@gmail.com</li> --}}
                  </ul>

                  <ul class="invoice-f">
                    <li class="invoice-arjun">DOCKET FROM:</li>
                    <li class="from-arjun">{{ $row->docketInfo->senderUserInfo->first_name }} {{ $row->docketInfo->senderUserInfo->last_name }}</li>
                    <li>{{  $row->docketInfo->senderCompanyInfo->address  }}</li>
                    <li class="mail-arjun">{{ $row->docketInfo->senderUserInfo->name }}</li>
                  </ul>
                </div>

                <div class="docket-description">
                  <table class="table-des">
                    <thead>
                      <tr>
                        <th>DESCRIPTION</th>
                        <th>VALUE/AMOUNT</th>
                      </tr>
                    </thead>
                    <tbody>
                      @if($row->docketInfo->sentDocketValue)
                        @foreach($row->docketInfo->sentDocketValue as $item)
                            @if($item->docketFieldInfo->docket_field_category_id==5 || $item->docketFieldInfo->docket_field_category_id==9 )
                                <tr>
                                    <td colspan="2">
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
                            @elseif($item->docketFieldInfo->docket_field_category_id==13)
                                @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
                            @elseif($item->docketFieldInfo->docket_field_category_id!=13)
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

                <div class="bottom-line">
                </div>
              </div>
              @endforeach
            @endif
          @endif
          <!-- attached-doc end -->
          <footer>
            Docket was created on Recordtime.
          </footer>
        </div>
      </div>
    </div>
    <br /><br />
    <style type="text/css">


      a {
        color: #0087C3;
        text-decoration: none;
      }
      .table-header{
        padding-top: 20px;
      }
      .table-header img{
        float: right;
        width: 100px;
        padding-right: 15px;
      }

      table{
        border-spacing: 0;
      }

      .full-width span{
        color: blue;
      }
      .full-width{
        width: 100%;
      }
      .list-unstyled b{
        font-size: 16px;
      }
      ul.list-unstyled{
        float: right;
      }
      ul.list-unstyled li{
        line-height: 1.3rem;
        text-align: right;
        list-style: none;
      }
      .bold{
        margin-top: 15px;
        font-size: 25px;
        font-weight: bold;
        line-height: 1;

      }
      .small{
        line-height: 0;
        font-size:10px;
        letter-spacing:3px;
        font-weight: bold;
      }
      .y-line{
        margin-top: 20px;
        width: 100%;
        border:20px solid #fab930;
      }
      .box-a{
        margin-top: 30px;
        width: 100%;
          position: relative;
          background-color: #fab930;
          height: 50px;
      }
      .box-b{
        position: absolute;
          right: 120px;
          width: 200px;
          height: 63px;
          background: #fff;
          text-align: center;
          top: 0px;
      }
      .box-a p{
        background-color: #ffffff;
        font-size: 46px;
        margin-top: -10px;
      }
      .box{
        margin-right: 100px;
          float: right;
          color: #000000;
          font-weight: bold;
          font-size: 51px;
          background-color: #ffffff;
          margin-top: -101px;
          padding: 0px 10px 0px 10px;
      }
      .invoice-header{
        padding-top: 20px;
      }
      .invoice-r td{
        width: calc('100%/4');
      }
      .invoice-td{
        width: calc('100%/4')
      }
      .invoice-to{
        font-weight: bold;
        font-size: 20px;
        line-height:1em;
      }
      .invoice-bold{
        font-size: 16px;
        font-weight: bold;
        line-height: 1px;
      }
      .invoice-r{
        width: 100%;
      }
      .invoice-b{
        font-size: 16px;
        font-weight: bold;
        line-height: 4px;
      }
      .invoice-one p{
        line-height: 1.5rem;
      }
      .invoice-b p{
        line-height: 1rem;
      }
      .invoice-c p{
        text-align: right;
        font-weight: normal;
        font-size: 16px;
      }
      .responsive-table tr:nth-child(even){background-color: #f2f2f2}

      .invoice-table table{
        border:2px solid #80808047;
        width: 100%;
      }
      .invoice-table th{
        color: #ffffff;
        padding: 10px 0px 10px 10px;
        background-color: #000000;
      }
      .responsive-table td{
        padding: 0px 0px 0px 10px;
        text-align: center;
      }
      .invoice-table p{
        text-align: left;
      }
      .description-th{
        text-align: left;
      }
      .head-black{
        background-color: #000000;
      }
      .docket{
        color: #c28403;
      }
      .sub{
        padding-top: 20px;
        width: 100%;
      }
      .sub td{
        width: calc('100%/2');
      }
      .sub-one{
        width: 100%;
      }
      .sub-r{
        float: right;
      }
      .terms-condition{
        padding-top: 15px;
        width: 100%;
      }
      .terms-condition td{
        width: 60%;
      }
      .yellow{
        padding-left:10px ;
        padding-top: 10px;
        padding-bottom: 10px;
        background-color: #fab930;
        width: 100%;
        left: 0;
      }
      .yellow td{
        width: calc('100%/2');
      }
      .yello-r{
        padding-right: 10px;
        text-align: right;
      }
      /*payment table*/
      table.payment-table{
        margin top: 15px;
        margin-bottom: 15px;
        border-collapse: collapse;
        background-color: #f2f2f2;
        padding: 10px;
        width: 100%;
      }
      table.payment-table th{
        border:1px solid #ffffff;
        padding:15px 0px 15px 10px;
        text-align: left;
      }
      table.payment-table td{
        border:1px solid #ffffff;
        padding:10px 0px 10px 10px;
        text-align: left;
      }
      /*payment table end*/

      /*.additional-images{
        padding-left: 15px;
        padding-right: 15px;
      }*/
      .additional-images img{
        width: 150px;
        height: 100px;
        padding-right: 15px;
      }
      .image-title p{
        font-weight: bold;
      }

      .signature-p{
        right: 0;
        width: 100px;
        float: right;
      }
      .phone-address{
        margin-top: 30px;
        padding-top: 20px;
        border:3px solid #fab930;
        border-left: none;
        border-right: none;
        border-bottom: none;
      }
      .phone-address td{
        font-weight: bold;
      }
      /*signature*/
      .signature{
        background-color: #ffffff;
        padding-left: 30px;
        padding-right: 30px;
        float: right;
        margin-right: 200px;
        margin-top: -24px;
      }
      .signature p{
        background-color: #ffffff;
        width: 100px;
        border-top:3px solid #0000005c;
        float: right;
      }
      .signature-p p{
        text-align: center;
      }
      .signature-p img{
        float: right;
        width: 80px;
        height: 50px;
      }
      td.signature-side{
        width: 100%;
      }
      /*signature end*/
      .bold-samenot{
        line-height: 2rem;
      }


      /*attached-doc */
      .attached-doc{
        margin-top: 100px;
        width: 100%;
      }
      .docket-bar{
        background-color: #000000;
        padding: 10px 10px 10px 10px;
        color: #ffffff;
        font-size: 16px;
        font-weight: bold;
      }
      .docket-bar span{
        float: right;
      }

      .logo-bar img{
        padding-top: 20px;
          position: absolute;
          float: left;
          width: 80px;
          margin-bottom: 15px;
      }
      .web-app{
        font-size: 16px;
      }
      .docket-r li{
        line-height: 2.5rem;
      }
      .list-item{
        padding-top: 10px;
        line-height: 2.5rem;
        position: relative;
        text-align: right;
        list-style: none;
        float: right;
      }
      .line-bar{
        margin-top: 120px;
          height: 1px;
          width: 100%;
          background-color: #f4c800;
      }
      .invoice-t{
        padding-left: 0;
        position: absolute;
          list-style: none;
      }
      .invoice-f{
        position: relative;
          list-style: none;
          text-align: right;
      }
      li.from-arjun{
        font-size: 18px;
      }
      .invoice-arjun{
        color: gray;
        font-size: 12px;
      }
      .mail-arjun{
        color: #d4ae07;
      }
      table.table-des{
        background-color: #f2f2f2;
        width: 100%;
        margin: 0;
        padding:0px 15px 0px 15px;
        
      }
      table.table-des th{
        border-bottom: 1px solid #ffffff;
        text-align: left;
        padding: 30px 0px 30px 0px;
      }
      table.table-des td{
        border-bottom: 1px solid #ffffff;
        text-align: left;
        padding: 15px 0px 15px 0px;
      }
      table.table-des thead>tr{
        border-bottom: 1px solid #ffffff;
      }
      .bottom-line{
        margin-top: 80px;
          height: 1px;
          width: 100%;
          background-color: #000000;
      }
      /*attached-doc end */

      /*additional information*/
      .additional-info{
        margin-top: 30px;
      }
      .top-info{
        padding-bottom: 15px;
      }
      .some-info{
        padding-bottom: 15px;
      }
      /*additional information end*/

      /*image list*/
      .image-dis{
          padding-bottom: 30px;
      }
      .image-list{
        position: absolute;
        padding-left: 0;
      }
      .image-list li{
        text-decoration: none;
        display: inline;
        list-style: none;
        margin-left: auto;
        margin-right: auto;
      }
      .image-list img{
        padding-right: 10px;
        width: 150px;
        height: 100px;
      }
      .signature-11{
        text-align: right;
        width: 200px;
        float: right;
      }
      .signature-11 img{
        width: 60px;
        float: right;
      }
      /*image list end*/

      footer{
        padding-top: 200px;
        padding-bottom: 40px;
        text-align: center;

      }

      /*MEDIAQUERY DOT CSS FOR BRAND*/
      @media only screen and (max-width: 320px) {
          .bold {
          margin-top: 15px;
          font-size: 25px;
          font-weight: bold;
          line-height: 0;
        }

        .box-a p {
          background-color: #ffffff;
          font-size: 25px;
          margin: 0px;
        }

        .box-b {
          position: absolute;
          right: 57px;
          width: 129px;
          height: 50px;
          background: #fff;
          text-align: center;
          top: 0px;
        }

        .box-a {
          margin-top: 30px;
          width: 100%;
          position: relative;
          background-color: #fab930;
          height: 25px;
        }
          
      }

      /*MEDIAQUERY DOT CSS END*/

      /*MEDIA QUERY FOR SIMPLE DOT HTML*/
      @media only screen and (max-width: 320px){
          .bill-two{
          font-family: 'Roboto', sans-serif;
          font-size: 14px;
          color: #000000b0;
          text-align: center;
          line-height: 5px;
          padding-bottom: 10px;
          }
          .terms p {
          font-size: 10px;
          text-align: left;
          }
      }

      /*MEDIA QUERY FOR SIMPLE DOT HTML END*/
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