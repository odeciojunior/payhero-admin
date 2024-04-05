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
                    <select class="select-pad" id="select_projects_edit" name="project_id" disabled>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="client_id_edit">CLIENTE ID</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="clientid"
                           id="clientid_edit"
                           placeholder="Digite o client id">
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="webhook_url_edit">WEBHOOK URL</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="webhook_url"
                           id="webhook_url_edit"
                           placeholder="" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="token_edit">TOKEN</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="token"
                           id="token_edit"
                           placeholder="" readonly>
                    <!-- <div class="input-group-append">
                        <button class="btn btn-primary bg-white btnCopiarLinkToken"
                                type="button"
                                data-toggle="tooltip"
                                title="Copiar Token"
                                style="width: 46px; border-left: 1px solid #F4F4F4;">
                            <img src="/build/global/img/icon-copy-b.svg">
                        </button>
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</form>
