<div style="display: block" id="select-platform-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10">
            <h4 class="col-12 modal-title text-center" id="modal-title"
                style="color:#787878; font: normal normal bold 22px Muli;">
                Novo pixel
            </h4>
            <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal"
               aria-label="Close">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div style='min-height: 100px'>
            <div>
                <h4 class="col-12 modal-title text-center mb-15" style="color:#787878">Selecione a
                    plataforma</h4>
                <div class="row text-center">
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/analytics.png')}}" class="rounded-circle img-fluid logo-pixels"
                             data-value="google_analytics"
                             alt="logo analytics">
                        <div class="" style="font: normal normal normal 11px Muli;">Google Analytics</div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/old-analytics.png')}}"
                             class="rounded-circle img-fluid  logo-pixels"
                             data-value="google_analytics_four"
                             alt="logo analytics four">
                        <div style="font: normal normal normal 11px Muli;">Google Analytics 4</div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/google-ads.png')}}"
                             class="rounded-circle img-fluid  logo-pixels"
                             data-value="google_adwords"
                             alt="logo google adwords">
                        <div style="font: normal normal normal 11px Muli;">Google Adwords</div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/facebook.png')}}" class="rounded-circle img-fluid  logo-pixels"
                             data-value="facebook"
                             alt="logo facebook">
                        <div style="font: normal normal normal 11px Muli;">Facebook</div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/outbrain.png')}}" class="rounded-circle img-fluid  logo-pixels"
                             data-value="outbrain"
                             alt="logo outbrain">
                        <div style="font: normal normal normal 11px Muli;">Outbrain</div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <img src="{{asset('pixel/taboola.png')}}" class="rounded-circle img-fluid logo-pixels"
                             data-value="taboola"
                             alt="logo taboola">
                        <div style="font: normal normal normal 11px Muli;">Taboola</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>


