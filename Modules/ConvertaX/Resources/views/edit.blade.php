<form id='form_update_integration'
      method="post"
      action="#"
      style="display:none">
    <input type='hidden'
           id='integration_id'
           value='' />
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="select_projects_edit">Selecione sua loja</label>
                    <select id="select_projects_edit" name="project_id" disabled>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">Link</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="link"
                           id="link_edit"
                           placeholder="Digite o link"
                           value=''>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="value">Valor</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="value"
                           id="value_edit"
                           placeholder="Digite o valor"
                           value=''>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token"
                           class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        {{-- <input type="checkbox" @if ($integration->boleto_generated == '1') value="1" checked="" @else value="0" @endif name="boleto_generated" id="boleto_generated" class='check'> --}}
                        <input type="checkbox"
                               name="boleto_generated"
                               id="boleto_generated_edit"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token"
                           class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="boleto_paid"
                               id="boleto_paid_edit"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token"
                           class='mb-10'>Cartão de crédito pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="credit_card_paid"
                               id="credit_card_paid_edit"
                               class='check'
                               value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token"
                           class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="abandoned_cart"
                               id="abandoned_cart_edit"
                               class='check'
                               value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token"
                           class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="credit_card_refused"
                               id="credit_card_refused_edit"
                               class='check'
                               value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
