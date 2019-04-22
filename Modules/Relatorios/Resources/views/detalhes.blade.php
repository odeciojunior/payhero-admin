<div class='col-xl-12 col-lg-12'>
    <table class='table table-bordered table-hover table-striped'>
        <thead>
        </thead>
        <tbody>
            <tr>
                <td colspan='2' class='text-center'><b>INFORMAÇÕES GERAIS</b></td>
            </tr>
            <tr>
                <td><b>Código da transação:</b></td>
                <td>#{!! $venda['id'] !!}</td>
            </tr>
            <tr>
                <td><b>Forma de pagamento:</b></td>
                <td>{!! $venda['forma_pagamento'] !!}</td>
            </tr>
            <tr>
                <td><b>Data:</b></td>
                <td>{!! $venda['data_inicio'] !!}</td>
            </tr>
            <tr>
                <td><b>Status:</b></td>
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
            <tr>
                <td colspan='2' class='text-center'><b>PRODUTOS DA VENDA</b></td>
            </tr>
            @foreach($planos as $plano)
                <tr>
                    <td><b>Produto:</b></td>
                    <td>{!! $plano['nome'] !!}</td>
                </tr>
                <tr>
                    <td><b>Quantidade:</b></td>
                    <td>{!! $plano['quantidade'] !!}</td>
                </tr>
            @endforeach
            <td colspan='2' class='text-center'><b>INFORMAÇÕES DO CLIENTE</b></td>
            </tr>
            <tr>
                <td><b>Comprador:</b></td>
                <td>{!! $comprador['nome'] !!}</td>
            </tr>
            <tr>
                <td><b>CPF:</b></td>
                <td>{!! $comprador['cpf_cnpj'] !!}</td>
            </tr>
            <tr>
                <td><b>Email:</b></td>
                <td>{!! $comprador['email'] !!}</td>
            </tr>
            <tr>
                <td><b>Telefone:</b></td>
                <td>{!! $comprador['telefone'] !!}</td>
            </tr>
            @if($entrega)
                <tr>
                    <td colspan='2' class='text-center'><b>INFORMAÇÕES DA ENTREGA</b></td>
                </tr>
                <tr>
                    <td><b>Valor do frete:</b></td>
                    <td>{!! $venda['valor_frete'] !!}</td>
                </tr>
                <tr>
                    <td><b>Rua:</b></td>
                    <td>{!! $entrega['rua'] !!}</td>
                </tr>
                <tr>
                    <td><b>Número:</b></td>
                    <td>{!! $entrega['numero'] !!}</td>
                </tr>
                <tr>
                    <td><b>Complemento:</b></td>
                    <td>{!! $entrega['ponto_referencia'] !!}</td>
                </tr>
                <tr>
                    <td><b>Bairro:</b></td>
                    <td>{!! $entrega['bairro'] !!}</td>
                </tr>
                <tr>
                    <td><b>Cidade:</b></td>
                    <td>{!! $entrega['cidade'] !!}</td>
                </tr>
                <tr>
                    <td><b>Estado:</b></td>
                    <td>{!! $entrega['estado'] !!}</td>
                </tr>
                <tr>    
                    <td><b>CEP:</b></td>
                    <td>{!! $entrega['cep'] !!}</td>
                </tr>
            @endif
            @if($venda['forma_pagamento'] == 'Boleto')
                <tr>
                    <td colspan='2' class='text-center'><b>INFORMAÇÕES DO BOLETO</b></td>
                </tr>
                <tr>
                    <td><b>Link do boleto:</b></td>
                    <td>{!! $venda['link_boleto'] !!}</td>
                </tr>
                <tr>
                    <td><b>Linha digitável do boleto:</b></td>
                    <td>{!! $venda['linha_digitavel_boleto'] !!}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

