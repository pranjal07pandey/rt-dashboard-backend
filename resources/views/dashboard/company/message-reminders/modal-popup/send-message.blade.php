<div class="modal fade rt-modal" id="newMessageModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div id="second"  class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Send Message</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('dashboard.company.docketManager.modal-popup.flash-message')
                        <div class="form-group" style="padding-bottom: 20px;margin: 0px 0 0 0;">
                            <label for="employeeId" class="control-label">Member</label>
                            <select  class="form-control "  required name="employeeId" id="chatUserId" >
                                <option value="{!! $company->user_id !!}">{{ $company->userInfo->first_name }} {{ $company->userInfo->last_name }}</option>
                                @if($company->employees)
                                    @foreach($company->employees as $row)
                                        @if(@$row->userInfo->isActive==1)
                                            <option value="{!! $row->userInfo->id !!}">{!! $row->userInfo->first_name." ".$row->userInfo->last_name !!}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <label for="messages" class="control-label">Message</label>
                        <div class="form-group">
                            <input type="text" name="message" class="form-control" id="singleMessages" >
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button  class="btn btn-primary submit"  id="submitSingleChat" >Send</button>
            </div>
        </div>
    </div>
</div>