@if($item->docket_field_category_id==1)

    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
         id="shortTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
            <div  @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                  @endif style="display: inline-block; margin-bottom: 10px;">
                <a href="#" id="shortText"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                @if($item->deleted_at == null)
                      <button type="button" id="removeShortText"
                        class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                        data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                  @else

                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                            data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>

                @endif

                <div class="form-group">
                    @if($item->deleted_at == null)
                    <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                            data-toggle="modal" data-target="#setPrefiller"
                            data-field_id="{{ $item->id }}"
                            data-docket_id ="{{$item->docket_id}}"
                            data-is_dependent_data = "{{$item->is_dependent}}"
                            style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                        Prefiller
                    </button>
                    @endif
                    <input id="title" type="text"
                           style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;" disabled
                           class="form-control" name="title" placeholder="Short Text" value="{{ old('title') }}"
                           required autofocus>
                </div>
            </div>

            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                <ul style="list-style-type: none;margin: 0;padding: 0;">
                    <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                    </li>
                    <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                    </li>
                    <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                         </span>
                    </li>

                    <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Is Email Subject?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketIsEmailSubject" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_emailed_subject==1) checked @endif> &nbsp;&nbsp;
                        </span>
                    </li>

                    <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                               data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Short Text"
                               class="btn btn-info btn-xs btn-raised"
                               style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                        </span>
                    </li>
                </ul>
                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                    <li style=" float: left;background: none;">
                        <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                        data-category_id="{{$item->docket_field_category_id}}"
                                        class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                        style="    margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;     background: #15B1B8;"><i class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                    </li>
                </ul>

                <div class="clearfix"></div>

