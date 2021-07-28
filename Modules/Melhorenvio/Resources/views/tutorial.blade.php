<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Melhor Envio (Homologação)</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    {{--        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">--}}
    {{--        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>--}}
    {{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>--}}
    {{--        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>--}}


    <link rel="stylesheet" href="{{ asset('modules/melhorenvio/css/tutorial.css?v='.uniqid()) }}">
</head>

<body>
<main>
    <div class="sidebar">
        <img class="logo" src="https://cloudfox.net/sirius/assets/img/logos/sirius-powered-cloudfox.svg" alt="Sirius Logo">
        <ol>
            <li>
                <a href="#section-1">Início</a>
            </li>
            <li>
                <a href="#section-2">Cadastrando aplicativo</a>
            </li>
            <li>
                <a href="#section-3">Adicionando o token no Sirius</a>
            </li>
            <li>
                <a href="#section-4">Autorizando o Sirius</a>
            </li>
            <li>
                <a href="#section-5">Cadastrando o frete</a>
            </li>
            <li>
                <a href="#section-6">Checkout</a>
            </li>
        </ol>
    </div>
    <div class="container">
        <h1>Manual de Integração com Melhor Envio</h1>
        <ol>
            <li id="section-1">
                <h4>Início</h4>
                <div>
                    Após criar a sua conta no Melhor Envio acesse: <span class="breadcrumb">Painel de Controle > Gerenciar > Token</span>
                    e clique no botão <span class="btn blue">NOVO APLICATIVO</span>
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-1.jpg') }}" alt="Imagem do passo 1">
                    </div>
                </div>
            </li>
            <li id="section-2">
                <h4>Cadastrando aplicativo</h4>
                <div>
                    Preencha dos dados e clique no botão <span class="btn blue">CADASTRAR APLICATIVO</span>. Você pode
                    preencher os dados da maneira que desejar, mas é importante que URL de callback seja:
                    <input class="url-container" readonly value="{{route('melhorenvio.finish')}}"/>.
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-2a.jpg') }}" alt="Imagem 1 do passo 2">
                        <img src="{{ asset('modules/melhorenvio/img/step-2b.jpg') }}" alt="Imagem 2 do passo 2">
                    </div>
                </div>
            </li>
            <li id="section-3">
                <h4>Adicionando o token no Sirius</h4>
                <div>
                    Na usa conta no Sirius acesse: <span class="breadcrumb">Aplicativos > Melhor Envio</span> e Clique
                    no botão <img class="inline-img" src="{{ asset('modules/melhorenvio/img/step-3a.jpg') }}"
                                  alt="Botão adicionar integração"> para adicionar uma nova integração. Feito isso
                    preencha com os dados do aplicativo criado no passo anterior e clique em <b>Realizar integração</b>.
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-3b.jpg') }}" alt="Imagem 2 do passo 3">
                    </div>
                </div>
            </li>
            <li id="section-4">
                <h4>Autorizando o Sirius</h4>
                <div>
                    Você será redirecionado para a tela de autorização Melhor Envio. Nessa etapa você pode configurar
                    alguns detalhes da sua integração. É muito importante que preencha os campos Inscrição estadual
                    padrão e Agência de postagem (caso use Jadlog) para que a integração funcione corretamente. Clique
                    em <span class="btn blue">Autorizar</span> e você será redirecionado de volta ao Sirius.
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-4a.jpg') }}" alt="Imagem 1 do passo 4">
                        <img src="{{ asset('modules/melhorenvio/img/step-4b.jpg') }}" alt="Imagem 2 do passo 4">
                    </div>
                </div>
            </li>
            <li id="section-5">
                <h4>Cadastrando o frete</h4>
                <div>
                    Acesse: <span class="breadcrumb">Projetos > Seu Projeto</span>. Em seguida clique na aba <span
                        class="breadcrumb">Frete</span> e depois no botão <b>Adicionar Frete</b>. Preencha os dados e
                    clique em <span class="btn green">Salvar</span>.
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-5a.jpg') }}" alt="Imagem 1 do passo 5">
                        <img src="{{ asset('modules/melhorenvio/img/step-5b.jpg') }}" alt="Imagem 2 do passo 5">
                    </div>
                </div>
            </li>
            <li id="section-6">
                <h4>Checkout</h4>
                <div>Se você chegou até aqui, as opções de frete do Melhor Envio já ficaram disponíveis no checkout da
                    sua
                    loja. Quando uma compra for aprovada será criada automáticamente uma etiqueta no carrinho de compras
                    do
                    Melhor Envio.
                    <div class="img-container">
                        <img src="{{ asset('modules/melhorenvio/img/step-6a.jpg') }}" alt="Imagem 1 do passo 6">
                        <img src="{{ asset('modules/melhorenvio/img/step-6b.jpg') }}" alt="Imagem 2 do passo 6">
                    </div>
                </div>
            </li>
        </ol>
    </div>
</main>
</body>

</html>
