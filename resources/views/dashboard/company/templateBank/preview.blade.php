
{{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}

{{--<div class="docketTemplatePreview">--}}
{{--    <img class="mobile" src="{{ asset('phone.png') }}">--}}
{{--    <div class="content">--}}
{{--        <?php $docket   =   json_decode($template->template_value,true); ?>--}}
{{--        @foreach ($docket['docket'] as $docketObject)--}}
{{--            <div class="docket-preview-navigation">--}}
{{--                <p style="padding: 11px;font-size: 12px;color: #fff; font-weight: 700; text-align: center;">--}}
{{--                    <i style="float: left;font-size: 14px;     padding: 3px;" class="fa fa-chevron-left" aria-hidden="true"></i>--}}
{{--                    {{ $docketObject["title"] }}--}}
{{--                    <i style="float: right;font-size: 14px;     padding: 3px;" class="fa fa-home" aria-hidden="true"></i></p>--}}
{{--            </div>--}}
{{--            <div class="mobileContentWrapper">--}}
{{--                <div class="mobilecontain">--}}
{{--                    <ul>--}}
{{--                        @foreach ($docket['docket_field'] as $docketfieldObject)--}}
{{--                            @if(App\DocketFiledCategory::where('id',$docketfieldObject["docket_field_category_id"])->count())--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==1)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0" >--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==2)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name ">--}}
{{--                                                        <input class="abc " type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==3)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc " type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==4)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc " type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"]}}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="font-weight: 700;"><label class="switch">--}}
{{--                                                            <input type="checkbox">--}}
{{--                                                            <span class="slider round"></span>--}}
{{--                                                        </label>--}}
{{--                                                        Use Current Location</p>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==5)--}}
{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="font-weight: 700;"><i class="fa fa-camera" aria-hidden="true">&nbsp;</i> {{ $docketfieldObject["label"]}}</p>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==6)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"]}}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==21)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $docketfieldObject["label"]}}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==7)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}

