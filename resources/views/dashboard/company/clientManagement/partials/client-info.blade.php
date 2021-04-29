<div class="row">
    <div class="col-lg-3 col-xs-6">
        <div class="small-box themePrimaryBg">
            <div class="inner">
                <h3>{{ $clients->count() }}</h3>
                <p>My Clients</p>
            </div>
            <div class="icon">
                <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('clientManagement.index') }}" class="small-box-footer"  style="z-index: 0;">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <div class="small-box themePrimaryBg">
            <div class="inner">
                <h3>{{ $company->clientRequest->count() }}</h3>
                <p>Client Request</p>
            </div>
            <div class="icon">
                <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('clients.request') }}" class="small-box-footer"  style="z-index: 0;">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box themePrimaryBg">
            <div class="inner">
                <h3>{{ $company->unapprovedClientRequest->count() }}</h3>
                <p>Unapproved Clients</p>
            </div>
            <div class="icon">
                <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('clients.request.unapproved') }}" class="small-box-footer"  style="z-index: 0;">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box themePrimaryBg">
            <div class="inner">
                <h3>{{ $company->emailClients->count() }}</h3>
                <p>Custom Email Clients</p>
            </div>
            <div class="icon">
                <i class="ion ion-email"></i>
            </div>
            <a href="{{ route('clients.emails.index') }}" class="small-box-footer"  style="z-index: 0;">
                More info <i class="fa fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>