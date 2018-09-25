

@extends('layouts.app')

    <!-- Page -->
    @section('content')
    {{-- <div class="">{{ __('Login') }}</div> --}}
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
      <div class="page-content vertical-align-middle">
        <div class="panel">
          <div class="panel-body">
            <div class="brand">
              <img class="brand-img logo-login" src="{{ asset('adminremark/assets/images/logo0.png') }}" alt="...">
              {{-- <h2 class="brand-text font-size-18">Remark</h2> --}}
            </div>
            <form autocomplete="off"   method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                @csrf
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus/>
                <label class="floating-label">{{ __('E-Mail') }}</label>
                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
              </div>
              <div class="form-group form-material floating" data-plugin="formMaterial">
                <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required />
                <label class="floating-label">{{ __('Password') }}</label>
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
              </div>
              <div class="form-group clearfix">
                <div class="checkbox-custom checkbox-inline checkbox-primary checkbox-lg float-left">
                  <input type="checkbox" id="inputCheckbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                  <label for="inputCheckbox">{{ __('Lembrar login') }}</label>
                </div>
                {{-- <a class="float-right" href="{{ route('password.request') }}">{{ __('Esqueceu sua Senha?') }}</a> --}}
              </div>
              <button type="submit" class="btn btn-primary btn-block btn-lg mt-40">{{ __('Sign in') }}</button>
            </form>
            {{-- <p>Não tem Conta? Porfavor <a href="register-v3.html">Cadastrar-se</a></p> --}}
          </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
          <p>CloudFox TLC</p>
          <p>© 2018. Todos Direitos Reservado.</p>
          <div class="social">
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-twitter" aria-hidden="true"></i>
          </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-facebook" aria-hidden="true"></i>
          </a>
            <a class="btn btn-icon btn-pure" href="javascript:void(0)">
            <i class="icon bd-google-plus" aria-hidden="true"></i>
          </a>
          </div>
        </footer>
      </div>
    </div>
    @endsection
    <!-- End Page -->


