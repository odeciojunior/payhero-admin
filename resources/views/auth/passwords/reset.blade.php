@extends('layouts.auth')

@section('title', '- Login')

@section('content')

    <div class="container d-flex justify-content-center">
        <div class="content-holder">
            <div class="page-content d-flex flex-column justify-content-center">

                <div class="logobar text-center">
                    <img src="{{ asset('modules/global/adminremark/assets/images/logo-oficial.svg') }}" alt="CloudFox">
                </div>

                <div class="clearfix"></div>

                <div class="panel">

                    <div id="panel-login">
                        <h3 class="text-center"> Change your password </h3>
                        <form autocomplete="off" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                            @csrf
                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('New password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('Re-type new password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>



                            <div class="clearfix"></div>

                            <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Change </button>

                            <div class="btnfix"></div>
                        </form>

                    </div>

                    <div id="panel-recover" style="display:none;">
                        <h3 class="text-center"> Recover your password </h3>

                        <form>
                            <div class="input-holder">
                                <input type="email" name="email" class="{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('E-Mail') }}" required autofocus>
                                </span>
                            </div>
                        </form>

                        <div class="password-alert text-center">
                            We will send a recover link to your e-mail address.
                        </div>

                        <div class="clearfix"></div>

                        <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Send recover link </button>
                        <div class="btnfix"></div>

                        <div class="text-left">
                            <a id="backLogin" role="button" class="sm-text d-flex align-items-center">  <i class="material-icons md-18">keyboard_arrow_left</i> Back to Login </a>
                        </div>

                    </div>
                </div>
                <div class="hr"></div>

                <div class="d-flex justify-content-center align-items-center">
                    <a class="linkfooter" target="_blank" href="https://cloudfox.net/terms" style="font-size: 11px"> Terms & Conditions </a>
                    <div class="oval"></div>
                    <a class="linkfooter" href="#" style="font-size: 11px"> Support </a>
                </div>

                <div class="clearfix"></div>

            </div>


        </div>
    </div>


    <script>
        $(document).ready(function(){
            $("#forgotClick").click(function(){
                $("#panel-login").slideUp( "800" ).delay( "250" ).fadeOut( "800" ).hide();
                $("#panel-recover").slideDown( "800" ).delay( "250" ).fadeIn( "800" ).show();
            });

            $("#backLogin").click(function(){
                $("#panel-recover").slideUp( "800" ).delay( "250" ).fadeOut( "800" ).hide();
                $("#panel-login").slideDown( "800" ).delay( "250" ).fadeIn( "800" ).show();
            });
        });

    </script>

@endsection


