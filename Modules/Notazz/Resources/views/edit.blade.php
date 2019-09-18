<input type='hidden' id='integration_id' value='{{Hashids::encode($integration->id)}}'/>
<form id='form_update_integration' method="post" action="#">
    @csrf
    @method('PUT')
    <div style="width:100%">
        <div class="row mt-20">
            <div class="col-12">
                <div class='form-group'>
                    <label for="company">Selecione seu projeto</label>
                    <select class="select-pad" id="project_id" name="project_id" disabled>
                        @foreach($projects as $project)
                            <option value="{!! $project['id'] !!}" {{ ($project->id == $integration->project_id) ? 'selected' : '' }}>{!! $project['name'] !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='form-group col-12'>
                <label for="url_store">Token Webhook</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_webhook" id="token_webhook" placeholder="Digite o Token Webhook" value='{{isset($integration->token_webhook) ? $integration->token_webhook : ''}}'>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Token Api</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_api" id="token_api" placeholder="Digite o Token Api" value='{{isset($integration->token_api) ? $integration->token_api : ''}}'>
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="url_store">Token Logística</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="token_logistics" id="token_logistics" placeholder="Digite o Token Logística" value='{{isset($integration->token_logistics) ? $integration->token_logistics : ''}}'>
                </div>
            </div>
        </div>
    </div>
</form>
