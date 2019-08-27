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
                <label for="url_store">Link</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="link" id="link" placeholder="Digite o link" value='{{isset($integration->link) ? $integration->link : ''}}'>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto gerado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" @if($integration->boleto_generated == '1') value="1" checked="" @else value="0" @endif name="boleto_generated" id="boleto_generated" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Boleto pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" @if($integration->boleto_paid == '1') value="1" checked="" @else value="0" @endif name="boleto_paid" id="boleto_paid" class='check'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Cartão de crédito pago:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" @if($integration->credit_card_paid == '1') value="1" checked="" @else value="0" @endif name="credit_card_paid" id="credit_card_paid" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Carrinho abandonado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" @if($integration->abandoned_cart == '1') value="1" checked="" @else value="0" @endif name="abandoned_cart" id="abandoned_cart" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="row mt-20">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Cartão de crédito Recusado:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" @if($integration->credit_card_refused == '1') value="1" checked="" @else value="0" @endif name="credit_card_refused" id="credit_card_refused" class='check' value='0'>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
