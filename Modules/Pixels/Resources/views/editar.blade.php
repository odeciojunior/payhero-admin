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
            </div>
        </div>
    </div>
</form>
