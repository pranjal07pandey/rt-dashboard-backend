<div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
     id="headerDiv" fieldId="{{ $item->id }}">

    <div class="horizontalList "  @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif id="normalAHView-{{$item->id}}">
        <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                      @endif style="display: inline-block;">
                    <a href="#" id="header"
                       class="   @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text" style="font-weight: 500;color: #000000;">Advance Header</a>
                </span>
            @if($item->deleted_at == null)
                  <button type="button" id="removeHeader"
                    class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                    data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
            @else
                <button type="button" id="removeShortText"
                        class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                        data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
            @endif

            <div class="form-group " id="displayAdvanceHeader{{$item->id}}" style="padding: 0px 0 0 16px;">
                {{--                    <input id="title" style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;" type="text" disabled class="form-control" name="title" placeholder="" value="{{ old('title') }}" required autofocus>--}}
                {!! $item->label !!}
            </div>
        </div>
        <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
            <ul style="list-style-type: none;margin: 0;padding: 0;">
                <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                </li>
                <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                </li>
                <li style=" float: left;background: none;">
                     <span style="font-size: 12px; color: #9D9D9E;">
                        <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Advance Header"
                                class="btn btn-info btn-xs btn-raised"
                                style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                           <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                    </span>
                </li>
            </ul>

            <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                                @if($item->deleted_at == null)
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                                    @endif
                         </span>
                </li>
                <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button category_id="{{$item->id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent editAdvanceHeader"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-edit" aria-hidden="true"></i> Edit</button>
                         </span>
                </li>
            </ul>


            <div class="clearfix"></div>
        </div>

    </div>

    <div id="editedAHView-{{$item->id}}"
         style="display: none; background-color: #f4f5f9;padding: 13px; margin-bottom: 9px;">
        <a href="#" id="header"
           class="   @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
           data-type="text" data-pk="{{ $item->id }}"
           data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
           data-title="Enter Label Text" style="font-weight: 500;color: #000000;">Advance Header</a>
        <br><br>
        <textarea
                name="editor{{$item->id}}">@if ($item->label != "Advance Header" ){{$item->label}}@endif</textarea>
        <button class="btn btn-info btn-xs btn-raised saveAdvanceHeader" id="{{$item->id}}"
                style="position: absolute;bottom: 17px;right: 31px;padding: 2px 8px 2px 8px;">Save
        </button>
    </div>

</div>


<script src="{{asset('assets/dashboard/ckeditor/ckeditor.js')}}"></script>
<script>

    $(document).ready(function () {
        CKEDITOR.replace('editor{{$item->id}}');
    });
</script>
