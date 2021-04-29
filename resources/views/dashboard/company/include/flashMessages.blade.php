{{--<div class="alert alert-warning" style="padding: 10px 10px;font-size: 13px;">--}}
    {{--<p>--}}
        {{--We have recently upgraded our docket (id) numbering system. The numbers are now template and employee sensitive. For example, RT-DOC-111-1-2 means template number 111, employee 1 and docket number 2. The ‘DOC’ prefix can be changed in the docket design page under, ‘docket prefix’. Please email us if you have any questions.--}}
    {{--</p>--}}
{{--</div>--}}
@if(Session::get('message'))
    <div class="dashboardFlash">
        <div class="alert alert-{{ Session::get('message')[1] }}" style="padding: 10px 10px;font-size: 13px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            <p>
                {{ Session::get('message')[0] }}</p>
            @if(Session::get('message')[2])
                <a href="{{ Session::get('message')[2] }}" class=" btn btn-info btn-xs" style="margin-bottom: 10px;background: #ddd;color: #000;font-weight: 500;text-decoration: none;">
                    @if(@Session::get('message')[3])
                        {{ Session::get('message')[3] }}
                    @else Click Here @endif
                </a>
            @endif
            <div class="clearfix"></div>
        </div>
    </div>
@endif

@if(Session::get('docketLimit'))
    <div class="dashboardFlash" style="position: absolute;top: 12px;right: 16px;min-width: 160px;border-radius: 3px;overflow: hidden;">
        <div class="alert" style="padding: 5px 10px;font-size: 12px;background: #50D69C;margin-bottom: 0px;">
            <p>
                <i class="material-icons" style="font-size: 12px;">date_range</i>
                {{ Session::get('docketLimit')[0] }}
            </p>
            <div class="clearfix"></div>
        </div>
    </div>
@endif

@include('flash::message')
{{--@if (Session::has('flash_notification.message'))--}}
    {{--<div class="dashboardFlash">--}}
        {{--<div class="alert alert-{{ Session::get('flash_notification.level') }}" style="padding: 5px 10px;font-size: 13px;">--}}
            {{--<button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>--}}

            {{--{{ Session::get('flash_notification.message') }}--}}
        {{--</div>--}}
    {{--</div>--}}
{{--@endif--}}
@foreach($errors->all() as $error)
    <div class="dashboardFlash">
        <div class="alert alert-danger" style="padding: 5px 10px;font-size: 13px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true" style="line-height: 0.8;">&times;</button>
            {{ $error }}
        </div>
    </div>
@endforeach