<div style="display: none;" id="configure-new-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10 align-items-center">
            <div class="col-2" data-toggle="tooltip" data-placement="top" title="Clique para alterar o pixel">
                <img src="" class="img-logo img-fluid img-selected pointer" alt="image selected">
                <input type="hidden" name="platform" id="platform" value="">
            </div>
            <div class="col-7"
                 style="border-left: 1px solid #70707040; border-right: 1px solid #70707040">
                <h4 class="col-12 modal-title text-center" id="modal-title"
                    style="color:#787878; font: normal normal bold 22px Muli;">
                    Cadastrar pixel
                </h4>
            </div>
            <div class="col-3">
                <div class="switch-holder d-flex align-items-center">
                    <label class='switch'>
                        <input type="checkbox" value="" name='status'
                               class='check pixel-status' checked>
                        <span class='slider round'></span>
                    </label>
                    <label for="boleto" style="font: normal normal bold 16px Muli;color: #41DC8F;margin-bottom: 0;">Ativo</label>
                </div>
            </div>

            <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal"
               aria-label="Close" style="position: absolute;right: 45px;top: 25px;">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div class="modal-body">
            <div class="row">
                <form id='form-register-pixel'>
                    @csrf
                    <div class="container-fluid">
                        <div class="panel" data-plugin="matchHeight">
                            <div style="width:100%">
                                <div class="row">
                                    {{-- INPUT RADIO FACEBOOK-API --}}
                                    <div class="form-group col-6" id="select-facebook-integration"
                                         style="display:none;">
                                        <label class="font-text">Tipo</label><br>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio" name="api-facebook" value="default"
                                                   class="col-md-2 form-check-input"
                                                   checked>
                                            Padrão
                                        </label>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio" name="api-facebook" value="api"
                                                   class="col-md-2 form-check-input">
                                            API
                                        </label>
                                    </div>
                                    {{-- INPUT TOKEN FACEBOOK--}}
                                    <div class="form-group col-6" id="div-facebook-token-api" style="display:none;">
                                        <label class="font-text">Token Acesso API Conversões</label>
                                        <input name="facebook-token-api"
                                               type="text"
                                               id='facebook-token-api'
                                               class="form-control pixel-code"
                                               placeholder="Token" maxlength='255'
                                               readonly
                                        >
                                    </div>

                                    {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                                    <div class="form-group col-6 purchase-event-name-div" style="display:none;">
                                        <label for="purchase-event-name" class="form-control-label">
                                            Nome Evento Conversão
                                        </label>
                                        <input name="purchase-event-name"
                                               type="text"
                                               id='purchase-event-name'
                                               class="form-control pixel-code"
                                               placeholder="Purchase"
                                               maxlength='255'>
                                    </div>

                                    {{-- INPUT DESCRIPTION--}}
                                    <div class="form-group col-12">
                                        <label for="name" class="font-text">Descrição</label>
                                        <input name="name"
                                               type="text"
                                               class="form-control description"
                                               placeholder="Descrição"
                                               maxlength='30'>
                                    </div>

                                    {{-- INPUT CODE PIXEL --}}
                                    <div class="form-group col-6">
                                        <label for="code-pixel" class="font-text">Código</label>
                                        <div class="input-group">
                                        <span class='input-group-text' id='input-code-pixel'
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

                                    {{-- INPUT PERCENTAGE BOLETO VALUE --}}
                                    <div class="form-group col-3">
                                        <label for="percentage-value" class="font-text">% Boleto</label>
                                        <input name="value_percentage_purchase_boleto"
                                               id="percentage-value"
                                               type="text"
                                               class="form-control"
                                               placeholder="100" maxlength='3'>
                                    </div>

                                    <div class='form-group col-12'>
                                        <label for="add_pixel_plans" class="form-control-label">
                                            Plano(s) que executarão o pixel
                                        </label>
                                        <select
                                                name="add_pixel_plans[]"
                                                id="add_pixel_plans"
                                                class="js-states form-control"
                                                style='width:100%'
                                                data-plugin="select2"
                                                multiple='multiple'>
                                            <option value='all'>Todos</option>
                                        </select>
                                    </div>

                                    <div class='mb-1 col-12'>
                                        <label>Rodar Pixel:</label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="switch-holder">
                                            <label for="checkout" class='mb-10'>Checkout</label>
                                            <br>
                                            <label class="switch">
                                                <input type="checkbox" value="" name='checkout'
                                                       class='check pixel-checkout' checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="switch-holder">
                                            <label for="cartao">Purchase (cartão)</label>
                                            <br>
                                            <label class='switch'>
                                                <input type="checkbox" value="" name='purchase_card'
                                                       class='check pixel-purchase-card'
                                                       checked>
                                                <span class='slider round'></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="switch-holder">
                                            <label for="boleto">Purchase (boleto)</label>
                                            <label class='switch'>
                                                <input type="checkbox" value="" name='purchase_boleto'
                                                       class='check pixel-purchase-boleto' checked>
                                                <span class='slider round'></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 row">
            <div class="col-4"></div>
            <div class="col-4 text-center">
                <button type="button" class="btn btn-success" id="btn-store-pixel"
                style="padding: 15px 50px;">Confirmar</button>
            </div>
            <div class="col-4"></div>
        </div>

    </div>
</div>

{{--<form id='form-register-pixel'>--}}
{{--    @csrf--}}
{{--    <div class="container-fluid">--}}
{{--        <div class="panel" data-plugin="matchHeight">--}}
{{--            <div style="width:100%">--}}
{{--                <div class="row">--}}
{{--                    <div class="form-group col-12 mt-4">--}}
{{--                        <label for="name">Descrição</label>--}}
{{--                        <input name="name" type="text" class="form-control pixel-description" placeholder="Descrição"--}}
{{--                               maxlength='30'>--}}
{{--                    </div>--}}
{{--                    <div class="form-group col-6">--}}
{{--                        <label for="platform">Plataforma</label>--}}
{{--                        <select name="platform" type="text" id='select-platform' class="form-control pixel-platform">--}}
{{--                            <option value="facebook">Facebook</option>--}}
{{--                            <option value="google_adwords">Google Adwords</option>--}}
{{--                            <option value="google_analytics">Google Analytics</option>--}}
{{--                            <option value="google_analytics_four">Google Analytics 4.0</option>--}}
{{--                            <option value="taboola">Taboola</option>--}}
{{--                            <option value="outbrain">Outbrain</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                    <div class="form-group col-6">--}}
{{--                        <label for="status">Status</label>--}}
{{--                        <select name="status" type="text" class="form-control pixel-status">--}}
{{--                            <option value="1">Ativo</option>--}}
{{--                            <option value="0">Desativado</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                --}}{{-- INPUT RADIO FACEBOOK-API --}}
{{--                <div class="form-group col-md-6" id="api-facebook" style="display:none;">--}}
{{--                    <label class="col-md-5 form-check-label">--}}
{{--                        <input type="radio" name="api-facebook" value="default" class="col-md-2 form-check-input"--}}
{{--                               checked>--}}
{{--                        Padrão--}}
{{--                    </label>--}}
{{--                    <label class="col-md-5 form-check-label">--}}
{{--                        <input type="radio" name="api-facebook" value="api" class="col-md-2 form-check-input">--}}
{{--                        API--}}
{{--                    </label>--}}
{{--                </div>--}}

{{--                <div class="form-row">--}}
{{--                    --}}{{-- INPUT CODE PIXEL --}}
{{--                    <div class="form-group col-md-9">--}}
{{--                        <label for="code-pixel" class="form-control-label">Código</label>--}}
{{--                        <div class="input-group">--}}
{{--                            <span class='input-group-text' id='input-code-pixel'--}}
{{--                                  style='background:#f3f3f3;display:none'>--}}
{{--                            </span>--}}
{{--                            <input type="text" class="form-control pixel-code" name="code" id="code-pixel"--}}
{{--                                   placeholder="52342343245553" maxlength="255">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    --}}{{-- INPUT PERCENTAGE BOLETO VALUE --}}
{{--                    <div class="form-group col-md-3 div-percentage-value-boleto">--}}
{{--                        <label for="percentage-value" class="form-control-label">% Valor Boleto</label>--}}
{{--                        <input name="value_percentage_purchase_boleto" id="percentage-value" type="text"--}}
{{--                               class="form-control"--}}
{{--                               placeholder="100" maxlength='3'>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                --}}{{-- INPUT TOKEN FACEBOOK --}}
{{--                <div class="form-group" id="div-facebook-token-api" style="display:none;">--}}
{{--                    <label for="facebook-token-api" class="form-control-label">Token Acesso API Conversões</label>--}}
{{--                    <input name="facebook-token-api" type="text" id='facebook-token-api'--}}
{{--                           class="form-control pixel-code"--}}
{{--                           placeholder="Token" maxlength='255'>--}}
{{--                </div>--}}
{{--                --}}{{-- INPUT TOKEN FACEBOOK --}}

{{--                --}}{{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
{{--                <div class="form-group purchase-event-name-div" style="display:none;">--}}
{{--                    <label for="purchase-event-name" class="form-control-label">Nome Evento Conversão </label>--}}
{{--                    <input name="purchase-event-name" type="text" id='purchase-event-name'--}}
{{--                           class="form-control pixel-code" placeholder="Purchase" maxlength='255'>--}}
{{--                </div>--}}
{{--                --}}{{-- END INPUT NAME PURCHASE EVENT TABOOLA --}}


{{--                <div class='form-group'>--}}
{{--                    <label for="add_pixel_plans" class="form-control-label">Executar no(s) plano(s)</label>--}}
{{--                    <select name="add_pixel_plans[]" id="add_pixel_plans" class="js-states form-control"--}}
{{--                            style='width:100%' data-plugin="select2" multiple='multiple'>--}}
{{--                        <option value='all'>Todos</option>--}}
{{--                    </select>--}}
{{--                </div>--}}
{{--                <div class='mb-1'>--}}
{{--                    <label>Rodar Pixel:</label>--}}
{{--                </div>--}}
{{--                <div class="row justify-content-center">--}}
{{--                    <div class="col-md-3">--}}
{{--                        <div class="switch-holder">--}}
{{--                            <label for="checkout" class='mb-10'>Checkout:</label>--}}
{{--                            <br>--}}
{{--                            <label class="switch">--}}
{{--                                <input type="checkbox" value="" name='checkout' class='check pixel-checkout' checked>--}}
{{--                                <span class="slider round"></span>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="switch-holder">--}}
{{--                            <label for="cartao">Purchase (cartão):</label>--}}
{{--                            <br>--}}
{{--                            <label class='switch'>--}}
{{--                                <input type="checkbox" value="" name='purchase_card' class='check pixel-purchase-card'--}}
{{--                                       checked>--}}
{{--                                <span class='slider round'></span>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-4">--}}
{{--                        <div class="switch-holder">--}}
{{--                            <label for="boleto">Purchase (boleto):</label>--}}
{{--                            <br>--}}
{{--                            <label class='switch'>--}}
{{--                                <input type="checkbox" value="" name='purchase_boleto'--}}
{{--                                       class='check pixel-purchase-boleto' checked>--}}
{{--                                <span class='slider round'></span>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</form>--}}
