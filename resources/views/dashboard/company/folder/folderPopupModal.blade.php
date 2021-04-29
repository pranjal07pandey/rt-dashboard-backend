<div class="modal fade" id="moveFolderModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div style="display: none;position: absolute;right: 50%;top: 50%; z-index: 10000;" class="spinerSubDocket">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                <span class="sr-only">Loading...</span>
            </div>

            <input type="hidden" id="moveFolderModel" class="re-mover">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Move to Folder</h4>
            </div>

            <input value="1" name="type"  id="inputValue" type="hidden">
            <div class="modal-body">
                <div id="folderLabel">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" id="submitFolderItems" class="btn btn-primary" >Save changes</button>
            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="createNewFolder" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div style="display: none;position: absolute;right: 50%;top: 60%; z-index: 10000;" class="spinerSubDocket">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw" style="color: #777777;"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Create to Folder</h4>
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
                    <div id="folderCreateSelect">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button"  class="btn btn-primary submitFolderItem" >Save changes</button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="removeFolder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-md" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Remove Folder</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="removeFolderid" name="id">
                        <p class="deleteMessage"> <i class="fa fa-exclamation-circle"></i>  </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit"  id="submitDeleteFolder" class="btn btn-primary">Yes</button>
                <button  class="btn btn-primary"  data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>

@include('dashboard.company.folder-management.popup-modal.assign-template.assign-template')



<div class="modal fade" id="updateFolderData" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Update Folder</h4>
            </div>
            <div class="modal-body">

                <input type="hidden"  id="editIdFolder" name="id">
                <div class="col-md-12">
                    <div style="margin-top: 4px;" class="form-group">
                        <label class="control-label" for="title">Name</label>
                        <input type="text"  name="name" class="form-control" id="editNameFolder" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button"  class="btn btn-primary"  id="UpdateFolder" >Save changes</button>
            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="searchFolderModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> Search Folder</h4>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <div style="margin-top: 4px;" class="form-group">
                        <label class="control-label" for="title">Name</label>
                        <input type="text"  name="name" class="form-control" id="searchFolderName" >
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button"  class="btn btn-primary"  id="submitSearchFolderName" >Search</button>
            </div>

        </div>
    </div>
</div>


