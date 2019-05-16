<form method="post" action="/empresas/cadastrarempresa">
    @csrf
    <input type="hidden" name="country" value="usa">
    <div class="row">
        <div class="form-group col-xl-6">
            <label>Legal Business Name</label>
            <input name="nome_fantasia" type="text" class="form-control" id="nome" placeholder="Legal Business Name" required>
        </div>

        <div class="form-group col-xl-6">
            <label>Document</label>
            <input name="cnpj" type="text" class="form-control" placeholder="Document" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>Statement descriptor</label>
            <input name="statement_descriptor" type="text" class="form-control" id="nome" placeholder="Statement descriptor" required>
        </div>

        <div class="form-group col-xl-6">
            <label>Shortened descriptor</label>
            <input name="shortened_descriptor" type="text" class="form-control" placeholder="Shortened descriptor" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>Business website</label>
            <input name="business_website" type="text" class="form-control" id="nome" placeholder="Business website" required>
        </div>

        <div class="form-group col-xl-6">
            <label>Support e-mail</label>
            <input name="support_email" type="text" class="form-control" placeholder="Support e-mail" required>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>Support telephone</label>
            <input name="support_telephone" type="text" class="form-control" placeholder="Support telephone" required>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-xl-6">
            <label>Zip Code</label>
            <input name="cep" type="text" class="form-control" id="cep" placeholder="Zip Code" data-mask="0#">
        </div>

        <div class="form-group col-xl-6">
            <label>State</label>
            <input name="uf" type="text" class="form-control" placeholder="State">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>City</label>
            <input name="municipio" type="text" class="form-control" placeholder="City">
        </div>

        <div class="form-group col-xl-6">
            <label>Neighborhood</label>
            <input name="bairro" type="text" class="form-control" placeholder="Neighborhood">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>Street</label>
            <input name="logradouro" type="text" class="form-control" placeholder="Street">
        </div>

        <div class="form-group col-xl-6">
            <label>Number</label>
            <input name="numero" type="text" class="form-control" placeholder="Number" data-mask="0#">
        </div>

    </div>

    <div class="row">

        <div class="form-group col-xl-6">
            <label>Complement</label>
            <input name="complemento" type="text" class="form-control" placeholder="Complement">
        </div>

    </div>
    <h4>Bank account</h4>

    <div class="row">
        <div class="form-group col-xl-6">
            <label>Routing Number</label>
            <input id="routing_number" name="banco" type="text" class="form-control" placeholder="Routing Number">
        </div>
        <div class="form-group col-xl-6">
            <label>Bank</label>
            <input id="bank" type="text" class="form-control" placeholder="Bank" disabled>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-12">
            <label>Account number</label>
            <input name="conta" type="text" class="form-control" placeholder="Account number">
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    </div>

</form>

