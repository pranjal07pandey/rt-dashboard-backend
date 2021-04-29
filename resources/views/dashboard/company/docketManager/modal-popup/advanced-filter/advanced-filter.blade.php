<div class="modal fade rt-modal" id="myModalFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Advanced Filter</h4>
            </div>

            {{ Form::open(['route' => 'dockets.advancedFilter']) }}
            <input type="hidden" name="type" value="{{$filterType}}">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Company</strong>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">Company</label>
                                    <div style="position:relative">
                                        <select id="company" class="form-control" name="company">
                                            <option value="">Select Company</option>
                                            <option value="{{ $company->id }}">{{ $company->name }} </option>
                                            @if($clients)
                                                @foreach($clients as $row)
                                                    <?php
                                                    if($row->company_id==Session::get('company_id'))
                                                        $companyDetails   =     $row->requestedCompanyInfo;
                                                    else
                                                        $companyDetails   =     $row->companyInfo;
                                                    ?>
                                                    <option value="{{ $companyDetails->id }}">{{ $companyDetails->name }} </option>
                                                @endforeach
                                            @endif

                                        </select>
                                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <strong class="pull-left">&nbsp;</strong>
                        <div class="pull-right">
                            <strong>Filter for Invoicing</strong>&nbsp;
                            <input type="checkbox" class="docketPreviewCheckboxInput" value="1" name="invoiceable" >
                        </div>
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">Employee</label>
                                    <div style="position:relative">
                                        <select id="employee" class="form-control" name="employee">
                                            <option value="">Select Employee</option>
                                            <option value="{{ $company->user_id }}" data-chained="{{ $company->id}}" >{{ @$company->userInfo->first_name}} {{ @$company->userInfo->last_name}} </option>
                                            @if($company->employees->count()>0)
                                                @foreach( $company->employees as $employee)
                                                    @if(@$employee->userInfo->first_name!="")
                                                    <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}" >{{ @$employee->userInfo->first_name}} {{ @$employee->userInfo->last_name}} </option>
                                                    @endif
                                                @endforeach
                                            @endif

                                            @if($clients)
                                                @foreach($clients as $row)
                                                    @php  if($row->company_id==Session::get('company_id')){ $companyDetails   =     $row->requestedCompanyInfo; }
                                                    else{ $companyDetails   =     $row->companyInfo; } @endphp
                                                    <option value="{{ $companyDetails->user_id }}" data-chained="{{ $companyDetails->id}}" >{{ @$companyDetails->userInfo->first_name}} {{ @$companyDetails->userInfo->last_name}} </option>
                                                    @if($companyDetails->employees->count()>0)
                                                        @foreach( $companyDetails->employees as $employee)
                                                            <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}" >{{ @$employee->userInfo->first_name}} {{ @$employee->userInfo->last_name}} </option>
                                                        @endforeach
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <br/>
                        <strong>Docket Info</strong>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="docketId" class="control-label">Dockets</label>
                                    <div style="position:relative">
                                        <select id="docketId " class="form-control selectDocketTemplate" name="docketTemplateId">
                                            <option value="">Select Docket Template</option>
                                            @if($dockets)
                                                @foreach($dockets as $row)
                                                    <option value="{{ $row->id }}">{{ $row->title }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">Docket Id</label>
                                    <input type="text" class="form-control"  name="docketId">
                                </div>
                            </div>

                            <div class="col-md-12" >
                                <br/>
                                <strong>Field Info</strong>
                                <div class="row docketFieldNameSelect">
                                    <div class="col-md-12 text-center"  style="    height: 30px;">
                                           <span style="color: #7b7b7b;">Please Select Docket Template</span>
                                    </div>
                                </div>
                            </div>

{{--                            <div class="col-md-6">--}}
{{--                                <div class="form-group" style="margin-top:0px;">--}}
{{--                                    <label for="templateId" class="control-label">Docket Field Value</label>--}}
{{--                                    <input type="text" class="form-control"  name="docketFieldValue">--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="col-md-12">
                                <br/>
                                <strong>By Date</strong>
                                <div class="row">
                                    <div class="col-md-6" >
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">Date Type</label>
                                            <div style="position:relative">
                                                <select id="company" class="form-control" name="date">
                                                    <option value="2" selected="selected">Inside docket date (User Selected date)</option>
                                                    <option value="1">Outside docket date (docket creation date)</option>
                                                </select>
                                                <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">From</label>
                                            <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from"  id="fromDatePicker" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">To</label>
                                            <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" value="" name="to" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
