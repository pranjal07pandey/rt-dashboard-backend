<select class="form-control selectUrl" >
        <option value="0">Please Select Url</option>
    @foreach($allUrl as $allUrls)
                <option value="{{$allUrls->id}}" @if($girdfield->id == $allUrls->id) selected @endif>{{$allUrls->url}}</option>
    @endforeach
</select>
