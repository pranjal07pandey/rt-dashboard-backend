<div class="modal fade rt-modal" id="newGroupModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">New Group</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        @include('dashboard.company.docketManager.modal-popup.flash-message')

                        <div class="form-group" style="margin-top:0px;">
                            <label class="control-label" for="subject">Title</label><br/>
                            <input name="title" class="form-control" required id="groupChatTitle">
                        </div>
                        <div class="form-group" style="padding-bottom: 20px;margin: 0px 0 0 0;">
                            <label for="employeeId" class="control-label">Members</label>
                            <select id="multipleEmployeeIdMessage" class="form-control slim-select" multiple required name="employeeId[]" >
                                @if($company->userInfo->id != Auth::user()->id)
                                <option value="{!! $company->user_id !!}">{{ $company->userInfo->first_name }} {{ $company->userInfo->last_name }}</option>
                                @endif
                                @if($company->employees)
                                    @foreach($company->employees as $row)
                                        @if(@$row->userInfo->isActive==1 && $row->userInfo->id!=Auth::user()->id)
                                            <option value="{!! $row->userInfo->id !!}">{!! $row->userInfo->first_name." ".$row->userInfo->last_name !!}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit">Create</button>
            </div>
        </div>
    </div>
</div>