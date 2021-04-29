<tr>
    <td> {{ $docketValue->label }}</td>
    <td>
        @if($docketValue->sentDocketImageValue->count()>0)
            <ul style="list-style: none;margin: 0px;padding: 0px;">
                @foreach($docketValue->sentDocketImageValue as $signature)
                    <li style="margin-right:10px;float: left;">
                        <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                            <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                        </a>
                        <p style="font-weight: 500;color: #868d90;margin-left: 12px;">{{$signature->name}}</p>
                    </li>
                @endforeach
            </ul>
        @else
            <b>No Signature Attached</b>
        @endif
    </td>
</tr>