@foreach($sentDocketLabels as $sentDocketLabel)
    @if($sentDocketLabel->docketLabel->company_id==Auth::user()->company()->id)
        <li style="background: {{$sentDocketLabel->docketLabel->color}}" class="docket-label-{{$sentDocketLabel->id}}">
            @if($sentDocketLabel->docketLabel->icon)
                <img src="{{ AmazoneBucket::url() }}{{ $sentDocketLabel->docketLabel->icon }}" height="10" width="10">
            @endif
            {{ $sentDocketLabel->docketLabel->title }}
            <button  data-toggle="modal" data-target="#deleteDocketLabelModal" data-type="{{$type}}"  data-id="{{$sentDocketLabel->id}}"  class="btn btn-raised btn-xs">
                <span  class="glyphicon glyphicon-remove" aria-hidden="true"/>
            </button>
        </li>
    @endif
@endforeach