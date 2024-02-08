@push('css')
    <link rel="stylesheet"
          href="{!! mix('build/layouts/pixels/edit.min.css') !!}">
@endpush

<div style="display: block;"
     id="configure-edit-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10 align-items-center">
            <div class="col-2"
                 data-placement="top">
                <img src=""
                     class="img-logo img-fluid img-edit-selected"
                     alt="image selected">
            </div>
            <div class="col-7"
                 style="border-left: 1px solid #70707040; border-right: 1px solid #70707040">
                <h4 class="col-12 modal-title text-center"
                    id="modal-title"
                    style="color:#787878; font: normal normal bold 22px Inter;">
                    Editar Pixel
                </h4>
            </div>
            <div class="col-3">
                <div class="switch-holder d-flex align-items-center">
                    <label class='switch'>
                        <input type="checkbox"
                               value=""
                               name='status'
                               class='check status-edit'
                               checked>
                        <span class='slider round'></span>
                    </label>
                    <label for="boleto"
                           style="font: normal normal bold 16px Inter;color: #41DC8F;margin-bottom: 0;">Ativo</label>
                </div>
            </div>

            <a id="modal-button-close"
               class="pointer close"
               role="button"
               data-dismiss="modal"
               aria-label="Close"
               style="position: absolute;right: 45px;top: 25px;">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div class="modal-body pb-0">
            <div class="row">
                <form id='form-register-pixel'>
                    @csrf
                    <input type="hidden"
                           name="platform"
                           id="platform"
                           value="">
                    <div class="container-fluid">
                        <div class="panel"
                             data-plugin="matchHeight"
                             style="box-shadow: none;">
                            <div style="width:100%">
                                <div class="row">
                                    {{-- INPUT RADIO FACEBOOK-API --}}
                                    <input type="hidden"
                                           class="platform-edit"
                                           value="">
                                    <div class="form-group col-6"
                                         id="select-facebook-integration-edit"
                                         style="display:none;">
                                        <label class="font-text">Tipo</label><br>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio"
                                                   name="api-facebook"
                                                   value="default"
                                                   class="col-md-2 form-check-input facebook-api-default-edit"
                                                   checked>
                                            Padrão
                                        </label>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio"
                                                   name="api-facebook"
                                                   value="api"
                                                   class="col-md-2 form-check-input facebook-api-edit">
                                            API
                                        </label>
                                    </div>
                                    {{-- INPUT TOKEN FACEBOOK --}}
                                    <div class="form-group col-6"
                                         id="div-facebook-token-api-edit"
                                         style="display:none;">
                                        <label class="font-text">Token Acesso API Conversões</label>
                                        <input name="facebook-token-api-edit"
                                               type="text"
                                               id='facebook-token-api-edit'
                                               class="form-control pixel-code"
                                               placeholder="Token"
                                               maxlength='255'
                                               readonly>
                                    </div>

                                    {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                                    <div class="form-group col-6 div-purchase-event-name-edit"
                                         style="display:none;">
                                        <label for="purchase-event-name"
                                               class="form-control-label">
                                            Nome Evento Conversão
                                        </label>
                                        <input name="purchase-event-name"
                                               type="text"
                                               id='purchase-event-name'
                                               class="form-control input-purchase-event-name-edit"
                                               placeholder="Purchase"
                                               maxlength='255'>
                                    </div>
                                    {{-- INPUT URL FACEBOOK API --}}
{{--                                    <div class="form-group col-12 url_facebook_api_div_edit"--}}
{{--                                         style="display:none;">--}}
{{--                                        <label for="url_facebook_domain_edit"--}}
{{--                                               class="font-text">Domínio URL (opcional):</label>--}}
{{--                                        <input name="url_facebook_domain_edit"--}}
{{--                                               type="text"--}}
{{--                                               class="form-control url_facebook_domain_edit">--}}
{{--                                    </div>--}}
                                    {{-- INPUT DESCRIPTION --}}
                                    <div class="form-group col-6">
                                        <label for="name"
                                               class="font-text">Descrição</label>
                                        <input name="name"
                                               type="text"
                                               class="form-control description-edit"
                                               placeholder="Descrição"
                                               maxlength='30'>
                                    </div>

                                    {{-- INPUT CODE PIXEL --}}
                                    <div class="form-group col-6">
                                        <label id="code-pixel-label"
                                               for="code-pixel"
                                               class="font-text">Código</label>
                                        <div class="input-group">
                                            <span class='input-group-text'
                                                  id='text-type-code-edit'
                                                  style='background:#f3f3f3;display:none'>
                                            </span>
                                            <input type="text"
                                                   class="form-control code-edit"
                                                   name="code"
                                                   placeholder="52342343245553"
                                                   maxlength="255">
                                        </div>
                                    </div>

                                    {{-- INPUT CONVERSIONAL LABEL --}}
                                    <div id="conversional-pixel-edit"
                                         class="form-group col-6"
                                         style="display: none">
                                        <label id=""
                                               for="conversional-pixel-code"
                                               class="font-text">Rótulo de conversão</label>
                                        <div class="input-group">
                                            <span class='input-group-text'
                                                  id='text-type-conversional-edit'
                                                  style='background:#f3f3f3;'>/
                                            </span>
                                            <input type="text"
                                                   class="form-control conversional-edit"
                                                   name="conversional"
                                                   id="conversional-pixel-code"
                                                   placeholder="AN7162ASNSG"
                                                   maxlength="255">
                                        </div>
                                    </div>

                                    <div class='form-group col-12'>
                                        <label for="edit-plans"
                                               class="form-control-label">
                                            Plano(s) que executarão o pixel
                                        </label>
                                        <select name="edit_plans[]"
                                                class="js-states form-control edit-plans apply_plans"
                                                style='width:100%'
                                                data-plugin="select2"
                                                multiple='multiple'>
                                            <option value='all'>Todos</option>
                                        </select>
                                    </div>

                                    <div class="col-6 swicth-show-input">
                                        <div class="switch-holder">
                                            <label for="checkout"
                                                   class='mb-10'>Disparar "Purchase" ao gerar boleto</label>
                                            <br>
                                            <label class="switch">
                                                <input type="checkbox"
                                                       value=""
                                                       name='percentage_purchase_boleto_enabled'
                                                       class='percentage-purchase-boleto-enabled'
                                                >
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <br>

                                        {{-- INPUT PERCENTAGE BOLETO VALUE --}}
                                        <div class="form-group" style="display: none;">
                                            <label for="percentage-boleto-value"
                                                   class="font-text">% Boleto</label>
                                            <input name="value_percentage_purchase_boleto"
                                                   id="percentage-boleto-value"
                                                   type="text"
                                                   class="form-control percentage-boleto-value"
                                                   placeholder="100"
                                                   maxlength='3'>
                                        </div>
                                    </div>

                                    <div class="col-6 swicth-show-input">
                                        <div class="switch-holder">
                                            <label for="checkout"
                                                   class='mb-10'>Disparar "Purchase" ao gerar pix</label>
                                            <br>
                                            <label class="switch">
                                                <input type="checkbox"
                                                       value=""
                                                       name='percentage_purchase_pix_enabled'
                                                       class='percentage-purchase-pix-enabled'
                                                >
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                        <br>

                                        {{-- INPUT PERCENTAGE PIX VALUE --}}
                                        <div class="form-group" style="display: none;">
                                            <label for="percentage-pix-value"
                                                   class="font-text">% PIX</label>
                                            <input name="value_percentage_purchase_pix"
                                                   id="percentage-pix-pixel"
                                                   type="text"
                                                   class="form-control percentage-pix-value"
                                                   placeholder="100"
                                                   maxlength='3'>
                                        </div>
                                    </div>

                                    <div class='mb-1 col-12'>
                                        <label>Configurar Eventos do Pixel:</label>
                                    </div>
                                    <div class="row single-event-edit d-none mb-20">
                                        <select name="single-event-edit"
                                                id="single-event-edit"
                                                class="sirius-select col-8">
                                            <option value='purchase_all'
                                                    name='purchase_pix'
                                                    class='purchase-pix'>
                                                Purchase - (Todos os métodos de pagamento)
                                            </option>
                                            <option value='purchase_card'
                                                    name='purchase_card'
                                                    class='purchase-card'>
                                                Purchase - (Cartão)
                                            </option>
                                            <option value='purchase_boleto'
                                                    name='purchase_boleto'
                                                    class='purchase-boleto'>
                                                Purchase - (Boleto)
                                            </option>
                                            <option value='purchase_pix'
                                                    name='purchase_pix'
                                                    class='purchase-pix'>
                                                Purchase - (Pix)
                                            </option>
                                            <option value='checkout'
                                                    name='checkout'
                                                    class='checkout'>
                                                Entrou no Checkout - (InitiateCheckout)
                                            </option>
                                            <option value='basic_data'
                                                    name='basic_data'
                                                    class='basic-data'>
                                                Informação do cliente - (BasicDataComplete)
                                            </option>
                                            <option value='payment_info'
                                                    name='checkout'
                                                    class='checkout'>
                                                Informação de pagamento - (AddPaymentInfo)
                                            </option>
                                            <option value='delivery'
                                                    name='delivery'
                                                    class='delivery'>
                                                Informação de endereço - (DeliveryComplete)
                                            </option>
                                            <option value='coupon'
                                                    name='coupon'
                                                    class='coupon'>
                                                Cupom - (AddCouponDiscount)
                                            </option>
                                            <option value='upsell'
                                                    name='upsell'
                                                    class='upsell'>
                                                Entrou no Upsell - (InitiateUpsell)
                                            </option>
                                            <option value='purchase-upsell'
                                                    name='purchase-upsell'
                                                    class='purchase-upsell'>
                                                Purchase - (Upsell - UpsellPurchase)
                                            </option>
                                        </select>
                                        <div class="col-4">
                                            <div class="switch-holder flex-column align-items-start">
                                                <label for="send_value-edit"
                                                       class='mb-10'>
                                                    <span>Disparar valor com evento</span>
                                                </label>
                                                <label class="switch">
                                                    <input id="send_value_edit"
                                                           type="checkbox"
                                                           value=""
                                                           name='send_value-edit'
                                                           class='send-value-edit'>
                                                    <span id="send_value_switch-edit"
                                                          class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="multiple-event-edit row mb-20">
                                            <div class="col-3">
                                                <div class="switch-holder">
                                                    <label for="checkout"
                                                           class='mb-10'>Initiate Checkout<br>(checkout)</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                               value=""
                                                               name='checkout-edit'
                                                               class='checkout-edit'
                                                               checked>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="switch-holder">
                                                    <label for="purchase_card">Purchase<br>(cartão)</label>
                                                    <br>
                                                    <label class='switch'>
                                                        <input type="checkbox"
                                                               value=""
                                                               name='purchase_card-edit'
                                                               class='purchase-card-edit'
                                                               checked>
                                                        <span class='slider round'></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="switch-holder">
                                                    <label for="purchase_boleto">Purchase<br>(boleto)</label>
                                                    <label class='switch'>
                                                        <input type="checkbox"
                                                               value=""
                                                               name='purchase_boleto-edit'
                                                               class='purchase-boleto-edit'
                                                               checked>
                                                        <span class='slider round'></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="switch-holder">
                                                    <label for="pix">Purchase<br>(pix)</label>
                                                    <label class='switch'>
                                                        <input type="checkbox"
                                                               value=""
                                                               name='purchase_pix-edit'
                                                               class='purchase-pix-edit'
                                                               checked>
                                                        <span class='slider round'></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row w-full justify-content-center">
                <button
                   class="btn btn-lg btn-default mr-4"
                   data-dismiss="modal"
                   aria-label="Close">
                    Fechar
                </button>

                <button type="button"
                        class="btn btn-lg btn-success"
                        id="btn-update-pixel">
                    Atualizar
                </button>
            </div>
        </div>
    </div>
</div>
