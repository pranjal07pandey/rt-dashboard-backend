<div class="messageUserInfo">
    <div class="profileInfo">
        <strong>
            @if($messageGroup->title!=null)
                {{ $messageGroup->title }}
            @else
                @foreach($messageGroup->messagesGroupUserinfo as $messageGroupUser)
                    @if($messageGroupUser->user_id!=Auth::user()->id || $messageGroup->messagesGroupUserinfo->count()==1)
                        {{ $messageGroupUser->userInfo->first_name." ".$messageGroupUser->userInfo->last_name }}
                    @endif
                @endforeach
            @endif
        </strong>
    </div>
    <div class="clearfix"></div>
</div>

<div class="messages" style="overflow-x: hidden;height: 490px;padding-right: 15px;">
    <ul class="messageList messagegrup{{$messageGroup->id}}" style="overflow: hidden;">
        @if($messageList)
            @include('dashboard.company.message-reminders.partials.message-list')
        @endif
        <div id="smoothscroll"></div>
    </ul>
</div>

<div style="height: 72px;display: flex;align-items: center;" class="message-form-wrapper">
    <input type="hidden" value="{{$messageGroup->id}}" id="groupId" >
    <input style="background: rgba(0, 0, 0, .05);width: calc(100% - 90px);border-radius: 20px;font-size: 13px;padding: 0px 15px;margin: 0px;height: 45px;margin-right: 10px;" class="form-control" id="chatMessage" placeholder="Type a message..." >
    <button class="btn btn-sm btn-info btn-raised chatData submit" style="height: 45px;border-radius: 22px;">Send</button>
</div>