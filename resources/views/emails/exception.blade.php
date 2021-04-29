<div style="padding:10px;">
    @if(@Session::get('company_id')) Company ID :  {{ @Session::get('company_id') }}<br/> @endif
    @if($request->header('companyid')) Company ID : {{ $request->header('companyid') }}<br/> @endif
    @if(@Auth::user()->id) User ID: {{ @Auth::user()->id." ".@Auth::user()->email }}<br/> @endif
    @if($request->header('userid')) User ID : {{ $request->header('userid') }} <br/>@endif
    Request : {{ $request->fullUrl() }}<br/>
    Form Data : @php print("<pre>".print_r($request->query(),true)."</pre>") @endphp
    {!! $error !!}
</div>