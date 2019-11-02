<form id='form_add_integration' method="post" action="#">
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="company">Selecione seu projeto</label>
                    <select class="select-pad" id="project_id" name="project_id">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">API URL</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="api_url" id="api_url" placeholder="Digite a API URL">
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">API Key</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="api_key" id="api_key" placeholder="Digite a API Key">
                </div>
            </div>
        </div>
    </div>
</form>
