<div style="text-align: center">
    <h4>Editar parceiro</h4>
</div>

<form id="editar_parceiro" method="post">
    @csrf
    <input type="hidden" name="id" value="{!! $parceiro['id'] !!}">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="email_parceiro">Nome do parceiro</label>
                        <input value="{!! $user['name'] !!}" type="text" class="form-control" placeholder="Nome" disabled>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="tipo">Tipo de parceiro</label>
                        <select id="tipo" name="tipo" class="form-control">
                            <option value="gerente" {!! $parceiro['tipo'] == 'gerente' ? 'selected' : '' !!}>Gerente</option>
                            <option value="socio" {!! $parceiro['tipo'] == 'socio' ? 'selected' : '' !!}>Sócio</option>
                            <option value="fabricante/distribuidora" {!! $parceiro['tipo'] == 'fabricante/distribuidora' ? 'selected' : '' !!}>Fabricante/Distribuidora</option>
                            <option value="afiliado" {!! $parceiro['tipo'] == 'afiliado' ? 'selected' : '' !!}>Afiliado</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="tipo_remuneracao">Tipo de remuneração</label>
                        <select id="tipo_remuneracao" name="tipo_remuneracao" class="form-control">
                            <option value="dinheiro" {!! $parceiro['tipo_remuneracao'] == 'dinheiro' ? 'selected' : '' !!}>Dinheiro</option>
                            <option value="porcentagem" {!! $parceiro['tipo_remuneracao'] == 'porcentagem' ? 'selected' : '' !!}>Porcentagem</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-xl-6">
                        <label for="valor_remuneracao">Valor</label>
                        <input name="valor_remuneracao" value="{!! $parceiro['valor_remuneracao'] !!}" type="text" class="form-control" id="valor" placeholder="Valor">
                    </div>
                    <div class="form-group col-xl-12">
                        <input name="responsavel_frete" type="checkbox" id="responsavel_frete">
                        <label for="responsavel_frete" {!! $parceiro['reponsavel_frete'] ? 'checked' : '' !!}>Responsável pelo frete</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
