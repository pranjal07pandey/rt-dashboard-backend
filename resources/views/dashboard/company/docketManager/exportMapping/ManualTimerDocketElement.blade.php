<div class="row">
    <div class="col-md-12" style="text-align: center; width: 881px; overflow-x: auto">
        <table>
            <tbody style=" border: 1px solid black;">
             <tr>
                 @foreach($docketField->docketManualTimer as $docketManualTimer)
                         <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                             <div style="background: #ffffff; padding: 15px 0px 7px 0px;  width: 228px;" >
                                 <p><b>{{$docketManualTimer->label}}</b></p>
                             </div>
                             <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                 <p>(<i>CSV Column header title here</i>)</p>
                                 <a href="#" id="docketManualTimer"
                                    class="editableExport"
                                    data-type="text" data-pk="{{ $docketManualTimer->id }}" data-type="docketManualTimer"
                                    data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                    data-title="Enter Label Text">{{$docketManualTimer->csv_header}}</a>

                                 <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketManualTimer->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketManualTimer->id}}" dataType="docketManualTimer" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketManualTimer->id}}" dataType="docketManualTimer" > @endif</p>

                             </div>
                         </td>
                  @endforeach

                     @foreach($docketField->docketManualTimerBreak as $docketManualTimerBreak)
                         <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px; ">
                             <div style="background: #ffffff; padding: 15px 0px 7px 0px;  width: 228px;" >
                                 <p><b>{{$docketManualTimerBreak->label}}</b></p>
                             </div>
                             <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                 <p>(<i>CSV Column header title here</i>)</p>
                                 <a href="#" id="docketManualTimerBreak"
                                    class="editableExport"
                                    data-type="text" data-pk="{{ $docketManualTimerBreak->id }}" data-type="docketManualTimerBreak"
                                    data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                    data-title="Enter Label Text">{{$docketManualTimerBreak->csv_header}}</a>
                                 <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($docketManualTimerBreak->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketManualTimerBreak->id}}" dataType="docketManualTimerBreak"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketManualTimerBreak->id}}" dataType="docketManualTimerBreak"  > @endif</p>

                             </div>
                         </td>
                     @endforeach




                 @foreach(unserialize($docketField->exportMapping->value)  as $data)
                     @if ($data["label"] == "Explanation")
                         <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                             <div style="background: #ffffff; padding: 15px 0px 7px 0px; width: 228px;" >
                                 <p><b>{{$data["label"]}}</b> </p>
                             </div>
                             <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                 <p>(<i>CSV Column header title here</i>)</p>
                                 <a href="#" id="explanation"
                                    class="editableExport"
                                    data-type="text" data-pk="{{ $docketField->exportMapping->id }}" data-type="explanation"
                                    data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                    data-title="Enter Label Text">{{$data["csvHeader"]}}</a>
                                 <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($data["isShow"] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="explanation"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="explanation"  > @endif</p>
                             </div>
                         </td>
                     @endif

                     @if ($data["label"] == "Total Hours")
                         <td style=" border: 1px solid black;    padding: 0px 0px 0px 0px;">
                             <div style="background: #ffffff; padding: 15px 0px 7px 0px;  width: 228px;" >
                                 <p><b>{{$data["label"]}}</b> </p>
                             </div>
                             <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                                 <p>(<i>CSV Column header title here</i>)</p>
                                 <a href="#" id="totalHours"
                                    class="editableExport"
                                    data-type="text" data-pk="{{ $docketField->exportMapping->id }}" data-type="totalHours"
                                    data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                                    data-title="Enter Label Text">{{$data["csvHeader"]}}</a>
                                 <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($data["isShow"] == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="totalHours"  checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$docketField->exportMapping->id}}" dataType="totalHours"  > @endif</p>
                             </div>
                         </td>
                     @endif

                 @endforeach
             </tr>
            </tbody>
        </table>

    </div>
</div>
