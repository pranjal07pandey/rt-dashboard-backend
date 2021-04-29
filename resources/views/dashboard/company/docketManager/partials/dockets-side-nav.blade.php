<div class="shell">
    <div class="head">
        <div class="menu">
            <h5 style="margin-top: 14px;font-size: 14px;font-weight: 600;margin-bottom: 16px;">Dockets</h5><hr>
            @php
                $folder_status  =    0;
                if(Route::currentRouteName()=='dockets.allDockets'){ $folder_status = 1; }
                if(Route::currentRouteName()=='dockets.sentDockets'){ $folder_status = 3; }
                if(Route::currentRouteName()=='dockets.receivedDockets'){ $folder_status = 4; }
                if(Route::currentRouteName()=='dockets.emailedDockets'){ $folder_status = 5; }
           @endphp
            <input type="hidden" value="{{ $folder_status }}" id="folder_status">
            <a href="{{route('dockets.createDockets')}}" class="rounded-primary-btn" style="margin-right: 10px;margin-bottom: 15px;">Create New Docket</a>
            <ul>
                <li @if(Route::currentRouteName()=='dockets.allDockets')class="active"@endif>
                    <a href="{{ route('dockets.allDockets') }}" >All Dockets</a>
                </li>
                <li @if(Route::currentRouteName()=='dockets.sentDockets')class="active"@endif>
                    <a href="{{ route('dockets.sentDockets') }}" >Sent Dockets</a>
                </li>
                <li @if(Route::currentRouteName()=='dockets.receivedDockets')class="active"@endif>
                    <a href="{{ route('dockets.receivedDockets') }}" >Received Dockets</a>
                </li>
                <li @if(Route::currentRouteName()=='dockets.emailedDockets')class="active"@endif>
                    <a href="{{ route('dockets.emailedDockets') }}" >Emailed Dockets</a>
                </li>

                <li @if(Route::currentRouteName()=='dockets.docketDraft')class="active"@endif>
                    <a href="{{ route('dockets.docketDraft') }}" > Docket Draft</a>
                </li>

            </ul>
        </div>
    </div>
</div>