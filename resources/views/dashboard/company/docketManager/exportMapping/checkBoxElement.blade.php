<div class="row">
    <div class="col-md-12" style="text-align: center; width: 881px; overflow-x: auto">
        <table>
            <tbody style=" border: 1px solid black;">
            <tr>

                    <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                        <div style="background: #ffffff; padding: 15px 0px 7px 0px;">
                            <p><b>{{$docketField->label}} ({{$docketField->fieldCategoryInfo->title}})</b></p>
                        </div>
                        <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                            <p>(<i>CSV Column header title here</i>)</p>
                            <a href="#" id="docketField"
                               class="editableExport"
                               data-type="text" data-pk="{{ $docketField->id }}" data-type="docketField"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                               data-title="Enter Label Text">{{$docketField->csv_header}}</a>
                            <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" value="1" checked> @else <input class="exportMappingCheckbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" type="checkbox" value="0"> @endif</p>
                        </div>
                    </td>


                @foreach(unserialize($docketField->exportMapping->value)  as $data)
                    @if ($data["label"] == "Checked")
                        <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                            <div style="background: #ffffff; padding: 15px 0px 7px 0px; text-align: center;" >
                                <p><b>{{$data["label"]}}</b> </p>
                            </div>
                            <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                <p>(<i>CSV Column header title here</i>)</p>
                                <a href="#" id="checked"
                                   class="editableExport"
                                   data-type="text" data-pk="{{ $docketField->exportMapping->id }}" data-type="checked"
                                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                   data-title="Enter Label Text">{{$data["csvHeader"]}}</a>
                                <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($data["isShow"] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="checked"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="checked"  > @endif</p>
                            </div>
                        </td>
                    @endif

                    @if ($data["label"] == "Unchecked")
                        <td style=" border: 1px solid black;    padding: 0px 0px 0px 0px;">
                            <div style="background: #ffffff; padding: 15px 0px 7px 0px;  text-align: center;" >
                                <p><b>{{$data["label"]}}</b> </p>
                            </div>
                            <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                <p>(<i>CSV Column header title here</i>)</p>
                                <a href="#" id="unchecked"
                                   class="editableExport"
                                   data-type="text" data-pk="{{ $docketField->exportMapping->id }}" data-type="unchecked"
                                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                   data-title="Enter Label Text">{{$data["csvHeader"]}}</a>
                                <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($data["isShow"] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="unchecked"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="unchecked"  > @endif</p>
                            </div>
                        </td>
                    @endif

                @endforeach
            </tr>
            </tbody>
        </table>

    </div>
</div>
