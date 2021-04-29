@if(count(@$result)==0)
    <div class="rtTabHeader">
        <ul>
            <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;"> {{$folder->name}}</h4></li>
            @if($folder->type == 1)
                <a tabindex="0" style="  position: absolute;  padding: 7px 7px 7px 7px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Trash (System)" data-content="Deleted dockets will be visible in this folder. It works like a trash/bin on your computer. You have 30 days to recover your deleted dockets. If not, it will be deleted permanently"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

            @endif
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
            <input type="hidden" value="{{$folder->id}}" id="removeItemFolderId">
            <li><h4 style="    margin: 17px 0px 17px 17px;font-size: 17px;font-weight: 500;color: #3a3a3a;"><img src="{{asset('assets/folder/icons/folder.png')}}" style="width: 17px;"> {{$folder->name}}</h4></li>
            @if($folder->id==398)
                <li class="pull-right">
                    <button class="rtMenuBtn" id="export-daycrs-mu2" style="margin-top: 15px;margin-right: 15px;">Export DAYCRS - Work Docket - MU2</button>
                </li>
            @endif
            @if($folder->id==268)
                <!--only for DAYCRS-->
                <li class="pull-right">
                    <button class="rtMenuBtn" id="export-daycrs-work-docket" style="margin-top: 15px;margin-right: 15px;">Export DAYCRS - Work Docket</button>
                </li>
            @endif
            @if($trashFolder == false)
            <li class="advacedFilter"><a href = "#close" class='forum-title'  id="MyModalFolderFilters"><i class="material-icons">filter_list</i> Advanced Filter</a></li>
           @endif
        </ul>
    </div>
    <div class="rtTabContent ">
        @include('dashboard.company.folder-management.partials.table-view.table-header.table-header-menu')


        <table class="rtDataTable datatable searchViewItems" >
            <thead>
                <tr>
                    <th>
                        <label>
                            <input type="checkbox" class="checkbox " value="1"  name="employed[]" >
                            <span class="checkbox-material"><span class="check"></span></span>
                        </label>
                    </th>
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
                    @php $docketCheckbox = true @endphp
                    @php $invoiceCheckbox = true @endphp
                    @php $shareableFolder = true  @endphp

                    @php $checktrashFolder = $trashFolder @endphp
                    @foreach($result->sortByDesc('created_at') as $row)
                        @if($row instanceof App\SentDockets)
                            @include('dashboard.company.docketManager.partials.table-view.sent-docket-row')
                        @endif
                        @if($row instanceof App\EmailSentDocket)
                            @include('dashboard.company.docketManager.partials.table-view.email-sent-docket-row')
                        @endif
                        @if($row instanceof App\SentInvoice)
                            @include('dashboard.company.invoiceManager.partials.table-view.sent-invoice-row')
                        @endif
                        @if($row instanceof App\EmailSentInvoice)
                            @include('dashboard.company.invoiceManager.partials.table-view.email-sent-invoice-row')
                        @endif
                    @endforeach
                @endif
            </tbody>
            <tfoot>

            @if($type == 'reload')
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
            </tfoot>
        </table>




    </div>



    @if($folder->id==268)
        <script>
            $(document).ready(function(){
                $(document).on('click','#export-daycrs-work-docket',function () {
                    if ($('.selectitem:checked').serialize()==""){
                        alert("Please Select Docket");
                    }else {
                        window.open("{{ url('/') }}/dashboard/company/docketBookManager/docket/exportDaycrsDocket" +"?"+$('.selectitem:checked').serialize() ,"_blank");
                    }
                });
            });
        </script>
    @endif
    @if($folder->id==398)
        <script>
            $(document).ready(function(){
                $(document).on('click','#export-daycrs-mu2',function () {
                    if ($('.selectitem:checked').serialize()==""){
                        alert("Please Select Docket");
                    }else {
                        window.open("{{ url('/') }}/dashboard/company/docketBookManager/docket/exportDaycrsMU2" +"?"+$('.selectitem:checked').serialize() ,"_blank");
                    }
                });
            });
        </script>
    @endif
@endif
<script>
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'top',
            trigger : 'hover'
        });
    });
</script>

<style>
    .popover-title{
        background: #2570ba;
        color: #ffffff;
    }
    .popover-content{
        color: #000000;
    }
    .popover.top {
        margin-top: -3px;
    }
</style>



