@if($row->is_cancel==1)
    @if($row->sender_company_id==$company->id)
        <tr class="cancelled">
            @if(@$docketCheckbox)
                <td><input type="checkbox" class="checkbox selectitem forDocket" value="{{ $row->id }}"  name="d[]"></td>
            @endif
            <td><span class="blackLabel">{{ $row->formatted_id }}<br/></span></td>
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
                <div class="docket-label-container">
                    <div class="item-wrapper" id="docketLabelIdentify{{$row->id}}">
                        <ul>
                            @if(count($row->sentDocketLabels)>0)
                                @php $sentDocketLabels  =   $row->sentDocketLabels; $type = 1; @endphp
                                @include('shareable-folder.folder.partials.table-view.label.docket-label')
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
                <a href="{{ url('folder/docket/view/'.$row->encryptedID()) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
            </td>
        </tr>
    @endif
@else
    <tr>
        @if(@$docketCheckbox)
            <td><input type="checkbox" class="checkbox selectitem forDocket" value="{{ $row->id }}"  name="docketId[]"></td>
        @endif
        <td><span class="blackLabel">{{ $row->formatted_id }}<br/></span></td>
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
            <div class="docket-label-container">
                <div class="item-wrapper" id="docketLabelIdentify{{$row->id}}">
                    <ul>
                        @if(count($row->sentDocketLabels)>0)
                            @php $sentDocketLabels  =   $row->sentDocketLabels; $type = 1; @endphp
                            @include('shareable-folder.folder.partials.table-view.label.docket-label')

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
            @else
                @if($row->status==3)
                    <span class="label label-danger"> Rejected</span>
                @else
                    <span class="label label-primary">{{ @$row->sentDocketRecipientApproved->count() }}/{{ @$row->sentDocketRecipientApproval->count() }} Approved</span>
                @endif
            @endif
        </td>
        <td>
        <a href="{{ url('folder/docket/view/'.$row->encryptedID()) }}" class="btn btn-success btn-xs btn-raised"><i class="fa fa-eye"></i></a>
        <a  href="{{url('folder/docket/download/'.$row->encryptedID())}}" class="btn btn-success btn-xs btn-raised"  style="background-color: #15b1b8;"><i class="fa fa-download"></i></a>
        </td>
    </tr>
@endif
