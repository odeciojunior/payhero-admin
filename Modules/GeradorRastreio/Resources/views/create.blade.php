<form id='form_add_integration'
      method="post"
      action="#">
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <div class='form-group'>
                    <label for="project_id">Selecione sua loja</label>
                    <select class="sirius-select"
                            id="project_id"
                            name="project_id">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="client_id">CLIENTE ID</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="input-pad addon"
                           name="clientid"
                           id="clientid"
                           placeholder="Digite o client id">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="switch-holder">
                    <label for="credit_flag"
                           class='mb-10'>Cartão:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='1'
                               name="credit_flag"
                               id="credit_flag"
                               class='check'
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="pix_flag"
                           class='mb-10'>Pix:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='0'
                               name="pix_flag"
                               id="pix_flag"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-4">
                <div class="switch-holder">
                    <label for="billet_flag"
                           class='mb-10'>Boleto:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               value='0'
                               name="billet_flag"
                               id="billet_flag"
                               class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
