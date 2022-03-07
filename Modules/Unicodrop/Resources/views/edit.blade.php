<input type='hidden' id='integration_id' value=''/>
<form id='form_update_integration' method="post" action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="company">Lojas</label>
                    <select class="sirius-select" id="select_projects_edit" name="project_id" disabled> </select>
                </div>
            </div>
        </div>
        <div class='row'>
            <div class='col-12'>
                <label for='email'>Token</label>
            </div>
            <div id='invite-link-select' class='input-group col-12'>
                <input name="token" type='text' class='form-control' id='inputToken' value=''>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="boleto_generated" id="boleto_generated_edit" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="boleto_paid" id="boleto_paid_edit" class='check'>
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
                        <input type="checkbox" name="credit_card_paid" id="credit_card_paid_edit" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="abandoned_cart" id="abandoned_cart_edit" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="credit_card_refused" id="credit_card_refused_edit" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="pix" class='mb-10'>PIX:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" name="pix" id="pix_edit" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
