<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">

    <title>{{ whitelabel_app_name() }} - Login</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon"
          sizes="180x180"
          href="{{ whitelabel_logo('icon') }}">
    <link rel="icon"
          type="image/png"
          sizes="32x32"
          href="{{ whitelabel_favicon() }}">
    <link rel="icon"
          type="image/png"
          sizes="16x16"
          href="{{ whitelabel_favicon() }}">
    <link rel="mask-icon"
          href="{{ whitelabel_logo('icon') }}"
          color="{{ whitelabel_color('primary') }}">
    <meta name="theme-color"
          content="{{ whitelabel_color('primary') }}">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&amp;display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,300;1,400&amp;display=swap"
          rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mulish:ital,wght@0,300;0,400;0,600;0,700;1,400&amp;display=swap"
          rel="stylesheet">

    <!-- Whitelabel Dynamic CSS -->
    <link rel="stylesheet"
          href="{{ route('whitelabel.css') }}">
          
    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            outline: 0;
            box-sizing: border-box;
        }

        body {
            font: 14px Poppins, sans-serif;
            color: #2e2e2e;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .img-container {
            flex: 1;
            background: url("https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-002/book.10242ce7.jpg") center center / cover no-repeat;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: center;
            flex: 1;
            max-width: 700px;
            background-color: #FFFFFF;
        }

        .content-logo {
            display: block;
            margin-top: 80px;
            margin-bottom: 55px;
        }

        .form-container {
            width: 60%;
            padding: 10px;
        }

        #form-forgot {
            display: none;
        }

        .form-container form {
            font-family: Mulish, sans-serif;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .input-pad {
            position: relative;
            padding: 8px 0 0;
            margin-top: 35px;
            width: 100%;
            height: 20px;
        }

        .input-pad input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #2e2e2e;
            padding: 9px 0 4px;
            font-family: Roboto, sans-serif;
            font-size: 14px;
            font-weight: 400;
            font-style: italic;
            transition: border-color 0.2s ease 0s;
        }

        .input-pad input::placeholder {
            color: transparent;
        }

        .input-pad label {
            position: absolute;
            top: 20px;
            display: block;
            font-family: Roboto, sans-serif;
            font-weight: normal;
            font-size: 10px;
            opacity: 0.4;
            transition: all 0.2s ease 0s;
        }

        .input-pad input:focus {
            border-bottom: 2px solid #2e2e2e;
        }

        .input-pad input:focus+label,
        .input-pad input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 12px;
            opacity: 1;
        }

        .input-pad .btn-show {
            position: absolute;
            top: 12px;
            right: 0;
            width: 20px;
            height: 20px;
            background-color: transparent;
            background-image: url("https://accounts.azcend.com.br/static/media/eye.62517b9b.svg");
            background-size: 20px;
            opacity: 0.5;
            border: none;
            cursor: pointer;
        }

        .invalid-feedback {
            display: none;
            margin-top: 4px;
            font-size: 12px;
            color: #dd2f14;
        }

        .input-pad.error input {
            border-color: #dd2f14;
        }

        .input-pad.error input+label {
            opacity: 1 !important;
            color: #dd2f14;
        }

        .input-pad.error .invalid-feedback {
            display: block;
        }

        .links-container {
            text-align: center;
        }

        .btn-primary {
            height: 40px;
            width: 100%;
            padding: 10px 20px;
            margin: 60px 0 33px;
            border: 1px solid #FFFFFF;
            border-radius: 4px;
            text-transform: uppercase;
            background: #191919;
            color: #FFFFFF;
        }

        .btn-link-secondary {
            border: none;
            background-color: transparent;
            font-size: 14px;
            color: #BCBCBC;
            cursor: pointer;
        }

        .btn-link-secondary:hover {
            color: #808080;
        }

        .btn-link-primary {
            border: none;
            background-color: transparent;
            font-size: 14px;
            color: #02ce7c;
            cursor: pointer;
        }

        .btn-signup {
            display: block;
            margin-top: 18px;
            text-decoration: none;
            color: #00b7ff;
        }

        @media (max-width: 768px) {
            .img-container {
                display: none;
            }

            .form-container {
                width: 100%;
                padding: 20px;
            }
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="img-container"></div>
        <div class="login-container">
            <img class="content-logo"
                 src="{{ whitelabel_logo('login') }}"
                 alt=""
                 width="300">
            <div class="form-container"
                 id="form-login">
                <p class="title">Acessar conta</p>
                <p>Insira seus dados para continuar</p>
                <form method="POST"
                      action="{{ route('login') }}">
                    @csrf
                    <div class="input-pad @if ($errors->has('email')) error @endif">
                        <input id="email"
                               name="email"
                               type="email"
                               placeholder="Email">
                        <label for="email">Email</label>
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    </div>
                    <div class="input-pad @if ($errors->has('password')) error @endif">
                        <input id="password"
                               name="password"
                               type="password"
                               placeholder="Senha">
                        <label for="password">Senha</label>
                        <button class="btn-show"
                                type="button"></button>
                        <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                    </div>
                    <button type="submit"
                            class="btn-primary">Acessar</button>
                </form>
                <div class="links-container">
                    <button id="btn-forgot-password"
                            class="btn-link-secondary">Esqueci minha senha</button>
                    <a class="btn-signup"
                       href="https://accounts.azcend.com.br/signup">
                        <p>Não tem conta? CADASTRE-SE</p>
                    </a>
                </div>
            </div>
            <div class="form-container"
                 id="form-forgot">
                <p class="title">Esqueceu a senha?</p>
                <p>Não tem problema! Só precisamos do email que você usou ao criar seu cadastro na Azcend.</p>
                <form method="POST"
                      action="{{ route('password.email') }}">
                    @csrf
                    <div class="input-pad @if ($errors->has('email')) error @endif">
                        <input id="email-forgot"
                               name="email"
                               type="email"
                               placeholder="Email">
                        <label for="email-forgot">Email</label>
                        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                    </div>
                    <button type="submit"
                            class="btn-primary">Enviar</button>
                </form>
                <div class="links-container">
                    <button class="btn-link-primary"
                            id="btn-signin">Fazer login</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = () => {

            Array.from(document.querySelectorAll('.input-pad input'))
                .forEach(elem => {
                    elem.onclick = () => {
                        Array.from(document.querySelectorAll('.input-pad.error'))
                            .forEach(elem => {
                                elem.classList.remove('error');
                            });
                    }
                });

            Array.from(document.querySelectorAll('.input-pad .btn-show'))
                .forEach(elem => {
                    elem.onclick = function() {
                        let input = this.parentNode.querySelector('input');
                        input.setAttribute('type', input.type === "password" ? "text" : "password");
                    }
                });

            document.querySelector('#btn-forgot-password').onclick = () => {
                document.querySelector('#form-login').style.display = 'none';
                document.querySelector('#form-forgot').style.display = 'block';
            }

            document.querySelector('#btn-forgot-password').onclick = () => {
                document.querySelector('#form-login').style.display = 'none';
                document.querySelector('#form-forgot').style.display = 'block';
            }

            document.querySelector('#btn-signin').onclick = () => {
                document.querySelector('#form-login').style.display = 'block';
                document.querySelector('#form-forgot').style.display = 'none';
            }
        }
    </script>
</body>

</html>
