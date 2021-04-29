<tr>
    <td> {{ $docketValue->label }}</td>

    <td>
        @if($docketValue->sentDocketImageValue->count()>0)
            <ul style="list-style: none;margin: 0px;padding: 0px;">
                @foreach($docketValue->sentDocketImageValue as $sketchPad)
                    <li style="margin-right:10px;float: left;">
                        <a href="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" target="_blank">
                            <img src="{{ AmazoneBucket::url() }}{{ $sketchPad->value }}" style="height: 100px;">
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <b>No Sketch Pad Attached</b>
        @endif
    </td>
</tr>