@extends('layouts.app')

@section('content')
    <div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="loginBox">
                <h3 style="margin-bottom: 26px;"><center>REGISTER FOR PAPERLESS DOCKETING APPLICATION - RT</center></h3>

                @if ($errors->has('email'))
                     <div class="alert alert-danger fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        {{ $errors->first('email') }}
                    </div>
                @endif

                @if ($errors->has('password'))
                    <div class="alert alert-danger fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        {{ $errors->first('password') }}
                    </div>
                @endif

                @if ($errors->has('g-recaptcha-response'))
                    <div class="alert alert-danger fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                        {{ $errors->first('g-recaptcha-response') }}
                    </div>
                @endif

                <form role="form" method="POST" action="{{ route('registration') }}">
                    {{ csrf_field() }}

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">mail</i>
                        </span>
                        <div class="form-group is-empty">
                            <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}"  autofocus="autofocus" required placeholder="E-Mail Address">
                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group is-empty">
                            <input type="password" class="form-control" placeholder="Password" name="password" value="" required="required"><span class="material-input"></span>
                        </div>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock_outline</i>
                        </span>
                        <div class="form-group is-empty">
                            <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" value="" required="required" autofocus="autofocus"><span class="material-input"></span>
                        </div>
                    </div>
                    
                    <center>
                        <div class="g-recaptcha" data-sitekey="6LdmJeEZAAAAADee9jTBUNCAiCNuC6sspDzu8GMV"></div><br>
                        <input type="checkbox" onchange="activateButton(this)">
                         I have read and I agree to the
                        <a href="http://recordtime.com.au/terms-of-use/" target="_blank">terms of use</a> &
                        <a href="http://recordtime.com.au/licence-agreement/" target="_blank">license agreement</a>
                        <br/>
                        <button type="submit" class="btn btn-primary" style="background: #003f67;color: #fff;" id="submit">
                        Register
                        </button>
                    </center>
                </form>
            </div>
        </div>
    </div>
    </div>
    <style>
        input[type='checkbox']{
            zoom: 1;
            margin-right: 5px;
            -ms-transform: scale(1.3);
            -webkit-transform: scale(1.3);
            -o-transform: scale(1.3);
            -moz-transform: scale(1.3);
        }
    </style>
@endsection
@section('customScript')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        $(document).ready(function(){
            document.getElementById("submit").disabled = true;
        });

        function activateButton(element) {
            if(element.checked) {
                document.getElementById("submit").disabled = false;
            }
            else  {
                document.getElementById("submit").disabled = true;
            }
        }
    </script>
@endsection