{{--                                                <td>--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{$docketfieldObject['subField'][0]["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ $docketfieldObject['subField'][1]["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==8)--}}
{{--                                    <li style=" padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="float: left; padding-left: 10px;" > {{ $docketfieldObject["label"] }}</p>--}}

{{--                                                </td>--}}
{{--                                                <td><input style="    margin: 10px;" class="checkbox" type="checkbox" id="myCheck"  onclick="myFunction()"></td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==9)--}}
{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="font-weight: 700;"><img style="width: 16px" src="{{asset('icon.png')}}">  {{$docketfieldObject["label"]  }}</p>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==26)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{$docketfieldObject["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}

{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==12)--}}
{{--                                    <li>--}}
{{--                                        <table style="    margin-top: 19px;"  class="header-title" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <p class="header-p">{{$docketfieldObject["label"]  }}</p>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==14)--}}
{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="font-weight: 700;"> <img style="width: 16px" src="{{asset('sketch.png')}}"> {{$docketfieldObject["label"]  }}</p>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==16)--}}
{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="">{{$docketfieldObject["label"]  }} <img style="width: 16px" src="{{asset('barcode.png')}}"></p>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==18)--}}
{{--                                    <li>--}}
{{--                                        <table class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <p class="docket" style="font-size:12px; ">  {{$docketfieldObject["label"]  }} </p>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}

{{--                                                    @if($docketfieldObject["subField"][0]["label_type"] || $docketfieldObject["subField"][1]["label_type"] || $docketfieldObject["subField"][2]["label_type"] == 1 )--}}
{{--                                                        <div style="width: 100%;">--}}
{{--                                                            <div>--}}
{{--                                                                <img style="width: 15px;height: 15px; background-color:#ababab; border-radius: 20px; " src="{{asset($docketfieldObject["subField"][0]["icon_image"])}}">--}}
{{--                                                            </div>--}}
{{--                                                            <div>--}}
{{--                                                                <img style="width: 15px;height: 15px; background-color:#ababab; border-radius: 20px; " src="{{asset($docketfieldObject["subField"][1]["icon_image"])}}">--}}
{{--                                                            </div>--}}
{{--                                                            <div>--}}
{{--                                                                <img style="width: 15px;height: 15px; background-color:#ababab; border-radius: 20px; " src="{{asset($docketfieldObject["subField"][2]["icon_image"])}}">--}}
{{--                                                            </div>--}}

{{--                                                        </div>--}}
{{--                                                    @else--}}
{{--                                                        <div style="width: 100%;">--}}
{{--                                                            <div style="    width: 8%;float: left;border: 2px solid #009c88;border-radius: 39px;height: 16px;color: #fff;background: #009988;     margin-bottom: 6px;font-size: 12px;margin-top: 2px;">--}}
{{--                                                                <i style="    position: absolute;" class="fa fa-check" aria-hidden="true"></i>--}}
{{--                                                            </div>--}}
{{--                                                            <div style="width: 90%; float: right;font-weight: 400;">--}}
{{--                                                                {{ @$docketfieldObject["subField"][0]["label"] }}&nbsp;--}}
{{--                                                            </div>--}}
{{--                                                            <div class="clearfix"></div>--}}
{{--                                                            <div style="    width: 8%;float: left;border: 2px solid #737373;border-radius: 39px;height: 16px;color: #fff;     margin-bottom: 6px;margin-top: 2px;">--}}
{{--                                                            </div>--}}
{{--                                                            <div style="width: 90%; float: right;font-weight: 400;">--}}
{{--                                                                {{ @$docketfieldObject["subField"][1]["label"] }}&nbsp;--}}
{{--                                                            </div>--}}
{{--                                                            <div class="clearfix"></div>--}}
{{--                                                            <div style="    width: 8%;float: left;border: 2px solid #737373;border-radius: 39px;height: 16px;color: #fff;     margin-bottom: 6px;margin-top: 2px;">--}}
{{--                                                            </div>--}}
{{--                                                            <div style="width: 90%; float: right;font-weight: 400;">--}}
{{--                                                                {{ @$docketfieldObject["subField"][2]["label"] }}&nbsp;--}}
{{--                                                            </div>--}}
{{--                                                            <div class="clearfix"></div>--}}
{{--                                                        </div>--}}
{{--                                                    @endif--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==20)--}}
{{--                                    <li style="margin-bottom: 15px;">--}}
{{--                                        <table  class="document" border="0" cellspacing="0" cellpadding="0" style="border-bottom: none;">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <p class="docket"  style="font-size:13px; ">{{$docketfieldObject["label"]  }}</p>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ @$docketfieldObject["subField"][0]["label"] }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ @$docketfieldObject["subField"][1]["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td colspan="2" style="    padding-top: 8px;">--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ @$docketfieldObject["subFieldBreak"][0]["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}

{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td colspan="2" style="    padding-top: 8px;">--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;Explanation"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}

{{--                                            </tr>--}}
{{--                                            <tr>--}}
{{--                                                <td colspan="2" style="    padding-top: 8px;">--}}
{{--                                                    <p class="docket"  style="font-size:12px;font-weight: bold; ">Total: 00 hrs 00 min</p>--}}
{{--                                                </td>--}}

{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==15)--}}

{{--                                    <li style="text-align: center; padding-top: 10px;" >--}}
{{--                                        <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td >--}}
{{--                                                    <p style="">   {{$docketfieldObject["label"]  }} </p>--}}

{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==22)--}}
{{--                                    <li style="margin-bottom: 20px;" class="table-responsive">--}}
{{--                                        <br />--}}
{{--                                        {{$docketfieldObject["label"]  }}--}}
{{--                                        <table  cellspacing="0" cellpadding="0" class="table-bordered">--}}
{{--                                            <thead>--}}
{{--                                            <tr>--}}
{{--                                                @foreach($docketfieldObject["subField"]   as $girdField)--}}
{{--                                                    <th  style="min-width: 80px; font-weight: 400;">{{ $girdField["label"] }}</th>--}}
{{--                                                @endforeach--}}
{{--                                            </tr>--}}
{{--                                            </thead>--}}
{{--                                            <tbody style="background-color: white; color: white">--}}
{{--                                            <tr style="height: 50px;">--}}
{{--                                                <th colspan="{{count($docketfieldObject["subField"])}}">  </th>--}}

