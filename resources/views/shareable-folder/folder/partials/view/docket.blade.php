@extends('layouts.shareableMaster')
@section('content')
    <section class="content-header rt-content-header">
        <h1>Docket Book Manager</h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-folder"></i> Folder</a></li>
            <li class="active">View</li>
        </ol>
        <div class="clearfix"></div>
    </section>

    @include('dashboard.company.include.flashMessages')

    <div class="dashboardFlashsuccess" style="display: none;">
        <div class="alert alert-success" style="padding: 5px 10px;font-size: 13px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p class="messagesucess"></p>
        </div>
    </div>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">

            <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
                <div class="col-md-6 text-left">
                    <button  class="btn btn-default btn-sm" onclick="goBack()" style="margin:0px;"><i class="fa fa-reply"></i> Back</button>
                </div>
                <div class="col-md-6 text-right">
                    <button  class="btn btn-default btn-sm" onclick="location.href='{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$sentDocket->encryptedID())}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download" aria-hidden="true"></i></i> Download</button>
                    <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>

            <div id="printContainer">
                <div class="row invoice-info">

                    <div class="col-md-12 invoice-col" style="    width: 100%;">
                        <div class="pull-left" >
                            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="height:90px;">
                            @else
                                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
                            @endif
                        </div>
                        <div class="pull-right" style="text-align:left;width:140px;">
                            <div style="width:100%">
                                <b>{{ @$sentDocket->template_title}}</b><br/>
                                <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>
                                <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>
                                {{ $sentDocket->formatted_id }}
                                <br>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>




                    <div class="col-md-12 invoice-col" style="width: 100%;">
                        <div class="col-md-8 pull-left" style="width: 60%;">
                            <br/><br/>From:<br/>
                            <strong>{{ @$sentDocket->sender_name }}</strong><br>
                            {{ @$sentDocket->company_name }}<br>
                            {{ @$sentDocket->company_address }}<br>
                            <b>ABN:</b> {{ @$sentDocket->abn }}
                            <br/><br/>
                        </div>

                        <div class="col-md-4 pull-left" style="margin-top: 42px; width: 40%;" >
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
                        </div>

                    </div>
                    <div class="clearfix"></div>







                </div>
                <!-- /.col -->


                <!-- /.col -->

                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <br/>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Description</div></th>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Value/Amount</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($docketFields)

                                @foreach($docketFields as $row)

                                    @if((!$row->docketFieldInfo->is_hidden && $sentDocket->sender_company_id!=Session::get('company_id')) || $sentDocket->sender_company_id==Session::get('company_id'))
                                        @if($row->docketFieldInfo->docket_field_category_id==7)
                                            <?php $sn = 1; $total = 0; ?>
                                            @foreach($row->sentDocketUnitRateValue as $row)
                                                <tr>
                                                    <td>{{ $row->label }}</td>
                                                    <td>@if($row->docketUnitRateInfo->type==1) $ @endif {{ $row->value }}</td>
                                                    @if($sn == 1)
                                                        <?php $total = floatval($row->value); ?>
                                                    @else
                                                        <?php $total    =   floatval($total)*floatval($row->value); ?>
                                                    @endif
                                                    <?php $sn++; ?>
                                                </tr>

                                            @endforeach
                                            <tr>
                                                <td>
                                                    <strong>Total:</strong>
                                                </td>
                                                <td>
                                                    <strong>$ {{ round($total,2) }}</strong>
                                                </td>
                                            </tr><!--unit-rate-->

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
                                                <td> @if($row->value==1) <i class="fa fa-check-circle" style="color:green"></i> @else <i class="fa fa-close" style="color:#ff0000 !important"></i>@endif  </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==9)
                                            <tr>
                                                <td> {{ $row->label }}</td>

                                                <td>
                                                    @if($row->sentDocketImageValue->count()>0)
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                            @foreach($row->sentDocketImageValue as $signature)
                                                                <li style="margin-right:10px;float: left;">
                                                                    <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                                        <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                                                    </a>
                                                                    <p style="font-weight: 500;color: #868d90;margin-left: 12px;">{{$signature->name}}</p>

                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Signature Attached</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==5)
                                            <tr>
                                                <td> {{ $row->label }}</td>

                                                <td>
                                                    @if($row->sentDocketImageValue->count()>0)
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                            @foreach($row->sentDocketImageValue as $signature)
                                                                <li style="margin-right:10px;float: left;">
                                                                    <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                                        <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height: 100px;">
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No  Image Attached</b>
                                                    @endif

                                                </td>
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
                                                                        <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="height: 100px;">
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Sketch Attached</b>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==15)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                <td>
                                                    @if($row->sentDocketAttachment->count()>0)
                                                        <ul class="pdf">
                                                            @foreach($row->sentDocketAttachment as $document)
                                                                <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></b></li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <b>No Document Attached</b>
                                                    @endif
                                                </td>
                                            <!--<td> {{ $row->value }}</td>-->
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id==12)
                                            <tr>
                                                <td  colspan="2"><strong>{{ $row->label }}</strong></td>
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

                                        @elseif($row->docketFieldInfo->docket_field_category_id == 22)
                                            <tr>
                                                <td colspan="2">{{ $row->label }}
                                                    <div style="width: 1094px;overflow: auto;">
                                                        <table  class="table table-striped" width="100%">
                                                            <thead>
                                                            <tr>
                                                                @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)
                                                                    <th class="printTh" style="min-width: 200px">
                                                                        <div class="printColorDark" >{{ $gridFieldLabels->label}}</div>
                                                                    </th>
                                                                @endforeach
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @php
                                                                $gridMaxRow     =    $row->sentDocketFieldGridValues->max('index');
                                                                $arryForSum = array();
                                                                $totalvalue = array()
                                                            @endphp
                                                            @for($i = 0; $i<=$gridMaxRow; $i++)



                                                                <tr>
                                                                    @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)

                                                                        @if(@$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || @$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)

                                                                            @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                                <td>N/a</td>
                                                                            @else

                                                                                @php
                                                                                    $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                                                @endphp
                                                                                <td>
                                                                                    <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                        @if(empty($values))
                                                                                            <b>No Image Attached</b>
                                                                                        @else
                                                                                            @foreach($values as $value)
                                                                                                <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                    <a href="{{ AmazoneBucket::url() }}{{ $value }}" target="_blank">
                                                                                                        <img src="{{ AmazoneBucket::url() }}{{ $value }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                                    </a>
                                                                                                </li>
                                                                                            @endforeach
                                                                                        @endif
                                                                                    </ul>
                                                                                </td>
                                                                            @endif
                                                                        @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 9)
                                                                            @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                                <td>N/a</td>
                                                                            @else
                                                                                @php
                                                                                    $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                                                @endphp
                                                                                <td>
                                                                                    @if(!empty($values))
                                                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                            @foreach($values as $value)
                                                                                                <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                                    <a href="{{ AmazoneBucket::url() }}{{ $value['image'] }}" target="_blank">
                                                                                                        <img src="{{ AmazoneBucket::url() }}{{ $value['image'] }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;">
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

                                                                            @if(@$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 8)
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
                                                                            @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id == 29)

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
                                                                                $manualTimerGrid =  @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
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
                                                                                        <strong>Total Break :</strong>{{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($breakDuration) }} <br>
                                                                                        <strong>Reason for break :</strong>  {{ json_decode($manualTimerGrid , true)['explanation'] }}<br>
                                                                                        <strong>Total time :</strong> {{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($totalDuration) }} <br>
                                                                                    </td>
                                                                                @else
                                                                                    <td>N/a</td>
                                                                                @endif

                                                                            @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==3)

                                                                                <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value }}</td>

                                                                            @else
                                                                                <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value }}</td>
                                                                            @endif
                                                                        @endif

                                                                    @endforeach


                                                                </tr>
                                                            @endfor


                                                            </tbody>
                                                            <tfooter>
                                                                <tr>
                                                                    @foreach($row->sentDocketFieldGridLabels as $gridFieldLsa)
                                                                        @if($gridFieldLsa->sumable == 1)
                                                                            @if($gridFieldLsa->docketFieldGrid->docket_field_category_id == 3)
                                                                                <?php
                                                                                $arryForSum = @\App\DocketFieldGridValue::where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLsa->docket_field_grid_id)->where('is_email_docket', 0)->pluck('value')->toArray();
                                                                                ?>
                                                                                <th class="printTh" style="">
                                                                                    <div  > Total:  {{array_sum($arryForSum)}}</div>

                                                                                </th>
                                                                            @else
                                                                                <th class="printTh" style="">
                                                                                </th>
                                                                            @endif
                                                                        @else
                                                                            <th class="printTh" style="">
                                                                            </th>
                                                                        @endif
                                                                    @endforeach
                                                                </tr>
                                                            </tfooter>
                                                        </table>

                                                    </div>


                                                </td>

                                            </tr>

                                        @elseif($row->docketFieldInfo->docket_field_category_id==18)
                                            <tr>
                                                <td colspan="2">

                                                    @php
                                                        $yesno = unserialize($row->label);
                                                    @endphp
                                                    <div style="width:100%;margin:0;">
                                                        <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
                                                        @if($row->value == "N/a")
                                                            <div style="width:50%; float:right;padding-left: 8px;"> N/a </div>
                                                        @else
                                                            @if(@$yesno['label_value'][$row->value]['label_type']==1)
                                                                <div style="width:50%; float:right;   padding-left: 8px;"><img style="width: 20px; height:20px; background-color:{{ $yesno['label_value'][$row->value]['colour']}}; border-radius:20px;padding:4px;" src="{{ AmazoneBucket::url() }}{{ @$yesno['label_value'][$row->value]['label'] }}"></div>
                                                            @else
                                                                <div style="width:50%; float:right;    padding-left: 8px;">{{ @$yesno['label_value'][$row->value]['label']}}</div>
                                                            @endif
                                                        @endif
                                                    </div>
                                                    @if(count($row->SentDocValYesNoValueInfo)==0)
                                                    @else
                                                        <table style="background: transparent; width: 100%;" class="table table-striped">
                                                            <thead style="background: transparent; ">
                                                            <tr>
                                                                <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody >
                                                            @foreach($row->SentDocValYesNoValueInfo as $items)

                                                                @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                                    @php
                                                                        $imageData=unserialize($items->value);
                                                                    @endphp
                                                                    <tr>
                                                                        <td style="    width: 50%;">{{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
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
                                                                        <td > {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                        <td>{{$items->value }}</td>
                                                                    </tr>
                                                                @endif
                                                                @if($items->YesNoDocketsField->docket_field_category_id==2)

                                                                    <tr>
                                                                        <td > {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                                        <td>{{$items->value }}</td>
                                                                    </tr>
                                                                @endif
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($row->docketFieldInfo->docket_field_category_id== 27)
                                            <tr>
                                                <td colspan="2"> {!! $row->label !!}</td>
                                            </tr>

                                        @elseif($row->docketFieldInfo->docket_field_category_id== 29)
                                            <tr>
                                                <td> {{ $row->label }}</td>

                                                <td style="line-height: 1.5em;white-space: pre-line;">
                                                    @foreach(unserialize($row->value) as $email)
                                                        {!! $email['email'] !!}
                                                    @endforeach
                                                </td>
                                            </tr>

                                        @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!= 30)
                                            <tr>
                                                <td> {{ $row->label }}</td>
                                                @if($row->value=="")
                                                    <td> N/a</td>
                                                @else
                                                    <td style="line-height: 1.5em;white-space: pre-wrap;"> {!! $row->value !!} </td>
                                                @endif
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                                @foreach($docketFields as $row)
                                    @if((!$row->docketFieldInfo->is_hidden && $sentDocket->sender_company_id!=Session::get('company_id')) || $sentDocket->sender_company_id==Session::get('company_id'))
                                        @if($row->docketFieldInfo->docket_field_category_id==13)
                                            <tr>
                                                <td  colspan="2"> <strong>{{ $row->label }}</strong><br>
                                                    {{ $row->value }}
                                                </td>
                                            </tr>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            {{--<tr>--}}
                            {{--<th colspan="6">Total</th>--}}
                            {{--<th>Rs. </th>--}}
                            {{--</tr>--}}
                            </tfoot>
                        </table>
                        @if($docketTimer->count()>0)
                            <div class="attachedTimer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><b> <i class="fa fa-paperclip" aria-hidden="true"></i>
                                                Timer Attachements</b></p>
                                    </div>
                                    @php
                                        $totalInterval = 0;
                                    @endphp
                                    @if($docketTimer->count())
                                        @foreach($docketTimer as $row)
                                            <div class="col-md-2">
                                                <div class="box-timer">
                                                    <i class="fa fa-clock-o" aria-hidden="true"></i>
                                                    <p><strong>@php
                                                                if($row->timerInfo->time_ended != NULL){
                                                                    $datetime1 = \Carbon\Carbon::parse($row->timerInfo->time_started);
                                                                    $datetime2 = \Carbon\Carbon::parse($row->timerInfo->time_ended);
                                                                    $interval = $datetime2->diffInSeconds($datetime1);
                                                                    $date = $interval - $totalInterval;
                                                                    echo gmdate("H:i:s", $date);
                                                                }
                                                            @endphp</strong></p>
                                                    <i class="fa fa-map-marker" aria-hidden="true"></i> <span>{!!  str_limit(strip_tags($row->timerInfo->location),35) !!}</span>
                                                    <p> {{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif

                                </div>

                            </div>
                        @endif
                        @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                            <div class="main-size" id="mobileviewHtml">
                                @include('dashboard.company.docketManager.docket.approvalTypeView')
                            </div>
                        @endif

                    </div>
                    <!-- /.col -->


                </div>
            </div>


            <div class="row no-print" >

                <div class="col-md-12 rejectedview" style="    margin-left: 14px;">
                    @if(count(@$sentDocket->sentDocketRejectExplanation) != 0)
                        <h5 style='font-weight: 800;'>Rejected By:</h5>
                        <ul>
                            @foreach(@$sentDocket->sentDocketRejectExplanation as $sentDocketRejection)
                                <li><b>{{@$sentDocketRejection->userInfo->first_name}} </b> {{@$sentDocketRejection->explanation}}     {{$sentDocketRejection->created_at}} </li>
                            @endforeach
                        </ul>
                    @endif
                </div>


                @if($sentDocket->shareFolderUserId()['type'] != "Public")
                    @if(@$sentDocket->docketApprovalType == 1 || @$sentDocket->docketApprovalType == 0 )

                        <div class="col-md-12">
                            @if(@$sentDocket->status==0)
                                @if(@$sentDocket->sentDocketRecipientApproval)
                                    @if(in_array($sentDocket->shareFolderUserId()['user']['id'],$sentDocket->sentDocketRecipientApproval->pluck('user_id')->toArray()))
                                        @if(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',1)->where('user_id',$sentDocket->shareFolderUserId()['user']['id'])->count()==1)
                                            <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>

                                        @elseif(App\SentDocketRecipientApproval::where('sent_docket_id',$sentDocket->id)->where('status',3)->where('user_id',$sentDocket->shareFolderUserId()['user']['id'])->count()==1)
                                            <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-danger pull-right" id="addNew"><i class="fa fa-check"></i> Rejected</a>
                                        @else
                                            @if($sentDocket->docketApprovalType==0)
                                                <div class="pull-right">
                                                    <button class="btn btn-raised btn-xs  btn-success" sentDocketIdsappr="{{$sentDocket->id}}" id="ApproveDocketType"><i class="fa fa-check"></i> Approve</button>

                                                    <button class="btn btn-raised btn-xs  btn-danger rejectDocketbutton" data-senddocketid="{{$sentDocket->id}}" data-toggle="modal" data-target="#rejectDocketModal" ><i class="fa fa-check"></i>Reject</button>
                                                </div>
                                            @else
                                                <div class="pull-right">
                                                    <a id="first" class="btn btn-raised btn-xs  btn-success modalApprove " data-toggle="modal" data-id="{{$sentDocket->id}}" data-target="#myModal3"><i class="fa fa-check"></i>Approve</a>
                                                    <button class="btn btn-raised btn-xs  btn-danger rejectDocketbutton" data-senddocketid="{{$sentDocket->id}}" data-toggle="modal" data-target="#rejectDocketModal" id=""><i class="fa fa-check"></i>
                                                        Reject
                                                    </button>
                                                </div>
                                            @endif

                                        @endif
                                    @else
                                        <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-warning pull-right" id="addNew"><i class="fa fa-check"></i> Pending</a>
                                    @endif
                                @endif
                            @elseif($sentDocket->status==3)
                                <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-danger pull-right" id="addNew"><i class="fa fa-check"></i> Rejected</a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>
                            @endif
                        </div>
                    @endif
                 @endif





            </div>


        </div>
    </div>
    <br/><br/>

    <div class="modal fade" id="rejectDocketModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Reject Docket</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="reject_docket_id" >
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to Reject Docket?</p>
                            <label class="control-label" for="title">Explain</label>
                            <input class="form-control" id="error_explain">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submitRejectDocket">Submit</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/view/approve' , 'files' => true]) }}--}}
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Docket Approvel</h4>
                </div>
                <div class="modal-body" style="    height: 446px;">
                    <div class="row">
                        <div class="col-md-12">
                            <div style="margin-top: 4px;" class="form-group">
                                <input type="hidden" id="docket_aprovel_name"  name="sentDocketId" value="">
                                <label class="control-label" for="title">Name <b style="color:red;    font-size: 13px;">*</b></label>
                                <input type="text" id="name_approval"  name="name" class="form-control" value="{{@Auth::user()->first_name}} {{@Auth::user()->last_name}}">
                                <p style="display: none; color: red;" class="flashsuccessText"><i>*Name Required</i> </p>
                            </div>
                            <div style="margin-top: 4px;    margin-bottom: 47px;" class="form-group">
                                {{--<label style="   background: #919191;width: 100%;height: 200px;padding: 83px;font-size: 26px;text-align: center;" class="control-label" for="title">Click here to upload Sign</label>--}}
                                {{--<div class="box-signature-shown"></div>--}}
                                {{--<input type='file' name="signature" id="imgInp" />--}}
                                {{--<img id="blah"  />--}}
                                {{--</div>--}}
                                <div class="wrapper1">
                                    <label class="control-label" for="title">Signature <b style="color:red;    font-size: 13px;">*</b></label><br><br>
                                    <img style="background-color: #ebebeb;" name="signature" width=550 height=200 />
                                    <canvas id="signature-pad" class="signature-pad"  width=532 height=200></canvas>
                                    <p style="display: none;   position: absolute;    bottom: -65px;left: 5px;color: red;" class="flashsuccess"><i>*Signature Required</i> </p>
                                </div>
                                <div>
                                    {{--<button id="save">Save</button>--}}
                                    <button style="    position: absolute;right: 2px;top: 9px;" id="clear">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-top: -89px;" class="modal-footer">
                    <button id="save" type="submit" class="btn btn-primary">Authorise</button>
                </div>
                {{--{{ Form::close() }}--}}
            </div>
        </div>
        <style>
            .printColorDark{ padding: 8px;display:block;background-color: #ddd !important;-webkit-print-color-adjust: exact;}
            .printTh{ padding: 0px !important; }
            .printColor{padding: 8px;display:block;background-color: #eee !important;-webkit-print-color-adjust: exact;}
        </style>
        @endsection

        @section('customScript')
            <style>
                td table{
                    border-spacing: 0 !important;
                    width: 100% !important;
                }
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
                    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                    transition: 0.3s;
                    width: 100%;
                    padding: 20px 0 20px 0px;
                    margin-bottom: 20px;
                }
                .box-timer:hover {
                    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
                }

            </style>
            <script type="text/javascript" src="{{ 'assets/dashboard/js/printThis.js') }}"></script>
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

                $('#rejectDocketModal').on('shown.bs.modal', function (e) {
                    var docketId = $(e.relatedTarget).data('senddocketid');
                    $("#reject_docket_id").val(docketId);
                });

                $(document).on('click','#submitRejectDocket',function () {
                    var docket_id = $('#reject_docket_id').val();
                    var  explain = $('#error_explain').val();
                    $.ajax({
                        type: "post",
                        data:{docket_id : docket_id, explanation:explain },
                        url: "{{url('folder/docket/reject')}}",
                        success: function (response) {
                            $('.rejectDocketbutton').replaceWith( '<a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-danger pull-right" id="addNew"><i class="fa fa-check"></i> Rejected</a>');
                            $('#ApproveDocketType').css('display','none')
                            $('.modalApprove').css('display','none')
                            $('.rejectedview').html(response.data);
                            $.ajax({
                                type: "GET",
                                url:"{{url('folder/docket/approvalTypeView/'.$sentDocket->id) }}",
                                success:function (response) {
                                    $("#mobileviewHtml").html(response);
                                    $('#rejectDocketModal').modal('hide');


                                }
                            });
                        }
                    })

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
                                url: "{{ url('folder/docket/view/approve') }}",
                        success: function (response) {
                            $('.dashboardFlashsuccess').css('display','none');
                            if (response['status']==true) {
                                var wrappermessage = ".messagesucess";
                                $(wrappermessage).html(response["message"]);
                                $('.dashboardFlashsuccess').css('display','block');
                                $.ajax({
                                    type: "GET",
                                    url:"{{url('folder/docket/approvalTypeView/'.$sentDocket->id) }}",
                                    success:function (response) {
                                        $("#mobileviewHtml").html(response);
                                        $('#ApproveDocketType').replaceWith( '<a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>');

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
                    if ($('input#name_approval').val() == ""){
                        $('.flashsuccessText').css('display','block');
                    }

                    var demoo=signaturePad.isEmpty();
                    if (demoo){
                        $('.flashsuccess').css('display','block');
                    }else {
                        var data = signaturePad.toDataURL('image/png');
                        var sentDocketId_approval = $('input[name=sentDocketId]').val();
                        var name_approval = $('input[name=name]').val();
                        $.ajax({
                            type: "POST",
                            data: {signature: data,sentDocketId:sentDocketId_approval,name:name_approval},
                            url: "{{ url('folder/docket/view/approve') }}",
                            success: function (response) {
                                $('.dashboardFlashsuccess').css('display','none');
                                if (response['status']==true) {
                                    var wrappermessage = ".messagesucess";
                                    $(wrappermessage).html(response["message"]);
//                        $("#mobileviewHtml").html(response);
                                    $('.dashboardFlashsuccess').css('display','block');
                                    $.ajax({
                                        type: "GET",
                                        url:"{{url('folder/docket/approvalTypeView/'.$sentDocket->id) }}",
                                        success:function (response) {
                                            $("#mobileviewHtml").html(response);
                                        }
                                    });
                                }
                                $('#myModal3').modal('hide');

                            }
                        });
                    }



// Send data to server instead...
                });

                cancelButton.addEventListener('click', function (event) {
                    signaturePad.clear();
                });
            </script>

            <script>
                function goBack() {
                    if (document.referrer == "") {
                        window.location = "{{ url('dashboard/company/docketBookManager/docket/allDockets') }}";
                    } else {
                        history.back()
                    }
                }
            </script>
@endsection
