<!doctype html>
<html lang="pt-BR">
<head>
    <title>Comprovante de Estorno</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            font-size: 10pt;
        }

        header {
            background-color: red;
            padding-top: 2px;
        }

        h2,
        h4 {
            text-align: center;
        }

        table {
            margin: 0 auto;
        }

        div.value {
            font-size: 14pt;
            text-align: center;
        }

    </style>
</head>
<body>

<small>{{date('d/m/Y H:i:s')}}</small>

<header>
    <img src="{{mix('build/global/img/getnet-logo.png')}}" alt="Getnet Logo">
</header>

<h2>Comprovante de Estorno</h2>
<h4>{{\Illuminate\Support\Carbon::parse($sale->summary->transaction_date)->format('d/m/Y H:i:s')}}</h4>
<br>
<table>
    <tr>
        <th>Estabelecimento</th>
        <td>
            {{$company->fantasy_name}} - {{\Modules\Core\Services\FoxUtils::getDocument($company->document)}}
        </td>
        <th>Código</th>
        <td>{{$company->subseller_getnet_id}}</td>
    </tr>
    <tr>
        <th></th>
        <td>{{mb_strtoupper("{$company->street}, {$company->number} - {$company->neighborhood}, {$company->city} - {$company->state}, {$company->zip_code}", 'UTF-8')}}</td>
        <th></th>
        <td></td>
    </tr>
    <tr>
        <th>Autorização</th>
        <td>{{$sale->summary->authorization_code}}</td>
        <th>NSU</th>
        <td>{{$sale->summary->acquirer_transaction_id}}</td>
    </tr>
    <tr>
        <th>Bandeira</th>
        <td>{{$sale->flag}}</td>
        <th>Parcelas</th>
        <td>{{$sale->summary->number_installments}}</td>
    </tr>
</table>
<br><br>
<div class="value">
    <b>Valor:</b>
    <span>{{\Modules\Core\Services\FoxUtils::formatMoney($sale->summary->card_payment_amount/100)}}</span>
</div>

</body>
</html>