{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==25)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{$docketfieldObject["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==27)--}}

{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td colspan="2">--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <div class="form-group " id="displayAdvanceHeaders" style="padding: 0px 0 0 0px;width: 220px;overflow-x: auto;">--}}
{{--                                                            {!!$docketfieldObject["label"]  !!}--}}
{{--                                                        </div>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}

{{--                                @if($docketfieldObject["docket_field_category_id"]==24)--}}
{{--                                    <li>--}}
{{--                                        <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td colspan="2">--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <p >{{ $docketfieldObject["label"]  }}</p>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            <tr>--}}

{{--                                                <td>--}}
{{--                                                    <form class="input-name" style="    padding-right: 3px;">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{$docketfieldObject["subField"][0]["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                                <td>--}}
{{--                                                    <form class="input-name">--}}
{{--                                                        <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ $docketfieldObject["subField"][1]["label"]  }}"><br>--}}
{{--                                                    </form>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                            @endif--}}
{{--                        @endforeach--}}


{{--                        @foreach ($docket['docket_field'] as $docketfieldObject)--}}
{{--                            @if(App\DocketFiledCategory::where('id',$docketfieldObject["docket_field_category_id"])->count())--}}
{{--                                @if($docketfieldObject["docket_field_category_id"]==13)--}}
{{--                                    <li>--}}
{{--                                        <table style="    margin-top: 15px;"  class="terms-conditions" border="0" cellspacing="0" cellpadding="0">--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                <td>--}}
{{--                                                    <p class="docket">{{$docketfieldObject["label"]}}</p>--}}
{{--                                                    --}}{{--                                         <p class="xyz">@{{$item->docketFieldFooter->value}}</p>--}}
{{--                                                </td>--}}
{{--                                            </tr>--}}
{{--                                            </tbody>--}}
{{--                                        </table>--}}
{{--                                    </li>--}}
{{--                                @endif--}}
{{--                            @endif--}}
{{--                        @endforeach--}}

{{--                    </ul>--}}
{{--                </div>--}}

{{--            </div>--}}




{{--        @endforeach--}}
{{--    </div>--}}
{{--</div>--}}
{{--<style>--}}
{{--    .docketTemplatePreview{--}}
{{--        width:282px;--}}
{{--        height: 590px;--}}
{{--        position: relative;--}}
{{--    }--}}
{{--    .docketTemplatePreview .mobile{--}}
{{--        position: absolute;--}}
{{--        top: 0px;--}}
{{--        bottom: 0px;--}}
{{--        right: 0px;--}}
{{--        left: 0px;--}}
{{--    }--}}
{{--    .docketTemplatePreview .content{--}}
{{--        padding-top: 85px;--}}
{{--        padding-left: 23px;--}}
{{--        padding-right: 20px;--}}
{{--    }--}}
{{--    .docket-preview-navigation{--}}
{{--        background: #002E55;--}}
{{--        height: 46px;--}}
{{--        width: 100%;--}}
{{--        color: #fff;--}}
{{--    }--}}
{{--</style>--}}

<div id="printContainer">
    <div class="row invoice-info">
        <?php $docket   =   json_decode($template->template_value,true); ?>


        <div class="col-md-4 invoice-col">
            <div style="    background: #eaeaec;padding: 6px;border-radius: 4px;font-size: 17px;font-weight: 600;text-align: center;">
                Logo
            </div>

            <br/><br/>From:<br/>
            <strong>Sender Name</strong><br>
             Company Name<br>
            Company Address<br>
            <b>ABN:</b> 000000
            <br/><br/>
            To:<br/>

            Recipient Name
            <br>
            <b>Company Name:</b>
              ********

        </div>
        <!-- /.col -->

        <div class="pull-right" style="text-align:left;width:140px;">
            <div style="width:100%">
                <b> {{$docket["title"]}}</b><br/>
                <b>Date:</b>  {{ \Carbon\Carbon::now()->format('d-M-Y') }}<br/>
                <b>Docket ID:</b> 0000<br>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <div class="row">
        <div class="col-xs-12 table-responsive">
            <br/>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="printTh"><div class="printColorDark">Description</div></th>
                    <th class="printTh"><div class="printColorDark">Value/Amount</div></th>
                </tr>
                </thead>
                <tbody>

                @if($docket['docket_field'])
                    @php
                        $tempFields     =    array();
                        foreach($docket['docket_field'] as $k => $d){ $tempFields[$k] =   $d['order']; }
                        array_multisort($tempFields, SORT_ASC, $docket['docket_field']);
                    @endphp

                    @foreach ($docket['docket_field'] as $docketfieldObject)
                            @if($docketfieldObject["docket_field_category_id"]==7)
                                @foreach($docketfieldObject['unit_rate'] as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>...</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td >
                                        <strong>Total:</strong>
                                    </td>
                                    <td>
                                        <strong>$ ...</strong>
                                    </td>
                                </tr><!--unit-rate-->
                            @elseif($docketfieldObject["docket_field_category_id"]==24)
                                @foreach($docketfieldObject['tally_unit_rate'] as $row)
                                    <tr>
                                        <td>{{ $row['label'] }}</td>
                                        <td>...</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td >
                                        <strong>Total:</strong>
                                    </td>
                                    <td>
                                        <strong>$ ...</strong>
                                    </td>
                                </tr><!--unit-rate-->

                            @elseif($docketfieldObject["docket_field_category_id"]==8)
                                <tr>
                                    <td> {{ $docketfieldObject["label"] }}</td>
                                    <td> <i class="fa fa-check-circle" style="color:green"></i> OR <i class="fa fa-close" style="color:#ff0000 !important"></i> </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==9 )
                                <tr>
                                    <td>{{ $docketfieldObject["label"] }}</td>

                                    <td>
                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                           <li style="margin-right:10px;float: left;">
                                              <img src="{{ asset('assets/bank.png') }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">
                                              <p style="font-weight: 500;color: #868d90;">...</p>
                                           </li>
                                        </ul>
                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==5)
                                <tr>
                                    <td> {{ $docketfieldObject["label"] }}</td>
                                    <td>
                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                            <li style="margin-right:10px;float: left;">
                                                    <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==14)
                                <tr>
                                    <td> {{ $docketfieldObject["label"] }}</td>
                                    <td>
                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                            <li style="margin-right:10px;float: left;">
                                               <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==15)
                                <tr>
                                    <td>{{ $docketfieldObject["label"] }}</td>
                                    <td>
                                        <ul class="pdf">
                                                <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="#" target="_blank">...</a></b></li>
                                        </ul>
                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==12)
                                <tr>
                                    <td  colspan="2"><strong>{{ $docketfieldObject["label"] }}</strong></td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==20)
                                <tr>
                                    <td>{{ $docketfieldObject["label"] }}</td>
                                    <td>
                                        @foreach($docketfieldObject["docket_manual_timer"] as $rows)
                                            <strong>{{$docketfieldObject["label"] }} :</strong>  ... &nbsp; &nbsp;
                                        @endforeach
                                        <br>
                                        @foreach($docketfieldObject["docket_manual_timer_break"] as $item)
                                            <strong>{{$docketfieldObject["label"] }} :</strong>  ...<br>
                                            <strong>Reason for break :</strong>  ...<br>
                                        @endforeach
                                        <strong>Total time :</strong> ...<br>

                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==18 )

                                <tr>
                                    <td colspan="2">
                                        <!--<table style="width:100%;">-->
                                        <!--<tr>-->

                                        <div style="width:100%;margin:0;">
                                            <div style="width:43%;float:left;">{{ $docketfieldObject["label"] }}</div>
                                            <div style="width:50%; float:right;margin-right: 38px;"> ... </div>
                                        </div>


                                        <!-- </tr>-->
                                        <!--</table>-->
                                            <table style="background: transparent; width: 100%;" class="table table-striped">
                                                <thead style="background: transparent; ">
                                                <tr>
                                                    <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>
                                                </tr>
                                                </thead>
                                                <tbody >
                                                        <tr>
                                                            <td>Image</td>
                                                            <td>
                                                                <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                    <li style="margin-right:10px;float: left;">
                                                                            <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">
                                                                    </li>

                                                                </ul>
                                                            </td>
                                                        </tr>


                                                        <tr>
                                                            <td> Short Text</td>
                                                            <td>...</td>
                                                        </tr>
                                                        <tr>
                                                            <td> Long Text</td>
                                                            <td>...</td>
                                                        </tr>

                                                </tbody>
                                            </table>

                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]==6 )
                                <tr>
                                    <td> {{ $docketfieldObject["label"] }}<br>

                                    </td>
                                        <td>...</td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]== 27)
                                <tr  >
                                    <td colspan="2">  ... </td>

                                </tr>

                            @elseif($docketfieldObject["docket_field_category_id"]== 22)
                                <tr>
                                    <td colspan="2">{{ $docketfieldObject["label"] }}
                                        <div style="    width: 1094px;overflow: auto;">
                                            <table  class="table table-striped" width="100%">
                                                <thead>
                                                <tr>
                                                    @foreach($docketfieldObject["gird_fields"] as $gridFieldLabels)
                                                        <th class="printTh" style="min-width: 200px">
                                                            <div class="printColorDark" >{{ $gridFieldLabels["label"]}}</div>
                                                        </th>
                                                    @endforeach
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        @foreach($docketfieldObject["gird_fields"] as $gridFieldLabels)
                                                            @if($gridFieldLabels["docket_field_category_id"]  == 5 || $gridFieldLabels["docket_field_category_id"]  == 14)
                                                                    <td>
                                                                        <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                    <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                            <img src="{{ asset('assets/bank.png') }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                    </li>
                                                                        </ul>
                                                                    </td>
                                                            @elseif($gridFieldLabels["docket_field_category_id"] == 9)
                                                                    <td>

                                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">
                                                                                    <li style="margin-right:10px;float: left; margin-bottom: 8px;">
                                                                                            <img src="{{ asset('assets/bank.png') }}" style=" width: 60px;height: 60px;border: 1px solid #ddd;">
                                                                                        <p style="font-weight: 500;color: #868d90;">...</p>
                                                                                    </li>
                                                                            </ul>
                                                                    </td>
                                                            @else
                                                                @if($gridFieldLabels["docket_field_category_id"]  == 8)
                                                                    <td>

                                                                            <i class="fa fa-check-circle" style="color:green"></i>
                                                                          Or
                                                                            <i class="fa fa-close" style="color:#ff0000 !important"></i>
                                                                    </td>

                                                                @elseif($gridFieldLabels["docket_field_category_id"]  ==20)


                                                                        <td>
                                                                            <strong>From :</strong>  ...<br>
                                                                            <strong>To :</strong>  ...
                                                                            <br>
                                                                            <strong>Total Break :</strong>... <br>
                                                                            <strong>Reason for break :</strong>  ...<br>
                                                                            <strong>Total time :</strong> ... <br>
                                                                        </td>

                                                                @else
                                                                    <td>...</td>
                                                                @endif
                                                            @endif
                                                        @endforeach
                                                    </tr>

                                                </tbody>
                                            </table>

                                        </div>
                                    </td>
                                </tr>
                            @elseif($docketfieldObject["docket_field_category_id"]!=13 && $docketfieldObject["docket_field_category_id"]!=17 && $docketfieldObject["docket_field_category_id"]!=6 && $docketfieldObject["docket_field_category_id"]!=20 && $docketfieldObject["docket_field_category_id"]!=7 )
                                <tr>
                                    <td> {{ $docketfieldObject["label"] }}</td>
                                    <td> ...</td>
                                </tr>
                            @endif
                    @endforeach
                    @foreach($docket['docket_field'] as $row)
                            @if($row["docket_field_category_id"]==13)
                                <tr>
                                    <td  colspan="2"> <strong>{{ $row["label"] }}</strong><br>
                                        ...
                                    </td>
                                </tr>
                                <tr>
                                    <table class="table-striped">
                                        <thead>
                                        <tr>
                                            {{--@foreach($te)--}}
                                        </tr>
                                        </thead>
                                    </table>
                                </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
                <tfoot></tfoot>
            </table>
        </div>
    </div>

