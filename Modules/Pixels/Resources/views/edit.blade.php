<div style="text-align: center">
    <h4>Editar pixel</h4>
</div>
<form id="editar_pixel" method="post" action="/pixels/editarpixel">
    @csrf
    <input type="hidden" value="{!! $pixel->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="name">Descrição</label>
                        <input value="{!! $pixel->name != '' ? $pixel->name : '' !!}" name="name" type="text" class="form-control" id="name" placeholder="Descrição">
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="code">Código</label>
                        <input value="{!! $pixel->code != '' ? $pixel->code : '' !!}" name="code" type="text" class="form-control" id="code" placeholder="Código">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="platform">Plataforma</label>
                        <select name="platform" type="text" class="form-control" id="platform">
                            <option value="facebook" {!! ($pixel->platform == 'facebook') ? 'selected' : '' !!}>Facebook</option>
                            <option value="google" {!! ($pixel->platform == 'google') ? 'selected' : '' !!}>Google</option>
                            <option value="taboola" {!! ($pixel->platform == 'taboola') ? 'selected' : '' !!}>Taboola</option>
                            <option value="outbrain" {!! ($pixel->platform == 'outbrain') ? 'selected' : '' !!}>Outbrain</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status">
                            <option value="1" {!! ($pixel->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($pixel->status == '0') ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="checkout">Rodar pixel no checkout</label>
                        <select name="checkout" class="form-control" id="checkout">
                            <option value="1" {!! ($pixel->checkout == '1') ? 'selected' : '' !!}>Sim</option>
                            <option value="0" {!! ($pixel->checkout == '0') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <label for="purchase_card">Rodar pixel no purchase (cartão)</label>
                        <select name="purchase_card" class="form-control" id="purchase_card">
                            <option value="1" {!! ($pixel->purchase_card == '1') ? 'selected' : '' !!}>Sim</option>
                            <option value="0" {!! ($pixel->purchase_card == '0') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="purchase_boleto">Rodar pixel no purchase (boleto)</label>
                        <select name="purchase_boleto" class="form-control" id="purchase_boleto">
                            <option value="1" {!! ($pixel->purchase_boleto == '1') ? 'selected' : '' !!}>Sim</option>
                            <option value="0" {!! ($pixel->purchase_boleto == '0') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>



