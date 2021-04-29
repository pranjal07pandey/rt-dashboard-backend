<div class="form-group" style="    padding-bottom: 34px;margin: 0;">
    <label for="employeeId"  class="control-label">Move To</label>
    <select id="folderFramework" class="form-control "  required  name="folder_id">
        @if(@$data)
            @foreach ($data as $datas){
               <option value="{{$datas['id']}}">{{$datas['value']}}</option>
            @endforeach
        @endif
    </select>
</div>

