@php
    $recipentArray = [];
    $parseDraft = $row->value;
    $is_email = ($parseDraft['docket_data']['is_email'] === 'true') ? true : false ;
    if($is_email == true){
        $receiver = $parseDraft['email_user_receivers'];
        foreach ($receiver as $item){
            $recipentArray[] =  ($item['full_name']!= "") ? $item['full_name'] : $item['email'];
        }
   }else if($is_email == false){
        $receiver = $parseDraft['rt_user_receivers'];
        foreach ($receiver as $item){
            $recipentArray[] =  $item['first_name'].' '.$item['last_name'];
        }
    }
@endphp
<tr>

    <td><span class="blackLabel">{{ $row->id }}</span></td>
    <td><span class="blackLabel" style="word-break: break-word;">{{ $parseDraft['docket_data']['draft_name'] }}</span></td>
    <td><span class="blackLabel" style="word-break: break-word;">{{ $parseDraft['template']['title'] }}</span></td>
    <td>
        <span class="blackLabel">
            @foreach ($recipentArray as $item)
            <span class="badge badge-pill" style="margin: 5px;padding:5px">{{ $item }}</span>
            @endforeach
        </span>
    </td>
    {{-- <td><span class="blackLabel">{{ implode(",",$recipentArray)}}</span></td> --}}
    <td><span class="blackLabel">{{ $parseDraft['docket_data']['draft_date'] }}</span></td>
    <td><span class="blackLabel">{{ $row->userInfo->first_name . ' ' . $row->userInfo->last_name  }}</span></td>
    <td><span class="blackLabel">Synced</span></td>
    {{-- <td> <a href="{{ url('dashboard/company/docketBookManager/docket/draft/view/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a></td> --}}
    <td> <a href="{{ route('dockets.draftEdit',$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a></td>




    {{--    <td>--}}
{{--        <span class="blackLabel">Sender</span>--}}
{{--        <span class="userInfo"> {{ $row->sender_name }}<br/></span>--}}
{{--        {{ $row->company_name }}<br/><br>--}}
{{--        <span class="blackLabel">Receiver</span>--}}
{{--        <span class="userInfo">--}}
{{--            @php $recipientNames =  ""; $recipientCompany   =   ""; @endphp--}}
{{--            @foreach($row->recipientInfo as $recipient)--}}
{{--                @php--}}
{{--                    $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;--}}
{{--                    $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;--}}
{{--                    if($row->recipientInfo->count()>1){--}}
{{--                       if($row->recipientInfo->last()->id!=$recipient->id){--}}
{{--                            $recipientNames =  $recipientNames.", ";--}}
{{--                            $recipientCompany = $recipientCompany.", ";--}}
{{--                        }--}}
{{--                    }--}}
{{--                @endphp--}}
{{--            @endforeach--}}
{{--            {{ $recipientNames }}<br>--}}
{{--        </span>--}}
{{--        {{ $recipientCompany }}<br>--}}
{{--        --}}{{--@php $folder =  $row->folder(); @endphp--}}
{{--        --}}{{--@if($folder != null)--}}
{{--        --}}{{--<div style="display:flex;align-items: center;font-size: 12px;">--}}
{{--        --}}{{--<span><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 15px; margin-top:-3px;">&nbsp;&nbsp;{{$folder->folder->name}}</span>--}}
{{--        --}}{{--</div>--}}
{{--        --}}{{--@endif--}}
{{--        <div class="docket-label-container">--}}
{{--            <div class="item-wrapper" id="emailDocketLabelIdentify{{$row->id}}">--}}
{{--                <ul>--}}
{{--                    @if(count($row->sentEmailDocketLabels)>0)--}}
{{--                        @php $sentDocketLabels  =   $row->sentEmailDocketLabels; $type = 2; @endphp--}}
{{--                        @include('dashboard.company.docketManager.partials.table-view.docket-label')--}}
{{--                    @endif--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </td>--}}
{{--    <td>{{ $row->template_title }}--}}
{{--        @if(@$row->docketInfo->previewFields->count()>0)--}}
{{--            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-preview')--}}
{{--        @endif--}}
{{--    </td>--}}
{{--    <td>{{ $row->formattedCreatedDate() }}</td>--}}
{{--    <td>--}}
{{--        @if($row->status==0)<span class="label label-primary">Sent</span>@endif--}}
{{--        @if($row->status==1)<span class="label label-success">Approved</span>@endif--}}
{{--    </td>--}}
{{--    <td>--}}
{{--        <a href="{{ url('dashboard/company/docketBookManager/docket/view/emailed/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>--}}
{{--        <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewemailed/'.$row->id)}}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-download"></i></a>--}}

{{--        @if(@$checktrashFolder == false)--}}
{{--            <a  data-toggle="modal" data-target="#deleteSentDocket" data-type="2" data-id="{{$row->id}}"  class="btn btn-warning btn-xs btn-raised" ><i class="fa fa-trash"></i></a>--}}
{{--            <a  data-toggle="modal" data-target="#docketLabelModal" data-formatted-id="{{ $row->formattedDocketID() }}" data-id="{{$row->id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>--}}

{{--        @else--}}
{{--            <a  data-toggle="modal" data-target="#recoverFolderItem" data-type="2" data-id="{{$row->id}}"  class="btn btn-info btn-xs btn-raised" >Recover</a>--}}
{{--            <i style=" color: lightgrey;font-size: 10px;"><strong style="color: #636363;">Deleted At: </strong>{{\Carbon\Carbon::parse($row->deleted_at)->format('d-M-Y g:i A')}}</i>--}}
{{--        @endif--}}

{{--    </td>--}}
</tr>