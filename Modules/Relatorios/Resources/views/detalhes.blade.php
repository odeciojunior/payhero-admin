<div class="page-content container-fluid">
    <div class="panel pt-10 p-10" data-plugin="matchHeight">

        <div class="nav-tabs-horizontal" data-plugin="tabs" style="height:100%">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item" role="presentation"><a class="nav-link active" data-toggle="tab" href="#tab_venda"
                    aria-controls="tab_venda" role="tab">Venda</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_cliente"
                    aria-controls="tab_cliente" role="tab">Cliente</a></li>
                <li class="nav-item" role="presentation"><a class="nav-link" data-toggle="tab" href="#tab_produtos"
                    aria-controls="tab_produtos" role="tab">Produtos</a></li>
            </ul>
            <div class="tab-content pt-20">
                <div class="tab-pane active" id="tab_venda" role="tabpanel" style="min-width: 300px">
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 40px">
                                <td style="width: 40%"><b>TRANSAÇÃO:</b></td>
                                <td>{!! "#".$venda['id'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%"><b>FORMA:</b></td>
                                <td>{!! $venda['forma_pagamento'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%"><b>DATA:</b></td>
                                <td>{!! $venda['data_inicio'] !!}</td>
                            </tr>
                            <tr style="height: 40px">
                                <td style="width: 40%"><b>STATUS:</b></td>
                                @if($venda['pagamento_status'] == 'paid')
                                    <td>Aprovada</td>
                                @elseif($venda['pagamento_status'] == 'refused')
                                    <td>Rejeitada</td>
                                @elseif($venda['pagamento_status'] == 'waiting_payment')
                                    <td>Aguardando pagamento</td>
                                @else
                                    <td>{!! $venda['pagamento_status'] !!}</td>
                                @endif
                            </tr>
                            @if($venda['forma_pagamento'] == 'Boleto')
                                <tr style="height: 60px">
                                    <td colspan='2' class='text-center' style="margin: 15px 0 15px 0"><b>INFORMAÇÕES DO BOLETO</b></td>
                                </tr>
                                <tr style="height: 40px">
                                    <td><b>Link do boleto:</b></td>
                                    <td>{!! $venda['link_boleto'] !!}</td>
                                </tr>
                                <tr style="height: 40px">
                                    <td><b>Linha digitável:</b></td>
                                    <td>{!! $venda['linha_digitavel_boleto'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab_cliente" role="tabpanel">
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            <tr style="height: 30px">
                                <td><b>Nome:</b></td>
                                <td>{!! $comprador['nome'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td><b>CPF:</b></td>
                                <td>{!! $comprador['cpf_cnpj'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td><b>Email:</b></td>
                                <td>{!! $comprador['email'] !!}</td>
                            </tr>
                            <tr style="height: 30px">
                                <td><b>Telefone:</b></td>
                                <td>{!! $comprador['telefone'] !!}</td>
                            </tr>
                            @if($entrega)
                                <tr style="height: 30px">
                                    <td colspan='2' class='text-center' style="margin: 15px 0 15px 0"><b>INFORMAÇÕES DA ENTREGA</b></td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Valor do frete:</b></td>
                                    <td>{!! $venda['valor_frete'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Rua:</b></td>
                                    <td>{!! $entrega['rua'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Número:</b></td>
                                    <td>{!! $entrega['numero'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Complemento:</b></td>
                                    <td>{!! $entrega['ponto_referencia'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Bairro:</b></td>
                                    <td>{!! $entrega['bairro'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Cidade:</b></td>
                                    <td>{!! $entrega['cidade'] !!}</td>
                                </tr>
                                <tr style="height: 30px">
                                    <td><b>Estado:</b></td>
                                    <td>{!! $entrega['estado'] !!}</td>
                                </tr>
                                <tr style="height: 30px">    
                                    <td><b>CEP:</b></td>
                                    <td>{!! $entrega['cep'] !!}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane" id="tab_produtos" role="tabpanel">
                    <table class="table-bordered table-hover" style="width: 100%">
                        <tbody>
                            @foreach($planos as $plano)
                                <tr style="height: 40px">
                                    <td><b>Produto:</b></td>
                                    <td>{!! $plano['nome'] !!}</td>
                                </tr>
                                <tr style="height: 40px">
                                    <td><b>Quantidade:</b></td>
                                    <td>{!! $plano['quantidade'] !!}</td>
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

        $("#tab_venda").css("min-width", $(window).width() / 2);
        
        $("#tab_cliente").css("min-width",$("#tab_venda").width());
        $("#tab_produtos").css("min-width",$("#tab_venda").width());
    });

</script>

