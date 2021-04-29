<div class="sidebarBox">
    <div class="boxHeader">
        <strong class="pull-left">Folders </strong>
        <input type="hidden" class="companyIdData" value="{{Session::get('company_id')}}">
        <a tabindex="0" style="  position: absolute;  padding: 0px 8px 5px 8px;    margin: 0 0 0 0;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Folders" data-content="Folders can help you organise dockets, just like folders on your computer. You can assign docket templates to a folder, so all dockets created using that template will file automatically. You can also manually move dockets to any folder you like.
       &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       Hover on the folder icon below and click on the three dots to the far right to edit, delete or assign templates."><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

        <div class="pull-right">
            <a href="#"  data-toggle="modal" data-target="#searchFolderModal"><i class="material-icons">search</i></a>
            <a href="#" id="newFolder"><i class="material-icons">create_new_folder</i></a>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="boxContent">
        <ul class="rtTree">
            @if($companyFolder)
                @foreach($companyFolder as $rowItems)


                    @if($rowItems->type == 0)
                        <li>
                            <a href="#"  id="{{$rowItems->id}}" >
                                {{$rowItems->name}}&nbsp;&nbsp;
                                <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                            </a>
                            <ul style="display:none;">
                                <li></li>
                            </ul>
                            <div  class="editBtn" id="editBtnId"  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                        </li>


                    @endif

                @endforeach
                @foreach($companyFolder as $rowItems)
                    @if($rowItems->type == 1)
                        <li>
                            <a href="#"  id="{{$rowItems->id}}" >
                                {{$rowItems->name}}&nbsp;&nbsp;
                                <span style="position: absolute;right: 4px;">@if(count($rowItems->folderItems)!=0)({{count($rowItems->folderItems)}}) @endif</span>
                            </a>
                            <ul style="display:none;">
                                <li></li>
                            </ul>
                            <div  data-id="{{$rowItems->id}}" data-title="{{$rowItems->name}}" style="position: absolute;    top: 1px;right: 1px;border-radius: 15px;height: 30px;    width: 31px; cursor: pointer;"></div>
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
        @if(!$companyFolder)
            <div class="directoryEmpty"><small>Press <i class="material-icons">create_new_folder</i> to add new folder</small></div>
        @endif
        <div class="directoryEmpty" style="display: none;"><small>Press <i class="material-icons">create_new_folder</i> to add new folder</small></div>
    </div>
</div>
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
    .btn:not(.btn-raised):not(.btn-link):hover{
        background-color: rgb(153 153 153 / 0%);
    }
    .shareableFolderUsers ul li:hover{
        background: #F9F9F9;
        border-radius: 3px;
        /*border: 1px solid #eaeaea;*/
        cursor: pointer;
        box-shadow: 0px 0px 2px 0px #888888;
        transition: 0.4s;
    }
    .shareableFolderUsers ul li{
        color: #959595;
    }

    .sharefolderSelect{
        height: 37px;
        padding: 0px 20px 0px 5px;
        border: 2px solid #CED4DA;
        background: #F7F7F7;
        border-top: none;
        border-left: none;
        border-right: none;
    }
    /*.shareableFolderUsers{*/
        /*margin: 35px 0px 35px 0px;*/
    /*}*/
    .shareableFolderUsers ul{
        padding: 30px 0px 0px 1px;
        white-space: nowrap;
        overflow-y: auto;
        overflow-x: auto;
        width: 100%;
        height: 90px;
    }

    .shareableFolderUsers ul li{
        display: inline;
        padding: 11px 11px 11px 11px;
        margin-right: 15px;
    }
    .approvalShareableLink{
        margin-top: 40px;
    }

    .approvalShareableLink .link{
        padding: 0;
    }
    .shareableFolderUsers ul li span{
        background: #E8E8E8;
        padding: 7px 10px 7px 10px;
        border-radius: 66px;
        font-weight: 500;
    }
    .shareableFolderUsers ul li:hover a{
        display: inline-block;
        opacity: 0.6;
        transition: 0.6s;
    }

    .shareableFolderUsers ul li a{
        display: none;
    }
    .errorMessageShareable{
        padding: 6px 6px 6px 6px;
        display: none;
    }

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>


@include('dashboard.company.docketManager.modal-popup.docket-label.docket-label')
@include('dashboard.company.docketManager.modal-popup.docket-label.delete-docket-label')
@include('dashboard.company.docketManager.modal-popup.cancel-docket.cancel-docket')
@include('dashboard.company.invoiceManager.modal-popup.invoice-label.invoice-label')
@include('dashboard.company.invoiceManager.modal-popup.invoice-label.delete-invoice-label')

@include('dashboard.company.folder-management.popup-modal.search-folder.search-folder')
@include('dashboard.company.folder-management.popup-modal.new-folder.new-folder')

@include('dashboard.company.folder-management.popup-modal.edit-folder.edit-folder')
@include('dashboard.company.folder-management.popup-modal.remove-folder.remove-folder')
@include('dashboard.company.folder-management.popup-modal.assign-template.assign-template')

@include('dashboard.company.folder-management.popup-modal.move-folder-item.move-folder-item')
@include('dashboard.company.folder-management.popup-modal.folder-filter.folder-filter')
@include('dashboard.company.folder-management.popup-modal.recover-folder-item.recover-folder-item')
@include('dashboard.company.docketManager.modal-popup.delete-docket.delete-docket')
@include('dashboard.company.folder-management.popup-modal.shareable-folder.shareable-folder')

