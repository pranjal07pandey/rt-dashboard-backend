@if($docketField->docketConstantField->export_mapping_field_category_id == 11)
    <div class="row">
        <div class="col-md-12">
            <div style="border: 1px solid #000000;text-align: center;">
                <div style="background: #ffffff; padding: 15px 0px 7px 0px;" >
                    <p>(<i>Field Title / Type</i>)</p>
                    <p><b>{{$docketField->docketConstantField->label}} ({{$docketField->fieldCategoryInfo->title}})</b></p>
                </div>
                <div style="background: #d0d0d0; padding: 15px 0px 15px 0px;border: 1px solid #000000;text-align: center; height: 130px;" class="col-md-6">
                    <p>(<i>CSV Column header title here</i>)</p>
                    <a href="#" id="docketConstant"
                       class="editableExport"
                       data-type="text" data-pk="{{ $docketField->docketConstantField->id }}" data-type="docketConstant"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                       data-title="Enter Label Text">{{$docketField->docketConstantField->csv_header}}</a>
                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketField->docketConstantField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->docketConstantField->id}}" dataType="docketConstant" value="1" checked> @else <input class="exportMappingCheckbox"  fieldId="{{$docketField->docketConstantField->id}}" dataType="docketConstant" type="checkbox" value="0"> @endif</p>
                </div>
                <div style="background: #d0d0d0; padding: 15px 0px 15px 0px;  border: 1px solid #000000;text-align: center; height: 130px;" class="col-md-6">
                    <p>(<i>CSV Column Value title here</i>)</p>

                    <a href="#" id="docketValueConstant"
                       class="editableExport"
                       data-type="text" data-pk="{{ $docketField->docketConstantField->id }}" data-type="docketValueConstant"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                       data-title="Enter Label Text">{{unserialize($docketField->exportMapping->value)[0]['csvHeader']}}</a>
                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if( unserialize($docketField->exportMapping->value)[0]['isShow'] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->docketConstantField->id}}" dataType="docketValueConstant" value="1" checked> @else <input class="exportMappingCheckbox"  fieldId="{{$docketField->docketConstantField->id}}" dataType="docketValueConstant" type="checkbox" value="0"> @endif</p>
                </div>
            </div>
        </div>
    </div>
@else

    <div class="row">
        <div class="col-md-12">
            <div style="border: 1px solid #000000;text-align: center;">
                <div style="background: #ffffff; padding: 15px 0px 7px 0px;">
                    <p>(<i>Field Title / Type</i>)</p>
                    <p><b>{{$docketField->docketConstantField->label}} ({{$docketField->fieldCategoryInfo->title}})</b></p>
                </div>
                <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                    <p>(<i>CSV Column header title here</i>)</p>
                    <a href="#" id="docketConstant"
                       class="editableExport"
                       data-type="text" data-pk="{{ $docketField->docketConstantField->id }}" data-type="docketConstant"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                       data-title="Enter Label Text">{{$docketField->docketConstantField->csv_header}}</a>
                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketField->docketConstantField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->docketConstantField->id}}" dataType="docketConstant" value="1" checked> @else <input class="exportMappingCheckbox"  fieldId="{{$docketField->docketConstantField->id}}" dataType="docketConstant" type="checkbox" value="0"> @endif</p>
                </div>
            </div>
        </div>
    </div>
@endif
