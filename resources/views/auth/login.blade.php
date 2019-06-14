@extends('layouts.auth')

 @section('title', '- Login')
    @section('content')


    <div class="container d-flex justify-content-center">
        <div class="content-holder">
            <div class="page-content d-flex flex-column justify-content-center">

                <div class="logobar text-center">
                    <img src="{{ asset('adminremark/assets/images/logo-oficial.svg') }}" alt="CloudFox">
                </div>

                <div class="toggle d-flex justify-content-center">
                    <div class="row align-items-center">
                        <div id="login" class="col-6 toggle-text active">LOGIN</div>
                        <div id="cadastro" class="col-6 toggle-text">CADASTRE-SE</div>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="panel">

                    <div id="panel-login">
                        <h3 class="text-center"> Acesse sua conta </h3>
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
                            </div>

                            <div class="input-holder">
                                <input type="password" placeholder="{{ __('Password') }}" class="{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required  required>
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

                                    <span class="sm-text gray">Salvar login</span>
                                </div>

                                <div class="forgot-pass d-flex text-right">
                                    <a href="#" class="sm-text"> Esqueci minha senha </a>
                                </div>

                            </div>

                            <div class="clearfix"></div>

                            <button type="submit" value="Entrar com e-mail" class="btn btn-primary orange"> Entrar com e-mail </button>

                            <div class="btnfix"></div>

                         
                        </form>


                    </div>

                    <div id="panel-sign-up" style="display:none;">

                        <h3 class="text-center"> Cadastre-se </h3>
                        <form action="">


                            <div class="btnfix"></div>

                
                            <div class="input-holder">
                                <input type="text" id="newuser_name" placeholder="Nome completo" required>
                            </div>

                            <div class="input-holder">
                                <input type="email" id="newuser_email" placeholder="E-mail" required>
                            </div>

                            <div class="input-holder">
                                <input type="tel" id="newuser_phone" placeholder="Celular" required>
                            </div>

                            <div class="input-holder">
                                <input type="cpf" id="newuser_cpf" placeholder="CPF" required>
                            </div>

                            <div class="clearfix"></div>

                            <input type="button" value="Cadastrar com e-mail" class="btn btn-primary orange">

                            <div class="btnfix"></div>


                            </form>
                    </div>

                    <div class="hr"></div>

<div class="d-flex justify-content-center align-items-center">
    <a class="sm-text linkfooter" href="#"> Política de Privacidade </a>
    <div class="oval"></div>
    <a class="sm-text linkfooter" href="#"> Termos e Condições </a>
    <div class="oval"></div>
    <a class="sm-text linkfooter" href="#"> Suporte </a>

</div>

<div class="clearfix"></div>

                </div>


            </div>
        </div>
    </div>
    @endsection


