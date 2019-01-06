<div style="text-align: center">
    <h4>Cadastrar parceiro</h4>
</div>

<form id="cadastrar_parceiro" method="post" enctype="multipart/form-data">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="email_parceiro">Email do parceiro</label>
                        <input name="email_parceiro" type="email" class="form-control" id="email_parceiro" placeholder="Email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <label for="tipo">Tipo de parceiro</label>
                        <select id="tipo" name="tipo" class="form-control">
                            <option value="gerente">Gerente</option>
                            <option value="socio">Sócio</option>
                            <option value="fabricante/distribuidora">Fabricante/Distribuidora</option>
                            <option value="afiliado">Afiliado</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label for="tipo_remuneracao">Tipo de remuneração</label>
                        <select id="tipo_remuneracao" name="tipo_remuneracao" class="form-control">
                            <option value="dinheiro">Dinheiro</option>
                            <option value="porcentagem">Porcentagem</option>
                        </select>
                    </div>
                </div>
                <div class="row"> 
                    <div class="form-group col-xl-6">
                        <label for="valor_remuneracao">Valor</label>
                        <input name="valor_remuneracao" type="text" class="form-control" id="valor_remuneracao" placeholder="Valor">
                    </div>

                    <div class="form-group col-xl-12">
                        <input name="responsavel_frete" type="checkbox" id="responsavel_frete">
                        <label for="responsavel_frete">Responsável pelo frete</label>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
