
<div class="col-md-12">
    <h5><strong>Add People</strong></h5>

    <p class="errorMessageShareable label-danger" > </p>

    <div class="col-md-5" style="padding: 0;">
        <input class="form-control shareableEmail" type="email" placeholder="Email" >
    </div>
    <div class="col-md-5">
        <input class="form-control sharePassword" type="password" placeholder="Password">
    </div>
    <div class="col-md-2" >
        <button class="btn btn-info submitUserShareable"> Add </button>
    </div>
</div>
<div class="col-md-12" >

    <div class="shareableFolderUsers">

        <ul>
            @if($folders->shareableFolder != null)
               @if(count($folders->shareableFolder->shareableFolderUsers) !=0 )
                @foreach($folders->shareableFolder->shareableFolderUsers as $shareableFolderUsers)
                    <li >
                        <span style="text-transform: capitalize;">{{$shareableFolderUsers->email[0]}}</span>
                        &nbsp;{{$shareableFolderUsers->email}}
                        <a style="margin-left: 18px; font-size: 24px;color: #4bd7f3;" class="editShareableUsers" data-shareableuserId="{{$shareableFolderUsers->id}}" data-shareableuserEmail="{{$shareableFolderUsers->email}}"><img src="{{asset('assets/dashboard/padlock.svg')}}"></a>
                        <a style="font-size: 18px;color: red;" class="deleteShareableUsers" data-shareableuserId="{{$shareableFolderUsers->id}}"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </li>
                @endforeach
               @else
                    <div class="text-center" style="color: #9e9e9e;">
                        Empty
                    </div>
               @endif
            @endif

        </ul>


    </div>

</div>


<div class="col-md-12">
    <h5><strong>Access Mode</strong></h5>
    @if($shareablefolder == 'data')
        <select class="sharefolderSelect">
            <option @if($folders->shareableFolder->shareable_type == "Restricted") selected @endif value="Restricted">Restricted - The link + the username and password is required to view this folder.
            </option>
            <option  @if($folders->shareableFolder->shareable_type == "Public") selected @endif value="Public">Public - Anyone with the link can access this folder.</option>
            <option  @if($folders->shareableFolder->shareable_type == "Disabled") selected @endif value="Disabled">Disabled - Folder access is disabled for anyone with the link.</option>
        </select>

        @else
        <select class="sharefolderSelect">
            <option  value="Restricted">Restricted</option>
            <option  value="Public">Public</option>
            <option  value="Disabled">Disabled</option>
        </select>

    @endif
</div>
<div class="col-md-12">
    <div class="approvalShareableLink">
        <h5><strong>Approval/Shareable Link</strong></h5>
        <div class="col-md-10 link" >

            @if($shareablefolder == 'data')
             <input style="background: #F6F6F6;" class="form-control" id="copyUrl{{ $folders->shareableFolder->id }}"  value="{{url('/folder/'.$folders->shareableFolder->link)}}">
            @else
                <input style="background: #F6F6F6;" class="form-control" disabled value="">
            @endif

        </div>

        <div class="col-md-2" >
            @if($shareablefolder == 'data')
               <button class="btn btn-info copyurl" data-clipboard-target="#copyUrl{{ $folders->shareableFolder->id }}" > Copy Link </button>
             @else
                <button class="btn btn-info" disabled> Copy Link </button>
            @endif
        </div>
    </div>

</div>