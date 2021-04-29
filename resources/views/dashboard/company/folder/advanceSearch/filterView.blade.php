

@if($type == 1)
    @if(@$sentDocket)
        @foreach($sentDocket as $row)
                <tr>
                    <td>
                        <label>
                            <input type="checkbox" class="checkbox selectitem forDocket" value="{{ $row->id }}"  name="d[]"  >
                            <span class="checkbox-material"><span class="check"></span>
                                            </span>
                        </label>
                    </td>
                    <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
                    <td>
                        <span class="blackLabel">Sender</span>
                        <span class="userInfo"> {{ @$row->sender_name }}<br/></span>
                        {{ @$row->company_name }}<br/><br>
                        <span class="blackLabel">Receiver</span>
                        <span class="userInfo">@if(@$row->recipientInfo)
                                <?php $sn = 1; ?>
                                @foreach($row->recipientInfo as $userInfo)
                                    {{ @$userInfo->userInfo->first_name }} {{ @$userInfo->userInfo->last_name }}
                                    @if($sn!=$row->recipientInfo->count())
                                        ,
                                    @endif
                                    <?php $sn++; ?>
                                @endforeach
                            @endif<br>
                                        </span>
                        <?php
                        $recipientIds   =   $row->recipientInfo->pluck('user_id');
                        $companyEmployeeQuery   =    \App\Employee::whereIn('user_id',$recipientIds)->pluck('company_id');
                        $empCompany    =    \App\Company::whereIn('id',$companyEmployeeQuery)->pluck('id')->toArray();
                        $adminCompanyQuery   =    \App\Company::whereIn('user_id',$recipientIds)->pluck('id')->toArray();
                        $company    =   \App\Company::whereIn('id',array_unique(array_merge($empCompany,$adminCompanyQuery)))->get();
                        ?>
                        <?php $sns = 1; ?>
                        @foreach($company as $companys)
                            {{$companys->name}}
                            @if($sns!=$company->count())
                                ,
                            @endif
                            <?php $sns++; ?>
                        @endforeach


                        @if(count($row->sentDocketLabels)>0)
                            <div style="height: 30px;">
                                <div style="position: absolute;" class="docketLabelIdentify{{$row->id}}">
                                    @foreach($row->sentDocketLabels as $sentDocLabel)
                                        {{--{{ $sentDocLabel->docketLabel->company_id }}--}}
                                        @if(@$sentDocLabel->docketLabel->company_id==Session::get('company_id'))
                                            <span style=" background: {{$sentDocLabel->docketLabel->color}};display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;" class="badge badge-pill badge-primary docketDelete{{$sentDocLabel->id}}">
                                                <img style="margin-right: 2px" src="{{ AmazoneBucket::url() }}{{ $sentDocLabel->docketLabel->icon }}" height="10" width="10">  {{ $sentDocLabel->docketLabel->title }}
                                                <button  data-toggle="modal" data-target="#deleteLabel" data-type="1"  data-id="{{$sentDocLabel->id}}"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;">
                                                    <span  class="glyphicon glyphicon-remove" aria-hidden="true"/>
                                                </button>
                                            </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                        @else
                            <div style="height: 30px;">
                                <div style="position: absolute;" class="docketLabelIdentify{{$row->id}}">
                                </div>
                            </div>

                        @endif
                    </td>
                    <td>{{ $row->docketInfo->title }}
                        @if(@$row->docketInfo->previewFields->count()>0)
                            <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                                @foreach(@$row->docketInfo->previewFields as $previewField)
                                    @if($previewField->docket_filed_info->docket_field_category_id==5 || $previewField->docket_filed_info->docket_field_category_id == 9)
                                        @if(@count(\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                            <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                            <ul class="sentDocketImagePreview">
                                                @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                    <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @elseif($previewField->docket_filed_info->docket_field_category_id == 7)
                                        <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                        @if(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue)
                                            <?php $sn = 1; $total = 0; ?>
                                            @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue as $unitRate)
                                                {{$unitRate->docketUnitRateInfo->label}} : @if($unitRate->docketUnitRateInfo->type==1) $ @endif {{ $unitRate->value }} &nbsp;&nbsp;&nbsp;
                                                @if($sn == 1)
                                                    <?php $total = $unitRate->value; ?>
                                                @else
                                                    <?php $total    =   $total*$unitRate->value; ?>
                                                @endif
                                                <?php $sn++; ?>
                                            @endforeach
                                            <strong>Total:</strong>
                                            <strong>$ {{ $total }}</strong>
                                        @endif
                                    @elseif($previewField->docket_filed_info->docket_field_category_id == 8)
                                        <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                        @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==0)
                                            <span>No</span>
                                        @elseif(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==1)
                                            <span>Yes</span>
                                        @endif
                                    @else
                                        <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                        <span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>

                                    @endif
                                    <br/>
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
                    </td>
                    {{--<td><span class="invoiceAmount">$134.99</span></td>--}}
                    <td>
                        @if($row->status==1)
                            <span class="label label-success">Approved</span>
                        @elseif($row->sender_company_id ==Session::get('company_id'))
                            <span class="label label-primary">Sent</span>
                            <span class="label label-success">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                        @else
                            <span class="label label-warning">Received</span>
                            <span class="label label-success">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ url('dashboard/company/docketBookManager/docket/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
                        <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$row->id)}}" class="btn btn-success btn-xs btn-raised"  ><i class="fa fa-download"></i></a>
                        @if(count($sentDocketLabel)==0)

                            <a  data-toggle="modal" data-target="#noFolderLabeling" data-id="{{$row->id}}" data-type="1" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

                        @else
                            <a  data-toggle="modal" data-target="#folderLabeling" data-id="{{$row->id}}" data-type="1" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

                        @endif

                    </td>
                </tr>
        @endforeach

    @endif
    @if(count(@$sentDocket)==0)
        <tr>
            <td style="border-bottom:none;    height: 200px;" colspan="7">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif
