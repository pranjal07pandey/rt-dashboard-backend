@if($item->docket_field_category_id==13)
    <div class="col-md-12 footerDiv docketField @if($tempDocketFields->first()->id == $item->id) six @endif" id="footerDiv" fieldId="{{ $item->id }}">
        <div class="horizontalList" @if($item->deleted_at != null) style="background-color: #ffe9e9;"  @endif>
            <span @if($tempDocketFields->first()->id == $item->id)class="fifth"@endif style="display: inline-block;">
                <a href="#" id="footer" class="editable    @if($tempDocketFields->first()->id == $item->id)firstEditElements @endif" data-type="text" data-pk="{{ $item->id }}" data-url="{{ url('dashboard/company/docketBookManager/designDocket/docketFieldLabelUpdate') }}" data-title="Enter Label Text">{{ $item->label }}</a>
            </span>

            <div>
                @if($docketSetting->count()==0)
                    <p style="font-style: italic;    margin-top: 23px;"><a href="{{ url('dashboard/company/profile/docketSetting') }}">Click here</a> to set-up terms and conditions.</p>
                @else
                {{ Form::open(['url' => 'dashboard/company/docketBookManager/designDocket/saveDocketFieldFooter', 'files' => true]) }}
                    <div class="row">
                        <div class="col-md-8">
                            <input type="hidden" value="{{ $item->id }}" name="field_id">
                            <input type="hidden" value="{{ $tempDocket->id }}" name="docket_id" >
                            <select style="width: 100%; height: 32px;margin-top: 20px;" name="value" required>
                                <option>Terms and Conditions Dropdown Box</option>
                                @if($docketSetting->count())
                                    @foreach($docketSetting as $row)
                                        <option value="{{$row->term_condition}}">{{$row->title}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button style="margin-top: 20px;" type="submit" class="btn btn-xs btn-raised btn-success">Load</button>
                        </div>
                    </div>
                {{ Form::close() }}
                @endif
            </div>

            {{ Form::open(['url' => 'dashboard/company/docketBookManager/designDocket/saveDocketFieldFooter','id'=>'saveDocketFieldFooter', 'files' => true]) }}
            <input type="hidden" value="{{ $item->id }}" name="field_id">
            <input type="hidden" value="{{ $tempDocket->id }}" name="docket_id">
            <div class="form-group">
                <textarea id="value" class="form-control form-textare-part"  name="value" placeholder="Footer Text" >@if($item->docketFieldFooter=="")     @else {{$item->docketFieldFooter->value}}@endif</textarea>
            </div>
            {{ Form::close() }}

            @if($item->deleted_at == null)
            <button type="button" style="float: right;margin-top: -82px !important;" id="removeFooter" class="btn btn-raised btn-xs btn-danger deleteDocketComponent" data-id="{{ $item->id }}"><i class="fa fa-trash-o"></i> </button>
            @else
                <button type="button" id="removeShortText"
                        class="btn btn-raised btn-xs btn-danger pull-right undofieldbutton" data-toggle="modal" data-target="#undoDocketField"
                        data-id="{{ $item->id }}"><i class="fa fa-undo"></i></button>

            @endif
            <span style="font-size: 12px;">
                Hide from Recipient?&nbsp;&nbsp;
                <input type="checkbox" value="1" class="docketFieldIsHidden"  data="{{ $item->id }}" name="isHidden" @if($item->is_hidden==1) checked @endif> &nbsp;&nbsp;
            </span>
            <br>
        </div>
    </div>
@endif
