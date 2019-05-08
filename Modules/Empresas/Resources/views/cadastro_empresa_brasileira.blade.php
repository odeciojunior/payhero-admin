<form method="post" action="/empresas/cadastrarempresa">
    @csrf

    <h4>Dados gerais</h4>
    <div class="row">
        <div class="form-group col-xl-6">
            <label for="cnpj">CNPJ / CPF</label>
            <input name="cnpj" type="text" class="form-control" id="cnpj" placeholder="CNPJ / CPF" data-mask="0#" required>
        </div>
        
        <div class="form-group col-xl-6">
            <label for="nome">Nome fantasia</label>
            <input name="nome_fantasia" type="text" class="form-control" id="nome" placeholder="Nome fantasia" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="cep">CEP</label>
            <input name="cep" type="text" class="form-control" id="cep" placeholder="CEP" data-mask="0#">
        </div>

        <div class="form-group col-xl-6">
            <label for="estado">Estado</label>
            <input name="uf" type="text" class="form-control" id="estado" placeholder="estado">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="municipio">Município</label>
            <input name="municipio" type="text" class="form-control" id="municipio" placeholder="Município">
        </div>

        <div class="form-group col-xl-6">
            <label for="bairro">Bairro</label>
            <input name="bairro" type="text" class="form-control" id="bairro" placeholder="Bairro">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label for="logradouro">Rua</label>
            <input name="logradouro" type="text" class="form-control" id="logradouro" placeholder="Rua">
        </div>

        <div class="form-group col-xl-6">
            <label for="numero">Número</label>
            <input name="numero" type="text" class="form-control" id="numero" placeholder="Número" data-mask="0#">
        </div>

    </div>

    <div class="row">

        <div class="form-group col-xl-6">
            <label for="complemento">Complemento</label>
            <input name="complemento" type="text" class="form-control" id="complemento" placeholder="Complemento">
        </div>

    </div>
    <h4>Dados bancários</h4>

    <div class="row">
        <div class="form-group col-xl-12">
            <label for="banco">Banco</label>
            <select id="banco" name="banco" class="form-control">
                <option value="">Selecione</option>
                @foreach($bancos as $banco)
                    <option value="{!! $banco['codigo'] !!}">{!! $banco['codigo'] . ' - ' .$banco['nome'] !!}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-9">
            <label for="agencia">Agência</label>
            <input name="agencia" type="text" class="form-control" id="agencia" placeholder="Agência" data-mask="0#">
        </div>
        <div class="form-group col-xl-3">
            <label for="agencia_digito">Dígito</label>
            <input name="agencia_digito" type="text" class="form-control" id="agencia_digito" placeholder="Dígito" data-mask="0#">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-9">
            <label for="conta">Conta</label>
            <input name="conta" type="text" class="form-control" id="conta" placeholder="Conta" data-mask="0#">
        </div>
        <div class="form-group col-xl-3">
            <label for="conta_digito">Dígito</label>
            <input name="conta_digito" type="text" class="form-control" id="agencia_digito" placeholder="Dígito" data-mask="0#">
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    </div>
</form>