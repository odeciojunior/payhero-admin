<form id='form-register-pixel'>
    @csrf
    <div class="container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-12 mt-4">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control pixel-description" placeholder="Descrição" maxlength='30'>
                    </div>
                    <div class="form-group col-6">
                        <label for="platform">Plataforma</label>
                        <select name="platform" type="text" id='select-platform' class="form-control pixel-platform">
                            <option value="facebook">Facebook</option>
                            <option value="google_adwords">Google Adwords</option>
                            <option value="google_analytics">Google Analytics</option>
                            <option value="taboola">Taboola</option>
                            <option value="outbrain">Outbrain</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control pixel-status">
                            <option value="1">Ativo</option>
                            <option value="0">Desativado</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="code">Código</label>
                        <input name="code" type="text" id='code-pixel' class="form-control pixel-code" placeholder="52342343245553" maxlength='30'>
                    </div>
                </div>
                <div class='mb-1'>
                    <label>Rodar Pixel:</label>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-3">
                        <div class="switch-holder">
                            <label for="checkout" class='mb-10'>Checkout:</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox" value="" name='checkout' class='check pixel-checkout' checked>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="cartao">Purchase (cartão):</label>
                            <br>
                            <label class='switch'>
                                <input type="checkbox" value="" name='purchase_card' class='check pixel-purchase-card' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="switch-holder">
                            <label for="boleto">Purchase (boleto):</label>
                            <br>
                            <label class='switch'>
                                <input type="checkbox" value="" name='purchase_boleto' class='check pixel-purchase-boleto' checked>
                                <span class='slider round'></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