@elseif($type==2)

    <?php $sn=1; ?>
    @if(@$sentEmailDockets)
        @foreach($sentEmailDockets as $row)
            <tr>
                <td>
                    <label>
                        <input type="checkbox" class="checkbox selectitem forEmailDocket" value="{{ $row->id }}"  name="ed[]"  >
                        <span class="checkbox-material"><span class="check"></span>
                                </span>
                    </label>
                </td>
                <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
                <td>
                    <span class="blackLabel">Sender</span>
                    <span class="userInfo"> {{ $row->senderUserInfo->first_name }} {{ $row->senderUserInfo->last_name }}<br/></span>
                    {{ @$row->company_name }}<br/><br>
                    <span class="blackLabel">Receiver</span>
                    <span class="userInfo">
                                    @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp
                        @foreach($row->recipientInfo as $recipient)
                            @php
                                $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;
                                $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;

                                if($row->recipientInfo->count()>1){
                                   if($row->recipientInfo->last()->id!=$recipient->id){
                                        $recipientNames =  $recipientNames.", ";
                                        $recipientCompany = $recipientCompany.", ";
                                    }
                                }
                            @endphp
                        @endforeach
                        {{ $recipientNames }}<br>
                        {{ $recipientCompany }}<br></span>

                    @if(count($row->sentEmailDocketLabels)>0)
                        <div style="height: 30px;">
                            <div style="position: absolute;" class="emailDocketLabelIdentify{{$row->id}}">
                                @foreach($row->sentEmailDocketLabels as $sentDocLabel)
                                    {{--{{ $sentDocLabel->docketLabel->company_id }}--}}
                                    @if($sentDocLabel->docketLabel->company_id==Session::get('company_id'))
                                        <span style=" background: {{$sentDocLabel->docketLabel->color}};display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;" class="badge badge-pill badge-primary emailDocketDelete{{$sentDocLabel->id}}">
                                                <img style="margin-right: 2px" src="{{ AmazoneBucket::url() }}{{ $sentDocLabel->docketLabel->icon }}" height="10" width="10">  {{ $sentDocLabel->docketLabel->title }}
                                            <button  data-toggle="modal" data-target="#deleteLabel" data-type="2" data-id="{{$sentDocLabel->id}}"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;">
                                                    <span  class="glyphicon glyphicon-remove" aria-hidden="true"  />
                                                </button>
                                            </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                    @else
                        <div style="height: 30px;">
                            <div style="position: absolute;" class="emailDocketLabelIdentify{{$row->id}}">
                            </div>
                        </div>

                    @endif

                </td>
                <td>{{ $row->docketInfo->title }}
                    @if(@$row->docketInfo->previewFields->count()>0)
                        <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                            @foreach(@$row->docketInfo->previewFields as $previewField)
                                @if($previewField->docket_filed_info->docket_field_category_id==5 || $previewField->docket_filed_info->docket_field_category_id == 9)
                                    @if(@count(\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                        <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                        <ul class="sentDocketImagePreview">
                                            @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                                            @endforeach
                                        </ul>
                                    @endif
                                @elseif($previewField->docket_filed_info->docket_field_category_id == 7)
                                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                    @if(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue)
                                        <?php $sn = 1; $total = 0; ?>
                                        @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketUnitRateValue as $unitRate)
                                            {{$unitRate->docketUnitRateInfo->label}} : @if($unitRate->docketUnitRateInfo->type==1) $ @endif {{ $unitRate->value }} &nbsp;&nbsp;&nbsp;
                                            @if($sn == 1)
                                                <?php $total = $unitRate->value; ?>
                                            @else
                                                <?php $total    =   $total*$unitRate->value; ?>
                                            @endif
                                            <?php $sn++; ?>
                                        @endforeach
                                        <strong>Total:</strong>
                                        <strong>$ {{ $total }}</strong>
                                    @endif
                                @elseif($previewField->docket_filed_info->docket_field_category_id == 8)

                                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                    @if(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==0)
                                        <span>No</span>
                                    @elseif(@$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value==1)
                                        <span>Yes</span>
                                    @endif
                                    {{--<span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>--}}
                                @else
                                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                    <span>{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->value }}</span>

                                @endif
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
                </td>

                <td>
                    @if($row->status==0)
                        <span class="label label-primary">Sent</span>
                    @endif
                    @if($row->status==1)
                        <span class="label label-success">Approved</span>
                    @endif

                </td>
                <td>

                    <a href="{{ url('dashboard/company/docketBookManager/docket/view/emailed/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
                    <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewemailed/'.$row->id)}}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-download"></i></a>
                    @if(count($sentDocketLabel)==0)
                        <a   data-toggle="modal" data-target="#noFolderLabeling" data-id="{{$row->id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                    @else
                        <a data-toggle="modal" data-target="#folderLabeling" data-id="{{$row->id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                    @endif
                </td>
            </tr>

        @endforeach
    @endif
    @if(count(@$sentEmailDockets)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif

@elseif($type == 3)
    @if(@$sentInvoice)
        @foreach($sentInvoice as $row)


      <tr>
        <td>  <label>
                <input type="checkbox" class="checkbox selectitem forInvoice" value="{{ $row->id }}"  name="invoiceId[]"  >
                <span class="checkbox-material"><span class="check"></span>
                                                     </span>
            </label>
        </td>
        <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
        <td>
            <span class="blackLabel">Sender</span>
            <span class="userInfo"> {{ $row->senderUserInfo->first_name }} {{ $row->senderUserInfo->last_name }}<br/></span>
            {{ @$row->senderCompanyInfo->name }}<br/><br>
            <span class="blackLabel">Receiver</span>
            <span class="userInfo">{{ $row->receiverUserInfo->first_name }} {{ $row->receiverUserInfo->last_name }}<br></span>
            {{ @$row->receiverCompanyInfo->name }}
            @if(count($row->sentInvoiceLabels)>0)
                <div style="height: 30px;">
                    <div style="position: absolute;" class="invoiceLabelIdentify{{$row->id}}">
                        @foreach($row->sentInvoiceLabels as $sentInvLabel)
                            @if(@$sentInvLabel->invoiceLabel->company_id==Session::get('company_id'))
                                <span style=" background: {{$sentInvLabel->invoiceLabel->color}};display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;" class="badge badge-pill badge-primary invoiceDelete{{$sentInvLabel->id}}">
                                                <img style="margin-right: 2px" src="{{ AmazoneBucket::url() }}{{ $sentInvLabel->invoiceLabel->icon }}" height="10" width="10">  {{ $sentInvLabel->invoiceLabel->title }}
                                    <button  data-toggle="modal" data-target="#deleteLabel" data-type="3" data-id="{{$sentInvLabel->id}}"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;">
                                                    <span  class="glyphicon glyphicon-remove" aria-hidden="true"  />
                                                </button>
                                            </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @else
                <div style="height: 30px;">
                    <div style="position: absolute;" class="invoiceLabelIdentify{{$row->id}}">
                    </div>
                </div>
            @endif
        </td>
        <td>{{ $row->invoiceInfo->title }}</td>
        <td>
            {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
        </td>
        {{--<td><span class="invoiceAmount">$134.99</span></td>--}}
        <td>
            @if($row->status==1)
                <span class="label label-success">Approved</span>
            @else
                <?php

                $employess= App\Employee::where('user_id',$row->user_id)->pluck('user_id')->toArray();
                $admin = App\Company::where('id',Session::get('company_id'))->pluck('user_id')->toArray();
                $totalCompanyuser = array_merge($employess,$admin);
                ?>
                @if(in_array($row->user_id,$totalCompanyuser)==false)
                    <span class="label label-warning">Received </span>
                @else
                    @if(\Illuminate\Support\Facades\Auth::user()->id==$row->user_id)
                        <span class="label label-primary">Sent</span>
                    @else
                        @if($row->receiver_user_id ==\Illuminate\Support\Facades\Auth::user()->id)
                            <span class="label label-warning">Received</span>
                        @else
                            <span class="label label-primary">Sent</span>
                        @endif

                    @endif

                @endif
            @endif

        </td>
        <td>
            <a href="{{ url('dashboard/company/invoiceManager/invoice/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
            <a href="{{url('dashboard/company/invoiceManager/invoice/downloadViewInvoice/'.$row->id)}}" class="btn btn-primary btn-xs btn-raised"><i class="fa fa-download"></i></a>
            @if($totalInvoiceLabel==0)
                <a data-toggle="modal" data-target="#noInvoiceFolderLabeling" data-id="{{$row->id}}" data-type="3" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
            @else
                <a   data-toggle="modal" data-target="#invoicefolderLabeling" data-id="{{$row->id}}" data-type="3"  class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
            @endif


            <?php
            $emplo = \App\Employee::where('company_id',\Illuminate\Support\Facades\Session::get('company_id'))->pluck('user_id')->toArray();
            $comp = \App\Company::where('id',\Illuminate\Support\Facades\Session::get('company_id'))->pluck('user_id')->toArray();
            $merge =array_merge($emplo,$comp);
            ?>
            @if(in_array($row->receiver_user_id,$merge) && in_array($row->user_id,$merge))

            @else
                @if( \App\XeroSyncedInvoice::where('sent_invoice_id',$row->id)->where('company_id',\Illuminate\Support\Facades\Session::get('company_id'))->count()==1 )
                    <a  href="{{ url('dashboard/company/xero/xeroInvoiceView/'.$row->id) }}" target="_blank" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;">
                        <i class="fa fa-eye"></i>  Xero
                    </a>
                @else
                    <a  href="{{ url('dashboard/company/xero/xeroInvoice/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px; background-color: #15b1b8;">
                        Xero Sync
                    </a>
                @endif
            @endif

        </td>
      </tr>


     @endforeach
 @endif
    @if(count(@$sentInvoice)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif

@elseif($type == 4)
    @if($sentEmailInvoice)
        @foreach($sentEmailInvoice as $row)
            <tr>
                <td>  <label>
                        <input type="checkbox" class="checkbox selectitem forEmailInvoice" value="{{ $row->id }}"  name="emailInvoiceId[]"  >
                        <span class="checkbox-material"><span class="check"></span>
                                </span>
                    </label>
                </td>
                <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
                <td>
                    <span class="blackLabel">Sender</span>
                    <span class="userInfo">                                {{ $row->senderUserInfo->first_name }} {{ $row->senderUserInfo->last_name }}<br/></span>
                    {{ @$row->senderCompanyInfo->name }}<br/><br>
                    <span class="blackLabel">Receiver</span>
                    <span class="userInfo">{{ $row->receiverInfo->email }}<br></span>
                    @if($row->emailSentInvoiceLabels)
                        @if(count(@$row->emailSentInvoiceLabels)>0)
                            <div style="height: 30px;">
                                <div style="position: absolute;" class="emailInvoiceLabelIdentify{{$row->id}}">
                                    @foreach($row->emailSentInvoiceLabels as $sentInvLabel)
                                        @if($sentInvLabel->invoiceLabel->company_id==Session::get('company_id'))
                                            <span style=" background: {{$sentInvLabel->invoiceLabel->color}};display:inline-block;font-size:10px;padding-right: 23px;padding-bottom: 5px;" class="badge badge-pill badge-primary emailinvoiceDelete{{$sentInvLabel->id}}">
                                                    <img style="margin-right: 2px" src="{{ AmazoneBucket::url() }}{{ $sentInvLabel->invoiceLabel->icon }}" height="10" width="10">  {{ $sentInvLabel->invoiceLabel->title }}

                                                <button  data-toggle="modal" data-target="#deleteLabel" data-type="4" data-id="{{$sentInvLabel->id}}"  class="btn btn-raised btn-danger btn-xs"  style="   margin: -1px 0px 0 6px;font-size: 9px;border-radius: 34px;width: 15px;height: 14px;padding: 1px 3px 0px 3px;background: #5a5a5a;position: absolute;">
                                                        <span  class="glyphicon glyphicon-remove" aria-hidden="true"  />
                                                    </button>
                                                </span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div style="height: 30px;">
                                <div style="position: absolute;" class="emailInvoiceLabelIdentify{{$row->id}}">
                                </div>
                            </div>

                        @endif
                    @endif
                </td>
                <td>{{ $row->invoiceInfo->title }}</td>
                <td>
                    {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
                </td>
                {{--<td><span class="invoiceAmount">$134.99</span></td>--}}
                <td>

                    <span class="label label-primary">Sent</span>


                </td>
                <td>
                    <a href="{{ url('dashboard/company/invoiceManager/emailedInvoices/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
                    <a href="{{url('dashboard/company/invoiceManager/invoice/downloadViewInvoiceEmail/'.$row->id)}}" class="btn btn-primary btn-xs btn-raised"><i class="fa fa-download"></i></a>
                    @if($totalInvoiceLabel==0)
                        <a  data-toggle="modal" data-target="#noInvoiceFolderLabeling" data-id="{{$row->id}}" data-type="4" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                    @else
                        <a   data-toggle="modal" data-target="#invoicefolderLabeling" data-id="{{$row->id}}" data-type="4"  class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>
                    @endif


                    @if($row->xero_invoice_id == "0")
                        <a  href="{{ url('dashboard/company/xero/xeroEmailInvoice/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px; background-color: #15b1b8;">
                            Xero Sync
                        </a>
                    @else
                        <a  href="{{ url('dashboard/company/xero/xeroEmailInvoiceView/'.$row->id) }}" target="_blank" class="btn btn-success btn-xs btn-raised"  style="margin:0px 5px 0px;">
                            <i class="fa fa-eye"></i>  Xero
                        </a>
                    @endif

                </td>
            </tr>



        @endforeach
    @endif
    @if(count(@$sentEmailInvoice)==0)
        <tr>
            <td colspan="9">
                <center>Data Empty</center>
            </td>
        </tr>
    @endif


@endif

