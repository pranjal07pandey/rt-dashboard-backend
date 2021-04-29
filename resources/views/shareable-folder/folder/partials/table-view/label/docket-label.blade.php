@foreach($sentDocketLabels as $sentDocketLabel)

        <li style="background: {{$sentDocketLabel->docketLabel->color}}" class="docket-label-{{$sentDocketLabel->id}}">
            @if($sentDocketLabel->docketLabel->icon)
                <img src="{{ AmazoneBucket::url() }}{{ $sentDocketLabel->docketLabel->icon }}" height="10" width="10">
            @endif
            {{ $sentDocketLabel->docketLabel->title }}
        </li>

@endforeach