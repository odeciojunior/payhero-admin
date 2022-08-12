<form id="form_add_upsell"
      method="post"
      action="#"
      style="display:none">
    @csrf
    <div style="width:100%">
        <div class="row">
            <div class="form-group col-12 mb-10">
                <label for="link">Descrição</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="form-control"
                           name="description"
                           id="add_description_upsell"
                           placeholder="Digite a descrição">
                </div>
            </div>
            <div class="form-group col-12 mb-10">
                <label for="link">Desconto</label>
                <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="discount"
                           id="add_discount_upsell"
                           placeholder="Digite o valor do desconto"
                           maxlength="2"
                           data-mask="0#">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>
            <div class="col d-flex mb-10">
                <div class="switch-holder w-full">
                    <label for="add_active_flag">Status</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="active_flag"
                               id="add_active_flag"
                               class="check"
                               value="1"
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="switch-holder w-full">
                    <label>Usar variantes</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="use_variants"
                               class="use-variants-upsell check"
                               value="1"
                               checked>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="form-group col-12 mb-10">
                <label for="add_apply_on_shipping">Ao selecionar o frete</label>
                <select name="apply_on_shipping[]"
                        id="add_apply_on_shipping"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
            <div class="form-group col-12 mb-10 apply-on-plan-container">
                <label for="add_apply_on_plans">Ao comprar o plano</label>
                <select name="apply_on_plans[]"
                        id="add_apply_on_plans"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
            <div class="form-group col-12 mb-10 offer-plan-container">
                <label for="add_offer_on_plans">Oferecer o plano</label>
                <select name="offer_on_plans[]"
                        id="add_offer_on_plans"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
        </div>
    </div>
</form>
