<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Integração com Melhor Envio</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ mix('modules/melhorenvio/css/tutorial.min.css') }}">
</head>

<body>
<main>
    <div class="sidebar">
        <img class="logo" src="https://cloudfox.net/sirius/assets/img/logos/sirius-powered-cloudfox.svg"
             alt="Sirius Logo">
        <ol>
            <li>
                <a href="#section-1">Integração</a>
            </li>
            <li>
                <a href="#section-2">Autorizando o Sirius</a>
            </li>
            <li>
                <a href="#section-3">Cadastrando o frete</a>
            </li>
            <li>
                <a href="#section-4">Checkout</a>
            </li>
        </ol>
    </div>
    <div class="container">
        <h1>Manual de Integração com Melhor Envio</h1>
        <ol>
            <li id="section-1">
                <h4>Integração</h4>
                <div>
                    Na sua conta no Sirius acesse: <span class="breadcrumb">Aplicativos > Melhor Envio</span> e Clique
                    no botão <img class="inline-img" src="{{ mix('modules/melhorenvio/img/step-1a.jpg') }}"
                                  alt="Botão adicionar integração"> para adicionar uma nova integração. Feito isso
                    dê um nome para a sua integração e clique em <span class="btn green">Realizar integração</span>.
                    <div class="img-container">
                        <img src="{{ mix('modules/melhorenvio/img/step-1b.jpg') }}" alt="Imagem 2 do passo 1">
                    </div>
                </div>
            </li>
            <li id="section-2">
                <h4>Autorizando o Sirius</h4>
                <div>
                    Você será redirecionado para a tela de autorização Melhor Envio (certifique-se que está logado em
                    sua conta). Nessa etapa você pode configurar alguns detalhes da sua integração.
                    <p> É muito importante que preencha os campos <b>Inscrição Estadual Padrão</b> e <b>Agência de
                            postagem (caso use Jadlog)</b> para que a integração funcione corretamente. Clique em <span
                            class="btn blue">Autorizar</span> e você será redirecionado de volta ao Sirius.</p>
                    <div class="img-container">
                        <img src="{{ mix('modules/melhorenvio/img/step-2a.jpg') }}"
                             alt="Imagem 1 do passo 2">
                        <img src="{{ mix('modules/melhorenvio/img/step-2b.jpg') }}"
                             alt="Imagem 2 do passo 2">
                    </div>
                </div>
            </li>
            <li id="section-3">
                <h4>Cadastrando o frete</h4>
                <div>
                    Acesse: <span class="breadcrumb">Loja > Sua Loja </span>. Em seguida clique na aba <span
                        class="breadcrumb">Frete</span> e depois no botão <b>Adicionar Frete</b>. Preencha os dados e
                    clique em <span class="btn green">Salvar</span>.
                    <div class="img-container">
                        <img src="{{ mix('modules/melhorenvio/img/step-3a.jpg') }}" alt="Imagem 1 do passo 3">
                        <img src="{{ mix('modules/melhorenvio/img/step-3b.jpg') }}" alt="Imagem 2 do passo 3">
                    </div>
                </div>
            </li>
            <li id="section-4">
                <h4>Checkout</h4>
                <div>Se você chegou até aqui, as opções de frete do Melhor Envio já ficaram disponíveis no checkout da
                    sua
                    loja. Quando uma compra for aprovada será criada automáticamente uma etiqueta no carrinho de compras
                    do
                    Melhor Envio.
                    <div class="img-container">
                        <img src="{{ mix('modules/melhorenvio/img/step-4a.jpg') }}" alt="Imagem 1 do passo 4">
                        <img src="{{ mix('modules/melhorenvio/img/step-4b.jpg') }}" alt="Imagem 2 do passo 4">
                    </div>
                </div>
            </li>
        </ol>
    </div>
</main>
</body>

</html>
