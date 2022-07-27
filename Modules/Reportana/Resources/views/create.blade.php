<form id='form_add_integration' method="post" action="#">
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class='form-group'>
                    <label for="project_id">Selecione sua loja</label>
                    <select class="sirius-select" id="project_id" name="project_id">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_api">URL API</label>
                <div class="d-flex input-group">
                    <input type="url" class="input-pad addon" name="url_api" id="url_api" placeholder="Digite a URL de integração">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_generated" class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="boleto_generated" id="boleto_generated" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_paid" class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="boleto_paid" id="boleto_paid" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_expired" class='mb-10'>Boleto expirado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="boleto_expired" id="boleto_expired" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_generated" class='mb-10'>Pix gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="pix_generated" id="pix_generated" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_paid" class='mb-10'>Pix pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="pix_paid" id="pix_paid" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_expired" class='mb-10'>Pix expirado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="pix_expired" id="pix_expired" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="credit_card_paid" class='mb-10'>Cartão de crédito pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="credit_card_paid" id="credit_card_paid" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="credit_card_refused" class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="credit_card_refused" id="credit_card_refused" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="abandoned_cart" class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="abandoned_cart" id="abandoned_cart" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
