<section class="company-summary-widget">
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <a href="{{ route('dockets.allDockets') }}" class="smallBoxfooter">
                <div class="rtSmallBox">
                    <div class="inner">
                        <span class="title">Dockets</span>
                        <strong class="counter">{{ $totalDockets }}</strong>
                    </div>
                    <div class="icon">
                        <i class="material-icons">filter_none</i>
                    </div>
                    <small>More info</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-xs-6">
            <a href="{{ route('invoices.allInvoices') }}" class="smallBoxfooter">
                <div class="rtSmallBox">
                    <div class="inner">
                        <span class="title">Invoices</span>
                        <strong class="counter">{{ $totalInvoices }}</strong>
                    </div>
                    <div class="icon" id="invoiceIcon">
                        <i class="material-icons">local_atm</i>
                    </div>
                    <small>More info</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-xs-6">
            <a href="{{ url('dashboard/company/employeeManagement') }}" class="smallBoxfooter">
                <div class="rtSmallBox">
                    <div class="inner">
                        <span class="title">Employees</span>
                        <strong class="counter">{{ count($company->getAllCompanyUserIds()) }}</strong>
                    </div>
                    <div class="icon" id="employeeIcon">
                        <i class="material-icons">supervised_user_circle</i>
                    </div>
                    <small>More info</small>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-xs-6">
            <a href="{{ route('clients.request') }}" class="smallBoxfooter">
                <div class="rtSmallBox">
                    <div class="inner">
                        <span class="title">Client Request</span>
                        <strong class="counter">{{ count($company->clientRequest) }}</strong>
                    </div>
                    <div class="icon" id="clientRequestIcon">
                        <i class="material-icons">business_center</i>
                    </div>
                    <small>More info</small>
                </div>
            </a>
        </div>
        <div class="clearfix"></div>
    </div>
</section><!--/.company-summary-widget-->