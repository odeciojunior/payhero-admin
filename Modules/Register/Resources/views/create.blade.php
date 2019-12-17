<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @if(getenv('APP_ENV') === 'production')
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @endif
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css"/>
    <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
</head>
<body id='register-body' style='padding-top:0px;background-color:white;'>
{{--
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
<section class="">
    <div class="container my-60">
        <form id="form-register" class="mt10">
            <div id="login" class="div1">
                <h4 class="bold title-content">
                    Dados básicos
                </h4>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="input-holder d-flex flex-column">
                            <label for="firstname">Nome</label>
                            <input type="text" name="firstname" id="firstname" placeholder="Digite seu nome" required>
                            <p class='sm-tex text-danger' id='nameError' style='display:none;'>O campo nome é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="input-holder d-flex flex-column">
                            <label for="lastname">Sobrenome</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Digite seu sobrenome" required>
                            <p class='sm-tex text-danger' id='lastNameError' style='display:none;'>O campo sobrenome é obrigatório</p>
                        </div>
                    </div>
                </div>
                <div class='row mt-20'>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="document">CPF</label>
                            <input type="text" name="document" id="document" placeholder="Digite seu CPF" required>
                            <p class='sm-tex text-danger' id='documentError' style='display:none;'>O campo CPF é obrigatório</p>
                            <p class='sm-tex text-danger' id='documentExistError' style='display:none;'>Esse CPF já está cadastrado na plataforma</p>
                            <p class='sm-tex text-danger' id='documentInvalidError' style='display:none;'>CPF inválido</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="phone">Celular</label>
                            <input type="text" name="cellphone" id="phone" placeholder="Digite seu celular" required>
                            <p class='sm-tex text-danger' id='phoneError' style='display:none;'>O campo celular é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="document">Data de nascimento</label>
                            <input type="date" name="date_birth" id="date_birth" placeholder="Digite sua data de nascimento" required>
                            <p class='sm-tex text-danger' id='dateBirthError' style='display:none;'>O campo Data de nascimento é obrigatório</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class='div2' style='display:none;'>
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="senha">
                            <h4 class="bold title-content">
                                Crie uma senha
                            </h4>
                            <p class="desc"> Ok. Para garantir seu acesso, você precisa de um e-mail e uma senha. </p>
                        </div>
                        <div class='row'>
                            <div class="col-lg-12">
                                <div class="input-holder d-flex flex-column">
                                    <label for="email">E-mail</label>
                                    <input type="email" name="email" id="email" value="" placeholder="Digite seu email" required>
                                    <p class='sm-tex text-danger' id='emailError' style='display:none;'>O campo E-mail é obrigatório</p>
                                    <p class='sm-tex text-danger' id='emailExistError' style='display:none;'>Esse email já está cadastrado na plataforma</p>
                                </div>
                            </div>
                        </div>
                        <div class='row mt-20'>
                            <div class='col-lg-12'>
                                <div class="input-holder">
                                    <label for="password">Senha</label>
                                    <input type="password" name="password" id="password" value="" placeholder="Password" required>
                                    <p class='sm-tex text-danger' id='passwordError' style='display:none;'>O campo senha é obrigatório</p>
                                    <p class="sm-text mt-2">Evite senhas usadas em outros sites e que sejam fáceis de identificar.</p>
                                    <div class="progress align-items-center justify-content-between">
                                        </span>
                                        <div class="progress-bar" id='progress-password' role="progressbar" aria-valuenow="25"
                                             aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
                                        <span class="bold ml15 text-progress" id="text-password"> </span>
                                    </div>
                                    <div class='pass-container'></div>
                                    <div class='pass-hint'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 justify-content-center mt-50">
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
            <div class='div3' style='display:none;'>
                <h4 class="bold title-content">
                    Dados residenciais
                </h4>
                <p class="desc"> Seu endereço.
                </p>
                <div class="row mb-20">
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="zip_code">CEP</label>
                            <input type="text" name="zip_code" id="zip_code" placeholder="Digite seu CEP" required>
                            <p class='sm-tex text-danger' id='zipCodeError' style='display:none;'>O campo CEP é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="street">Endereço</label>
                            <input type="text" name="street" id="street" placeholder="Digite o seu endereço" required>
                            <p class='sm-tex text-danger' id='streetError' style='display:none;'>O campo Rua é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="number">Número</label>
                            <input type="text" name="number" id="number" value="" placeholder="Digite seu número residencial" required>
                            <p class='sm-tex text-danger' id='numberError' style='display:none;'>O campo Número é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="document">Complemento</label>
                            <input type="text" name="complement" id="complement" placeholder="Digite o complemento">
                            <p class='sm-tex text-danger' id='complementError' style='display:none;'>O campo Complemento é obrigatório</p>
                        </div>
                    </div>
                </div>
                <div class='row mt-20'>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="neighborhood">Bairro</label>
                            <input type="text" name="neighborhood" id="neighborhood" placeholder="Digite o nome do seu bairro" required>
                            <p class='sm-tex text-danger' id='neighborhoodError' style='display:none;'>O campo Bairro é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="city">Cidade</label>
                            <input type="text" name="city" id="city" placeholder="Digite o nome da sua cidade" required>
                            <p class='sm-tex text-danger' id='cityError' style='display:none;'>O campo Cidade é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="state">Estado</label>
                            <input type="text" name="state" id="state" placeholder="Digite seu estado" required>
                            <p class='sm-tex text-danger' id='stateError' style='display:none;'>O campo Estado é obrigatório</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="empresa" class="div4" style="display:none;padding-botton:10%;">
                <div id='text-main' class='row justify-content-center text-center'>
                    <h1 class="bold">
                        Você gostaria de utilizar a CloudFox para receber pagamentos para o seu negócio como...
                    </h1>
                </div>
                <div id='text-company' class='row justify-content-center text-center' style='display:none;'>
                    <h1 class="bold">
                        Precisamos saber um pouco mais da sua empresa...
                    </h1>
                </div>
                <div class='row justify-content-center text-center mt-40'>
                    <div class='col-lg-6'>
                        <button id='btn-physical-person' class='btn btn-info' data-type='physical person'>Pessoa fisíca</button>
                    </div>
                    <div class='col-lg-6'>
                        <button id='btn-juridical-person' class='btn btn-info' data-type='juridical person'>Pessoa jurídica</button>
                    </div>
                </div>
                <div id='div-juridical-person' class='mt-40' style='display:none;'>
                    <div class='row'>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="company_document">CNPJ</label>
                                <input type="text" name="company_document" id="company_document" placeholder="Digite seu CNPJ" required>
                                <p class='sm-tex text-danger' id='companyDocumentError' style='display:none;'>O campo CNPJ é obrigatório</p>
                                <p class='sm-tex text-danger' id='companydocumentExistError' style='display:none;'>Esse CNPJ já está cadastrado na plataforma</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="fantasy_name">Razão social</label>
                                <input type="text" name="company_document" id="fantasy_name" placeholder="Digite a razão social" required>
                                <p class='sm-tex text-danger' id='fantasyNameError' style='display:none;'>O campo Razão social é obrigatório</p>
                            </div>
                        </div>
                    </div>
                    <div class='row mt-20'>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="support_email">E-mail</label>
                                <input type="text" name="support_email" id="support_email" placeholder="Digite o e-mail" required>
                                <p class='sm-tex text-danger' id='supportEmailError' style='display:none;'>O campo E-mail é obrigatório</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="input-holder d-flex flex-column">
                                <label for="support_telephone">Telefone</label>
                                <input type="text" name="support_telephone" id="support_telephone" placeholder="Digite o telefone" required>
                                <p class='sm-tex text-danger' id='supportTelephoneError' style='display:none;'>O campo Telefone é obrigatório</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class='div5' style="display:none;">
                <h4 class="bold title-content">
                    Dados bancários
                </h4>
                <p class="desc"> Seus dados bancários.
                </p>
                <div class='row'>
                    <div class='col-lg-4'>
                        <div class="input-holder d-flex flex-column">
                            <label for="bank">Banco</label>
                            <select id="bank" class="form-control" name="bank" style='width:100%' data-plugin="select2">
                                <option value="">Selecione</option>
                            </select>
                            <p class='sm-tex text-danger' id='bankError' style='display:none;'>O campo Banco é obrigatório</p>
                        </div>
                    </div>
                </div>
                <div class='row mt-20'>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="agency">Agência</label>
                            <input type="text" name="agency" id="agency" placeholder="Digite a agência" required>
                            <p class='sm-tex text-danger' id='agencyError' style='display:none;'>O campo Agência é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="agency_digit">Dígito</label>
                            <input type="text" name="agency_digit" id="agency_digit" placeholder="Digite o dígito da agência" required>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="account">Conta</label>
                            <input type="text" name="account" id="account" placeholder="Digite a conta" required>
                            <p class='sm-tex text-danger' id='accountError' style='display:none;'>O campo Conta é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="account_digit">Dígito</label>
                            <input type="text" name="account_digit" id="account_digit" placeholder="Digite o dígito da conta" required>
                        </div>
                    </div>
                </div>
            </div>
            <div id="success" class="div6" style="display: none">
                <div class="content-success">
                    <img src="https://cloudfox.nyc3.cdn.digitaloceanspaces.com/cloudfox/defaults/cloudfox-loading-register.gif">
                    <h1 class="bold orange mt10"> Tudo pronto! </h1>
                    <p class="mt10"> Só um momento. Estamos preparando a plataforma para você! Em alguns instantes, você estará na sua nova dashboard.
                </div>
            </div>
            <div class='div7' style="display:none;">
                <h4 class="bold title-content">
                    Endereço comercial da empresa
                </h4>
                <p class="desc"> Onde sua empresa está localizada.
                </p>
                <div class='row'>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="zip_code_company">CEP</label>
                            <input type="text" name="zip_code_company" id="zip_code_company" placeholder="Digite o CEP" required>
                            <p class='sm-tex text-danger' id='zipCodeCompanyError' style='display:none;'>O campo CEP é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="street_company">Endereço</label>
                            <input type="text" name="street_company" id="street_company" placeholder="Digite o endereço" required>
                            <p class='sm-tex text-danger' id='streetCompanyError' style='display:none;'>O campo Endereço é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="number_company">Número</label>
                            <input type="text" name="number_company" id="number_company" placeholder="Digite o número" required>
                            <p class='sm-tex text-danger' id='numberCompanyError' style='display:none;'>O campo Número é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-holder d-flex flex-column">
                            <label for="complement_company">Complemento</label>
                            <input type="text" name="complement_company" id="complement_company" placeholder="Digite o complemento" required>
                        </div>
                    </div>
                </div>
                <div class='row mt-20'>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="neighborhood_company">Bairro</label>
                            <input type="text" name="neighborhood_company" id="neighborhood_company" placeholder="Digite o bairro" required>
                            <p class='sm-tex text-danger' id='neighborhoodCompanyError' style='display:none;'>O campo Bairro é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="city_company">Cidade</label>
                            <input type="text" name="city_company" id="city_company" placeholder="Digite o nome da cidade" required>
                            <p class='sm-tex text-danger' id='cityCompanyError' style='display:none;'>O campo Cidade é obrigatório</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-holder d-flex flex-column">
                            <label for="state_company">Estado</label>
                            <input type="text" name="state_company" id="state_company" placeholder="Digite o estado" required>
                            <p class='sm-tex text-danger' id='stateCompanyError' style='display:none;'>O campo Estado é obrigatório</p>
                        </div>
                    </div>
                </div>
            </div>
            <div id='alert-row' class='row mt-100' style='display:none;'>
                <div class='col-lg-12 mt-80'>
                    <p class='info pt-5' style='font-size: 15px;'>
                        <i class='icon wb-info-circle' aria-hidden='true'></i> Essas informações poderão ser alteradas depois.
                    </p>
                </div>
            </div>
        </form>
    </div>
</section>
<footer>
    <div class="container">
        <div class="p-10">
            <div class="row justify-content-between align-items-center">
                <div class="col-12 d-flex align-items-center justify-content-end">
                    <div class="btn-holder footer">
                        <button class="btn btn-primary" id='btn-go'>Prosseguir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
--}}
<div class="page-holder">
    <div class="content-error d-flex text-center">
        <img class="svgorange" src="{!! asset('modules/global/img/error.png') !!}">
        <h1 class="big"> Aviso! </h1>
        <h3>O limite de usuários da versão beta foi atingido, aguarde a versão oficial para juntar-se a nós!</h3>
    </div>
