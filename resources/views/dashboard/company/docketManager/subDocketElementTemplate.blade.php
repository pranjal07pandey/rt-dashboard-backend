@if($item->docket_field_category_id==1)
    <div style="margin-right: 25px;" class=" shortTextDiv docketField subdocketing" id="shortTextDiv" fieldId="{{ $item->id }}" subdocketingFieldId="{{ $item->id }}" yesnoSubdocketing="{{$item->yes_no_field_id}}">
        <div class="horizontalList subdocketinghorizontalList" >
            <span  style="display: inline-block;">
                <a href="#" id="shortText" style="position: relative;"  class="editablesubdocket" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/docketBookManager/subDocketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="title" type="text" disabled class="form-control" name="title" placeholder="Short Text" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteSubDocketComponent " subDocketingfieldId="{{ $item->id }}" yesnofield="{{$item->yes_no_field_id}}"><i class="fa fa-trash-o"></i> </button>

            <span style="font-size: 12px;">
               <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                <input type="checkbox" value="1" class="SubDocketfieldrequired"  subDocketData="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
            </span>
        </div>
    </div><!--shortText field-->
@endif
@if($item->docket_field_category_id==2)
    <div style="margin-right: 25px;" class=" shortTextDiv docketField subdocketing" id="shortTextDiv" fieldId="{{ $item->id }}" subdocketingFieldId="{{ $item->id }}" yesnoSubdocketing="{{$item->yes_no_field_id}}">
        <div class="horizontalList subdocketinghorizontalList" >
            <span  style="display: inline-block;">
                <a href="#" id="shortText" style="position: relative;"  class="editablesubdocket" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/docketBookManager/subDocketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="title" type="text" disabled class="form-control" name="title" placeholder="Short Text" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteSubDocketComponent " subDocketingfieldId="{{ $item->id }}" yesnofield="{{$item->yes_no_field_id}}"><i class="fa fa-trash-o"></i> </button>

            <span style="font-size: 12px;">
               <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                <input type="checkbox" value="1" class="SubDocketfieldrequired"  subDocketData="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
            </span>
        </div>
    </div><!--shortText field-->
@endif
@if($item->docket_field_category_id==5)
    <div style="margin-right: 25px;" class=" shortTextDiv docketField subdocketing" id="shortTextDiv" fieldId="{{ $item->id }}" subdocketingFieldId="{{ $item->id }}" yesnoSubdocketing="{{$item->yes_no_field_id}}">
        <div class="horizontalList subdocketinghorizontalList" >
            <span  style="display: inline-block;">
                <a href="#" id="shortText" style="position: relative;" class="editablesubdocket" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/docketBookManager/subDocketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
            <div class="form-group">
                <input id="title" type="text" disabled class="form-control" name="title" placeholder="Image" value="{{ old('title') }}" required autofocus>
            </div>
            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteSubDocketComponent" subDocketingfieldId="{{ $item->id }}" yesnofield="{{$item->yes_no_field_id}}"><i class="fa fa-trash-o"></i> </button>


            <span style="font-size: 12px;">
               <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                <input type="checkbox" value="1" class="SubDocketfieldrequired"  subDocketData="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
            </span>
        </div>
    </div>
@endif
