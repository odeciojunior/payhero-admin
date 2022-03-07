@push('css')
    <link rel="stylesheet" href="{!! mix('modules/pixels/css/pixel-edit.min.css') !!}">
@endpush
<div style="display: none; width: 100%;" id="select-platform-edit-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10">
            <h4 class="col-12 modal-title text-center" id="modal-title"
                style="color:#787878; font: normal normal bold 22px Muli;">
                Editar Pixel
            </h4>
            <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div style='min-height: 100px'>
            <div>
                <h4 class="col-12 modal-title text-center mb-15" style="color:#787878">Selecione a plataforma</h4>
                <div class="row text-center">
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/analytics"
                            class="rounded-circle img-fluid logo-pixels logo-pixels-create"
                            data-value="google_analytics"
                            alt="logo analytics">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Google Analytics</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/old-analytics"
                            class="rounded-circle img-fluid  logo-pixels logo-pixels-create"
                            data-value="google_analytics_four"
                            alt="logo analytics four">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Google Analytics 4</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/google-ads"
                            class="rounded-circle img-fluid  logo-pixels logo-pixels-create"
                            data-value="google_adwords"
                            alt="logo google adwords">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Google Ads</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/facebook"
                            class="rounded-circle img-fluid  logo-pixels logo-pixels-create"
                            data-value="facebook"
                            alt="logo facebook">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Facebook</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/outbrain"
                            class="rounded-circle img-fluid  logo-pixels logo-pixels-create"
                            data-value="outbrain"
                            alt="logo outbrain">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Outbrain</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/taboola"
                            class="rounded-circle img-fluid logo-pixels logo-pixels-create"
                            data-value="taboola"
                            alt="logo taboola">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Taboola</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/pixel/pinterest"
                            class="rounded-circle img-fluid logo-pixels logo-pixels-create"
                            data-value="pinterest"
                            alt="logo pinterest">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">Pinterest</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/uol-ads"
                            class="rounded-circle img-fluid logo-pixels logo-pixels-create"
                            data-value="uol_ads"
                            alt="logo uol ads">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">UOL Ads</div>
                    </div>
                    <div class="col-lg-4 col-6 text-center mb-30">
                        <img src="https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/tiktok"
                            class="rounded-circle img-fluid logo-pixels logo-pixels-create"
                            data-value="tiktok"
                            alt="logo uol ads">
                        <div class="mt-10" style="font: normal normal normal 11px Muli;">TikTok</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>

