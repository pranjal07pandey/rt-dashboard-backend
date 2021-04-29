@extends('layouts.companyDashboard')
@section('content')
  <section class="content-header">
  	<h1>
      <i class="fa fa fa-file-text-o"></i> Docket Book Manager
      <small>Add/View Docket</small>
  	</h1>
  	<ol class="breadcrumb hidden-sm hidden-xs">
      <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
      <li><a href="#">Docket Book Manager</a></li>
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
              <button  class="btn btn-default btn-sm" onclick="location.href='{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$sentDocket->id)}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download" aria-hidden="true"></i></i> Download</button>
              <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
          </div>
      </div>
      <div id="printContainer">

        <div class="division-four">
            <div class="left-one">
                @if(AmazoneBucket::fileExistt::fileExist(@$sentDocket->senderCompanyInfo->logo))
                    <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->senderCompanyInfo->logo }}">
                @else
                    <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                @endif
            <!--  -->
            </div>
            <div class="right-one">
                <ul>
                  <li class="web-app">{{ @$sentDocket->company_name }}</li>
                  <li >{{ @$sentDocket->company_address }} <i class="fa fa-map-marker" aria-hidden="true"></i></li>
                  <li >ABN:{{ @$sentDocket->abn }}</li>
                  <li >{{ @$sentDocket->senderCompanyInfo->contactNumber }} <i class="fa fa-phone" aria-hidden="true"></i></li>
                </ul>
            </div>
        </div>



        <div class="division-six">
            <ul>
               <li>From:</li>
              <li class="bold-d">{{ @$sentDocket->sender_name }}</li>
              <li>M:{{ @$sentDocket->senderUserInfo->email }}</li>
            </ul>
        </div>


        <div class="division-five">
            <div class="left-two">
                <ul>
                    <li>To:</li>
                    
                          <li><b>  {{$employee_name}}</b>

                           
                            @if($company_name=="")
                            
                            @else
                              <br>
                             <li> <b>Company Name:</b>
                            {{$company_name}}</li>
                            @endif
           
                </ul>
            </div>
            <div class="right-two">
                <div class="aged-care">
                   
                        <p>{{ $sentDocket->docketInfo->title }}</p>
                        <ul>
                          <li><b>Docket ID:</b>&nbsp;<span>{{ $sentDocket->id }}</span></li>
                          <li><b>Date:</b>&nbsp;<span>{{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}</span></li>
                        </ul>
                </div>
            </div>
        </div>
        <div class="amount-value amount-valuedocsix">
          <table cellpadding="0" class="responsive-tab">
            <thead>
              <tr class="">
                <th class="amount-des">DESCRIPTION</th>
                <th class="value-des">VALUE/ AMOUNT</th>
              </tr>
            </thead>
            <tbody class="body-data">
              @if($docketFields)
                @foreach($docketFields as $item)
                  @if($item->docketFieldInfo->docket_field_category_id!=13 && $item->docketFieldInfo->docket_field_category_id!=8 && $item->docketFieldInfo->docket_field_category_id!=12  && $item->docketFieldInfo->docket_field_category_id!=15 && $item->docketFieldInfo->docket_field_category_id!=9 && $item->docketFieldInfo->docket_field_category_id!=5 && $item->docketFieldInfo->docket_field_category_id!=14 && $item->docketFieldInfo->docket_field_category_id!=7)
    					<tr>
    						 <td> {{ $item->label }}</td>
    					 @if($item->value=="")
    						 <td> N/a</td>
    					 @else
    						  <td>{{ $item->value }}</td>
    					 @endif
    					</tr>
				  @endif
                  @if($item->docketFieldInfo->docket_field_category_id==9 )
					 <tr class="image-tit">
						 @if($item->sentDocketImageValue->count()>0)
						  <td colspan="2">
                           <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    							<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
    						  </ul>
                          </td>
						@else
						  <td>
                              <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    								<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
							  </ul>
                            </td>
                          <td> <b>No Signature Attached</b></td>
						@endif
					</tr>
					@endif
					
					@if($item->docketFieldInfo->docket_field_category_id==14 )
					 <tr class="image-tit">
						 @if($item->sentDocketImageValue->count()>0)
						  <td colspan="2">
                           <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    							<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
    						  </ul>
                          </td>
						@else
						  <td>
                              <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    								<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
							  </ul>
                            </td>
                          <td> <b>No Sketchpad Attached</b></td>
						@endif
					</tr>
					@endif
					@if($item->docketFieldInfo->docket_field_category_id==5 )
					 <tr class="image-tit">
						 @if($item->sentDocketImageValue->count()>0)
						  <td colspan="2">
                           <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    							<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
    						  </ul>
                          </td>
						@else
						  <td>
                              <p>{{ $item->label }}</p>
                              <ul class="images-3">
                                @foreach($item->sentDocketImageValue as $signature)
    								<li><img src="{{ AmazoneBucket::url() }}{{ $signature->value }}"></li>
                                @endforeach
							  </ul>
                            </td>
                          <td> <b>No Image Attached</b></td>
						@endif
					</tr>
					@endif
                  @if($item->docketFieldInfo->docket_field_category_id==7)
                    <tr class="table-unitper">
                      <td>
                      <table class="unit-per">
                        <tr>
                          @foreach($item->sentDocketUnitRateValue as $row)
                            <td>{{ $row->docketUnitRateInfo->label }}:{{ $item->sentDocketUnitRateValue->first()->value }}</td>
                          @endforeach
                        </tr>
                        <tr>
                          <td class="bold-d " colspan="2">Total </td>
                        </tr>
                      </table>
                      <td class="bold-d" class="value-des">
                        <?php $total    =    0; ?>
                        $ {{  $item->sentDocketUnitRateValue->first()->value*$item->sentDocketUnitRateValue->last()->value }}
                      </td>
                    </tr>
                  @endif
                  @if($item->docketFieldInfo->docket_field_category_id==8)
                    <tr>
                      <td class="amount-des">{{ $item->label }} </td>
                      <td>
                        <ul class="check-time">
                          @if($item->value==1)
                            <li><a href="#"><i class="fa fa-check-circle" aria-hidden="true"></i></a></li>
                          @else
                            <li><a href="#"><i class="fa fa-times-circle" aria-hidden="true"></i></a></li>
                          @endif
                        </ul>
                      </td>
                    </tr>
                  @endif
                  @if($item->docketFieldInfo->docket_field_category_id==12)
                    <tr>
                      <td class="bold-d" colspan="2" class="amount-des">{{ $item->label }} </td>
                    </tr>
                  @endif
                 @if($item->docketFieldInfo->docket_field_category_id==15)
                             <tr>
                             
                                @if($item->sentDocketAttachment->count()>0)
                                  <td colspan="2" class="amount-des">
                                   <div class="document-doc">
                                    <div class="document-title">
                                      <p>{{ $item->label }}</p>
                                      <ul class="list-unstyled list-inline document-list">
                                        @foreach($item->sentDocketAttachment as $document)
                                          <li>
                                            <a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">
                                              <i class="fa fa-file-text" aria-hidden="true"></i>&nbsp;<span>{{$document->document_name}}</span>
                                            </a>
                                          </li>
                                        @endforeach
                                      </ul>
                                    </div>
                                  </div>
                                 </td>
                               @else
                               <td class="amount-des">
                                   <div class="document-doc">
                                    <div class="document-title">
                                      <p>{{ $item->label }}</p>
                                       </div>
                                  </div>
                                 </td>  
                                 <td>
                                     <b>No Document Attached</b>
                                 </td>
                               @endif
                              </tr>
                              
                              
                            @endif
                  @if($item->docketFieldInfo->docket_field_category_id==13)
                    @php $footerValue = $item->value; $footerLabel  =    $item->label; @endphp
                    @if(@$footerValue)
                      <tr>
                        <td colspan="2" class="amount-des">
                          <div class="terms-condition">
                            <div class="terms-title">
                              <p>{{ $footerLabel }}</p>
                            </div>
                            <p>{{ $footerValue }}</p>
                          </div>
                        </td>
                      </tr>
                    @endif
                  @endif
                 
               
                @endforeach
              @endif
               @if($docketTimer->count()>0)
                    <tr class="attached-timers">
                      <td colspan="2" >
                        <div class="timer-heading">
                          <p><b>Attached Timers</b></p>
                        </div>
                        @php
                            $totalInterval = 0;
                        @endphp
                        @if($docketTimer->count())
                          @foreach($docketTimer as $row)
                            <div class="timer-wrapper">
                              <div class="timer-box">
                                <div class="timer-left">
                                  <p class="timer-dem">Total</p>
                                  @php
                                    if($row->timerInfo->time_ended != NULL){
                                        $datetime1 = \Carbon\Carbon::parse($row->timerInfo->time_started);
                                        $datetime2 = \Carbon\Carbon::parse($row->timerInfo->time_ended);
                                        $interval = $datetime2->diffInSeconds($datetime1);
                                        $date = $interval - $totalInterval;
                                        echo '<p class="timer-bold">'.gmdate("H:i:s", $date).'</p>';
                                    }
                                  @endphp
                                  <p class="timer-dem">#{{ $row->timerInfo->id }}</p>
                                </div>
                                <div class="timer-right">
                                  <p>{!!  $row->timerInfo->location !!}</p>
                                  <p class="timer-sm">{{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                </div>
                              </div>
                            </div>
                          @endforeach
                        @endif
                      </td>
                    </tr>
                  @endif
            </tbody>  
          </table>
        </div>
        <div class="main-size" id="mobileviewHtml">
          @include('dashboard.company.docketManager.docket.approvalTypeView')
        </div>
      
      </div>
      <div class="row no-print">
          <div class="col-xs-12">
              @if($sentDocket->status==0)
                  @if($sentDocket->sender_company_id==Session::get('company_id'))
                      @if($sentDocket->sentDocketRecipientApproval)
                          @if(in_array(Auth::user()->id,$sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray()))
                              @if($sentDocket->docketApprovalType==0)
                                  <a href="" class="btn btn-raised btn-xs  btn-success pull-right"  sentDocketIdsappr="{{$sentDocket->id}}" id="ApproveDocketType"><i class="fa fa-check"></i> Approve</a>
                              @else
                                  <a  id="first" class="btn btn-raised btn-xs  btn-success pull-right" data-toggle="modal" data-id="{{$sentDocket->id}}" data-target="#myModal3" >
                                      <i class="fa fa-check"></i>Approve
                                  </a>
                              @endif
                          @else
                              <a href="#" class="btn btn-raised btn-xs  btn-warning pull-right" id="addNew"><i class="fa fa-check"></i> Pending</a>
                          @endif
                      @endif
                  @else
                      @if($sentDocket->docketApprovalType==0)
                          <a href="" class="btn btn-raised btn-xs  btn-success pull-right"  sentDocketIdsappr="{{$sentDocket->id}}" id="ApproveDocketType"><i class="fa fa-check"></i> Approve</a>
                      @else
                          <a  id="first" class="btn btn-raised btn-xs  btn-success pull-right" data-toggle="modal" data-id="{{$sentDocket->id}}" data-target="#myModal3" >
                              <i class="fa fa-check"></i>Approve
                          </a>
                      @endif
                  @endif
              @else
                  <a href="#" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>
              @endif
          </div>
      </div>
    </div>
  </div>
  <br />
  <br />
  <style type="text/css">
   <style type="text/css">

        @font-face {
            font-family: Arial, sans-serif;
            src: url(SourceSansPro-Regular.ttf);
        }

        #printContainer {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        a {
            color: #0087C3;
            text-decoration: none;
        }

        /*division one*/
        .division-one {
            min-height: 150px;
            line-height: 2rem;
        }

        .invoice-from img {
            width: 141px;
        }

        .table-unitper td .unit-per tbody tr {
            background: none;
        }

        .division-logo ul {
            float: right;
            text-align: right;
            list-style: none;
        }

        .web-app {
            font-weight: bold;
            font-size: 18px;
            color: #333333;
        }

        .bold-d {
            font-weight: bold;
            color: #333333;
        }

        .division-logo i {
            padding-right: 5px;
        }

        /*taxinvoice*/
        .taxinvoice {
            text-align: center;
            z-index: 0;
        }

        /*invoice-from*/
        .invoice-from {
            float: left;
            text-align: center;
        }

        .invoice-from ul {
            list-style: none;
            padding-left: 0;
        }

        .division-one li {
            line-height: 2rem;
        }

        .invoice-to {
            float: left;
        }

        .invoice-to ul {
            line-height: 2rem;
            list-style: none;
            padding-left: 0;
        }

        .tax-bold {
            font-size: 14px;
            color: #0fb9d6;
            font-weight: bold;
        }

        /*tax invoice*/
        .division-two {
            min-height: 100px;
            width: 100%;
        }

        .division-two table {
            width: 100%;
            border-spacing: 0;
            padding: 0;
            margin: 0;
        }

        .table-division2 td {
            width: 33.333%;
        }

        .division-two h1 {
            text-align: center;
            font-size: 30px;
            font-weight: 600;
            color: #3a4f62;
        }

        .tax-invoice {
            width: 180px;
            float: right;
            padding-bottom: 40px;
        }

        .invoice-date {
            width: 170px;
            border-left: 7px solid #032d57;
            background-color: #ece9e994;
            float: right;
        }

        .invoice-to span {
            padding-left: 5px;
            color: #5d5f5db3;
        }

        .invoice-date ul {
            list-style: none;
            padding: 0px 10px 0px 10px;
        }

        .division-three {
            min-height: 100px;
            width: 100%;
        }

        .invoice-total {
            width: 180px;
            border-left: 7px solid #032d57;
            background-color: #ece9e994;
            float: right;
        }

        .invoice-bold {
            position: relative;
            z-index: 1;
            text-align: center;
        }

        /*description value/amount*/
        .amount-value {
            position: relative;
        }

        .amount-dess {
            text-align: left;
            padding-left: 10px;
        }

        th.value-dess {
            width: 170px;
            text-align: left;
        }

        .responsive-table th {
            background-color: #032d57;
            color: #ffffff;
            padding: 15px 10px 15px 10px;
        }

        .responsive-table td {
            padding-left: 10px;
        }

        table.responsive-table {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        table.responsive-table tr:nth-child(odd) {
            background-color: #f2f2f2
        }

        .amount-value ul {
            line-height: 2rem;
            list-style: none;
            padding-left: 0;
        }

        .responsive-table thead {
            background-color: #236d83;
        }

        .responsive-table .fa {
            padding-top: 9px;
            float: left;
            font-size: 12px;
            padding-right: 10px;
        }

        .padding-oops {
            padding: 15px 0px 15px 0px;
        }

        .timer-box {
            width: 92%;
            float: left;
            margin-top: 15px;
            background-color: #ffffff;
            padding: 15px;
            margin-bottom: 10px;
            margin-right: 15px;

        }

        .timer-left {
            line-height: 0.6rem;
            margin-right: 15px;
            width: 70px;
            height: 70px;
            float: left;
            border-radius: 50%;
            border: 3px solid #00bcd4;
            text-align: center;
        }

        .timer-right {
            line-height: 1rem;
        }

        .timer-bold {
            font-size: 12px;
            font-weight: bold;
        }

        .timer-sm {
            font-size: 13px;
        }

        .timer-dem {
            font-size: 12px;
            color: #00000082;
        }

        ul.list-timers {
            list-style: none;
        }

        ul.list-timers li {
            width: 33%;
            display: inline-block;
        }

        /*attached timer end*/
        /*description value/amount end*/

        /*payment-method*/
        .payment-method table {
            border-radius: 5px;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #f7f7f7;
        }

        tr.payment-line td {
            border-bottom: 1px solid #ececec;
        }

        .payment-method {
            width: 100%;
        }

        /*.payment-method.paymend-table-b tbody{
          padding-top: 15px;
        }*/
        .payment-title td {
            font-weight: bold;
            line-height: 2rem;
        }

        table.payment-table td {
            border-collapse: collapse;
            padding: 7px 0px 7px 7px;
        }

        .payment-left {
            width: 100%;
        }

        ul.important-info {
            padding: 0;
            line-height: 2rem;
        }

        ul.important-info li {
            list-style: none;

        }

        .payment-right {
            width: 100%;
            float: right;
            margin-bottom: 25px;
        }

        .verified-signature img {
            width: 100px;
            height: 60px;
            padding-right: 7%;
            padding-top: 15px;
        }

        ul.signature-list {
            padding: 0;
        }

        ul.signature-list li {
            list-style: none;
            display: inline;
        }

        table.right-table tr:nth-child(even) {
            background-color: #f2f2f2
        }

        table.right-table {
            width: 100%;
            background-color: #f2f2f2;
            border-spacing: 0;
        }

        table.right-table td {
            padding: 15px 10px 15px 10px;
        }

        tr.grand-total {
            background-color: #032d57;
            color: #ffffff;
            padding:
        }

        tr.grand-total td {

        }

        tr.grand-total {
            border-collapse: collapse;
        }

        .proof-title p {
            font-weight: bold;
        }

        .proof-work img {
            width: 100px;
            height: 60px;
            padding-right: 5px;
        }

        .proof-work ul {
            padding-left: 0;
        }

        .proof-work li {
            list-style: none;
            display: inline;
        }

        td.total-right {
            width: 170px;
        }

        .verified-by p {
            padding-top: 15px;
            font-weight: bold;
        }

        /*payment-method end*/

        /*=========================*/

        /*description value/amount*/

        .amount-des {
            width: 50%;
            text-align: left;
            padding-left: 10px;
        }

        .value-des {
            width: 50%;
            text-align: left;
        }

        tbody.body-data td {
            padding: 10px;
        }

        .responsive-tab th {
            background-color: #032d57;
            color: #ffffff;
            padding: 10px;
        }

        .responsive-table td {
            padding-left: 10px;
            padding-top: 10px;
        }

        table.responsive-tab {
            width: 100%;
            border-spacing: 0;
            border-collapse: collapse;
        }

        table.responsive-tab tr:nth-child(odd) {
            background-color: #f2f2f2
        }

        .amount-value ul {
            line-height: 2rem;
            list-style: none;
            padding-left: 0;
        }

        .responsive-tab thead {
            background-color: #236d83;
        }

        .responsive-tab .fa {
            padding-top: 9px;
            float: left;
            font-size: 12px;
            padding-right: 10px;
        }

        .image-tit p {

        }

        .images-3 {
            padding-left: 0;
        }

        .images-3 img {
            width: 100px;
            height: 60px;
            padding-right: 5px;
        }

        .images-3 li {
            list-style: none;
            display: inline;
        }

        .per-unit td {
            padding: 0 !important;
        }

        .check-time .fa-check-circle {
            color: #0fb9d6;
            font-size: 16px;
        }

        .check-time .fa-times-circle {
            color: red;
            font-size: 16px;
        }

        ul.check-time {
            padding: 0;
            margin: 0;
        }

        ul.check-time li {
            list-style: none;
            display: inline;
        }

        /*description value/amount end*/
        ul.sketch-img {
            padding: 0;
            margin: 0;
        }

        .table-unitper td {
            padding-left: 5px !important;
        }

        ul.sketch-img li {
            list-style: none;
            display: inline;
        }

        ul.sketch-img img {
            width: 100px;
            height: 60px;
            padding-right: 10px;
        }

        /*document-doc*/
        .document-list .fa {
            position: absolute;
            float: left;
            padding-right: 10px;
        }

        ul.document-list {
            margin: 0;
            padding: 0;
        }

        table.unit-per {
            width: 100%;
            border-spacing: 0;
            left: 0
        }

        table.unit-per tr {
            border-spacing: 0;
            left: 0
        }

        table.unit-per td {
            padding-top: 5px;
        }

        ul.document-list li {
            list-style: none;
            display: inline;
        }

        ul.document-list a {
            text-decoration-line: none;
            color: black;
            padding-right: 30px;
        }

        ul.document-list span {
            text-decoration: underline;
            padding-left: 15px;
        }

        hr.mid-line {
            color: green;
            margin-top: 60px;
        }

        /*division-four */
        .division-four {
            width: 100%;
            min-height: 150px;
        }

        .left-one img {
            width: 147px;
        }

        .left-one ul {
            padding-left: 0;
            float: left;
        }

        .left-one li {
            list-style: none;
        }

        .right-one {
            text-align: right;
            padding: 0;
            margin: 0;
            float: right;
            width: 50%;
        }

        .left-one {
            width: 50%;
            float: left;
        }

        .right-one li {
            list-style: none;
        }

        /*division-four*/

        /*division five*/
        .division-five {
            padding-top: 15px;
            width: 100%;
            min-height: 150px;
        }

        .left-two {
            width: 340px;
        }

        .left-two p {
            text-align: left;
        }

        .left-two ul {
            padding-left: 0;
            float: left;
        }

        .left-two li {
            list-style: none;
        }

        .right-two {
            padding: 0;
            margin: 0;
            float: right;
            margin-bottom: 17px;
        }

        .right-two p {
            color: #032d57;
            font-weight: bold;
        }

        .right-two ul {
            width: 185px;
            border-left: 7px solid #032d57;
            background-color: #ada8a82e;
            padding: 10px 10px 10px 10px;
            float: right;
        }

        .right-two li {
            list-style: none;
        }

        /*division five end*/
        .terms-condition {
            padding-top: 15px;
        }

        /*document-doc*/
        footer {
            padding-top: 50px;
            padding-bottom: 30px;
            text-align: center;
            width: 100%;
        }

        .division-six {
            width: 100%;
            float: left;
        }

        .division-six ul {
            list-style: none;
            padding-left: 0;
        }

        .division-six li {
            list-style: none;
        }
    </style>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
        });
        $('#myModal3').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $("#docket_aprovel_name").val(id);



        });
    </script>
    <script>
      function readURL(input) {
        if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
        $('#blah').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
        }
        }

        $("#imgInp").change(function() {
        readURL(this);
      });
    </script>
    <script>
      $(document).on('click', '#ApproveDocketType', function(){
          var sentDocketIdsappr = $(this).attr('sentDocketIdsappr');
          $.ajax({
              type: "POST",
              data: {sentDocketId:sentDocketIdsappr},
              url: "{{ url('dashboard/company/docketBookManager/docket/view/approve') }}",
              success: function (response) {
                  $('.dashboardFlashsuccess').css('display','none');
                  if (response['status']==true) {
                      var wrappermessage = ".messagesucess";
                      $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                      $('.dashboardFlashsuccess').css('display','block');
                      $.ajax({
                          type: "GET",
                          url:"{{url('dashboard/company/docketBookManager/docket/approvalTypeView/'.$sentDocket->id) }}",
                          success:function (response) {
                              $("#mobileviewHtml").html(response);
                          }
                      });
                  }

              }


          });

      });
    </script>
    <script>
        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        var saveButton = document.getElementById('save');
        var cancelButton = document.getElementById('clear');

        saveButton.addEventListener('click', function (event) {
            var data = signaturePad.toDataURL('image/png');
            var sentDocketId_approval = $('input[name=sentDocketId]').val();
            var name_approval = $('input[name=name]').val();
            $.ajax({
                type: "POST",
                data: {signature: data,sentDocketId:sentDocketId_approval,name:name_approval},
                url: "{{ url('dashboard/company/docketBookManager/docket/view/approve') }}",
                success: function (response) {
                    $('.dashboardFlashsuccess').css('display','none');
                    if (response['status']==true) {
                        var wrappermessage = ".messagesucess";
                        $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                        $('.dashboardFlashsuccess').css('display','block');
                        $.ajax({
                            type: "GET",
                            url:"{{url('dashboard/company/docketBookManager/docket/approvalTypeView/'.$sentDocket->id) }}",
                            success:function (response) {
                                $("#mobileviewHtml").html(response);
                            }
                        });
                    }
                    $('#myModal3').modal('hide');

                }
            });

// Send data to server instead...
        });

        cancelButton.addEventListener('click', function (event) {
            signaturePad.clear();
        });
    </script>
@endsection