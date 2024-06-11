<input type='hidden'
       id='integration_id'
       value='' />
<form id='form_update_integration'
      method="post"
      action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class='form-group'>
                    <label for="select_projects_edit">Selecione sua loja</label>
                    <select class="sirius-select" id="select_projects_edit" name="project_id" disabled>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="token_edit">Token</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="token"
                           id="token_edit"
                           placeholder="Digite o token da API">
                </div>
            </div>
        </div>
    </div>
</form>
