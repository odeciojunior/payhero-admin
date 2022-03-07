@push('css')
    <link rel="stylesheet" href="{{ mix('modules/shipping/css/shipping-edit.min.css') }}">
@endpush

<form id='form-update-shipping' enctype="multipart/form-data">
    @method('PUT')
    <input type="hidden" class="shipping-id" value="">
    <div class='row'>
        <div class='form-group col-12'>
            <label for='type'>Tipo</label>
            <select name='type' id="shipping-type" class='sirius-select type'>
                <option value='static'>Frete fixo (você define um valor fixo para o frete)</option>
                <option value='pac'>PAC (Calculado automaticamente pela API)</option>
                <option value='sedex'>SEDEX (Calculado automaticamente pela API)</option>
            </select>
        </div>
    </div>
    <div class='row name-shipping-row'>
        <div class='form-group col-12'>
            <label for='name'>Descrição no checkout</label>
            <input name='name' type='text' class='input-pad shipping-description' value='' placeholder='PAC'
                   maxlength='60'>
        </div>
    </div>
    <div class='row information-shipping-row'>
        <div class='form-group col-12'>
            <label for='information'>Tempo de entrega apresentado</label>
            <input name='information' type='text' class='input-pad shipping-info' value='' placeholder='10 até 20 dias'
                   maxlength='100'>
        </div>
    </div>
    <div class='row value-shipping-row' style="display:none">
        <div class='form-group col-12 mb-0'>
            <label for='value'>Valor</label>
            <input name='value' type='text' class='input-pad shipping-value' value='' placeholder='0' maxlength='7'>
        </div>
    </div>
    <div class='row zip-code-origin-shipping-row' style="display:block">
        <div class='form-group col-12 mb-0'>
            <label for='zip-code-origin'>CEP de origem</label>
            <input name='zip_code_origin' type='text' class='input-pad shipping-zipcode' data-mask="00000-000" value=''
                   placeholder='12345-678'>
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
    <div class='row mt-20'>
        <div class='form-group col-12'>
            <label for='zip-code-origin'>Disponível para compras acima de: </label>
            <input name='rule_value' type='text' class='input-pad rule-shipping-value' value='0' placeholder=''>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12 shipping-plans-edit-container'>
            <label for='shipping-plans-edit'>Oferecer o frete para os planos: </label>
            <select name="apply_on_plans[]" id="shipping-plans-edit" class="form-control shipping-plans-edit"
                    style='width:100%'
                    data-plugin="select2" multiple='multiple'> </select>
        </div>
    </div>
    <div class='row'>
        <div class='form-group col-12 shipping-not-apply-plans-edit-container'>
            <label for='shipping-not-apply-plans-edit'>Não oferecer o frete para os planos: </label>
            <select name="not_apply_on_plans[]" id="shipping-not-apply-plans-edit" class="form-control shipping-not-apply-plans-edit"
                    style='width:100%'
                    data-plugin="select2" multiple='multiple'></select>
        </div>
    </div>
</form>
