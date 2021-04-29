<div class="modal fade rt-modal" id="assignFolderModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Assign Folder</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p class="assignTempalteErrorMessage" style="display: none;     color: white;background: red;padding: 0 0 0px 11px;font-size: 15px;">   </p>
                        <p style="    margin-left: 14px;font-size: 15px;font-weight: 600;">Template Name: <span class="assignFolderName" style="font-weight: 100;"></span> </p>
                        <input type="hidden" id="templateId">
                        <div class="col-md-12">
                            <strong>Folder</strong>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" style="margin-top:0px;">
                                        <div style="position:relative">
                                            <select id="assignFolderId" class="form-control" name="type">
                                                <option value="">Select Folder</option>
                                                @if(@$data)
                                                    @foreach ($data as $datas)
                                                        <option value="{{$datas['id']}}">{!! $datas['space'] !!}{{$datas['name'][0]}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit">Save</button>
            </div>
        </div>
    </div>
</div>
