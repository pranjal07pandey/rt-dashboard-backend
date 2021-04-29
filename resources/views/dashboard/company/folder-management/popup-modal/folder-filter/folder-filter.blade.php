<div class="modal fade" id="MyModalFolderFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="    min-height: 392px;">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp; Folder Advanced Filter</h4>
            </div>
            <div class="col-md-12">
                <select id="folderSelect" class="form-control">
                    <option value="1" selected>Dockets</option>
                    <option value="2">Email Dockets</option>
                    <option value="3">Invoices</option>
                    <option value="4">Email Invoices</option>
                </select>
            </div><br>
            <span class="spinnerCheck" style="font-size: 41px;position: absolute;display: none;z-index: 1;left: 50%;top: 50%;"><i class="fa fa-spinner fa-spin"></i></span>

            <div id="folderContentFilter"></div>
        </div>
    </div>
</div>
