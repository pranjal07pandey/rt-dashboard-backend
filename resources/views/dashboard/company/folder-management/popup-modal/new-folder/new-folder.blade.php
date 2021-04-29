<div class="modal fade rt-modal" id="createNewFolder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div style="display: none;position: absolute;right: 50%;top: 60%; z-index: 10000;" class="spinerSubDocket">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Folder</h4>
            </div>
            <div class="modal-body" style="min-height: 204px;">
                <div class="dashboardFlashsuccess" style="display: none;">
                    <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;">
                        <p class="messagesucess"></p>
                    </div>
                </div>

                <input type="hidden"  id="rootId">
                <div class="col-md-12">
                    <div style="margin-top: 4px;" class="form-group">
                        <label class="control-label" for="title">Name</label>
                        <input type="text"  name="name" class="form-control " id="folderNewName">
                    </div>
                </div>

                <div class="col-md-12">
                    <div id="folderCreateSelect"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button"  class="btn btn-primary submit" >Save changes</button>
            </div>
        </div>
    </div>
</div>
