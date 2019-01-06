<div style="text-align: center">
    <h4>Editar cupom de desconto</h4>
</div>

<form id="editar_cupom" method="post">
    @csrf
    <input type="hidden" value="{!! $cupom->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="nome">Nome</label>
                        <input value="{!! $cupom->nome != '' ? $cupom->nome : '' !!}" name="nome" type="text" class="form-control" id="nome" placeholder="Nome">
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="descricao">Descrição</label>
                        <input value="{!! $cupom->descricao != '' ? $cupom->descricao : '' !!}" name="descricao" type="text" class="form-control" id="descricao" placeholder="Descrição">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="tipo">Tipo</label>
                        <select name="tipo" class="form-control" id="tipo" required>
                            <option value="">Selecione</option>
                            <option value="0" {!! ($cupom->tipo == '0') ? 'selected' : '' !!}>Porcentagem</option>
                            <option value="1" {!! ($cupom->tipo == '1') ? 'selected' : '' !!}>Valor</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="valor">Valor</label>
                        <input value="{!! $cupom->valor != '' ? $cupom->valor : '' !!}" name="valor" type="text" class="form-control" id="valor" placeholder="Valor" data-mask="0#">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="cod_cupom">Código</label>
                        <input value="{!! $cupom->cod_cupom != '' ? $cupom->cod_cupom : '' !!}" name="cod_cupom" type="text" class="form-control" id="cod_cupom" placeholder="Código">
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" id="status" required>
                            <option value="">Selecione</option>
                            <option value="1" {!! ($cupom->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($cupom->status == '0') ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
