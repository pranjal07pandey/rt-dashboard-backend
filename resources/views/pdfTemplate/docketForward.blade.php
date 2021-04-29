<style>
    table>thead>tr>th {
        vertical-align: bottom;
        border-bottom: 2px solid #ddd;
        background: #ddd;
        padding: 13px;
    }
    table>tbody>tr>td, table>tbody>tr>th, table>tfoot>tr>td, table>tfoot>tr>th, table>thead>tr>td, table>thead>tr>th {

    }
    table>tbody>tr>td, table>tbody>tr>th, table>tfoot>tr>td, table>tfoot>tr>th, table>thead>tr>td, table>thead>tr>th {
        padding: 13px;
        vertical-align: top;
        border-top: 1px solid #ddd;
    }
    table>tbody>tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
    td {
        vertical-align: top;
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
    .box-timer{
        text-align: center;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
        transition: 0.3s;
        padding: 20px 0 20px 0px;
        border: 1px solid #c1bcbc;
    }
    .box-timer:hover {
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    td table{
        border-spacing: 0 !important;
        width: 100% !important;
    }
</style>

<div style="page-break-inside: avoid;font-size:20px;line-height:1.6em">

    <div style="width:100%;">
        <div style="width:50%;float:left;">
            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="height:150px;">
            @else
                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo" >
            @endif<br/>
        </div>
        <div style="width:50%;float:right;">
            <div style="float:right;width:200px;">
                <b>{{ @$sentDocket->docketInfo->title }}</b><br/>
                <b>Date:</b> {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>
                <b>{{$sentDocket->docketInfo->docket_id_label}}:</b>
                {{ $sentDocket->formatted_id }}

                <br>
            </div>
        </div>
    </div>
    <div style="clear:both"></div>

    <div style="width:100%;">
        <div style="width:60%;float:left;">

            From:<br/>
            <strong>{{ @$sentDocket->sender_name }}</strong><br>
            {{ @$sentDocket->company_name }}<br>
            {{ @$sentDocket->company_address }}<br>
            <b>ABN:</b> {{ @$sentDocket->abn }}
            <br/><br/>
        </div>
        <div style="width:40%;float:left;">
            <br/>To:
            @if($sentDocket->formattedRecipientList())
                <?php $sns = 0; ?>
                @foreach($sentDocket->formattedRecipientList() as $key=>$value)
                    @foreach($value as $keys)
                        @php $sns++; @endphp
                        @if($sns<=count($sentDocket->recipientInfo) && $sns!=1) , @endif
                        {{ $keys }}
                    @endforeach
                @endforeach
                <br>
                <?php $sn = 0; ?>
                <b>Company Name:</b>
                @foreach($sentDocket->formattedRecipientList() as $key=>$value)
                    <?php $sn++; ?>
                    @if($sn<=count($sentDocket->formattedRecipientList()) && $sn!=1) , @endif
                    {{$key}}
                @endforeach
            @endif
        </div>
    </div>
    <div style="clear:both"></div>

    <table width="100%" style="margin-top: 10px;">
        <thead>
        <tr>
            <th width="50%">Description</th>
            <th width="50%">Value</th>
        </tr>
        </thead>
        <tbody>
        @if($docketFields)
            @foreach($docketFields as $row)
                @if((!$row->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))

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
                                <strong>$ {{ round($total,2) }}</strong>
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
                            <td>{{ $row->label }}</td>
                            <td>
                                @if($row->value==1)<img src="{{ asset('assets/dashboard/img/checked.png') }}" width="20px">
                                @else  <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="20px">@endif
                            </td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==20)

                        <tr>
                            <td>{{ $row->label }}</td>
                            <td>
                                @foreach($row->sentDocketManualTimer as $rows)
                                    <strong>{{ $rows->label }} :</strong>  {{ $rows->value }} &nbsp; &nbsp;
                                @endforeach
                                <br>
                                @foreach($row->sentDocketManualTimerBreak as $item)
                                    <strong>{{ $item->label }} :</strong>  {{ $item->value }}<br>
                                    <strong>Reason for break :</strong>  {{ $item->reason }}<br>
                                @endforeach
                                <strong>Total time :</strong>  {{ $row->value }}<br>

                            </td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==9)

                        <tr>
                            <td> {{ $row->label }}</td>
                            <td>
                                @if($row->sentDocketImageValue->count()>0)
                                    <ul class="signatureList" style="list-style: none;">
                                        @php
                                            $sn = 1; @endphp
                                        @foreach($row->sentDocketImageValue as $signature)
                                            <li style="  display:inline-block;margin: 10px;">
                                                <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="width:90px; height 90px;">
                                                <p style="font-weight: 500;">{{$signature->name}}</p>
                                            </li>

                                            @if($sn==3)
                                                <br/>
                                                @php $sn=0; @endphp
                                            @endif
                                            @php $sn++; @endphp
                                        @endforeach
                                    </ul>
                                @else
                                    <b>No Signature Attached</b>
                                @endif
                                <div style="clear:both;"></div>
                            </td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==5)

                        <tr>
                            <td> {{ $row->label }}</td>
                            <td>
                                <div style="clear:both;"></div><br/>
                                @if($row->sentDocketImageValue->count()>0)
                                    <ul style="list-style: none;margin: 0px;padding: 20px;">
                                        @php $sn = 1; @endphp
                                        @foreach($row->sentDocketImageValue as $signature)
                                            <li style="margin-right:10px;display: inline-block; padding-bottom: 13px; ">
                                                <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" width="100px"  style=" border:1px solid #dddddd;">
                                                </a>
                                            </li>
                                            @if($sn==3)<br/> @php $sn=0; @endphp @endif
                                            @php $sn++; @endphp
                                        @endforeach
                                    </ul>
                                @else
                                    <b>No Image Attached</b>
                                @endif
                                <div style="clear:both;"></div>
                            </td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==14)
                        <tr>
                            <td> {{ $row->label }}</td>
                            <td>
                                @if($row->sentDocketImageValue->count()>0)
                                    <ul style="list-style: none;margin: 0px;padding: 0px;margin-top:5px;">
                                        @php $sn = 1; @endphp
                                        @foreach($row->sentDocketImageValue as $sketchPad)
                                            <li style="display:inline-block;">
                                                <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank">
                                                    <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="height: 100px;margin: 10px;padding: 10px;">
                                                </a>
                                            </li>
                                            @if($sn==3)<br/> @php $sn=0; @endphp @endif
                                            @php $sn++; @endphp
                                        @endforeach
                                    </ul>
                                @else
                                    <b>No Sketch Attached</b>
                                @endif
                                <div style="clear:both"></div>
                            </td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==15)
                        <tr>
                            <td> {{ $row->label }}</td>
                            <td>
                                @if($row->sentDocketAttachment->count()>0)
                                    <ul class="pdf">
                                        @foreach($row->sentDocketAttachment as $document)
                                            <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank" style="font-size: 16px;">{{$document->document_name}}</a></b></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <b>No Document Attached</b>
                                @endif
                                <div style="clear:both"></div>
                            </td>
                        <!--<td> {{ $row->value }}</td>-->
                        </tr>

                    @elseif($row->docketFieldInfo->docket_field_category_id==12)

                        <tr>
                            <td  colspan="2"> <strong>{{ $row->label }}</strong></td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==27)

                        <tr>
                            <td  colspan="2">{!! $row->label !!}</td>
                        </tr>
                    @elseif($row->docketFieldInfo->docket_field_category_id==18)

                        <tr>
                            <td colspan="2">
                                @php $yesno = unserialize($row->label); @endphp
                                <div style="width:100%;margin:0;">
                                    <div style="width:50%;float:left;">{{ $yesno['title']}}</div>
                                    @if($row->value == "N/a")
                                        <div style="width:50%; float:right;margin-right: -14px;"> N/a </div>
                                    @else
                                        @if($yesno['label_value'][$row->value]['label_type']==1)
                                            <div style="width:50%; float:right;margin-right: -14px;"><img style="width: 20px; height:20px; padding:4px; background-color:{{ $yesno['label_value'][$row->value]['colour']}};" src="{{ AmazoneBucket::url() }}{{ $yesno['label_value'][$row->value]['label'] }}"></div>
                                        @else
                                            <div style="width:50%; float:right;margin-right: -14px;">{{ $yesno['label_value'][$row->value]['label']}}</div>
                                        @endif
                                    @endif
                                    <div style="clear:both"></div>
                                </div>
                                @if(count($row->SentDocValYesNoValueInfo)==0)
                                @else
                                    <table style="background: transparent; width: 100%;" class="table">
                                        <thead style="background: transparent; ">
                                        <tr>
                                            <th width="50%" style="border: none;"><strong style="margin-bottom: 0px;font-size: 18px;color: #929292;padding:0px;">Explanation</strong></th>
                                            <th width="50%"  style="border: none;"></th>
                                        </tr>
                                        </thead>
                                        <tbody >
                                        @foreach($row->SentDocValYesNoValueInfo as $items)
                                            @if($items->YesNoDocketsField->docket_field_category_id==5)
                                                @php $imageData=unserialize($items->value); @endphp
                                                <tr>
                                                    <td>{{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                    <td style="margin-bottom:10px;">
                                                        <ul style="list-style: none;margin: 0px;padding: 0px;margin-top:20px;">
                                                            @if(empty($imageData))
                                                                <b>No Image Attached</b>
                                                            @else
                                                                @foreach($imageData as $rowData)
                                                                    <li style="margin-right:10px;float: left; margin-bottom:0px;">
                                                                        <a href="{{ AmazoneBucket::url() }}{{ $rowData }}" target="_blank">
                                                                            <img src="{{ AmazoneBucket::url() }}{{ $rowData }}" style="height: 100px;">
                                                                        </a>
                                                                    </li>
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                        <div style="clear:both;"></div>
                                                    </td>
                                                </tr>
                                            @endif

                                            @if($items->YesNoDocketsField->docket_field_category_id==1)
                                                <tr>
                                                    <td> {{ $items->label }} &nbsp; @if(@$items->required==1)*@endif</td>
                                                    <td>{{$items->value }}</td>
                                                </tr>
                                            @endif
                                            @if($items->YesNoDocketsField->docket_field_category_id==2)
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
                    @elseif($row->docketFieldInfo->docket_field_category_id==22)
                        <tr>
                            <td colspan="2">{{ $row->label }}
                                @php $fontSize = ($row->sentDocketFieldGridLabels->count()>12)?16:18 @endphp
                                <table style="background: transparent; width: 100%; " class="table table-striped">
                                    <thead>

                                    @if($row->sentDocketFieldGridLabels)
                                        <tr  style="font-size:{{$fontSize}}px">
                                            @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)
                                                @if((!$gridFieldLabels->docketFieldGrid->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                                    <th style="min-width: 200px; text-align:left;">
                                                        {{ @$gridFieldLabels->label}}
                                                    </th>
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif

                                    </thead>
                                    <tbody>
                                    @php
                                        $gridMaxRow     =    $row->sentDocketFieldGridValues->max('index');
                                    @endphp
                                    @for($i = 0; $i<=$gridMaxRow; $i++)
                                        <tr style="font-size:{{$fontSize}}px">
                                            @foreach($row->sentDocketFieldGridLabels as $gridFieldLabels)
                                                @if((!$gridFieldLabels->docketFieldGrid->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                                    @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || $gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
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
                                                                        @php $sn = 1; @endphp
                                                                        @foreach($values as $value)
                                                                            <li style="margin-right:10px; margin-bottom: 8px;">
                                                                                <a href="{{ AmazoneBucket::url() }}{{ $value }}" target="_blank">
                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $value }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                </a>
                                                                            </li>
                                                                            @if($sn==2)
                                                                                <div style="clear:both"></div>
                                                                                @php $sn=0 @endphp
                                                                            @endif
                                                                            @php $sn++ @endphp
                                                                        @endforeach
                                                                    @endif
                                                                    <div style="clear:both"></div>
                                                                </ul>
                                                            </td>
                                                        @endif
                                                    @elseif($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 9)
                                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                            <td>N/a</td>
                                                        @else
                                                            @php
                                                                $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                            @endphp
                                                            <td>
                                                                @if(!empty($values))
                                                                    <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                        @php $sn = 1; @endphp
                                                                        @foreach($values as $value)
                                                                            <li style="margin-right:10px; margin-bottom: 8px;">
                                                                                <a href="{{ AmazoneBucket::url() }}{{ $value['image'] }}" target="_blank">
                                                                                    <img src="{{ AmazoneBucket::url() }}{{ $value['image'] }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                                                                </a>
                                                                                @if($value['name'])<p style="font-weight: 500;color: #868d90;">{{$value['name']}}</p>@endif
                                                                            </li>
                                                                            @if($sn==2)
                                                                                <div style="clear:both"></div>
                                                                                @php $sn=0 @endphp
                                                                            @endif
                                                                            @php $sn++ @endphp
                                                                        @endforeach
                                                                        <div style="clear:both"></div>

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
                                                                    $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                                                @endphp
                                                                @if($value==1)
                                                                    <img src="{{ asset('assets/dashboard/img/checked.png') }}" width="20px">
                                                                @else
                                                                    <img src="{{ asset('assets/dashboard/img/cancel.png') }}" width="20px">
                                                                @endif
                                                            </td>
                                                        @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==29)
                                                            @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == null)
                                                                <td></td>
                                                            @else
                                                                <td  style="line-height: 2em;">
                                                                    <ul style=" list-style-type: none;">
                                                                        @foreach(unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value) as $data)
                                                                            <li>  {!! $data['email'] !!}</li>
                                                                        @endforeach
                                                                    </ul>
                                                                </td>
                                                            @endif


                                                        @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                                            <?php
                                                            $manualTimerGrid =  @\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                                            ?>

                                                            @if($manualTimerGrid != "")

                                                                <?php
                                                                $totalDuration = json_decode($manualTimerGrid , true)['totalDuration'];
                                                                $breakDuration =json_decode($manualTimerGrid , true)['breakDuration'];
                                                                ?>
                                                                <td>
                                                                    <strong>From :</strong>  {{   json_decode($manualTimerGrid , true)['from'] }}<br>
                                                                    <strong>To :</strong>  {{ json_decode($manualTimerGrid , true)['to'] }}<br/>
                                                                    <strong>Total Break :</strong>  {{ convertHrsMin($breakDuration) }}<br>
                                                                    <strong>Reason for break :</strong>  {{ json_decode($manualTimerGrid , true)['explanation'] }}<br>
                                                                    <strong>Total time :</strong>  {{ convertHrsMin($totalDuration) }}<br>
                                                                </td>
                                                            @else
                                                                <td>N/a</td>

                                                            @endif

                                                        @else

                                                            @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value)
                                                                <td > {{@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value}} </td>
                                                            @else
                                                                <td > N/a</td>
                                                            @endif

                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endfor

                                    @if($row->sentDocketFieldGridLabels->where('sumable',1)->count())
                                        <tr>
                                            @foreach($row->sentDocketFieldGridLabels as $gridFieldLsa)
                                                @if((!$gridFieldLsa->docketFieldGrid->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                                                    @if($gridFieldLsa->sumable == 1)
                                                        @if($gridFieldLsa->docketFieldGrid->docket_field_category_id == 3)
                                                            <?php
                                                            $arryForSum =  @\App\DocketFieldGridValue::where('docket_field_id',$row->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLsa->docket_field_grid_id)->where('is_email_docket', 0)->pluck('value')->toArray();
                                                            ?>
                                                            <th style="text-align: left;">
                                                                Total:  {{array_sum($arryForSum)}}
                                                            </th>
                                                        @else
                                                            <th></th>
                                                        @endif
                                                    @else
                                                        <th></th>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>

                        </tr>

                    @elseif($row->docketFieldInfo->docket_field_category_id == 29 )
                        <tr>
                            <td> {{ $row->label }}</td>

                            <td style="line-height: 1.5em;">
                                <ul style=" list-style-type: none;">
                                    @foreach(unserialize($row->value) as $email)
                                        <li> {!! $email['email'] !!}</li>
                                    @endforeach
                                </ul>
                            </td>


                        </tr>


                    @elseif($row->docketFieldInfo->docket_field_category_id!=13 && $row->docketFieldInfo->docket_field_category_id!=18 && $row->docketFieldInfo->docket_field_category_id!=30)
                        <tr>
                            <td> {{ $row->label }}</td>

                            @if($row->value != null)
                                <td > {!! @$row->value !!} </td>
                            @else
                                <td > N/a</td>
                            @endif

                        </tr>
                    @endif
                @endif
            @endforeach

            @foreach($docketFields as $row)
                @if((!$row->docketFieldInfo->is_hidden && $row->sentDocket->sender_company_id!=Session::get('company_id')) || $row->sentDocket->sender_company_id==Session::get('company_id'))
                    @if($row->docketFieldInfo->docket_field_category_id==13)

                        <tr style="padding-top: 0px;">
                            <td  colspan="2">
                                {{ $row->label }}
                                @if($row->value != null)
                                    <p style="color:#7b7b7bd4;margin-top: 0;">{{ $row->value }}</p>
                            @else
                                <td > N/a</td>
                                @endif
                                </td>
                        </tr>
                    @endif
                @endif
            @endforeach<!--foreach end for cat 13-->


        @endif <!--docketFields endif-->
        </tbody>
    </table>

    @if($sentDocket->attachedTimer()->count()>0)
        <div class="attachedTimer">
            <div class="row">
                <div class="col-md-12">
                    <p><b><i class="fa fa-paperclip" aria-hidden="true"></i> Timer Attachments</b></p>
                </div><br>
                @php $totalInterval = 0; @endphp
                @if($sentDocket->attachedTimer()->count())
                    @php    $sn = 1; @endphp
                    @foreach($sentDocket->attachedTimer() as $row)
                        <div style="width: 20%;display: inline-block;" class="col-md-2">
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
                        @if($sn==5)<br/> @php $sn=0; @endphp @endif
                        @php $sn++; @endphp
                    @endforeach
                @endif
            </div>
        </div>
    @endif


    @if($sentDocket->docketApprovalType == 1 || $sentDocket->docketApprovalType == 0 )
        @if($sentDocket->status != 3)
            @if($sentDocket->docketApprovalType==0)
                <hr class='dotted' />
                <div class="col-md-12">
                    <div style="width:50%;float:left;" class="col-md-6">
                        <h5 style="font-weight: 800;">Approved By:</h5>
                        @foreach($sentDocket->sentDocketRecipientApproval as $row)
                            @if($row->status==1)
                                <p style="padding-top: 8px;">{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                            @endif
                            <div class="clearfix"></div>
                            <br>
                        @endforeach
                    </div>
                    <div style="width:50%;float:right;" class="col-md-6">
                        @if($sentDocket->status==0)
                            <h5 style="font-weight: 800;">Pending Approval:</h5>
                            @foreach($sentDocket->sentDocketRecipientApproval as $row)
                                @if($row->status==0)
                                    <p >{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}</p>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @else
                <hr class='dotted' />
                <div class="col-md-12">
                    <div style="width:50%;float:left;" class="col-md-6">
                        <h5 style="font-weight: 800;">Approved By:</h5>
                        @foreach($sentDocket->sentDocketRecipientApproval as $row)
                            @if($row->status==1)
                                <img style="width: 84px;float: left;margin-right: 16px;" src="{{ AmazoneBucket::url() }}{{ $row->signature }}">
                                <p style="padding-top: 8px;    padding-left: 104px;">{{ $row->name }}  on {{\Carbon\Carbon::parse($row->approval_time)->format('d-M-Y h:i a T')}}</p>
                            @endif
                            <div class="clearfix"></div>
                            <br>
                        @endforeach
                    </div>
                    <div style="width:50%;float:right;" class="col-md-6">
                        @if($sentDocket->status==0)
                            <h5 style="font-weight: 800;">Pending Approval:</h5>
                            @foreach($sentDocket->sentDocketRecipientApproval as $row)
                                @if($row->status==0)
                                    <p>{{ $row->userInfo->first_name." ".$row->userInfo->last_name }}</p>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif<!--/ Approval condtion check endif-->
        @endif

    @endif
    @if(count($sentDocket->sentDocketRejectExplanation) != 0)
        <br/>
        <strong>Rejected By:</strong>
        @foreach($sentDocket->sentDocketRejectExplanation as $sentDocketRejection)
            <p><b>{{$sentDocketRejection->userInfo->first_name}} </b>: {{$sentDocketRejection->explanation}}  on {{\Carbon\Carbon::parse($sentDocketRejection->created_at)->format('d-M-Y h:i a T')}}</p>
            @if($sentDocketRejection!=$sentDocket->sentDocketRejectExplanation->last())<br/>@endif
        @endforeach
    @endif
</div>
