@extends('layouts.app')

@section('content')
<div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="loginBox">
                    <h3>RESET YOUR PASSWORD</h3>
                        @if (session('status'))
                           <div class="alert alert-success fade in alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                {{ session('status') }}
                            </div>
                        @endif
                        @if ($errors->has('email'))
                            <div class="alert alert-danger fade in alert-dismissable">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                                {{ $errors->first('email') }}
                            </div>
                        @endif

                        <form role="form" method="POST" action="{{ url('password/email') }}">
                            {{ csrf_field() }}

                            <div class="input-group">
                                <span class="input-group-addon">
                                    <i class="material-icons">mail</i>
                                </span>
                                <div class="form-group is-empty">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required placeholder="E-Mail Address">
                                </div>
                            </div>

                            <center>
                                <button type="submit" class="btn btn-primary" style="background: #003f67;color: #fff;">
                                    Send Password Reset Link
                                </button>
                            </center>
                        </form>
                </div>
            </div>
        </div>
</div><!--/.container-->
@endsection
