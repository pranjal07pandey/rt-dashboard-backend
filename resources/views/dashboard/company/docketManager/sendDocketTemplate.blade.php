{{--@if($row->docket_field_category_id==13)--}}
{{--{{$docketField}}--}}
{{--@endif--}}
{{--<h3 class="text-center"  style="font-weight: 800;">Complete the docket below</h3>--}}

@if($docketField->count())
    @foreach($docketField as $row)
        @if($row->docket_field_category_id==6)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input  type="text"  id="datepickerDocket" class="form-control"  name="date" placeholder="Date"  required autofocus>
                    </div>
                <br>
            </div>


        @endif
        @if($row->docket_field_category_id==3)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input id="title" type="number"  class="form-control" name="number" placeholder="Number"  required autofocus>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==1)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input id="title" type="text"  class="form-control" name="title" placeholder="Short Text"  required autofocus>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==2)
            <div class="col-md-12 shortTextDiv docketField ">
            <span   style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input id="title" type="text"  class="form-control" name="title" placeholder="Long Text"  required autofocus>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==4)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input id="title" type="text"  class="form-control" name="title" placeholder="Location"  required autofocus>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==5)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input type="file" id="image" name="profile" multiple>
                        <input type="text" readonly="" class="form-control" placeholder="Image">

                    </div>
                    <br>

            </div>
        @endif
        @if($row->docket_field_category_id==7)
                <div class="col-md-4" style="margin-bottom: 14px;">
                    <span id="unitRateEdit">
                         <a style="font-weight: bold;" href="#" >{{ $row->unitRate[0]->label }}</a>
                    </span>
                    <div class="form-group " style="margin: 14px 0 0 0;">
                        <span><b>$</b></span> <input style="margin-top: -31px; margin-left: 11px;" id="perUnitRate{{$row->id}}" type="number" oninput="calculate({{ $row->id }})"  class="form-control" placeholder="{{ $row->unitRate[0]->label }}" />
                    </div>
                </div>
                <div class="col-md-4" style="padding-right: 0px;">
                    <span class="unitRateEdit">
                            <a style="font-weight: bold;" href="#" >{{ $row->unitRate[1]->label }}</a>
                    </span>
                    <div class="form-group" style="margin: 14px 0 0 0;" >
                         <input style="margin-top: -9px;" id="totalUnite{{$row->id}}" type="number" oninput="calculate({{ $row->id }})"   class="form-control" placeholder="{{ $row->unitRate[1]->label }}" aria-describedby="basic-addon1"/>
                    </div>
                </div>
                      <div class="col-md-4" style="padding-right: 0px;">
                            <span class="unitRateEdit">
                                    <a style="font-weight: bold;" href="#"  >Total</a>
                            </span>
                        <div class="form-group" style="margin: 14px 0 0 0;" >
                            <span><b>$</b></span> <input style="margin-top: -31px; margin-left: 11px;" id="result{{$row->id}}" disabled  class="form-control" placeholder="Total"/>
                        </div>
                    </div>
                <div class="clearfix"></div>


        @endif
        @if($row->docket_field_category_id==8)
            <div class="col-md-12 shortTextDiv docketField ">
              <div class="col-md-10" style="padding: 0px;">
                <span  style="display: inline-block;">
                    <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
                </span>
              </div>
                    <div class="form-group col-md-2" style="margin: 6px 0 0 0; margin: 6px 0 0 0;text-align: right;">
                        <div class="checkbox" style="margin-top: -2px;">
                            <label>
                                <input type="checkbox" value="1" name="employed" >
                                <span class="checkbox-material"><span class="check"></span>
                                </span>
                            </label>
                        </div>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==9)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input type="file" id="file-input" name="profile">
                        <input type="text" readonly="" class="form-control" placeholder="Signature">
                    </div>
                <div class="col-md-3">
                    <div id="preview"></div>
                </div>

                    <br>
            </div>
        @endif

        @if($row->docket_field_category_id==12)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <input id="title" type="text"  class="form-control" name=" Header/Title" placeholder="Header/Title"  required autofocus>
                    </div>
                    <br>
            </div>
        @endif

        @if($row->docket_field_category_id==14)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        {{--<input id="title" type="text"  class="form-control" name="title" placeholder="Sketch Pad" required autofocus>--}}
                        <div class="my-drawing"></div>
                    </div>
                    <br>
            </div>
        @endif
        @if($row->docket_field_category_id==15)
            <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
                    <div class="form-group" style="margin: 6px 0 0 0;">
                        <ul class="pdf">
                            @if($row->docketAttached )
                                <?php $i=1 ?>
                              @foreach($row->docketAttached as $rows)
                                        <li><img src="{{ asset('assets/pdf.png') }}"></i><a href="{{ AmazoneBucket::url() }}{{ $rows->url }}" target="_blank">{{$rows->name}}</a></li>
                                 <?php $i++ ?>
                              @endforeach
                            @endif
                        </ul>
                    </div>
                    <br>
            </div>
        @endif
    @endforeach
    @foreach($docketField as $row)
    @if($row->docket_field_category_id==13)
        <div class="col-md-12 shortTextDiv docketField ">
            <span  style="display: inline-block;">
                <a style="font-weight: bold;" href="#" >{{ $row->label }}</a>
            </span>
            <div class="form-group" style="margin: 6px 0 0 0;">
                <textarea id="value" style="    border-bottom: none;height: 77px;" class="form-control form-textare-part" disabled name="value" placeholder="Footer Text" >@if($row->docketFieldFooter=="")     @else {{$row->docketFieldFooter->value}}@endif</textarea>
            </div>
            <br>
        </div>
    @endif
    @endforeach
@endif
<script>
    LC.init(
        document.getElementsByClassName('my-drawing')[0],
        {imageURLPrefix: '/static/img'}
    );

    function calculate(key) {
        var perUnitRate = document.getElementById('perUnitRate'+key).value;
        var totalUnite = document.getElementById('totalUnite'+key).value;
        var result = document.getElementById('result'+key);
        var myResult = perUnitRate * totalUnite;
        result.value = myResult;
    }

</script>



