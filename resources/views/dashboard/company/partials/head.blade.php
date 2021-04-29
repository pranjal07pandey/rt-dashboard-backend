<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RecordTime | Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/dashboard/img/favicon.png') }}"/>

    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/bootstrap/css/bootstrap.min.css') }}">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/dist/css/AdminLTE.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/dashboard/dist/css/skins/_all-skins.min.css') }}"/>
    {!! Html::style('assets/dashboard/css/bootstrap-material-design.min.css') !!}
    {{ Html::style('assets/dashboard/css/ripples.min.css') }}
    <link type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"/>

    {{ Html::style('assets/dashboard/css/custom.css') }}
    {{ Html::style('assets/dashboard/css/dashboardv2.css')}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @toastr_css
    @yield('css')
    <script src="{{ asset('assets/dashboard/js/jquery-3.1.1.js') }}"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-139409412-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-139409412-1');
    </script>

    <script type="text/javascript">
        var base_url = "{{ url('') }}";
    </script>
    <!-- Start of  Zendesk Widget script -->
    <script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key=feda57d8-6292-4633-b228-35dab70e4312"> </script>
    <!-- End of  Zendesk Widget script -->
</head>