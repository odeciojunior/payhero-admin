<!DOCTYPE html>
<html>

<head>
    <title>teste</title>
    <style>
        body {
            font-family: sans-serif;
        }

        table td {
            padding: 10px 50px 10px 15px;
        }

        .recuo {
            text-indent: 2em
        }

        .titulo_tabela {
            border: solid 1px gray;
            padding: 15px;
            background: #e9e9e9;
        }

        .badge {
            color: white;
            padding: 7px 12px;
            border-radius: 6px;
            font-weight: 700;
            display: inline-block;
            font-size: 62%;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
        }

        .badge-pending {
            background: rgb(255, 157, 0);
        }

        .badge-success {
            background: #11c26d;
        }

        .badge-danger {
            background: #ff4c52;
        }

        .badge-primary {
            background: #3e8ef7;
        }

        .badge-antifraud {
            background: rgb(22, 142, 209);
        }

        .card {
            font-family: 'Muli', sans-serif;
            border-radius: 5px !important;
            box-shadow: 0 10px 20px 0 rgba(0, 0, 0, 0.1);
            padding: 5px 20px;
            margin-top: 10px;
        }

        .pad {
            padding: 10px 20px;
        }

        .table_title {
            font-size: 13px;
            font-weight: 600;
            font-style: normal;
            font-stretch: normal;
            line-height: 1.5;
            letter-spacing: normal;
            color: black;
        }

        .text_muted {
            color: #a3afb7 !important;
            font-size: 10px;
        }

        .w50p {
            display: inline-block;
            width: 180px;
            padding-bottom: 3px;
        }

        .tab_data {
            color: #a1a1a1;
            font-size: 12px;
            font-weight: 600;
            font-style: normal;
            font-stretch: normal;
            line-height: 1.5;
            letter-spacing: normal;
        }

        .tx_right {
            text-align: right;
        }

        .table-striped td {
            padding: 5px 9px;
        }

        .table-striped {
            width: 100%;
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #fff;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #F5F6F6;
        }

        .table-striped-tracking tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, .05);
        }

        .table-striped-tracking tbody tr:nth-of-type(even) {
            background-color: #fff;
        }

        .table-striped-tracking {
            font-size: 13px;
        }

        .table-striped-tracking td,
        .table-striped-tracking th {
            border-top: 1px solid #dee2e6;
        }

        .page-break {
            page-break-after: always;
        }
    </style>

</head>

