<div class="modal fade rt-modal" id="unassignFolderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Unassign Folder</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="unassignTempalteErrorMessage" style="display: none;     color: white;background: red;padding: 0 0 0px 11px;font-size: 15px;">   </p>
                        <input type="hidden" id="unassignFolderId">
                        <input type="hidden" id="unassignTemplateId">
                        <input type="hidden" id="unassignTemplateName">
                        <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to unassign Folder from this template?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitUnAssignFolder" class="btn btn-primary submit">Yes</button>
                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>