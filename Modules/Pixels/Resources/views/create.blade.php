<div style="display: block; width: 100%;"
     id="select-platform-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10">
            <h4 class="col-12 modal-title text-center"
                id="modal-title"
                style="color:#787878; font: normal normal bold 22px Inter;">
                Novo pixel
            </h4>
            <a id="modal-button-close"
               class="pointer close"
               role="button"
               data-dismiss="modal"
               aria-label="Close">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div class="modal-body">
            <div style='min-height: 100px'>
                <h4 class="col-12 modal-title text-center mb-15"
                    style="color:#787878">Selecione a plataforma</h4>
                <div class="row text-center">
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/analytics.png"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="google_analytics"
                             alt="logo analytics">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Google Analytics</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/old-analytics.png"
                             class="rounded-circle img-fluid  logo-pixels logo-pixels-create pointer"
                             data-value="google_analytics_four"
                             alt="logo analytics four">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Google Analytics 4</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/google-ads.png"
                             class="rounded-circle img-fluid  logo-pixels logo-pixels-create pointer"
                             data-value="google_adwords"
                             alt="logo google adwords">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Google Ads</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/facebook.png"
                             class="rounded-circle img-fluid  logo-pixels logo-pixels-create pointer"
                             data-value="facebook"
                             alt="logo facebook">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Facebook</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/outbrain.png"
                             class="rounded-circle img-fluid  logo-pixels logo-pixels-create pointer"
                             data-value="outbrain"
                             alt="logo outbrain">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Outbrain</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-001/taboola.png"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="taboola"
                             alt="logo taboola">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Taboola</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-001/pinterest"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="pinterest"
                             alt="logo pinterest">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Pinterest</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-001/uol-ads.png"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="uol_ads"
                             alt="logo uol ads">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">UOL Ads</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-001/tiktok"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="tiktok"
                             alt="logo uol ads">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">TikTok</div>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://nexuspay-digital-products.s3.amazonaws.com/admin/admin-001/kwai"
                             class="rounded-circle img-fluid logo-pixels logo-pixels-create pointer"
                             data-value="kwai"
                             alt="logo kwai ads">
                        <div class="mt-10"
                             style="font: normal normal normal 11px Inter;">Kwai</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>

