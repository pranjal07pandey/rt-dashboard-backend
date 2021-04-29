<div class="row">
    <div class="col-md-12 " style="text-align: center; width: 881px; overflow-x: auto" >
        <table>
            <tbody style=" border: 1px solid black;">
                <tr>

                    <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">

                        <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                            <p><b>{{$docketField->label}}</b></p>
                        </div>
                        <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                            <p>(<i>CSV Column header title here</i>)</p>
                            <a href="#" id="docketField"
                               class="editableExport"
                               data-type="text" data-pk="{{ $docketField->id }}" data-type="docketField"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                               data-title="Enter Label Text">{{$docketField->csv_header}}</a>
                            <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i > @if($docketField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->id}}" dataType="docketField" > @endif</p>
                        </div>
                    </td>

                    @foreach ($docketField->yesNoField as $yesNoField)
                        <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                            <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                <p><b>{{$yesNoField->label}}</b></p>
                            </div>
                            <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                <p>(<i>CSV Column header title here</i>)</p>
                                <a href="#" id="yesNoField"
                                   class="editableExport"
                                   data-type="text" data-pk="{{ $yesNoField->id }}" data-type="yesNoField"
                                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                   data-title="Enter Label Text">{{$yesNoField->csv_header}}</a>
                                <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value </i> @if($yesNoField->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$yesNoField->id}}" dataType="yesNoField" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$yesNoField->id}}" dataType="yesNoField" > @endif</p>
                            </div>
                        </td>

                        @if ($yesNoField->explanation ==1)
                            @foreach ($yesNoField->yesNoDocketsField as $yesNoDocketsFields)
                                <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                                    <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                        <p><b>{{$yesNoDocketsFields->YesNoFieldInfo->label}}:{{$yesNoDocketsFields->label}}</b></p>
                                    </div>
                                    <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                        <p>(<i>CSV Column header title here</i>)</p>
                                        <a href="#" id="yesNoDocketsFields"
                                           class="editableExport"
                                           data-type="text" data-pk="{{ $yesNoDocketsFields->id }}" data-type="yesNoDocketsFields"
                                           data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                           data-title="Enter Label Text">{{$yesNoDocketsFields->csv_header}}</a>
                                        <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value </i> @if($yesNoDocketsFields->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$yesNoDocketsFields->id}}" dataType="yesNoDocketsFields" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$yesNoDocketsFields->id}}" dataType="yesNoDocketsFields" > @endif</p>
                                    </div>
                                </td>
                            @endforeach
                        @endif

                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
