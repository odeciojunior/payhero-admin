<div style="text-align: center">
    <h4>Adicionar pixel</h4>
</div>
<form id='cadastrar_pixel' method="post" action="/pixels/cadastrarpixel">
    @csrf
    <div class="page-content container-fluid">
        <div class="panel" data-plugin="matchHeight">
            <div style="width:100%">

                <div class="row">
                    <div class="form-group col-xl-12">
                        <label for="nome">Descrição</label>
                        <input name="nome" type="text" class="form-control" id="nome" placeholder="Descrição">
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="cod_pixel">Código</label>
                        <input name="cod_pixel" type="text" class="form-control" id="cod_pixel" placeholder="Código">
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-12">
                        <label for="plataforma">Plataforma</label>
                        <select name="plataforma" type="text" class="form-control" id="plataforma">
                            <option value="" selected>Selecione</option>
                            <option value="facebook">Facebook</option>
                            <option value="google">Google</option>
                            <option value="taboola">Taboola</option>
                            <option value="outbrain">Outbrain</option>
                        </select>
                    </div>

                    <div class="form-group col-xl-12">
                        <label for="status">Status</label>
                        <select name="status" type="text" class="form-control" id="status_pixel">
                            <option value="" selected>Selecione</option>
                            <option value="1">Ativo</option>
                            <option value="0">Inativo</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>
    </div>
</form>