{{--                <span class="docketprefiller" style="font-size: 12px; margin-top: 7px;">--}}
{{--                    <div style="float: left;">--}}
{{--                        <b> Prefillers</b>--}}
{{--                    </div>--}}
{{--                    <div style="position: absolute;right: 31px;">--}}
{{--                            <div class="prtefillerButtonSection{{ $item->id }} pull-right">--}}
{{--                                  @if(@$finalPrefillerView)--}}
{{--                                    @if(in_array($item->id, array_column($finalPrefillerView, 'id')) == true)--}}
{{--                                        <button type="button"--}}
{{--                                                class="btn btn-danger btn-xs btn-raised pull-right clickToshowprefiller showHideButton{{ $item->id }}"--}}
{{--                                                docketFeildIdForPrefiller="{{ $item->id }}"--}}
{{--                                                style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;">Show</button>--}}
{{--                                        <a href="{{ url('dashboard/company/docketBookManager/designDocket/deleteAllPreFiller/'.$item->id) }}"--}}
{{--                                           class="btn btn-danger btn-xs btn-raised pull-right"--}}
{{--                                           style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;  text-transform: lowercase;"><i--}}
{{--                                                    class="fa fa-minus"></i>&nbsp;Clear All</a>--}}
{{--                                    @endif--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <button data-toggle="modal" data-target="#prefillers" data-id="{{$item->id}}"--}}
{{--                                    data-label="{{$item->label}}" data-prefillertype="0"--}}
{{--                                    class="btn btn-info btn-xs btn-raised pull-left"--}}
{{--                                    style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;"><i--}}
{{--                                        class="fa fa-plus"></i> Add</button>--}}
{{--                    </div>--}}

{{--                    <div class="clearfix"></div>--}}
{{--                </span>--}}
                <div class="clearfix"></div>
{{--                <hr class="prefilerLine">--}}

{{--                @if(@$finalPrefillerView)--}}
{{--                    <div id="prefillerValueWrapper{{ $item->id }}">--}}
{{--                        @if(in_array($item->id, array_column($finalPrefillerView, 'id')) == true)--}}
{{--                            <table style="display: block;overflow-x: auto;white-space: nowrap;width: 709px;padding-bottom: 15px;height: 85px; overflow-y: hidden; margin-bottom: 10px;">--}}
{{--                                @foreach($finalPrefillerView as $data)--}}
{{--                                    @if($item->id == $data['id'])--}}
{{--                                        {!! $data['final'] !!}--}}
{{--                                    @endif--}}
{{--                                @endforeach--}}
{{--                            </table>--}}
{{--                            <div style="height: 35px; margin-left: -14px;margin-bottom: -14px;  background-image: linear-gradient(to bottom, rgba(255,0,0,0), #d6d5d5); text-align: center;">--}}

{{--                                <div class="bg"></div>--}}
{{--                                <div class="button"><a class="prefillerDownButton"><i class="fa fa-chevron-down"--}}
{{--                                                                                      aria-hidden="true"></i></a>--}}

{{--                                </div>--}}
{{--                                @else--}}
{{--                                    <p style="color: #adacac;text-align: center;"--}}
{{--                                       class="prefillerEmptyView{{ $item->id }}">Empty</p>--}}
{{--                                @endif--}}
{{--                            </div>--}}


{{--                        @else--}}
{{--                            <div id="prefillerValueWrapper{{ $item->id }}">--}}
{{--                                <p style="color: #adacac;text-align: center;" class="prefillerEmptyView{{ $item->id }}">--}}
{{--                                    Empty</p>--}}
{{--                            </div>--}}
{{--                        @endif--}}
                    </div>

            </div>
        </div><!--shortText field-->

        @endif

@if($item->docket_field_category_id==2)
            <div class="col-md-12 longTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="longTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>

                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a href="#" id="longText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                            @else
                            <button type="button" id="removeShortText"
                                          class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                          data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            @if($item->deleted_at == null)
                            <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                                    data-toggle="modal" data-target="#setPrefiller"
                                    data-field_id="{{ $item->id }}"
                                    data-docket_id ="{{$item->docket_id}}"
                                    data-is_dependent_data = "{{$item->is_dependent}}"
                                    style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                                Prefiller
                            </button>
                            @endif
                            <input disabled
                                   style=" background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;cursor: pointer;     padding-left: 10px;"
                                   id="description" type="text" class="form-control" name="description"
                                   placeholder="Long Text" value="{{ old('description') }}" required autofocus>
                        </div>
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span class=""
                              style="font-size: 12px;color: #9D9D9E; @if($tempDocket->invoiceable!=1) display:none @endif">
                            Invoice Description&nbsp;&nbsp;
                            <input type="checkbox" class="docketInvoiceCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketInvoiceField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Is Email Subject?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketIsEmailSubject" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_emailed_subject==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>



                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Long Text"
                                    class="btn btn-info btn-xs btn-raised"
                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                        </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                            <li style=" float: left;background: none;">
                                <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                            </li>

                        </ul>

                        <div class="clearfix"></div>



                    </div>
                </div>
            </div>
@endif

@if($item->docket_field_category_id==3)
            <div class="col-md-12 docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="numDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>

                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                <a href="#" id="numText"
                   class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>

                        @endif

                        <div class="form-group">
                            @if($item->deleted_at == null)
                            <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                                    data-toggle="modal" data-target="#setPrefiller"
                                    data-field_id="{{ $item->id }}"
                                    data-docket_id ="{{$item->docket_id}}"
                                    data-is_dependent_data = "{{$item->is_dependent}}"
                                    style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                                Prefiller
                            </button>
                            @endif
                            <input style=" background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;cursor: pointer;     padding-left: 10px;"
                                   id="hourlyRate" type="text" class="form-control" name="hourlyRate"
                                   placeholder="Number Label" value="{{ old('hourlyRate') }}" required autofocus
                                   disabled>
                        </div>
                        {{--            <button type="button" id="removeNum" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}


                        <div class="col-md-4">
                            <div class="docketFieldNumber">
                                <h5 style="float: left;    font-weight: 600; color: #9f9f9f;">Min:</h5> <span
                                        @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                        @endif id="unitRateEdit"
                                        style="display: block !important;     padding: 5px 0px 0px 38px;">
                         <a href="#" id="1"
                            class="docketFieldNumbereditable editable-empty @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketFieldNumbers->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateFieldNumber') }}"
                            data-type="1" data-title="Enter Label Text">{{ @$item->docketFieldNumbers->min }}</a>
                    </span>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="docketFieldNumber">
                                <h5 style="float: left;    font-weight: 600; color: #9f9f9f;">Max:</h5> <span
                                        @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                        @endif id="unitRateEdit"
                                        style="display: block !important;padding: 5px 0px 0px 40px;">
                         <a href="#" id="2"
                            class="docketFieldNumbereditable editable-empty @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketFieldNumbers->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateFieldNumber') }}"
                            data-type="2" data-title="Enter Label Text">{{  @$item->docketFieldNumbers->max }}</a>
                    </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="docketFieldNumber">
                                <h5 style="float: left;    font-weight: 600; color: #9f9f9f;">Tolerance % :</h5> <span
                                        @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                        @endif id="unitRateEdit"
                                        style="display: block !important; padding: 5px 0px 0px 76px;">
                         <a href="#" id="3"
                            class="docketFieldNumbereditable editable-empty @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketFieldNumbers->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/updateFieldNumber') }}"
                            data-type="3" data-title="Enter Label Text">{{ @$item->docketFieldNumbers->tolerance }}</a>
                    </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    Is Email Subject?&nbsp;&nbsp;
                                    <input type="checkbox" value="1" class="docketIsEmailSubject" data="{{ $item->id }}"
                                           name="isHidden" @if($item->is_emailed_subject==1) checked @endif> &nbsp;&nbsp;
                                </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Number"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>
                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">

                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>




                    </div>

                </div>
            </div><!--/.numDiv-->
        @endif

@if($item->docket_field_category_id==4)
            <div class="col-md-12 locationDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="locationDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a href="#" id="shortText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            @if($item->deleted_at == null)
                            <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                                    data-toggle="modal" data-target="#setPrefiller"
                                    data-field_id="{{ $item->id }}"
                                    data-docket_id ="{{$item->docket_id}}"
                                    data-is_dependent_data = "{{$item->is_dependent}}"
                                    style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                                Prefiller
                            </button>
                            @endif
                            <input id="location"
                                   style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                   disabled="disabled" type="text" class="form-control" name="location"
                                   placeholder="Location" value="{{ old('location') }}" required autofocus>

                        </div>
                        {{--                <button type="button" id="removeLocation" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Is Email Subject?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketIsEmailSubject" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_emailed_subject==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Location"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>

                    </div>

                </div>

            </div>
        @endif

@if($item->docket_field_category_id==5)
            <div class="col-md-12 imageDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="imagesDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a href="#" id="imageText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            <input id="hourlyRate"
                                   style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                   disabled="disabled" type="text" class="form-control" name="hourlyRate"
                                   placeholder="Image" value="{{ old('hourlyRate') }}" required autofocus>
                        </div>
                        {{--                <button type="button" id="removeImage" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Image"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>

                    </div>


                </div>

            </div>
        @endif

@if($item->docket_field_category_id==6)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="dateTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="margin-bottom: 10px;">
                    <a href="#" id="shortText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input id="title"
                                   style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                   disabled type="text" class="form-control" name="title" placeholder="Date"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                        {{--                <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                    </div>


                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Time?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="timeRequired" data="{{ $item->id }}"
                                   name="timeRequired" @if(@$item->docketFieldDateOption->time==1) checked @endif> &nbsp;&nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Date"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>


                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div><!--shortText field-->
        @endif

@if($item->docket_field_category_id==7)

            <div class="col-md-12 unitRateDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="unitRateDiv " fieldId="{{ $item->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                            <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>

                                <div class="col-md-6">
                            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                  @endif id="unitRateEdit">
                             <a href="#" id="shortText"
                                class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                                data-type="text" data-pk="{{ $item->unitRate[0]->id }}"
                                data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldUnitFieldLabelUpdate') }}"
                                data-title="Enter Label Text">{{ $item->unitRate[0]->label }}</a>
                        </span>

                                    <div class="form-group" style="min-width: 150px;">
                                        <input disabled id="hours"
                                               style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                               type="text" class="form-control" placeholder="Unit Rate" name="hours"
                                               required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-right: 0px;">
                        <span class="unitRateEdit">
                             <a href="#" id="shortText" class="editable" data-type="text"
                                data-pk="{{ $item->unitRate[1]->id }}"
                                data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldUnitFieldLabelUpdate') }}"
                                data-title="Enter Label Text">{{ $item->unitRate[1]->label }}</a>
                        </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input id="to"
                                               style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                               disabled="disabled" placeholder="Total Unit" type="text"
                                               class="form-control" name="to" required autofocus>
                                    </div>
                                    @if($item->deleted_at == null)
                                    <button type="button" id="removeLongText"
                                            class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                            data-id="{{ $item->id }}" style="    position: absolute;top: 0px;"><i
                                                class="fa fa-trash-o"></i></button>
                                    @else
                                        <button type="button" id="removeShortText"
                                                class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                                data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                                    @endif

                                    {{--                            <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                                <ul style="list-style-type: none;margin: 0;padding: 0;">
                                    <li style=" float: left;">
                                <span style="font-size: 12px; color: #9D9D9E;">
                                    Docket Preview
                                    <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                           @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                                </span>
                                    </li>
                                    <li style=" float: left;">
                                <span style="font-size: 12px; color: #9D9D9E;">
                                   <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                                    <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                           name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                                </span>
                                    </li>
                                    <li style=" float: left;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    Hide from Recipient?&nbsp;&nbsp;
                                    <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                           name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                                </span>
                                    </li>

                                    <li style=" float: left;">
                                    <span style="font-size: 12px; color: #9D9D9E; @if($tempDocket->invoiceable!=1) display:none; @endif">
                                        Invoice Amount&nbsp;&nbsp;
                                        <input type="checkbox" class="docketInvoiceCheckboxInput" data="{{ $item->id }}"
                                               @if(@count($item->docketInvoiceField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;
                                    </span>
                                    </li>

                                    <li style=" float: left;background: none;">
                                         <span style="font-size: 12px; color: #9D9D9E;">
                                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="7" data-value="Unit Rate"
                                                    class="btn btn-info btn-xs btn-raised"
                                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                        </span>
                                    </li>
                                    <div class="clearfix"></div>

                                </ul>

                                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                                    <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                                    </li>

                                </ul>
                                <div class="clearfix"></div>
                            </div>


                            {{--                    <span style="font-size: 12px;">--}}
                            {{--                        Docket Preview--}}
                            {{--                        <input type="checkbox" class="docketPreviewCheckboxInput"  data="{{ $item->id }}"  @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                            {{--                    </span>--}}
                            {{--                    <span style="font-size: 12px;">--}}
                            {{--                        <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;--}}
                            {{--                        <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;--}}
                            {{--                    </span>--}}
                            {{--                    <span style="font-size: 12px;">--}}
                            {{--                        Hide from Recipient?&nbsp;&nbsp;--}}
                            {{--                        <input type="checkbox" value="1" class="docketFieldIsHidden"  data="{{ $item->id }}" name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;--}}
                            {{--                    </span>--}}
                            <div class="clearfix"></div>


                        </div>
                    </div>
                </div>
            </div>

        @endif

@if($item->docket_field_category_id==8)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="shortTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeShortText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                              @endif style="display: block !important;">
                    <a href="#" id="shortText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>

                        <div class="form-group">
                            <input id="checkbox"
                                   style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                   type="text" disabled class="form-control" name="checkbox" placeholder="Checkbox"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="8" data-value="Check Box"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div><!--checkbox field-->




        @endif

@if($item->docket_field_category_id==9)
            <div class="col-md-12 signatureDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="signatureDiv " fieldId="{{ $item->id }}" style="margin-bottom: 15px;margin-top: 15px;">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a href="#" id="shortText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeSignature"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        {{--              <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}

                        <div class="form-group">
                            @if($item->deleted_at == null)
                            <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                                    data-toggle="modal" data-target="#setPrefiller"
                                    data-field_id="{{ $item->id }}"
                                    data-docket_id ="{{$item->docket_id}}"
                                    data-is_dependent_data = "{{$item->is_dependent}}"
                                    style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                                Prefiller
                            </button>
                            @endif
                            <input id="signature"
                                   style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                   type="text" class="form-control" disabled name="signature" placeholder="Signature"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                        <div class="name">
                            {{--<h5 style="font-weight: 600;color: #9f9f9f;">Name</h5>--}}
                            <div class="form-group">
                                <input id="signature"
                                       style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                       type="text" class="form-control" disabled name="signature" placeholder="Name"
                                       value="{{ old('title') }}" required autofocus>
                                <span style="font-size: 12px;    position: absolute; right: 0;top: 8px;">
                            <strong style="color: red;font-size: 15px;"> *</strong> Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="0" class="docketImageNamefieldrequired" data="{{ $item->id }}"
                                   name="required" @if(@$item->docketFieldSignatureOption->name==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </div>
                        </div>
                    </div>


                    {{--                <span class="docketprefiller" style="font-size: 12px;     margin-top: 7px;">--}}
                    {{--                    <div style="float: left;">--}}
                    {{--                        <b> Prefillers</b>--}}
                    {{--                      <button data-toggle="modal" data-target="#prefillers" data-id="{{$item->id}}" data-label="{{$item->label}}" data-prefillertype="0"  class="btn btn-info btn-xs btn-raised" style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;"><i class="fa fa-plus"></i></button>--}}
                    {{--                      <a href="{{ url('dashboard/company/docketBookManager/designDocket/deleteAllPreFiller/'.$item->id) }}" class="btn btn-danger btn-xs btn-raised" style="margin: 0px 0px 2px 8px;     padding: 0px 3px 0px 3px;"><i class="fa fa-minus"></i>&nbsp;Clear All</a>--}}
                    {{--                    </div>--}}
                    {{--                    <div style="float: right;position: absolute;right: 31px;"><a data-toggle="modal" data-target="#linkPrefiller" data-id="{{$item->id}}" data-label="{{$item->label}}">Link to Prifiller</a>--}}
                    {{--                    </div>--}}

                    {{--                    <div class="clearfix"></div>--}}
                    {{--                    @if(@$finalPrefillerView)--}}

                    {{--                         <div id="prefillerValueWrapper{{ $item->id }}">--}}
                    {{--                               <br>--}}
                    {{--                             <table style="display: block;overflow-x: auto;white-space: nowrap;width: 709px;padding-bottom: 15px;">--}}
                    {{--                                 @foreach($finalPrefillerView as $data)--}}
                    {{--                                             @if($item->id == $data['id'])--}}
                    {{--                                                 {!! $data['final'] !!}--}}
                    {{--                                             @endif--}}
                    {{--                                         @endforeach--}}
                    {{--                              </table>--}}
                    {{--                         </div>--}}
                    {{--                        @endif--}}

                    {{--                </span>--}}

                    {{--                <div class="clearfix"></div>--}}
                    {{--           --}}
                    {{--            <span style="font-size: 12px;">--}}
                    {{--                Docket Preview--}}
                    {{--                <input type="checkbox" class="docketPreviewCheckboxInput"  data="{{ $item->id }}"  @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;&nbsp;&nbsp;--}}
                    {{--            </span>--}}

                    {{--            <span style="font-size: 12px;">--}}
                    {{--               <strong style="color: red;font-size: 15px;"> *</strong> Required ?&nbsp;&nbsp;--}}
                    {{--                <input type="checkbox" value="0" class="docketfieldrequired" data="{{ $item->id }}" name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;--}}
                    {{--            </span>--}}

                    {{--            <span style="font-size: 12px;">--}}
                    {{--                Hide from Recipient?&nbsp;&nbsp;--}}
                    {{--                <input type="checkbox" value="1" class="docketFieldIsHidden"  data="{{ $item->id }}" name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;--}}
                    {{--            </span>--}}
                    {{--            <br>--}}


                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Signature"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>



                    </div>

                </div>
            </div><!--shortText field-->
        @endif

@if($item->docket_field_category_id==14)
            <div class="col-md-12 imageDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="sketchPadDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a href="#" id="sketchPadText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeSketchPad"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            <input id="sketchPad"
                                   style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                   type="text" class="form-control" disabled name="sketchPad" placeholder="Sketch Pad"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                    </div>
                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Sketch Pad"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        @endif

@if($item->docket_field_category_id==12)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="headerDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="display: inline-block;">
                <a href="#" id="header"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeHeader"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            <input id="title"
                                   style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                   type="text" disabled class="form-control" name="title" placeholder="Header"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Header/Title"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                            <li style=" float: left;background: none;">

                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        @endif

@if($item->docket_field_category_id==16)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="headerDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                      @endif style="display: inline-block;">
                    <a href="#" id="header"
                       class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeHeader"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="form-group">
                            <input id="title" type="text"
                                   style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                   disabled class="form-control" name="title" placeholder="Bar Code"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Bar Code Scanner"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>

                </div>
            </div>
        @endif

@if($item->docket_field_category_id==18)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="headerDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>

              <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="display: inline-block;">
                <a href="#" id="header"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                        @if($item->deleted_at == null)
                        <button style="    position: absolute;right: 17px;" type="button" id="removeHeader"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="clearfix"></div>


                        <br>
                        <div id="yesNoNaLabelTypes1">

                            <label style="    color: #484848;font-size: 14px;margin-right: 6px;margin-top: 13px;margin-bottom: 0px;">Label
                                Type:</label>
                            <select title="Pick a number" class="selectpicker" yesnoSelectId="{{$item->id}}">
                                <option value="0"
                                        @if(@$item->yesNoField[0]->label_type || @$item->yesNoField[1]->label_type || @$item->yesNoField[2]->label_type  == 0) selected @else @endif>
                                    Text
                                </option>
                                <option value="1"
                                        @if(@$item->yesNoField[0]->label_type || @$item->yesNoField[1]->label_type || @$item->yesNoField[2]->label_type == 1) selected @else @endif>
                                    Icon
                                </option>
                            </select>
                            <div class="form-group">

                                <div class="col-md-4 yesnofield" style="padding-right: 0px;">
                                    @if(@$item->yesNoField[1]->label_type==1)
                                        <a class="labelTypePop label-type-icon"
                                           popupyesnofieldid="{{@$item->yesNoField[1]->id}}"
                                           labelpopupFieldid="{{ @$item->id }}" class="label-type-icon">
                           <span class="iconBackground{{ @$item->yesNoField[1]->id}}"
                                 style="display: inline-block; background:{{$item->yesNoField[1]->colour}} ">
                            <img style="width: 23px;" src="{{ AmazoneBucket::url() }}{{ @$item->yesNoField[1]->icon_image }}">
                           </span>
                                        </a>
                                    @else
                                        <span style="display: inline-block;">
                         <a href="#" id="shortText" class="editable" data-type="text"
                            data-pk="{{ @$item->yesNoField[1]->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketYesNoFieldLabelUpdate') }}"
                            data-title="Enter Label Text">{{@$item->yesNoField[1]->label }}</a>
                    </span>
                                    @endif
                                    <br><label class="checkbox-inline YesCheckBox{{ @$item->yesNoField[1]->id }}"
                                               style="margin-top: 6px;">Explaination&nbsp;&nbsp; <input type="checkbox"
                                                                                                        class="explanationClick"
                                                                                                        data-subDocketSelected="{{ @$item->yesNoField[1]->id }}"
                                                                                                        data-fieldidss="{{ $item->id }}"
                                                                                                        data-subDocketQuestion="{{ $item->label }}"
                                                                                                        @if(@$item->yesNoField[1]->explanation==1) checked @else @endif >
                                    </label>
                                    <div id="cp2" class="input-group colorpicker colorpicker-component">
                                        <span style="font-size: 15px;line-height: 1.42857143;color: #a4a4a4;font-weight: 400;    margin-left: -4px;">Select Color:</span>
                                        <input type="hidden" id="color" name="color" class="form-control collourPallet"
                                               value="{{ @$item->yesNoField[1]->colour}}"
                                               colorYesNoId="{{ @$item->yesNoField[1]->id}}"/>
                                        <span class="input-group-addon"><i id="color"
                                                                           style="height: 22px;width: 24px;border:1px solid rgb(115, 115, 115);margin-top: -7px;"></i></span>
                                    </div>
                                    @if(@$item->yesNoField[1]->explanation==1)
                                        <a class="explanationClickEdit yesnoExplanationEdit{{ @$item->yesNoField[1]->id }}"
                                           data-subDocketSelectedEdit="{{ @$item->yesNoField[1]->id }}"
                                           data-fieldidsEdit="{{ $item->id }}"
                                           data-subDocketQuestionEdit="{{ $item->label }}"
                                           data-explanationEdit="{{ @$item->yesNoField[1]->explanation }}">Edit
                                            Sub-Docket</a>
                                    @endif
                                </div>
                                <div class="col-md-4 yesnofield" style="padding-right: 0px;">
                                    @if(@$item->yesNoField[0]->label_type==1)
                                        <a class="labelTypePop label-type-icon"
                                           popupyesnofieldid="{{@$item->yesNoField[0]->id}}"
                                           labelpopupFieldid="{{ $item->id }}" class="label-type-icon">
                                           <span class="iconBackground{{ @$item->yesNoField[0]->id}}"
                                                 style="display: inline-block; background:{{$item->yesNoField[0]->colour}} ">
                                            <img style="width: 23px;" src="{{ AmazoneBucket::url() }}{{ @$item->yesNoField[0]->icon_image }}">
                                           </span>
                                        </a>
                                    @else
                                        <span style="display: inline-block;">
                                             <a href="#" id="shortText" class="editable" data-type="text"
                                                data-pk="{{ @$item->yesNoField[0]->id }}"
                                                data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketYesNoFieldLabelUpdate') }}"
                                                data-title="Enter Label Text">{{ @$item->yesNoField[0]->label }}</a>
                                        </span>
                                    @endif
                                    <br>
                                    <label class="checkbox-inline NoCheckBox{{ @$item->yesNoField[0]->id }}"
                                           style="margin-top: 6px;">Explaination&nbsp;&nbsp; <input type="checkbox"
                                                                                                    class="explanationClick"
                                                                                                    data-subDocketSelected="{{ @$item->yesNoField[0]->id }}"
                                                                                                    data-fieldidss="{{ $item->id }}"
                                                                                                    data-subDocketQuestion="{{ $item->label }}"
                                                                                                    @if(@$item->yesNoField[0]->explanation==1) checked @else @endif >
                                    </label>
                                    <div id="cp2" class="input-group colorpicker colorpicker-component">
                                        <span style="font-size: 15px;line-height: 1.42857143;color: #a4a4a4;font-weight: 400;    margin-left: -4px;">Select Color:</span>
                                        <input type="hidden" id="color" name="color" class="form-control collourPallet"
                                               value="{{ @$item->yesNoField[0]->colour}}"
                                               colorYesNoId="{{ @$item->yesNoField[0]->id}}"/>
                                        <span class="input-group-addon"><i id="color"
                                                                           style="height: 22px;width: 24px;border:1px solid rgb(115, 115, 115);margin-top: -7px;"></i></span>
                                    </div>
                                    @if(@$item->yesNoField[0]->explanation==1)

                                        <a class="explanationClickEdit yesnoExplanationEdit{{ @$item->yesNoField[0]->id }}"
                                           data-subDocketSelectedEdit="{{ @$item->yesNoField[0]->id }}"
                                           data-fieldidsEdit="{{ $item->id }}"
                                           data-subDocketQuestionEdit="{{ $item->label }}"
                                           data-explanationEdit="{{ @$item->yesNoField[0]->explanation }}">Edit
                                            Sub-Docket</a>
                                    @endif
                                </div>
                                <div class="col-md-4 yesnofield" style="padding-right: 0px;">
                                    @if(@$item->yesNoField[2]->label_type==1)
                                        <a class="labelTypePop label-type-icon"
                                           popupyesnofieldid="{{@$item->yesNoField[2]->id}}"
                                           labelpopupFieldid="{{ $item->id }}" class="label-type-icon">
                           <span class="iconBackground{{ @$item->yesNoField[2]->id}}"
                                 style="display: inline-block; background:{{$item->yesNoField[2]->colour}} ">
                            <img style="width: 23px;" src="{{ AmazoneBucket::url() }}{{ @$item->yesNoField[2]->icon_image }}">
                           </span>
                                        </a>
                                    @else
                                        <span style="display: inline-block;">
                         <a href="#" id="shortText" class="editable" data-type="text"
                            data-pk="{{ @$item->yesNoField[2]->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketYesNoFieldLabelUpdate') }}"
                            data-title="Enter Label Text">{{ @$item->yesNoField[2]->label }}</a>
                    </span>
                                    @endif
                                    <br><label class="checkbox-inline NaCheckBox{{ @$item->yesNoField[2]->id }}"
                                               style="margin-top: 6px;">Explaination&nbsp;&nbsp; <input type="checkbox"
                                                                                                        class="explanationClick"
                                                                                                        data-subDocketSelected="{{ @$item->yesNoField[2]->id }}"
                                                                                                        data-fieldidss="{{ $item->id }}"
                                                                                                        data-subDocketQuestion="{{ $item->label }}"
                                                                                                        @if(@$item->yesNoField[2]->explanation==1) checked @else @endif>
                                    </label>
                                    <div id="cp2" class="input-group colorpicker colorpicker-component">
                                        <span style="font-size: 15px;line-height: 1.42857143;color: #a4a4a4;font-weight: 400;    margin-left: -4px;">Select Color:</span>
                                        <input type="hidden" id="color" name="color" class="form-control collourPallet"
                                               value="{{ @$item->yesNoField[2]->colour}}"
                                               colorYesNoId="{{ @$item->yesNoField[2]->id}}"/>
                                        <span class="input-group-addon"><i id="color"
                                                                           style="height: 22px;width: 24px;border:1px solid rgb(115, 115, 115);margin-top: -7px;"></i></span>
                                    </div>
                                    @if(@$item->yesNoField[2]->explanation==1)
                                        <a class="explanationClickEdit yesnoExplanationEdit{{ @$item->yesNoField[2]->id }}"
                                           data-subDocketSelectedEdit="{{ @$item->yesNoField[2]->id }}"
                                           data-fieldidsEdit="{{ $item->id }}"
                                           data-subDocketQuestionEdit="{{ $item->label }}"
                                           data-explanationEdit="{{ @$item->yesNoField[2]->explanation }}">Edit
                                            Sub-Docket</a>
                                    @endif
                                </div>
                            </div>

                        </div>

                    </div>


                    {{--            <span style="font-size: 12px;">--}}
                    {{--                Docket Preview--}}
                    {{--                <input type="checkbox" class="docketPreviewCheckboxInput"   data="{{ $item->id }}"  @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;--}}
                    {{--            </span>--}}
                    {{--            <span style="font-size: 12px;">--}}
                    {{--                Hide from Recipient?&nbsp;&nbsp;--}}
                    {{--                <input type="checkbox" value="1" class="docketFieldIsHidden"  data="{{ $item->id }}" name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;--}}
                    {{--            </span>--}}

                    <div class="clearfix"></div>
                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>


                            <li style=" float: left;background: none;">
                                         <span style="font-size: 12px; color: #9D9D9E;">
                                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="18" data-value="Yes/No-N/a Checkbox"
                                                    class="btn btn-info btn-xs btn-raised"
                                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                        </span>
                            </li>



                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>


                </div>
            </div>
        @endif

@if($item->docket_field_category_id==15)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="documentDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>

                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="display: inline-block;">
                <a href="#" id="document"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeDocument"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        {{--<button  style="    margin-right: 48px;" data-toggle="modal" data-target="#modalDocument" class="btn btn-xs btn-raised  btn-info pull-right" ><i class="fa fa-plus-square"></i>  Add</button>--}}
                        <div class="form-group">
                            <div style="background: #f0f0ee;">

                                <div style="background: #f0f0ee;         padding: 0px 0px 0px 22px;">


                                    <table width="100%">
                                        @if($item->docketAttached )
                                            <?php $i = 1 ?>
                                            @foreach($item->docketAttached as $row)
                                                <tr>
                                                    <td><?php echo $i ?>.</td>
                                                    <td><a style="color: #000; text-decoration: none;"
                                                           href="{{ AmazoneBucket::url() }}{{ $row->url }}"
                                                           target="_blank">{{$row->name}}</a></td>
                                                    {{--<td><img src="{{asset($row->url)}}"></td>--}}
                                                    <td>
                                                        <button type="button"
                                                                class="btn btn-raised btn-xs btn-danger deleteDocumentAttached"
                                                                fieldId="{{ $row->id }}"
                                                                style="background: transparent;color: red;border: none;box-shadow: none;font-size: 14px;">
                                                            <i class="fa fa-remove"></i></button>
                                                    </td>
                                                </tr>
                                                <?php $i++ ?>
                                            @endforeach
                                        @endif
                                    </table>


                                </div>
                                <select class="multiple" id="multiple{{ $item->id }}" name="name"
                                        fieldId="{{ $item->id }}">
                                    <option data-placeholder="true"></option>
                                    @if($docketDocument)
                                        @foreach($docketDocument as $row)
                                            <option value="{{$row->id}}">{{$row->name}}</option>
                                        @endforeach
                                    @endif
                                </select>

                            </div>
                        </div>
                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Document"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>


                </div>
            </div>
            <script>
                $(document).ready(function () {
                    new SlimSelect({
                        select: '#multiple{{ $item->id }}',
                        placeholder: 'Select Pdf file',
                        onChange: function (info) {
                            updateAttachment("{{  $item->id }}", info["value"]);
                            console.log(info)
                        }

                    });
                });
            </script>
        @endif

@if($item->docket_field_category_id==20)

            <div class="col-md-12 unitRateDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="manualTimeDiv " fieldId="{{ $item->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                            <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                    <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                          @endif style="display: inline-block;    margin-bottom: 10px; float: left">
                        <a href="#" id="header"
                           class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                           data-type="text" data-pk="{{ $item->id }}"
                           data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                           data-title="Enter Label Text">{{ $item->label }}</a>
                    </span>
                                @if($item->deleted_at == null)
                                <button type="button" id="removeShortText"
                                        class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                        data-id="{{ $item->id }}" style="    float: right;"><i
                                            class="fa fa-trash-o"></i></button>
                                @else
                                    <button type="button" id="removeShortText"
                                            class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                            data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                                @endif

                                <div class="clearfix"></div>
                                <br>
                                <div class="col-md-6">
                        <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif id="unitRateEdit"
                              style="display: block !important;">
                         <a href="#" id="shortText"
                            class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketManualTimer[0]->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldManualTimerLabelUpdate') }}"
                            data-title="Enter Label Text">{{ @$item->docketManualTimer[0]->label }}</a>
                    </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                               disabled id="hours" type="text" class="form-control" placeholder="From"
                                               name="from" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6">
                        <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif id="unitRateEdit"
                              style="display: block !important;">
                         <a href="#" id="shortText"
                            class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketManualTimer[1]->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldManualTimerLabelUpdate') }}"
                            data-title="Enter Label Text">{{ @$item->docketManualTimer[1]->label }}</a>
                    </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                               disabled id="hours" type="text" class="form-control" placeholder="To"
                                               name="to" required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-12">
                        <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif id="unitRateEdit"
                              style="display: block !important; float: left">
                         <a href="#" id="shortText"
                            class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                            data-type="text" data-pk="{{ @$item->docketManualTimerBreak[0]->id }}"
                            data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldManualTimerBreakLabelUpdate') }}"
                            data-title="Enter Label Text">{{ @$item->docketManualTimerBreak[0]->label }}</a>
                    </span>
                                    <span style="font-size: 12px; float: right;">
                          Require explanation?
                            <input type="checkbox" value="1" class="expnanationType"
                                   data="{{ @$item->docketManualTimerBreak[0]->id }}" name="expnanationType"
                                   @if(@$item->docketManualTimerBreak[0]->explanation==1) checked @endif > &nbsp;&nbsp;
                        </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                               disabled id="hours" type="text" class="form-control"
                                               placeholder="Total Break" name="totalbreak" required autofocus>
                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                                <ul style="list-style-type: none;margin: 0;padding: 0;">
                                    <li style=" float: left;">
                                        <span style="font-size: 12px; color: #9D9D9E;">
                                           <strong style="color: red;font-size: 15px;"> *</strong>  Required? &nbsp;&nbsp;
                                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                                        </span>
                                    </li>
                                    <li style=" float: left;">
                                         <span style="font-size: 12px; color: #9D9D9E;">
                                            Hide from Recipient?&nbsp;&nbsp;
                                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                                        </span>
                                    </li>
                                    <li style="float: left;background: none;">

                                        <button style="margin: 0;" class="btn btn-info btn-xs btn-raised manualTimeFormat"  data-fieldid="{{$item->id}}"   data-timeformat="{{$item->time_format}}" >Time Format</button>


                                    </li>


                                    <li style=" float: left;background: none;">
                                         <span style="font-size: 12px; color: #9D9D9E;">
                                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="20" data-value="Manual Timer"
                                                    class="btn btn-info btn-xs btn-raised"
                                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                        </span>
                                    </li>


                                </ul>
                                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">


                                    <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;    background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                                    </li>

                                </ul>
                                <div class="clearfix"></div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>


        @endif

@if($item->docket_field_category_id == 28)
    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
         id="shortTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
            <div  @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                  @endif style="display: inline-block; margin-bottom: 10px;">
                <a href="#" id="shortText"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                @if($item->deleted_at == null)
                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                            data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                @else

                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                            data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>

                @endif

                <div class="form-group">

                    <input id="title" type="text"
                           style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;" disabled
                           class="form-control" name="title" placeholder="Folder" value="{{ old('title') }}"
                           required autofocus>
                </div>
            </div>

            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                <ul style="list-style-type: none;margin: 0;padding: 0;">

                    <li style="float: left;background: none;">
                     <button style="margin: 0;" class="btn btn-info btn-xs btn-raised defaultFolder"  data-fieldid="{{$item->id}}"   data-default="{{$item->folder_default_id}}" >Default Folder</button>
                    </li>





                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
    </div><!--shortText field-->


@endif


@if($item->docket_field_category_id == 29)
    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
         id="shortTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
            <div  @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                  @endif style="display: inline-block; margin-bottom: 10px;">
                <a href="#" id="shortText"
                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                   data-type="text" data-pk="{{ $item->id }}"
                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                   data-title="Enter Label Text">{{ $item->label }}</a>
            </span>
                @if($item->deleted_at == null)

                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                            data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                @else

                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                            data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>

                @endif

                <div class="form-group">
                    @if($item->deleted_at == null)
                        <button class="btn btn-raised btn-xs btn-info updateSetPrefiller"
                                data-toggle="modal" data-target="#setPrefiller"
                                data-field_id="{{ $item->id }}"
                                data-docket_id ="{{$item->docket_id}}"
                                data-is_dependent_data = "{{$item->is_dependent}}"
                                style="position: absolute;right: 0px;top: -9px;padding: 10px; z-index:1;">
                            Prefiller
                        </button>
                    @endif
                    <input id="title" type="text"
                           style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;" disabled
                           class="form-control" name="title" placeholder="Email" value="{{ old('title') }}"
                           required autofocus>
                </div>
            </div>

            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                <ul style="list-style-type: none;margin: 0;padding: 0;">

                    <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                    </li>
                    <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                    </li>

                    <li style="float: left;">
                        <span style="font-size: 12px; color: #9D9D9E">
                             Send a Docket
                             <input type="checkbox" value="1" class="docketSendCopy" data="{{ $item->id }}"
                                    name="isHidden" @if($item->send_copy_docket==1) checked @endif> &nbsp;
                        </span>

                    </li>



                    <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Short Text"
                                    class="btn btn-info btn-xs btn-raised"
                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                        </span>
                    </li>
                </ul>
                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                    <li style=" float: left;background: none;">
                        <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                data-category_id="{{$item->docket_field_category_id}}"
                                class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                style="    margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;     background: #15B1B8;"><i class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                    </li>
                </ul>

                <div class="clearfix"></div>


            </div>

        </div>
    </div>



@endif


@if($item->docket_field_category_id == 30)
    <div class="col-md-12 longTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
         id="longTextDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList"   @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #fbfbdb;"  @endif>

            <div  @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #fbfbdb;"  @endif  >
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif>
                    <a  href="#" id="longText"
                       class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                       data-type="text" data-pk="{{ @$item->docketConstantField->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketConstantLabelUpdate') }}"
                       data-title="Enter Label Text">{{ @$item->docketConstantField->label}}</a>
                </span>
                @if($item->deleted_at == null)
                <button type="button" id="removeLongText"
                        class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                        data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                @else
                    <button type="button" id="removeShortText"
                            class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                            data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                @endif

                <div class="form-group">
                    <input disabled
                           style=" background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;cursor: pointer;     padding-left: 10px;"
                           id="description" type="text" class="form-control" name="description"
                           placeholder="Docket Constant" value="{{ old('description') }}" required autofocus>
                </div>
            </div>

            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                <ul style="list-style-type: none;margin: 0;padding: 0;">

                    <li style=" float: left;background: none;">

                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{ @$item->docketConstantField->label}} ({{$item->fieldCategoryInfo->title}})" data-type="30" data-value="Docket Constant"
                                    class="btn btn-info btn-xs btn-raised"
                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                        </span>
                    </li>
                </ul>

                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                </ul>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
@endif

@if($item->docket_field_category_id==24)
            <div class="col-md-12 tallyableUnitRateDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="tallayableUnitRateDiv " fieldId="{{ $item->id }}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>

                            <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                                <div class="col-md-12">
                            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                  @endif style="display: inline-block; margin-bottom: 22px;" id="unitRateEdit">
                                <a href="#" id="shortText"
                                   class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                                   data-type="text" data-pk="{{ $item->id }}"
                                   data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                                   data-title="Enter Label Text">{{ $item->label }}</a>
                            </span>
                                    @if($item->deleted_at == null)
                                    <button type="button"
                                            class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                            data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                                    @else
                                        <button type="button" id="removeShortText"
                                                class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                                data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                                    @endif

                                </div>
                                <div class="col-md-6">
                             <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                                   @endif id="unitRateEdit">
                               <a href="#" id="shortText"
                                  class="editable  @if($tempDocketFields->first()->id == $item->id)firstEditElement @endif"
                                  data-type="text" data-pk="{{ $item->tallyUnitRate[0]->id }}"
                                  data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketTallyableUnitRateLabelUpdate') }}"
                                  data-title="Enter Label Text">{{ $item->tallyUnitRate[0]->label }}</a>
                             </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input disabled id="hours"
                                               style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                               type="text" class="form-control" placeholder="Unit Rate" name="hours"
                                               required autofocus>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-right: 0px;">
                            <span id="unitRateEdit">
                                 <a href="#" id="shortText" class="editable" data-type="text"
                                    data-pk="{{ $item->tallyUnitRate[1]->id }}"
                                    data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketTallyableUnitRateLabelUpdate') }}"
                                    data-title="Enter Label Text">{{ $item->tallyUnitRate[1]->label }}</a>
                            </span>
                                    <div class="form-group" style="min-width: 150px;">
                                        <input id="to"
                                               style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                               disabled="disabled" placeholder="Total Unit" type="text"
                                               class="form-control" name="to" required autofocus>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>

                            <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                                <ul style="list-style-type: none;margin: 0;padding: 0;">
                                    <li style=" float: left;">
                                <span style="font-size: 12px; color: #9D9D9E;">
                                   <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                                    <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                           name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                                </span>
                                    </li>

                                    <li style=" float: left;background: none;">
                                         <span style="font-size: 12px; color: #9D9D9E;">
                                            <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                                    data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="24" data-value="Tallyable Unit Rate"
                                                    class="btn btn-info btn-xs btn-raised"
                                                    style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                               <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                        </span>
                                    </li>
                                </ul>
                                <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">

                                    <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right; background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                                    </li>

                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endif

@if($item->docket_field_category_id == 25)
            <div class="col-md-12 tallyableValueDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="tallyableValueDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                <span @if($tempDocketFields->first()->id == $item->id)class="fifth"
                      @endif style="display: inline-block; margin-bottom: 10px;">
                    <a href="#" id="shortText"
                       class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif"
                       data-type="text" data-pk="{{ $item->id }}"
                       data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                       data-title="Enter Label Text">{{ $item->label }}</a>
                </span>
                        @if($item->deleted_at == null)
                        <button type="button" class="btn btn-raised btn-xs btn-danger deleteDocketComponent pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif
                        <div class="form-group">
                            <input id="title" type="text"
                                   style="background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;"
                                   disabled class="form-control" name="title" placeholder="Tallyable Value"
                                   value="{{ old('title') }}" required autofocus>
                        </div>

                    </div>

                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                                <span style="font-size: 12px; color: #9D9D9E;">
                                   <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                                    <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                           name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                                </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Talleyable Value"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0;float: right;">

                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right;background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>
                    </div>
                </div><!--shortText field-->
            </div>
        @endif

@if($item->docket_field_category_id==26)
            <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
                 id="dateTextDiv" fieldId="{{ $item->id }}">
                <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                    <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
        <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="margin-bottom: 10px;">
            <a href="#" id="shortText"
               class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif"
               data-type="text" data-pk="{{ $item->id }}"
               data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
               data-title="Enter Label Text">{{ $item->label }}</a>
        </span>
                        @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                                class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                                data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                        @else
                            <button type="button" id="removeShortText"
                                    class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                    data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                        @endif

                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input id="title"
                                   style="    background-color: #ffffff;    border: 1px solid #E7EAF0;    text-indent: 14px;"
                                   disabled type="text" class="form-control" name="title" placeholder="Time"
                                   value="{{ old('title') }}" required autofocus>
                        </div>
                        {{--                <button type="button" id="removeShortText" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>--}}
                    </div>


                    <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                        <ul style="list-style-type: none;margin: 0;padding: 0;">
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                            Docket Preview
                            <input type="checkbox" class="docketPreviewCheckboxInput" data="{{ $item->id }}"
                                   @if(@count($item->docketPreviewField) != 0) checked @endif>&nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                        <span style="font-size: 12px; color: #9D9D9E;">
                           <strong style="color: red;font-size: 15px;"> *</strong>  Required ?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketfieldrequired" data="{{ $item->id }}"
                                   name="required" @if($item->required==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>
                            <li style=" float: left;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            Hide from Recipient?&nbsp;&nbsp;
                            <input type="checkbox" value="1" class="docketFieldIsHidden" data="{{ $item->id }}"
                                   name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
                        </span>
                            </li>

                            <li style=" float: left;background: none;">
                                 <span style="font-size: 12px; color: #9D9D9E;">
                                    <button data-toggle="modal" data-target="#exportMapping" data-id="{{$item->id}}"
                                            data-category_id="{{$item->docket_field_category_id}}" data-title="{{$item->label}} ({{$item->fieldCategoryInfo->title}})" data-type="1" data-value="Time"
                                            class="btn btn-info btn-xs btn-raised"
                                            style="margin: 0px 13px 0px 0px;padding: 0px 5px 0px 5px;float: right;background: #15B1B8;">
                                       <i class="fa fa-files-o" aria-hidden="true"></i> Export</button>
                                </span>
                            </li>

                        </ul>
                        <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">

                            <li style=" float: left;background: none;">
                         <span style="font-size: 12px; color: #9D9D9E;">
                            <button data-toggle="modal" data-target="#duplicateGrid" data-id="{{$item->id}}"
                                    data-category_id="{{$item->docket_field_category_id}}"
                                    class="btn btn-info btn-xs btn-raised cloneDocketComponent"
                                    style="    margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right; background: #15B1B8;"><i
                                        class="fa fa-files-o" aria-hidden="true"></i> clone</button>
                         </span>
                            </li>

                        </ul>
                        <div class="clearfix"></div>

                    </div>
                </div>
            </div><!--shortText field-->
        @endif

@if($item->docket_field_category_id==27)
    @include('dashboard.company.docketManager.docket-template.design-docket.modular-field.advanced-header')
@endif

@if($item->docket_field_category_id==22)
    @include('dashboard.company.docketManager.docket-template.design-docket.modular-field.grid')
@endif

@if($item->docket_field_category_id == 31)
    <div class="col-md-12 shortTextDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif"
        id="dateTextDiv" fieldId="{{ $item->id }}">
        <form class="image_form" enctype="multipart/form-data">
            <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
                <div @if($item->deleted_at != null) style="background-color: #ffe9e9;" @else style="background-color: #F9FAFC;"  @endif>
                    <span @if($tempDocketFields->first()->id == $item->id)class="fifth" @endif style="margin-bottom: 10px;">
                        <a href="#" id="shortText"
                        class="editable  @if($tempDocketFields->first()->id == $item->id) firstEditElement @endif"
                        data-type="text" data-pk="{{ $item->id }}"
                        data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}"
                        data-title="Enter Label Text">{{ $item->label }}</a>
                    </span>

                    @if($item->deleted_at == null)
                        <button type="button" id="removeLongText"
                            class="btn btn-raised btn-xs btn-danger deleteDocketComponent  pull-right"
                            data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i></button>
                    @else
                        <button type="button" id="removeShortText"
                                class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                                data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>
                    @endif

                    <div class="clearfix"></div>
                    <div class="form-group image_instruction">
                        <input id="files" style="opacity: 1;position: inherit;background-color: #ffffff;border: 1px solid #E7EAF0;text-indent: 14px;" multiple
                        type="file" name="files[]" accept="image/*" autofocus class="imagePreview" data-id="{{ $item->id }}"
                        @isset($item->default_value) @if(count(json_decode($item->default_value)) < 0) required @endif @endisset>
                        <div class="form-group pipcontent">
                            @isset($item->default_value)
                                @for($i=0; $i <= count(json_decode($item->default_value)) - 1; $i++)
                                    <span class="pip">
                                        <span class="badge badge-pill badge-danger remove removeImageInstruction"><i class="fa fa-times"></i></span>
                                        <img class="imageThumb" src="{{ json_decode($item->default_value)[$i] }}" />
                                        <input type="hidden" name="oldImageInstruction[]" value="{{ json_decode($item->default_value)[$i] }}">
                                    </span>
                                @endfor
                            @endisset
                        </div>
                    </div>

                </div>


                <div style="background-color: #F3F5F9; min-height: 20px;     margin-left: -14px;padding: 15px 0px 15px 15px;    margin-bottom: -15px; margin-top: 7px;">
                    <ul style="list-style-type: none;margin: 0;padding: 0; float: right;">
                        <input type="hidden" name="field_id" value="{{ $item->id }}">
                        <input type="hidden" name="docket_id" value="{{ $item->docket_id }}">
                        <li style=" float: left;background: none;">
                            <span style="font-size: 12px; color: #9D9D9E;">
                                <button class="btn btn-info btn-xs btn-raised saveImageInstruction"
                                    type="button"
                                        style="margin: 0px 13px 0px 0px;padding: 2px 5px 2px 5px;float: right; background: #15B1B8;"><i
                                            class="fa fa-save"></i> save </button>
                            </span>
                        </li>

                    </ul>
                    <div class="clearfix"></div>

                </div>
            </form>
        </div>
    </div>
@endif
