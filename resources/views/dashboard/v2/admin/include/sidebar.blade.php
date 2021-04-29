<div class="sidebar primaryBackgroundColor">
    <div class="header primaryBackgroundColor">
        <span>Record Time</span>
    </div>
    <div class="profile primaryBackgroundColor">
        <div class="profileWrapper" style="background-image: url('https://oms.dev/assets/dashboard/images/avatar.png');"></div>
        <div class="profileDetails">
            <strong>{{ Auth::user()->first_name." ".Auth::user()->last_name }}</strong>
            <span>{{ Auth::user()->email }}</span>
        </div>
    </div>
    <div class="nav lightPrimaryBackgroundColor" style="height: calc(100vh - 155px);width:100%;">
        <ul data-widget="tree" class="tree">
            {{--<li><a href="{{ url('dashboard') }}"><i class="material-icons">dashboard</i> Dashboard</a></li>--}}
            <li><a href="{{ url('dashboard/reports') }}" @if(Session::get('navigation')=='reports') class="active secondaryBackgroundColor" @endif><i class="material-icons">dvr</i> Reports</a></li>
            <li><a href="{{ route('report_by_comapny') }}" @if(Session::get('navigation')=='company') class="active secondaryBackgroundColor" @endif><i class="material-icons">account_balance</i> Company</a></li>
            <li><a href="{{ route('non_active_company') }}" @if(Session::get('navigation')=='non_active_company') class="active secondaryBackgroundColor" @endif><i class="material-icons">account_balance</i> Non Active Company</a></li>

            <li><a href="{{ route('stripe_invoices') }}" @if(Session::get('navigation')=='stripe_invoices') class="active secondaryBackgroundColor" @endif><i class="material-icons">attach_money</i> Stripe Invoices</a></li>
            <li><a href="{{ url('dashboard/subscriptionPlan') }}" @if(Session::get('navigation')=='subscriptionPlan') class="active secondaryBackgroundColor" @endif><i class="material-icons">view_list</i>  Subscription Plans</a></li>
            <li><a href="{{ url('dashboard/addOnsManagement') }}" @if(Session::get('navigation')=='addOnsManagement') class="active secondaryBackgroundColor" @endif><i class="material-icons">add_shopping_cart</i>  Add-ons Management</a></li>
            <li><a href="{{ url('dashboard/appSetting') }}" @if(Session::get('navigation')=='appSetting') class="active secondaryBackgroundColor" @endif><i class="material-icons">settings_cell</i> App Setting</a></li>
            <li><a href="{{ url('dashboard/documentThemes') }}" @if(Session::get('navigation')=='documentThemes') class="active secondaryBackgroundColor" @endif><i class="material-icons">text_format</i> Document Themes</a></li>
            <li><a href="{{ url('dashboard/defaultTemplate') }}"><i class="material-icons">sd_storage</i> Default Templates</a></li>
            <li><a href="{{ route('category_list') }}" @if(Session::get('navigation')=='feature') class="active secondaryBackgroundColor" @endif><i class="material-icons">sd_storage</i> Feature</a></li>
            <li><a href="{{ route('purchase_themes_company') }}" @if(Session::get('navigation')=='purchasedThemes') class="active secondaryBackgroundColor" @endif><i class="material-icons">sd_storage</i> Purchased Themes</a></li>
            <li><a href="{{ route('docket.field.category')  }}" @if(Session::get('navigation')=='docketField') class="active secondaryBackgroundColor" @endif><i class="material-icons">extension</i> Docket Field Category</a></li>
            <li>
                <a class="dropdown-item" href="{{ route('logout') }}">
                    <i class="material-icons">power_settings_new</i> {{ __('Logout') }}
                </a>
            </li>
        </ul>
    </div>
</div>
<style>
    ul.tree:before {
        background-image: url({{ asset('assets/dashboard/images/dashboardBackground.jpg') }});
    }
</style>