<div class="modal fade" id="folderLabeling" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i><span class="folderItemsNamefilter"></span> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboardFlashdanger" style="display: none;">
                            <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
                                <p class="messagedanger"></p>
                            </div>
                        </div>
                        <div style="margin-top: 4px;" class="form-group">
                            <label class="control-label" for="title">Docket Id</label>
                            <input type="text" id="itemsIdForCompanyId" name="company_sent_docket_id" class="form-control" style="margin-top: 11px;padding-left: 87px;" readonly>
                            <span class="itemIdForCompanyIdPrefix" style="position: absolute;top: 45px;left: -2px;background: #eee; color: #555;padding: 6px 12px;font-size: 14px;border-radius: 4px;font-weight: 400;text-align: center;"></span>
                            <input type="hidden" id="itemsIdForLabel" name="sent_docket_id" class="form-control">
                            <input type="hidden" id="itemsIdForLabelType" class="form-control" >
                        </div>
                        <div class="form-group label-floating">
                            <div class="col-md-9">
                                <div class="form-group" style="padding-bottom: 20px;margin: 27px 0px 0px -12px;">
                                    <label for="employeeId" class="control-label">Docket Label</label>

                                    <select id="folderFramework" class="form-control" multiple required name="docket_label_id[]">
                                        @if(@$sentDocketLabel)
                                        @foreach($sentDocketLabel as $row)
                                          <option value="{!! $row['id'] !!}">{!! $row['title'] !!}</option>
                                        @endforeach
                                        @endif
                                    </select>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="padding-bottom: 20px;margin: 28px 0 0 0;">
                                <button style=""  class="btn btn-primary submitFolderLabel">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="noFolderLabeling" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Assign Docket Label</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><i class="fa fa-exclamation-circle"></i> Please create a docket label before marking it.</p>
                    </div>

                </div>
                <div class="modal-footer">
                    <hr>
                    <a type="submit" href="{{ route('companyDocketLabel') }}" class="btn btn-primary">Ok</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="invoicefolderLabeling" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i><span class="folderItemsNamefilter"></span> </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboardFlashdanger" style="display: none;">
                            <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
                                <p class="messagedanger"></p>
                            </div>
                        </div>
                        <div style="margin-top: 4px;" class="form-group">
                            <label class="control-label" for="title">Invoice Id</label>
                            <input type="text" id="itemsInvoiceIdIdForCompanyId" name="company_sent_docket_id" class="form-control" readonly>
                            <input type="hidden" id="itemsInvoiceIdForLabel" name="sent_docket_id" class="form-control" >
                            <input type="hidden" id="itemsInvoiceIdForLabelType" class="form-control" >
                        </div>
                        <div class="form-group label-floating">
                            <div class="col-md-9">
                                <div class="form-group" style="padding-bottom: 20px;margin: 27px 0px 0px -12px;">
                                    <label for="employeeId" class="control-label">Invoice Label</label>
                                    <select id="folderInvoiceFramework" class="form-control" multiple required name="invoice_label_id[]">
                                        @if(@$sentInvoiceLabel)
                                            @foreach($sentInvoiceLabel as $row)
                                                <option value="{!! $row['id'] !!}">{!! $row['title'] !!}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="padding-bottom: 20px;margin: 28px 0 0 0;">
                                <button style=""  class="btn btn-primary submitInvoiceFolderLabel">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>

    </div>
</div>


<div class="modal fade" id="cancelRtItemsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        {{--<div id="model" data-target="#myModal"></div>--}}
        {{--{{ Form::open(['url' => 'dashboard/company/docketBookManager/docket/deleteEmailAssignLabel' ,'method'=>'DELETE', 'files' => true]) }}--}}
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;Cancel Docket </h4>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <input type="hidden" id="cancelid" name="id">
                        <input type="hidden" id="canceltype" name="type">
                        <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to Cancel this Docket?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="cancelItems"  class="btn btn-primary">Yes</button>
                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
        {{--{{ Form::close() }}--}}
    </div>
</div>


<div class="modal fade" id="noInvoiceFolderLabeling" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Assign Invoice Label</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p><i class="fa fa-exclamation-circle"></i> Please create a invoice label before marking it.</p>
                    </div>

                </div>
                <div class="modal-footer">
                    <hr>
                    <a type="submit" href="{{ route('companyInvoiceLabel') }}" class="btn btn-primary">Ok</a>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="deleteLabel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i> <span class="headerTextChange"></span></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="dashboardFlashdangerDelete" style="display: none;">
                            <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
                                <p class="messagedangerdelete"></p>
                            </div>
                        </div>
                        <input type="hidden" id="delete_label" name="id">
                        <input type="hidden" id="delete_label_type" name="id">
                        <p> <i class="fa fa-exclamation-circle"></i>  Are you sure you want to remove assigned label?</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="deleteAssignLabels">Yes</button>
                <button  class="btn btn-primary" data-dismiss="modal" aria-label="Close">No</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade " id="MyModalFolderFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="    min-height: 392px;">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp; Folder Advanced Filter</h4>
            </div>
            {{--{{ Form::open(['url' => 'dashboard/company/folderContentFilter/']) }}--}}

            <div class="col-md-12">
                <select id="folderSelect" class="form-control">
                    <option value="1" selected>Dockets</option>
                    <option value="2">Email Dockets</option>
                    <option value="3">Invoices</option>
                    <option value="4">Email Invoices</option>

                </select>
            </div><br>

            <div id="folderContentFilter">

            </div>
        </div>
    </div>
</div>






