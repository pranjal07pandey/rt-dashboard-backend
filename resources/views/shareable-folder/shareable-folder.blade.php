<html lang="en">
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
    <link rel="stylesheet" href="{{ asset('assets/dashboard/dist/css/skins/_all-skins.min.css') }}"/>
    {!! Html::style('assets/dashboard/css/bootstrap-material-design.min.css') !!}
    {{ Html::style('assets/dashboard/css/ripples.min.css') }}
    <link type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"/>

    {{ Html::style('assets/dashboard/css/custom.css') }}
    {{ Html::style('assets/dashboard/css/dashboardv2.css')}}
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/dashboard/js/jquery-3.1.1.js') }}"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->



    <script type="text/javascript">
        var base_url = "{{ url('') }}";
    </script>
</head>
<body>
<div class="content-wrapper">
    <div class="container" style="position:relative;">
        <div style="background-image: url('../assets/dashboard/share-folder.jpg');min-height: 840px;background-position: center;background-repeat: no-repeat;background-size: cover;filter: blur(8px); -webkit-filter: blur(4px);" ></div>
        <div class="modal fade" id="shareableLogin" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none;">
            <div id="second"  class="modal-dialog modal-md" role="document">
                {{--<div id="model" data-target="#myModal"></div>--}}
                <div class="modal-content"  style="margin-top: 30%;     border-radius: 8px;">
                    <div class="modal-header themeSecondaryBg">
                        <h4 class="modal-title text-center" id="myModalLabel">PLEASE LOGIN TO VIEW THIS SHARED FOLDER</h4>
                    </div>
                    <input type="hidden" class="shareableUserId">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="errorMessage label-danger" > </p>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">mail</i>
                                    </span>
                                    <div class="form-group is-empty">
                                        <input type="email" class="form-control emailData" placeholder="E-Mail Address" name="email" value="" required="required" autofocus="autofocus"><span class="material-input"></span>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="material-icons">lock_outline</i>
                                    </span>
                                    <div class="form-group is-empty">
                                        <input type="password" class="form-control passwordData" placeholder="Password" name="password" value="" required="required" autofocus="autofocus"><span class="material-input"></span>
                                        <a tabindex="0" style=" position: absolute;padding: 7px 7px 7px 7px;background: none;    font-size: 24px;margin: 0 0 0px -6px;" class="btn btn-lg btn-danger" role="button" data-toggle="popover"  title="Password" data-content="Please ask the person who shared this link for the password"><i class="fa fa-question-circle" aria-hidden="true" style="color: #2471ba;"></i></a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" >
                        <button type="button" class="btn btn-primary loginShareable" style="    background: #003f67;color: #fff;" >Login</button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container -->
</div><!-- /.content-wrapper -->
<script src="{{ asset('assets/dashboard/bootstrap/js/bootstrap.min.js') }}"></script>
<script>
    $(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    });
</script>
{{--<script src="{{ asset('assets/dashboard/js/drag.js') }}"></script>--}}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/js/material.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/dashboard/js/ripples.min.js') }}"></script>


<script src="{{ asset('assets/dashboard/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<!-- AdminLTE App -->
<script src="{{ asset('assets/dashboard/dist/js/app.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="{{ asset('js/dashboard.js') }}"></script>

<script type="text/javascript">;
    $(document).ready(function(){
        $.material.init();
    });

    $(document).ready(function(){
        $('[data-toggle="popover"]').popover({
            placement : 'left',
            trigger : 'hover'
        });
    });
</script>

<style>
    .popover-title{
        background: #2570ba;
        color: #ffffff;
    }
    .popover-content{
        color: #000000;
    }
    .popover.top {
        margin-top: -3px;
    }
    .modal-footer{
        text-align: center;
        height: 60px;
    }
    .btn{
        background: #003f67;
        color: #fff;
    }

    .modal-header {
        border-top-right-radius: 8px;
        border-top-left-radius: 8px;
    }
    .errorMessage{
        display: none;
        padding: 8px;
    }


</style>
<script>

    $(document).ready(function(){
        var url = window.location.href;
        var token = url.split('/')[4];
        $.ajax({
            type:'post',
            url :base_url+'/folder/verifyToken',
            data:{token:token},
            success:function (response) {
                if(response['status'] == 404){
                    window.location.replace(base_url+'/dashboard/error/404');
                }else if(response['status'] == 200){
                    if(response['data'] == "auth"){
                        if(response['url'] == null){
                            $('#shareableLogin').modal('show');
                        }else{
                            window.location.replace(response['url']);
                        }

                    }else if(response['data'] == "no-auth"){
                        window.location.replace(response['url']);
                    }
                }else{
                    $('#shareableLogin').modal('show');
                }

            }

        })
    })

    $(document).on('click','.loginShareable',function () {
        var url = window.location.href;
        $('.errorMessage').css('display','none');
        var email = $('.emailData').val();
        var password = $('.passwordData').val();
        var token = url.split('/')[4];
        $.ajax({
            type:"POST",
            url:  base_url+'/folder/login',
            data: {email:email,password:password,token:token},
            success: function (response) {
                if(response['status'] == false){
                    $('.errorMessage').css('display','block');
                    $('.errorMessage').text(response['message'])
                }else{
                    window.location.replace(response['data']);
                }
            }
        })
    });

</script>
</body>
</html>









