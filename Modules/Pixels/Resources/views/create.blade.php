<div style="text-align: center">
    <h4>Adicionar pixel</h4>
</div>
<form id='form-register-pixel' method="post" action="/pixels">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Descrição">
                    </div>
                    <div class="form-group col-xl-12">
                        <label for="code">Código</label>
                        <input name="code" type="text" class="form-control" id="code" placeholder="Código">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="platform">Plataforma</label>
                        <select name="platform" type="text" class="form-control" id="platform">
                            <option value="facebook">Facebook</option>
                            <option value="google">Google</option>
                            <option value="taboola">Taboola</option>
                            <option value="outbrain">Outbrain</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_pixel">
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="checkout">Rodar pixel no checkout</label>
                        <select name="checkout" class="form-control" id="checkout">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="purchase_card">Rodar pixel no purchase (cartão)</label>
                        <select name="purchase_card" class="form-control" id="purchase_card">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="purchase_boleto">Rodar pixel no purchase (boleto)</label>
                        <select name="purchase_boleto" class="form-control" id="purchase_boleto">
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
