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
                        <span role="alert">
                            <strong>{{ $email }}</strong>
                        </span>
                        <form autocomplete="off" method="POST" action="{{ route('password.reset.post') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">
                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('New password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                @if( isset($errors) && count($errors) > 0 )
                                    <span class="invalid-feedback" role="alert">
                                        @foreach( $errors->all() as $error )
                                            <strong>{{ $error}}</strong><br>
                                        @endforeach
                                    </span>
                                @endif
                                @if(session()->has('error'))
                                    <span class="invalid-feedback" role="alert">
                                        {{session('error') }}
                                    </span>
                                @endif
                                @if(session()->has('warning'))
                                    <span class="invalid-feedback" role="alert">
                                        {{session('warning') }}
                                    </span>
                                @endif
                                @if(session()->has('success'))
                                    <span class="invalid-feedback" role="alert">
                                        {{session('success') }}
                                    </span>
                                @endif
                            </div>
                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('Re-type new password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password_confirmation" required>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="clearfix"></div>
                            <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Change</button>
                            <div class="btnfix"></div>
                        </form>
                    </div>
                    <div id="panel-recover" style="display:none;">
                        <h3 class="text-center"> Recover your password </h3>
                        <form>
                            <div class="form-group input-holder">
                                <input type="email" name="email" class="{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('E-Mail') }}" required autofocus></span>
                            </div>
                        </form>
                        <div class="password-alert text-center">
                            We will send a recover link to your e-mail address.
                        </div>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <button id='send_recovery_link' type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Send recover link</button>
                        </div>
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




@endsection


