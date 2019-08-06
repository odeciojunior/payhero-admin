<form id="form-register-coupon" method="post" action="/couponsdiscounts">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="name">Descrição</label>
                        <input name="name" type="text" class="form-control" id="nome_cupom" placeholder="Descrição">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="type">Tipo</label>
                        <select name="type" class="form-control" id="type" required>
                            <option value="0">Porcentagem</option>
                            <option value="1">Valor</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="value">Valor</label>
                        <input name="value" type="text" class="form-control" id="valor_cupom_cadastrar" placeholder="Valor" data-mask="0#">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="code">Código</label>
                        <input name="code" type="text" class="form-control" id="code" placeholder="Código">
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" id="status_cupom" required>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
