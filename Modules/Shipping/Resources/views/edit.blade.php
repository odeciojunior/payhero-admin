
            <form id='form-update-shipping' method="PUT" action="/shippings" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='type'>Tipo</label>
                        <select id='shipping-type' name='type' class='form-control input-pad type'>
                            <option value='pac' {{$shipping->type == "pac"?"selected":""}}>PAC (Calculado automaticamente pela API)</option>
                            <option value='sedex' {{$shipping->type == "sedex"?"selected":""}}>SEDEX (Calculado automaticamente pela API)</option>
                            <option value='static' {{$shipping->type == "static"?"selected":""}}>Frete fixo (você define um valor fixo para o frete)</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='name'>Descrição no Checkout</label>
                        <input name='name' type='text' id='shipping-name' class='input-pad' value='{{$shipping->name}}' placeholder='PAC'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='information'>Informação Apresentada</label>
                        <input name='information' type='text' id='shipping-information' class='input-pad' value='{{$shipping->information}}' placeholder='10 até 20 dias'>
                    </div>
                </div>
                <div class='row' id='value-shipping-row' @if($shipping->type != 'static')style='display:none;'@endif>
                    <div class='form-group col-12'>
                        <label for='value'>Valor</label>
                        <input name='value' type='text' id='shipping-value' class='input-pad' value='{{$shipping->value}}' placeholder='0'>
                    </div>
                </div>
                <div class='row' id='zip-code-origin-shipping-row'>
                    <div class='form-group col-12'>
                        <label for='zip-code-origin'>CEP de origem</label>
                        <input name='zip_code_origin' id='shipping-zip-code-origin' type='text' class='input-pad' value='{{$shipping->zip_code_origin}}' placeholder='12345-678'>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='status'>Status</label>
                        <select id='shipping-status' name='status' class='form-control input-pad'>
                            <option value='1' {{$shipping->status == 1 ? 'selected':''}}>Ativado</option>
                            <option value='0'{{$shipping->status == 0 ? 'selected':''}}>Desativado</option>
                        </select>
                    </div>
                </div>
                <div class='row'>
                    <div class='form-group col-12'>
                        <label for='pre_selected'>Pré-selecionado</label>
                        <select name='pre_selected' id='shipping-pre-selected' class=' form-control input-pad'>
                            <option value='1' {{$shipping->pre_selected == 1 ?'selected':''}}>Sim</option>
                            <option value='0' {{$shipping->pre_selected == 0 ? 'selected' : ''}}>Não</option>
                        </select>
                    </div>
                </div>
            </form>
