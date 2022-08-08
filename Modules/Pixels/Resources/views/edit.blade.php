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
                    style="color:#787878; font: normal normal bold 22px Muli;">
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
                           style="font: normal normal bold 16px Muli;color: #41DC8F;margin-bottom: 0;">Ativo</label>
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
                             data-plugin="matchHeight">
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
                                    <div class="form-group col-12 url_facebook_api_div_edit"
                                         style="display:none;">
                                        <label for="url_facebook_domain_edit"
                                               class="font-text">Domínio URL:</label>
                                        <input name="url_facebook_domain_edit"
                                               type="text"
                                               class="form-control url_facebook_domain_edit">
                                    </div>
                                    {{-- INPUT DESCRIPTION --}}
                                    <div class="form-group col-12">
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

                                    {{-- INPUT PERCENTAGE BOLETO VALUE --}}
                                    <div class="form-group col-3">
                                        <label for="percentage-boleto-value-edit"
                                               class="font-text">% Boleto</label>
                                        <input name="value_percentage_purchase_boleto"
                                               id="percentage-boleto-value-edit"
                                               type="text"
                                               class="form-control percentage-boleto-value-edit"
                                               placeholder="100"
                                               maxlength='3'>
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

                                    <div class="multiple-event-edit row mx-0 mb-20">
                                        <div class="col-3">
                                            <div class="switch-holder">
                                                <label for="checkout"
                                                       class='mb-10'>Checkout<br>(venda)</label>
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
                                        {{-- <div class="row mx-0 justify-content-between mb-20"> --}}
                                        {{-- <div class="row col-6"> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Entrou no Checkout</span> --}}
                                        {{-- <br>(InitiateCheckout)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='checkout' class='checkout-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Informação do cliente</span> --}}
                                        {{-- <br>(BasicDataComplete)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='basic_data' class='basic-data-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Informação de pagamento</span> --}}
                                        {{-- <br>(AddPaymentInfo)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='checkout' class='checkout-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Informação de endereço</span> --}}
                                        {{-- <br>(DeliveryComplete)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='delivery' class='delivery-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Cupom</span> --}}
                                        {{-- <br>(AddCouponDiscount)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='coupon' class='coupon-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="row col-6"> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="purchase_card"> --}}
                                        {{-- <span>Purchase</span> --}}
                                        {{-- <br>(Cartão - Purchase)</label> --}}
                                        {{-- <label class='switch'> --}}
                                        {{-- <input type="checkbox" value="" name='purchase_card' class='purchase-card-edit' checked> --}}
                                        {{-- <span class='slider round'></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="purchase_boleto"> --}}
                                        {{-- <span>Purchase</span> --}}
                                        {{-- <br>(Boleto - Purchase)</label> --}}
                                        {{-- <label class='switch'> --}}
                                        {{-- <input type="checkbox" value="" name='purchase_boleto' class='purchase-boleto-edit' checked> --}}
                                        {{-- <span class='slider round'></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="pix"> --}}
                                        {{-- <span>Purchase</span> --}}
                                        {{-- <br>(Pix - Purchase)</label> --}}
                                        {{-- <label class='switch'> --}}
                                        {{-- <input type="checkbox" value="" name='purchase_pix' class='purchase-pix-edit' checked> --}}
                                        {{-- <span class='slider round'></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="checkout" class='mb-10'> --}}
                                        {{-- <span>Entrou no Upsell</span> --}}
                                        {{-- <br>(InitiateUpsell)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='upsell' class='upsell-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- <div class="col-12"> --}}
                                        {{-- <div class="switch-holder"> --}}
                                        {{-- <label for="pix"> --}}
                                        {{-- <span>Purchase</span> --}}
                                        {{-- <br>(Upsell - UpsellPurchase)</label> --}}
                                        {{-- <label class="switch"> --}}
                                        {{-- <input type="checkbox" value="" name='purchase-upsell' class='purchase-upsell-edit' checked> --}}
                                        {{-- <span class="slider round"></span> --}}
                                        {{-- </label> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                        {{-- </div> --}}
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 row">
            <div class="col-4">
                <a id="btn-mobile-modal-close"
                   class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none"
                   style='color:white'
                   role="button"
                   data-dismiss="modal"
                   aria-label="Close">
                    Fechar
                </a>
            </div>
            <div class="col-4 text-center">
                <button type="button"
                        class="btn btn-success"
                        id="btn-update-pixel"
                        style="padding: 15px 50px;">
                    Atualizar
                </button>
            </div>
            <div class="col-4"></div>
        </div>
    </div>
</div>
