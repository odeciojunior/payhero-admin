<form id="form-add-domain" method="post">
    @csrf
                    <div class="form-group">
                        <label for="name">Domínio</label>
                        <input name="name" type="text" class="input-pad" id="name" placeholder="Domínio">
                    </div>
                </div>
                @if($project->shopify_id == '')
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="domain_ip">Ip que o domínio aponta</label>
                            <input name="domain_ip" type="text" class="form-control" id="ip_dominio_cadastrar" placeholder="Ip do domínio" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                        </div>
                    </div>
                @endif
</form>


