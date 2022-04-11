<!doctype html>
<html lang="pt-BR">
<head>
    <title>Comprovante de Estorno</title>
    <style>
        body {margin: 0; font-family: sans-serif; font-size: 14pt;}
        table {margin: 0 auto;}
        td{padding: 20px; text-align: left}
        div{margin: 10px 0}

        .title{font-size: 16px; font-weight: 700; letter-spacing: 0em; text-align: left; color: #002F66;}

        .box{border-radius: 8px; padding: 24px; border: 1px solid #EBEBEB}
        .box td{padding: 0; margin: 0;}
        .box td:nth-child(2){padding-left: 10px;}
        .box .head{font-size: 14px; font-weight: 400; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}
        .box .content{font-size: 12px; font-weight: 700; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}

        .head{font-size: 15px;font-weight: 400; letter-spacing: 0em; text-align: left; color: #0050AF;}
        .content{font-size: 13px; font-weight: 400; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}
        .content .bold{font-weight: 700}

        .bg-azul{background-color: #F4F6FB}
        .bg-azul div{margin: 20px 0}
        .icon{padding:0; margin:0; width: 32px}

    </style>
</head>
<body>
<table>
    <tr>
        <td style="width: 50%">
            <div class="title">Confirmação de estorno</div>
        </td>
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
                            <div class="content">{{\Modules\Core\Services\FoxUtils::formatMoney($sale->sale->original_total_paid_value/100)}}</div>
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
                            <div class="content">{{\Illuminate\Support\Carbon::parse($sale->sale->date_refunded)->format('d/m/Y H:i')}}</div>
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
            <div class="content">
                <span class="bold">{{$company->fantasy_name}}</span><br>
                <span class="bold">CNPJ:</span> {{\Modules\Core\Services\FoxUtils::getDocument($company->document)}}
            </div>

            <div class="head">
                Sua compra
            </div>
            <div class="content">
                Código: #{{hashids_encode($sale->sale_id, 'sale_id')}}<br>&nbsp;<br>
                <span class="bold">
                    {{count($plans_sales)}} Produto(s):<br>
                </span>
                @foreach ($plans_sales as $plan)
                    - {{\Illuminate\Support\Str::limit($plan->name, 20)}}
                @endforeach

            </div>

            <div class="head">
                Cliente
            </div>
            <div class="content">
                {{$sale_info->customer_name}}
            </div>

            <div class="head">
                Prazo de recebimento
            </div>
            <div class="content">
                Até 30 dias após o recebimento desta confirmação (normalmente é efetuado dentro de 1 dia útil).
            </div>

            <div class="head">
                Recebimento
            </div>
            <div class="content">
                <span class="bold">
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
                </span>
                <br />
                Final {{$sale_info->last_four_digits}}
            </div>

            </td>
        </td>
        <td>
            <div class="box bg-azul" style="height: 400px;">
                <div style="margin-top: 80px; margin-bottom:20px"><img src="build/global/img/estorno-shape.svg" alt="icon estorno"></div>
                <div class="head" style="margin-bottom:20px">Sua compra foi estornada, <span style="color:#000">{{ $sale_info->firstname }}</span></div>
                <div class="head" style="margin-bottom:20px">Esperamos que seu problema tenha sido solucionado e pedimos desculpas por qualquer transtorno.</div>
                <div class="head">Lembrando que você sempre pode voltar a conversar conosco através do <a href="https://sac.cloudfox.net/login">https://sac.cloudfox.net</a></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right">
            <br>
            <div style="font-size: 14px">Com a tecnologia <img src="build/global/img/vega-logo.png" alt="Vega Logo" style="vertical-align: middle;"></div>
        </td>
    </tr>
</table>
</body>
</html>
