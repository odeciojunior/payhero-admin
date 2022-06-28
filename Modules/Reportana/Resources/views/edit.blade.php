<input type='hidden' id='integration_id' value=''/>
<form id='form_update_integration' method="post" action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class='form-group'>
                    <label for="select_projects_edit">Selecione sua loja</label>
                    <select class="sirius-select" id="select_projects_edit" name="project_id" disabled>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_api_edit">URL API</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="url_api" id="url_api_edit"
                           placeholder="Digite a URL de integração">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_generated" class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="boleto_generated" id="boleto_generated_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_paid" class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="boleto_paid" id="boleto_paid_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="boleto_expired_edit" class='mb-10'>Boleto expirado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="boleto_expired_edit" id="boleto_expired_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_generated_edit" class='mb-10'>Pix gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="pix_generated" id="pix_generated_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_paid_edit" class='mb-10'>Pix pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="pix_paid" id="pix_paid_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_expired_edit" class='mb-10'>Pix expirado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="pix_expired" id="pix_expired_edit" class='check'>
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
                        <input type="checkbox" name="credit_card_paid" id="credit_card_paid_edit" class='check'
                               value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="credit_card_refused" class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="credit_card_refused" id="credit_card_refused_edit" class='check'
                               value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="abandoned_cart" class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="abandoned_cart" id="abandoned_cart_edit" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
