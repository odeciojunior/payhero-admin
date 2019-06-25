<div class="page-content container-fluid">
    <div class="panel pt-10 p-10" data-plugin="matchHeight">

        <div class="nav-tabs-horizontal" data-plugin="tabs" style="height:100%">
            <ul class="nav nav-tabs-line" role="tablist" style="color: #ee535e">
                <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#sales_tab"
                    aria-controls="sales_tab" role="tab">Venda</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#client_tab"
                    aria-controls="client_tab" role="tab">Cliente</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#products_tab"
                    aria-controls="products_tab" role="tab">Produtos</a></li>
            </ul>
            <div class="tab-content pt-20">
                <div class="tab-pane active" id="sales_tab" role="tabpanel" style="min-width: 300px">
                    <table class="table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 40px">
                                <td style="width: 40%" class="text-right">TRANSAÇÃO:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! "#".$sale['id'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%" class="text-right">FORMA DE PAGAMENTO:</td>
                                <td style="width:20px">
                                @if($sale['payment_form'] == 'boleto')
                                    <td class="text-left">Boleto</td>
                                @else
                                    <td class="text-left">Cartão de crédito</td>
                                @endif
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%" class="text-right">DATA:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! $sale['start_date'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%" class="text-right">STATUS:</td>
                                <td style="width:20px">
                                @if($sale['gateway_status'] == 'paid' || $sale['gateway_status'] == 'CO')
                                    <td class="text-left">Aprovada</td>
                                @elseif($sale['gateway_status'] == 'refused')
                                    <td class="text-left">Rejeitada</td>
                                @elseif($sale['gateway_status'] == 'waiting_payment' || $sale['gateway_status'] == 'PE')
                                    <td class="text-left">Aguardando pagamento</td>
                                @else
                                    <td class="text-left">{!! $sale['gateway_status'] !!}</td>
                                @endif
                            </tr>
                            @if($sale['payment_method'] == '2')
                                <tr style="height: 60px">
                                    <td colspan='3' class='text-center' style="margin: 15px 0 15px 0"><b>INFORMAÇÕES DO BOLETO</b></td>
                                </tr>
                                <tr style="height: 40px">
                                    <td style="width: 40%" class="text-right">Link do boleto:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $sale['boleto_link'] !!}</td>
                                </tr>
                                <tr style="height: 40px; margin-top:10px">
                                    <td style="width: 40%" class="text-right">Linha digitável:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $sale['boleto_digitable_line'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="client_tab" role="tabpanel">
                    <table class="table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 30px">
                                <td style="width: 40%" class="text-right">Nome:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! $client['name'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td style="width: 40%" class="text-right">CPF:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! $client['document'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td style="width: 40%" class="text-right">Email:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! $client['email'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td style="width: 40%" class="text-right">Telefone:</td>
                                <td style="width:20px">
                                <td class="text-left">{!! $client['telephone'] !!}</td>
                            </tr>
                            @if($delivery)
                                <tr style="height: 30px">
                                    <td colspan='3' class='text-center' style="margin: 15px 0 15px 0">INFORMAÇÕES DA ENTREGA</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Valor do frete:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $sale['shipment_value'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Rua:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['street'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Número:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['number'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Complemento:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['complement'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Bairro:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['neighboorhod'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Cidade:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['city'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td style="width: 40%" class="text-right">Estado:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['state'] !!}</td>
                                </tr>
                                <tr style="height: 30px">    
                                    <td style="width: 40%" class="text-right">CEP:</td>
                                    <td style="width:20px">
                                    <td class="text-left">{!! $delivery['zip_code'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="products_tab" role="tabpanel">
                    <table class="table-hover" style="width: 100%">
                        <thead>
                            <th class="text-left">Produto</th>
                            <th class="text-left">Quantidade</th>
                        </thead>
                        <tbody style="margin-top:15px">
                            @foreach($plans as $plan)
                                <tr style="height: 30px">
                                    <td class="text-left">{!! $plan['name'] !!}</td>
                                    <td class="text-left">{!! $plan['amount'] !!}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function(){

        $("#sales_tab").css("min-width", $(window).width() / 2);
        
        $("#client_tab").css("min-width",$("#sales_tab").width());
        $("#products_tab").css("min-width",$("#sales_tab").width());
    });

</script>

