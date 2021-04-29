<tr>
    <td colspan="2">{{ $docketValue->label }}
        <div style="width: 1094px;overflow: auto;">
            <table  class="table table-striped" width="100%">
                <thead>
                <tr>
                    @foreach($docketValue->sentDocketFieldGridLabels as $gridFieldLabels)
                        <th class="printTh" style="min-width: 200px">
                            <div class="printColorDark" >{{ $gridFieldLabels->label}}</div>
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @php
                    $gridMaxRow     =    $docketValue->sentDocketFieldGridValues->max('index');
                @endphp
                @for($i = 0; $i<=$gridMaxRow; $i++)

                    <tr>
                        @foreach($docketValue->sentDocketFieldGridLabels as $gridFieldLabels)
                            @if((!$gridFieldLabels->docketFieldGrid->is_hidden && $sentDocket->sender_company_id != @$recipient->company()->id) || $sentDocket->sender_company_id==@$recipient->company()->id)
                                @if(@$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 5 || @$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 14)
                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                        <td>N/a</td>
                                    @else
                                        @php
                                            $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
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
                                    @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == 'N/a')
                                        <td>N/a</td>
                                    @else
                                        @php
                                            $values = unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_id',$sentDocket->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value);
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
                                                $value = @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
                                            @endphp
                                            @if($value==1)
                                                <i class="fa fa-check-circle" style="color:green"></i>
                                            @else
                                                <i class="fa fa-close" style="color:#ff0000 !important"></i>
                                            @endif
                                        </td>
                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id  == 29)
                                        @if(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value == null)
                                            <td ></td>
                                        @else
                                            <td  style="line-height: 2em;white-space: pre-wrap;">
                                                @foreach(unserialize(@\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value) as $data)
                                                    {!! $data['email'] !!}
                                                @endforeach
                                            </td>
                                        @endif



                                    @elseif(@$gridFieldLabels->docketFieldGrid->docket_field_category_id ==20)
                                        <?php
                                        $manualTimerGrid =  @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value;
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

                                    @else
                                        <td>{{ @\App\DocketFieldGridValue::where('index',$i)->where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLabels->docket_field_grid_id)->where('is_email_docket', 0)->first()->value }}</td>
                                    @endif
                                @endif
                            @endif
                        @endforeach

                    </tr>
                @endfor
                </tbody>
                <tfooter>
                    <tr>
                        @foreach($docketValue->sentDocketFieldGridLabels as $gridFieldLsa)
                            @if($gridFieldLsa->sumable == 1)
                                @if($gridFieldLsa->docketFieldGrid->docket_field_category_id == 3)
                                    <?php
                                    $arryForSum = @\App\DocketFieldGridValue::where('docket_id',$sentDocket->id)->where('docket_field_id',$docketValue->docketFieldInfo->id)->where('docket_field_grid_id',$gridFieldLsa->docket_field_grid_id)->where('is_email_docket', 0)->pluck('value')->toArray();
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