</div>
<!-- Scripts -->
{{--<script src="{{asset('modules/register/js/jquery-ui.min.js')}}"></script>
<script src="{{asset('modules/register/js/jquery-ui.min.js')}}"></script>
<script src="{{asset('modules/register/js/bootstrap.min.js')}}"></script>
<script src="{{asset('modules/register/js/wow.min.js')}}"></script>
<script src="{{asset('modules/register/js/pesquisaCep.js')}}"></script>
<script src="{{asset('modules/register/js/register.js?v=9')}}"></script>--}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
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
<style>
    .page-holder {
        font-family: 'Muli', sans-serif;
        color: black;
        width: 100%;
        min-height: 100vh;
        margin: 0;
        overflow: hidden;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
    }
    .logo {
        margin-bottom: 20px;
        width: 60px;
    }
    .content-error {
        color: black;
        display: flex;
        align-content: center;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
    {{--  padding: 20px;  --}}
}
    .big {
        font-size: 45px;
        color: black;
        font-weight: 700;
    }
    .btn.orange {
        background: transparent;
        background-color: transparent;
        border: 2px solid orangered;
        cursor: pointer;
        color: orangered !important;
        font-weight: 600;
        border-radius: 5px;
        align-self: center;
        padding: 10px 22px;
        transition: all 300ms linear;
    }
    .btn.orange:hover {
        background-color: orangered;
        border-color: orangered;
        color: white !important;
    }
</style>
</html>


