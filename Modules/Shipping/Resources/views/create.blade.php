<div class='page-content container-fluid'>
    <div class='panel' data-plugin='matchHeight'>
        <div style='width: 100%;'>
            <form id='form-add-shipping'>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='type'>Tipo</label>
                        <select id='shipping-type' name='type' class='form-control type'>
                            <option value='pac'>PAC (Calculado automaticamente pela API)</option>
                            <option value='sedex'>SEDEX (Calculado automaticamente pela API)</option>
                            <option value='static'>Frete fixo(vocễ define um valor fixo para o frete)</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='name'>Descrição</label>
                        <input name='name' type='text' id='shipping-name' class='form-control' value='' placeholder='PAC'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='information'>Informação Apresentada</label>
                        <input name='information' type='text' id='shipping-information' class='form-control' value='' placeholder='10 até 20 dias'>
                    </div>
                </div>
                <div class='row' id='value-shipping-row' style='display:none;'>
                    <div class='form-group col-12'>
                        <label for='value'>Valor</label>
                        <input name='value' type='text' id='shipping-value' class='form-control' value='' placeholder='0'>
                    </div>
                </div>
                <div class='row' id='zip-code-origin-shipping-row'>
                    <div class='form-group col-12'>
                        <label for='zip-code-origin'>CEP de origem</label>
                        <input name='zip_code_origin' id='shipping-zip-code-origin' type='text' class='form-control' value='' placeholder='12345-678'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='status'>Status</label>
                        <select id='shipping-status' name='status' class='form-control'>
                            <option value='1'>Ativado</option>
                            <option value='0'>Desativado</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='pre_selected'>Pré-selecionado</label>
                        <select name='pre_selected' id='shipping-pre-selected' class='form-control'>
                            <option value='1'>Sim</option>
                            <option value='0'>Não</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
