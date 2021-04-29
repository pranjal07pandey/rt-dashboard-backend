<select id="frameworkemployee" class="form-control docketTemplete"  required  name="recipient" >
    <option value="{{ $company->user_id }}">{{ $company->userInfo->first_name }} {{ $company->userInfo->last_name }}</option>
    @if(@$employee)
        @foreach ($employee as $rows)
            @if(@$rows->userInfo['isActive'] == 1)
                <option value="{{ $rows->userInfo['id']}}">{{ $rows->userInfo['first_name'] }} {{ $rows->userInfo['last_name'] }}</option>
            @endif
        @endforeach
    @endif
</select>