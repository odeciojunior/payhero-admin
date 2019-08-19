<div class='row no-gutters mb-10'>
    <div class="top-holder text-right mb-5" style="width: 100%;">
        {{-- <div id="add-sms" class="d-flex align-items-center justify-content-end pointer" data-toggle="modal" data-target="#modal-content"> --}}
        <div id="add-sms" class="d-flex align-items-center justify-content-end pointer">
            <button type="button" class="ml-10 rounded-add pointer btn" disabled='true'>
                <i class="icon wb-plus" aria-hidden="true"></i></button>
        </div>
    </div>
</div>
<div class="card shadow">
    <div style='min-height: 300px'>
        <div class='page-invoice-table table-responsive'>
            <table id='tabela-sms' class='table text-left table-sms table-striped unify' style='width:100%'>
                <thead>
                    <tr>
                        <td class='table-title'>Tipo</td>
                        <td class='table-title display-m-none display-sm-none'>Evento</td>
                        <td class='table-title'>Tempo</td>
                        <td class='table-title'>Mensagem</td>
                        <td class='table-title'>Status</td>
                        <td class='table-title text-center options-column-width'>Opções</td>
                    </tr>
                </thead>
                <tbody id='data-table-sms' class='min-row-height'>
                    <tr class='page-1'>
                        <td class="" style="vertical-align: middle;">SMS</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto gerado</td>
                        <td class="" style="vertical-align: middle;">Imediato</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">
                            Olá {primeiro_nome}, não esqueça de pagar seu boleto para enviarmos seu pedido! {url_boleto}
                        </td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td class="text-center" style="vertical-align: middle;">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-1'>
                        <td class="" style="vertical-align: middle;">SMS</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto vencendo</td>
                        <td class="" style="vertical-align: middle;">10:00 horas</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, seu boleto vence hoje, não deixe de efetuar o pagamento e garantir seu pedido! {url_boleto}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class="text-center">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-1'>
                        <td class="" style="vertical-align: middle;">SMS</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Carrinho abandonado
                        </td>
                        <td class="" style="vertical-align: middle;">4 horas depois</td>
                        <td class="shipping-zip-code-origin " style="vertical-align: middle">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class="text-center">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-1'>
                        <td class="" style="vertical-align: middle;">SMS</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Carrinho abandonado</td>
                        <td class="" style="vertical-align: middle;">10:00 horas próximo dia</td>
                        <td class="shipping-zip-code-origin " style="vertical-align: middle;">Olá {primeiro_nome}, somos da loja {projeto_nome}, vimos que voce não finalizou seu pedido, aproveite o último dia da promoção! {link_carrinho_abandonado}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class="text-center">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-1'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto gerado</td>
                        <td class="" style="vertical-align: middle;">Imediato</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, aqui está seu boleto. Como você optou por Boleto Bancário, estamos enviando por aqui para você não se esquecer. O boleto deve ser pago até a data de vencimento para enviarmos seu(s) pedido(s)! {url_boleto}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class="text-center">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-2' style='display:none;'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto gerado</td>
                        <td class="" style="vertical-align: middle;">10:00 horas próximo dia</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, estamos enviando esse email só para avisar que já empacotamos sua encomenda e estamos prontos para enviar para você. Assim que o boleto for pago e recebermos a confirmação sua encomenda será enviada!</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class="text-center">
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-2' style='display:none;'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto gerado</td>
                        <td class="" style="vertical-align: middle;">10:00 horas 2 dias após</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, por falta de pagamento vamos ter que liberar sua mercadoria para o estoque novamente. Isso siginigfica que se você não efetuar o pagamento, cancelaremos seu pedido!</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class='text-center'>
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-2' style='display:none;'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Boleto vencendo</td>
                        <td class="" style="vertical-align: middle;">10:00 horas</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, seu boleto vencerá hoje, ainda dá tempo de pagar! Não se esqueça, só enviaremos o seu pedido (que já está separado) se você efetuar o pagamento! {url_boleto}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class='text-center'>
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-2' style='display:none;'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Carrinho abandonado</td>
                        <td class="" style="vertical-align: middle;">4 horas depois</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, nossos produtos estão com preços especiais e o estoque é bem limitado. Recomendamos que você finalize a compra ainda hoje para garantir a promoção e economizar dinheiro! {link_carrinho_abandonado}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class='text-center'>
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                    <tr class='page-2' style='display:none;'>
                        <td class="" style="vertical-align: middle;">Email</td>
                        <td class="display-m-none display-sm-none" style="vertical-align: middle;">Carrinho abandonado</td>
                        <td class="" style="vertical-align: middle;">10:00 horas próximo dia</td>
                        <td class="shipping-zip-code-origin " style="vertical-align:middle;">Olá {primeiro_nome}, vimos que você não aproveitou a promoção de ontem. O seu pedido ainda está separado aguardando a finalização da compra, mas não podemos segurar por muito tempo! {link_carrinho_abandonado}</td>
                        <td class="shipping-status " style="vertical-align: middle;">
                            <span class="badge badge-success mb-1">Ativo</span>
                            <span class="badge badge-primary">Grátis</span>
                        </td>
                        <td style='vertical-align: middle' class='text-center'>
                            <a role='button' class='pointer disabled details-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'>remove_red_eye</i></a>
                            <a role='button' class='pointer disabled edit-sms mg-responsive' data-target='#modal-content' data-toggle='modal'>
                                <i class='material-icons gradient'> edit </i></a>
                            <a role='button' class='pointer disabled delete-sms mg-responsive' data-toggle='modal' data-target='#modal-delete'>
                                <i class='material-icons gradient'> delete_outline </i></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<button id='current-page-shippings2' class='btn nav-btn float-right'>2</button>
<button id='current-page-shippings1' class='btn nav-btn active float-right'>1</button>

