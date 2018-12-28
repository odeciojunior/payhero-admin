<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="CloudFox">
        <meta name="author" content="">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CloudFox @yield('title')</title> 
        <link rel="apple-touch-icon" href="{{ asset('adminremark/assets/images/apple-touch-icon.png') }}">
        <link rel="shortcut icon" href="{{ asset('adminremark/assets/images/favicon.ico') }}">

        <!-- Stylesheets -->
        <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/global/css/bootstrap-extend.min.css') }}">
        <link rel="stylesheet" href="{{ asset('adminremark/assets/css/site.min.css') }}">

        <!-- Scripts -->
        <script src="{{ asset('adminremark/global/vendor/jquery/jquery.js') }}"></script>
    </head>

    <body style="margin:0; padding: 0">
        <div class="text-center">
            <h2>Seja bem vindo ao Cloudfox</h2>
        </div>

        <div class="page">
            <div class="page-content container-fluid">
                <form method="post" action="/cadastro/novousuario">
                    <input type="hidden" name="id_convite" value="{!! $convite->id !!}">
                    @csrf
                    <div class="page-content container-fluid">
                        <div class="panel pt-30 p-30" data-plugin="matchHeight">
                            <div style="width:100%">
                                <h4>Dados para o login</h4>
                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="email">Email</label>
                                        <input name="email" value="{!! $convite->email_convidado !!}" type="text" class="form-control" id="email" placeholder="Email">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label for="password">Password</label>
                                        <input name="password" type="password" class="form-control" id="password" placeholder="Senha">
                                    </div>
                                </div>

                                <h4>Informações básicas</h4>
                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="nome">Nome</label>
                                        <input name="name" type="text" class="form-control" id="nome" placeholder="Nome">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label for="cpf">CPF</label>
                                        <input name="cpf" type="text" class="form-control" id="cpf" placeholder="CPF">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="data_nascimento">Data de nascimento</label>
                                        <input name="data_nascimento" type="date" class="form-control" id="data_nascimento">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label for="celular">Celular</label>
                                        <input name="celular" type="text" class="form-control" id="celular" placeholder="Celular">
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="form-group col-xl-6">
                                        <label for="telefone1">Telefone</label>
                                        <input name="telefone1" type="text" class="form-control" id="telefone1" placeholder="Telefone">
                                    </div>
                                </div>

                                <h4>Endereço</h4>
                                <div class="row">

                                    <div class="form-group col-xl-6">
                                        <label for="cep">CEP</label>
                                        <input name="cep" type="text" class="form-control" id="cep" placeholder="CEP">
                                    </div>
        
                                    <div class="form-group col-xl-6">
                                        <label for="pais">País</label>
                                        <input name="pais" type="text" class="form-control" id="pais" placeholder="País">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="estado">Estado</label>
                                        <input name="estado" type="text" class="form-control" id="estado" placeholder="Estado">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label for="cidade">Cidade</label>
                                        <input name="cidade" type="text" class="form-control" id="cidade" placeholder="Cidade">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="bairro">Bairro</label>
                                        <input name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
                                    </div>
                                    <div class="form-group col-xl-6">
                                        <label for="rua">Rua</label>
                                        <input name="logradouro" type="text" class="form-control" id="rua" placeholder="Rua">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="form-group col-xl-6">
                                        <label for="numero">Número</label>
                                        <input name="numero" type="text" class="form-control" id="numero" placeholder="Número">
                                    </div>
        
                                    <div class="form-group col-xl-6">
                                        <label for="complemento">Complemento</label>
                                        <input name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Salvar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="{{ asset('adminremark/global/vendor/bootstrap/bootstrap.js') }}"></script>

    </body>
</html>

