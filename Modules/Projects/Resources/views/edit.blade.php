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
                                    <b> Privado: </b> completamente invisivel para outros usuários, afiliações somente
                                    por convite
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
                            <img id='image-logo-email' alt='Selecione a foto do projeto'
                                 src='{{asset('modules/global/img/projeto.png')}}'
                                 style='max-height:250px;max-width:250px;margin:auto'>
                            <input type='hidden' name='logo_h'> <input type='hidden' name='logo_w'>
                            <p class='info mt-5' style='font-size: 10px;'>
                                <i class='icon wb-info-circle' aria-hidden='true'></i> A imagem escolhida deve estar no
                                formato JPG, JPEG ou PNG.
                                <br> Dimensões ideais: largura ou altura de no máximo 300 pixels. <br>
                                <strong>Sem sobras no topo ou na parte inferior.</strong>
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
                        <input name="contact" value="" type="text" class="input-pad" id="contact" placeholder="Contato"
                               maxlength='40'>
                        <span id='contact-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Contato da loja informado no checkout
                            e nos emails
                        </p>
                        <span id='contact-error'></span>
                    </div>
                    <div class="form-group col-12">
                        <label for="contact">Telefone para suporte</label>
                        <input name="support_phone" value="" type="text" class="input-pad" id="support_phone"
                               placeholder="Telefone" data-mask="(00) 00000-0000">
                        <span id='contact-error' class='text-danger'></span>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Telefone para suporte
                        </p>
                        <span id='contact-error'></span>
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
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Descrição apresentada na fatura do
                            cartão de crédito
                        </p>
                    </div>
                    <div class='form-group col-6 col-xs-12'>
                        <label for='company'>Empresa responsável</label>
                        <select id='companies' name='company_id' class="form-control select-pad">
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Empresa responsável pelo faturamento
                            das vendas
                        </p>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
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
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Quantidade máxima de parcelas
                            oferecidas no checkout
                        </p>
                    </div>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
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
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Quantidade de parcelas oferecidas sem
                            juros (se oferecida mais de uma a taxa de juros é descontada do produtor)
                        </p>
                        <span id='error-juros' class='text-danger' style='display: none'>A quantidade de parcelas sem juros deve ser menor ou igual que a quantidade de parcelas</span>
                    </div>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
                        <label for="parcelas_sem_juros">Boleto no checkout</label>
                        <select name='boleto' class='form-control select-pad' id="boleto">
                            <option value='1'>Sim</option>
                            <option value='0'>Não</option>
                        </select>
                        <p class='info pt-5' style='font-size: 10px;'>
                            <i class='icon wb-info-circle' aria-hidden='true'></i> Oferecer a opção de pagamento com
                            boleto no checkout
                        </p>
                    </div>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
                        <label for='boleto_redirect'>Boleto (Redirecionamento página obrigado)</label>
                        <input id='boleto_redirect' name='boleto_redirect' value='' class='input-pad' type='text'
                               placeholder='URL' maxlength='60'>
                        <span id='boleto_redirect-error' class='text-danger'></span>
                    </div>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
                        <label for='card_redirect'>Cartão (Redirecionamento página obrigado)</label>
                        <input id='card_redirect' name='card_redirect' value='' class='input-pad' type='text'
                               placeholder='URL' maxlength='60'>
                        <span id='input-pad-error' class='text-danger'></span>
                    </div>
                    <div class='form-group col-md-4 col-sm-12 col-xs-12'>
                        <label for='analyzing_redirect'>Em Analise (Redirecionamento página obrigado)</label>
                        <input id='analyzing_redirect' name='analyzing_redirect' value='' class='input-pad' type='text'
                               placeholder='URL' maxlength='60'>
                    </div>
                    <p class="info mt-5 col-12" style="font-size: 10px;">
                        <i class="icon wb-info-circle" aria-hidden="true"></i> Caso você queira redirecionar o seu
                        cliente para paginas de obrigado propias, informe a
                        <strong>URL</strong> delas nos campos acima. Caso não informadas será redirecionado para a
                        pagina de obrigado padrão do cloudfox.
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

