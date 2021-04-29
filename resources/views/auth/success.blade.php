@extends('layouts.app')
@section('content')
<section style="margin-top: 130px;" class="email-verigication-rt">
    <div class="container">
        <div class="row">
            <div class="col col-md-12 col-sm-12 col-xs-12 text-center box-part-rtmid">
                <div class="box-qtsptod" style="background: url({{asset("assets/email.png")}});background-size: cover;">
                </div>
                <div class="text-contentqtsptod">
                    <h3>Check Your inbox to confirm your email address</h3>
                    <p>We have sent an email to <b>{{$userEmail}}</b>.Please click the link in the email to verify your account.</p>

                    <p>If the email address, listed above is incorrect, please go back to the signup page and signup again with the correct email address.</p>
                    <a href="{{ url('login') }}" class=" btn-gotologin">Login</a>
                </div>
            </div>
        </div>
    </div>
    </div>
</section>

<style>
    main{
        width: 70%;
        height: 50vh;
        padding-top:  10%;
        margin: 0px auto;

    }
    .box-part-rtmid{
        -webkit-box-shadow: 0px 2px 24px 0px rgba(130,130,130,1);
        -moz-box-shadow: 0px 2px 24px 0px rgba(130,130,130,1);
        box-shadow: 0px 2px 24px 0px rgba(130,130,130,1);
        background: #fff;
        margin-top: 50px;
        padding: 0px;
    }
    .box-qtsptod{
        width: 100%;
        min-height: 300px;
        background-size: contain;
        background-position: center;
    }
    .text-contentqtsptod{
        padding-top: 30px;
        padding-bottom: 30px;
    }
    .text-contentqtsptod h3{
        text-transform:uppercase;
        font-size: 20px;
        font-weight: 800;
    }
    .text-contentqtsptod p{
        margin-bottom: 4px;

    }
    .btn-gotologin{
        background: #00849c;
        color: #fff !important;
        padding: 8px 40px;
        text-transform: uppercase;
        margin-top: 20px;
        letter-spacing: 1px;
        border-radius: 0px;
        -webkit-box-shadow: 0px 2px 24px 0px rgba(130,130,130,1);
        -moz-box-shadow: 0px 2px 24px 0px rgba(130,130,130,1);
        box-shadow: 0px 2px 24px 0px rgba(130,130,130,1) !important;
        display: inline-block;
    }
    .btn-gotologin:hover{
        color: #fff;
        background: #00849c;


    }
</style>
@endsection
