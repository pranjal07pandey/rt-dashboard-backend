<div style="margin-top: 4px;" class="form-group">
    <label class="control-label" for="title">
        <input type="checkbox" name="nastedLabels" id="nastedLabel"  checked> Subfolder</label>
    <select  class="form-control"   required  name="folder_id" id="folderSelect">
        <option class="dislableNested" value="0">Please select a parent...</option>
        @if(@$data)
            @foreach ($data as $datas)
              <option value="{{$datas['id']}}">{!! $datas['space'] !!}{{$datas['name'][0]}}</option>
            @endforeach
        @endif
    </select>
</div>
