@extends('layouts.auth')

 @section('title', '- Login')
    @section('content')
    <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out" style="background-image: linear-gradient(to right, #e6774c, #f92278);">
      <div class="page-content vertical-align-middle" style="height: 100%">
        <div class="panel">
          <div class="panel-body">
            <div class="brand">
              <img class="brand-img logo-login" src="{{ asset('adminremark/assets/images/cloudfox_logo.png') }}" alt="...">
            </div>
            <form autocomplete="off" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
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
              <div class="form-group clearfix" style="margin-bottom:30px">
              </div>
              <button type="submit" class="btn btn-block btn-lg mt-40" style="background-image: linear-gradient(to right, #FFA500, #D2691E);">{{ __('Sign in') }}</button>
            </form>
          </div>
        </div>

        <footer class="page-copyright page-copyright-inverse">
          <p>CloudFox - help@cloudfox.app</p>
          <p>© 2019 - All rights reserved</p>
        </footer>
      </div>
    </div>
@endsection

