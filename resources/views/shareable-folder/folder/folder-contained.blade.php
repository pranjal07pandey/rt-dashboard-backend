
@if($isValidData == false)
    <div class="rtTabHeader">

    </div>

    <div class="rtTabContent" >
        <div class="tableHeaderMenu" style="    min-height: 476px;">
            <span style="    text-align: center;display: block;font-size: 12px;padding-top: 100px;color: #ababab;">
                <i style="    font-size: 47px;" class="fa fa-folder-open-o" aria-hidden="true"></i>
                <br>
                Invalid Folder Id</span>
        </div>
    </div>
@else

    @if(count(@$result)==0)
        <div class="rtTabHeader">
            <ul>
                <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
            </ul>
        </div>

        <div class="rtTabContent" >
            <div class="tableHeaderMenu" style="    min-height: 476px;">
            <span style="    text-align: center;display: block;font-size: 12px;padding-top: 100px;color: #ababab;">
                <i style="    font-size: 47px;" class="fa fa-folder-open-o" aria-hidden="true"></i>
                <br>
                Folder Empty</span>
            </div>
        </div>

    @else
        <div class="rtTabHeader">
            <ul>
                <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;">  {{$companyFolder[0]->name}}</h4></li>
            </ul>
        </div>
        <div class="rtTabContent">
            <table class="rtDataTable datatable" >
                <thead>
                <tr>
                    <th>Docket Id</th>
                    <th>Info</th>
                    <th>Docket Name</th>
                    <th>Date Added</th>
                    <th>Status</th>
                    <th width="200px">Action</th>
                </tr>
                </thead>


                <tbody  id="folderAdvanceFilterView">
                @if(@$result)
                    @php $docketCheckbox = false @endphp
                    @php $invoiceCheckbox = false @endphp
                    @php $shareableFolder = true  @endphp


                    @foreach($result->sortByDesc('created_at') as $row)
                        @if($row instanceof App\SentDockets)
                            @include('shareable-folder.folder.partials.table-view.sent-docket-row')
                        @endif
                        @if($row instanceof App\EmailSentDocket)
                            @include('shareable-folder.folder.partials.table-view.email-sent-docket-row')
                        @endif
                        @if($row instanceof App\SentInvoice)
                            @include('shareable-folder.folder.partials.table-view.sent-invoice-row')
                        @endif
                        @if($row instanceof App\EmailSentInvoice)
                            @include('shareable-folder.folder.partials.table-view.email-sent-invoice-row')
                        @endif
                    @endforeach
                @endif
                </tbody>

                @if($isreload == 'reload')
                    <tr id="folderAdvanceFilterFooterView">
                        <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
                        <td colspan="5" class="text-right">
                            @if(@$searchKey) <div id="reloadFolderPagination">  {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
                            @else <div id="reloadFolderPagination"> {{ $result->appends(['items'=>$items])->links() }}</div>@endif
                        </td>
                    </tr>
                @else
                    <tr id="folderAdvanceFilterFooterView">
                        <td colspan="3"><span>Showing  {{ $result->firstItem() }} to {{ $result->lastItem() }} of {{ $result->total() }} entries</span></td>
                        <td colspan="5" class="text-right">
                            @if(@$searchKey) <div id="folderPagination">  {{ $result->appends(['search'=>$searchKey,'items'=>$items])->links() }}</div>
                            @else <div id="folderPagination"> {{ $result->appends(['items'=>$items])->links() }}</div>@endif
                        </td>
                    </tr>
                @endif
            </table>
        </div>

    @endif

@endif

