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
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 40px">
                                <td style="width: 40%">TRANSAÇÃO:</td>
                                <td>{!! "#".$sale['id'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%">FORMA:</td>
                                <td>{!! $sale['payment_form'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%">DATA:</td>
                                <td>{!! $sale['start_date'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%">STATUS:</td>
                                @if($sale['gateway_status'] == 'paid' || $sale['gateway_status'] == 'CO')
                                    <td>Aprovada</td>
                                @elseif($sale['gateway_status'] == 'refused')
                                    <td>Rejeitada</td>
                                @elseif($sale['gateway_status'] == 'waiting_payment' || $sale['gateway_status'] == 'PE')
                                    <td>Aguardando pagamento</td>
                                @else
                                    <td>{!! $sale['gateway_status'] !!}</td>
                                @endif
                            </tr>
                            @if($sale['payment_form'] == 'boleto')
                                <tr style="height: 60px">
                                    <td colspan='2' class='text-center' style="margin: 15px 0 15px 0"><b>INFORMAÇÕES DO BOLETO</b></td>
                                </tr>
                                <tr style="height: 40px">
                                    <td>Link do boleto:</td>
                                    <td>{!! $sale['boleto_link'] !!}</td>
                                </tr>
                                <tr style="height: 40px">
                                    <td>Linha digitável:</td>
                                    <td>{!! $sale['boleto_digitable_line'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="client_tab" role="tabpanel">
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 30px">
                                <td>Nome:</td>
                                <td>{!! $client['name'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td>CPF:</td>
                                <td>{!! $client['document'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td>Email:</td>
                                <td>{!! $client['email'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td>Telefone:</td>
                                <td>{!! $client['telephone'] !!}</td>
                            </tr>
                            @if($delivery)
                                <tr style="height: 30px">
                                    <td colspan='2' class='text-center' style="margin: 15px 0 15px 0">INFORMAÇÕES DA ENTREGA</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Valor do frete:</td>
                                    <td>{!! $sale['shipment_value'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Rua:</td>
                                    <td>{!! $delivery['street'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Número:</td>
                                    <td>{!! $delivery['number'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Complemento:</td>
                                    <td>{!! $delivery['complement'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Bairro:</td>
                                    <td>{!! $delivery['neighboorhod'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Cidade:</td>
                                    <td>{!! $delivery['city'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td>Estado:</td>
                                    <td>{!! $delivery['state'] !!}</td>
                                </tr>
                                <tr style="height: 30px">    
                                    <td>CEP:</td>
                                    <td>{!! $delivery['zip_code'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="products_tab" role="tabpanel">
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            @foreach($plans as $plan)
                                <tr style="height: 40px">
                                    <td>Produto:</td>
                                    <td>{!! $plan['name'] !!}</td>
                                </tr>
                                <tr style="height: 40px">
                                    <td>Quantidade:</td>
                                    <td>{!! $plan['amount'] !!}</td>
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

