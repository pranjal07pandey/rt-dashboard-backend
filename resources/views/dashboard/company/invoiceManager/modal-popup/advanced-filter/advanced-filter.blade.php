<div class="modal fade " id="myModalFilter" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-plus"></i>&nbsp;&nbsp;Advanced Filter</h4>
            </div>

            {{ Form::open(['url' => 'dashboard/company/invoiceManager/filterInvoice/',]) }}
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
                        <div class="clearfix"></div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">Employee</label>
                                    <div style="position:relative">
                                        <select id="empolyees" class="form-control" name="empolyees">
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
                        <strong>Invoice Info</strong>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="docketId" class="control-label">Invoices</label>
                                    <div style="position:relative">
                                        <select id="invoiceId" class="form-control" name="invoiceTemplateId">
                                            <option value="">Select Invoice Template</option>
                                            @if($invoices)
                                                @foreach($invoices as $row)
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
                                    <label for="templateId" class="control-label">Invoice Id</label>
                                    <input type="text" class="form-control"  name="invoiceId">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <br/>
                                <strong>By Date</strong>
                                <div class="row">
                                    <div class="col-md-6" >
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">Date Type</label>
                                            <div style="position:relative">
                                                <select id="company" class="form-control" name="date">
                                                    {{--<option value="2" selected="selected">Inside docket date (User Selected date)</option>--}}
                                                    <option value="1">Outside Invoiced date (Invoice creation date)</option>
                                                </select>
                                                <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">From</label>
                                            <input type="text" class="form-control dateInput datepicker" dateType="docketCreated"  name="from"  id="fromDatePicker" >
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="margin-top:0px;">
                                            <label for="templateId" class="control-label">To</label>
                                            <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker" value="" name="to">
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