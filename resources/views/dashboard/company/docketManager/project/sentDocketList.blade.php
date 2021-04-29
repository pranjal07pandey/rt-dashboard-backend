@extends('layouts.companyDashboard')
@section('content')

    <section class="content-header">
        <h1>
            <i class="fa fa fa-file-text-o"></i> Docket Book Manager
            <small>Add/View Project Details</small>
        </h1>
        <ol class="breadcrumb hidden-sm hidden-xs">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="#">Docket Book Manager</a></li>
            <li class="active">View</li>
        </ol>
        {{--<a href="#" class="btn btn-xs btn-raised  btn-success"  id='start-tour' style="position: absolute;right: 0px;top: -25px;font-weight: bold;font-size: 12px;text-decoration: none;">Help</a>--}}
    </section>
    @include('dashboard.company.include.flashMessages')


    <br>
    <div class="companyProfileBox" style="padding: 20px 5px 0 20px;">
        <div class="companyProfileImage" style="background-image: url('{{ asset('assets/project.png') }}');    background-size: 50px 50px;"></div>
       <div class="row">
           <div class="col-md-4">
               <div class="companyInfo" >
                   <strong>{{$projects->name}}</strong>
                   <span>{{$projects->userInfo->first_name." ".$projects->userInfo->last_name}}</span>
               </div>
           </div>
           <div class="col-md-7">
               <div class="companyInfo" style="float: right;" >
                   <strong>Total: ${{$totalValue}} </strong>
                   <strong>Budget: ${{$projects->budget}} </strong>
                   <strong>Ratio: {{ round($totalValue/$projects->budget,5) }} </strong>
               </div>
           </div>
       </div>


    </div>
    <br>
    <div class="row" style="padding-top: 15px;background: #fff;margin: 0px;min-height: 400px;">
        <div class="clearfix"></div>
        <div class="col-md-12">
            <h3 style="font-size: 20px; margin: 0px 0px 29px;font-weight: 500;display:inline-block">All Sent Docket</h3>
            <div class="pull-right closeButtonProject">
                <!-- Button trigger modal -->

                @if($projects->is_close == 1)
                     <span style="color: red"> <i class="fa fa-close"></i> Project Closed</span>

                    @else
                <button style="margin-top: -1px;" type="button" id="first" class="btn btn-xs btn-raised btn-block btn-info" data-id="{{$projects->id}}" data-toggle="modal" data-target="#projectacr">
                    <i class="fa fa-close"></i> Close Project
                    <div class="ripple-container"></div>
                </button>
                @endif

            </div>

        </div>
        <div class="col-md-12">
            <div class="datatable">
                <table class="table" id="emailClientDataTable">
                    <thead>
                    <tr>
                        <th>Docket Id</th>
                        <th>Info</th>
                        <th>Docket Name</th>
                        <th>Date Added</th>
                        <th>Dollar Value</th>
                        <th>Status</th>
                        <th width="200px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(@$result)
                        @foreach($result->sortByDesc('created_at') as $row)
                            @if($row instanceof App\SentDockets)

                                <?php
                                if ($row->senderUserInfo->employeeInfo){
                                    $companyId = $row->senderUserInfo->employeeInfo->companyInfo->id;
                                }else if ($row->senderUserInfo->companyInfo){
                                    $companyId = $row->senderUserInfo->companyInfo->id;
                                }
                                ?>
                                @if($row->is_cancel==1)
                                    @if($companyId ==Session::get('company_id'))
                                        <tr style="    background: #f3f3f3;font-style: italic;">

                                            <td><span class="blackLabel">{{ 'rt-'.$row->sender_company_id.'-doc-'.$row->company_docket_id }}</span></td>

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



                                                @php

                                                    if (\App\FolderItem::where('ref_id',$row->id)->where('type',1)->count()!=0){
                                                       $folderItem = \App\FolderItem::where('ref_id',$row->id)->where('type',1)->first()->folder->name;
                                                    }else{
                                                       $folderItem = null;
                                                    }
                                                @endphp

                                                @if($folderItem!=null)
                                                    <h5 style="    margin-bottom: 19px;margin-top: 0px;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;"> {{$folderItem}}</h5>
                                                @endif


                                            </td>
                                            <td>{{ $row->docketInfo->title }}
                                                @if(@$row->docketInfo->previewFields->count()>0)
                                                    <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                                                        @foreach(@$row->docketInfo->previewFields as $previewField)
                                                            @if(@$previewField->docket_filed_info->docket_field_category_id==5 || @$previewField->docket_filed_info->docket_field_category_id == 9)
                                                                @if(@count(\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                                                    <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                                    <ul class="sentDocketImagePreview">
                                                                        @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                                            <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                                                                        @endforeach
                                                                    </ul>
                                                                @endif
                                                            @elseif(@$previewField->docket_filed_info->docket_field_category_id == 7)
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
                                                            @elseif(@$previewField->docket_filed_info->docket_field_category_id == 8)
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


                                            <td>

                                                <?php

                                                $docketTallyValue = array();
                                                foreach ($row->docketInfo->docketField as $docketField){
                                                    if ($docketField->docket_field_category_id == 24 ){
                                                        $sn = 1; $total = 0;
                                                        foreach($docketField->docketFieldValueBySentDocketId($row->id)->first()->sentDocketTallyableUnitRateValue as $rows){
                                                            if($sn == 1){
                                                                $total = $rows->value;
                                                            }else{
                                                                $total    =   $total*$rows->value;
                                                            }
                                                            $sn++;
                                                        }


                                                        $docketTallyValue[] =  $total;


                                                    }elseif($docketField->docket_field_category_id == 25 ){

                                                        $docketTallyValue[] =   intval($docketField->docketFieldValueBySentDocketId($row->id)->first()->value);

                                                    }
                                                }

                                                ?>

                                                <span class="invoiceAmount">$ {{array_sum($docketTallyValue)}} </span>


                                            </td>


                                            <td>
                                                <span class="label label-danger">Cancelled</span>
                                            </td>
                                            <td>
                                                <a href="{{ url('dashboard/company/docketBookManager/docket/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>





                                            </td>
                                        </tr>

                                    @endif
                                @else
                                    <tr>

                                        <td><span class="blackLabel">{{ 'rt-'.$row->sender_company_id.'-doc-'.$row->company_docket_id }}</span></td>

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
                                            @php

                                                if (\App\FolderItem::where('ref_id',$row->id)->where('type',1)->count()!=0){
                                                   $folderItem = \App\FolderItem::where('ref_id',$row->id)->where('type',1)->first()->folder->name;
                                                }else{
                                                   $folderItem = null;
                                                }
                                            @endphp

                                            @if($folderItem!=null)
                                                <h5 style="    margin-bottom: 19px;margin-top: 0px;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;"> {{$folderItem}}</h5>
                                            @endif
                                        </td>
                                        <td>{{ $row->docketInfo->title }}
                                            @if(@$row->docketInfo->previewFields->count()>0)
                                                <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                                                    @foreach(@$row->docketInfo->previewFields as $previewField)
                                                        @if(@$previewField->docket_filed_info->docket_field_category_id==5 || @$previewField->docket_filed_info->docket_field_category_id == 9)
                                                            @if(@count(\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                                                <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                                <ul class="sentDocketImagePreview">
                                                                    @foreach(@\App\SentDocketsValue::where('sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                                        <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        @elseif(@$previewField->docket_filed_info->docket_field_category_id == 7)
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
                                                        @elseif(@$previewField->docket_filed_info->docket_field_category_id == 8)

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
                                        <td>
                                            <?php

                                            $docketTallyValue = array();
                                            foreach ($row->docketInfo->docketField as $docketField){
                                                if ($docketField->docket_field_category_id == 24 ){
                                                    $sn = 1; $total = 0;
                                                    foreach($docketField->docketFieldValueBySentDocketId($row->id)->first()->sentDocketTallyableUnitRateValue as $rows){
                                                        if($sn == 1){
                                                            $total = $rows->value;
                                                        }else{
                                                            $total    =   $total*$rows->value;
                                                        }
                                                        $sn++;
                                                    }


                                                    $docketTallyValue[] =  $total;


                                                }elseif($docketField->docket_field_category_id == 25 ){

                                                    $docketTallyValue[] =   intval($docketField->docketFieldValueBySentDocketId($row->id)->first()->value);

                                                }
                                            }

                                            ?>

                                            <span class="invoiceAmount">$ {{array_sum($docketTallyValue)}} </span>
                                        </td>
                                        <td>

                                            @if($row->status==1)
                                                <span class="label label-success">Approved</span>
                                            @elseif($row->sender_company_id ==Session::get('company_id'))
                                                <span class="label label-primary">Sent</span>
                                                @if($row->status==3)
                                                    <span class="label label-danger"> Rejected</span>

                                                @else
                                                    <span class="label label-success">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>

                                                @endif
                                            @else
                                                <span class="label label-warning">Received</span>
                                                @if($row->status==3)
                                                    <span class="label label-danger"> Rejected</span>

                                                @else
                                                    <span class="label label-success">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                                                @endif
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ url('dashboard/company/docketBookManager/docket/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
                                            <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$row->id)}}" class="btn btn-success btn-xs btn-raised"  ><i class="fa fa-download"></i></a>


                                            @if(Session::get('company_id')==1)
                                                @if($row->sentDocketRecipientApproval->count()== $row->sentDocketRecipientUnapproved->count())
                                                    @if($companyId == Session::get('company_id'))
                                                        <a  id="openCancelModal" data-id="{{$row->id}}" data-type="1" class="btn btn-danger btn-xs btn-raised"><i class="fa fa-times"></i>Cancel</a>
                                                    @endif
                                                @endif
                                            @endif

                                        </td>
                                    </tr>

                                @endif

                            @endif
                            @if($row instanceof App\EmailSentDocket)
                                <tr>

                                    <td><span class="blackLabel">{{ 'rt-'.$row->company_id.'-edoc-'.$row->company_docket_id }}</span></td>

                                    <td>
                                        <span class="blackLabel">Sender</span>
                                        <span class="userInfo"> {{ $row->sender_name }}<br/></span>
                                        {{ @$row->company_name }}<br/><br>
                                        <span class="blackLabel">Receiver</span>
                                        <span class="userInfo">   @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp
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

                                        @php

                                            if (\App\FolderItem::where('ref_id',$row->id)->where('type',3)->count()!=0){
                                               $folderItem = \App\FolderItem::where('ref_id',$row->id)->where('type',3)->first()->folder->name;
                                            }else{
                                               $folderItem = null;
                                            }
                                        @endphp

                                        @if($folderItem != null)
                                            <h5 style="    margin-bottom: 19px;margin-top: 0px;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;"> {{$folderItem}}</h5>
                                        @endif


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
                                    <td>{{ $row->template_title }}
                                        @if(@$row->docketInfo->previewFields->count()>0)
                                            <div style="padding: 5px 10px;background: #fafafa;border: 1px dotted #ddd;line-height: 1.5em;font-size: 12px;">
                                                @foreach(@$row->docketInfo->previewFields as $previewField)
                                                    @if(@$previewField->docket_filed_info->docket_field_category_id==5 || @$previewField->docket_filed_info->docket_field_category_id == 9)
                                                        @if(@count(\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue)>0)
                                                            <span style="font-weight: 500;">{{ @$row->sentDocketPreviewValueBySentDocketId($row->id,$previewField->docket_field_id)->docketFieldInfo->label }}</span>
                                                            <ul class="sentDocketImagePreview">
                                                                @foreach(@\App\EmailSentDocketValue::where('email_sent_docket_id',$row->id)->where('docket_field_id',$previewField->docket_field_id)->first()->sentDocketImageValue as $images)
                                                                    <li> <a href="{{ AmazoneBucket::url() }}{{ $images->value }}" target="_blank"><img height="50"  src="{{ AmazoneBucket::url() }}{{ $images->value }}"></a></li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    @elseif(@$previewField->docket_filed_info->docket_field_category_id == 7)
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
                                                    @elseif(@$previewField->docket_filed_info->docket_field_category_id == 8)

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
                                                    <br/>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') }}
                                    </td>
                                    <td>

                                        <?php

                                        $emailDocketTallyValue = array();
                                        foreach ($row->docketInfo->docketField as $docketField){
                                            if ($docketField->docket_field_category_id == 24 ){
                                                $sn = 1; $total = 0;
                                                foreach($docketField->docketFieldValueByEmailSentDocketId($row->id)->first()->sentDocketTallyableUnitRateValue as $rows){
                                                    if($sn == 1){
                                                        $total = $rows->value;
                                                    }else{
                                                        $total    =   $total*$rows->value;
                                                    }
                                                    $sn++;
                                                }
                                                $emailDocketTallyValue[] =  $total;


                                            }elseif($docketField->docket_field_category_id == 25 ){
                                                $emailDocketTallyValue[] =   intval($docketField->docketFieldValueByEmailSentDocketId($row->id)->first()->value);

                                            }
                                        }

                                        ?>

                                        <span class="invoiceAmount">$ {{array_sum($emailDocketTallyValue)}} </span>


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

                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endif


                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br><br>

    <div class="modal fade" id="projectacr" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
        <div id="second"  class="modal-dialog modal-lg" role="document">
            <div id="model" data-target="#myModal"></div>
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Close Project</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="project_id">
                            <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to close this Project?</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" >
                            <div class="form-group" style="margin: 9px 0 0 0;">
                                <i style="color: red;" class="passwordErrorMessage"></i>
                                <input type="password" class="form-control" id="confirmPassword" placeholder="Enter Password">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="closeProject">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('customScript')
    <style>
        #emailClientDataTable_filter{
            position: absolute;
            right: 130px;
            top: -53px;
        }
    </style>

    <script>
        $('#projectacr').on('show.bs.modal', function(e) {
            var id = $(e.relatedTarget).data('id');
            $("#project_id").val(id);

        });

        $('#closeProject').on('click',function () {
            var project_id = $("#project_id").val();
            var password = $("#confirmPassword").val();
            $.ajax({
                type: "post",
                url: "{{url('dashboard/company/docketManager/project/closeProject')}}",
                data : {id:project_id,password:password},
                success: function (response) {
                    if (response['status'] == 1){
                       $('.closeButtonProject').html('<span style="color: red"> <i class="fa fa-close"></i> Project Closed</span>')
                        $('.passwordErrorMessage').css('display','none')
                        $('#projectacr').modal('hide')


                    }else if(response['status'] == 0){
                        $(".passwordErrorMessage").text(response['message'])


                    }

                }
            });

        })
    </script>



@endsection