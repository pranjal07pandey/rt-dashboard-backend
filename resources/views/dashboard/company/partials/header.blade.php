<div class="rtNavBar">
    <nav class="navbar navbar-default">
        <div class="topNavbar">
            <div class="headerBG "></div>
            <div class="container">
                <div style="height: 60px;width: 100%;overflow: hidden;position:relative;">
                    <div class="topNavRight">
                        <ul>
                            <li class="hidden-sm hidden-xs">
                                <a href="https://recordtime.com.au/contact-invoicing-docketing-construction-paperwork-management-app" target="_blank">
                                    <i class="material-icons">headset_mic</i> Contact Us 24/7
                                </a>
                            </li>
                            <li class="hidden-sm hidden-xs">
                                <a href="https://recordtime.com.au/frequently-asked-questions" target="_blank">
                                    <i class="material-icons">live_help</i> Help
                                </a>
                            </li>
                            <li class="hidden-xs">
                                <?php $clientRequest  =   \App\ClientRequest::where('status',0)->Where('requested_company_id',Session::get('company_id'))->get(); ?>
                                <a href="{{ route('clients.request') }}">
                                    <i class="material-icons">notifications</i>
                                    @if(count($clientRequest))<span class="label label-warning">&nbsp;</span>@endif
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('companyProfile') }}" style="margin-left:0px;margin-right: 0px;">
                                    @if(AmazoneBucket::fileExist(auth()->user()->image))
                                    <img src="{{ AmazoneBucket::url() }}{{ auth()->user()->image }}" class="user-image" alt="User Image">
                                    @else
                                    @endif
                                    @if(\Illuminate\Support\Facades\Auth::user()->first_name!='') {{ \Illuminate\Support\Facades\Auth::user()->first_name }} {{ \Illuminate\Support\Facades\Auth::user()->last_name }}
                                    @else {{ \Illuminate\Support\Facades\Auth::user()->email }} @endif
                                    {{-- <i class="material-icons" style="font-size: 20px;margin-top: 0px;margin-right: -8px;">more_vert</i> --}}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="twoLinesWrapper">
                        <img src="{{ asset('assets/dashboard/img/logo.png') }}" height="40px" style="position: absolute;z-index: 3;top: 10px;">
                        <div class="twoLines visible-lg"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="navbar-header hidden-lg">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li  class="{{ request()->is('dashboard/company') ? 'active' : '' }}">
                        <a href="{{ url('/') }}" style="padding-left: 0px;">
                            <i class="material-icons">dashboard</i>Dashboard
                        </a>
                    </li>
                    <li class="dropdown {{ request()->is('dashboard/company/employeeManagement*') ? 'active' : '' }}" >
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">supervised_user_circle</i>Employee Management <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('employeeManagement.index') }}"> Manage Employees</a></li>
                            <li><a href="{{ route('message-reminder.index') }}"> Message/Reminders</a></li>
                            <li><a href="{{ route('leave_management.index') }}"> Leave Management</a></li>
                            <li><a href="{{ route('machine_management.index') }}"> Manage Machine</a></li>
                            <li><a href="{{ route('machine_management.availability') }}"> Machine Availability Management</a></li>
                        </ul>
                    </li>
                    <li class="{{ request()->is('dashboard/company/clientManagement*') ? 'active' : '' }}"><a href="{{ route('clientManagement.index') }}"><i class="material-icons">business_center</i>Client</a></li>
                    <li class="dropdown {{ request()->is('dashboard/company/docketBookManager*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">filter_none</i>Docket Book Manager <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('dockets.allDockets') }}">Dockets</a></li>
                            <li><a href="{{ route('dockets.template.index') }}">Docket Templates</a></li>
                            <li><a href="{{ route('companyAssignDockets') }}">Assign Dockets Template</a></li>
                            <li><a href="{{ route('companyAssignDocketsCalender') }}">Assign Task/Job</a></li>
                            <li><a href="{{ route('companyDocketLabel') }}">Docket Label</a></li>
                            <li><a href="{{ url('dashboard/company/profile/docketSetting') }}">Docket Settings</a></li>
                            <li><a href="{{ url('dashboard/company/docketManager/project') }}">Project</a></li>
                            <li><a href="{{ route('templateBank') }}">Docket Bank</a></li>
                        </ul>
                    </li>
                    <li class="dropdown {{ request()->is('dashboard/company/invoiceManager*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons">local_atm</i>
                            Invoice Manager <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('invoices.allInvoices') }}">Invoices</a></li>
                            <li><a href="{{ route('companyInvoiceManager') }}">Invoice Templates</a></li>
                            <li><a href="{{ route('companyAssignInvoice') }}">Assign Invoice Template</a></li>
                            <li><a href="{{ route('companyInvoiceLabel') }}">Invoice Label</a></li>
                            <li><a href="{{ url('dashboard/company/profile/invoiceSetting') }}">Invoice Settings</a></li>
                        </ul>
                    </li>
                    <li class="dropdown {{ request()->is('dashboard/company/timers*') ? 'active' : '' }}">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="material-icons" >pie_chart</i>Utilities <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('timers') }}"> Timer</a></li>
                            <li><a href="{{ route('companyPrefillerManager') }}">Prefiller Manager</a></li>
                            <li><a href="{{ route('companyDocumentTheme') }}">Document Theme</a></li>
                            <li><a href="{{ route('companyDocumentManager') }}">Document Manager</a></li>
                            <li><a href="{{ url('dashboard/company/xero/companyXeroManager/') }}">TimeSheet</a></li>
                            <li><a href="{{ route('templateBank') }}">Template Bank</a></li>

                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}" style="padding-right: 0px;">
                            <i class="material-icons">power_settings_new</i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</div>