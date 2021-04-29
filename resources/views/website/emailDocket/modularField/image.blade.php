
<tr>
    <td> {{ $docketValue->label }}</td>
    <td>
        @if($docketValue->sentDocketImageValue->count()>0)
            <ul style="list-style: none;margin: 0px;padding: 10px;">
                @foreach($docketValue->sentDocketImageValue as $signature)
                    <li style="margin-right:10px;display: inline-block; padding-bottom: 13px; ">
                        <a href="{{ AmazoneBucket::url() }}{{ $signature->value }}" target="_blank">
                            <img src="{{ AmazoneBucket::url() }}{{ $signature->value }}" width="100px"  style="border:1px solid #dddddd;">
                        </a>
                    </li>

                @endforeach

            </ul>
        @else
            <b>No Image Attached</b>
        @endif
    </td>
</tr>