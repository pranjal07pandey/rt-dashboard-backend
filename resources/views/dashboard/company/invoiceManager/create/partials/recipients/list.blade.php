@if($request->type==1)
    <select id="frameworkemployee" class="form-control docketTemplete"  required  name="recipient" >
        <option value="{{ $company->user_id }}">{{ $company->userInfo->first_name }} {{ $company->userInfo->last_name }}</option>
        @if($company->employees)
            @foreach($company->employees as $employee)
                @if(@$employee->userInfo->isActive == 1)
                    <option value="{{ $employee->userInfo->id }}">{{ $employee->userInfo->first_name }} {{ $employee->userInfo->last_name }}</option>
                @endif
            @endforeach
        @endif
    </select>
@else
    <select  id="frameworkemailemployee"  class="form-control"  required  name="recipient" >
        @if($company->emailClients)
            @foreach ($company->emailClients as $rows)
                <option value="{{ $rows->email_user_id }}">{{ $rows->emailUser->email }}</option>
            @endforeach
        @endif
    </select>
@endif