<div style="display: block;" id="configure-edit-pixel">
    <div class="modal-content p-10 s-border-radius">
        <div class="modal-header simple-border-bottom mb-10 align-items-center">
            <div class="col-2" data-toggle="tooltip" data-placement="top" title="Clique para alterar o pixel">
                <img src="" class="img-logo img-fluid img-edit-selected pointer" alt="image selected">
            </div>
            <div class="col-7" style="border-left: 1px solid #70707040; border-right: 1px solid #70707040">
                <h4 class="col-12 modal-title text-center" id="modal-title"
                    style="color:#787878; font: normal normal bold 22px Muli;">
                    Editar Pixel
                </h4>
            </div>
            <div class="col-3">
                <div class="switch-holder d-flex align-items-center">
                    <label class='switch'>
                        <input type="checkbox" value="" name='status' class='check status-edit' checked>
                        <span class='slider round'></span>
                    </label>
                    <label for="boleto" style="font: normal normal bold 16px Muli;color: #41DC8F;margin-bottom: 0;">Ativo</label>
                </div>
            </div>

            <a id="modal-button-close" class="pointer close" role="button" data-dismiss="modal" aria-label="Close" style="position: absolute;right: 45px;top: 25px;">
                <i class="material-icons md-16">close</i>
            </a>
        </div>
        <div class="modal-body">
            <div class="row">
                <form id='form-register-pixel'>
                    @csrf
                    <input type="hidden" name="platform" id="platform" value="">
                    <div class="container-fluid">
                        <div class="panel" data-plugin="matchHeight">
                            <div style="width:100%">
                                <div class="row">
                                    {{-- INPUT RADIO FACEBOOK-API --}}
                                    <input type="hidden" class="platform-edit" value="">
                                    <div class="form-group col-6" id="select-facebook-integration-edit" style="display:none;">
                                        <label class="font-text">Tipo</label><br>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio" name="api-facebook" value="default" class="col-md-2 form-check-input facebook-api-default-edit" checked>
                                            Padrão
                                        </label>
                                        <label class="col-md-5 form-check-label">
                                            <input type="radio" name="api-facebook" value="api" class="col-md-2 form-check-input facebook-api-edit">
                                            API
                                        </label>
                                    </div>
                                    {{-- INPUT TOKEN FACEBOOK--}}
                                    <div class="form-group col-6" id="div-facebook-token-api-edit" style="display:none;">
                                        <label class="font-text">Token Acesso API Conversões</label>
                                        <input name="facebook-token-api-edit" type="text" id='facebook-token-api-edit' class="form-control pixel-code" placeholder="Token" maxlength='255' readonly>
                                    </div>

                                    {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                                    <div class="form-group col-6 div-purchase-event-name-edit" style="display:none;">
                                        <label for="purchase-event-name" class="form-control-label">
                                            Nome Evento Conversão
                                        </label>
                                        <input name="purchase-event-name" type="text" id='purchase-event-name' class="form-control input-purchase-event-name-edit" placeholder="Purchase" maxlength='255'>
                                    </div>
                                    {{-- INPUT URL FACEBOOK API --}}
                                    <div class="form-group col-12 url_facebook_api_div_edit" style="display:none;">
                                        <label for="url_facebook_domain_edit" class="font-text">Domínio URL:</label>
                                        <input name="url_facebook_domain_edit" type="text" class="form-control url_facebook_domain_edit">
                                    </div>
                                    {{-- INPUT DESCRIPTION--}}
                                    <div class="form-group col-12">
                                        <label for="name" class="font-text">Descrição</label>
                                        <input name="name" type="text" class="form-control description-edit" placeholder="Descrição" maxlength='30'>
                                    </div>

                                    {{-- INPUT CODE PIXEL --}}
                                    <div class="form-group col-6">
                                        <label id="code-pixel-label" for="code-pixel" class="font-text">Código</label>
                                        <div class="input-group">
                                            <span class='input-group-text' id='text-type-code-edit' style='background:#f3f3f3;display:none'>
                                            </span>
                                            <input type="text" class="form-control code-edit" name="code" placeholder="52342343245553" maxlength="255">
                                        </div>
                                    </div>

                                    {{-- INPUT CONVERSIONAL LABEL --}}
                                    <div id="conversional-pixel-edit" class="form-group col-6" style="display: none">
                                        <label id="" for="conversional-pixel-code" class="font-text">Rótulo de conversão</label>
                                        <div class="input-group">
                                            <span class='input-group-text' id='text-type-conversional-edit' style='background:#f3f3f3;'>/
                                            </span>
                                            <input type="text" class="form-control conversional-edit" name="conversional" id="conversional-pixel-code" placeholder="AN7162ASNSG" maxlength="255">
                                        </div>
                                    </div>

                                    {{-- INPUT PERCENTAGE BOLETO VALUE --}}
                                    <div class="form-group col-3">
                                        <label for="percentage-boleto-value-edit" class="font-text">% Boleto</label>
                                        <input name="value_percentage_purchase_boleto" id="percentage-boleto-value-edit" type="text" class="form-control percentage-boleto-value-edit" placeholder="100" maxlength='3'>
                                    </div>
                                    <div class='form-group col-12'>
                                        <label for="edit-plans" class="form-control-label">
                                            Plano(s) que executarão o pixel
                                        </label>
                                        <select name="edit_plans[]" class="js-states form-control edit-plans apply_plans" style='width:100%' data-plugin="select2" multiple='multiple'>
                                            <option value='all'>Todos</option>
                                        </select>
                                    </div>

                                    <div class='mb-1 col-12'>
                                        <label>Rodar Pixel:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="switch-holder">
                                            <label for="checkout" class='mb-10'>Checkout<br>(venda)</label>
                                            <br>
                                            <label class="switch">
                                                <input type="checkbox" value="" name='checkout' class='checkout-edit' checked>
                                                <span class="slider round"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="switch-holder">
                                            <label for="purchase_card">Purchase<br>(cartão)</label>
                                            <br>
                                            <label class='switch'>
                                                <input type="checkbox" value="" name='purchase_card' class='purchase-card-edit' checked>
                                                <span class='slider round'></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="switch-holder">
                                            <label for="purchase_boleto">Purchase<br>(boleto)</label>
                                            <label class='switch'>
                                                <input type="checkbox" value="" name='purchase_boleto' class='purchase-boleto-edit' checked>
                                                <span class='slider round'></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="switch-holder">
                                            <label for="pix">Purchase<br>(pix)</label>
                                            <label class='switch'>
                                                <input type="checkbox" value="" name='purchase_pix' class='purchase-pix-edit' checked>
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
            <div class="col-4">
                <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
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