{{--</div><div id="printContainer">--}}
{{--    <div class="row invoice-info">--}}
{{--        <?php $docket   =   json_decode($template->template_value,true); ?>--}}


{{--        <div class="col-md-4 invoice-col">--}}
{{--            <div style="    background: #eaeaec;padding: 6px;border-radius: 4px;font-size: 17px;font-weight: 600;text-align: center;">--}}
{{--                Logo--}}
{{--            </div>--}}

{{--            <br/><br/>From:<br/>--}}
{{--            <strong>Sender Name</strong><br>--}}
{{--            Company Name<br>--}}
{{--            Company Address<br>--}}
{{--            <b>ABN:</b> 000000--}}
{{--            <br/><br/>--}}
{{--            To:<br/>--}}

{{--            Recipient Name--}}
{{--            <br>--}}
{{--            <b>Company Name:</b>--}}
{{--            ********--}}

{{--        </div>--}}
{{--        <!-- /.col -->--}}

{{--        <div class="pull-right" style="text-align:left;width:140px;">--}}
{{--            <div style="width:100%">--}}
{{--                <b> {{$docket["title"]}}</b><br/>--}}
{{--                <b>Date:</b>  {{ \Carbon\Carbon::now()->format('d-M-Y') }}<br/>--}}
{{--                <b>Docket ID:</b> 0000<br>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <!-- /.col -->--}}
{{--    </div>--}}
{{--    <div class="row">--}}
{{--        <div class="col-xs-12 table-responsive">--}}
{{--            <br/>--}}
{{--            <table class="table table-striped">--}}
{{--                <thead>--}}
{{--                <tr>--}}
{{--                    <th class="printTh"><div class="printColorDark">Description</div></th>--}}
{{--                    <th class="printTh"><div class="printColorDark">Value/Amount</div></th>--}}
{{--                </tr>--}}
{{--                </thead>--}}
{{--                <tbody>--}}

