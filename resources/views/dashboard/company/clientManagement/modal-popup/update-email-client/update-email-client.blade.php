<div class="modal fade rt-modal" id="updateEmailClientModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Update Custom Email Client</h4>
            </div>
            {{ Form::open(['route' => 'clients.emails.update']) }}
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="id" name="id">
                    <div class="form-group col-md-6 ">
                        <label class="control-label" for="title">Email</label>
                        <input type="text" id="email" name="email" class="form-control" disabled="">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Full Name</label>
                        <input type="text" id="fullname" name="full_name" class="form-control">
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Company Name</label>
                        <input type="text" id="companyname" name="company_name" class="form-control" >
                    </div>
                    <div class="form-group col-md-6">
                        <label class="control-label" for="title">Company Address</label>
                        <input type="text" id="companyaddress" name="company_address" class="form-control" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
