<div class="shell">
    <div class="head">
        <div class="menu">
            <h5 style="margin-top: 14px;font-size: 14px;font-weight: 600;margin-bottom: 30px;">Invoices</h5>
            @php
                $folder_status  =    0;
                if(Route::currentRouteName()=='invoices.allInvoices'){ $folder_status = 2; }
                if(Route::currentRouteName()=='invoices.sentInvoices'){ $folder_status = 6; }
                if(Route::currentRouteName()=='invoices.receivedInvoices'){ $folder_status = 7; }
                if(Route::currentRouteName()=='invoices.emailedInvoices'){ $folder_status = 8; }
            @endphp
            <input type="hidden" value="{{ $folder_status }}" id="folder_status">
            <a href="{{ route('invoices.create') }}" class="rounded-primary-btn" style="margin-right: 10px;margin-bottom: 15px;">Create New Invoice</a>
            <ul>
                <li @if(Route::currentRouteName()=='invoices.allInvoices')class="active"@endif><a href="{{ route('invoices.allInvoices') }}">All Invoices</a></li>
                <li @if(Route::currentRouteName()=='invoices.sentInvoices')class="active"@endif><a href="{{ route('invoices.sentInvoices') }}">Sent Invoices</a></li>
                <li @if(Route::currentRouteName()=='invoices.receivedInvoices')class="active"@endif><a href="{{ route('invoices.receivedInvoices') }}">Received Invoices</a></li>
                <li @if(Route::currentRouteName()=='invoices.emailedInvoices')class="active"@endif><a href="{{ route('invoices.emailedInvoices') }}">Emailed Invoices</a></li>
            </ul>
        </div>
    </div>
</div>