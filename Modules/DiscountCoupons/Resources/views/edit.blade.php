<div style="text-align: center">
    <h4>Editar cupom de desconto</h4>
</div>

<form id="editar_cupom" method="post">
    @csrf
    <input type="hidden" value="{!! $id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="nome">Nome</label>
                        <input value="{!! $cupom->name != '' ? $cupom->name : '' !!}" name="name" type="text" class="form-control" id="nome" placeholder="Nome">
                    </div>

                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="type">Tipo</label>
                        <select name="type" class="form-control" id="tipo" required>
                            <option value="0" {!! ($cupom->type == '0') ? 'selected' : '' !!}>Porcentagem</option>
                            <option value="1" {!! ($cupom->type == '1') ? 'selected' : '' !!}>Valor</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="valor">Valor</label>
                        <input value="{!! $cupom->value != '' ? $cupom->value : '' !!}" name="value" type="text" class="form-control" id="valor_cupom_editar" placeholder="Valor" data-mask="0#">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="code">Código</label>
                        <input value="{!! $cupom->code != '' ? $cupom->code : '' !!}" name="code" type="text" class="form-control" id="code" placeholder="Código">
                    </div>

                    <div class="form-group col-xl-6">
                        <label for="status">Status</label>
                        <select name="status" class="form-control" id="status" required>
                            <option value="1" {!! ($cupom->status == '1') ? 'selected' : '' !!}>Ativo</option>
                            <option value="0" {!! ($cupom->status == '0') ? 'selected' : '' !!}>Inativo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
