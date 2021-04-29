@extends('layouts.app')

@section('content')
<div class="container">
    <div class="loginBox">
        <div class="row">
            <div class="col-md-5 ">
                <h3>&nbsp;&nbsp;LOGIN TO RECORD TIME</h3>
                <form  role="form" method="POST" action="{{ route('login') }}">
                    @if( $errors->has('password'))
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.passwordLogin').modal();
                            });
                        </script>
                        <div class="modal fade passwordLogin" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="top: 159px;">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="background: #16969b;">
                                        <h5 style="color: #ffffff;margin-top: -4px;margin-bottom: 13px;" class="modal-title" id="exampleModalLongTitle"><i class="fa fa-info-circle" aria-hidden="true"></i>  Warning</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger fade in alert-dismissable" style="padding: 0px 0px 0px 20px;font-size: 14px;background-color: transparent;color: #2b2b2bbf;font-weight: 500;margin-bottom: 0px;">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                            {{ $errors->first('password') }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button style="text-transform: capitalize;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if( $errors->has('email'))
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.emailLogin').modal();
                            });
                        </script>
                        <div class="modal fade emailLogin" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="top: 159px;">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="    background: #16969b;">
                                        <h5 style="color: #ffffff;margin-top: -4px;margin-bottom: 13px;" class="modal-title" id="exampleModalLongTitle"><i class="fa fa-info-circle" aria-hidden="true"></i>  Warning</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-danger fade in alert-dismissable" style="padding: 0px 0px 0px 20px;font-size: 14px;background-color: transparent;color: #2b2b2bbf;font-weight: 500;margin-bottom: 0px;">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                            {{ $errors->first('email') }}
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button style="text-transform: capitalize;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(session('message'))
                            <div class="alert alert-success fade in alert-dismissable" style="margin-bottom:0px;margin-left:10px;margin-top:20px;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                            {{ session('message') }}
                        </div>
                    @endif

                    @if( Session::has('flash_notification.message'))
                        <script type="text/javascript">
                            $(document).ready(function() {
                                $('.login').modal();
                            });
                        </script>
                        <div class="modal fade login" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" style="top: 159px;">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" style="    background: #16969b;">
                                        <h5 style="color: #ffffff;margin-top: -4px;margin-bottom: 13px;" class="modal-title" id="exampleModalLongTitle"><i class="fa fa-info-circle" aria-hidden="true"></i>  Warning</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="dashboardFlash">
                                            <div  class="alert alert-{{ Session::get('flash_notification.level') }}" style="padding: 0px 0px 0px 20px;font-size: 14px;background-color: transparent;color: #2b2b2bbf;font-weight: 500;     margin-bottom: 0px;">
                                                    {{ Session::get('flash_notification.message') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button style="text-transform: capitalize;" type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{ csrf_field() }}
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">mail</i>
                        </span>
                        <div class="form-group is-empty">
                            <input type="email" class="form-control" placeholder="E-Mail Address" name="email" value="" required="required" autofocus="autofocus"><span class="material-input"></span>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group is-empty">
                            <input type="password" class="form-control" placeholder="Password" name="password" value="" required="required" autofocus="autofocus"><span class="material-input"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember"> Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <a  href="{{ route('password.request') }}" style="margin-top: 14px;display: block;text-align: right;margin-right: -15px;">
                            Forgot Your Password?
                        </a>
                    </div>
                    <div class="clearfix"></div>
                    <center>
                        <br/>
                        <button type="submit" class="btn btn-primary" style="background: #003f67;color: #fff;">
                        Login
                        </button>
                    </center>
                </form>
            </div>
            <div class="col-md-offset-2 col-md-5">
                <a data-toggle="modal" data-target="#myModal" class="btn btn-xs  btn-info playvideo" style="width: 100%;margin-top: 50px; padding: 0px; position: relative;">
                    <img src="https://www.recordtimeapp.com.au/backend/assets/dashboard/img/backendloginpage.jpg" style="width: 100%;">
                    <div class="playBtnDiv">
                        <i id="playBtn" class="fa fa-play-circle-o"></i>
                    </div>
                </a><br/>
                <span style="color: #999;"><center>No more manual docket signatures.</center></span>
            </div>
        </div>
    </div>
</div>

<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header themeSecondaryBg">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">No more manual docket signatures.</h4>
            </div>
            <div class="modal-body">
                <iframe width="100%" height="500" id="youtubeVideo" src="" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>

 <script type="text/javascript">
    $(document).ready(function(){
        $(".playvideo").on("click", function(){
            $('#youtubeVideo').attr('src', 'https://www.youtube.com/embed/Nj0JWYoyuTU'); 
        });
        $('#myModal').on('hidden.bs.modal', function () {
            $('#youtubeVideo').attr('src', '');
        });
    });
</script> 
@endsection