{{--<select  class="form-control" name="docketFieldId" >--}}
{{--    <option value="">Select Docket Field Name</option>--}}
{{--    @if($docketTemplate)--}}
{{--        @foreach($docketTemplate as $row)--}}
{{--                <option value="{{ $row->id }}" >{{ $row->label }}</option>--}}
{{--        @endforeach--}}

{{--    @endif--}}
{{--</select>--}}
{{--<div style="position: absolute;right: 0px;top: 10px;"><i class="fa fa-angle-down"></i></div>--}}


<div class="col-md-12" style="display: -webkit-inline-box; overflow-y: auto;overflow-x: auto;width: 99%;">

    @if($docketTemplate)
        @foreach($docketTemplate as $row)
          @if($row->docket_field_category_id == 20)
                <div class="form-group" style="margin-top:0px; min-width: 200px; margin-right: 11px; ">
                    <label for="templateId" class="control-label">{{ $row->label }} (Manual Timer)</label>
                    <input type="text" class="form-control"  name="docketFieldValue[{{$row->id}}]">
                </div>
          @elseif($row->docket_field_category_id == 24)
                <div class="form-group" style="margin-top:0px; min-width: 200px; margin-right: 11px; ">
                    <label for="templateId" class="control-label">{{ $row->label }} (Tallyable Unit Rate)</label>
                    <input type="text" class="form-control"  name="docketFieldValue[{{$row->id}}]">
                </div>
          @elseif($row->docket_field_category_id == 18)
              <div class="form-group" style="margin-top:0px; min-width: 200px; margin-right: 11px; ">
                  <label for="templateId" class="control-label">{{ $row->label }} (Yes/No-N/a Checkbox)</label>
                  <input type="text" class="form-control"  name="docketFieldValue[{{$row->id}}]">
              </div>
            @elseif($row->docket_field_category_id == 22)
                <div class="form-group" style="margin-top:0px; min-width: 200px; margin-right: 11px; ">
                    <label for="templateId" class="control-label">{{ $row->label }} (Grid)</label>
                    <input type="text" class="form-control"  name="docketFieldValue[{{$row->id}}]">
                </div>
            @else
                <div class="form-group" style="margin-top:0px; min-width: 200px; margin-right: 11px; ">
                    <label for="templateId" class="control-label">{{ $row->label }}</label>
                    <input type="text" class="form-control"  name="docketFieldValue[{{$row->id}}]">
                </div>
          @endif

        @endforeach
    @endif


</div>