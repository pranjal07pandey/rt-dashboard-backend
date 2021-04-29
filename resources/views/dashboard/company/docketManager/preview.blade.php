<meta name="format-detection" content="telephone=no">
<style>
    * {
        -webkit-touch-callout: none;
        -webkit-user-select: none; /* Disable selection/copy in UIWebView */
    }

    body { margin: 0; padding: 0; font-family:Helvetica Neue, Helvetica, Arial, sans-serif; }
    .divWrapper{
        padding:10px 10px;
        background: #ffffff;
        /*border: 1px dashed #eaeaea;*/
        font-size: 12px
    }
    .pageBreak{
        height: 20px;
        width: 100%;
        background: #5B6366;
        display: block;
        padding: 0px -50px;
    }
    table{
        font-size:12px;
    }
    th, td{
        padding:5px 0px 5px 10px;
        text-align: left;
        line-height: 1.5em;
    }
    td span{
        float:right;
        width: calc( 40% - 10px);
        text-align: left;
        display:block;
        padding-left: 10px;
    }

    .docketDetailsTable tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;

    }
    .docketDetailsTable tbody tr td{
        padding: 8px;
        vertical-align: top;
        border-top: 1px solid #ddd;
        font-size: 12px;
    }


    div{
        line-height: 1.5em;
    }
    strong{
        font-size: 12px;
    }
    .docketId{
        padding-left: 10px;
        padding-top: 10px;
        display: block;
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
        background: #f5f6f5;
        margin-bottom: 16px;
    }
