
<div class="row" id="sortable">
    @if($tempDocketFields)
        @foreach($tempDocketFields as $item)
            @include('dashboard.company.docketManager.elementTemplate')
        @endforeach
    @endif
</div>
<div class="row" id="sortableFooter">
    @if($tempDocketFields)
        @foreach($tempDocketFields as $item)
            @include('dashboard.company.docketManager.footerElementTemplate')
        @endforeach
    @endif
</div>
<div id="elementTemplateBottom"></div>

