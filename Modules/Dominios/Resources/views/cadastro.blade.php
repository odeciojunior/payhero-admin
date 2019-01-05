<div style="text-align: center">
    <h4>Cadastro de domínio no projeto</h4>
</div>
<form id="cadastrar_dominio" method="post" action="/dominios/cadastrardominio">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">
                <div class="row">
                    <div class="form-group col-12">
                        <label for="dominio">Domínio</label>
                        <input name="dominio" type="text" class="form-control" id="dominio" placeholder="Domínio">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-12">
                        <label for="ip_dominio">Ip que o domínio aponta</label>
                        <input name="ip_dominio" type="text" class="form-control" id="ip_dominio" placeholder="Ip do domínio">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

