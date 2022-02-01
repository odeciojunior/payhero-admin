<form id="form-update-coupon" method="PUT">
    @csrf
    @method('PUT')
    <input type="hidden" value="" class="coupon-id" name="couponId">

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="name">Descrição</label>
            <input value="" name="name" type="text" class="form-control coupon-name" placeholder="Descrição" maxlength='20' style="height: 50px !important; border-radius: 8px;">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="type">Tipo</label>
            <select name="type" class="sirius-select coupon-type" required>
                <option value="0">Porcentagem</option>
                <option value="1">Valor</option>
            </select>
        </div>
        <div class="form-group col-xl-6">
            <label for="value">Valor</label>
            <input value="" name="value" type="text" class="form-control coupon-value" placeholder="Valor" data-mask="0#" style="height: 50px !important; border-radius: 8px;">
        </div>
    </div>
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="code">Código</label>
            <input value="" name="code" type="text" class="form-control coupon-code" placeholder="Código" maxlength='30' style="height: 50px !important; border-radius: 8px;">
        </div>
        <div class="form-group col-xl-6">
            <label for="status">Status</label>
            <select name="status" class="sirius-select coupon-status" required>
                <option value="1">Ativo</option>
                <option value="0">Desativado</option>
            </select>
        </div>
        <div class="form-group col-xl-12">
            <label for="status">Válido para compras com valor maior que:</label>
            <input name="rule_value" type="text" class="form-control rule-value" placeholder="Valor" style="height: 50px !important; border-radius: 8px;">
        </div>
    </div>
</form>
