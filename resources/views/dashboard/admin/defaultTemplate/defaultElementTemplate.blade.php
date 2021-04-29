@if($item->docket_field_category_id==1)
    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="shortTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif style="display: inline-block;">
                <a href="#" id="shortText" class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="title" type="text" disabled class="form-control" name="title" placeholder="Short Text" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>


        </div>
    </div><!--shortText field-->

@endif

@if($item->docket_field_category_id==2)
    <div class="col-md-12 longTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="longTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="longText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>

            </span>
            <div class="form-group">
                <input disabled style="height: 70px; background: #eee;cursor: pointer;     padding-left: 10px;" id="description"  type="text" class="form-control" name="description" placeholder="Long Text" value="{{ old('description') }}" required autofocus>
            </div>
            <button type="button" id="removeLongText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <span class="" style="font-size: 12px; @if($tempDocket->invoiceable!=1) display:none @endif">
                Invoice Description&nbsp;&nbsp;
                <input type="checkbox" class="docketInvoiceCheckboxInput"   data="{{ $item->id }}"  @if(@count($item->docketInvoiceField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;
            </span>
            <br>
        </div>
    </div>

@endif

@if($item->docket_field_category_id==3)
    <div class="col-md-12 docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="numDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="numText" class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="hourlyRate" type="text" class="form-control" name="hourlyRate" placeholder="Number Label" value="{{ old('hourlyRate') }}" required autofocus disabled>
            </div>
            <button type="button" id="removeNum" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div><!--/.numDiv-->
@endif

@if($item->docket_field_category_id==4)
    <div class="col-md-12 locationDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="locationDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="shortText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="location" disabled="disabled" type="text" class="form-control" name="location" placeholder="Location" value="{{ old('location') }}" required autofocus>
            </div>
            <button type="button" id="removeLocation" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div>
@endif

@if($item->docket_field_category_id==5)
    <div class="col-md-12 imageDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="imagesDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="imageText" class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="hourlyRate" disabled="disabled" type="text" class="form-control" name="hourlyRate" placeholder="Image" value="{{ old('hourlyRate') }}" required autofocus>
            </div>
            <button type="button" id="removeImage" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>

    </div>
@endif


@if($item->docket_field_category_id==6)
    <div  class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="dateTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="shortText" class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="title" disabled type="text" class="form-control" name="title" placeholder="Date" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div><!--shortText field-->
@endif



{{--@if($item->docket_field_category_id==7)--}}
    {{--<div class="col-md-12 unitRateDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="unitRateDiv " fieldId="{{ $item->id }}">--}}
        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="horizontalList">--}}
                    {{--<div class="col-md-6">--}}
                        {{--<span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>--}}
                         {{--<a href="#" id="shortText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->unitRate[0]->id }}" data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldUnitFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->unitRate[0]->label }}</a>--}}
                        {{--</span>--}}
                        {{--<div class="form-group" style="min-width: 150px;">--}}
                            {{--<input disabled id="hours" type="text" class="form-control" placeholder="Unit Rate" name="hours"  required autofocus>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-md-6" style="    padding-right: 0px;">--}}
                      {{--<span>--}}
                         {{--<a href="#" id="shortText" class="editable" data-type="text" data-pk="{{ $item->unitRate[1]->id }}" data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldUnitFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->unitRate[1]->label }}</a>--}}
                        {{--<span class="pull-right" style="font-size: 12px; @if($tempDocket->invoiceable!=1) display:none; @endif">--}}
                            {{--Invoice Amount&nbsp;&nbsp;--}}
                            {{--<input type="checkbox" class="docketInvoiceCheckboxInput" data="{{ $item->id }}"  @if(@count($item->docketInvoiceField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                        {{--</span>--}}
                      {{--</span>--}}
                        {{--<div class="form-group" style="min-width: 150px;">--}}
                            {{--<input id="to" disabled="disabled" placeholder="Total Unit" type="text" class="form-control" name="to"   required autofocus>--}}
                        {{--</div>--}}
                        {{--<button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                    {{--</div>--}}
                    {{--<div class="clearfix"></div>--}}

                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endif--}}

@if($item->docket_field_category_id==8)
    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="dateTextDiv" fieldId="{{ $item->id }}" style="margin-bottom: 15px;margin-top: 15px;">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="shortText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span><br/>
            <div class="form-group" style="margin-top: 0px;width: calc( 100% - 50px)">
                <input id="checkbox"  type="text" disabled class="form-control" name="checkbox" placeholder="Check Box" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div><!--shortText field-->
@endif

@if($item->docket_field_category_id==9)
    <div class="col-md-12 signatureDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="signatureDiv " fieldId="{{ $item->id }}" style="margin-bottom: 15px;margin-top: 15px;">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="shortText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="signature" type="text" class="form-control" disabled name="signature" placeholder="Signature" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeSignature" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div><!--shortText field-->
@endif

@if($item->docket_field_category_id==14)
    <div class="col-md-12 imageDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="sketchPadDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList">
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="sketchPadText" class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/defaultTemplate/designDefaultDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="sketchPad" type="text" class="form-control" disabled name="sketchPad" placeholder="Sketch Pad" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeSketchPad" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            <br>
        </div>
    </div>
@endif