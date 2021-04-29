<div class="row">
    <div class="col-md-12" style="text-align: center; width: 881px; overflow-x: auto">
        <table>
            <tbody style=" border: 1px solid black;">
            <tr>
                @foreach($docketField->tallyUnitRate as $tallyUnitRate)
                    <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                        <div style="background: #ffffff; padding: 15px 0px 7px 0px; " >
                            <p><b>{{$tallyUnitRate->label}}</b></p>
                        </div>
                        <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                            <p>(<i>CSV Column header title here</i>)</p>
                            <a href="#" id="tallyUnitRate"
                               class="editableExport"
                               data-type="text" data-pk="{{ $tallyUnitRate->id }}" data-type="tallyUnitRate"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                               data-title="Enter Label Text">{{$tallyUnitRate->csv_header}}</a>

                            <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($tallyUnitRate->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$tallyUnitRate->id}}" dataType="tallyUnitRate" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$tallyUnitRate->id}}" dataType="tallyUnitRate" > @endif</p>

                        </div>
                    </td>
                @endforeach


            </tr>
            </tbody>
        </table>

    </div>
</div>