{{--                @if($docket['docket_field'])--}}
{{--                    @foreach ($docket['docket_field'] as $docketfieldObject)--}}
{{--                        @if($docketfieldObject["docket_field_category_id"]==7)--}}
{{--                            @foreach($docketfieldObject['subField'] as $row)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $row['label'] }}</td>--}}
{{--                                    <td>...</td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            <tr>--}}
{{--                                <td >--}}
{{--                                    <strong>Total:</strong>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <strong>$ ...</strong>--}}
{{--                                </td>--}}
{{--                            </tr><!--unit-rate-->--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==24)--}}
{{--                            @foreach($docketfieldObject['subField'] as $row)--}}
{{--                                <tr>--}}
{{--                                    <td>{{ $row['label'] }}</td>--}}
{{--                                    <td>...</td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                            <tr>--}}
{{--                                <td >--}}
{{--                                    <strong>Total:</strong>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <strong>$ ...</strong>--}}
{{--                                </td>--}}
{{--                            </tr><!--unit-rate-->--}}

{{--                        @elseif($docketfieldObject["docket_field_category_id"]==8)--}}
{{--                            <tr>--}}
{{--                                <td> {{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td> <i class="fa fa-check-circle" style="color:green"></i> OR <i class="fa fa-close" style="color:#ff0000 !important"></i> </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==9 )--}}
{{--                            <tr>--}}
{{--                                <td>{{ $docketfieldObject["label"] }}</td>--}}

{{--                                <td>--}}
{{--                                    <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                        <li style="margin-right:10px;float: left;">--}}
{{--                                            <img src="{{ asset('assets/bank.png') }}" style=" width: 100px;height: 100px;border: 1px solid #ddd;margin: 10px;padding: 10px;">--}}
{{--                                            <p style="font-weight: 500;color: #868d90;">...</p>--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==5)--}}
{{--                            <tr>--}}
{{--                                <td> {{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td>--}}
{{--                                    <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                        <li style="margin-right:10px;float: left;">--}}
{{--                                            <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==14)--}}
{{--                            <tr>--}}
{{--                                <td> {{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td>--}}
{{--                                    <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                        <li style="margin-right:10px;float: left;">--}}
{{--                                            <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">--}}
{{--                                        </li>--}}
{{--                                    </ul>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==15)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td>--}}
{{--                                    <ul class="pdf">--}}
{{--                                        <li><img src="{{ asset('assets/pdf.png') }}"><b><a href="#" target="_blank">...</a></b></li>--}}
{{--                                    </ul>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==12)--}}
{{--                            <tr>--}}
{{--                                <td  colspan="2"><strong>{{ $docketfieldObject["label"] }}</strong></td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==20)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td>--}}
{{--                                    @foreach($docketfieldObject["subField"] as $rows)--}}
{{--                                        <strong>{{$docketfieldObject["label"] }} :</strong>  ... &nbsp; &nbsp;--}}
{{--                                    @endforeach--}}
{{--                                    <br>--}}
{{--                                    @foreach($docketfieldObject["subFieldBreak"] as $item)--}}
{{--                                        <strong>{{$docketfieldObject["label"] }} :</strong>  ...<br>--}}
{{--                                        <strong>Reason for break :</strong>  ...<br>--}}
{{--                                    @endforeach--}}
{{--                                    <strong>Total time :</strong> ...<br>--}}

{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==18 )--}}

