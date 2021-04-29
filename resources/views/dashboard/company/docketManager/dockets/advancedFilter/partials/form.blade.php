{{ Form::open(['route' => 'dockets.advancedFilter']) }}
    <input type="hidden" name="type" value="{{ $request->type }}">
    <strong style="border-bottom: 1px solid #ddd;display: block;margin-bottom: 10px;padding-bottom: 5px;">Advanced Filter </strong>
    <button type="submit" class="btn btn-info btn-xs btn-raised" style="position: absolute;top: 16px;right: 15px;margin: 0px;">Filter</button>

    <div  style="border-bottom: 1px solid #ddd;    background-color: #f6f6f6;padding: 15px;">
        <div class="row">
            <div class="col-md-6">
                <strong>Company</strong>
                <div class="form-group" style="margin-top:0px;">
                    <label for="templateId" class="control-label">Company</label>
                    <div style="position:relative">
                        <select id="company" class="form-control" name="company">
                            <option value="">Select Company</option>
                            <option value="{{ $company->id }}"  @if($request->company==$company->id) selected @endif>{{ $company->name }} </option>
                            @if($clients)
                                @foreach($clients as $row)
                                    <?php
                                        if($row->company_id==Session::get('company_id'))
                                            $companyDetails   =     $row->requestedCompanyInfo;
                                        else
                                            $companyDetails   =     $row->companyInfo;
                                    ?>
                                    <option value="{{ $companyDetails->id }}" @if($request->company==$companyDetails->id) selected @endif>{{ $companyDetails->name }} </option>
                                @endforeach
                            @endif
                        </select>
                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <strong class="pull-left">&nbsp;</strong>
                <div class="pull-right">
                    <strong>Filter for Invoicing</strong>&nbsp;
                    <input type="checkbox" class="docketPreviewCheckboxInput" value="1" name="invoiceable" @if($request->invoiceable==1) checked @endif>
                </div>
                <div class="clearfix"></div>

                <div class="form-group" style="margin-top:0px;">
                    <label for="templateId" class="control-label">Employee</label>
                    <div style="position: relative">
                        <select id="employee" class="form-control" name="employee">
                        <option value="">Select Employee</option>
                        <option value="{{ $company->user_id }}" data-chained="{{ $company->id}}"  @if($request->employee==$company->user_id) selected @endif>{{ @$company->userInfo->first_name}} {{ @$company->userInfo->last_name}} </option>
                        @if($company->employees->count()>0)
                            @foreach( $company->employees as $employee)
                                @if(@$employee->userInfo->first_name!="")
                                    <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}" @if($request->employee==$employee->user_id) selected @endif>{{ @$employee->userInfo->first_name}} {{ @$employee->userInfo->last_name}} </option>
                                @endif
                            @endforeach
                        @endif

                        @if($clients)
                            @foreach($clients as $row)
                                @php  if($row->company_id==Session::get('company_id')){ $companyDetails   =     $row->requestedCompanyInfo; }
                                else{ $companyDetails   =     $row->companyInfo; } @endphp
                                <option value="{{ $companyDetails->user_id }}" data-chained="{{ $companyDetails->id}}" @if($request->employee==$companyDetails->user_id) selected @endif>{{ @$companyDetails->userInfo->first_name}} {{ @$companyDetails->userInfo->last_name}} </option>
                                @if($companyDetails->employees->count()>0)
                                    @foreach( $companyDetails->employees as $employee)
                                        <option value="{{ $employee->user_id }}" data-chained="{{ $employee->company_id}}" @if($request->employee==$employee->user_id) selected @endif>{{ @$employee->userInfo->first_name}} {{ @$employee->userInfo->last_name}} </option>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </select>
                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <br/>
                <strong>Docket Info</strong>
                <div class="form-group" style="margin-top:0px;">
                    <label for="docketTemplateId" class="control-label">Dockets</label>
                    <div style="position:relative;">
                        <select id="docketTemplateId" class="form-control selectDocketTemplate" name="docketTemplateId">
                            <option value="">Select Docket Template</option>
                            @if($company->dockets)
                                @foreach($company->dockets as $row)
                                    <option value="{{ $row->id }}" @if($request->docketTemplateId==$row->id) selected @endif >{{ $row->title }}</option>
                                @endforeach
                            @endif
                        </select>
                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" style="margin-top:40px;">
                    <label for="templateId" class="control-label">Docket Ids</label>
                    <input type="text" class="form-control"  name="docketId" value="{{ $request->docketId }}">
                </div>
            </div>
{{--            <div class="col-md-6">--}}
{{--                <div class="form-group docketFieldNameSelect" style="margin-top:40px;" >--}}

