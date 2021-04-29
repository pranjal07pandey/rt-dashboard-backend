<tr>
    <td> {{ $docketValue->label }}</td>
    <td>
        @if($docketValue->sentDocketAttachment->count()>0)
            <ul class="pdf">
                @foreach($docketValue->sentDocketAttachment as $document)
                    <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="{{ AmazoneBucket::url() }}{{ $document->url }}" target="_blank">{{$document->document_name}}</a></b></li>
                @endforeach
            </ul>
        @else
            <b>No Document Attached</b>
        @endif
    </td>
</tr>