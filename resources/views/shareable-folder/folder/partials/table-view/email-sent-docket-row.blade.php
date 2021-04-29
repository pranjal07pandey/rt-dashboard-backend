<tr>
    @if(@$docketCheckbox)
        <td><input type="checkbox" class="checkbox selectitem forEmailDocket" value="{{ $row->id }}"  name="ed[]"></td>
    @endif
    <td><span class="blackLabel">{{ $row->formatted_id }}</span></td>
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
        <div class="docket-label-container">
            <div class="item-wrapper" id="emailDocketLabelIdentify{{$row->id}}">
                <ul>
                    @if(count($row->sentEmailDocketLabels)>0)
                        @php $sentDocketLabels  =   $row->sentEmailDocketLabels; $type = 2; @endphp
                        @include('shareable-folder.folder.partials.table-view.label.docket-label')
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

        @if($row->status==1)
            <span class="label label-success">Approved</span>
        @else
            <span class="label label-primary">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
        @endif
    </td>
    <td>
        <a href="{{ url('/folder/docket/view/emailed/'.$row->encryptedID()) }}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-eye"></i></a>
        <a  href="{{url('/folder/docket/download/emailed/'.$row->encryptedID())}}" class="btn btn-success btn-xs btn-raised" ><i class="fa fa-download"></i></a>
    </td>
</tr>
