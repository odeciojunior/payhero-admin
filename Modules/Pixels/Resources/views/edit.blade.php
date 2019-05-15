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
                        <label for="nome">Descrição</label>
                        <input value="{!! $pixel->nome != '' ? $pixel->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Descrição">
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="descricao">Código</label>
                        <input value="{!! $pixel->cod_pixel != '' ? $pixel->cod_pixel : '' !!}" name="cod_pixel" type="text" class="form-control" id="cod_pixel" placeholder="Código">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="plataforma">Plataforma</label>
                        <select name="plataforma" type="text" class="form-control" id="plataforma">
                            <option value="facebook" {!! ($pixel->plataforma == 'facebook') ? 'selected' : '' !!}>Facebook</option>
                            <option value="google" {!! ($pixel->plataforma == 'google') ? 'selected' : '' !!}>Google</option>
                            <option value="taboola" {!! ($pixel->plataforma == 'taboola') ? 'selected' : '' !!}>Taboola</option>
                            <option value="outbrain" {!! ($pixel->plataforma == 'outbrain') ? 'selected' : '' !!}>Outbrain</option>
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
                            <option value="sim" {!! ($pixel->checkout == 'sim') ? 'selected' : '' !!}>Sim</option>
                            <option value="nao" {!! ($pixel->checkout == 'nao') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>

                    <div class="form-group col-12">
                        <label for="purchase_cartao">Rodar pixel no purchase (cartão)</label>
                        <select name="purchase_cartao" class="form-control" id="purchase_cartao">
                            <option value="sim" {!! ($pixel->purchase_cartao == 'sim') ? 'selected' : '' !!}>Sim</option>
                            <option value="nao" {!! ($pixel->purchase_cartao == 'nao') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label for="purchase_boleto">Rodar pixel no purchase (boleto)</label>
                        <select name="purchase_boleto" class="form-control" id="purchase_boleto">
                            <option value="sim" {!! ($pixel->purchase_boleto == 'sim') ? 'selected' : '' !!}>Sim</option>
                            <option value="nao" {!! ($pixel->purchase_boleto == 'nao') ? 'selected' : '' !!}>Não</option>
                        </select>
                    </div>

                </div>


            </div>
        </div>
    </div>
</form>
