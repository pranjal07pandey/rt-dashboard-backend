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

    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="col-md-12">
            <div class="row  with-border" style="border-bottom: 1px solid #ddd;margin-bottom: 15px;padding-bottom:10px;">
                <div class="col-md-6 text-left">
                    <button  class="btn btn-default btn-sm" onclick="goBack()" style="margin:0px;"><i class="fa fa-reply"></i> Back</button>
                </div>
                <div class="col-md-6 text-right">
                    <button  class="btn btn-default btn-sm" onclick="location.href='{{url('/folder/docket/download/emailed/'.$sentDocket->encryptedID())}}';" style="margin: 0px 0px 0px;"><i class="fa fa-download" aria-hidden="true"></i></i> Download</button>
                    <button  class="btn btn-default btn-sm" id="printDiv" style="margin: 0px 0px 0px;"><i class="fa fa-print"></i> Print</button>
                </div>
            </div>

            <div id="printContainer">
                <div class="row invoice-info">
                    <div class="col-md-12 invoice-col" style="width: 100%;">
                        <div class="pull-left" >
                            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="height:150px;margin-bottom:10px;">
                            @else
                                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo" style="margin-bottom:10px;">
                            @endif
                        </div>


                        <div class="pull-right" style="text-align:left;width:170px;">
                            <strong>{{ $sentDocket->template_title }}</strong><br/>
                            <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>
                            <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>
                            {{ $sentDocket->formatted_id }}

                            <br>
                        </div>


                    </div>
                    <div class="clearfix"></div>


                    <div class="col-md-12 invoice-col" style="width: 100%;">
                        <div class="col-md-8 pull-left"  style="width: 60%;">
                            <br/>From:<br/>
                            <strong>{{ @$sentDocket->sender_name }}</strong><br>
                            {{ @$sentDocket->company_name }}
                            <br>
                            {{ @$sentDocket->company_address }}<br>
                            <b>ABN:</b> {{ @$sentDocket->abn }}
                            <br/><br/>
                        </div>

                        <div class="col-md-4 pull-right" style="    width: 40%">
                            To:
                            @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp
                            @foreach($sentDocket->recipientInfo as $recipient)
                                @php
                                    $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;
                                    $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;

                                    $data[]= array(
                                     'email'=>$recipient->emailUserInfo->email,
                                     'company_name'=>$recipient->receiver_company_name
                                );
                                @endphp
                            @endforeach

                            @foreach($distinctValue as $rowData)

                                @if($rowData == "")
                                    @php $recipientNames =  ''; @endphp
                                    @foreach($data as $items)
                                        @if($rowData == $items['company_name'])
                                            @php $recipientNames .=  $items['email'].', ';@endphp

                                        @endif
                                    @endforeach
                                    <br> {{strtolower(substr($recipientNames, 0, -2))}}
                                @endif
                            @endforeach

                            @foreach($distinctValue as $rowData)
                                @if($rowData != "")

                                    <br> <strong>{{$rowData}}: </strong>
                                    @php $recipientNames =  ""; @endphp
                                    @foreach($data as $items)
                                        @if($rowData == $items['company_name'])
                                            @php $recipientNames .=  $items['email'].', ' @endphp

                                        @endif
                                    @endforeach
                                    {{strtolower(substr($recipientNames, 0, -2))}}

                                @endif



                            @endforeach
                        </div>
                    </div>
                    <div class="clearfix"></div>


                    <!-- /.col -->


                    <!-- /.col -->
                </div>
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <br/>
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Description</div></th>
                                <th class="printTh" style="width:50%"><div class="printColorDark">Value</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if($docketFields)

                                @foreach($docketFields as $row)

                                    @if($row->docketFieldInfo->docket_field_category_id==7)
                                        <?php $sn = 1; $total = 0; ?>
                                        @foreach($row->sentDocketUnitRateValue as $row)
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
                                        <tr >
                                            <td>
                                                <strong>Total:</strong>
                                            </td>
                                            <td>
                                                <strong>$ {{ $total }}</strong>
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
                                            <td> @if($row->value==1) <i class="fa fa-check-circle" style="color:green"></i> @else <i class="fa fa-close" style="color:red"></i>@endif  </td>
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
                                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
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
                                                    <ul style="list-style: none;margin: 0px;padding: 10px;">
                                                        @foreach($row->sentDocketImageValue as $signature)
                                                            <br>
                                                            <li style="margin-right:10px;display: inline-block; padding-bottom: 13px; ">
                                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" width="100px"  style="border:1px solid #dddddd;">
                                                                </a>
                                                            </li>

                                                        @endforeach

                                                    </ul>
                                                @else
                                                    <b>No Image Attached</b>
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
                                                    <b>No Sketch Pad Attached</b>
                                                @endif
                                            </td>
                                        </tr>
                                    @elseif($row->docketFieldInfo->docket_field_category_id==15)
                                        <tr>
                                            <td> {{ $row->label }}</td>
                                            <td>
                                                @if($row->sentEmailAttachment->count()>0)
                                                    <ul class="pdf">
                                                        @foreach($row->sentEmailAttachment as $document)
                                                            <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></b></li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <b>No Document Attached</b>
                                                @endif
                                            </td>
                                        <!--<td> {{ $row->value }}</td>-->
                                        </tr>

                                    @elseif($row->docketFieldInfo->docket_field_category_id==20)

                                        <tr>
                                            <td>{{ $row->label }}</td>
                                            <td>
                                                @foreach($row->emailSentDocManualTimer as $rows)
                                                    <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                                @endforeach
                                                <br>
                                                @foreach($row->emailSentDocManualTimerBrk as $item)
                                                    <strong>{{ $item->label }} :</strong>  {{ $item->value }}<br>
                                                    <strong>Reason for break :</strong>  {{ $item->reason }}<br>
                                                @endforeach
                                                <strong>Total time :</strong>  {{ $row->value }}<br>

                                            </td>
                                        </tr>
                                    @elseif($row->docketFieldInfo->docket_field_category_id==12)
                                        <tr>
                                            <td  colspan="2"> <strong>{{ $row->label }}</strong></td>
                                        </tr>

                                    @elseif($row->docketFieldInfo->docket_field_category_id == 22)
                                        <tr>
                                            <td colspan="2">{{ $row->label }}

                                                <div style="    width: 1094px;overflow: auto;">
                                                    <table  class="table table-striped" width="100%">
                                                        <thead>
                                                        <tr>
                                                            @foreach($row->emailSentDocketFieldGridLabels as $gridFieldLabels)
                                                                <th class="printTh" style="min-width: 200px">
                                                                    <div class="printColorDark">{{ $gridFieldLabels->label}}</div>
                                                                </th>
                                                            @endforeach
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @php
                                                            $gridMaxRow     =    $row->emailSentDocketFieldGridValues->max('index');
                                                        @endphp
                                                        @for($i = 0; $i<=$gridMaxRow; $i++)
                                                            <tr>
                                                                @foreach($row->emailSentDocketFieldGridLabels as $gridFieldLabels)
                                                                    @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || $gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == 'N/a')
                                                                            <td>N/a</td>
                                                                        @else

                                                                            @php
                                                                                $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value);
                                                                            @endphp

                                                                            <td>
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
                                                                    @elseif($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 9)

                                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == 'N/a')
                                                                            <td>N/a</td>
                                                                        @else
                                                                            @php
                                                                                $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value);
                                                                            @endphp

                                                                            <td>
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
                                                                    @else
                                                                        @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 8)
                                                                            <td>
                                                                                @php
                                                                                    $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value;
                                                                                @endphp
                                                                                @if($value==1)
                                                                                    <i class="fa fa-check-circle" style="color:green"></i>
                                                                                @else
                                                                                    <i class="fa fa-close" style="color:#ff0000 !important"></i>
                                                                                @endif
                                                                            </td>
                                                                        @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                                                            <?php
                                                                            $manualTimerGrid =  @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value;
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
                                                                                    <strong>Total Break :</strong> {{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($breakDuration) }}  <br>
                                                                                    <strong>Reason for break :</strong>  {{ json_decode($manualTimerGrid , true)['explanation'] }}<br>
                                                                                    <strong>Total time :</strong>  {{ \App\Http\Controllers\CompanyDashboard::convertHrsMin($totalDuration) }}<br>
                                                                                </td>
                                                                            @else
                                                                                <td>N/a</td>

                                                                            @endif

                                                                        @elseif($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 29)
                                                                            @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value == null)
                                                                                <td></td>
                                                                            @else
                                                                                <td  style="line-height: 2em;white-space: pre-wrap;">
                                                                                    @foreach(unserialize( @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value) as $data)
                                                                                        {!! $data['email'] !!}
                                                                                    @endforeach
                                                                                </td>
                                                                            @endif
                                                                        @else


                                                                            <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 1)->first()->value }}</td>
                                                                        @endif
                                                                    @endif
                                                                @endforeach
                                                            </tr>
                                                        @endfor
                                                        </tbody>

                                                        <tfooter>
                                                            <tr>
                                                                @foreach($row->emailSentDocketFieldGridLabels as $gridFieldLsa)
                                                                    @if($gridFieldLsa->sumable == 1)
                                                                        @if($gridFieldLsa->docketFieldGrid->docket_field_category_id == 3)
                                                                            <?php
                                                                            $arryForSum = @\App\DocketFieldGridValue::where('docket_id',$sentDocket->id)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLsa->docket_field_grid_id)->where('is_email_docket', 1)->pluck('value')->toArray();
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
                                                <!--<table style="width:100%;">-->
                                                <!--<tr>-->
                                                @php
                                                    $yesno = unserialize($row->label);
                                                @endphp
                                                <div style="width:100%;margin:0;">
                                                    <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
                                                    @if($row->value == "N/a")
                                                        <div style="width:50%; float:right;padding-left: 8px;"> N/a </div>
                                                    @else
                                                        @if($yesno['label_value'][$row->value]['label_type']==1)
                                                            <div style="width:50%; float:right;   padding-left: 8px;"><img style="width: 20px; height:20px; background-color:{{ $yesno['label_value'][$row->value]['colour']}}; border-radius:20px;padding:4px;" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$row->value]['label'] }}"></div>
                                                        @else
                                                            <div style="width:50%; float:right;  padding-left: 8px;">{{ $yesno['label_value'][$row->value]['label']}}</div>
                                                        @endif
                                                    @endif
                                                </div>

                                                <!-- </tr>-->
                                                <!--</table>-->
                                                @if(count($row->SentEmailDocValYesNoValueInfo)==0)
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
                                                                    <td style="    width: 50%;">{{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
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
                                                                    <td> {{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
                                                                    <td>{{$items->value }}</td>
                                                                </tr>
                                                            @endif
                                                            @if($items->YesNoDocketsField->docket_field_category_id==2)
                                                                <tr>
                                                                    <td> {{ $items->label }}&nbsp; @if(@$items->required==1)*@endif</td>
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
                                    @elseif($row->docketFieldInfo->docket_field_category_id == 29 )
                                        <tr>
                                            <td> {{ $row->label }}</td>
                                            <td style="line-height: 1.5em;white-space: pre-wrap;">
                                                @foreach(unserialize($row->value) as $email)
                                                    {!! $email['email'] !!}
                                                @endforeach
                                            </td>
                                        </tr>

                                    @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!= 30)
                                        <tr>
                                            <td> {{ $row->label }}</td>
                                            <td style="line-height: 1.5em;white-space: pre-wrap;"> {!! $row->value !!} </td>
                                        </tr>
                                    @endif
                                @endforeach
                                @foreach($docketFields as $row)
                                    @if($row->docketFieldInfo->docket_field_category_id==13)
                                        <tr>
                                            <td  colspan="2"> <strong style="font-size: 14px; font-weight: 300;">{{ $row->label }}</strong><br>
                                                {{ $row->value }}
                                            </td>
                                        </tr>
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

                    </div>

                    @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                        @if($sentDocket->docketApprovalType==0)
                            <div class="col-md-12">
                                <hr class='dotted' />
                                <div class="col-md-6">
                                    <h5 style="font-weight: 800;">Approved By:</h5>
                                    @foreach($approval_type as $row)
                                        @if($row['status']==1)
                                            {{--<img style="width: 63px;float: left;margin-right: 16px;" src="{{asset($row->signature)}}">--}}
                                            <p style="padding-top: 8px;">{{@$row['email']}}  on {{\Carbon\Carbon::parse($row['approval_time'])->format('d-M-Y h:i a T')}}</p>
                                            <div class="clearfix"></div>
                                            <br>
                                        @endif

                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    @if($sentDocket->status==0)
                                        <h5 style="font-weight: 800;">Pending Approval:</h5>
                                        @foreach($approval_type as $row)
                                            @if($row['approval']==1 && $row['status']!=1)
                                                <p>{{@$row['email']}}</p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="col-md-12">
                                <hr class='dotted' />
                                <div class="col-md-6">
                                    <h5 style="font-weight: 800;">Approved By:</h5>

                                    @foreach($sentDocket->recipientInfo as $row)

                                        @if($row->status==1)
                                            <img style="width: 84px;float: left;margin-right: 16px;" src="{{ AmazoneBucket::url() }}{{ @$row->signature }}">
                                            <p style="padding-top: 8px;">{{@$row->name}} on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                                        @endif
                                        <div class="clearfix"></div>
                                        <br>
                                    @endforeach

                                </div>
                                <div class="col-md-6">
                                    @if($sentDocket->status==0)
                                        <h5 style="font-weight: 800;">Pending Approval:</h5>
                                        @foreach($sentDocket->recipientInfo as $row)
                                            @if($row->approval==1)
                                                <p >{{$row->emailUserInfo->email}} </p>
                                            @endif
                                        @endforeach
                                    @endif
                                </div>


                            </div>
                    @endif
                @endif

                <!-- /.col -->
                </div>
            </div>

            @if($sentDocket->shareFolderUserId()['type'] == "Restricted")
              @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
                    @if(in_array($sentDocket->shareFolderUserId()['user']['id'],$sentDocket->recipientInfo->pluck('email_user_id')->toArray()))
                        @if(App\EmailSentDocketRecipient::where('email_sent_docket_id',$sentDocket->id)->where('approval',1)->where('email_user_id',$sentDocket->shareFolderUserId()['user']['id'])->count()==1)
                            @if(App\EmailSentDocketRecipient::where('email_sent_docket_id',$sentDocket->id)->where('status',1)->where('email_user_id',$sentDocket->shareFolderUserId()['user']['id'])->count()==1)
                                <a class="btn btn-success btn-xs btn-raised pull-right" href="#">
                                    <i class="fa fa-check" aria-hidden="true"></i> Approved
                                </a>
                            @else
                                @if($sentDocket->docketApprovalType == 1)
                                    <a class="btn btn-success btn-xs btn-raised pull-right" data-toggle="modal" data-target="#signatureApprove" style="color: #fff;">
                                        <i class="fa fa-check" aria-hidden="true"></i> Approve
                                    </a>
                                @else
                                    <a class="btn btn-success btn-xs btn-raised pull-right" href="{{ url('folder/docket/emailed/'.$sentDocket->id.'/'.App\EmailSentDocketRecipient::where('email_sent_docket_id',$sentDocket->id)->where('email_user_id',$sentDocket->shareFolderUserId()['user']['id'])->get()->first()->hashKey.'/approve') }}">
                                        <i class="fa fa-check" aria-hidden="true"></i> Approve
                                    </a>
                                @endif
                            @endif

                         @endif
                     @else
                        <div class="col-xs-12">
                            @if($sentDocket->status==0)
                                <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-warning pull-right" id="addNew"><i class="fa fa-check"></i> Pending</a>
                            @else
                                <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>
                            @endif
                        </div>
                    @endif
                @endif
            @else
                <div class="col-xs-12">
                    @if($sentDocket->status==0)
                        <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-warning pull-right" id="addNew"><i class="fa fa-check"></i> Pending</a>
                    @else
                        <a href="javascript:void(0);" class="btn btn-raised btn-xs  btn-success pull-right" id="addNew"><i class="fa fa-check"></i> Approved</a>
                    @endif
                </div>
            @endif

                {{--</div>--}}
            {{--@endif--}}
        </div>
    </div>
    <br/><br/>



    @if($sentDocket->docketApprovalType == 1)

            <div class="modal fade"  id="signatureApprove" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Docket Approval</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="margin-top: 4px;" class="form-group">
                                        <input type="hidden" id="docket_aprovel_name"  name="sentDocketId" value="">
                                        <input type="hidden" id="docket_hashKey" name="hashKey" value="">
                                        <label class="control-label" for="title">Name<b style="color:red;    font-size: 13px;">*</b></label>
                                        <input type="text" id="name_approval"  name="name" class="form-control" value="" required>
                                        <p style="display: none; font-size: 12px; color: red;" id="title" class="flashsuccess"><i>*Title Required</i> </p>
                                    </div>
                                    <div style="margin-top: 4px;" class="form-group">

                                        <div class="wrapper1">
                                            <label class="control-label" for="title">Signature<b style="color:red;    font-size: 13px;">*</b></label><br>
                                            <canvas id="signature-pad" class="signature-pad" width="466px" height="200px"></canvas>
                                            <p style="display: none; font-size: 12px; color: red;" class="flashsuccess" id="signature"><i>*Signature Required</i> </p>
                                        </div>
                                        <div>
                                            <button  class="btn btn-sm btn-link" style="position: absolute;right: 18px;top: 90px" id="clear">Clear</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="save" class="btn btn-primary">Authorise</button>
                        </div>
                    </div>
                </div>
            </div>


            <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
            <script>
                if(window.innerWidth<466){
                    $("#signature-pad").attr("width",(window.innerWidth -55)+"px");
                }
                console.log(window.innerWidth);
                var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: 'rgb(0, 0, 0)'
                });

                var saveButton = document.getElementById('save');
                var cancelButton = document.getElementById('clear');

                cancelButton.addEventListener('click', function (event) {
                    signaturePad.clear();
                });



                saveButton.addEventListener('click', function (event) {


                    if ($('input#name_approval').val() == ""){
                        $('#title').css('display','block');
                    }
                    else if(signaturePad.isEmpty()){
                        $('#signature').css('display','block');
                    }else {
                        $(".flashsuccess").fadeOut();
                        var data = signaturePad.toDataURL('image/png');
                        var name_approval = $('input#name_approval').val();

                        $.ajax({
                            type: "get",
                            data: {"signature": data,"name":name_approval,"_token": "{{ csrf_token() }}"},
                            url: "{{ url('folder/docket/emailed/'.$sentDocket->id.'/'.App\EmailSentDocketRecipient::where('email_sent_docket_id',$sentDocket->id)->where('email_user_id',$sentDocket->shareFolderUserId()['user']['id'])->get()->first()->hashKey.'/approve') }}",
                            success: function (response) {

                            }
                        });

                    }
                });
            </script>

    @endif



@endsection

@section('customScript')
    <style>
        .printColorDark{ padding: 8px;display:block;background-color: #ddd !important;-webkit-print-color-adjust: exact;}
        .printTh{ padding: 0px !important; }
        .printColor{padding: 8px;display:block;background-color: #eee !important;-webkit-print-color-adjust: exact;}
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
    <script type="text/javascript" src="{{ asset('assets/dashboard/js/printThis.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script>
        $(document).ready(function(){
            $("#printDiv").on("click",function(){
                $('#printContainer').printThis({
                    removeInline: false,
                    importCSS: true,
                    importStyle: true
                });
            });
            var clipboard = new ClipboardJS('.btn');
        })
    </script>
    <script>
        function goBack() {
            if (document.referrer == "") {
                window.location = "{{ url('dashboard/company/docketBookManager/docket/emailed') }}";
            } else {
                history.back()
            }
        }
    </script>
@endsection
