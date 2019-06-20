<div class='page-content container-fluid'>
    <div class='panel' data-plugin='matchHeight'>
        <div style='width: 100%;'>
            <form id='form-add-shipping'>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='type'>Tipo</label>
                        <select id='shipping-type' name='type' class='form-control type'>
                            <option value='pac' {{$shipping->type == "pac"?"selected":""}}>PAC (Calculado automaticamente pela API)</option>
                            <option value='sexed' {{$shipping->type == "sedex"?"selected":""}}>SEDEX (Calculado automaticamente pela API)</option>
                            <option value='static' {{$shipping->type == "static"?"selected":""}}>Frete fixo(vocễ define um valor fixo para o frete)</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='name'>Descrição</label>
                        <input name='name' type='text' id='shipping-name' class='form-control' value='{{$shipping->name}}' placeholder='PAC'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='information'>Informação Apresentada</label>
                        <input name='information' type='text' id='shipping-information' class='form-control' value='{{$shipping->information}}' placeholder='10 até 20 dias'>
                    </div>
                </div>
                <div class='row' id='value-shipping-row' @if($shipping->type != 'static')style='display:none;'@endif>
                    <div class='form-group col-12'>
                        <label for='value'>Valor</label>
                        <input name='value' type='text' id='shipping-value' class='form-control' value='{{$shipping->value}}' placeholder='0'>
                    </div>
                </div>
                <div class='row' id='zip-code-origin-shipping-row'>
                    <div class='form-group col-12'>
                        <label for='zip-code-origin'>CEP de origem</label>
                        <input name='zip_code_origin' id='shipping-zip-code-origin' type='text' class='form-control' value='{{$shipping->zip_code_origin}}' placeholder='12345-678'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='status'>Status</label>
                        <select id='shipping-status' name='status' class='form-control'>
                            <option value='1' {{$shipping->status == 1 ? 'selected':''}}>Ativado</option>
                            <option value='0'{{$shipping->status == 0 ? 'selected':''}}>Desativado</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='pre_selected'>Pré-selecionado</label>
                        <select name='pre_selected' id='shipping-pre-selected' class='form-control'>
                            <option value='1' {{$shipping->pre_selected == 1 ?'selected':''}}>Sim</option>
                            <option value='0' {{$shipping->pre_selected == 0 ? 'selected' : ''}}>Não</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