{{--                            <tr>--}}
{{--                                <td colspan="2">--}}
{{--                                    <!--<table style="width:100%;">-->--}}
{{--                                    <!--<tr>-->--}}

{{--                                    <div style="width:100%;margin:0;">--}}
{{--                                        <div style="width:43%;float:left;">{{ $docketfieldObject["label"] }}</div>--}}
{{--                                        <div style="width:50%; float:right;margin-right: 38px;"> ... </div>--}}
{{--                                    </div>--}}


{{--                                    <!-- </tr>-->--}}
{{--                                    <!--</table>-->--}}
{{--                                    <table style="background: transparent; width: 100%;" class="table table-striped">--}}
{{--                                        <thead style="background: transparent; ">--}}
{{--                                        <tr>--}}
{{--                                            <th colspan="2"><h5 style=" margin-bottom: 0px;font-size: 12px;color: #929292;margin-left: -0px;">Explanation</h5></th>--}}
{{--                                        </tr>--}}
{{--                                        </thead>--}}
{{--                                        <tbody >--}}
{{--                                        <tr>--}}
{{--                                            <td>Image</td>--}}
{{--                                            <td>--}}
{{--                                                <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                                    <li style="margin-right:10px;float: left;">--}}
{{--                                                        <img src="{{ asset('assets/bank.png') }}" style="height: 100px;">--}}
{{--                                                    </li>--}}