<body>
    @php
        use Carbon\Carbon;
    @endphp

    <h2 class="text-center">Pedido de reversão de disputa</h2>
    <div class="col-12"
         style="background: #e9e9e9; padding:5px;">
        <h4><u>Informação de disputa</u></h4>
        <h4>Número da transação (interna): #{{ $dataSale->id }} </h4>
        <h4>Número NSU da Transação GETNET: {{ $dataSale->data_decoded['NSU'] }} </h4>
        <h4>Código da autorização GETNET: {{ $dataSale->nsu }}</h4>
        <h4>Número de Atividade: {{ $dataSale->data_decoded['Número de Atividade'] }}</h4>
        <h4>Número Chargeback: {{ $dataSale->data_decoded['Número de Referência'] }}</h4>
    </div>
    <div>
        <h4>Resumo do caso:</h4>
        <p class="recuo">
            Nossos clientes chegam em nossas lojas através de anúncios em
            diversas plataformas, como Facebook, Google, Instagram. Após visitar a
            página do produto, o cliente é direcionado ao nosso Checkout onde
            processamos o pagamento escolhido pelo cliente, baseado em suas
            informações informadas.
        </p>

        <p class="recuo">
            Nosso sistema repassa todas as informações ao nosso
            processador de pagamentos, que verifica o risco e processa a venda
            junto a adquirente (Getnet).
        </p>

        <p class="recuo">
            Assim que a processadora de pagamentos Getnet reconhece a
            autenticidade da transação, ela libera a venda para processamento. Nós
            despachamos o produto escolhido pelo cliente em seu endereço
            informado, informamos todos os passos através de e-mail, SMS e assim
            encerramos o ciclo de compra iniciada em nosso site.
        </p>

        <h4>Abaixo é uma lista de evidências para ajudar no caso:</h4>
        <ul style="font-weight: bolder;">
            <li>Carta Explicativa;</li>
            <li>Dados da Solicitação Defesa;</li>
            <li>Envio do produto;</li>
            <li>Informações extras (Tela do pedido/IP que foi usado para
                compra/Geolocalização do IP que foi usado para compra);
            </li>
            <li>Considerações Finais.</li>
        </ul>
    </div>

    <div class="page-break"></div>
    <!-- user info -->

    <div>
        <h2 class="text-center">Carta Explicativa</h2>
        <p>
            Prezados
        </p>

        <p>A compra contestada foi realizada no dia {{ $dataSale->transaction_date }}, onde foi adquirido
            {{ $dataSale->products_str }} para o comprador:</p>

        <p>
            1 - Nome do comprador ou da pessoa que utilizou o serviço e CPF: <br />
            Nome: {{ $dataSale->customer->name }} <br />
            CPF: {{ $dataSale->customer->document }} <br />
        </p>

        <p>
            2 - Endereço do comprador ou da entrega do produto: <br />
            Endereço: {{ $dataSale->delivery->street }} <br />
            Bairro: {{ $dataSale->delivery->neighborhood }} <br />
            CEP: {{ $dataSale->delivery->zip_code }} <br />
            Cidade: {{ $dataSale->delivery->city }} <br />
        </p>

        <p>
            3 - E-mail e telefone do comprador ou da pessoa que utilizou o serviço: <br />
            Telefone: {{ $dataSale->customer->telephone }} <br />
            Email: {{ $dataSale->customer->email }} <br />
        </p>

        <p>
            4 - Outras informações (Rastreio):
            Código
            rastreio:
            {{ isset($dataSale->tranckings[0]->tracking_code) ? $dataSale->tranckings[0]->tracking_code : '' }}
            <br />
        </p>

    </div>

    <div class="page-break"></div>

    <div>

        <h2 class="text-center">Envio Produto: </h2>

        @foreach ($dataSale->tranckings as $tracking)
            <p>Objeto: #{{ $tracking->tracking_code }}</p>

            <table class="table-striped-tracking"
                   cellspacing="0">
                <thead>
                    <tr style="background: #212529; color: #fff">
                        <th style="background-color: #343a40;">Data</th>
                        <th style="background-color: #343a40;">Evento</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tracking->checkpoints as $checkpoint)
                        <tr>
                            <td>{{ $checkpoint->created_at }}</td>
                            <td>{{ $checkpoint->event }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>

    <div class="page-break"></div>

    <div>

        <h2 class="text-center">Informações extra</h2>

        <!-- Modal detalhes da venda-->
        <div>
            <style>
                #full_detail_view {
                    display: none
                }
            </style>
            @include('sales::detail_sale_partials', $sale_details)
        </div>

    </div>

    <div class="page-break"></div>

    <div>

        <h2 class="text-center">Informações extra</h2>

        <h5>Geolocalização do IP que foi usado para compra:</h5>

        <h6>IP: {{ $dataSale->ip->ip }}</h6>
        <h6>Dispositivo: {{ $dataSale->operational_system }}</h6>
        <h6>Navegador: {{ $dataSale->browser }}</h6>

        <h5>Geolocalização do IP que foi usado para compra:</h5>

        <table>
            <tr>
                <td>Ip</td>
                <td>{{ $dataSale->ip->ip }}</td>
            </tr>
            <tr>
                <td>Latitude</td>
                <td>{{ $dataSale->ip->lat }}</td>
            </tr>
            <tr>
                <td>Longitude</td>
                <td>{{ $dataSale->ip->lon }}</td>
            </tr>
            <tr>
                <td>País</td>
                <td>{{ $dataSale->ip->country }}</td>
            </tr>
            <tr>
                <td>Cidade</td>
                <td>{{ $dataSale->ip->city }}</td>
            </tr>
            <tr>
                <td>Estado</td>
                <td>{{ $dataSale->ip->state_name }}</td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>
    <div>
        <h3>Considerações finais</h3>

        <p class="recuo">
            Após análise manual de todos os metadados disponíveis no sistema,
            fica evidente a relação do proprietário do cartão crédito, com a
            transação NSU{{ $dataSale->data_decoded['NSU'] }}, com a compra efetuada, afastando a hipótese de fraude.
        </p>
        <p class="recuo">
            Também não há evidências de desacordo comercial, uma vez que o
            produto está a caminho da residência do solicitante.
        </p>
    </div>
</body>

</html>
