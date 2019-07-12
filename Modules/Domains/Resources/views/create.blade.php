<div class="modal-body">

    <form id="form-add-domain" method="post">
        @csrf
        <div class="row">
            <div class="form-group">
                <label for="name">Domínio</label>
                <input name="name" type="text" class="input-pad" id="name" placeholder="Digite o domínio">
            </div>
        </div>
        @if($project->shopify_id == '')
            <div class="row">
                <div class="form-group col-12">
                    <label for="domain_ip">IP que o domínio aponta</label>
                    <input name="domain_ip" type="text" class="input-pad" id="ip_dominio_cadastrar" placeholder="IP do domínio" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                </div>
            </div>
        @endif
    </form>

</div>

