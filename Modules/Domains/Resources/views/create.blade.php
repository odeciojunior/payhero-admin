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
                        <label for="name">Domínio</label>
                        <input name="name" type="text" class="form-control" id="name" placeholder="Domínio">
                    </div>
                </div>
                @if($project['shopify_id'] == '')
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="domain_ip">Ip que o domínio aponta</label>
                            <input name="domain_ip" type="text" class="form-control" id="ip_dominio_cadastrar" placeholder="Ip do domínio" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</form>

