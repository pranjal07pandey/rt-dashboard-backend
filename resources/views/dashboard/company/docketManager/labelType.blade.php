<div class="headerSubDocket">
    <div class="row">
        <div style="   margin-bottom: 5px;" class="col-md-1">
            <strong>Question:</strong>
        </div>
        <div style="   margin-bottom: 5px;" class="col-md-11">
            <span style="font-size: 15px;color: black;font-weight: 300;">{{@$yesNoExplanations->docketFieldInfo->label}}</span>
        </div>
        <div style="" class="col-md-1">
            <strong>Selected:</strong>
        </div>
        <div style="" class="col-md-11">
            <img style="width: 23px; background-color:{{ AmazoneBucket::url() }}{{ @$yesNoExplanations->colour }} "
                 src="{{ AmazoneBucket::url() }}{{ @$yesNoExplanations->icon_image }}">
        </div>
    </div>
</div>
{{ Form::open(['url' => 'dashboard/company/docketBookManager/yesNoIconImageUpdate/' , 'files' => true]) }}
<div style="    margin-top: 20px;    min-height: 84px;" class="formElement">
    <input type="hidden" name="id" value="{{$yesNoExplanations->id}}">
    <strong>Change Icon</strong>
    <ul style="list-style: none;    margin-top: 14px;    margin-left: -29px;">
        <li style="    padding-bottom: 10px;    float: left;    margin-right: 28px;">
            <p>
                <input type="checkbox" class="check"  id="labelCheck" value="assets/yesnonaimage/check.png" name="icon_image"
                       @if($yesNoExplanations->icon_image=="assets/yesnonaimage/check.png") checked @else @endif >
                <label for="labelCheck">
                    <img style="width: 23px; background-color:{{ AmazoneBucket::url() }}{{ @$yesNoExplanations->colour }}"
                         src="{{asset('assets/yesnonaimage/check.png')}}">
                </label>
            </p>

        </li>
        <li style="    padding-bottom: 10px;    float: left;    margin-right: 28px;">
            <p>
                <input type="checkbox" class="check"  id="labelCross" value="assets/yesnonaimage/close.png" name="icon_image"
                       @if($yesNoExplanations->icon_image=="assets/yesnonaimage/close.png") checked @else @endif >
                <label for="labelCross">
                    <img style="width: 23px; background-color:{{ AmazoneBucket::url() }}{{ @$yesNoExplanations->colour }}"
                         src="{{asset('assets/yesnonaimage/close.png')}}">
                </label>
            </p>
        </li>
        <li style="    padding-bottom: 20px;    float: left;">

            <p>
                <input type="checkbox" class="check"  id="labelNa" value="assets/yesnonaimage/na.png" name="icon_image"
                       @if($yesNoExplanations->icon_image=="assets/yesnonaimage/na.png") checked @else @endif >
                <label for="labelNa">
                    <img style="width: 23px; background-color:{{ AmazoneBucket::url() }}{{ @$yesNoExplanations->colour }}"
                         src="{{asset('assets/yesnonaimage/na.png')}}">
                </label>
            </p>
        </li>
    </ul>


</div>
<div style="margin-top: -17px;" class="footer_button">
    <button style="padding: 4px 22px;float: right;" type="submit" class="btn btn-primary submitLabelImage">Save</button>

</div>
{{ Form::close() }}