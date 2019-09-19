<form id='form_add_integration' method="post" action="#">
    @csrf
    <div style="width:100%">
        <div class="row">
            <div class="col-12">
                <label for="token">Token (password)</label>
                <input type="text" class="input-pad" name="token" id="token" placeholder="Password da chave de integração">
            </div>
        </div>
        <div class="row" style="margin-top:30px">
            <div class="input-group col-12">
                <label for="url_store">URL da sua loja no Shopify</label>
                <div class="d-flex input-group">
                    <input type="text" class="input-pad col-7 addon" name="url_store" id="url_store" placeholder="Digite a URL da sua loja">
                    <span class="input-group-addon input-pad col-lg-5">.myshopify.com</span>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top:30px">
            <div class="col-12">
                <label for="company">Selecione sua empresa</label>
                <select class="select-pad" id="select_companies" name="company">
{{--                    @foreach($companies as $company)--}}
{{--                        <option value="{{ $company['id_code'] }}">{{ $company['fantasy_name'] }}</option>--}}
{{--                    @endforeach--}}
                </select>
            </div>
        </div>
    </div>
</form>
