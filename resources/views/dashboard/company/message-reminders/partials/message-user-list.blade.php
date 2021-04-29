@foreach($messageData as $messageDatas )
    <li style="position: relative;">
        <a href="javascript:void(0)"   class="clickToChat single_chat{{$messageDatas['id']}}" idAtt="{{$messageDatas['id']}}" type="{{$messageDatas['type']}}" style="padding: 19px;">
            @if($messageDatas['profile'])
                @php $i = 1; @endphp
                @foreach($messageDatas['profile'] as $profile)
                    @if($i!=1)
                        @if(AmazoneBucket::fileExist(@$profile['image']))
                            <div class="profile" style="background-image: url({{ AmazoneBucket::url() }}{{ $profile['image'] }});    position: absolute; left: 28px;top: 1px;border: solid 2px #fff   "></div>
                        @else
                            <div class="profile" style="position: absolute; background-color: #022e55;  left: 28px;top: 1px;border: solid 2px #fff;color: #fff;font-size: 25px;padding: 0px 0px 0px 9px;font-weight: 900; text-transform: capitalize;">{{mb_substr($profile['name'], 0, 1, "UTF-8")}}</div>
                        @endif
                    @else($i=1)
                        @if(AmazoneBucket::fileExist(@$profile['image']))
                            <div class="profile" style="background-image: url({{ AmazoneBucket::url() }}{{ $profile['image'] }});       position: absolute;    left: 6px;    z-index: 1;border: solid 1px #d8d8d8;"></div>
                        @else
                            <div class="profile" style="  position: absolute;background-color: #022e55;     left: 6px;border: solid 1px #d8d8d8;color: #fff;font-size: 25px;padding: 0px 0px 0px 9px;font-weight: 900; text-transform: capitalize;">{{mb_substr($profile['name'], 0, 1, "UTF-8")}}</div>
                        @endif
                    @endif
                    <?php $i++; ?>
                @endforeach
            @endif
            <div class="profileInfo">
                <strong style="margin-top: 11px;margin-left: 48px;">{{$messageDatas['title'] }}</strong>
            </div>
            <div class="clearfix"></div>
        </a>
    </li>
@endforeach
