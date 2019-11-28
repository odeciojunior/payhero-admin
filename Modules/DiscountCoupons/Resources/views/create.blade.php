<form id="form-register-coupon">
    @csrf
    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Descrição</label>
            <input name="name" type="text" class="form-control coupon-name" placeholder="Descrição" maxlength='20'>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="type">Tipo</label>
            <select name="type" class="form-control coupon-type" required>
                <option value="0">Porcentagem</option>
                <option value="1">Valor</option>
            </select>
        </div>
        <div class="form-group col-xl-6">
            <label for="value">Valor</label>
            <input name="value" type="text" class="form-control coupon-value" placeholder="Valor" data-mask="0#">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="code">Código</label>
            <input name="code" type="text" class="form-control coupon-code" placeholder="Código" maxlength='30'>
        </div>
        <div class="form-group col-xl-6">
            <label for="status">Status</label>
            <select name="status" class="form-control coupon-status" required>
                <option value="1">Ativo</option>
                <option value="0">Desativado</option>
            </select>
        </div>
        <div class="form-group col-xl-12">
            <label for="status">Válido para compras com valor maior que:</label>
            <input name="rule_value" type="text" class="form-control rule-value" value="0,00" placeholder="0,00">
        </div>
    </div>
</form>
