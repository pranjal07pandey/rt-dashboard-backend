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
				<!--division-one-e  -->
				<div class="division-one-e">
					<!-- <hr class="invoice-line"> -->
					<div class="invoice-fromm">
						@if(AmazoneBucket::fileExist(@$sentDocket->senderCompanyInfo->logo))
							<img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->senderCompanyInfo->logo }}">
						@else
							<img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
						@endif
						<ul>
							<li class="deam">From:</li>
							<li class="bold-d">{{ @$sentDocket->sender_name }}</li>
							<li>{{ @$sentDocket->senderUserInfo->email }}</li>
						</ul>
					</div>
					<div class="division-logoo">
						<ul>
							<li class="bold-d">{{ @$sentDocket->company_name }}</li>
							<li class="deam">{{ @$sentDocket->company_address }}</li>
							<li class="deam">ABN:{{ @$sentDocket->abn }}</li>
							<li class="deam"><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;{{ @$sentDocket->senderCompanyInfo->contactNumber }}</li>
						</ul>
					</div>
				</div>
				<div class="division-o">
					<div class="invoiceto">
						<div class="invoice-to">
							<ul>
								<li class="deam">To:</li><br>
								@if($sentDocket->recipientInfo)
                                    <?php $sn = 1; ?>
									@foreach($sentDocket->recipientInfo as $recipient)
										<li><b> {{ @$recipient->userInfo->first_name }} {{ @$recipient->userInfo->last_name }}</b></li>
									<!--<li>{{ @$recipient->userInfo->email }}</li>-->
										@if($sn!=$sentDocket->recipientInfo->count())
											,
										@endif
                                        <?php $sn++; ?>
									@endforeach
                                    <?php $sns = 1; ?>


								@endif
							</ul>
							<ul style="margin-top: -10px;">
								@if($sentDocket->recipientInfo)
									<li><b>Company Name:</b></li>
									@foreach($company as $companys)
										{{$companys->name}}
										@if($sns!=$company->count())
											,
										@endif
                                        <?php $sns++; ?>
									@endforeach
								@endif
							</ul>
						</div>
					</div>
					<div class="invoice-tax">
						<div class="tax-invoice">
							<p class="tax-bold">{{ $sentDocket->docketInfo->title }}</p>
							<div class="invoice-date">
								<ul>
									<li><b>Docket ID:</b><span>{{ $sentDocket->id }}</span></li>
									<li><b>Date:</b><span>{{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}</span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- description value/amount -->
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
                                          <div class="row">
									 @php
										$totalInterval = 0;
									@endphp
									@if($docketTimer->count())
										@foreach($docketTimer as $row)
											<div class="col-4 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                                              <div class="timer-box">
                                                <div class="timer-left">
                                                  <p class="timer-dem">Total</p>
                                                  <p class="timer-bold">
                                             @php
												if($row->timerInfo->time_ended != NULL){
                                                    $datetime1 = \Carbon\Carbon::parse($row->timerInfo->time_started);
                                                    $datetime2 = \Carbon\Carbon::parse($row->timerInfo->time_ended);
                                                    $interval = $datetime2->diffInSeconds($datetime1);
                                                    $date = $interval - $totalInterval;
                                                    echo gmdate("H:i:s", $date);
                                                }
											@endphp
													</p>
                                          <p class="timer-dem">#{{$row->timerInfo->id}}</p>
                                        </div>
                                        <div class="timer-right">
                                          <p>{!!  str_limit(strip_tags($row->timerInfo->location),28) !!}</p>
                                          <p class="timer-sm">{{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                        </div>
                                      </div>
                                    </div>
                                    @endforeach
									@endif
											</div>
                                          </td>
                                      </tr>
                                   @endif
										</tbody>
                                      </table>

                                  </div>

                                  <div class="main-size" id="mobileviewHtml">
										@include('dashboard.company.docketManager.docket.approvalTypeView')
								  </div>
                                          <!--description value amount -->
								<!--<footer>-->
								<!--  Docket was created on Recordtime-->
								<!--</footer>-->
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
		</div><br /><br />
		<style type="text/css">

			a {
				color: #0087C3;
				text-decoration: none;
			}
			/*division one*/
			.division-one{
				line-height: 2.3rem;
			}
			.division-logo{
				width: 50%;
				float: right;
			}
			.invoice-from img{
				width: 150px;
			}
			.division-logo ul{
				float: right;
				list-style: none;
			}
			.yes-imp{
				padding-bottom: 10px;
			}
			.bold-d{
				font-weight: bold;
			}
			.deam{
				color: #5d5f5db3;
			}
			.division-logo i{
				padding-right: 5px;
			}
			/*division-*/
			.division-same{

			}
			/*division-o*/
			.division-o{

			}
			.invoiceto{
				width: 50%;
				float:left;
				clear: both;
			}
			.invoiceto li{
				line-height: 2.3rem;
				display: inline-block;
				color: gray;
				float: inherit;
			}
			.invoice-tax{
				width: 50%;
				float: right;
			}
			/*division-o*/
			/*invoice-from*/
			.invoice-from{
				width: 50%;
				clear: both;
				float: left;
			}
			.invoice-from ul{
				list-style: none;
				padding-left: 0;
			}
			.division-one li{
				line-height: 2.3rem;
			}
			.invoice-to{
				width: 50%;
			}
			.invoice-to ul{
				line-height: 1.3rem;
				list-style: none;
				padding-left: 0;
			}
			.tax-bold{
				font-size: 14px;
				color: #0fb9d6;
				font-weight: bold;
			}
			/*tax invoice*/
			.invoice-tx{
				width: 50%;
				float: right;
			}
			.tax-invoice{
				width: 225px;
				float: right;
				padding-bottom: 40px;
			}
			.invoice-date{
				border-left:7px solid #00bcd4;
				background-color: #ece9e994;
				padding: 1px;
			}
			.invoice-date span{
				padding-left: 5px;
				color: #5d5f5db3;
			}
			.invoice-date ul{
				list-style: none;
				padding:0px 10px 0px 10px;
			}

			/*division one end*/

			/* division-same"*/
			.division-one-e{
			}
			.invoice-fromm{
				width: 50%;
				clear: both;
				float: left;

			}
			.invoice-fromm ul{
				list-style: none;
				padding-left: 0;
			}
			.invoice-fromm img{
				width: 150px;
			}
			.division-logoo{
				width: 50%;
				float: right;
			}

			.division-logoo ul{
				float: right;
				padding: 0;

			}
			.division-logoo li{
				list-style: none;
				padding-right: 30px;
				line-height: 2.3rem;
			}


			/*description value/amount*/
			.amount-value{
				position: relative;
			}
			.amount-dess{
				text-align: left;
				padding-left: 10px;
			}

			th.value-dess{
				width: 235px;
				text-align: left;
			}
			.responsive-table th{
				background-color: #00bcd4;
				color: #ffffff;
				padding: 10px;
			}
			.responsive-table td{
				padding-left:10px;
			}
			table.responsive-table{
				width: 100%;
				border-spacing: 0;
				border-collapse: collapse;
			}
			table.responsive-table tr:nth-child(odd){background-color: #f2f2f2}
			.amount-value ul{
				line-height: 2rem;
				list-style: none;
				padding-left: 0;
			}
			.responsive-table thead{
				background-color: #236d83;
			}
			.bold-d i{
				padding-top: 3px;
				float: left;
				font-size: 12px;
				padding-right: 10px;
				padding-bottom: 40px;
			}
			.padding-oops{
				padding: 15px 0px 15px 0px;
			}

			/*description value/amount end*/

			/*payment-method*/
			.payment-method table{
				width: 100%;
				margin-top: 15px;
			}
			.payment-method{
				width: 100%;
			}
			/*.payment-method.paymend-table-b tbody{
              padding-top: 15px;
            }*/
			.payment-title td{
				font-weight: bold;
				line-height: 2rem;
			}
			table.payment-table td{
				padding: 7px 0px 7px 0px;
			}
			.payment-left{
				width: 50%;
				position:absolute;
			}
			ul.important-info{
				padding: 0;
				line-height: 1.3rem;
			}
			ul.important-info li{
				list-style: none;

			}
			.important-height li{
				line-height: 2rem;
			}
			.payment-line{
				width: 100%;
				border-bottom: 2px solid #000000c7;
				clear: both;
				margin-bottom: 30px;
			}

			.payment-right{
				width: 50%;
				float: right;
				margin-bottom: 150px;
			}
			.verified-signature img{
				width: 100px;
				height: 60px;
				padding-right: 7%;
				padding-top: 15px;
			}
			ul.signature-list{
				padding: 0;
			}
			ul.signature-list li{
				list-style: none;
				display: inline;
			}
			table.right-table tr:nth-child(even){background-color: #f2f2f2}
			table.right-table{
				background-color: #f2f2f2;
				border-spacing: 0;
			}
			table.right-table td{
				padding: 10px;
			}
			tr.grand-total {
				background-color: #00bcd4;
				color: #ffffff;
			}
			tr.grand-total td{

			}
			tr.grand-total{
				border-collapse: collapse;
			}
			.proof-title p{
				font-weight: bold;
			}
			.proof-work img{
				width: 100px;
				height: 60px;
				padding-right: 5px;
			}
			.proof-work ul{
				padding-left: 0;
			}
			.proof-work li{
				list-style: none;
				display: inline;
			}
			td.total-right{
				width: 235px;
			}
			.verified-by p{
				padding-top: 15px;
				font-weight: bold;
			}
			.table-unitper td .unit-per tbody tr{
				background:none;
			}

			/*payment-method end*/

			/*=========================*/

			/*description value/amount*/
			.amount-value{
				position: relative;
			}
			.amount-des{
				width: 50%;
				text-align: left;
				padding-left: 10px;
			}
			.value-des{
				width: 50%;
				text-align: left;
			}
			tbody.body-data td{
				padding: 10px;
			}

			.responsive-tab th{
				background-color: #00bcd4;
				color: #ffffff;
				padding: 10px;
			}
			.responsive-table td{
				padding:10px;
			}
			table.responsive-tab{
				width: 100%;
				border-spacing: 0;
				border-collapse: collapse;
			}
			table.responsive-tab tr:nth-child(odd){background-color: #f2f2f2}
			.amount-value ul{
				line-height: 2rem;
				list-style: none;
				padding-left: 0;
			}
			.responsive-tab thead{
				background-color: #236d83;
			}
			.bold-d i{
				padding-top: 4px;
				float: left;
				font-size: 12px;
				padding-right: 10px;
			}
			.image-tit p{

			}
			.images-3{
				padding-left: 0;
			}
			.images-3 img{
				width: 100px;
				height: 60px;
				padding-right: 5px;
			}
			.images-3 li{
				list-style: none;
				display: inline;
			}

			.per-unit td{
				padding: 0 !important;
			}
			.check-time .fa-check-circle{
				color: #0fb9d6;
				font-size: 16px;
			}
			.check-time .fa-times-circle{
				color: red;
				font-size: 16px;
			}
			ul.check-time{
				padding: 0;
				margin: 0;
			}
			ul.check-time li{
				list-style: none;
				display: inline;
			}
			/*description value/amount end*/
			ul.sketch-img{
				padding: 0;
				margin: 0;
			}
			.table-unitper td{
				padding-left: 5px!important;
			}
			ul.sketch-img li{
				list-style: none;
				display: inline;
			}
			ul.sketch-img img{
				width: 100px;
				height: 60px;
				padding-right: 5px;
			}
			/*document-doc*/
			.document-list i{
				padding-top: 4px;
				font-size: 14px;
				position: absolute;
				float: left;
				padding-right: 16px;
			}
			ul.document-list{
				margin: 0;
				padding: 0;
			}
			table.unit-per{
				width: 100%;
				border-spacing: 0;
				left: 0
			}
			table.unit-per tr{
				border-spacing: 0;
				left: 0
			}
			table.unit-per td{
				padding-top: 5px;
			}
			ul.document-list li{
				list-style: none;
				display: inline;
			}
			ul.document-list a{
				text-decoration-line: none;
				color: black;
				padding-right: 30px;
			}
			ul.document-list span{
				text-decoration: underline;
				padding-left: 15px;
			}
			/*document-doc*/
			footer{
				padding-top: 50px;
				padding-bottom: 30px;
				text-align: center;
				width: 100%;
			}
		</style>
		@endsection
		@section('customScript')
			<script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
			<style>
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
				.dotted {
					border: 3px dashed  #919191;
					border-style: none none dashed;
					color: #fff;
					background-color: #fff;
				}

				.box-signature-shown{
					position: relative;
				}
				#blah{
					position: absolute;
					top: 16px;
					height: 200px;
					width: 100%;

				}
				.wrapper1 {
					position: relative;
					width: 400px;
					height: 200px;
					-moz-user-select: none;
					-webkit-user-select: none;
					-ms-user-select: none;
					user-select: none;
				}
				.wrapper1 img {
					position: absolute;
					left: 0;
					top: 0;
					margin-top: 36px;
				}

				.signature-pad {
					position: absolute;
					left: 0;
					top: 40px;
					width:532px;
					height:200px;
				}
				.box-timer{
					text-align: center;
					background: #f5f6f5;
					padding: 16px 0px 10px 0px;
					margin-bottom: 16px;
				}
				.timer-box{
					min-height: 107px;
					width: 100%;
					background-color: #ffffff;
					padding: 15px;

				}
				.timer-left{
					padding: 8px;
					line-height: 0.6rem;
					margin-right: 15px;
					width: 80px;
					height: 80px;
					float: left;
					border-radius: 50%;
					border: 3px solid #00bcd4;
					text-align: center;
					padding-top: 15px;
				}
				.timer-right{
					padding-top: 15px;
					line-height: 1.6rem;
				}
				.timer-bold{
					font-size: 12px;
					font-weight: bold;
				}
				.timer-sm{
					font-size: 13px;
				}
				.timer-dem{
					font-size: 12px;
					color: #00000082;
				}
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