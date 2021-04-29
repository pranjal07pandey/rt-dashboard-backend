<div class="col-md-12 docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="modularGridDiv" fieldId="{{ $item->id }}">
    <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background: #f7f8fa;" @endif>
        <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="display: inline-block !important;margin-bottom: 11px;">
            <a href="#" id="modularGridText" class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
        </span>
        @if($item->deleted_at == null)
        <button type="button" id="removeModularGridDiv" class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
        @else
            <button type="button" id="removeShortText"
                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
        @endif
        <div class="gridSection pull-left">
            <div class="form-group" style="width: 100%;margin-top: 15px;">
                <table class="table table-bordered" style="width:100%; " id="table">
                    <thead>
                    <tr class="row_position" style="background-color: #ffffff;k">
                        @foreach($item->girdFields as $girdField)
                            <th filed_id="{{ $girdField->id }}" class="grid-row">
                                @if($item->deleted_at == null)
                                <button type="button" id="removeModularGridColumn"
                                        class="btn btn-raised btn-xs btn-danger deleteGridColumn pull-right card-link"
                                        data-id="{{ $girdField->id }}"><i
                                            style="    font-size: 23px;color: #d3564f;"
                                            class="fa fa-minus-circle" aria-hidden="true"></i></button>

                                @endif

                                <div class="gridbody">
                                    <h5>
                                        <a href="#" id="modularGridLabelText"
                                           class=" editabledocketprefiller @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                                           data-type="text" data-pk="{{ $girdField->id }}"
                                           data-url="{{ route('grid.table.label.update') }}"
                                           data-title="Enter Label Text">
                                            {{ $girdField->label }}
                                        </a>

                                    </h5>
                                    <hr style="margin: 0;">
                                    <div style=" padding: 0px 21px 0px 0px;">
                                        <input id="title" type="text" style="border: 1px solid #E7EAF0; "
                                               disabled class="form-control " name="title"
                                               placeholder="{{ $girdField->docketFieldCategory->title }}"
                                               value="{{ $girdField->docketFieldCategory->title }}" required
                                               autofocus>
{{--                                            <select style="width: 100%--}}
{{--">--}}
{{--                                                <option>das</option>--}}
{{--                                            </select>--}}
                                        @if($girdField->docket_field_category_id == 3 )
                                            @php
                                                $checkFormulaExists = \App\GridFieldFormula::where([['formula','like','%'."cell".$girdField->id.'%']])->first();
                                            @endphp
                                            @if($item->deleted_at == null)
                                                <button class="btn btn-raised btn-xs btn-info formulaButtonData{{ $girdField->id }}"
                                                        data-toggle="modal" data-target="#setFormula"
                                                        data-field_id="{{ $item->id }}"
                                                        data-grid_field_id="{{ $girdField->id }}"l
                                                        @if(count($girdField->linkGridFieldAutoPrefiller) != 0 || count($girdField->gridFieldPreFiller) != 0 || $girdField->docket_prefiller_id != 0 || $checkFormulaExists != null) style="display:none; position: absolute;right: 11px;top: 54px;padding: 10px;"  @endif style="position: absolute;right: 11px;top: 54px;padding: 10px;">
                                                    Formula
                                                </button>
                                            @endif

                                            @if($item->deleted_at == null)
                                                <button  class="btn btn-raised btn-xs btn-info showhideprefillerbtn{{$girdField->id}}"
                                                         data-toggle="modal" data-target="#setgridPrefiller"
                                                         data-field_id="{{ $item->id }}"
                                                         data-grid_field_id="{{ $girdField->id }}"
                                                         data-autofield="{{$girdField->auto_field}}"
                                                         data-is_dependent_data = "{{$girdField->is_dependent}}"
                                                         data-echowise_id = "{{$girdField->echowise_id}}"
                                                         data-grid_field_type="0"
                                                         @if(count($girdField->linkGridFieldAutoPrefiller) != 0 || $girdField->gridFieldFormula != null || $checkFormulaExists != null) style="display:none; position: absolute;right: 75px;top: 54px;padding: 10px;"  @endif style="position: absolute;right: 75px;top: 54px;padding: 10px;">
                                                    Prefiller
                                                </button>
                                            @endif

                                            <div style="float: right;padding: 15px 0px 0px 8px;"> Summable <input type="checkbox" class="sumableValue" docfieldId="{{ $girdField->id }}" value="0" @if($girdField->sumable==1) checked @endif ></div>

                                        @elseif($girdField->docket_field_category_id == 20)
                                            <div style="float: right;padding: 15px 0px 0px 8px;"> <button class="btn btn-info btn-xs btn-raised gridManualTimeFormat"  data-fieldid="{{$item->id}}"  data-fieldgridid="{{$girdField->id}}" data-timeformat="{{$girdField->time_format}}" >Time Format</button> </div>


                                        @elseif($girdField->docket_field_category_id == 29)
                                            @if($item->deleted_at == null)
                                                <button  class="btn btn-raised btn-xs btn-info showhideprefillerbtn{{$girdField->id}}"
                                                         data-toggle="modal" data-target="#setgridPrefiller"
                                                         data-field_id="{{ $item->id }}"
                                                         data-grid_field_id="{{ $girdField->id }}"
                                                         data-autofield="{{$girdField->auto_field}}"
                                                         data-is_dependent_data = "{{$girdField->is_dependent}}"
                                                         data-grid_field_type="0"
                                                         data-echowise_id = "{{$girdField->echowise_id}}"
                                                         @if(count($girdField->linkGridFieldAutoPrefiller) != 0) style="display:none; position: absolute;right: 11px;top: 54px;padding: 10px;"  @endif style="position: absolute;right: 11px;top: 54px;padding: 10px;">
                                                    Prefiller
                                                </button>
                                            @endif
                                            <div style="float: right;padding: 15px 0px 0px 8px;"> Send a Docket <input type="checkbox" class="gridSendDocket" docfieldId="{{ $girdField->id }}" value="0" @if($girdField->send_copy_docket==1) checked @endif ></div>

                                        @elseif($girdField->docket_field_category_id == 4 )
                                            @if($item->deleted_at == null)
                                                <button  class="btn btn-raised btn-xs btn-info showhideprefillerbtn{{$girdField->id}}"
                                                         data-toggle="modal" data-target="#setgridPrefiller"
                                                         data-field_id="{{ $item->id }}"
                                                         data-grid_field_id="{{ $girdField->id }}"
                                                         data-autofield="{{$girdField->auto_field}}"
                                                         data-is_dependent_data = "{{$girdField->is_dependent}}"
                                                         data-grid_field_type="0"
                                                         data-echowise_id = "{{$girdField->echowise_id}}"
                                                         @if(count($girdField->linkGridFieldAutoPrefiller) != 0) style="display:none; position: absolute;right: 11px;top: 54px;padding: 10px;"  @endif style="position: absolute;right: 11px;top: 54px;padding: 10px;">
                                                    Prefiller
                                                </button>

                                            @endif
                                        @elseif($girdField->docket_field_category_id == 1 || $girdField->docket_field_category_id == 2 )
                                            @if($item->deleted_at == null)
                                                <button  class="btn btn-raised btn-xs btn-info showhideprefillerbtn{{$girdField->id}}"
                                                         data-toggle="modal" data-target="#setgridPrefiller"
                                                         data-field_id="{{ $item->id }}"
                                                         data-grid_field_id="{{ $girdField->id }}"
                                                         data-autofield="{{$girdField->auto_field}}"
                                                         data-is_dependent_data = "{{$girdField->is_dependent}}"
                                                         data-grid_field_type="0"
                                                         data-echowise_id = "{{$girdField->echowise_id}}"
                                                         @if(count($girdField->linkGridFieldAutoPrefiller) != 0) style="display:none; position: absolute;right: 11px;top: 54px;padding: 10px;"  @endif style="position: absolute;right: 11px;top: 54px;padding: 10px;">
                                                    Prefiller
                                                </button>
                                                <div style="float: right;padding: 15px 0px 0px 8px;"> <span style="color: #999;font-size: 12px;">Is Email Subject ? </span>&nbsp; <input type="checkbox" value="1" class="gridIsEmailSubject" data="{{ $girdField->id }}" name="required" @if($girdField->is_emailed_subject==1) checked @endif></div>
                                            @endif

                                        @endif



                                        <div class="gridPrefillerShowasss{{ $girdField->id }}" style="padding:  13px 0px 1px 9px;        position: relative; overflow-x: auto;">

                                            @if ($girdField->gridFieldFormula != null)
                                                fx =
                                                <?php
                                                $formulaValue = unserialize(@$girdField->gridFieldFormula->formula);
                                                ?>

                                                @foreach ($formulaValue as $formulaValues)
                                                    @if (is_numeric($formulaValues))
                                                        <label class="textsie">{{$formulaValues}}</label>
                                                    @elseif (preg_match("/TDiff/i", $formulaValues))
                                                        <?php
                                                        $startTime = substr(explode(",", $formulaValues)[0], 10);
                                                        $endtime = substr_replace(substr(explode(",", $formulaValues)[1], 4), "", -1);
                                                        $docketFieldGrid = \App\DocketFieldGrid::where('docket_field_id', $item->id)->where('docket_field_category_id', 26)->get();
                                                        ?>
                                                        <label class="textsie">Time
                                                            Diff: (</label>
                                                        @foreach($docketFieldGrid as $row)
                                                            @if($startTime == $row->id)
                                                                <label class="textsie">{{@$row->label}} </label>
                                                            @endif
                                                        @endforeach
                                                        -
                                                        @foreach($docketFieldGrid as $row)
                                                            @if($endtime == $row->id)
                                                                <label class="textsie">{{@$row->label}} </label>
                                                            @endif
                                                        @endforeach

                                                        <label class="textsie" >)</label>
                                                    @elseif (preg_match("/cell/i", $formulaValues))
                                                        <?php
                                                        $id = substr($formulaValues, 4);
                                                        $docketFieldGrid = \App\DocketFieldGrid::where('id', $id)->where('docket_field_category_id', 3)->first();
                                                        ?>
                                                        <label class="textsie">{{ @$docketFieldGrid->label}}</label>
                                                    @else
                                                        @if ('+' == $formulaValues)
                                                            <label class="textsie">+</label>
                                                        @elseif('-' == $formulaValues)
                                                            <label class="textsie">-</label>
                                                        @elseif('*' == $formulaValues)
                                                            <label class="textsie">*</label>
                                                        @elseif('/' == $formulaValues)
                                                            <label class="textsie">/</label>
                                                        @endif
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        {{--<span style="font-size: 12px; color: #9D9D9E;">--}}
                                            {{--<strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;--}}
                                            {{--<input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;--}}
                                        {{--</span>--}}
                                        <div style="float: right;padding: 15px 0px 0px 8px;">
                                        </div>

                                        <div style="float: left; padding: 0 0 0 12px;">
                                            <strong style="color: red;font-size: 15px;"> *</strong> <span style="color: #999;font-size: 12px;"> Required ? </span>&nbsp;&nbsp;
                                            <input type="checkbox" value="1" class="updateGridRequired" data-id="{{ $girdField->id }}" data-docketfieldid="{{ $item->id }}" name="required" @if($girdField->required==1) checked @endif> &nbsp;&nbsp;
                                        </div>
                                        <div style="float: left; padding: 0 0 0 12px;">
                                            <span style="color: #999;font-size: 12px;"> Grid Preview ? </span>&nbsp;&nbsp;
                                            <input type="checkbox" value="1" class="updateGridPreview gridPreview{{ $girdField->id }}" data-id="{{ $girdField->id }}" data-docketfieldid="{{ $item->id }}" name="required" @if($girdField->preview_value==1) checked @endif> &nbsp;&nbsp;
                                        </div>
                                        <div style="float: left; padding: 0 0 0 12px;">
                                            <span style="color: #999;font-size: 12px;"> Pdf Name by Value ? </span>&nbsp;&nbsp;
                                            <input type="checkbox" value="1" class="updateGridPdfName gridPdfName{{ $girdField->id }}" data-id="{{ $girdField->id }}" data-docketfieldid="{{ $item->id }}" name="required" @if($girdField->pdf_name_by_value==1) checked @endif> &nbsp;&nbsp;
                                        </div>
                                        <div style="float: left; padding: 0 0 0 12px;">
                                            <span style="font-size: 12px; color: #9D9D9E;">
                                                Hide from Recipient?&nbsp;&nbsp;
                                                <input type="checkbox" value="1" class="docketGridFieldIsHidden" data="{{ $girdField->id }}" data-docketfieldid="{{ $item->id }}"
                                                       name="isHidden" @if($girdField->is_hidden==1) checked @endif> &nbsp;&nbsp;
                                            </span>
                                        </div>


                                    </div>


                                </div>

                            </th>

                        @endforeach

                    </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="gridadd-button pull-right">
            @if($item->deleted_at == null)
                <button type="button" id="removeModularGridDiv"
                        class="btn btn-raised btn-xs btn-info pull-right " data-toggle="modal"
                        data-target="#gridModalUpdate" data-field_id="{{ $item->id }}"><i
                            class="fa fa-plus"></i></button>
             @else
                <button style="background: #e1e8f1"  type="button" id="removeModularGridDiv"
                        class="btn btn-raised btn-xs btn-info pull-right " >
                    <i class="fa fa-plus"></i></button>
            @endif

        </div>

        <div class="clearfix"></div>
        <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">

              <ul style="list-style-type: none;margin: 0;padding: 0; float: left;">
                <li style=" float: left;background: none;">
                     <span style="font-size: 12px; color: #9D9D9E;">
                        <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="22" data-value="Grid"
                                class="btn btn-info btn-xs btn-raised"
                                style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                           <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                    </span>
                </li>

                  <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                  </li>
              </ul>
            @if($item->deleted_at == null)
            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                    data-category_id="{{$item->docket_field_category_id}}"
                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right; background: #15B1B8;">
                <i class="fa fa-files-o" aria-hidden="true"></i> Clone
            </button>
            @endif

            <div class="clearfix"></div>
        </div>
    </div>

</div>

