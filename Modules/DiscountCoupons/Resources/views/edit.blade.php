<form id="form-update-coupon" method="PUT" action="/couponsdiscounts" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <input type="hidden" value="{{Hashids::encode($coupon->id)}}" name="couponId">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="name">Descrição</label>
                        <input value="{!! $coupon->name != '' ? $coupon->name : '' !!}" name="name" type="text" class="form-control" id="name_coupon" placeholder="Descrição" maxlength='20'>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="type">Tipo</label>
                        <select name="type" class="form-control" id="type" required>
                            <option value="0" {!! ($coupon->type == '0') ? 'selected' : '' !!}>Porcentagem</option>
                            <option value="1" {!! ($coupon->type == '1') ? 'selected' : '' !!}>Valor</option>
                        </select>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="value">Valor</label>
                        <input value="{!! $coupon->value != '' ? $coupon->value : '' !!}" name="value" type="text" class="form-control" id="value" placeholder="Valor" data-mask="0#">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="code">Código</label>
                        <input value="{!! $coupon->code != '' ? $coupon->code : '' !!}" name="code" type="text" class="form-control" id="code" placeholder="Código" maxlength='30'>
                    </div>
                    <div class="form-group col-xl-6">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" id="status" required>
                            <option value="1" {!! ($coupon->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($coupon->status == '0') ? 'selected' : '' !!}>Desativado</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