{{--                                                </ul>--}}
{{--                                            </td>--}}
{{--                                        </tr>--}}


{{--                                        <tr>--}}
{{--                                            <td> Short Text</td>--}}
{{--                                            <td>...</td>--}}
{{--                                        </tr>--}}
{{--                                        <tr>--}}
{{--                                            <td> Long Text</td>--}}
{{--                                            <td>...</td>--}}
{{--                                        </tr>--}}

{{--                                        </tbody>--}}
{{--                                    </table>--}}

{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]==6 )--}}
{{--                            <tr>--}}
{{--                                <td> {{ $docketfieldObject["label"] }}<br>--}}

{{--                                </td>--}}
{{--                                <td>...</td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]== 27)--}}
{{--                            <tr  >--}}
{{--                                <td colspan="2">  ... </td>--}}

{{--                            </tr>--}}

{{--                        @elseif($docketfieldObject["docket_field_category_id"]== 22)--}}
{{--                            <tr>--}}
{{--                                <td colspan="2">{{ $docketfieldObject["label"] }}--}}
{{--                                    <div style="    width: 1094px;overflow: auto;">--}}
{{--                                        <table  class="table table-striped" width="100%">--}}
{{--                                            <thead>--}}
{{--                                            <tr>--}}
{{--                                                @foreach($docketfieldObject["subField"] as $gridFieldLabels)--}}
{{--                                                    <th class="printTh" style="min-width: 200px">--}}
{{--                                                        <div class="printColorDark" >{{ $gridFieldLabels["label"]}}</div>--}}
{{--                                                    </th>--}}
{{--                                                @endforeach--}}
{{--                                            </tr>--}}
{{--                                            </thead>--}}
{{--                                            <tbody>--}}
{{--                                            <tr>--}}
{{--                                                @foreach($docketfieldObject["subField"] as $gridFieldLabels)--}}
{{--                                                    @if($gridFieldLabels["docket_field_category_id"]  == 5 || $gridFieldLabels["docket_field_category_id"]  == 14)--}}
{{--                                                        <td>--}}
{{--                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                                                <li style="margin-right:10px;float: left; margin-bottom: 8px;">--}}
{{--                                                                    <img src="{{ asset('assets/bank.png') }}"  style="width: 60px;height: 60px;border: 1px solid #ddd;">--}}
{{--                                                                </li>--}}
{{--                                                            </ul>--}}
{{--                                                        </td>--}}
{{--                                                    @elseif($gridFieldLabels["docket_field_category_id"] == 9)--}}
{{--                                                        <td>--}}