{{--                    <label for="templateId" class="control-label">Docket Field Name</label>--}}
{{--                    <select  class="form-control" name="docketFieldId" >--}}
{{--                        <option value="">Select Docket Field Name</option>--}}

{{--                        @if($docketData)--}}
{{--                            @foreach($docketData as $row)--}}
{{--                                <option value="{{ $row->id }}" @if($request->docketFieldId == $row->id) selected @endif>{{ $row->label }}</option>--}}
{{--                            @endforeach--}}
{{--                        @endif--}}
{{--                    </select>--}}
{{--                    <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>--}}

{{--                </div>--}}
{{--            </div>--}}

            <div class="col-md-12" >
                <br/>
                <strong>Field Info</strong>
                <br/>
                <div class="row docketFieldNameSelect">
                    <div class="col-md-12" style="display: -webkit-inline-box; overflow-y: auto;overflow-x: auto;width: 99%;">
                        @if($filterArray)
                            @foreach($filterArray as $row)
                                @if($row['docket_field']['docket_field_category_id'] == 20)
                                    <div class="form-group" style="margin-top:0px; width: 200px; margin-right: 11px; ">
                                        <label for="templateId" class="control-label">{{ $row['docket_field']['label'] }} (Manual Timer)</label>
                                        <input type="text" class="form-control"  name="docketFieldValue[{{$row['docket_field']['id']}}]" value="{{$row['value']}}">
                                    </div>
                                @elseif($row['docket_field']['docket_field_category_id'] == 24)
                                    <div class="form-group" style="margin-top:0px; width: 200px; margin-right: 11px; ">
                                        <label for="templateId" class="control-label">{{ $row['docket_field']['label'] }} (Tallyable Unit Rate)</label>
                                        <input type="text" class="form-control"  name="docketFieldValue[{{$row['docket_field']['id']}}]" value="{{$row['value']}}">
                                    </div>
                                @elseif($row['docket_field']['docket_field_category_id'] == 18)
                                    <div class="form-group" style="margin-top:0px; width: 200px; margin-right: 11px; ">
                                        <label for="templateId" class="control-label">{{ $row['docket_field']['label'] }}  (Yes/No-N/a Checkbox)</label>
                                        <input type="text" class="form-control"  name="docketFieldValue[{{$row['docket_field']['id']}}]" value="{{$row['value']}}">
                                    </div>
                                @elseif($row['docket_field']['docket_field_category_id'] == 22)
                                    <div class="form-group" style="margin-top:0px; width: 200px; margin-right: 11px; ">
                                        <label for="templateId" class="control-label">{{ $row['docket_field']['label'] }} (Grid)</label>
                                        <input type="text" class="form-control"  name="docketFieldValue[{{$row['docket_field']['id']}}]" value="{{$row['value']}}">
                                    </div>
                                @else
                                    <div class="form-group" style="margin-top:0px; width: 200px; margin-right: 11px; ">
                                        <label for="templateId" class="control-label">{{ $row['docket_field']['label'] }}</label>
                                        <input type="text" class="form-control"  name="docketFieldValue[{{$row['docket_field']['id']}}]" value="{{$row['value']}}">
                                    </div>
                                @endif




                            @endforeach
                        @endif
                     </div>
                </div>
            </div>

            <div class="col-md-6">
                <br/>
                <strong>By Date</strong>
                <div class="form-group" style="margin-top:0px;">
                    <label for="templateId" class="control-label">Date Type</label>
                    <div style="position:relative">
                        <select id="company" class="form-control" name="date">
                            <option value="2" @if($request->date==2) selected @endif >Inside docket date (User Selected date)</option>
                            <option value="1" @if($request->date==1) selected @endif >Outside docket date (docket creation date)</option>
                        </select>
                        <div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group" style="margin-top:40px;">
                    <label for="templateId" class="control-label">From</label>
                    <input type="text" class="form-control dateInput datepicker" dateType="docketCreated" value="{{ $request->from }}"  name="from"  id="fromDatePicker" autocomplete="off">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group" style="margin-top:40px;">
                    <label for="templateId" class="control-label">To</label>
                    <input type="text" class="form-control dateInput" dateType="docketCreated" id="toDatePicker"  name="to" value="{{ $request->to }}" autocomplete="off">
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}