<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="https://www.recordtimeapp.com.au/images/favicon.png"/>

    <title>Record TIME | Login</title>

    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    {{ Html::style('assets/beta/bootstrap/css/bootstrap.min.css') }}
    {{ Html::style('assets/beta/css/bootstrap-material-design.min.css') }}
    {{ Html::style('assets/beta/css/style.css') }}

    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body style="background-color: #FAFAFA;">
    <nav class="navbar navbar-fixed-top">
        <div class="container">
            <div class="twoLinesWrapper" style="left:0px;">
                <div class="whiteBackground"></div>
                <div class="twoLines"></div>
            </div>
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"><img src="{{ asset('assets/beta/images/logoWhite.jpg') }}" style="height:100%;"></a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="{{ url('login') }}"><i class="material-icons">fingerprint</i> Login</a></li>
                    <li style="margin-right: 40px;"><a href="{{ url('registration') }}"><i class="material-icons">person_add</i> Sign up</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </nav><!--/.navbar-->

    @yield('content')

    <div class="loginFooterWrapper">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h3>About Record Time</h3>
                    <p>Record TIME is a revolutionary application that will help you manage your paperwork. The software is specifically designed for the construction industry, with the ability to create Modular Dockets. Any paperwork can be turned digital in seconds.</p>
                </div>
                <div class="col-md-5">
                    <div class="footerNav">
                        <ul>
                            <li> <a href="https://recordtime.com.au/terms-of-use" target="_blank">Terms of Use </a></li>
                            <li> <a href="https://recordtime.com.au/license-aggrement" target="_blank">Licence Agreement</a></li>
                            <li> <a href="https://recordtime.com.au/contact-us" target="_blank">Contact Us</a></li>
                            <li> <a href="https://help.recordtime.com.au/hc/en-us/categories/360000153335-General" target="_blank">FAQ</a></li>
                            <li> <a href="https://help.recordtime.com.au/hc/en-us/categories/360000153516-How-to-use-the-website" target="_blank">How To's</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <h3>Download Our App</h3>
                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <a href="https://itunes.apple.com/au/app/record-time/id971035780?mt=8" target="_blank" title="Apple Store Download Link">
                                    <img src="{{ asset('assets/beta/images/iOSBadge.png') }}" alt="Download Record Time for iPhone Devices" style="max-width: 130px;">
                                </a>
                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6">
                                <a href="https://play.google.com/store/apps/details?id=com.webtecsolutions.recordtimeapp" target="_blank"  title="Google Play Store Download Link">
                                    <img src="{{ asset('assets/beta/images/androidBadge.png') }}" alt="Download Record Time for Android Devices" style="max-width: 130px;">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <br>

             <p class="navbar-text" style="font-size:12px;margin-top:0px;margin-left: 0px;">© 2015 - 2017 Record TIME Pty Ltd. All rights reserved. ABN: 99 604 582 649 | Unit 5, 9 Beaconsfield Street Fyshwick ACT 2609 AUSTRALIA | Support: 0421 955 630
            </p>

            <a href="https://www.webtecsolutions.com.au/" class="text-black pull-right" style="font-size:12px;color:#fff !important;">Developed By: WebTec Solutions</a>
        </div>
    </div>


    <script type="text/javascript" src="{{ asset('assets/beta/js/material.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/beta/js/ripples.min.js') }}"></script>

     <script type="text/javascript">;
        $(document).ready(function(){
            $.material.init();
        });
    </script>
    <script type="text/javascript">
        var csrfToken = $('[name="csrf_token"]').attr('content');

        setInterval(refreshToken, 3600000); // 1 hour

        function refreshToken(){
            $.get('refresh-csrf').done(function(data){
                csrfToken = data; // the new token
            });
        }

        setInterval(refreshToken, 3600000); // 1 hour

    </script>
    @yield('customScript')
</body>
</html>
