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
        <div class="row">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="credit_flag_edit"
                           class='mb-10'>Cartão:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='1'
                               name="credit_flag_edit"
                               id="credit_flag_edit"
                               class='check'
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_flag_edit"
                           class='mb-10'>Pix:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='0'
                               name="pix_flag_edit"
                               id="pix_flag_edit"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="billet_flag_edit"
                           class='mb-10'>Boleto:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='1'
                               name="billet_flag_edit"
                               id="billet_flag_edit"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
