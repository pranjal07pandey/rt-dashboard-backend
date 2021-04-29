<div class="row">
    <div class="col-md-12" style="text-align: center; width: 881px; overflow-x: auto">
        <table>
            <tbody style=" border: 1px solid black;">
            <tr>
                @foreach($docketField->unitRate as $unitRate)
                    <td style=" border: 1px solid black;     padding: 0px 0px 0px 0px;">
                        <div style="background: #ffffff; padding: 15px 0px 7px 0px; " >
                            <p><b>{{$unitRate->label}}</b></p>
                        </div>
                        <div style="background: #d0d0d0; padding: 15px 0px 7px 0px;">
                            <p>(<i>CSV Column header title here</i>)</p>
                            <a href="#" id="unitRate"
                               class="editableExport"
                               data-type="text" data-pk="{{ $unitRate->id }}" data-type="unitRate"
                               data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateExportMappingHeader') }}"
                               data-title="Enter Label Text">{{$unitRate->csv_header}}</a>

                            <p style="padding-top: 10px;"><i style="padding-right: 10px;"> export this value  </i> @if($unitRate->is_show == 1) <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$unitRate->id}}" dataType="unitRate" checked> @else <input type="checkbox" class="exportMappingCheckbox" fieldId="{{$unitRate->id}}" dataType="unitRate" > @endif</p>

                        </div>
                    </td>
                @endforeach


            </tr>
            </tbody>
        </table>

    </div>
</div>
