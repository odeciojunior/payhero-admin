<form id='form-update-shipping' enctype="multipart/form-data">
    @method('PUT')
    <input type="hidden" class="shipping-id" value="">
    <div class='row'>
        <div class='form-group col-12'>
            <label for='type'>Tipo</label>
            <select name='type' class='form-control input-pad type shipping-type'>
                <option value='pac'>PAC (Calculado automaticamente pela API)</option>
                <option value='sedex'>SEDEX (Calculado automaticamente pela API)</option>
                <option value='static'>Frete fixo (você define um valor fixo para o frete)</option>
            </select>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12'>
            <label for='name'>Descrição no checkout</label>
            <input name='name' type='text' class='input-pad shipping-description' value='' placeholder='PAC'
                   maxlength='30'>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12'>
            <label for='information'>Tempo de entrega estimado Apresentada</label>
            <input name='information' type='text' class='input-pad shipping-info' value='' placeholder='10 até 20 dias'
                   maxlength='30'>
        </div>
    </div>
    <div class='row value-shipping-row' style="display:none">
        <div class='form-group col-12'>
            <label for='value'>Valor</label>
            <input name='value' type='text' class='input-pad shipping-value' value='' placeholder='0' maxlength='7'>
        </div>
    </div>
    <div class='row zip-code-origin-shipping-row' style="display:block">
        <div class='form-group col-12'>
            <label for='zip-code-origin'>CEP de origem</label>
            <input name='zip_code_origin' type='text' class='input-pad shipping-zipcode' data-mask="00000-000" value=''
                   placeholder='12345-678'>
        </div>
    </div>
    <div class="row mt-20">
        <div class="col-6">
            <div class="switch-holder">
                <label for="token" class='mb-10'>Status:</label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="status" class='check shipping-status' value='0'>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="col-6">
            <div class="switch-holder">
                <label for="token" class='mb-10'>Pré-selecionado:</label>
                <br>
                <label class="switch">
                    <input type="checkbox" name="pre_selected" class='check shipping-pre-selected' value='0'>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
    </div>
</form>
