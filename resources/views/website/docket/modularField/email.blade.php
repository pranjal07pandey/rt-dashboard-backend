<tr>
    <td scope="row">{{ $docketValue->label }}</td>
    <td style="white-space: pre-wrap;">
        @foreach(unserialize($docketValue->value) as $email)
            {!! $email['email'] !!}
        @endforeach
    </td>
</tr>
