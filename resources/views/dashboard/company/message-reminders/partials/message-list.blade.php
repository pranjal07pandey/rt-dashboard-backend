@foreach($messageList as $items)
    <li>
        <div class="message"  style="background: #48b790f2;color: #fff;" >
            <p>{{$items['message']}}</p>
            <div class="messageTime">
                <span style="color:#fff;" >{{$items['date']}}</span>
            </div>
        </div>
        @if(AmazoneBucket::fileExist($items['profile']))
            <div class="profile" style="background-image:url({{ AmazoneBucket::url() }}{{ $items['profile'] }})"></div>
        @else
            @php $userName    =   explode(" ",$items['userName']) @endphp
            <div class="profile" style=" background-color: #022e55; color: #fff;font-size: 20px;line-height: 4rem;padding: 0px 0px 0px 9px;font-weight: 900; text-transform: capitalize;">
                {{substr($userName[0], 0, 1) . substr($userName[1], 0, 1)}}
            </div>
        @endif
        <div class="clearfix"></div>
        @if (count($items['seen']))
            <div style="display: flex;padding: 5px 50px 0px 3px;justify-content: flex-end;">
                <ul style="margin:0px;padding:0px;list-style: none">
                    @foreach($items['seen'] as $seen)
                        <li style="display: inline-block" class="seenUser{{$seen['user_id']}}">
                            <div  style="border-radius: 50%;background-color: #022e55;  border: solid 2px #fff;color: #fff;font-size: 9px;height: 25px; width: 25px;padding: 2px 0px 0px 5px;font-weight: 900; text-transform: uppercase;line-height:1.8rem;">{{mb_substr($seen['user_name'], 0, 1, "UTF-8")}}{{mb_substr($seen['user_last_name'], 0, 1, "UTF-8")}}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </li>
@endforeach