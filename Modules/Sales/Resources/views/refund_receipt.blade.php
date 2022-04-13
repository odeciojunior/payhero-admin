<!doctype html>
<html lang="pt-BR">
<head>
    <title>Comprovante de Estorno</title>
    <style>
        body {margin: 0; font-family: sans-serif; font-size: 14pt;}
        table {margin: 0 auto;}
        td{padding: 0 20px; text-align: left}
        div{margin: 10px 0}

        .title{font-size: 16px; font-weight: 700; letter-spacing: 0em; text-align: left; color: #002F66;}

        .box{border-radius: 8px; padding: 10px; border: 1px solid #EBEBEB}
        .box table{width:100%}
        .box-icon{width:32px}
        .box td{padding: 0; margin: 0;}
        .box td:nth-child(2){padding-left: 10px;}
        .box .tit{font-size: 12px; font-weight: 400; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}
        .box .val{font-size: 14px; font-weight: 700; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}

        .head{font-size: 15px;font-weight: 400; letter-spacing: 0em; text-align: left; color: #0050AF;}
        .head-top{margin-top:26px}
        .content{font-size: 13px; font-weight: 400; letter-spacing: 0em; text-align: left; color: #3D4456; margin:0; padding: 0}
        .content .bold{font-weight: 700}

        .bg-azul{background-color: #F4F6FB; height: 400px; border-radius: 8px; padding: 32px;}
        .bg-azul div{margin: 20px 0}
        .bg-azul a{color:#2E85EC}
        #linha1{margin-top: 80px; margin-bottom:20px}
        #linha2{margin-bottom:20px; color: #0050AF; font-weight: 700}
        #linha2 span{color:#000}
        #linha3 { color:#0050AF; margin-bottom:20px;}
        #linha4 { color:#0050AF}
        .icon{padding:0; margin:0; width: 32px}
        .gateway-logo{ vertical-align: text-bottom; max-height: 20px }
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
                        <td class="box-icon">
                            <img src="build/global/img/estorno-money.svg" class="icon" alt="icon money">
                        </td>
                        <td>
                            <div class="tit">Valor estornado</div>
                            <div class="val">{{\Modules\Core\Services\FoxUtils::formatMoney($sale->sale->original_total_paid_value/100)}}</div>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
        <td>
            <div class="box">
                <table>
                    <tr>
                        <td class="box-icon">
                            <img src="build/global/img/estorno-calendar.svg" class="icon" alt="icon calendar">
                        </td>
                        <td>
                            <div class="tit">Realizado em</div>
                            <div class="val">{{\Illuminate\Support\Carbon::parse($sale->sale->date_refunded)->format('d/m/Y')}} às {{\Illuminate\Support\Carbon::parse($sale->sale->date_refunded)->format('H:i')}}</div>
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

            <div class="head head-top">
                Sua compra
            </div>
            <div class="content">
                Código: #{{hashids_encode($sale->sale_id, 'sale_id')}}<br>
                <span class="bold">
                    Produto(s):
                </span>
                @foreach ($products_plans_sales as $item)
                    <br>{{\Illuminate\Support\Str::limit($item->name, 40)}} ({{$item->amount}}x)
                @endforeach
            </div>

            <div class="head head-top">
                Cliente
            </div>
            <div class="content">
                {{$sale_info->customer_name}}
            </div>

            <div class="head head-top">
                Prazo de recebimento
            </div>
            <div class="content">
                Até 30 dias após o recebimento desta confirmação (normalmente é efetuado dentro de 1 dia útil).
            </div>

            <div class="head head-top">
                Recebimento
            </div>
            <div class="content">
                <span class="bold">
                    @if ($sale->sale->payment_method == 1)
                    Cartão de crédito
                    @elseif ($sale->sale->payment_method == 2)
                    Boleto
                    @elseif ($sale->sale->payment_method == 4)
                    PIX
                    @endif

                    @if ($sale->sale->payment_method == 1 && $sale->flag)
                        {{$sale->flag}}
                    @endif
                </span>
                <br />
                @if ($sale->sale->payment_method == 1 && $sale_info->last_four_digits)
                    Final {{$sale_info->last_four_digits}}
                @endif
            </div>

            </td>
        </td>
        <td style="vertical-align: top">
            <div class="bg-azul">
                <div id="linha1"><img src="build/global/img/estorno-shape.svg" alt="icon estorno"></div>
                <div id="linha2" class="head">Sua compra foi estornada, <span>{{ $sale_info->firstname }}</span></div>
                <div id="linha3" class="head">Esperamos que seu problema tenha sido solucionado e pedimos desculpas por qualquer transtorno.</div>
                <div id="linha4" class="head">Lembrando que você sempre pode voltar a conversar conosco através do <a href="https://sac.cloudfox.net/login">https://sac.cloudfox.net</a></div>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: right">
            <br>
            <div style="font-size: 14px;">Com a tecnologia &nbsp; &nbsp;
                @if ($sale->gateway_id == 8 || $sale->gateway_id == 20)
                    <img src="build/global/img/adquirintes/asaas.svg" alt="Asaas Logo" class="gateway-logo">
                @elseif($sale->gateway_id == 18 || $sale->gateway_id == 19)
                    <img src="build/global/img/adquirintes/gerencianet.svg" alt="Gerencianet Logo" class="gateway-logo">
                @elseif($sale->gateway_id == 15 || $sale->gateway_id == 14)
                    <img src="build/global/img/getnet-logo.png" alt="Getnet Logo" class="gateway-logo">
                @elseif($sale->gateway_id == 5 || $sale->gateway_id == 6)
                    <img src="build/global/img/adquirintes/cielo.svg" alt="Cielo Logo" class="gateway-logo">
                @elseif($sale->gateway_id == 21 || $sale->gateway_id == 22)
                    <img src="build/global/img/vega-logo.png" alt="Vega Logo" class="gateway-logo">
                @endif
            </div>
        </td>
    </tr>
</table>
</body>
</html>
