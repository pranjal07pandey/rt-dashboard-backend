@if($sentInvoice->isDocketAttached==1)
    @if($sentInvoice->attachedEmailDocketsInfo->count()>0)
        @foreach($sentInvoice->attachedEmailDocketsInfo as $invoiceDocket)
            <tr>
                <td>
                    <img src="{{ asset('assets/dashboard/img/documentIcon2.png') }}" width="8px">
                    <strong>{{ $invoiceDocket->docketInfo->docketInfo->title }}</strong><br/>
                    Docket :  {{ $emailDocket->formattedDocketID() }}<br/>

                    <?php $invoiceDescriptionQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',1)->get(); ?>
                    @foreach($invoiceDescriptionQuery as $description)
                        {{  $description->sentEmailDocketValueInfo->docketFieldInfo->label }} : {{ $description->sentEmailDocketValueInfo->value }}<br/>
                    @endforeach
                </td>
                <td>
                    <?php
                    $invoiceAmount  =    0;
                    $invoiceAmountQuery    =    \App\SentEmailDocketInvoice::where('email_sent_docket_id',$invoiceDocket->docketInfo->id)->where('type',2)->get();
                    foreach($invoiceAmountQuery as $amount){
                        $unitRate    =  $amount->sentEmailDocketValueInfo->sentDocketUnitRateValue->toArray();
                        $invoiceAmount   =   $invoiceAmount + $unitRate[0]["value"] * $unitRate[1]["value"];
                    }
                    ?>
                    $ {{ $invoiceAmount }}
                </td>
            </tr>
        @endforeach
    @endif
@endif