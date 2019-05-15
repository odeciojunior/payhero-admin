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
                            <option value="socio">Sócio</option>
                            <option value="gerente">Gerente</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="valor_remuneracao">Valor (porcentagem)</label>
                        <input name="valor_remuneracao" type="text" class="form-control" id="valor_remuneracao" placeholder="Valor">
                    </div>

                </div>
            </div>
        </div>
    </div>
</form>
