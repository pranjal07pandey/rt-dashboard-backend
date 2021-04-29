

<div class="row">
    <div class="col-md-12">
        <div style="border: 1px solid #000000;text-align: center;">
                <div style="background: #ffffff; padding: 15px 0px 7px 0px;">
                    <p>(<i>Field Title / Type</i>)</p>
                    <p><b>{!! $docketField->label !!} ({{$docketField->fieldCategoryInfo->title}})</b></p>
                </div>
                <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                    <p>(<i>CSV Column header title here</i>)</p>
                    <a href="#" id="docketField"
                       class="editableExport"
                       data-type="text" data-pk="{{ $docketField->id }}" data-type="docketField"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                       data-title="Enter Label Text">{!! $docketField->csv_header !!}}}</a>
                       <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" value="1" checked> @else <input class="exportMappingCheckbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" type="checkbox" value="0"> @endif</p>
                </div>
        </div>
    </div>
</div>
