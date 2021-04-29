<select  id="frameworkemailemployee"  class="form-control"  required  name="recipient" >
    @if($emailRecepients)
        @foreach ($emailRecepients as $rows)
            <option value="{{ $rows['id']}}">{{ $rows['name'] }}</option>
        @endforeach
    @endif
</select>