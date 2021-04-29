<div class="form-group" style="    padding-bottom: 34px;margin: 0;">
    <input type="hidden" value="{{$fieldId}}" class="folderFieldId">
    <label for="employeeId"  class="control-label">Move To</label>
    <select  class="form-control defaultFolderId"  required  name="folder_id">
        <option>Please select folder</option>
        @if(@$data)
            @foreach ($data as $datas){
            <option value="{{$datas['id']}}" @if($defaultId == $datas['id']) selected @endif>{{$datas['value']}}</option>
            @endforeach
        @endif
    </select>
</div>
                