<div style="display: none;"
     id="configure-new-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10 align-items-center">
            <div class="col-2"
                 data-toggle="tooltip"
                 data-placement="top"
                 title="Clique para alterar o pixel">
                <img src=""
                     class="img-logo img-fluid img-selected pointer"
                     alt="image selected">
            </div>
            <div class="col-7"
                 style="border-left: 1px solid #70707040; border-right: 1px solid #70707040">
                <h4 class="col-12 modal-title text-center"
                    id="modal-title"
                    style="color:#787878; font: normal normal bold 22px Inter;">
                    Cadastrar pixel
                </h4>
            </div>
            <div class="col-3">
                <div class="switch-holder d-flex align-items-center">
                    <label class='switch'>
                        <input type="checkbox"
                               value="true"
                               name='status'
                               class='check pixel-status'
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
                                    <div class="form-group col-6"
                                         id="select-facebook-integration"
                                         style="display:none;">
                                        <label class="font-text">Tipo</label><br>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio"
                                                   name="api-facebook"
                                                   value="default"
                                                   class="col-md-2 form-check-input select-default-facebook"
                                                   checked>
                                            Padrão
                                        </label>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio"
                                                   name="api-facebook"
                                                   value="api"
                                                   class="col-md-2 form-check-input select-api-facebook">
                                            API
                                        </label>
                                    </div>
                                    {{-- INPUT TOKEN FACEBOOK --}}
                                    <div class="form-group col-6"
                                         id="div-facebook-token-api"
                                         style="display:none;">
                                        <label class="font-text">Token Acesso API Conversões</label>
                                        <input name="facebook-token-api"
                                               type="text"
                                               id='facebook-token-api'
                                               class="form-control pixel-code"
                                               placeholder="Token"
                                               maxlength='255'
                                               readonly>
                                    </div>
                                    {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                                    <div class="form-group col-6 purchase-event-name-div"
                                         style="display:none;">
                                        <label for="purchase-event-name"
                                               class="form-control-label">
                                            Nome Evento Conversão
                                        </label>
                                        <input name="purchase-event-name"
                                               type="text"
                                               id='purchase-event-name'
                                               class="form-control pixel-code"
                                               placeholder="Purchase"
                                               maxlength='255'>
                                    </div>
                                    {{-- INPUT URL FACEBOOK API --}}
{{--                                    <div class="form-group col-12 url_facebook_api_div"--}}
{{--                                         style="display:none;">--}}
{{--                                        <label for="url_facebook_domain"--}}
{{--                                               class="font-text">Domínio URL:</label>--}}
{{--                                        <input name="url_facebook_domain"--}}
{{--                                               type="text"--}}
{{--                                               class="form-control url_facebook_domain">--}}
{{--                                    </div>--}}
                                    {{-- INPUT DESCRIPTION --}}
                                    <div class="form-group col-6">
                                        <label for="name"
                                               class="font-text">Descrição</label>
                                        <input name="name"
                                               type="text"
                                               class="form-control description"
                                               placeholder="Descrição"
                                               maxlength='30'>
                                    </div>
                                    {{-- INPUT CODE PIXEL --}}
                                    <div class="form-group col-6">
                                        <label id=""
                                               for="code-pixel"
                                               class="font-text">Código</label>
                                        <div class="input-group">
                                            <span class='input-group-text'
                                                  id='input-code-pixel'
                                                  style='background:#f3f3f3;display:none'>
                                            </span>
                                            <input type="text"
                                                   class="form-control pixel-code"
                                                   name="code"
                                                   id="code-pixel"
                                                   placeholder="52342343245553"
                                                   maxlength="255">
                                        </div>
                                    </div>
                                    {{-- INPUT CONVERSIONAL LABEL --}}
                                    <div id="conversional-pixel"
                                         class="form-group col-6"
                                         style="display: none">
                                        <label id=""
                                               for="conversional-pixel-code"
                                               class="font-text">Rótulo de conversão</label>
                                        <div class="input-group">
                                            <span class='input-group-text'
                                                  id='input-conversional-pixel'
                                                  style='background:#f3f3f3;'>/
                                            </span>
                                            <input type="text"
                                                   class="form-control conversional-code"
                                                   name="conversional"
                                                   id="conversional-pixel-code"
                                                   placeholder="AN7162ASNSG"
                                                   maxlength="255">
                                        </div>
                                    </div>

                                    {{-- INPUT SELECT PLANS --}}
                                    <div class='form-group col-12'>
                                        <label for="add_pixel_plans"
                                               class="form-control-label">
                                            Plano(s) que executarão o pixel
                                        </label>
                                        <select name="add_pixel_plans[]"
                                                id="add_pixel_plans"
                                                class="js-states form-control"
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

                                    <div class="row single-event d-none mb-20">
                                        <select name="single-event"
                                                id="single-event"
                                                class="sirius-select col-8">
                                            <option value='purchase_all'
                                                    name='purchase_pix'
                                                    class='purchase-pix'
                                                    selected>
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
                                                <label for="send_value"
                                                       class='mb-10'>
                                                    <span>Disparar valor com evento</span>
                                                </label>
                                                <label class="switch">
                                                    <input id="send_value"
                                                           type="checkbox"
                                                           value=""
                                                           name='send_value'
                                                           class='send-value'>
                                                    <span id="send_value_switch"
                                                          class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="multiple-event row mb-20">
                                            <div class="col-3">
                                                <div class="switch-holder">
                                                    <label for="checkout"
                                                           class='mb-10'>Initiate Checkout<br>(checkout)</label>
                                                    <br>
                                                    <label class="switch">
                                                        <input type="checkbox"
                                                               value=""
                                                               name='checkout'
                                                               class='checkout'
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
                                                               name='purchase_card'
                                                               class='purchase-card'
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
                                                               name='purchase_boleto'
                                                               class='purchase-boleto'
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
                                                               name='purchase_pix'
                                                               class='purchase-pix'
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

        <div class="modal-footer justify-content-center">
            <button type="button"
                    class="btn btn-success"
                    id="btn-store-pixel"
                    style="padding: 15px 50px;">Confirmar
            </button>
        </div>
    </div>
</div>
