<div class="modal fade rt-modal" id="addEmailClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Custom Email Client</h4>
            </div>
            {{ Form::open(['route' => 'clients.emails.store', 'method'=>'post']) }}
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6 ">
                        <label class="control-label" for="title">Email <b style="color: red;">*</b></label>
                        <input type="text" id="title" name="email" class="form-control" required >
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Full Name <b style="color: red;">*</b></label>
                        <input type="text" id="title" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Company Name</label>
                        <input type="text" id="title" name="company_name" class="form-control" >
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Company Address</label>
                        <input type="text" id="title" name="company_address" class="form-control" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit">Save</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>