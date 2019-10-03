<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cadastro | CloudFox </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{asset('modules/register/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('modules/register/css/animate.css')}}">
    <link rel="stylesheet" href="{{asset('modules/register/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('modules/register/css/jquery-ui.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <link rel='stylesheet' href="{{ asset('modules/global/css/sweetalert2.min.css') }}">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('modules/global/img/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('modules/global/img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('modules/global/img/favicon-16x16.png') }}">
    <link rel="mask-icon" href="{{ asset('modules/global/img/safari-pinned-tab.svg') }}" color="#5bbad5">
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/css/bootstrap-extend.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/assets/css/site.min.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/loading.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/checkAnimation.css') }}">
    <!-- Plugins -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery-mmenu.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <link rel='stylesheet' href="{{ asset('modules/global/css/sweetalert2.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" href="{{ asset('modules/global/jquery-imgareaselect/css/imgareaselect-default.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('modules/global/adminremark/global/fonts/web-icons/web-icons.min.css') }}">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.materialdesignicons.com/3.7.95/css/materialdesignicons.min.css">
    <link href="https://fonts.googleapis.com/css?family=Muli:400,700,800&display=swap" rel="stylesheet">
    <!-- New CSS -->
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/new-site.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/reports.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/register/css/animateColor.css') }}">
    <script src="https://code.jquery.com/jquery-3.4.1.js"
            integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
            integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
            crossorigin="anonymous"></script>
</head>
<body style='padding-top:0px;'>
<div id='loadingOnScreen' style='height:100%; width:100%; position:absolute'>
</div>
<section class="topbar" style=''>
    <div class="container">
        <div class="d-flex align-items-center content-top">
            <img src="{{asset('modules/register/img/fox-60.png')}}">
            <span class="toptitle bold ml15"> Bem vindo! </span>
        </div>
    </div>
</section>
<div class="progress">
    <div class="progress-bar progress-bar-indicating active progress-bar-success" id="progress-bar-register" role="progressbar" aria-valuenow="25" aria-valuemin="0"
         aria-valuemax="100">
    </div>
</div>
<section class="holder-content">
    <div class="container">
        <div class="wrap">
            <form class="mt10">
                <div id="login" class="div1">
                    <h4 class="bold title-content">Dados básicos</h4>
                    <p class="desc"> Preencha os dados atentamente.
                    </p>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="firstname">Nome</label>
                                <p class='sm-tex text-danger' id='nameError' style='display:none;'>O campo nome é obrigatório</p>
                                <input type="text" name="firstname" id="firstname" placeholder="Digite seu nome" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="lastname">Sobrenome</label>
                                <p class='sm-tex text-danger' id='lastNameError' style='display:none;'>O campo sobrenome é obrigatório</p>
                                <input type="text" name="lastname" id="lastname" placeholder="Digite seu sobrenome" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="email">E-mail</label>
                                <p class='sm-tex text-danger' id='emailError' style='display:none;'>O campo email é obrigatório</p>
                                <input type="email" name="email" id="email" value="" placeholder="Digite seu email" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="phone">Celular</label>
                                <p class='sm-tex text-danger' id='phoneError' style='display:none;'>O campo celular é obrigatório</p>
                                <input type="text" name="cellphone" id="phone" placeholder="Digite seu celular" required>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center" style='margin-bottom:5%'>
                        <div class="col-lg-6">
                            <div class="senha mt30">
                                <h4 class="bold title-content">Crie uma senha </h4>
                                <p class="desc"> Ok. Para garantir seu acesso, você precisa de uma senha. </p>
                            </div>
                            <div class="input-holder d-flex flex-column">
                                <label for="password">Senha</label>
                                <p class='sm-tex text-danger' id='passwordError' style='display:none;'>O campo senha é obrigatório</p>
                                <input type="password" name="password" id="password" value="" placeholder="Password" required>
                                <div class='pass-container' style='width: 199px;'></div>
                                <div class='pass-hint'></div>
                            </div>
                            <p class="sm-text">Evite senhas usadas em outros sites e que sejam fáceis de identificar.</p>
                            <div class="progress align-items-center justify-content-between">
                                </span>
                                <div class="progress-bar" id='progress-password' role="progressbar" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                                <span class="bold ml15 text-progress" id="text-password"> </span>
                            </div>
                        </div>
                        <div class="col-lg-5 justify-content-center">
                            <div class="password-tip">
                                <h5> Sua senha deve conter: </h5>
                                <div class="passtip-item mt10 d-flex align-items-center">
                                        <span id='number-count-correct' class="check" style='display:none;'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.393 7.5l-5.643 5.784-2.644-2.506-1.856 1.858 4.5 4.364 7.5-7.643-1.857-1.857z"/>
                                            </svg>
                                        </span>
                                    <span id='number-count-incorrect' class="check">
                                            <svg class="wrong" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style='fill:#D30D0C'>
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5 15.538l-3.592-3.548 3.546-3.587-1.416-1.403-3.545 3.589-3.588-3.543-1.405 1.405 3.593 3.552-3.547 3.592 1.405 1.405 3.555-3.596 3.591 3.55 1.403-1.416z"/>
                                            </svg>
                                        </span>
                                    <span> 8 ou mais caracteres </span>
                                </div>
                                <div class="passtip-item d-flex align-items-center">
                                        <span id="length-correct" class="check" style="display:none">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.393 7.5l-5.643 5.784-2.644-2.506-1.856 1.858 4.5 4.364 7.5-7.643-1.857-1.857z"/>
                                            </svg>
                                        </span>
                                    <span id="length-incorrect" class="check">
                                            <svg class="wrong" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style='fill:#D30D0C'>
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5 15.538l-3.592-3.548 3.546-3.587-1.416-1.403-3.545 3.589-3.588-3.543-1.405 1.405 3.593 3.552-3.547 3.592 1.405 1.405 3.555-3.596 3.591 3.55 1.403-1.416z"/>
                                            </svg>
                                        </span>
                                    <span> Pelo menos um número</span>
                                </div>
                                <div class="passtip-item d-flex align-items-center">
                                        <span id='character-count-correct' class="check" style='display:none;'>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm4.393 7.5l-5.643 5.784-2.644-2.506-1.856 1.858 4.5 4.364 7.5-7.643-1.857-1.857z"/>
                                            </svg>
                                        </span>
                                    <span id='character-count-incorrect' class="check">
                                            <svg class="wrong" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style='fill:#D30D0C'>
                                                <path d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5 15.538l-3.592-3.548 3.546-3.587-1.416-1.403-3.545 3.589-3.588-3.543-1.405 1.405 3.593 3.552-3.547 3.592 1.405 1.405 3.555-3.596 3.591 3.55 1.403-1.416z"/>
                                            </svg>
                                        </span>
                                    <span> Pelo menos uma letra </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="empresa" class="div2" style="display:none;padding-botton:10%;">
                    <h4 class="bold title-content">Cadastre sua empresa</h4>
                    <p class="desc"> Para cadastrar seus produtos e começar a vender, você precisa cadastrar sua empresa primeiro.
                    </p><br>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label>Selecione o país de sua empresa:</label>
                                <div class="btn-group colors select-country" data-toggle="buttons">
                                    <label id='btnBrasil' class="btn btn-outline-primary active ">
                                        <input type="radio" class="typeBrasil active" name="country" value="brasil" autocomplete="off" checked='checked'> Brasil
                                    </label>
                                    <label id='btnUSA' class="btn btn-outline-primary" disabled>
                                        <input type="radio" class="typeUSA" name="country" value="{{--usa--}}brasil" autocomplete="off" disabled> United States
                                    </label>
                                </div>
                                <input type="hidden" id="country" value="brasil">
                            </div>
                        </div>
                    </div>
                    <div id="brasil-form">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_company_document">CPF/CNPJ</label>
                                    <p class='sm-tex text-danger' id='brasilCompanyDocumentError' style='display:none;'>Documeto inválido</p>
                                    <input type="text" name="company_document" id="brasil_company_document" placeholder="Digite seu CPF ou CNPJ da empresa" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_fantasy_name">Nome Fantasia</label>
                                    <p class='sm-tex text-danger' id='brasilFantasyNameError' style='display:none;'>Documeto inválido</p>
                                    <input type="text" name="fantasy_name" id="brasil_fantasy_name" placeholder="Digite o nome fantasia" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_zip_code">CEP</label>
                                    <input type="text" name="brasil_zip_code" id="brasil_zip_code" placeholder="Digite o CEP" required>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-lg-6 col-sm-12 col-md-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_street">Rua</label>
                                    <input type="text" name="brasil_street" id="brasil_street" placeholder="Rua, Avenida..." required>
                                </div>
                            </div>
                            <div class="col-2 ">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_number">Nº</label>
                                    <input type="text" name="brasil_number" id="brasil_number" placeholder="Nº">
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_neighborhood">Bairro</label>
                                    <input type="text" name="brasil_neighborhood" id="brasil_neighborhood" placeholder="Digite o bairro" required>
                                </div>
                            </div>
                        </div>
                        <div class='row form-group' style='margin-bottom: 5%'>
                            <div class="col-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_state">Estado</label>
                                    <input type="text" name="brasil_state" id="brasil_state" placeholder="Estado" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="brasil_city">Cidade</label>
                                    <input type="text" name="brasil_city" id="brasil_city" placeholder="Digite a cidade" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="eua-form" style='display:none;'>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_fantasy_name">Legal Business Name</label>
                                    <p class='sm-tex text-danger' id='euaFantasyNameError' style='display:none;'>Invalid name</p>
                                    <input type="text" name="fantasy_name" id="eua_fantasy_name" placeholder="Enter your Legal Business Name" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_company_document">EIN</label>
                                    <p class='sm-tex text-danger' id='euaCompanyDocumentError' style='display:none;'>Invalid EIN</p>
                                    <input type="text" name="company_document" id="eua_company_document" placeholder="Enter your document" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt30">
                            <div class="col-lg-3">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_zip_code">ZIP/Postal Code</label>
                                    <input type="text" name="eua_zip_code" id="eua_zip_code" placeholder="Enter your ZIP Code" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_street"> Street Address</label>
                                    <input type="text" name="eua_street" id="eua_street" placeholder="Enter your address" required>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_number">Nº</label>
                                    <input Enter="text" name="eua_number" id="eua_number" placeholder="Nº">
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_state">State</label>
                                    <select name="eua_state" id="eua_state" required>
                                        <option value="AL">Alabama</option>
                                        <option value="AK">Alaska</option>
                                        <option value="AZ">Arizona</option>
                                        <option value="AR">Arkansas</option>
                                        <option value="CA">California</option>
                                        <option value="CO">Colorado</option>
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="DC">District Of Columbia</option>
                                        <option value="FL">Florida</option>
                                        <option value="GA">Georgia</option>
                                        <option value="HI">Hawaii</option>
                                        <option value="ID">Idaho</option>
                                        <option value="IL">Illinois</option>
                                        <option value="IN">Indiana</option>
                                        <option value="IA">Iowa</option>
                                        <option value="KS">Kansas</option>
                                        <option value="KY">Kentucky</option>
                                        <option value="LA">Louisiana</option>
                                        <option value="ME">Maine</option>
                                        <option value="MD">Maryland</option>
                                        <option value="MA">Massachusetts</option>
                                        <option value="MI">Michigan</option>
                                        <option value="MN">Minnesota</option>
                                        <option value="MS">Mississippi</option>
                                        <option value="MO">Missouri</option>
                                        <option value="MT">Montana</option>
                                        <option value="NE">Nebraska</option>
                                        <option value="NV">Nevada</option>
                                        <option value="NH">New Hampshire</option>
                                        <option value="NJ">New Jersey</option>
                                        <option value="NM">New Mexico</option>
                                        <option value="NY">New York</option>
                                        <option value="NC">North Carolina</option>
                                        <option value="ND">North Dakota</option>
                                        <option value="OH">Ohio</option>
                                        <option value="OK">Oklahoma</option>
                                        <option value="OR">Oregon</option>
                                        <option value="PA">Pennsylvania</option>
                                        <option value="RI">Rhode Island</option>
                                        <option value="SC">South Carolina</option>
                                        <option value="SD">South Dakota</option>
                                        <option value="TN">Tennessee</option>
                                        <option value="TX">Texas</option>
                                        <option value="UT">Utah</option>
                                        <option value="VT">Vermont</option>
                                        <option value="VA">Virginia</option>
                                        <option value="WA">Washington</option>
                                        <option value="WV">West Virginia</option>
                                        <option value="WI">Wisconsin</option>
                                        <option value="WY">Wyoming</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="input-holder d-flex flex-column">
                                    <label for="eua_city">City</label>
                                    <input type="text" name="eua_city" id="eua_city" placeholder="Enter your city" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div id="select-projeto" class="div3" style="display: none;">
                    <div class="text-center">
                        <h4 class="bold title-content">Vamos criar seu primeiro projeto?</h4>
                        <p class="desc"> Primeiro, conta pra gente, qual tipo de projeto? </p>
                    </div>
                    <div class="options-holder mt30">
                        <div class="row">
                            <div id="project-default">
                                <div class="project-option mt30">
                                    <div class="row align-items-center">
                                        <div class="col-lg-2 text-center">
                                            <svg class="plusvg" xmlns="http://www.w3.org/2000/svg" width="60"
                                                height="60"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/>
                                            </svg>
                                        </div>
                                        <div class="col-lg-10">
                                            <h4 class="bold title-content d-flex">Projeto Padrão</h4>
                                            <p class="desc"> Cadastrarei meus planos e produtos manualmente, optando ou não pelo serviço de integração </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="project-shopify">
                                <div class="project-option shopify mt30">
                                    <div class="row align-items-center">
                                        <div class="col-lg-2 text-center">
                                            <svg class="plusvg" xmlns="http://www.w3.org/2000/svg" width="60"
                                                height="60"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm6 13h-5v5h-2v-5h-5v-2h5v-5h2v5h5v2z"/>
                                            </svg>
                                        </div>
                                        <div class="col-lg-10 project-shopify">
                                            <h4 class="bold title-content d-flex align-items-center">
                                        <span> <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="30"
                                                    height="30" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px"
                                                    y="0px" viewBox="0 0 1000 1000"
                                                    enable-background="new 0 0 1000 1000" xml:space="preserve">
                                            <g>
                                                <g id="c2be471c56fc5b4dd571614bb3052f50">
                                                    <path
                                                        d="M824.5,201.7c0,0-0.8-4.4-3.3-6c-2.5-1.6-5.2-1.9-5.2-1.9l-78.8-5.9l-58-57.6c-2.2-1.7-4.7-2.7-7.3-3.2c-6.2-1.2-14.5,0.2-16,0.6l-30.1,9.3c-13.1-38.6-30.9-66-53.3-81.9c-16.6-11.7-34.8-16.7-54.4-15.2c-4.1-5.5-8.7-10.3-13.6-14.3C482.8,8,454.7,5.3,421.2,17.4C320.6,54,277.9,184.4,262.5,249.5l-87.7,27.2c0,0-20.6,5.9-25.5,11.7c-5.3,6.4-6.3,23.6-6.3,23.6L68.6,885.5L628.9,990l302.5-65.5L824.5,201.7z M514,171.6l-96.9,30c12.8-50.3,37.5-101.6,84.7-121.7C511.6,105.6,514.3,140.7,514,171.6z M432.7,49.1c20.5-7.4,36.2-7.1,48.1,0.9C417.5,78.8,389.9,151.5,378,213.7l-77.6,24C317.8,175.1,356.4,76.8,432.7,49.1z M487,471.8c-4.4-2.2-9.7-4.5-15.6-6.6c-5.9-2.2-12.4-4.3-19.5-6c-7-1.7-14.6-3.1-22.5-3.9c-7.9-0.8-16.2-1.1-24.8-0.6c-7.9,0.5-15,1.9-21.3,4.1c-6.2,2.2-11.7,5.3-16.1,9.1c-4.4,3.8-8,8.3-10.4,13.5c-2.5,5.2-3.9,11-4.2,17.5c-0.2,4.8,0.5,9.4,2.2,13.9c1.7,4.5,4.3,8.9,7.9,13.3c3.6,4.4,8.2,8.8,13.7,13.2c5.6,4.5,12.2,9.1,19.8,13.8c10.7,6.8,21.6,14.5,32,23.4c10.5,9,20.5,19.1,29,30.6c8.7,11.7,15.9,24.7,20.7,39.4c4.9,14.8,7.4,31.1,6.6,49c-1.3,29.4-7.7,54.7-18.4,75.7c-10.5,20.7-25,37-42.5,48.8c-17.1,11.5-36.9,18.7-58.4,21.6c-20.9,2.8-43.3,1.6-66.4-3.5c-0.2,0-0.4-0.1-0.6-0.1c-0.2,0-0.4-0.1-0.5-0.1c-0.2,0-0.4-0.1-0.6-0.1c-0.2,0-0.4-0.1-0.6-0.1c-10.8-2.6-21.3-6-31.1-10c-9.7-3.9-18.8-8.3-27-13.2c-8.2-4.8-15.6-9.9-22-15.1c-6.3-5.2-11.7-10.6-15.9-16.1l25.5-84.6c4.3,3.6,9.6,7.8,15.6,12c6.1,4.3,12.9,8.6,20.1,12.7c7.4,4.1,15.2,7.9,23.3,11c8.2,3.1,16.7,5.6,25,6.8c7.4,1.1,14,0.9,19.8-0.4c5.8-1.3,10.8-3.7,14.9-7.1c4.1-3.3,7.3-7.5,9.5-12.3c2.2-4.9,3.5-10.3,3.7-16.1c0.3-5.8-0.3-11.3-1.7-16.6c-1.5-5.3-3.9-10.5-7.3-15.6c-3.4-5.1-7.9-10.3-13.5-15.6c-5.6-5.3-12.3-10.7-20.2-16.5c-9.8-7.3-18.9-15.1-27.1-23.7c-8.1-8.5-15.3-17.6-21.1-27.6c-5.8-9.9-10.4-20.5-13.4-32.1c-3-11.5-4.3-24-3.7-37.6c1-22.7,5.5-43.6,13.1-62.5c7.7-19,18.6-36.1,32.2-50.8c13.9-15,30.8-27.7,50.3-37.4c20.1-10,43-17.1,68.4-20.4c11.8-1.6,23.1-2.3,33.8-2.3c10.9,0,21.1,0.6,30.5,1.8c9.5,1.2,18.2,3,25.9,5.2c7.8,2.2,14.5,4.8,20,7.6L487,471.8z M547.6,161.2c0-3.8-0.1-7.7-0.3-11.6c-1.1-30.1-5.2-55.5-12.2-76.1c7.6,0.8,14.5,3.3,20.8,7.7c17.5,12.4,30.2,37.4,39.1,65.3L547.6,161.2z"/>
                                                </g>
                                            </g>
                                        </svg> </span>
                                                Loja no Shopify
                                            </h4>
                                            <p class="desc"> Você será encaminhado para criar seu projeto integrado automaticamente com sua loja no Shopify </p>
                                        </div>
                                        <div class="badge-exclusive">
                                            EXCLUSIVO
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="company_id">
                </div>
                <div id="standard-project" class="div4" style="display: none;">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h4 class="bold title-content">Crie seu primeiro projeto</h4>
                            <p class="desc"> Na nossa plataforma, os projetos são as lojas. Você pode ter uma ou várias lojas. Não se preocupe, vamos te explicando passo a passo!
                            </p>
                            <div class="row">
                                <div class="col-lg-12 mt20">
                                    <div class="input-holder d-flex flex-column">
                                        <label for="project_name_standard">Nome</label>
                                        <input type="text" name="project_name_standard" id="project_name_standard" placeholder="Digite o nome do projeto" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-holder d-flex flex-column">
                                        <label for="project_desc_standard">Descrição</label>
                                        <textarea type="text" name="project_desc_standard" id="project_desc_standard" placeholder="Digite uma breve apresentação do projeto" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-holder d-flex flex-column">
                                        <label>Imagem do projeto</label>
                                        <label for="file-upload"
                                            class="btn btn-primary custom-upload d-flex justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M16 16h-3v5h-2v-5h-3l4-4 4 4zm3.479-5.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                                            </svg>
                                            Upload
                                        </label>
                                        <input id="file-upload" name='photo' type="file"/></div>
                                    <p class="sm-text gray"> Indicamos uma imagem 500x500
                                        <br> JPG, JPEG ou PNG de até 2MB</p>
                                </div>
                                <div class="col-lg-12">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 hidden-m d-flex justify-content-center">
                            <div class="project-card d-flex flex-column align-items-center hidden-m">
                                <div class="card-img">
                                    <img class="card-img-top" id='image_standard' src="{{asset('modules/register/img/projeto.png')}}" style='height:240px; width:240px;'>
                                </div>
                                <div class="card-body mt30">
                                    <h5 id='name_preview_standard' class="card-title">Nome do seu projeto</h5>
                                    <p id='description_preview_standard' class="card-text sm-text gray">
                                        Aqui irá a descrição do seu projeto. Ela poderá ser acessada por possiveis afiliados que visualizarem a vitrine da plataforma. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="shopify-project" class="div5" style="display: none">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h4 class="bold title-content">Novo projeto Shopify</h4>
                            <p class="desc"> Criando seu primeiro projeto 100% integrado ao Shopify
                            </p>
                            <div class="row">
                                <div class="col-lg-12 mt20">
                                    <div class="input-holder d-flex flex-column">
                                        <label for='project_shopify_token'>Token Shopify</label>
                                        <input type="text" name="project_shopify_token" id="project_shopify_token" placeholder="Insira o token do Shopify" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-holder d-flex flex-column">
                                        <label for="project_name_shopify">Nome</label>
                                        <input type="text" name="project_name_shopify" id="project_name_shopify" placeholder="Digite o nome do projeto" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-holder d-flex flex-column">
                                        <label for="project_desc_shopify">Descrição</label>
                                        <textarea type="text" name="project_desc_shopify" id="project_desc_shopify" placeholder="Digite uma breve apresentação do projeto" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="input-holder d-flex flex-column">
                                        <label>Imagem do projeto</label>
                                        <label for="file-upload"
                                            class="btn btn-primary custom-upload d-flex justify-content-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M16 16h-3v5h-2v-5h-3l4-4 4 4zm3.479-5.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                                            </svg>
                                            Upload
                                        </label>
                                        <input id="file-upload-shopify" type="file" name="photo-shopify"/>
                                        <p class="sm-text gray"> Indicamos uma imagem 500x500
                                            <br> JPG, JPEG ou PNG de até 2MB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 hidden-m d-flex justify-content-center">
                            <div class="project-card d-flex flex-column align-items-center hidden-m">
                                <div class="private-over justify-content-center text-center">
                                    <div class="private-card-content">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="35" height="35"
                                            viewBox="0 0 24 24">
                                            <path
                                                d="M17 9.761v-4.761c0-2.761-2.238-5-5-5-2.763 0-5 2.239-5 5v4.761c-1.827 1.466-3 3.714-3 6.239 0 4.418 3.582 8 8 8s8-3.582 8-8c0-2.525-1.173-4.773-3-6.239zm-8-4.761c0-1.654 1.346-3 3-3s3 1.346 3 3v3.587c-.927-.376-1.938-.587-3-.587s-2.073.211-3 .587v-3.587zm3 17c-3.309 0-6-2.691-6-6s2.691-6 6-6 6 2.691 6 6-2.691 6-6 6zm2-6c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2z"/>
                                        </svg>
                                        <h4 class="bold">Privado</h4>
                                    </div>
                                </div>
                                <div class="card-img">
                                    <img class="card-img-top" id='image-shopify' src="{{asset('modules/register/img/projeto.png')}}" style='height:240px; width:240px;'>
                                </div>
                                <div class="card-body mt30">
                                    <h5 id='name_preview_shopify' class="card-title">Nome do seu projeto</h5>
                                    <p id='description_preview_shopify' class="card-text sm-text gray"> Aqui irá a descrição do seu projeto. Ela poderá ser acessada por possiveis afiliados que visualizarem a vitrine da plataforma. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div id="success" class="div6" style="display: none">
                    <div class="content-success">
                        <img src="{{asset('modules/global/gif/cloudfox-loading-1.gif')}}">
                        <h1 class="bold orange mt10"> Tudo pronto! </h1>
                        <p class="mt10"> Só um momento. Estamos preparando a plataforma para você! Em alguns instantes, você estara na sua nova dashboard.
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<footer>
    <div class="container">
        <div class="wrap-footer">
            <div class="row justify-content-between align-items-center">
                {{-- <div class="col-3">
                    <div class="btn-voltar d-flex">
                        <a href="#" class="d-flex">
                            <div id="btnBack" style="display:none;">
                                <div class="btn-voltar">
                                    <span class="icon-back mr10">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24">
                                            <path d="M0 12c0 6.627 5.373 12 12 12s12-5.373 12-12-5.373-12-12-12-12 5.373-12 12zm7.58 0l5.988-5.995 1.414 1.416-4.574 4.579 4.574 4.59-1.414 1.416-5.988-6.006z"/>
                                        </svg>
                                    </span>
                                    <span class="bold hidden-m back" id="back">
                                        Voltar
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                </div> --}}
                <div class="col-12 d-flex align-items-center justify-content-end">
                    <div class="btn-voltar d-flex mr15">
                            <span>
                                <a href="#" id="jump" class="bold" style="display:none;">Pular</a>
                            </span>
                    </div>
                    <div class="btn-holder footer">
                        <button class="btn btn-primary" id='btn-go'>Prosseguir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<script src="{{asset('modules/register/js/jquery-ui.min.js')}}"></script>
<script src="{{asset('modules/register/js/bootstrap.min.js')}}"></script>
<script src="{{asset('modules/register/js/wow.min.js')}}"></script>
<script src="{{asset('modules/register/js/pesquisaCep.js')}}"></script>
<script src="{{asset('modules/register/js/register.js')}}"></script>
<script src="{{asset('modules/register/js/passwordStrength.js')}}"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.js'></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/babel-external-helpers/babel-external-helpers.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/popper-js/umd/popper.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/animsition/animsition.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollbar/jquery-asScrollbar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/asscrollable/jquery-asScrollable.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/jquery-mmenu/jquery.mmenu.min.all.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/vendor/matchheight/jquery.matchHeight-min.js') }}"></script>
<script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>
<script src="{{ asset('modules/global/js-extra/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Component.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Plugin.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Base.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/global/js/Config.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/Menubar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/Sidebar.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/PageAside.js') }}"></script>
<script src="{{ asset('modules/global/adminremark/assets/js/Section/GridMenu.js') }}"></script>
<script src="{{ asset('modules/global/jquery-imgareaselect/scripts/jquery.imgareaselect.pack.js') }}"></script>
<script src="{{ asset('modules/global/js/global.js') }}"></script>
</body>
</html>


