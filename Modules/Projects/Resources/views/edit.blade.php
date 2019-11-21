<div class='card shadow p-30'>
    <form id='update-project'>
        @method('PUT')
        @csrf
        <div class='row justify-content-between align-items-baseline mt-15'>
            <div class='col-lg-12'>
                <h3>Configurações Básicas</h3>
                <p>Preencha atentamente as informações</p>
            </div>
            <div class='col-lg-4'>
                <div class='d-flex flex-column text-center' id='div-img-project' style='position: relative;'>
                    <input name='photo' type='file' class='form-control' id='photoProject' style='display:none;'
                           accept='image/*'>
                    <label for='photo'>Selecione uma imagem capa do projeto</label>
                    <div style="width:100%" class="text-center">
                        <img id='previewimage' alt='Selecione a foto do projeto'
                             src="{{asset('modules/global/img/projeto.png')}}"
                             style="min-width: 250px; max-width: 250px;margin: auto">
                    </div>
                    <input type='hidden' id='photo_x1' name='photo_x1'><input id='photo_y1' type='hidden'
                                                                              name='photo_y1'>
                    <input type='hidden' id='photo_w' name='photo_w'><input id='photo_h' type='hidden' name='photo_h'>
                    <p class='info pt-5' style='font-size: 10px;'>
                        <i class='icon wb-info-circle' aria-hidden='true'></i> Usada apenas internamente no sistema
                        <br>A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                        <br> Dimensões ideais: 300 x 300 pixels.
                    </p>
                </div>
            </div>
            <div class='col-lg-8'>
                <div class='row'>
                    <div class='form-group col-lg-12'>
                        <label for='name'>Nome do projeto</label>
                        <input name='name' value="" type='text' class='input-pad' id='name'
                               placeholder='Nome do Projeto' maxlength='40' required>
                        <span id='name-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Usado apenas internamente no sistema
                        </p>
                    </div>
                    <div class='form-group col-lg-12'>
                        <label for='description'>Descrição</label>
                        <textarea style='height:100px;' name='description' type='text' class='input-pad'
                                  id='description' placeholder='Fale um pouco sobre seu Projeto' required=''
                                  maxlength='100'></textarea>
                        <span id='description-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Usado apenas internamente no sistema
                        </p>
                    </div>
                    <div class='form-group col-lg-4'>
                        <label for='visibility'>Visibilidade</label>
                        <select name='visibility' class='form-control select-pad' id='visibility' required>
                            <option type='hidden' disabled value='public'>Projeto público</option>
                            <option value='private'>Projeto privado</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-8">
                        <div class="d-flex align-items-baseline justify-content-start mt-35">
                            <div class="info" style="font-size: 10px;">
                                <p class="ml-5">
                                    <b> Público: </b> visível na vitrine e disponível para afiliações (em breve). <br>
                                    <b> Privado: </b> completamente invisivel para outros usuários, afiliações somente por convite
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-md-4 col-lg-4 col-sm-12'>
                <div class="text-center">
                    <label for='name'>Imagem para página do checkout e para emails</label>
                </div>
                <div class='row'>
                    <div class="col-12">
                        <div class='d-flex flex-column text-center' id='div-img-project' style='position: relative;'>
                            <input name='logo' type='file' class='form-control' id='photo-logo-email'
                                   style='display:none;'>
                            <img id='image-logo-email' alt='Selecione a foto do projeto' src='{{asset('modules/global/img/projeto.png')}}' style='max-height:250px;max-width:250px;margin:auto'>
                            <input type='hidden' name='logo_h'> <input type='hidden' name='logo_w'>
                            <p class='info mt-5' style='font-size: 10px;'>
                                <i class='icon wb-info-circle' aria-hidden='true'></i> A imagem escolhida deve estar no formato JPG, JPEG ou PNG.
                                <br> Dimensões ideais: largura ou altura de no máximo 300 pixels. <br>
                                <strong>Sem sobras em branco no topo ou na parte inferior.</strong>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class='row col-md-8 col-lg-8 col-sm-12'>
                <div class="col-12 row">
                    <div class="form-group col-12">
                        <label for="url_page">URL da página principal</label>
                        <input name="url_page" value="" type="text" class="input-pad" id="url-page"
                               placeholder="URL da página" maxlength='60'>
                        <span id='url-page-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> URL da página principal da loja
                        </p>
                    </div>
                    <div class="form-group col-12">
                        <label for="contact">Email de Contato (checkout e email)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="input_group_contact" id="addon-contact">
                                </span>
                            </div>
                            <input name="contact" value="" type="text" class="input-pad form-control" id="contact" placeholder="Contato" maxlength='40' aria-describedby="addon-contact">
                        </div>
                        <span id='contact-error' class='text-danger'></span>
                        <small id="message_not_verified_contact" style='color:red; display:none;'>Email não verificado, clique
                            <a href='#' id='btn_verify_contact' onclick='event.preventDefault();' data-toggle='modal' data-target='#modal_verify_contact'>aqui</a>
                            para verificá-lo!
                        </small>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Contato da loja informado no checkout e nos emails
                        </p>
                    </div>
                    <div class="form-group col-12">
                        <label for="contact">Telefone para suporte</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="input_group_support_phone" id="addon-support_phone">
                                </span>
                            </div>
                            <input name="support_phone" value="" type="text" class="input-pad form-control" id="support_phone" placeholder="Telefone" data-mask="(00) 00000-0000" aria-describedby="addon-support_phone">
                        </div>
                        <span id='contact-error' class='text-danger'></span>
                        <small id="message_not_verified_support_phone" style='color:red; display:none;'>Telefone não verificado, clique
                            <a href='#' id='btn_verify_support_phone' onclick='event.preventDefault();' data-toggle='modal' data-target='#modal_verify_support_phone'>aqui</a>
                            para verificá-lo!
                        </small>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Telefone para suporte. Em compras por boleto na página de obrigado quando o cliente clicar em receber pelo whats a mensagem é encaminhada para esse número
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class='col-12 row' style='margin:auto; padding-top:50px'>
            <div id='toggler' class='col-12' data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                 aria-controls="collapseOne">
                <h3 style='position:absolute; bottom: 0px;'>Configurações Avançadas
                    <u style='font-size:15px; color:blue;cursor:pointer;' id='showMore'>exibir mais</u>
                </h3>
            </div>
        </div>
        {{--COMEÇO CONFIGURAÇÕES AVANÇADAS--}}
        <div class='mt-30 mb-15'>
            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class='row'>
                    <div class='form-group col-6 col-xs-12'>
                        <label for='invoice-description'>Descrição da Fatura</label>
                        {{--                        <input name='invoice_description' value='{{$project->invoice_description}}' type='text' class='input-pad' id='invoice-description' placeholder='Descrição da fatura' maxlength='50'>--}}
                        <input name='invoice_description' value='' type='text' class='input-pad'
                               id='invoice-description' placeholder='Descrição da fatura' maxlength='50'>
                        <span id='invoice-description-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Descrição apresentada na fatura do cartão de crédito
                        </p>
                    </div>
                    <div class='form-group col-6 col-xs-12'>
                        <label for='company'>Empresa responsável</label>
                        <select id='companies' name='company_id' class="form-control select-pad"> </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Empresa responsável pelo faturamento das vendas
                        </p>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for='quantity-installment_amount'>Quantidade de parcelas (cartão de crédito)</label>
                        <select class='installment_amount form-control select-pad' name='installments_amount'
                                class='form-control select-pad'>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Quantidade máxima de parcelas oferecidas no checkout
                        </p>
                    </div>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for="parcelas_sem_juros">Quantidade de parcelas sem juros</label>
                        <select class='parcelas-juros form-control select-pad' name='installments_interest_free'>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Quantidade de parcelas oferecidas sem juros (se oferecida mais de uma a taxa de juros é descontada do produtor)
                        </p>
                        <span id='error-juros' class='text-danger' style='display: none'>A quantidade de parcelas sem juros deve ser menor ou igual que a quantidade de parcelas</span>
                    </div>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for="parcelas_sem_juros">Dias para vencimento do boleto</label>
                        <select class='form-control select-pad' id='boleto_due_days' name='boleto_due_days'>
                            @for($x = 1; $x <= 28; $x++)
                                <option value='{{ $x }}'>{{ $x . ($x == 1 ? " dia" : " dias") }}</option>
                            @endfor
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Dias para vencimento do boleto
                        </p>
                    </div>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for="parcelas_sem_juros">Boleto no checkout</label>
                        <select name='boleto' class='form-control select-pad' id="boleto">
                            <option value='1'>Sim</option>
                            <option value='0'>Não</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Oferecer a opção de pagamento com boleto no checkout
                        </p>
                    </div>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for="cost_currency_type">Moeda padrão de custo</label>
                        <select name='cost_currency_type' class='form-control select-pad' id="cost_currency_type">
                            <option value='BRL'>Real</option>
                            <option value='USD'>Dólar</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Definir uma moeda padrão para a configuração dos seus planos. Configuração utilizada para emissão de notas fiscais.
                        </p>
                    </div>
                    <div class='form-group col-md-6 col-sm-12'>
                        <label for="credit_card">Cartão de crédito no checkout</label>
                        <select name='credit_card' class='form-control select-pad' id="credit_card">
                            <option value='1' class='credit_card_yes'>Sim</option>
                            <option value='0' class='credit_card_no'>Não</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Oferecer a opção de pagamento com cartão de crédito no checkout
                        </p>
                    </div>
                    <div class='form-group col-md-4 col-sm-12'>
                        <label for="default_currency">Tipo de checkout</label>
                        <select name='checkout_type' class='form-control select-pad' id="checkout_type">
                            <option value='1'>Checkout de 3 etapas (recomendado)</option>
                            <option value='2'>Checkout de 1 etapa</option>
                            <option value='' disabled>Checkout selecionado por IA - inteligência artificial (em breve)</option>
                        </select>
                    </div>
                    <div class='col-md-8'></div>
                    <div class='form-group col-md-6 col-sm-12 col-xs-12'>
                        <label for='card_redirect'>Cartão (Redirecionamento página obrigado)</label>
                        <input id='card_redirect' name='card_redirect' value='' class='input-pad' type='text' placeholder='URL' maxlength='60'>
                        <span id='input-pad-error' class='text-danger'></span>
                    </div>
                    <div class='form-group col-md-6 col-sm-12 col-xs-12'>
                        <label for='boleto_redirect'>Boleto (Redirecionamento página obrigado)</label>
                        <input id='boleto_redirect' name='boleto_redirect' value='' class='input-pad' type='text'
                               placeholder='URL' maxlength='60'>
                        <span id='boleto_redirect-error' class='text-danger'></span>
                    </div>
                    <p class="info mt-5 col-12" style="font-size: 10px;">
                        <i class="icon wb-info-circle" aria-hidden="true"></i> Caso você queira redirecionar o seu cliente para paginas de obrigado propias, informe a
                        <strong>URL</strong> delas nos campos acima. Caso não informadas será redirecionado para a pagina de obrigado padrão do cloudfox.
                    </p>
                </div>
            </div>
        </div>
        {{--FIM CONFIGURAÇÕES AVANÇADAS--}}
        <div id="shopify-configs" style="display:none">
            <div class='col-12 row' style='margin:auto; padding-top:50px;'>
                <div id='toggler' class='col-12' data-toggle="collapse" data-target="#collapseOneShopify"
                     aria-expanded="true" aria-controls="collapseOne">
                    <h3 style='position:absolute; bottom: 0px;'>Configurações Shopify
                        <u style='font-size:15px; color:blue;cursor:pointer;' id='showMore'>exibir mais</u>
                    </h3>
                </div>
            </div>
            {{-- COMEÇO CONFIGURAÇÕES SHOPIFY --}}
            <div class='mt-30 mb-15'>
                <div id='collapseOneShopify' class='collapse'>
                    <div class='row justify-content-center mx-30'>
                        <label for='undo-integration'></label>
                        <div class="col-md-4 mb-10 mt-10">
                            <a id="bt-change-shopify-integration" role="button" integration-status=""
                               class="pointer align-items-center" data-toggle="modal"
                               data-target="#modal-change-shopify-integration">
                                <i class="material-icons gray"> sync </i>
                                <span class="gray"></span>
                            </a>
                            <div id="shopify-integration-pending" style="display:none">
                                <i class="icon wb-alert-circle  gray"> </i>
                                <span class="gray"> Integração com o shopify em andamento, aguarde. </span>
                            </div>
                        </div>
                        <div class='col-md-4 mt-10'>
                            <a id="bt-shopify-sincronization-product" role="button" integration-status=""
                               class="pointer align-items-center" data-toggle="modal"
                               data-target="#modal-change-shopify-integration">
                                <i class="material-icons gray"> sync </i>
                                <span class="gray"> Sincronizar produtos com shopify </span>
                            </a>
                        </div>
                        <div class='col-md-4 mt-10'>
                            <a id="bt-shopify-sincronization-template" role="button"
                               integration-status=""
                               class="pointer align-items-center" data-toggle="modal"
                               data-target="#modal-change-shopify-integration">
                                <i class="material-icons gray"> sync </i>
                                <span class="gray"> Sincronizar template com shopify </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- FIM CONFIGURAÇÕES SHOPIFY--}}
        <div class="mt-30">
            <div class="row">
                <div class="col-6">
                    <a id="bt-delete-project" role="button" class="pointer align-items-center" data-toggle="modal"
                       data-target="#modal-delete-project" style="float: left;">
                        <i class="material-icons gray"> delete </i>
                        <span class="gray"> Deletar projeto</span>
                    </a>
                </div>
                <div class="col-6">
                    <button id="bt-update-project" type="button" class="btn btn-success" style="float: right;">
                        Atualizar
                    </button>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-delete-project" aria-hidden="true" role="dialog" tabindex="-1">
        <div class="modal-dialog  modal-dialog-centered  modal-simple">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <a class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div id="modal_excluir_body" class="modal-body text-center p-20">
                    <div class="d-flex justify-content-center">
                        <i class="material-icons gradient" style="font-size: 80px;color: #ff4c52;"> highlight_off </i>
                    </div>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Se você excluir esse registro, não será possível recuperá-lo! </p>
                </div>
                <div class="modal-footer d-flex align-items-center justify-content-center">
                    <button type="button" class="col-4 btn btn-gray" data-dismiss="modal" style="width: 20%;">Cancelar</button>
                    <button type="button" class="col-4 btn btn-danger btn-delete" data-dismiss="modal" style="width: 20%;">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    {{--Modal Verificação Celular--}}
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_support_phone" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" style="width: 100%; text-align:center">Verificar telefone de suporte</h4>
                </div>
                <div class="modal-body" style="margin-top: 10px">
                    <span>Um código de verificação foi enviado para o seu celular, digite o código recebido no campo abaixo (pode demorar alguns instantes, aguarde até receber o sms)</span>
                    <br>
                    <form method="POST" enctype="multipart/form-data" id='match_support_phone_verifycode_form'>
                        @csrf
                        <label for="support_phone_verify_code" style="margin-top: 20px">Código de verificação</label>
                        <input id="support_phone_verify_code" type="number" min='0' max='9999999' minlength='6' maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                        <button type='submit' class='btn btn-success mt-20'>
                            <i class='fas fa-check'></i> Verificar
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    {{--Modal Verificação Email--}}
    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_verify_contact" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-simple">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="fechar_modal_excluir">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" style="width: 100%; text-align:center">Verificar email de contato</h4>
                </div>
                <div class="modal-body" style="margin-top: 10px">
                    <span>Um código de verificação foi enviado para o seu email, digite o código recebido no campo abaixo (pode demorar alguns instantes, aguarde até receber o email)</span>
                    <br>
                    <form method="POST" enctype="multipart/form-data" id='match_contact_verifycode_form'>
                        @csrf
                        <label for="contact_verify_code" style="margin-top: 20px">Código de verificação</label>
                        <input id="contact_verify_code" type="number" min='0' max='9999999' minlength='6' maxlength='7' class="form-control input-pad" placeholder="Insira o código aqui">
                        <button type='submit' class='btn btn-success mt-20'>
                            <i class='fas fa-check'></i> Verificar
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</div>
