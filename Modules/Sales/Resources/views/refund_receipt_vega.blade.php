<!doctype html>
<html lang="pt-BR">
<head>
    <title>Comprovante de Estorno</title>
    <style>
        body {margin: 0; font-family: sans-serif; font-size: 14pt;}
        table {margin: 0 auto;}
        td{padding: 20px; text-align: left}
        div{margin: 10px 0}
        .title{font-weight: bold; font-size: 18pt; color: #002F66;}
        .box td{padding: 0; margin: 0;}
        .box td:nth-child(2){padding-left: 10px;}
        .head{font-size: 16pt;font-weight: 400; letter-spacing: 0em;text-align: left;color: #0050AF;}
        .bg-azul{background-color: #F4F6FB}
        .bg-azul div{margin: 20px 0}
        .icon{padding:0; margin:0}
        .box{border-radius: 8px; padding: 24px; border: 1px solid #EBEBEB}
    </style>
</head>
<body>
<table>
    <tr>
        <td style="width: 50%" class="title">Confirmação de estorno</td>
        <td style="width: 50%; text-align: right">
            @if(!empty($checkout_configs->checkout_logo))
            <img src="{{$checkout_configs->checkout_logo}}" style="max-width: 160px">
            @endif
        </td>
    </tr>
    <tr>
        <td>
            <div class="box">
                <table>
                    <tr>
                        <td>
                            <img src="build/global/img/estorno-money.svg" class="icon" alt="icon money">
                        </td>
                        <td>
                            <div class="head">Valor estornado</div>
                            {{\Modules\Core\Services\FoxUtils::formatMoney($sale->sale->original_total_paid_value/100)}}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td>
            <div class="box">
                <table>
                    <tr>
                        <td>
                            <img src="build/global/img/estorno-calendar.svg" class="icon" alt="icon calendar">
                        </td>
                        <td>
                            <div class="head">Realizado em</div>
                            {{\Illuminate\Support\Carbon::parse($sale->sale->date_refunded)->format('d/m/Y H:i')}}
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="head">
                Informações do vendedor
            </div>
            <div>
                {{$company->fantasy_name}}<br>
                CNPJ: {{\Modules\Core\Services\FoxUtils::getDocument($company->document)}}
            </div>

            <div class="head">
                Sua compra
            </div>
            <div>
                Código: #{{hashids_encode($sale->sale_id, 'sale_id')}}<br>
                Produto(s):<br>
                @foreach ($plans_sales as $plan)
                    - {{\Illuminate\Support\Str::limit($plan->name, 20)}}
                @endforeach

            </div>

            <div class="head">
                Cliente
            </div>
            <div>
                {{$sale_info->customer_name}}
            </div>

            <div class="head">
                Prazo de recebimento
            </div>
            <div>
                Até 30 dias após o recebimento desta confirmação (normalmente é efetuado dentro de 1 dia útil).
            </div>

            <div class="head">
                Recebimento
            </div>
            <div>
                @if ($sale->sale->payment_method == 1)
                Cartão de crédito
                @elseif ($sale->sale->payment_method == 2)
                Boleto
                @elseif ($sale->sale->payment_method == 3)
                PIX
                @endif

                @if ($sale->flag)
                    {{$sale->flag}}
                @endif
                <br />
                Final {{$sale_info->last_four_digits}}
            </div>

            </td>
        </td>
        <td class="bg-azul">
            <div><img src="build/global/img/estorno-shape.svg" alt="icon calendar"></div>
            <div class="head">Sua compra foi estornada, <span style="color:#000">{{ $sale_info->firstname }}</span></div>
            <div>Esperamos que seu problema tenha sido solucionado e pedimos desculpas por qualquer transtorno.</div>
            <div>Lembrando que você sempre pode voltar a conversar conosco através do <a href="https://sac.cloudfox.net/login">https://sac.cloudfox.net</a></div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right">
            <br>
            Com a tecnologia <img src="build/global/img/vega-logo.png" alt="Vega Logo" style="vertical-align: middle;">
        </td>
    </tr>
</table>
</body>
</html>
