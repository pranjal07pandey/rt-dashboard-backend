@foreach($finalPrefillerView as $data)
    {!! $data['final'] !!}
@endforeach
<input type="hidden" class="gridPrefillerPaginate" value="{{$currentPage}}">