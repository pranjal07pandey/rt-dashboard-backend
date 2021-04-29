<!--Navbar-->
<nav class="navbar navbar-expand-lg fixed-top">
    <!-- Navbar brand -->
    <a class="navbar-brand" href="#">
        @if(Session::get('navigationIcon'))<i class="material-icons">{{ Session::get('navigationIcon') }}</i> @else  <i class="material-icons">menu</i> &nbsp;  @endif

            @if(Session::get('pageTitle')) {{ Session::get('pageTitle') }} @else Dashboard @endif</a>

    <!-- Collapse button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#basicExampleNav" aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Collapsible content -->
    <div class="collapse navbar-collapse" id="basicExampleNav">

        <!-- Links -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="material-icons">notifications_none</i><small>0</small> </a>
                {{--<div class="dropdown-menu dropdown-primary" aria-labelledby="navbarDropdownMenuLink" id="notificationList">--}}
                    {{--<a class="dropdown-item" href="#">Action</a>--}}
                    {{--<a class="dropdown-item" href="#">Another action</a>--}}
                    {{--<a class="dropdown-item" href="#">Something else here</a>--}}
                {{--</div>--}}
            </li>
            <li class="nav-item">
                <a  class="nav-link"  href="{{ route('logout') }}">
                    <i class="material-icons">power_settings_new</i>
                </a>
            </li>

        </ul>
        <!-- Links -->
    </div>
    <!-- Collapsible content -->

</nav>
<!--/.Navbar-->