{{--                                                            <ul style="list-style: none;margin: 0px;padding: 0px;">--}}
{{--                                                                <li style="margin-right:10px;float: left; margin-bottom: 8px;">--}}
{{--                                                                    <img src="{{ asset('assets/bank.png') }}" style=" width: 60px;height: 60px;border: 1px solid #ddd;">--}}
{{--                                                                    <p style="font-weight: 500;color: #868d90;">...</p>--}}
{{--                                                                </li>--}}
{{--                                                            </ul>--}}
{{--                                                        </td>--}}
{{--                                                    @else--}}
{{--                                                        @if($gridFieldLabels["docket_field_category_id"]  == 8)--}}
{{--                                                            <td>--}}

{{--                                                                <i class="fa fa-check-circle" style="color:green"></i>--}}
{{--                                                                Or--}}
{{--                                                                <i class="fa fa-close" style="color:#ff0000 !important"></i>--}}
{{--                                                            </td>--}}

{{--                                                        @elseif($gridFieldLabels["docket_field_category_id"]  ==20)--}}


{{--                                                            <td>--}}
{{--                                                                <strong>From :</strong>  ...<br>--}}
{{--                                                                <strong>To :</strong>  ...--}}
{{--                                                                <br>--}}
{{--                                                                <strong>Total Break :</strong>... <br>--}}
{{--                                                                <strong>Reason for break :</strong>  ...<br>--}}
{{--                                                                <strong>Total time :</strong> ... <br>--}}
{{--                                                            </td>--}}

{{--                                                        @else--}}
{{--                                                            <td>...</td>--}}
{{--                                                        @endif--}}
{{--                                                    @endif--}}
{{--                                                @endforeach--}}
{{--                                            </tr>--}}

{{--                                            </tbody>--}}
{{--                                        </table>--}}

{{--                                    </div>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @elseif($docketfieldObject["docket_field_category_id"]!=13 && $docketfieldObject["docket_field_category_id"]!=17 && $docketfieldObject["docket_field_category_id"]!=6 && $docketfieldObject["docket_field_category_id"]!=20 && $docketfieldObject["docket_field_category_id"]!=7 )--}}
{{--                            <tr>--}}
{{--                                <td> {{ $docketfieldObject["label"] }}</td>--}}
{{--                                <td> ...</td>--}}
{{--                            </tr>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                    @foreach($docket['docket_field'] as $row)--}}
{{--                        @if($row["docket_field_category_id"]==13)--}}
{{--                            <tr>--}}
{{--                                <td  colspan="2"> <strong>{{ $row["label"] }}</strong><br>--}}
{{--                                    ...--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                            <tr>--}}
{{--                                <table class="table-striped">--}}
{{--                                    <thead>--}}
{{--                                    <tr>--}}
{{--                                        --}}{{--@foreach($te)--}}
{{--                                    </tr>--}}
{{--                                    </thead>--}}
{{--                                </table>--}}
{{--                            </tr>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                @endif--}}
{{--                </tbody>--}}
{{--                <tfoot></tfoot>--}}
{{--            </table>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--</div>--}}