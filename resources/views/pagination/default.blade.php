@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled"><span>&laquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl()}}" rel="prev">&laquo;</a></li>
{{-- <li><a href="{{ $paginator->previousPageUrl()."&filedid=".$data['field_id']."&grid_field_id=".$data['grid_field_id']."&type=".$data['type']."&isDependent=".$data['isDependent']."&isOpen=0"  }}" rel="prev">&laquo;</a></li>--}}
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span>{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>

{{--                        <li><a href="{{ $url."&filedid=".$data['field_id']."&grid_field_id=".$data['grid_field_id']."&type=".$data['type']."&isDependent=".$data['isDependent']."&isOpen=0" }}">{{ $page }}</a></li>--}}
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl()  }}" rel="next">&raquo;</a></li>
{{--            <li><a href="{{ $paginator->nextPageUrl() ."&filedid=".$data['field_id']."&grid_field_id=".$data['grid_field_id']."&type=".$data['type']."&isDependent=".$data['isDependent']."&isOpen=0" }}" rel="next">&raquo;</a></li>--}}

        @else
            <li class="disabled"><span>&raquo;</span></li>
        @endif
    </ul>
@endif