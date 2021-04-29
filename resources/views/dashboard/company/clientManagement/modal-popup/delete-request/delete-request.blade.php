<div class="modal fade rt-modal" id="deleteRequestModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('clients.destroy') }}">
            {{ csrf_field() }}
            {{ method_field('DELETE') }}
            <div class="modal-content">
                <div class="modal-header themeSecondaryBg">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Delete Client</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" id="clientid" name="id">
                            <p> <i class="fa fa-exclamation-circle"></i> Are you sure you want to remove this client?</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary submit">Yes</button>
                    <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
                </div>
            </div>
        </form>
    </div>
</div>
