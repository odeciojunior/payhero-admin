<form id="form_edit_upsell"
      method="PUT"
      action="#"
      style="display:none">
    @csrf
    @method('PUT')
    <input type="hidden"
           value=""
           name="id"
           class="upsell-id">
    <div style="width:100%">
        <div class="row">
            <div class="form-group col-12 mb-10">
                <label for="link">Descrição</label>
                <div class="d-flex input-group">
                    <input type="text"
                           class="form-control"
                           name="description"
                           id="edit_description_upsell"
                           placeholder="Digite a descrição">
                </div>
            </div>

            <div class="form-group col-12 mb-10 mt-20">
                <div class="row">

                    <div class="col-6" style="padding-left: 25px">

                        <input name="type" value="1" class="discount_radio " type="radio" id="usu_type_value" checked="">
                        <label for="usu_type_value">
                            Valor em R$
                        </label>
                    </div>
                    <div class="col-6">
                        <input name="type" value="0" class="discount_radio " type="radio" id="usu_type_percent">
                        <label for="usu_type_percent">Porcentagem</label>
                    </div>
                </div>
            </div>
            <div class="form-group col-12" >
                <label>Desconto de</label>

                <div id="usu_money_opt" class="input-group input-group-lg mb-3" >
                    <div class="input-group-prepend">
                        <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">R$</span>
                    </div>
                    <input  maxlength="9" name="discount_value" id="usu_discount_value" type="text" class=" input-pad form-control value-mask" >
                </div>

                <div id="usu_percent_opt" class="input-group-lg input-group mb-3" style="display: none;">

                    <input  maxlength="2" data-mask="0#" name="percent_value" id="usu_percent_value" style="" type="text" class=" input-dad form-control" placeholder="" aria-label="" aria-describedby="basic-addon1" autocomplete="off">
                    <div class="input-group-append">
                        <span style="border-color: #e0e7ee; background-color: #fafafa" class="input-group-text" id="basic-addon1">%</span>
                    </div>
                </div>
            </div>

            {{-- hide --}}
            <div class="form-group col-12 mb-10" style="display: none">
                <label for="link">Desconto</label>
                <div class="input-group">
                    <input type="text"
                           class="form-control"
                           name="discount"
                           id="edit_discount_upsell"
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
                    <label for="edit_active_flag">Status</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="active_flag"
                               id="edit_active_flag"
                               class="check">
                        <span class="slider round"></span>
                    </label>
                </div>
                <div class="switch-holder w-full">
                    <label>Usar variantes</label>
                    <br>
                    <label class="switch">
                        <input type="checkbox"
                               name="use_variants"
                               class="use-variants-upsell check">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
            <div class="form-group col-12 mb-10">
                <label for="edit_apply_on_shipping">Ao selecionar o frete</label>
                <select name="apply_on_shipping[]"
                        id="edit_apply_on_shipping"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
            <div class="form-group col-12 mb-10 apply-on-plan-container">
                <label for="edit_apply_on_plans">Ao comprar o plano</label>
                <select name="apply_on_plans[]"
                        id="edit_apply_on_plans"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
            <div class="form-group col-12 mb-10 offer-plan-container">
                <label for="edit_offer_on_plans">Oferecer o plano</label>
                <select name="offer_on_plans[]"
                        id="edit_offer_on_plans"
                        class="form-control"
                        style="width:100%"
                        multiple="multiple"></select>
            </div>
        </div>
    </div>
</form>
