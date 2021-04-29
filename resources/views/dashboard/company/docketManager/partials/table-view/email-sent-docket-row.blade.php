<tr>
    @if(@$docketCheckbox)
    <td><input type="checkbox" class="checkbox selectitem forEmailDocket" value="{{ $row->id }}"  name="ed[]"></td>
    @endif
    <td><span class="blackLabel">{{ $row->formatted_id }}</span>


        <div style="padding: 5px 10px;line-height: 1.5em;font-size: 12px;">

            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-grid-preview')

        </div>

    </td>
    <td>
        <span class="blackLabel">Sender</span>
        <span class="userInfo"> {{ $row->sender_name }}<br/></span>
        {{ $row->company_name }}<br/><br>

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
        </span>
        {{ $recipientCompany }}<br>
        {{--@php $folder =  $row->folder(); @endphp--}}
        {{--@if($folder != null)--}}
            {{--<div style="display:flex;align-items: center;font-size: 12px;">--}}
            {{--<span><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 15px; margin-top:-3px;">&nbsp;&nbsp;{{$folder->folder->name}}</span>--}}
            {{--</div>--}}
        {{--@endif--}}

        <div class="docket-label-container">
            <div class="item-wrapper" id="emailDocketLabelIdentify{{$row->id}}">
                <ul>
                    @if(count($row->sentEmailDocketLabels)>0)
                        @php $sentDocketLabels  =   $row->sentEmailDocketLabels; $type = 2; @endphp
                        @include('dashboard.company.docketManager.partials.table-view.docket-label')
                    @endif
                </ul>
            </div>
        </div>
    </td>
    <td>{{ $row->template_title }}
        @if(@$row->docketInfo->previewFields->count()>0)
            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-preview')
        @endif
    </td>
    <td>{{ $row->formattedCreatedDate() }}</td>
    <td>
        @if($row->status==0)<span class="label label-primary">Sent</span>@endif
        @if($row->status==1)<span class="label label-success">Approved</span>@endif
    </td>
    <td>
        <a href="{{ url('dashboard/company/docketBookManager/docket/view/emailed/'.$row->id) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
        <a  href="{{url('dashboard/company/docketBookManager/docket/downloadViewemailed/'.$row->id)}}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-download"></i></a>

        @if(@$checktrashFolder == false)

               <a  data-toggle="modal" data-target="#deleteSentDocket" data-type="2" data-id="{{$row->id}}"  class="btn btn-warning btn-xs btn-raised" ><i class="fa fa-trash"></i></a>
               <a  data-toggle="modal" data-target="#docketLabelModal" data-formatted-id="{{ $row->formattedDocketID() }}" data-id="{{$row->id}}" data-type="2" class="btn btn-info btn-xs btn-raised"><i class="fa fa-check"></i> Mark</a>

         @else
            <a  data-toggle="modal" data-target="#recoverFolderItem" data-type="2" data-id="{{$row->id}}"  class="btn btn-info btn-xs btn-raised" >Recover</a>
            <i style=" color: lightgrey;font-size: 10px;"><strong style="color: #636363;">Deleted At: </strong>{{\Carbon\Carbon::parse($row->deleted_at)->format('d-M-Y g:i A')}}</i>
        @endif

    </td>
</tr>
