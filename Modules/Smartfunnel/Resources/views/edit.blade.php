<input type='hidden'
       id='integration_id'
       value='' />
<form id='form_update_integration'
      method="post"
      action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="company">Selecione seu projeto</label>
                    <select class="select-pad"
                            id="select_projects_edit"
                            name="project_id"
                            disabled>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">URL API</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="api_url"
                           id="api_url_edit"
                           placeholder="Digite a URL de integração">
                </div>
            </div>
        </div>
    </div>
</form>
