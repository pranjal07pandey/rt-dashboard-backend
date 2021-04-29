<div class="row">
    <div class="col-md-12" style="text-align: center; width: 881px; overflow-x: auto">
        <table>
            <tbody style=" border: 1px solid black;">
            <tr>

                @foreach($docketField->girdFields as $girdField)
                    @if ($girdField->docket_field_category_id == 20)
                        @foreach(unserialize($girdField->export_value) as $exportValue)
                            <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                                <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                    <p>{{$girdField->label}}:<b> {{$exportValue['label']}}</b> </p>
                                </div>
                                <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                    <p>(<i>CSV Column header title here</i>)</p>
                                    <a href="#" id="grid{{$exportValue['label']}}"
                                       class="editableExport"
                                       data-type="text" data-pk="{{ $girdField->id }}" data-type="grid{{$exportValue['label']}}"
                                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                       data-title="Enter Label Text">{{$exportValue['csvHeader']}}</a>
                                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($exportValue['isShow'] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="grid{{$exportValue['label']}}"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="grid{{$exportValue['label']}}"  > @endif</p>
                                </div>
                            </td>
                        @endforeach
                        @elseif($girdField->docket_field_category_id == 8)
                        @foreach(unserialize($girdField->export_value) as $exportValue)
                            <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                                <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                    <p>{{$girdField->label}}:<b> {{$exportValue['label']}}</b> </p>
                                </div>
                                <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                    <p>(<i>CSV Column header title here</i>)</p>
                                    <a href="#" id="grid{{$exportValue['label']}}"
                                       class="editableExport"
                                       data-type="text" data-pk="{{ $girdField->id }}" data-type="grid{{$exportValue['label']}}"
                                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                       data-title="Enter Label Text">{{$exportValue['csvHeader']}}</a>
                                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($exportValue['isShow'] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="grid{{$exportValue['label']}}"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="grid{{$exportValue['label']}}"  > @endif</p>
                                </div>
                            </td>
                        @endforeach

                        @else
                            <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                                <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                    <p><b>{{$girdField->label}}</b> </p>
                                </div>
                                <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                    <p>(<i>CSV Column header title here</i>)</p>
                                    <a href="#" id="gridNormal"
                                       class="editableExport"
                                       data-type="text" data-pk="{{ $girdField->id }}" data-type="gridNormal"
                                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                       data-title="Enter Label Text">{{$girdField->csv_header}}</a>
                                    <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($girdField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="gridNormal"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$girdField->id}}" dataType="gridNormal"  > @endif</p>
                                </div>
                            </td>
                        @endif
                @endforeach

            </tr>
            </tbody>
        </table>

    </div>
</div>


