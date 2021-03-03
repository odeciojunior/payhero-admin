<form id='form-register-pixel'>
    @csrf
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-12 mt-4">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control pixel-description" placeholder="Descrição"
                               maxlength='30'>
                    </div>
                    <div class="form-group col-6">
                        <label for="platform">Plataforma</label>
                        <select name="platform" type="text" id='select-platform' class="form-control pixel-platform">
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
                        <select name="status" type="text" class="form-control pixel-status">
                            <option value="1">Ativo</option>
                            <option value="0">Desativado</option>
                        </select>
                    </div>
                </div>
                <label for="code">Código</label>
                <div class="input-group mb-3">
                    <div class='input-group-prepend'>
                        <span class='input-group-text' id='input-code-pixel'
                              style='background:#f3f3f3;display:none'></span>
                    </div>
                    <input name="code" type="text" id='code-pixel' class="form-control pixel-code"
                           placeholder="52342343245553" maxlength='100' aria-describedby="input-code-pixel">
                </div>
                {{-- INPUT RADIO FACEBOOK-API --}}
                <div class="form-group col-md-6 row" id="api-facebook" style="display:none;">
                        <label class="col-md-5">
                            <input type="radio" name="api-facebook" value="default" class="col-md-2" checked >
                            Padrão
                        </label>
                        <label class="col-md-5">
                            <input type="radio" name="api-facebook" value="api" class="col-md-2" >
                            API
                        </label>
                </div>
                {{-- INPUT RADIO FACEBOOK-API --}}

                {{-- INPUT TOKEN FACEBOOK --}}
                <div class="row" id="div-facebook-token-api" style="display:none;">
                    <div class="form-group col-12 my-20">
                        <label for="facebook-token-api">Token Facebook</label>
                        <input name="facebook-token-api" type="text" id='facebook-token-api'
                               class="form-control pixel-code"
                               placeholder="Token" maxlength='255'>
                    </div>
                </div>
                {{-- INPUT TOKEN FACEBOOK --}}

                {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                <div class="row purchase-event-name-div" style="display:none;">
                    <div class="form-group col-12 my-20">
                        <label for="purchase-event-name">Nome Evento Conversão </label>
                        <input name="purchase-event-name" type="text" id='purchase-event-name'
                               class="form-control pixel-code"
                               placeholder="Purchase" maxlength='255' aria-describedby="purchase-event-name">
                    </div>
                </div>
                {{-- END INPUT NAME PURCHASE EVENT TABOOLA --}}


                <div class='row'>
                    <div class='form-group col-12 my-20'>
                        <label for="add_pixel_plans">Executar no(s) plano(s)</label>
                        <select name="add_pixel_plans[]" id="add_pixel_plans" class="js-states form-control"
                                style='width:100%' data-plugin="select2" multiple='multiple'>
                            <option value='all'>Todos</option>
                        </select>
                    </div>
                </div>
                <div class='mb-1'>
                    <label>Rodar Pixel:</label>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="switch-holder">
                            <label for="checkout" class='mb-10'>Checkout:</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" value="" name='checkout' class='check pixel-checkout' checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="cartao">Purchase (cartão):</label>
                            <br>
                            <label class='switch'>
                                <input type="checkbox" value="" name='purchase_card' class='check pixel-purchase-card'
                                       checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="boleto">Purchase (boleto):</label>
                            <br>
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
