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
				<div class="division-one">
					<div class="invoice-from">
						@if(AmazoneBucket::fileExist(@$sentDocket->senderCompanyInfo->logo))
							<img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->senderCompanyInfo->logo }}">
						@else
							<img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
						@endif
					</div>
					<div class="division-logo">
						<ul>
							<li class="web-app">{{ @$sentDocket->company_name }}</li>
							<li >{{ @$sentDocket->company_address }}</li>
							<li >ABN: {{ @$sentDocket->abn }}</li>
							<li >Phone: {{ @$sentDocket->senderCompanyInfo->contactNumber }} </li>
						</ul>
					</div>
				</div>
				<div class="division-two">
					<div class="invoice-fm">
						<ul>
							<li>From:</li>
							<li class="bold-d">{{ @$sentDocket->sender_name }} </li>
							<li>{{ @$sentDocket->senderUserInfo->email }} </li>
						</ul>
					</div>

					<div class="invoice-to">
						<ul>
							<li class="deam">To:</li>
							@if($sentDocket->recipientInfo)
                                <?php $sn = 1; ?>
								@foreach($sentDocket->recipientInfo as $recipient)
									<li class="bold-d"> <b>{{ @$recipient->userInfo->first_name }} {{ @$recipient->userInfo->last_name }}</b>
										@if($sn!=$sentDocket->recipientInfo->count())
											,</li>
									@endif
                                    <?php $sn++; ?>
								@endforeach
                                <?php $sns = 1; ?>
								@foreach($company as $companys)
									<li>{{$companys->name}}</li>
									@if($sns!=$company->count())
										,</li>
									@endif
                                    <?php $sns++; ?>
								@endforeach
							@endif
						</ul>
					</div>

					<div class="invoice-date">
						<ul>
							<li><b>Docket ID:</b>  <span>{{ $sentDocket->id }}</span></li>
							<li><b>Date:</b>  <span>{{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}</span></li>
						</ul>
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
										<td class="amount-des">{{ $item->label }}</td>
										<td  class="value-des">{{ $item->value }}</td>
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
								@if($docketTimer->count()>0)
									<tr class="attached-timers">
										<td colspan="2" >
											<div class="timer-heading">
												<p>Attached Timers</p>
											</div>
											@php
												$totalInterval = 0;
											@endphp
											@if($docketTimer->count())
												@foreach($docketTimer as $row)
													<div class="timer-wrapper">
														<div class="timer-box">
															<div class="timer-left">
																<div class="timer-in">
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

							@endforeach
						@endif
						</tbody>
					</table>
				</div>
				<div class="main-size" id="mobileviewHtml">
					@include('dashboard.company.docketManager.docket.approvalTypeView')
				</div>
				<!--<footer style="padding-top: 50px;padding-bottom: 30px;text-align: center;width: 100%;">-->
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
	</div>
	</div>
	<br /><br />
	<style type="text/css">
		/*division one*/
		.division-one{
			border-bottom: 5px solid #0096a7;
			min-height: 130px;
			line-height: 2rem;
		}
		.division-logo {
			width: 33.3333%;
			float: right;
		}
		.division-logo ul{
			float: right;
			text-align: left;
			list-style: none;
			background-color: hsl(186, 92%, 34%);
			color: #ffffff;
			padding: 23px;
		}
		.web-app{
			font-weight: bold;
			font-size: 18px;
		}
		.bold-d{
			font-weight: bold;
		}
		.division-logo i{
			padding-right: 5px;
		}
		/*invoice-from*/
		.invoice-from{
			width: 33.3333%;
			float: left;
			text-align: center;
		}
		.invoice-from img{
			width: 125px;
			float: left;
		}
		.invoice-from ul{
			list-style: none;
			padding-left: 0;
		}
		.division-one li{
			line-height: 2rem;
		}
		.invoice-to ul{
			position: relative;
			left: 33%;
			list-style: none;
			padding-left: 0;
		}
		.tax-bold{
			font-size: 14px;
			color: #0fb9d6;
			font-weight: bold;
		}

		/*division-two*/
		.division-two{
			height: 130px;
			width: 100%;
			padding-top: 30px;
		}
		.division-two li{
			line-height: 2rem;
		}
		.invoice-fm {
			width: 33.3333%;
			float: left;
		}
		.invoice-fm ul{
			padding-left: 0;
		}
		.invoice-fm li{
			list-style: none;
		}
		.invoice-to {
			width: 33.3333%;
			float: left;
		}
		.division-two table{
			width: 100%;
			border-spacing: 0;
			padding: 0;
			margin: 0;
		}
		.table-division2 td{
			width: 33.333%;
		}
		.division-two h1{
			text-align: center;
			font-size: 30px;
		}
		.tax-invoice{
			text-align: center;
			padding-top: 40px;
			width: 33.3333%;
			float: left;
		}
		.tax-invoice h3{
			font-weight: bold;
		}

		.invoice-date{
			width: 33.3333%;
			float: right;
		}
		.invoice-date ul{
			list-style: none;
			float: right;
		}
		.division-three{
			width: 100%;
		}
		.invoice-total{
			padding: 15px 0px 0px 10px;
			width: 180px;
			border-left: 7px solid #191c3f;
			background-color: #ece9e994;
			float: right;
		}
		/*division one end*/

		/*description value/amount*/
		.amount-value{
			position: relative;
		}
		.bold-left{
			font-weight: bold;
			border-left: 5px solid #ffffff;
			text-align: center;
		}
		.amount-dess{
			text-align: left;
			padding-left: 10px;
		}

		th.value-dess{
			border-left: 5px solid #ffffff;
			width: 30%;
			text-align: center;
		}
		.amount-table th{
			background-color: #0096a7;
			color: #ffffff;
			padding: 15px 10px 15px 10px;
		}
		.amount-table td{
			padding-left:10px;
		}
		table.amount-table{
			width: 100%;
			border-spacing: 0;
			border-collapse: collapse;
		}
		table.amount-table tr:nth-child(odd){background-color: #f2f2f2}
		.amount-value ul{
			line-height: 2rem;
			list-style: none;
			padding-left: 0;
		}
		.amount-table thead{
			background-color: #236d83;
		}
		.amount-table .fa{
			padding-top: 9px;
			float: left;
			font-size: 12px;
			padding-right: 10px;
		}
		.padding-oops{
			padding: 15px 0px 15px 0px;
		}
		.document-title p{
			line-height: 0;
		}

		/*description value/amount end*/

		/*payment-method*/
		.payment-method table{
			background-color: #d8d3d33d;
			width: 100%;
			border-collapse: collapse;
			margin-top: 15px;
			float: left;
			margin-bottom: 30px;
		}
		.proof-title{
			font-weight: bold;
			padding-top:15px;
		}
		.payment-method{
			width: 49%;
			margin-top: 30px;
			padding-right: 30px;
		}
		.payment-line{
			border-bottom: 1px solid #8080804d;
		}

		/*.payment-method.paymend-table-b tbody{
		  padding-top: 15px;
		}*/
		.payment-title td{
			font-weight: bold;
			line-height: 2rem;
		}
		table.payment-table td{
			border-collapse: collapse;
			padding: 7px 0px 7px 10px;
		}
		ul.important-info{
			padding: 0;
			line-height: 2rem;
			margin-top: 15px;
		}
		ul.important-info li{
			list-style: none;
		}
		.verified-by{
			padding-top: 20px;
			width: 50%;
			float: right;
		}
		.payment-right{
			width: 100%;
			float: right;
		}
		.verified-signature img{
			width: 100px;
			height: 60px;
			/*padding-right: 7%;
            padding-top: 15px;*/
		}
		ul.signature-list{
			padding: 0;
		}
		ul.signature-list li{
			list-style: none;
			margin-right: 7%;
			display: inline;
		}
		table.right-table tr:nth-child(even){background-color: #f2f2f2}
		table.right-table{
			width: 100%;
			background-color: #f2f2f2;
			border-spacing: 0;
		}
		table.right-table td{
			padding: 15px 10px 15px 10px;
		}
		tr.grand-total {
			background-color: #0096a7;
			color: #ffffff;
			padding:
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
			width: 30%;
		}
		.verified-by p{
			padding-top: 15px;
			font-weight: bold;
		}

		/*payment-method end*/

		/*=========================*/

		/*description value/amount*/

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
			background-color: #0096a7;
			color: #ffffff;
			padding: 15px 10px 15px 10px;
		}
		.responsive-table td{
			padding-left:10px;
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
		.responsive-tab .fa{
			padding-top: 9px;
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
			padding-right: 10px;
		}
		/*document-doc*/
		.document-list .fa{
			position: absolute;
			float: left;
			padding-right: 10px;
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

		.mid-line{
			margin-bottom: 30px;
			height: 100px;
			width: 100%;
			border-bottom: 1px solid #bab8b9;
			clear: both;
		}
		/*description value/amount*/
		.amount-value{
			position: relative;
		}
		.amount-dess{
			text-align: left;
			padding-left: 10px;
		}


		.responsive-table th{
			background-color: #006c9c;
			color: #ffffff;
			padding: 15px 10px 15px 10px;
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
			margin: 0;
		}
		.responsive-table thead{
			background-color: #236d83;
		}
		.responsive-table .fa{
			padding-top: 9px;
			float: left;
			font-size: 12px;
			padding-right: 10px;
		}
		.padding-oops{
			padding: 15px 0px 15px 0px;
		}
		/*attached-timers*/
		.attached-timers{
			width: 100%;
		}
		.timer-wrapper{
			height: 110px;
			width: 33.3333%;
			float: left;
		}
		.timer-box{
			width: 92%;
			float: left;
			background-color: #ffffff;
			padding: 15px;
			margin-bottom: 10px;
			margin-right: 15px;
		}
		.timer-left{
			width:100px;
			float: left;
		}
		.timer-in{
			line-height: 0.6rem;
			width: 80px;
			height: 80px;
			border-radius: 50%;
			border: 3px solid #00bcd4;
			text-align: center;
			padding: 14px;
		}
		.timer-in p{
			padding: 5px 0px;
			text-align: center;
		}
		.timer-right{
			width:calc(100% - 100px);
			float: right;
		}
		.timer-bold{
			margin: 0;
			font-size: 12px;
			font-weight: bold;
		}
		.timer-sm{
			font-size: 12px;
			margin: 0;
		}
		.timer-dem{
			margin: 0;
			font-size: 12px;
			color: #00000082;
		}
		@media (min-width: 320px) and (max-width: 340px) {
			.timer-wrapper{
				width:100%;
				float: left;
			}
		}
		@media (min-width: 375px) and (max-width: 400px) {
			.timer-wrapper{
				width:100%;
				float: left;
			}
		}
		@media (min-width: 400px) and (max-width: 768px) {
			.timer-wrapper{
				width:100%;
				float: left;
			}
			/*attached timer end*/
			/*description value/amount end*/
			.terms-condition{
				padding-top: 15px;
			}
			.terms-condition p{
				color: #00000082;
			}
			/*terms-condition*/
			/*document-doc*/
			.footer{
				padding-top: 50px;
				padding-bottom: 30px;
				text-align: center;
				width: 100%;
			}
		/*footer end*/
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