@push("css")
    <link rel="stylesheet" href="{{ mix("build/layouts/shipping/create.min.css") }}">
@endpush

<form id="form-add-shipping">
    @csrf
    <input name="regions_values" id="regions_values" type="hidden">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="type">Tipo</label>
                <select name="type" id="shipping-type" class="sirius-select shipping-type">
                    <option value="static" selected>Frete fixo (você define um valor fixo para o frete)</option>
                    <option value="pac">PAC (Calculado automaticamente pela API)</option>
                    <option value="sedex">SEDEX (Calculado automaticamente pela API)</option>
                </select>
            </div>
            <div class="form-group name-shipping-row">
                <label for="name">Descrição no checkout</label>
                <input name="name" type="text" class="input-pad shipping-description" value=""
                       placeholder="Frete grátis" maxlength="60">
                <span id="shipping-name-error" class="text-danger"></span>
            </div>
            <div class="form-group information-shipping-row">
                <label for="information">Tempo de entrega apresentado</label>
                <input name="information" type="text" class="input-pad shipping-info" value=""
                       placeholder="10 até 20 dias" maxlength="100">
                <span id="shipping-information-error" class="text-danger"></span>
            </div>
            <div class="form-group zip-code-origin-shipping-row" style="display:none">
                <label for="zip-code-origin">CEP de origem</label>
                <input name="zip_code_origin" type="text" class="input-pad shipping-zipcode" data-mask="00000-000"
                       value="" placeholder="12345-678">
            </div>
            <div class="value-shipping-row">
                <div class="switch-holder">
                    <label>Valor único para todas as regiões</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" checked class="check shipping-regions" value="1">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="form-group" id="shipping-single-value">
                    <label for="value">Valor do Frete</label>
                    <input name="value" type="text" class="input-pad shipping-value shipping-money-format" value=""
                           placeholder="R$ 0,00" maxlength="8">
                    <span id="shipping-value-error" class="text-danger"></span>
                </div>
                <div id="shipping-multiple-value" style="display:none">
                    <div class="row">
                        <div class="form-group col-6" id="shipping-region-">
                            <label for="value">Valor para o Norte</label>
                            <input name="value1" type="text" class="input-pad shipping-value1 shipping-money-format"
                                   value="" placeholder="R$ 0,00" maxlength="8">
                            <span id="shipping-value-error" class="text-danger"></span>
                        </div>
                        <div class="form-group col-6" id="shipping-region-">
                            <label for="value">Valor para o Nordeste</label>
                            <input name="value2" type="text" class="input-pad shipping-value2 shipping-money-format"
                                   value="" placeholder="R$ 0,00" maxlength="8">
                            <span id="shipping-value-error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6" id="shipping-region-">
                            <label for="value3">Valor para o Centro-Oeste</label>
                            <input name="value3" type="text" class="input-pad shipping-value3 shipping-money-format"
                                   value="" placeholder="R$ 0,00" maxlength="8">
                            <span id="shipping-value-error" class="text-danger"></span>
                        </div>
                        <div class="form-group col-6" id="shipping-region-">
                            <label for="value">Valor para o Sudeste</label>
                            <input name="value4" type="text" class="input-pad shipping-value4 shipping-money-format"
                                   value="" placeholder="R$ 0,00" maxlength="8">
                            <span id="shipping-value-error" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-6" id="shipping-region-">
                            <label for="value5">Valor para o Sul</label>
                            <input name="value5" type="text" class="input-pad shipping-value5 shipping-money-format"
                                   value="" placeholder="R$ 0,00" maxlength="8">
                            <span id="shipping-value-error" class="text-danger"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label>Disponível para compras acima de: </label>
                <input name="rule_value" type="text" class="input-pad rule-shipping-value shipping-money-format"
                       value="" placeholder="R$ 0,00">
            </div>
            <div class="d-flex">
                <div class="switch-holder w-full">
                    <label for="cartao">Ativo</label>
                    <br>
                    <label class="switch">
                        <input name="status" value="1" class="check shipping-status" type="checkbox" checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="switch-holder w-full">
                    <label for="cartao">Pré-selecionado</label>
                    <br>
                    <label class="switch">
                        <input name="pre_selected" value="1" class="check shipping-pre-selected" type="checkbox">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="form-group shipping-plans-add-container">
                <label for="shipping-plans-add">Oferecer o frete para os planos: </label>
                <select name="apply_on_plans[]" id="shipping-plans-add" class="form-control shipping-plans-add"
                        style="width:100%"
                        data-plugin="select2" multiple="multiple"> </select>
            </div>
            <div class="form-group shipping-not-apply-plans-add-container">
                <label for="shipping-not-apply-plans-add">Não oferecer o frete para os planos: </label>
                <select name="not_apply_on_plans[]" id="shipping-not-apply-plans-add"
                        class="form-control shipping-not-apply-plans-add"
                        style="width:100%"
                        data-plugin="select2" multiple="multiple"></select>
            </div>
        </div>
    </div>
</form>
