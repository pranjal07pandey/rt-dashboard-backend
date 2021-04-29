@if($row->is_cancel==1)
    @if($row->sender_company_id==$company->id)
    <tr class="cancelled">
        @if(@$docketCheckbox)
            <td><input type="checkbox" class="checkbox selectitem forDocket" value="{{ $row->id }}"  name="d[]"></td>
        @endif
        <td><span class="blackLabel">{{ $row->formatted_id }}<br/></span>
        </td>
        <td>
            <span class="blackLabel">Sender</span>
            <span class="userInfo">{{ @$row->sender_name }}</span>
            {{ @$row->company_name }}<br/><br/>
            <span class="blackLabel">Receiver</span>
            <span class="userInfo">
                @if($row->formattedRecipientList())
                    @php $sns = 0; @endphp
                    @foreach($row->formattedRecipientList() as $key=>$value)
                        @foreach($value as $keys)
                            @php $sns++; @endphp
                            @if($sns<=count($row->recipientInfo) && $sns!=1) , @endif
                            {{ $keys }}
                        @endforeach
                    @endforeach
                @endif
            </span>
            <?php $sn = 0; ?>
            @foreach($row->formattedRecipientList() as $key=>$value)
                <?php $sn++; ?>
                @if($sn<=count($row->formattedRecipientList()) && $sn!=1),@endif
                {{$key}}
            @endforeach

{{--            @php $folder =  $row->folder(); @endphp--}}
{{--            @if($folder != null)--}}
{{--                <div style="display:flex;align-items: center;font-size: 12px;">--}}
{{--                    <span><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 15px; margin-top:-3px;">&nbsp;&nbsp;{{$folder->folder->name}}</span>--}}
{{--                </div>--}}
{{--            @endif--}}

            <div class="docket-label-container">
                <div class="item-wrapper" id="docketLabelIdentify{{$row->id}}">
                    <ul>
                        @if(count($row->sentDocketLabels)>0)
                            @php $sentDocketLabels  =   $row->sentDocketLabels; $type = 1; @endphp
                            @include('dashboard.company.docketManager.partials.table-view.docket-label')
                        @endif
                    </ul>
                </div>
            </div>
        </td>
        <td>
            {{ $row->docketInfo->title }}
            @if(@$row->docketInfo->previewFields->count()>0)
                @include('dashboard.company.docketManager.partials.table-view.sent-docket-preview')
            @endif
        </td>
        <td>{{ $row->formattedCreatedDate() }}</td>
        <td>
            <span class="label label-danger">Cancelled</span>
        </td>
        <td>
            <a href="{{ url('dashboard/company/docketBookManager/docket/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
        </td>
    </tr>
    @endif
@else
    <tr>
        @if(@$docketCheckbox)
            <td><input type="checkbox" class="checkbox selectitem forDocket" value="{{ $row->id }}"  name="docketId[]"></td>
        @endif
        <td><span class="blackLabel">{{ $row->formatted_id }}<br/></span>

            <div style="padding: 5px 10px;line-height: 1.5em;font-size: 12px;">
               @include('dashboard.company.docketManager.partials.table-view.sent-docket-grid-preview')
            </div>

        </td>
        <td>
            <span class="blackLabel">Sender</span>
            <span class="userInfo">{{ @$row->sender_name }}</span>
            {{ @$row->company_name }}<br/><br/>

            <span class="blackLabel">Receiver</span>
            <span class="userInfo">
                @if($row->formattedRecipientList())
                    @php $sns = 0; @endphp
                    @foreach($row->formattedRecipientList() as $key=>$value)
                        @foreach($value as $keys)
                            @php $sns++; @endphp
                            @if($sns<=count($row->recipientInfo) && $sns!=1) , @endif
                            {{ $keys }}
                        @endforeach
                    @endforeach
                @endif
            </span>
            <?php $sn = 0; ?>
            @foreach($row->formattedRecipientList() as $key=>$value)
                <?php $sn++; ?>
                @if($sn<=count($row->formattedRecipientList()) && $sn!=1),@endif
                {{$key}}
            @endforeach

{{--            @php $folder =  $row->folder(); @endphp--}}
{{--            @if($folder != null)--}}
{{--                <div style="display:flex;align-items: center;font-size: 12px;">--}}
{{--                    <span><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 15px; margin-top:-3px;">&nbsp;&nbsp;{{$folder->folder->name}}</span>--}}
{{--                </div>--}}
{{--            @endif--}}

            <div class="docket-label-container">
                <div class="item-wrapper" id="docketLabelIdentify{{$row->id}}">
                    <ul>
                    @if(count($row->sentDocketLabels)>0)
                        @php $sentDocketLabels  =   $row->sentDocketLabels; $type = 1; @endphp
                        @include('dashboard.company.docketManager.partials.table-view.docket-label')
                    @endif
                    </ul>
                </div>
            </div>
        </td>
        <td>
            {{ $row->template_title }}
            @if(@$row->docketInfo->previewFields->count()>0)
                @include('dashboard.company.docketManager.partials.table-view.sent-docket-preview')
            @endif
        </td>

        <td>{{ $row->formattedCreatedDate() }}</td>
        <td>
            @if($row->status==1)
                <span class="label label-success">Approved</span>
            @elseif($row->sender_company_id	==Session::get('company_id'))
                <span class="label label-warning">Sent</span>
                @if($row->status==3)
                    <span class="label label-danger"> Rejected</span>
                @else
                <span class="label label-primary">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                @endif
            @else
                <span class="label label-warning">Received</span>
                @if($row->status==3)
                    <span class="label label-danger"> Rejected</span>
                @else
                    <span class="label label-primary">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                @endif
            @endif
        </td>

        <td>



            <a href="{{ url('dashboard/company/docketBookManager/docket/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
            <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewDocket/'.$row->id)}}" class="btn btn-success btn-xs btn-raised"  style="background-color: #15b1b8;"><i class="fa fa-download"></i></a>
            @if(@$checktrashFolder == false)

                <a  data-toggle="modal" data-target="#deleteSentDocket" data-type="1" data-id="{{$row->id}}"  class="btn btn-warning btn-xs btn-raised" ><i class="fa fa-trash"></i></a>
                <a  data-toggle="modal" data-target="#docketLabelModal" data-formatted-id="{{ $row->formattedDocketID() }}" data-id="{{$row->id}}" data-type="1" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

                @if($row->sentDocketRecipientApproval->count()== $row->sentDocketRecipientUnapproved->count())
                    @if($row->sender_company_id == Session::get('company_id'))

                        <a  data-toggle="modal" data-target="#cancelDocketModal"  data-id="{{$row->id}}" data-type="1" class="btn btn-danger btn-xs btn-raised"><i class="fa fa-times"></i> Cancel</a>

                    @endif
                @endif
            @else

                <a  data-toggle="modal" data-target="#recoverFolderItem" data-type="1" data-id="{{$row->id}}"  class="btn btn-info btn-xs btn-raised" >Recover</a>
                <i style=" color: lightgrey;font-size: 10px;"><strong style="color: #636363;">Deleted At: </strong>{{\Carbon\Carbon::parse($row->deleted_at)->format('d-M-Y g:i A')}}</i>

            @endif




        </td>
    </tr>
@endif
