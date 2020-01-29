<div class="card">
    <div class="col-md-12">
        <input type='hidden' id='integration_id' value=''/>
        <form id='form_update_integration' method="post" action="#">
            @csrf
            @method('PUT')
            <div style="width:100%">
                <div class="row no-gutters">
                    <div class="col-md-3">
                        <img id="show-photo" class="card-img" src="" alt="">
                    </div>
                    <div class="col-md-9 pl-10">
                        <div class="card-body">
                            <div class="row justify-content-between align-items-baseline">
                                <div class="col-md-6">
                                    <h4 class="title-pad"></h4>
                                    <p class="card-text sm" id="created_at"></p>
                                </div>
                            </div>
                            <h5 class="sm-title"><strong> Descrição </strong></h5>
                            <p id="show-description" class="card-text sm"></p>
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
                <div class="row">
                    <div class='form-group col-12'>
                        <button id="bt_integration" type="button" class="btn btn-success">Salvar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>