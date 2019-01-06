<div style="text-align: center">
    <h4> Editar domínio </h4>
</div>

<form id="editar_dominio" method="post" action="/dominios/editardominio">
    @csrf
    <input type="hidden" value="{!! $dominio->id !!}" name="id">
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="dominio">Domínio</label>
                        <input value="{!! $dominio->dominio != '' ? $dominio->dominio : '' !!}" name="dominio" type="text" class="form-control" id="dominio" placeholder="Domínio">
                    </div>
                </div>

                <div class="row">

                    <div class="form-group col-12">
                        <label for="ip_dominio">Ip que o domínio aponta</label>
                        <input name="ip_dominio" type="text" class="form-control" id="ip_dominio" placeholder="Ip do domínio" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                    </div>
                </div>

            </div>

        </div>
    </div>
</form>
