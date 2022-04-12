<form id='form-add-shipping'>
    @csrf
    <div class='row'>
        <div class='form-group col-12'>
            <label for='type'>Tipo</label>
            <select name='type' id="shipping-type" class='sirius-select shipping-type'>
                <option value='static' selected>Frete fixo (você define um valor fixo para o frete)</option>
                <option value='pac'>PAC (Calculado automaticamente pela API)</option>
                <option value='sedex'>SEDEX (Calculado automaticamente pela API)</option>
            </select>
        </div>
    </div>
    <div class='row name-shipping-row'>
        <div class='form-group col-12'>
            <label for='name'>Descrição no checkout</label>
            <input name='name' type='text' class='input-pad shipping-description' value='' placeholder='Frete grátis' maxlength='60'>
            <span id='shipping-name-error' class='text-danger'></span>
        </div>
    </div>
    <div class='row information-shipping-row'>
        <div class='form-group col-12 mb-20'>
            <label for='information'>Tempo de entrega apresentado</label>
            <input name='information' type='text' class='input-pad shipping-info' value='' placeholder='10 até 20 dias' maxlength='100'>
            <span id='shipping-information-error' class='text-danger'></span>
        </div>
    </div>
    
    <div class='row value-shipping-row' >
        <div class='form-group col-6 mb-0' id="shipping-single-value">
            <label for='value'>Valor do Frete</label>
            <input name='value' type='text' class='input-pad shipping-value' value='' placeholder='0' maxlength='7'>
            <span id='shipping-value-error' class='text-danger'></span>
        </div>
        <div class='form-group col-6 mb-0' id="shipping-multiple-value" style="display: none">
            <div class="row">
                <div class='form-group col-12 mb-4' id="shipping-region-">
                    <label for='value'>Valor do frete para o Norte</label>
                    <input name='value1' type='text' class='input-pad shipping-value1' value='' placeholder='0' maxlength='7'>
                    <span id='shipping-value-error' class='text-danger'></span>
                </div>
            </div>
            <div class="row">
                <div class='form-group col-12 mb-4' id="shipping-region-">
                    <label for='value'>Valor do frete para o Nordeste</label>
                    <input name='value2' type='text' class='input-pad shipping-value2' value='' placeholder='0' maxlength='7'>
                    <span id='shipping-value-error' class='text-danger'></span>
                </div>
            </div>
            <div class="row">
                <div class='form-group col-12 mb-4' id="shipping-region-">
                    <label for='value3'>Valor do frete para o Centro-Oeste</label>
                    <input name='value' type='text' class='input-pad shipping-value3' value='' placeholder='0' maxlength='7'>
                    <span id='shipping-value-error' class='text-danger'></span>
                </div>
            </div>
            <div class="row">
                <div class='form-group col-12 mb-4' id="shipping-region-">
                    <label for='value'>Valor do frete para o Sudeste</label>
                    <input name='value4' type='text' class='input-pad shipping-value4' value='' placeholder='0' maxlength='7'>
                    <span id='shipping-value-error' class='text-danger'></span>
                </div>
            </div>
            <div class="row">
                <div class='form-group col-12 mb-0' id="shipping-region-">
                    <label for='value5'>Valor do frete para o Sul</label>
                    <input name='value' type='text' class='input-pad shipping-value5' value='' placeholder='0' maxlength='7'>
                    <span id='shipping-value-error' class='text-danger'></span>
                </div>
            </div>
            <input type="hidden" id="regions_value" name="regions_value" >
        </div>

        <div class="col-6">
            <div class="switch-holder">
                <label for="own_hand" class='mb-10'>Mesmo valor em todas as regiões:
                    
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" checked name="" class='check shipping-regions' value='0'>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>

    <div class='row zip-code-origin-shipping-row' style='display:none'>
        <div class='form-group col-12 mb-0'>
            <label for='zip-code-origin'>CEP de origem</label>
            <input name='zip_code_origin' type='text' class='input-pad shipping-zipcode' data-mask="00000-000" value='' placeholder='12345-678'>
        </div>
    </div>
    <div class="row options-shipping-row mt-20" style="display:none">
        <div class="col-6">
            <div class="switch-holder">
                <label for="receipt" class='mb-10'>Aviso de Recebimento (AR):</label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="receipt" class='check shipping-receipt' value='0'>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="col-6">
            <div class="switch-holder">
                <label for="own_hand" class='mb-10'>Mão própria:
                    <i class="material-icons font-size-16" data-toggle="tooltip"
                       title="Serviço adicional dos Correios que faz com que apenas o destinatário possa receber o objeto.">help</i>
                </label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="own_hand" class='check shipping-ownhand' value='0'>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
    <div class='row mt-20'>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="switch-holder">
                <label for="cartao">Status</label>
                <br>
                <label class='switch'>
                    <input name='status' value='1' class='check shipping-status' type="checkbox" checked>
                    <span class='slider round'></span>
                </label>
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6">
            <div class="switch-holder">
                <label for="cartao">Pré-selecionado</label>
                <br>
                <label class='switch'>
                    <input name='pre_selected' value='1' class='check shipping-pre-selected' type="checkbox">
                    <span class='slider round'></span>
                </label>
            </div>
        </div>
    </div>
    <div class='row mt-20'>
        <div class='form-group col-12'>
            <label>Disponível para compras acima de: </label>
            <input name='rule_value' type='text' class='input-pad rule-shipping-value' value='0,00' placeholder='0,00'>
        </div>
    </div>
    <div class='row'>
        <div class="form-group col-12 shipping-plans-add-container">
            <label for='shipping-plans-add'>Oferecer o frete para os planos: </label>
            <select name="apply_on_plans[]" id="shipping-plans-add" class="form-control shipping-plans-add"
                    style='width:100%'
                    data-plugin="select2" multiple='multiple'> </select>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12 shipping-not-apply-plans-add-container'>
            <label for='shipping-not-apply-plans-add'>Não oferecer o frete para os planos: </label>
            <select name="not_apply_on_plans[]" id="shipping-not-apply-plans-add"
                    class="form-control shipping-not-apply-plans-add"
                    style='width:100%'
                    data-plugin="select2" multiple='multiple'></select>
        </div>
    </div>
    {{--    <div class='row'>
            <div class='form-group col-12'>
                <label for='status'>Status</label>
                <select id='shipping-status' name='status' class='form-control input-pad'>
                    <option value='1'>Ativado</option>
                    <option value='0'>Desativado</option>
                </select>
            </div>
        </div>
        <div class='row'>
            <div class='form-group col-12'>
                <label for='pre_selected'>Pré-selecionado</label>
                <select name='pre_selected' id='shipping-pre-selected' class='form-control input-pad'>
                    <option value='1'>Sim</option>
                    <option value='0'>Não</option>
                </select>
            </div>
        </div>--}}
</form>
