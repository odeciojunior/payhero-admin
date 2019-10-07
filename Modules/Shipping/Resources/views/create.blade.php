<form id='form-add-shipping'>
    @csrf
    <div class='row'>
        <div class='form-group col-12'>
            <label for='type'>Tipo</label>
            <select name='type' class='form-control type select-pad shipping-type'>
                <option value='pac'>PAC (Calculado automaticamente pela API)</option>
                <option value='sedex'>SEDEX (Calculado automaticamente pela API)</option>
                <option value='static' selected>Frete fixo(você define um valor fixo para o frete)</option>
            </select>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12'>
            <label for='name'>Descrição no checkout</label>
            <input name='name' type='text' class='input-pad shipping-description' value='' placeholder='Frete grátis' maxlength='30'>
            <span id='shipping-name-error' class='text-danger'></span>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12'>
            <label for='information'>Tempo de entrega apresentado</label>
            <input name='information' type='text' class='input-pad shipping-info' value='' placeholder='10 até 20 dias' maxlength='30'>
            <span id='shipping-information-error' class='text-danger'></span>
        </div>
    </div>
    <div class='row value-shipping-row' style=''>
        <div class='form-group col-12'>
            <label for='value'>Valor do Frete</label>
            <input name='value' type='text' class='input-pad shipping-value' value='' placeholder='0' maxlength='7'>
            <span id='shipping-value-error' class='text-danger'></span>
        </div>
    </div>
    <div class='row zip-code-origin-shipping-row' style='display:none'>
        <div class='form-group col-12'>
            <label for='zip-code-origin'>CEP de origem</label>
            <input name='zip_code_origin' type='text' class='input-pad shipping-zipcode' data-mask="00000-000" value='' placeholder='12345-678'>
        </div>
    </div>
    <div class='row'>
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
                    <input name='pre_selected' value='1' class='check shipping-pre-selected' type="checkbox" checked>
                    <span class='slider round'></span>
                </label>
            </div>
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
