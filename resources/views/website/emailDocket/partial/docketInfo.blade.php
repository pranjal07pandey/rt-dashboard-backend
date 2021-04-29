<div class="col-md-6">
    @if(AmazoneBucket::fileExist($emailDocket->company_logo))
        <img src="{{ AmazoneBucket::url() }}{{ $emailDocket->company_logo }}" style="max-width: 100%;max-height:150px;" class="company-logo">
    @else
        <img src="https://dummyimage.com/100x100/f0f0f0/ffffff.jpg&text=logo" class="company-logo">
    @endif
    <br/>From:<br/>
    <strong>{{ $emailDocket->sender_name }}</strong><br>
    {{ $emailDocket->company_name }}
    <br>
    {{ $emailDocket->company_address }}<br>
    <b>ABN:</b> {{ $emailDocket->abn }}
    <br/><br/>
    To:
    @php $recipientNames =  ""; $recipientCompany   =   ""; $data = array(); @endphp
    @foreach($emailDocket->recipientInfo as $recipient)
        @php
            $recipientNames = $recipientNames." ".$recipient->emailUserInfo->email;
            $recipientCompany = $recipientCompany." ".$recipient->receiver_company_name;
            $data[]= array('email'=>$recipient->emailUserInfo->email,'company_name'=>$recipient->receiver_company_name);
        @endphp
    @endforeach
    @foreach($emailDocket->getDistinctRecipientCompany() as $rowData)
        @if($rowData == "")
            @php $recipientNames =  ''; @endphp
            @foreach($data as $items)
                @if($rowData == $items['company_name'])
                    @php $recipientNames .=  $items['email'].', ';@endphp
                @endif
            @endforeach<br/>
            {{strtolower(substr($recipientNames, 0, -2))}}
        @endif
    @endforeach

    @foreach($emailDocket->getDistinctRecipientCompany() as $rowData)
        @if($rowData != "")
            <br> <strong>{{$rowData}}: </strong>
            @php $recipientNames =  ""; @endphp
            @foreach($data as $items)
                @if($rowData == $items['company_name'])
                    @php $recipientNames .=  $items['email'].', ' @endphp
                @endif
            @endforeach
            {{substr($recipientNames, 0, -2)}}
        @endif
    @endforeach
</div>
<div class="col-md-6 text-right docket-details">
    <div class="text-left float-right">
        <strong>{{ $emailDocket->template_title }}</strong><br/>
        <b>Date:</b> {{ $emailDocket->formattedCreatedDate() }}<br/>
        <b>{{$emailDocket->docketInfo->docket_id_label}}:</b>  {{ $emailDocket->formatted_id }}<br>
    </div>
</div>