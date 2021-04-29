<ul>
    <li>
        <table class="main-docket" border="0" cellspacing="0" cellpadding="0">
            <p class="docket">Complete the docket below</p>
        </table>
    </li>
@if($tempDocketFields)
@foreach($tempDocketFields as $item)
    @if($item->docket_field_category_id==1)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                        </form>
                    </td>

                </tr>

                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==2)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                        </form>
                    </td>
                </tr>

                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==3)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==4)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
        <li style="text-align: center; padding-top: 10px;" >
            <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td >
                        <p style="font-weight: 700;"><label class="switch">
                                <input type="checkbox">
                                <span class="slider round"></span>
                            </label>
                            Use Current Location</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==5)
        <li style="text-align: center; padding-top: 10px;" >
            <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td >
                        <p style="font-weight: 700;"><i class="fa fa-camera" aria-hidden="true">&nbsp;</i>  {{ $item->label }}</p>

                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==6)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
        @if($item->docket_field_category_id==21)
            <li>
                <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <form class="input-name">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                            </form>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </li>
        @endif
    @if($item->docket_field_category_id==7)
        <li>
            <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                <tbody>
                <tr>

                    <td>
                        <form class="input-name" style="    padding-right: 3px;">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->unitRate[0]->label }}"><br>
                        </form>
                    </td>
                    <td>
                        <form class="input-name">
                            <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ $item->unitRate[1]->label }}"><br>
                        </form>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==8)
        <li style=" padding-top: 10px;" >
            <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td >
                        <p style="float: left; padding-left: 10px;" > {{ $item->label }}</p>

                    </td>
                    <td><input style="    margin: 10px;" class="checkbox" type="checkbox" id="myCheck"  onclick="myFunction()"></td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==9)
        <li style="text-align: center; padding-top: 10px;" >
            <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td >
                        <p style="font-weight: 700;"><img style="width: 16px" src="{{asset('icon.png')}}">  {{ $item->label }}</p>

                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
        @if($item->docket_field_category_id==26)
            <li>
                <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <form class="input-name">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                            </form>
                        </td>

                    </tr>

                    </tbody>
                </table>
            </li>
        @endif
    @if($item->docket_field_category_id==12)
        <li>
            <table style="    margin-top: 19px;"  class="header-title" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <p class="header-p">{{ $item->label }}</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==14)
        <li style="text-align: center; padding-top: 10px;" >
            <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td >
                        <p style="font-weight: 700;"> <img style="width: 16px" src="{{asset('sketch.png')}}"> {{ $item->label }}</p>

                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
        @if($item->docket_field_category_id==16)
            <li style="text-align: center; padding-top: 10px;" >
                <table  class="docket-image" border="0" cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td >
                            <p style="">  {{ $item->label }} <img style="width: 16px" src="{{asset('barcode.png')}}"></p>

                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
        @endif
        @if($item->docket_field_category_id==18)
            <li>
                <table class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <p class="docket" style="font-size:12px; ">{{$item->label}}</p>
                        </td>
                    </tr>
                <tr>
                 <td>

                     @if(@$item->yesNoField[0]->label_type || @$item->yesNoField[1]->label_type || @$item->yesNoField[2]->label_type == 1 )
                         <div style="width: 100%;">
                             <div>
                                 <img style="width: 15px;height: 15px; padding:4px; background-color:#ababab; border-radius: 20px; " src="{{ AmazoneBucket::url() }}{{ $item->yesNoField[1]->icon_image }}">
                             </div>
                             <div>
                                 <img style="width: 15px;height: 15px; padding:4px; background-color:#ababab; border-radius: 20px; " src="{{ AmazoneBucket::url() }}{{ $item->yesNoField[0]->icon_image }}">
                             </div>
                             <div>
                                 <img style="width: 15px;height: 15px; padding:4px; background-color:#ababab; border-radius: 20px; " src="{{ AmazoneBucket::url() }}{{ $item->yesNoField[2]->icon_image }}">
                             </div>

                         </div>
                     @else


                         <div style="width: 100%;">
                             <div style="    width: 6%;float: left;border: 2px solid #009c88;border-radius: 39px;height: 15px;color: #fff;background: #009988;     margin-bottom: 6px;font-size: 12px;margin-top: 2px;">
                                 <i style="    position: absolute;" class="fa fa-check" aria-hidden="true"></i>
                             </div>

                             <div style="width: 90%; float: right;font-weight: 400;">
                                 {{ @$item->yesNoField[1]->label }}&nbsp;
                             </div>
                             <div class="clearfix"></div>
                             <div style="    width: 6%;float: left;border: 2px solid #737373;border-radius: 39px;height: 15px;color: #fff;     margin-bottom: 6px;margin-top: 2px;">
                             </div>

                             <div style="width: 90%; float: right;font-weight: 400;">
                                 {{ @$item->yesNoField[0]->label }}&nbsp;
                             </div>
                             <div class="clearfix"></div>
                             <div style="    width: 6%;float: left;border: 2px solid #737373;border-radius: 39px;height: 15px;color: #fff;     margin-bottom: 6px;margin-top: 2px;">
                             </div>



                             <div style="width: 90%; float: right;font-weight: 400;">
                                 {{ @$item->yesNoField[2]->label }}&nbsp;
                             </div>
                             <div class="clearfix"></div>

                         </div>

                     @endif


                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
        @endif

        @if($item->docket_field_category_id==20)
            <li style="margin-bottom: 15px;">
                <table  class="document" border="0" cellspacing="0" cellpadding="0" style="border-bottom: none;">
                    <tbody>
                    <tr>
                        <td>
                            <p class="docket"  style="font-size:13px; ">{{$item->label}}</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form class="input-name" style="    padding-right: 3px;">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ @$item->docketManualTimer[0]->label }}"><br>
                            </form>
                        </td>
                        <td>
                            <form class="input-name">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ @$item->docketManualTimer[1]->label }}"><br>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="    padding-top: 8px;">
                            <form class="input-name" style="    padding-right: 3px;">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ @$item->docketManualTimerBreak[0]->label }}"><br>
                            </form>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2" style="    padding-top: 8px;">
                            <form class="input-name" style="    padding-right: 3px;">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;Explanation"><br>
                            </form>
                        </td>

                    </tr>
                    <tr>
                        <td colspan="2" style="    padding-top: 8px;">
                            <p class="docket"  style="font-size:12px;font-weight: bold; ">Total: 00 hrs 00 min</p>
                        </td>

                    </tr>

                    </tbody>
                </table>
            </li>
        @endif
     @if($item->docket_field_category_id==15)
        <li style="margin-bottom: 20px;">
            <table  class="document" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <p class="docket">{{$item->label}}</p>
                    </td>
                </tr>
                <tr>
                    <table style="background: #f0f0ee;">
                        @if($item->docketAttached )
                            @foreach($item->docketAttached as $row)
                                <ul>
                                    <li style="float: left;padding: 5px 10px 1px 9px;border: 1px solid #9e9e9e94;text-align: center;     margin-right: 7px;">
                                        <img  style="width: 16px;" src="{{asset('pdf.png')}}"><br>
                                        <a style="color: #000; text-decoration: none; font-size: 11px;" href="{{ AmazoneBucket::url() }}{{ $row->url }}" target="_blank">{{$row->name}}</a>
                                    </li>
                                </ul>
                            @endforeach
                        @endif
                    </table>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
    @if($item->docket_field_category_id==22)
        <li style="margin-bottom: 20px;" class="table-responsive">
            <br />
            {{ $item->label  }}
            <table  cellspacing="0" cellpadding="0" class="table-bordered">
                <thead>
                    <tr>
                        @foreach($item->girdFields as $girdField)
                            <th  style="min-width: 80px; font-weight: 400;">{{ $girdField->label }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody style="background-color: white; color: white">
                    <tr style="height: 50px;">
                            <th colspan="{{count($item->girdFields)}}">  </th>

                    </tr>
                </tbody>
            </table>
        </li>
    @endif

        @if($item->docket_field_category_id==25)
            <li>
                <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td>
                            <form class="input-name">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
        @endif

        @if($item->docket_field_category_id==24)
            <li>
                <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td colspan="2">
                            <form class="input-name" style="    padding-right: 3px;">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->label }}"><br>
                            </form>
                        </td>
                    </tr>
                    <tr>

                        <td>
                            <form class="input-name" style="    padding-right: 3px;">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp;{{ $item->tallyUnitRate[0]->label }}"><br>
                            </form>
                        </td>
                        <td>
                            <form class="input-name">
                                <input class="abc" type="text" name="FirstName" placeholder="&nbsp; {{ $item->tallyUnitRate[1]->label }}"><br>
                            </form>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </li>
        @endif


        @if($item->docket_field_category_id==27)
            <li>
                <table  class="docket-unit" border="0" cellspacing="5" cellpadding="0">
                    <tbody>
                    <tr>
                        <td colspan="2">
                            <form class="input-name" style="    padding-right: 3px;">
                                <div class="form-group " id="displayAdvanceHeaders{{$item->id}}" style="padding: 0px 0 0 16px;width: 257px;overflow-x: auto;">
                                    {!! $item->label !!}
                                </div>
                            </form>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </li>
        @endif


@endforeach


@foreach($tempDocketFields as $item)
    @if($item->docket_field_category_id==13)
        <li>
            <table style="    margin-top: 15px;"  class="terms-conditions" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                <tr>
                    <td>
                        <p class="docket">@{{$item->label}}</p>

                        <p class="xyz">@{{$item->docketFieldFooter->value}}</p>
                    </td>
                </tr>
                </tbody>
            </table>
        </li>
    @endif
@endforeach
@endif
</ul>
<footer>
    <a id="addNew" href="https://recordtime-laravel.dev/dashboard/company/docketBookManager/template" class="btn btn-xs btn-raised btn-success eight tourModel" style="margin: 0px; background: #012f54; border: 1px #012f54;width: 100%;border-radius: 20px;">
        Send<div class="ripple-container"></div>
    </a>
</footer>