</style>
{{--@if($sentDocket->isDocketAttached==1)--}}
{{--@if($invoice->attachedDocketsInfo)--}}
{{--@foreach($sentDocket as $row)--}}
<div class="docket">
    {{--<strong class="docketId">#Doc{{ $sentDocket->id }}</strong>--}}
    <div class="divWrapper">
        <div style="width:65%;float:left;margin-bottom: 10px;">
            @if(AmazoneBucket::fileExist(@$sentDocket->company_logo))
                <img src="{{ AmazoneBucket::url() }}{{ @$sentDocket->company_logo }}" style="height:70px;">
            @else
                <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=your+logo">
            @endif<br/><br/>

            <strong>From</strong> : <span style="text-decoration: dotted">
                            {{--{{ $sentDocket->senderUserInfo->first_name }} {{ $sentDocket->senderUserInfo->last_name }}--}}
                {{ @$sentDocket->sender_name }}<br>
                {{ @$sentDocket->company_name }}<br>
                {{ @$sentDocket->company_address }}<br>
                        <b>ABN:</b> {{ @$sentDocket->abn }}
                        </span>
        </div>
        <!--<div style="width:35%;float:left;font-size: 12px;text-align: right;">-->
    <!--    <b>{{ @$sentDocket->docketInfo->title }}</b><br/>-->
    <!--    <strong>Date</strong>: {{ \Carbon\Carbon::parse($sentDocket->created_at)->format('d-M-Y') }}<br/>-->
    <!--    <b>Docket ID:</b> {{ $sentDocket->id }}<br>-->
        <!--</div>-->
        <div style="clear:both"></div>

        <div><br/>
            <strong>To :</strong><br>

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
                <b>Company Name:</b><br>

                @foreach($receiverDetail as $key=>$value)
                    <?php $sn++; ?>
                    @if($sn<=count($receiverDetail) && $sn!=1)
                        ,
                    @endif
                    {{$key}}
                @endforeach
            @endif



            <br/>
            {{--<span class="dotted">{{ $row->docketInfo->companyInfo->name }}</span><br/>--}}
            {{--<span class="dotted">{{ $row->docketInfo->companyInfo->address }}</span>--}}
            <br/><br/>
        </div>

        <div>
            <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable">
                <thead>
                <tr style="background:#ddd;text-align:left;width:100%;">
                    <th width="45%">Description</th>
                    <th width="55%">Value/Amount</th>
                </tr>
                </thead>
                <tbody style="background-color: #fff;">
                @if($sentDocket->sentDocketValue)
                    @foreach($sentDocket->sentDocketValue as $item)
                        @if((!$item->is_hidden && $item->sentDocket->sender_company_id!=$request->header('companyId')) || $item->sentDocket->sender_company_id==$request->header('companyId'))
                            @if($item->docketFieldInfo->docket_field_category_id==5)
                                <tr>
                                    <td colspan="2">
                                        {{ $item->label }}<br/>
                                        @if($item->sentDocketImageValue->count()>0)
                                            <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                @foreach($item->sentDocketImageValue as $image)
                                                    <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 5px;">
                                                        <a href="{{ AmazoneBucket::url() }}{{ $image->value }}" target="_blank">
                                                            <img src="{{ AmazoneBucket::url() }}{{ $image->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            No Image Attached
                                        @endif
                                    </td>
                                </tr>
                            @elseif( $item->docketFieldInfo->docket_field_category_id==9 )
                                <tr>
                                    <td colspan="2">
                                        {{ $item->label }}<br/>
                                        @if($item->sentDocketImageValue->count()>0)
                                            <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                @foreach($item->sentDocketImageValue as $signature)
                                                    <li style="background:#fff;margin-right:10px;float: left;margin-bottom:20px;width: 90px; height: 100px;border:1px solid #eee;padding: 10px;">
                                                        <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                                                            <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style="height:90px; width:90px;margin:0px auto; display: block">
                                                        </a>
                                                        <p style="font-weight: 500;color: #868d90;margin-top:20px">{{$signature->name}}</p>

                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            No Signature Attached
                                        @endif
                                    </td>
                                </tr>
                            @elseif( $item->docketFieldInfo->docket_field_category_id==14)
                                <tr>
                                    <td colspan="2">
                                        {{ $item->label }}<br/>
                                        @if($item->sentDocketImageValue->count()>0)
                                            <ul style="list-style: none;margin: 10px 0px 0px;padding: 0px;">
                                                @foreach($item->sentDocketImageValue as $sketchPad)
                                                    <li style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: 90px; height: 90px;border:1px solid #eee;padding: 10px;">
                                                        <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank">
                                                            <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="max-height:100%; max-width:100%;margin:0px auto; display: block">
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            No Sketch Attached
                                        @endif
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
                                        $ <strong> {{ round(floatval($item->sentDocketUnitRateValue->first()->value)*floatval($item->sentDocketUnitRateValue->last()->value), 2) }}</strong>
                                    </td>
                                </tr>

                            @elseif($item->docketFieldInfo->docket_field_category_id==24)
                                <tr>
                                    <td colspan="2"><strong>{{ $item->label }}</strong> </td>
                                </tr>
                                <?php $sn = 1; $total = 0; ?>
                                @foreach($item->sentDocketTallyableUnitRateValue as $row)
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

                            @elseif($item->docketFieldInfo->docket_field_category_id==20)
                                <tr>
                                    <td>{{ $item->label }}</td>
                                    <td>
                                        @foreach($item->sentDocketManualTimer as $rows)
                                            <strong>{{ $rows->label }} :</strong>  {{ $rows->value }}   <br>
                                        @endforeach

                                        @foreach($item->sentDocketManualTimerBreak as $items)
                                            <strong>{{ $items->label }} :</strong>  {{ $items->value }}<br>
                                            <strong>Reason for break :</strong>  {{ $items->reason }}<br>
                                        @endforeach
                                        <strong>Total time :</strong>  {{ $item->value }}<br>

                                    </td>
                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id==15)
                                <tr>
                                    <td> {{ $item->label }}
                                        @if($item->sentDocketAttachment->count()>0)
                                            <ul class="pdf">
                                                @foreach($item->sentDocketAttachment as $document)
                                                    <li><img src="{{ asset('assets/pdf.png') }}"><a href="{{ AmazoneBucket::url() }}{{$document->url}}" target="_blank">{{$document->document_name}}</a></li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <br/><strong>No Document Attached</strong>
                                        @endif
                                    </td>
                                    <td></td>
                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id==12)
                                <tr>
                                    <td  colspan="2"> <strong>{{ $item->label }}</strong></td>
                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id== 27)
                                <tr>
                                    <td colspan="2">{!! $item->label !!}</td>
                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id==18)

                                <tr>
                                    <td colspan="2">

                                        @php
                                            $yesno = unserialize($item->label);

                                        @endphp
                                        <div style="width:100%;margin:0;">
                                            <div style="width:50%;float:left;">{{ @$yesno['title']}}</div>
                                            @if($item->value == "N/a")
                                                <div style="width:50%; float:right;margin-right: -9px;"> N/a </div>
                                            @else
                                                @if(@$yesno['label_value'][$item->value]['label_type']==1)
                                                    <div style="width:50%; float:right;margin-right: -9px;"><img style="width: 20px; height:20px; background-color:{{ @$yesno['label_value'][$item->value]['colour']}}; border-radius:20px;padding:4px;" src="{{ AmazoneBucket::url() }}{{ @$yesno['label_value'][$item->value]['label'] }}"></div>
                                                @else
                                                    <div style="width:50%; float:right;margin-right: -9px;">{{ @$yesno['label_value'][$item->value]['label']}}</div>
                                                @endif
                                            @endif
                                        </div>
                                        @if(count($item->SentDocValYesNoValueInfo)==0)
                                        @else
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
                                                            <td> {{ $items->label }} &nbsp;@if(@$items->required==1)*@endif</td>
                                                            <td>{{$items->value }}</td>
                                                        </tr>
                                                    @endif
                                                    @if($items->YesNoDocketsField->docket_field_category_id==2)
                                                        <tr>
                                                            <td> {{ $items->label }} &nbsp;@if(@$items->required==1)*@endif</td>
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


                            @elseif($item->docketFieldInfo->docket_field_category_id == 22)
                                <tr>

                                    <td colspan="2" id="grid">{{ $item->label }}
                                        <div style=" overflow: scroll;" id="gridContainer">
                                            <table  class="table table-striped" style="width: 100%;">
                                                <thead>
                                                <tr>
                                                    @foreach($item->sentDocketFieldGridLabels as $gridFieldLabels)
                                                        <th class="printTh" style="min-width: 200px">
                                                            <div class="printColorDark">{{ $gridFieldLabels->label}}</div>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @php
                                                    $gridMaxRow     =    $item->sentDocketFieldGridValues->max('index');
                                                @endphp
                                                @for($i = 0; $i<=$gridMaxRow; $i++)
                                                    <tr>
                                                        @foreach($item->sentDocketFieldGridLabels as $gridFieldLabels)
                                                          @if((!$gridFieldLabels->docketFieldGrid && $item->sentDocket->sender_company_id!=$request->header('companyId')) || $item->sentDocket->sender_company_id==$request->header('companyId'))
                                                            @if($gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || $gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                                                @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                    <td>N/a</td>
                                                                @else
                                                                    @php
                                                                        $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
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
                                                                @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                                                    <td>N/a</td>
                                                                @else
                                                                    @php
                                                                        $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
                                                                    @endphp
                                                                    <td>
                                                                        @if(!empty($values))
                                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                @foreach($values as $value)
                                                                                    <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                        <a href="{{ AmazoneBucket::url() }}{{ $value['image'] }}" target="_blank">
                                                                                            <img src="{{ AmazoneBucket::url() }}{{ $value['image'] }}" style="width: 60px;height: 60px;border: 1px solid #ddd;">
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
                                                                            $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                                                        @endphp
                                                                        @if($value==1)
                                                                            <img src="{{ asset('assets/dashboard/img/icons/checkmark.png') }}" width="10px">
                                                                        @else
                                                                            <img src="{{ asset('assets/dashboard/img/icons/cross.png') }}" width="10px">
                                                                        @endif
                                                                    </td>
                                                                @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==29)
                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == null)
                                                                        <td>N/a</td>
                                                                    @else
                                                                        <td  style="line-height: 2em;">
                                                                            <ul style=" list-style-type: none;">
                                                                                @foreach(unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value) as $data)
                                                                                    <li>{!! $data['email'] !!}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </td>
                                                                    @endif

                                                                @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                                                    <?php
                                                                    $manualTimerGrid =  @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
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
                                                                            <strong>Total Break :</strong> {{ \App\Http\Controllers\APIController::convertHrsMin($breakDuration) }}  <br>
                                                                            <strong>Reason for break :</strong>  {{ json_decode($manualTimerGrid , true)['explanation'] }}<br>
                                                                            <strong>Total time :</strong>{{ \App\Http\Controllers\APIController::convertHrsMin($totalDuration) }}  <br>
                                                                        </td>
                                                                    @else
                                                                        <td>N/a</td>

                                                                    @endif

                                                                @else
                                                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == null)
                                                                        <td>N/a</td>
                                                                    @else
                                                                        <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value }}</td>

                                                                    @endif
                                                                @endif
                                                            @endif
                                                          @endif
                                                        @endforeach
                                                    </tr>
                                                @endfor
                                                </tbody>

                                                <tfooter>
                                                    <tr>
                                                        @foreach($item->sentDocketFieldGridLabels as $gridFieldLsa)
                                                            @if($gridFieldLsa->sumable == 1)
                                                                @if($gridFieldLsa->docketFieldGrid->docket_field_category_id == 3)
                                                                    <?php
                                                                    $arryForSum = @\App\DocketFieldGridValue::where('docket_id',$sentDocket->id)->where('docket_field_id',$item->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLsa->docket_field_grid_id)->where('is_email_docket', 0)->pluck('value')->toArray();
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

                                        <script type="text/javascript">
                                            var width =  (window.innerWidth-40).toString() + "px";
                                            document.getElementById("gridContainer").style.width = width;
                                        </script>
                                    </td>

                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id == 29 )
                                <tr>
                                    <td> {{ $item->label }}</td>
                                    <td style="line-height: 1.5em;">
                                        <ul style=" list-style-type: none;">
                                            @foreach(unserialize($item->value) as $email)
                                                <li> {!! $email['email'] !!}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @elseif($item->docketFieldInfo->docket_field_category_id!=13 && $item->docketFieldInfo->docket_field_category_id!=18  && $item->docketFieldInfo->docket_field_category_id!=30)
                                <tr>
                                    <td> {{ $item->label }}</td>
                                    <td style="line-height: 1.5em;white-space: pre-wrap;"> {!! $item->value !!} </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach

                    @foreach($sentDocket->sentDocketValue as $item)
                        @if((!$item->docketFieldInfo->is_hidden && $item->sentDocket->sender_company_id!=$request->header('companyId')) || $item->sentDocket->sender_company_id==$request->header('companyId'))
                            @if($item->docketFieldInfo->docket_field_category_id==13)
                                <tr>
                                    <td  colspan="2"> <strong>{{ $item->label }}</strong><br>
                                        <p style="padding-top:0px;margin-top:0px;word-break: break-word;">{{ $item->value }}</p>
                                    </td>
                                </tr>
                            @endif
                        @endif
                    @endforeach
                @endif
                </tbody>

            </table>
            <br>
            @if($docketTimer->count()>0)
                <table width="100%" cellpadding="0" cellspacing="0" class="docketDetailsTable">
                    <thead>
                    <tr style="background:#ddd;text-align:left;width:100%;">
                        <th width="45%"> Timer Attachements</th>
                    </tr>
                    </thead>
                    <tbody style="background-color: #fff;">
                    <tr>
                        <td colspan="2">

                            <ul style="list-style: none;margin: 0px 0px 0px;padding: 0px;" class="timerList">
                                @php
                                    $totalInterval = 0;
                                @endphp
                                @if($docketTimer->count())
                                    @foreach($docketTimer as $row)
                                        <li class="box-timer" style="background:#fff;margin-right:10px;float: left;margin-bottom:10px;width: calc(33% - 28px); height: 130px;border:1px solid #eee;padding: 10px;">
                                            <a href="{{ url('api/timerDetails/webview/'.$row->timer_id.".timer") }}" style="text-decoration: none;color:inherit;">
                                                <img src="{{ asset('assets/clock.png') }}" style="max-height:14px; max-width:21px;margin:0px  auto; display: block"><br>
                                                <p style="margin-top:-10px"><strong>{{$row->timerInfo->total_time}}</strong></p>
                                                <p><img src="{{ asset('assets/marker.png') }}" style="max-height:10px; max-width:16px;margin:0px ;"> {!!  str_limit(strip_tags($row->timerInfo->location),20) !!}</p>
                                                <p> {{ \Carbon\Carbon::parse($row->timerInfo->time_started)->format('d-M-Y') }}</p>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                                <div style="clear:both;"></div>
                            </ul>
                            <style>
                                .timerList li:nth-child(3n){
                                    margin-right: 0px !important;
                                }
                            </style>
                        </td>
                    </tr>
                    </tbody>

                </table>
            @endif


        </div>
    </div>
</div><!--/.divWrapper-->
</div>
{{--@endforeach--}}
{{--@endif--}}
{{--@endif--}}
