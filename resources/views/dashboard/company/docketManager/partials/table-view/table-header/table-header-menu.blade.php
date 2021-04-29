<div class="tableHeaderMenu rtDataTableHeaderMenu" dataCurrentURL=" {{ url()->current() }}">
    <ul>
        @if($docketDraft != true)
        <li><button class="rtMenuBtn" id="exportcsv">Export .csv</button></li>
        @if(Session::get('company_id')==1)
            <li><button class="rtMenuBtn" id="exportpdf">Export Pdf's</button></li>
        @endif
        <li><button class="rtMenuBtn" id="moveFolder" type="1">Move</button></li>
        @endif
        <li>
            Show&nbsp;&nbsp;
            <select aria-controls="datatable" class="selectPaginate"  name="items">
                <option value="10"  @if($items==10) selected @endif>10</option>
                <option value="50" @if($items==50) selected @endif>50</option>
                <option value="100" @if($items==100) selected @endif>100</option>
                <option value="500" @if($items==500) selected @endif>500</option>
            </select>&nbsp;&nbsp;entries
        </li>
        <li class="pull-right" style="position: absolute;right: 7px;">
            Search
            <input type="search" class="rtMenuSearch" id="searchInput" placeholder="" @if(@$searchKey) value="{{ @$searchKey }}" @endif>
        </li>
    </ul>
</div>