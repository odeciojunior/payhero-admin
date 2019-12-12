@extends('layouts.auth')
@section('title', '- Login')

@section('content')

    <div class="container d-flex justify-content-center">
        <div class="content-holder">
            <div class="page-content d-flex flex-column justify-content-center">
                <div class="logobar text-center">
                    <img src="{{ asset('modules/global/adminremark/assets/images/logo-oficial.svg') }}" alt="CloudFox">
                </div>
                <div class="toggle d-flex justify-content-center">
                    <div class="row align-items-center">
                        <div id="login" class="col-6 toggle-text active" style='cursor:pointer;'>LOGIN</div>
                        <div id="cadastro" class="col-6 toggle-text" style='cursor:pointer;' onclick="window.open('http://www.cloudfox.net','_blank');">SIGN UP</div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <div class="panel">
                    <div id="panel-login">
                        <h3 class="text-center"> Access your account </h3>

                        /* blocked account */
                        @if($errors->any())
                            <strong><h4 style="color: red">{{$errors->first()}}</h4></strong>
                        @endif

                        <form autocomplete="off" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                            @csrf
                            <div class="input-holder">
                                <input type="email" name="email" class="{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('E-Mail') }}" required autofocus>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('Password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="input-holder d-flex justify-content-between">
                                <div class="switch-holder d-flex align-items-center">
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="sm-text gray">Remember me</span>
                                </div>
                                <div class="forgot-pass d-flex text-right">
                                    <a id="forgotClick" role="button" class="sm-text d-flex align-items-center">
                                        <i class="material-icons md-18 mr-">fingerprint</i> Forgot my password
                                    </a>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Sign in</button>
                            <div class="btnfix"></div>
                        </form>
                    </div>
                    <div id="panel-recover" style="display:none;">
                        <h3 class="text-center"> Recover your password </h3>
                        <form id='form_recovery_password' method="POST" action="{{ route('password.email') }}" aria-label="{{ __('Reset Password') }}">
                            @csrf
                            <div class="input-holder">
                                <input type="email" name="email" class="{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('E-Mail') }}" required autofocus></span>
                            </div>
                            <div class="password-alert text-center">
                                We will send a recover link to your e-mail address.
                            </div>
                            <div class="clearfix"></div>
                            <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Send recover link</button>
                        </form>
                        <div class="btnfix"></div>
                        <div class="text-left">
                            <a id="backLogin" role="button" class="sm-text d-flex align-items-center">
                                <i class="material-icons md-18">keyboard_arrow_left</i> Back to Login
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
                <div class="d-flex justify-content-center align-items-center">
                    <a class="linkfooter" target="_blank" href="https://cloudfox.net/terms" style="font-size: 11px"> Terms & Conditions</a>
                    <div class="oval"></div>
                    <a class="linkfooter" href="#" style="font-size: 11px"> Support</a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $("#forgotClick").click(function () {
                $("#panel-login").slideUp("800").delay("250").fadeOut("800").hide();
                $("#panel-recover").slideDown("800").delay("250").fadeIn("800").show();
            });

            $("#backLogin").click(function () {
                $("#panel-recover").slideUp("800").delay("250").fadeOut("800").hide();
                $("#panel-login").slideDown("800").delay("250").fadeIn("800").show();
            });
        });
    </script>


    @push('scripts')
        <script src="{{ mix('/modules/auth/js/reset.js') }}"></script>
    @endpush
@endsection


