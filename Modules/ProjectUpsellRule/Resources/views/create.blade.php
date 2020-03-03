<form id='form_add_upsell' method="post" action="#" style="display:none">
    @csrf
    <div style="width:100%">
        <div class="row">
            <div class='form-group col-12'>
                <label for="link">Descrição</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="description" id="add_description_upsell" placeholder="Digite a descrição">
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="link">Ao comprar o plano</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="apply_on_plans" id="add_apply_on_plans" placeholder="Digite a descrição">
                </div>
            </div>
            <div class='form-group col-12'>
                <label for="link">Oferecer o plano</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad addon" name="offer_on_plans" id="add_offer_on_plans" placeholder="Digite a descrição">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <div class="switch-holder">
                    <label for="token" class='mb-10'>Status:</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox" value='1' name="active_flag" id="add_active_flag" class='check' checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</form>
