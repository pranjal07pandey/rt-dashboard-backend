<div class="tableHeaderMenu rtDataTableHeaderMenu" dataCurrentURL="{{ url('dashboard/company/folder/searchFolderItems') }}">
    <ul>
        <li><button class="rtMenuBtn" id="exportcsv">Export .csv</button></li>
            <li><button class="rtMenuBtn" id="exportpdf">Export Pdf's</button></li>
        @if($trashFolder == false)
        <li><button class="rtMenuBtn" id="moveFolder" type="2">Move</button></li>
        <li><button class="rtMenuBtn" id="removeItemsFolder">Remove</button></li>
        @endif
        <li>
            Show&nbsp;&nbsp;
            <select aria-controls="datatable" class="selectPaginateFolder"  name="items">
                <option value="10"  @if($items==10) selected @endif>10</option>
                <option value="50" @if($items==50) selected @endif>50</option>
                <option value="100" @if($items==100) selected @endif>100</option>
                <option value="500" @if($items==500) selected @endif>500</option>
            </select>&nbsp;&nbsp;entries
        </li>
        <li class="pull-right" style="position: absolute;right: 7px;">
            Search <input type="search" class="rtMenuSearch" id="searchFolderInputs" placeholder="" @if(@$searchKey) value="{{ $searchKey }}" @endif>
        </li>
    </ul>
</div>
