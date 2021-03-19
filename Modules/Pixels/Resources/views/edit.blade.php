<form id="form-update-pixel">
    @csrf
    <input type="hidden" value="" name="id" class="pixel-id">
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width: 100%">
                <div class="row">
                    <div class="form-group col-12 mt-4">
                        <label for="name">Descrição</label>
                        <input value="" name="name" type="text" class="input-pad pixel-description"
                               placeholder="Descrição"
                               maxlength='30'>
                    </div>
                    <div class="form-group col-6">
                        <label for="platform">Plataforma</label>
                        <select name="platform" id='select-platform' type="text"
                                class="form-control select-pad pixel-platform">
                            <option value="facebook">Facebook</option>
                            <option value="google_adwords">Google Adwords</option>
                            <option value="google_analytics">Google Analytics</option>
                            <option value="google_analytics_four">Google Analytics 4.0</option>
                            <option value="taboola">Taboola</option>
                            <option value="outbrain">Outbrain</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" id="status" class="form-control select-pad pixel-status">
                            <option value="1">Ativo</option>
                            <option value="0">Desativado</option>
                        </select>
                    </div>
                </div>

                {{-- INPUT RADIO FACEBOOK-API --}}
                <div class="form-group col-md-6 row" id="api-facebook" style="display:none;">
                    <label class="col-md-5 form-check-label">
                        <input type="radio" id="default-api-facebook" name="api-facebook" value="default"
                               class="col-md-2 api-facebook-check form-check-input" checked>
                        Padrão
                    </label>
                    <label class="col-md-5 form-check-label">
                        <input type="radio" id="api-facebook" name="api-facebook" value="api"
                               class="col-md-2 api-facebook-check form-check-input">
                        API
                    </label>
                </div>

                <div class="form-row">
                    {{-- INPUT RADIO FACEBOOK-API --}}
                    <div class="form-group col-md-9">
                        <label for="code-pixel" class="form-control-label">Código</label>
                        <div class="input-group">
                            <span class='input-group-text' id='input-code-pixel-edit'
                                  style='background:#f3f3f3;display:none'></span>
                            <input value="" name="code" id='code-pixel' type="text" class="form-control pixel-code"
                                   placeholder="52342343245553" maxlength='100'
                                   aria-describedby="input-code-pixel-edit">
                        </div>
                    </div>

                    {{-- INPUT PERCENTAGE BOLETO VALUE --}}
                    <div class="form-group col-md-3 div-percentage-value-boleto">
                        <label for="percentage-value" class="form-control-label">% Valor Boleto</label>
                        <input type="text" class="form-control" name="value_percentage_purchase_boleto"
                               id="percentage-value" placeholder="100" maxlength="3">
                    </div>
                </div>

                {{-- INPUT TOKEN FACEBOOK --}}
                <div class="form-group" id="div-facebook-token-api" style="display:none;">
                    <label for="facebook-token-api">Token Acesso API Conversões</label>
                    <input name="facebook-token-api" type="text" id='facebook-token-api'
                           class="form-control pixel-code"
                           placeholder="Token" maxlength='255'>
                </div>

                {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                <div class="form-group purchase-event-name-div" style="display:none;">
                    <label for="purchase-event-name" class="form-control-label">Nome Evento Conversão </label>
                    <input name="purchase-event-name" type="text"
                           class="form-control pixel-code purchase-event-name"
                           placeholder="Purchase" maxlength='255' aria-describedby="purchase-event-name">
                </div>

                {{-- SELECT PLANS APPLY TO PIXEL --}}
                <div class='form-group'>
                    <label for="edit_pixel_plans" class="form-control-label">Executar no(s) plano(s)</label>
                    <select name="edit_pixel_plans[]" id="edit_pixel_plans"
                            class="apply_plans js-states form-control"
                            style='width:100%' data-plugin="select2" multiple='multiple'> </select>
                </div>
            </div>
            <div class='mb-1'>
                <label>Rodar Pixel:</label>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="switch-holder">
                        <label for="Checkout" class="mb-10">Checkout:</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='checkout' class='check pixel-checkout'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="switch-holder">
                        <label for="cartao">Purchase (cartão):</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='purchase_card' class='check pixel-purchase-card'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="switch-holder">
                        <label for="boleto">Purchase (boleto):</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='purchase_boleto' class='check pixel-purchase-boleto'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


