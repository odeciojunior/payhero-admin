<form id='form_add_integration' method="post" action="#" style="display:none">
    @csrf
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="company">Selecione seu projeto</label>
                    <select class="input-pad" id="select_projects" name="project_id">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="link">Link</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="link" id="link" placeholder="Digite o link">
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="value">Valor</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="value" id="value" placeholder="Digite o valor">
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="boleto_generated" id="boleto_generated" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="boleto_paid" id="boleto_paid" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Cartão de crédito pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="credit_card_paid" id="credit_card_paid" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="abandoned_cart" id="abandoned_cart" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-12">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="credit_card_refused" id="credit_card_refused" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
