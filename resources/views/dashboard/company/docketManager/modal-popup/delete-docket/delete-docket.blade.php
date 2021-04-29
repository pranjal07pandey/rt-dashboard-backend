<div class="modal fade rt-modal" id="deleteSentDocket" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Delete Docket</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('dashboard.company.docketManager.modal-popup.flash-message')
                        <input type="hidden" id="deleteDocketIds">
                        <input type="hidden" id="deleteDocketTypes" >
                        <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to delete this docket? Deleted dockets will be listed under the “Trash (System)” folder and you have a maximum of 30 days to recover.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary submit">Yes</button>
                <button class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div><!--/#deleteDocketModal-->
