<div class="modal fade rt-modal" id="moveFolderItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <input type="hidden" id="moveFolderModel" class="re-mover">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Move to Folder</h4>
            </div>

            <input value="1" name="type"  id="inputValue" type="hidden">
            <div class="modal-body">
                <div id="folderLabel"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submitFolderItems" class="btn btn-primary submit" >Move</button>
            </div>
        </div>
    </div>
</div>