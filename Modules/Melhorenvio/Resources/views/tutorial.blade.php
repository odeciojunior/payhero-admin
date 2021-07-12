<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorial Melhor Envio (Homologação)</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('modules/melhorenvio/css/tutorial.css?v='.uniqid()) }}">
</head>

<body>
<div class="container">
    <h3>Tutorial de Integração com Melhor Envio</h3>
    <ol>
        <li> Após criar a sua conta no Melhor Envio acesse: <em>Painel de Controle > Gerenciar > Token</em> e clique
            no botão <b>NOVO APLICATIVO</b>
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-1.jpg') }}" alt="Imagem do passo 1">
            </div>
        </li>
        <li> Preencha dos dados e clique no botão <b>CADASTRAR APLICATIVO</b>. Você pode preencher os dados da
            maneira que desejar, mas é importante que URL de callback seja <span class="url-container">{{route('melhorenvio.finish')}}</span>.
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-2a.jpg') }}" alt="Imagem 1 do passo 2">
                <img src="{{ asset('modules/melhorenvio/img/step-2b.jpg') }}" alt="Imagem 2 do passo 2">
            </div>
        </li>
        <li> Na usa conta no Sirius acesse: <em>Aplicativos > Melhor Envio</em> e Clique no botão <img
                class="inline-img" src="{{ asset('modules/melhorenvio/img/step-3a.jpg') }}" alt="Botão adicionar integração"> para adicionar uma nova
            integração. Feito isso preencha com os dados do aplicativo criado no passo anterior e clique em
            <b>Realizar integração</b>.
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-3b.jpg') }}" alt="Imagem 2 do passo 3">
            </div>
        </li>
        <li> Você será redirecionado para a tela de autorização Melhor Envio. Nessa etapa você pode configurar
            alguns detalhes da sua integração. É muito importante que preencha os campos Inscrição estadual padrão e
            Agência de postagem (caso use Jadlog) para que a integração funcione corretamente. Clique em
            <b>Autorizar</b> e você será redirecionado de volta ao Sirius.
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-4a.jpg') }}" alt="Imagem 1 do passo 4">
                <img src="{{ asset('modules/melhorenvio/img/step-4b.jpg') }}" alt="Imagem 2 do passo 4">
            </div>
        </li>
        <li> Acesse: <em>Projetos > Seu Projeto</em>. Em seguida clique na aba <b>Frete</b> e depois no botão
            <b>Adicionar Frete</b>. Preencha os dados e clique em <b>Salvar</b>.
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-5a.jpg') }}" alt="Imagem 1 do passo 5">
                <img src="{{ asset('modules/melhorenvio/img/step-5b.jpg') }}" alt="Imagem 2 do passo 5">
            </div>
        </li>
        <li> Se você chegou até aqui, as opções de frete do Melhor Envio já ficaram disponíveis no checkout da sua
            loja. Quando uma compra for aprovada será criada automáticamente uma etiqueta no carrinho de compras do
            Melhor Envio.
            <div class="img-container">
                <img src="{{ asset('modules/melhorenvio/img/step-6a.jpg') }}" alt="Imagem 1 do passo 6">
                <img src="{{ asset('modules/melhorenvio/img/step-6b.jpg') }}" alt="Imagem 2 do passo 6">
            </div>
        </li>
    </ol>
</div>
</body>

</html>
