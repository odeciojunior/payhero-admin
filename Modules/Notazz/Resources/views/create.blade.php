<input type='hidden' id='integration_id' value=''/>
<form id='form_add_integration' method="post" action="#">
    @csrf
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="select_projects_create">Selecione seu projeto</label>
                    <select class="select-pad" id="select_projects_create" name="select_projects_create"> </select>
                </div>
            </div>
            <div class="col-12">
                <div class='form-group'>
                    <label for="select_invoice_type_create">Tipo de nota fiscal emitida para o projeto</label>
                    <select class="select-pad" id="select_invoice_type_create" name="select_invoice_type_create">
                        <option value='1'>Nota de Serviço (nfse)</option>
                        {{--                        <option value='2'>Nota de Produto (nfe)</option>--}}
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">Token Api</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_api_create" id="token_api_create" placeholder="Digite o Token Api" value=''>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Token Webhook</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_webhook_create" id="token_webhook_create" placeholder="Digite o Token Webhook" value=''>
                    <small>Endereço de configuração do webhook da notazz</small>
                    <small>https://app.cloudfox.net/postback/notazz</small>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Token Logística</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_logistics_create" id="token_logistics_create" placeholder="Digite o Token Logística" value=''>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Data inicial</label>
                <div class="d-flex input-group">
                    <input type="date" class="input-pad addon" name="start_date_create" id="start_date_create" placeholder="Data Inicial" value=''>
                    <small>Data inicial da geração das notas fiscais. Esta data pode ser retroativa.</small>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Dias em espera</label>
                <div class="d-flex input-group">
                    <select class="select-pad" id="select_pending_days_create" name="select_pending_days_create">
                        <option value='1'>1</option>
                        <option value='7'>7</option>
                        <option value='15'>15</option>
                        <option value='30'>30</option>
                        <option value='60'>60</option>
                    </select>
                    <small>Quantidade de dias que a notazz vai aguardar para efetivamente emitir a nota fiscal.</small>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Emitir nota zerada</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" id='emit_zero' name="emit_zero" class='check shipping-pre-selected' value='0'>
                        <span class="slider round"></span>
                    </label>
                    <br>
                    <small>Emitir notas com valor zerado.</small>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Remover taxa</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" id='remove_tax' name="remove_tax" class='check shipping-pre-selected' value='0'>
                        <span class="slider round"></span>
                    </label>
                    <br>
                    <small>Remover da nota fiscal o valor cobrado pela plataforma.</small>
                </div>
            </div>
        </div>
    </div>
</form>
