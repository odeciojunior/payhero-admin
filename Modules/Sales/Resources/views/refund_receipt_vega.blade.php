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
            background-color: white;
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
    <img src="build/global/img/vega-logo.png" alt="Vega Logo">
</header>

<h2>Comprovante de Estorno</h2>
<h4>{{\Illuminate\Support\Carbon::parse($sale->sale->start_date)->format('d/m/Y H:i:s')}}</h4>
<br>
<table>
    <tr>
        <th>Estabelecimento</th>
        <td>
            {{$company->fantasy_name}} - {{\Modules\Core\Services\FoxUtils::getDocument($company->document)}}
        </td>
        <th>Código</th>
        <td>{{$sale->sale->id_code}}</td>
    </tr>
    <tr>
        <th></th>
        <td>{{ mb_strtoupper("{$company->street}, {$company->number} - {$company->neighborhood}, {$company->city} - {$company->state}, {$company->zip_code}", 'UTF-8') }}</td>
        <th></th>
        <td></td>
    </tr>
    <tr>
        <th>&nbsp;
        </th>
        <td>&nbsp;
        </td>
        <th>NSU</th>
        <td>{{$sale->sale->gateway_transaction_id}}</td>
    </tr>
    <tr>
        <th>Bandeira</th>
        <td>{{$sale->flag}}</td>
        <th>Parcelas</th>
        <td>{{$sale->sale->installments_amount}}</td>
    </tr>
</table>
<br><br>
<div class="value">
    <b>Valor:</b>
    <span>{{\Modules\Core\Services\FoxUtils::formatMoney($sale->sale->original_total_paid_value/100)}}</span>
</div>

</body>
</html>
