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
            <span style="font-size: 15px;color: black;font-weight: 300;">{{@$yesNoExplanations->label}}</span>
        </div>
    </div>
</div>
<div  class="formElement">
    <strong>Form Element</strong>
    <ul style="list-style-type: none;    margin-left: -38px;margin-top: 6px;">
        <li style="float: left;    margin-right: 8px;">
            <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple subDocketComponent" yesNOFieldId="{{@$yesNoExplanations->id}}"  explanationFieldType="1" requires="1">
                <span><i class="fa fa-plus-square"></i> Short Text </span>
            </a>
        </li>
        <li style="float: left; margin-right: 8px;">
            <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple subDocketComponent" yesNOFieldId="{{@$yesNoExplanations->id}}"  explanationFieldType="2" requires="1">
                <span><i class="fa fa-plus-square"></i> Long Text </span>
            </a>
        </li>
        <li style="float: left;">
            <a href="#" class="btn btn-primary themeSecondaryBg  btn-xs withripple subDocketComponent" yesNOFieldId="{{@$yesNoExplanations->id}}"  explanationFieldType="5" requires="1">
                <span><i class="fa fa-plus-square"></i> Images </span>
            </a>
        </li>

    </ul><br><br>
    <div style="    padding-bottom: 13px;" class="showingForm">
        <strong style="font-weight: 500;">Explanation Form</strong>

        <div  id="confirm" style="display: none;" class="ui-content" data-role="popup" data-theme="a">
            <p id="question">Are you sure you want to delete:</p>
            <div class="ui-grid-a">
                <div class="ui-block-a">
                    <a id="yes" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Yes</a>
                </div>
                <div class="ui-block-b">
                    <a id="cancel" class="ui-btn ui-corner-all ui-mini ui-btn-a" data-rel="back">Cancel</a>
                </div>
            </div>
        </div><!-- /popup -->

        <div style="display: none; position: absolute;right: 14px;top: 102px;font-size: 7px;color: gray;" class="loadspin"><i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
            <span class="sr-only">Loading...</span></div>
        <div style="height: 400px; overflow-y: auto;">
            <div style="display: none; position: absolute;right: 14px;top: 102px;font-size: 7px;color: gray;" class="loadCompleteCheck"><i class="fa fa-check" aria-hidden="true"></i></div>
            <div style="height: 366px; overflow-y: auto;">

            <div id="subdocketSorting">
                @if($tempSubDocket)
                    @foreach($tempSubDocket as $item)
                        @include('dashboard.company.docketManager.subDocketElementTemplate')
                    @endforeach
                @endif
            </div>
            <div id="bottom"></div>

        </div>


    </div>
</div>

</div>
<div style="margin-top: -17px;" class="footer_button">
    <button style="padding: 4px 22px;float: right;" class="btn btn-primary closeSubdocket" id="saveTimerAttachementChecked">Save</button>

</div>






