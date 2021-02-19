<form id="form-update-pixel">
    @csrf
    <input type="hidden" value="" name="id" class="pixel-id">
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width: 100%">
                <div class="row">
                    <div class="form-group col-xl-12 mt-4">
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
                <label for="code">Código</label>
                <div class="input-group mb-3">
                    <div class='input-group-prepend'>
                        <span class='input-group-text' id='input-code-pixel-edit'
                              style='background:#f3f3f3;display:none'></span>
                    </div>
                    <input value="" name="code" id='code-pixel' type="text" class="form-control pixel-code"
                           placeholder="52342343245553" maxlength='100' aria-describedby="input-code-pixel-edit">
                </div>
                <div class="row" id="meta-tag-facebook" style="display:none;">
                    <div class="form-group col-12 my-20">
                        <a class="facebook-meta-tag-tooltip" data-html="true" data-toggle="tooltip" title="<img src='https://cloudfox-documents.s3.amazonaws.com/cloudfox/defaults/meta-tag-facebook' />">
                            <i class="ml-2 icon wb-info-circle"  aria-hidden="true"></i>
                        </a>
                        <label for="code_meta_tag_facebook">Meta-tag verificação do facebook</label>
                        <input name="code_meta_tag_facebook" type="text" id='code_meta_tag_facebook' class="form-control pixel-code-meta-tag-facebook"
                               placeholder="pi89g6zc6ci1wywhdekrw7hy1c1jc7" maxlength='255' aria-describedby="input-code-pixel">
                    </div>
                </div>
                {{-- INPUT NAME PURCHASE EVENT TABOOLA --}}
                <div class="row purchase-event-name-div" style="display:none;">
                    <div class="form-group col-12 my-20">
                        <label for="purchase-event-name">Nome Evento Conversão </label>
                        <input name="purchase-event-name" type="text"
                               class="form-control pixel-code purchase-event-name"
                               placeholder="Purchase" maxlength='255' aria-describedby="purchase-event-name">
                    </div>
                </div>
                {{-- END INPUT NAME PURCHASE EVENT TABOOLA --}}
                <div class="row">
                    <div class='form-group col-12'>
                        <label for="edit_pixel_plans">Executar no(s) plano(s)</label>
                        <select name="edit_pixel_plans[]" id="edit_pixel_plans"
                                class="apply_plans js-states form-control"
                                style='width:100%' data-plugin="select2" multiple='multiple'> </select>
                    </div>
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


