<form id='form_add_integration' method="post" action="#">
    @csrf
    <div style="width:100%">
        <div class="row" style="margin-top:30px">
            <div class="input-group col-12">
                <label for="url_store">URL da sua loja no WooCommerce</label>
                <div class="d-flex input-group">
                    <input required type="text" class="input-pad" name="url_store" id="url_store" placeholder="Digite a URL da sua loja">

                </div>
            </div>
        </div>
        <div class="row" style="margin-top:30px">
            <div class="input-group col-12">
                <label for="token">Consumer key</label>
                <div class="d-flex input-group">
                    <input required type="text" class="input-pad" name="token_user" id="token_user" placeholder="">
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:30px">
            <div class="input-group col-12">
                <label for="token">Consumer secret</label>
                <div class="d-flex input-group">
                    <input required type="text" class="input-pad" name="token_pass" id="token_pass" placeholder="">
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:30px">
            <div class="col-12">
                <label for="company">Empresa</label>
                <input type="text" disabled class="company_name" style="text-overflow: ellipsis;">
                <input type="hidden" name="company" id="company-navbar-value">
            </div>
        </div>
    </div>
</form>


