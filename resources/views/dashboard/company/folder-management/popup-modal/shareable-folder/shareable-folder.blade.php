<div class="modal fade rt-modal" id="shareableFolderModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="width: 70%;">
        <div class="modal-content" >
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Share Folder</h4>
            </div>
            <input type="hidden" class="shareableFolderId">
            <div class="modal-body">
               <div class="shareableContain">

               </div>
            </div>
            <span class="loadspin" style="font-size: 41px;position: absolute; display:none; z-index: 1;left: 50%;top: 50%;"><i class="fa fa-spinner fa-spin"></i></span>

            <div class="modal-footer"  style="min-height: 400px;">
                {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                {{--<button type="button"  class="btn btn-primary submit"  id="submitSearchFolderName" >Search</button>--}}
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteShareableUsersModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-md" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content" style="margin-top: 25%;">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Delete User</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" class="shareableUserId">
                        <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to delete this user?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary deleteShareableUser" >Yes</button>
                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editShareableUserModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-md" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content" style="margin-top: 20%;">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Update Password</h4>
            </div>
            <input type="hidden" class="shareableUserId">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="errorMessageShareableUser label-danger" > </p>
                        <input type="hidden" class="updateShareableUserId">
                        <div style="margin-top: 4px;" class="form-group">
                            <label class="control-label" for="title">Email</label>
                            <input type="email"  name="name" class="form-control editshareableEmail" disabled>
                        </div>

                        <div style="margin-top: 4px;" class="form-group">
                            <label class="control-label" for="title">New Password</label>
                            <input type="password"  name="name" class="form-control editshareablePassword">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary updateShareableUser" >Yes</button>
                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>