@if($type == 1)
    <input type="hidden" class="filterType" value="{{$type}}">
    <div class="modal-body" style="    margin-top: 56px;">
        <div class="row">
            <div class="col-md-6">
                <strong>Company</strong>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label filtercompanys">Company</label>
                            <div style="position:relative">
                                <select id="filtercompany" class="form-control filtercompanys" name="company">
                                    <option value="">Select Company</option>
                                    <?php
                                    $wonCompany = \App\Company::where('id',Session::get('company_id'))->first();
                                    ?>
                                    <option value="{{ $wonCompany->id }}">{{ $wonCompany->name }} </option>
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
                    <input type="checkbox" class="docketPreviewCheckboxInput invoiceableFilter" value="1" name="invoiceable" id="invoiceable">
                </div>
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Employee</label>
                            <div style="position:relative">
                                <select id="filterempolyees" class="form-control filterempolyeess" name="empolyees">
                                    <option value="">Select Employee</option>
                                    @if($totalCompany)
                                        @foreach($totalCompany as $company)
                                            <option value="{{ $company->user_id }}" data-chained="{{ $company->id}}" >{{ @$company->userInfo->first_name}} {{ @$company->userInfo->last_name}} </option>
                                            @if($company->employees->count()>0)
                                                @foreach( $company->employees as $employee)
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
                                <select id="itemName" class="form-control selectDocketTemplate" name="docketTemplateId">
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
                            <input type="text" class="form-control"  name="docketId" id="itemId">
                        </div>
                    </div>

                    <div class="col-md-12 folderFilter" >
                        <br/>
                        <strong>Field Info</strong>
                        <div class="row docketFieldNameSelect">
                            <div class="col-md-12 text-center"  style="    height: 30px;">
                                <span style="color: #7b7b7b;">Please Select Docket Template</span>
                            </div>
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
                                        <select id="itemDateCat" class="form-control" name="date">
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
                                    <input type="text" class="form-control dateInput datepicker itemDateFrom " dateType="docketCreated"  name="from"   autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">To</label>
                                    <input type="text" class="form-control dateInput itemDateto" dateType="docketCreated" value="" name="to" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary submitData" >Filter</button>
    </div>

@elseif($type ==2)
    <input type="hidden"  class="filterType" value="{{$type}}">
    <div class="modal-body"  style="margin-top: 56px;">
        <div class="row">
            <div class="col-md-12">
                <strong class="pull-left">&nbsp;</strong>
                <div class="pull-right">
                    <strong>Filter for Invoicing</strong>&nbsp;
                    <input type="checkbox" class="docketPreviewCheckboxInput invoiceableFilter" value="1" name="invoiceable" >
                </div>
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Receiver Email</label>
                            <input type="text" class="form-control"  name="email" id="emailFilter" >
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
                                <select id="itemName" class="form-control selectDocketTemplate" name="docketTemplateId">
                                    <option value="">Select Docket Template</option>
                                    @if($docketusedbyemail)
                                        @foreach($docketusedbyemail as $row)
                                            <option value="{{ $row->docket_id }}">{{ $row->docketInfo->title }}</option>
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
                            <input type="text" class="form-control"  name="docketId" id="itemId">
                        </div>
                    </div>

                    <div class="col-md-12 folderFilter" >
                        <br/>
                        <strong>Field Info</strong>
                        <div class="row docketFieldNameSelect">
                            <div class="col-md-12 text-center"  style="    height: 30px;">
                                <span style="color: #7b7b7b;">Please Select Docket Template</span>
                            </div>
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
                                        <select id="itemDateCat" class="form-control" name="date">
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
                                    <input type="text" class="form-control dateInput datepicker itemDateFrom" dateType="docketCreated"  name="from"   autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">To</label>
                                    <input type="text" class="form-control dateInput itemDateto" dateType="docketCreated"  value="" name="to" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary submitData">Filter</button>
        </div>
    </div>
@elseif($type == 3)
    <input type="hidden"  class="filterType" value="{{$type}}">
    <div class="modal-body" style="    margin-top: 56px;">
        <div class="row">
            <div class="col-md-6">
                <strong>Company</strong>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Company</label>
                            <div style="position:relative">
                                <select  id="filtercompany" class="form-control filtercompanys" name="company">
                                    <option value="">Select Company</option>
                                    <?php
                                    $wonCompany = \App\Company::where('id',Session::get('company_id'))->first();
                                    ?>
                                    <option value="{{ $wonCompany->id }}">{{ $wonCompany->name }} </option>
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
                {{--<div class="pull-right">--}}
                {{--<strong>Filter for Invoicing</strong>&nbsp;--}}
                {{--<input type="checkbox" class="docketPreviewCheckboxInput" value="1" name="invoiceable" >--}}
                {{--</div>--}}
                <div class="clearfix"></div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Employee</label>
                            <div style="position:relative">
                                <select  id="filterempolyees" class="form-control filterempolyeess" name="empolyees">
                                    <option value="">Select Employee</option>
                                    @if($totalCompany)
                                        @foreach($totalCompany as $company)
                                            <option value="{{ $company->user_id }}" data-chained="{{ $company->id}}" >{{ $company->userInfo->first_name}} {{ $company->userInfo->last_name}} </option>
                                            @if($company->employees->count()>0)
                                                @foreach( $company->employees as $employee)
                                                    <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}" >{{ $employee->userInfo->first_name}} {{ $employee->userInfo->last_name}} </option>
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
                                <select id="itemName" class="form-control" name="invoiceTemplateId">
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
                            <input type="text" class="form-control"  name="invoiceId" id="itemId">
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
                                        <select id="itemDateCat" class="form-control" name="date">
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
                                    <input type="text" class="form-control dateInput datepicker itemDateFrom" dateType="docketCreated"  name="from"  autocomplete="off" >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">To</label>
                                    <input type="text" class="form-control dateInput itemDateto" dateType="docketCreated" id="toDatePicker" value="" name="to" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary submitData">Filter</button>
    </div>
@elseif($type == 4)

    <input type="hidden"  class="filterType" value="{{$type}}">
    <div class="modal-body"  style="margin-top: 56px;">
        <div class="row">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group" style="margin-top:0px;">
                            <label for="templateId" class="control-label">Receiver Email</label>
                            <input type="text" class="form-control"  name="email" id="emailFilter" >
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
                            <label for="docketId" class="control-label">Invoice</label>
                            <div style="position:relative">
                                <select id="itemName" class="form-control" name="docketTemplateId">
                                    <option value="">Select Invoice Template</option>
                                    @if($invoiceUsedByEmail)
                                        @foreach($invoiceUsedByEmail as $row)
                                            <option value="{{ $row->invoice_id }}">{{ $row->invoiceInfo->title }}</option>
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
                            <input type="text" class="form-control"  name="docketId" id="itemId">
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
                                        <select id="itemDateCat" class="form-control" name="date">
                                            {{--<option value="2" selected="selected">Inside docket date (User Selected date)</option>--}}
                                            <option value="1">Outside docket date (docket creation date)</option>
                                        </select>
                                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">From</label>
                                    <input type="text" class="form-control dateInput datepicker itemDateFrom" dateType="docketCreated"  name="from"  autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group" style="margin-top:0px;">
                                    <label for="templateId" class="control-label">To</label>
                                    <input type="text" class="form-control dateInput itemDateto" dateType="docketCreated" value="" name="to" autocomplete="off">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button class="btn btn-primary submitData">Filter</button>
        </div>
    </div>


@endif