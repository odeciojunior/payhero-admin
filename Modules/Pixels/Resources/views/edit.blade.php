<form id="form-update-pixel">
    @csrf
    <input type="hidden" value="" name="id" class="pixel-id">
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div class="row">
                <div class="form-group col-xl-12 mt-4">
                    <label for="name">Descrição</label>
                    <input value="" name="name" type="text" class="input-pad pixel-description" placeholder="Descrição" maxlength='30'>
                </div>
                <div class="form-group col-6">
                    <label for="platform">Plataforma</label>
                    <select name="platform" id='select-platform' type="text" class="form-control select-pad pixel-platform">
                        <option value="facebook">Facebook</option>
                        <option value="google_adwords">Google Adwords</option>
                        <option value="google_analytics">Google Analytics</option>
                        <option value="taboola">Taboola</option>
                        <option value="outbrain">Outbrain</option>
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="status">Status</label>
                    <select name="status" type="text" class="form-control select-pad pixel-status">
                        <option value="1">Ativo</option>
                        <option value="0">Desativado</option>
                    </select>
                </div>
                <div class="form-group col-xl-12">
                    <label for="code">Código</label>
                    <input value="" name="code" id='code-pixel' type="text" class="input-pad pixel-code" placeholder="52342343245553" maxlength='30'>
                </div>
                <div class='form-group col-12'>
                    <label for="edit_pixel_plans">Plano</label>
                    <select name="edit_pixel_plans[]" id="edit_pixel_plans" class="form-control" style='width:100%' data-plugin="select2" multiple='multiple'> </select>
                </div>
            </div>
            <div class='mb-1'>
                <label>Rodar Pixel:</label>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="switch-holder">
                        <label for="Checkout">Checkout:</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='checkout' class='check pixel-checkout'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="switch-holder">
                        <label for="cartao">Purchase (cartão):</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='purchase_card' class='check pixel-purchase-card'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="switch-holder">
                        <label for="boleto">Purchase (boleto):</label>
                        <br>
                        <label class='switch'>
                            <input type="checkbox" name='purchase_boleto' class='check pixel-purchase-boleto'>
                            <span class='slider round'></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


