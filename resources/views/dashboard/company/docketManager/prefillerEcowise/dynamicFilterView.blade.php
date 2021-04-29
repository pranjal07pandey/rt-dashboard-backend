<div style="margin: 0px 0px 30px 0px;"><br>
    <div class="col-md-6">

{{--        <select style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseGridFieldFilter">--}}
{{--            <option  value="0" gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" >Select Index</option>--}}
{{--                @foreach($keyValue as $keyValues)--}}
{{--                    <option >{{$keyValues}}</option>--}}
{{--                @endforeach--}}
{{--        </select>--}}


            <select  style="height: 37px;padding: 0px 20px 0px 5px;border: 1px solid #CED4DA; width: 100%" class="ecowiseGridFieldFilter">
                <option  value="0" gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" linkprefillerfilterid="0" >Select Index</option>
                @foreach($keyValue as $keyValues)
                    <option gridId="{{ $docketGridField->id }}"  fieldId="{{$docketField->id}}" value="{{ $keyValues}}" linkprefillerfilterid="0"    >{{$keyValues}}</option>
                @endforeach
            </select>


    </div>
    <div class="col-md-5">

        <select  class="form-control prefillerGridLinkFilter">
            <option value="0">Please Select Filter Value</option>
        </select>

    </div>
    <div class="col-md-1">
        <button type="button" name="add"  class="btn btn-danger removefilterLinkPrefiller" linkprefillerfilterid="0" style="margin: 0;">Remove</button>
    </div>
</div>
