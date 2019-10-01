@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Editar empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" id="redirect_back_link">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="card shadow" data-plugin="matchHeight">
                <div>
                    <div class="example-wrap">
                        <div class="nav-tabs-horizontal nav-tabs-line pt-15" data-plugin="tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation" id='nav_users'>
                                    <a class="nav-link active" data-toggle="tab" href="#tab_user" aria-controls="tab_user" role="tab">Empresa
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id='nav_bank_data'>
                                    <a class="nav-link" data-toggle="tab" href="#tab_bank_data" aria-controls="tab_bank_data" role="tab">Dados Bancários</a>
                                </li>
                                <li class="nav-item" role="presentation" id="nav_documents">
                                    <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                       aria-controls="tab_documentos" role="tab"> Documentos
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content pt-10 pr-30 pl-30">
                            <div class="tab-pane active" id="tab_user" role="tabpanel">
                                <form method="POST" enctype="multipart/form-data" id='company_update_form'>
                                    @csrf
                                    @method('PUT')
                                    <h3 class="mb-15 mt-10">Informações básicas</h3>
                                    <div class="row">
                                        <div class="form-group col-xl-4">
                                            <label for="fantasy_name">Nome Fantasia</label>
                                            <input name="fantasy_name" value="" type="text" class="input-pad" id="fantasy_name" placeholder="Nome Fantasia" maxlength='40'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="company_document">CNPJ</label>
                                            <input name="company_document" value="" type="text" class="input-pad" id="company_document" placeholder='CNPJ'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="business_website">Site</label>
                                            <input name="business_website" value="" type="text" class="input-pad" id="business_website" placeholder='Site' maxlength='60'>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-4">
                                            <label for="support_email">E-mail</label>
                                            <input name="support_email" value="" type="text" class="input-pad" id="support_email" placeholder='E-mail' maxlength='40'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="support_telephone">Telefone</label>
                                            <input name="support_telephone" value="" type="text" data-mask="(00) 0000-0000" class="input-pad" id="support_telephone" placeholder='Telefone'>
                                        </div>
                                    </div>
                                    <h3 class="mb-15">Informações complementares</h3>
                                    <div class="row">
                                        <div class="form-group col-xl-2">
                                            <label for="zip_code">CEP</label>
                                            <input name="zip_code" value="" type="text" data-mask="00000-000" class="input-pad" id="zip_code" placeholder='CEP'>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-5">
                                            <label for="street">Rua/Avenida</label>
                                            <input name="street" value="" type="text" class="input-pad" id="street" placeholder='Rua/Avenida' maxlength='40'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="number">Nº</label>
                                            <input name="number" value="" type="text" data-mask="0#########" class="input-pad" id="number" placeholder='Nº' maxlength='10'>
                                        </div>
                                        <div class="form-group col-xl-5">
                                            <label for="neighborhood">Bairro</label>
                                            <input name="neighborhood" value="" type="text" class="input-pad" id="neighborhood" placeholder='Bairro' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="complement">Complemento</label>
                                            <input name="complement" value="" type="text" class="input-pad" id="complement" placeholder='Complemento' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="state">Estado</label>
                                            <input name="state" value="" type="text" class="input-pad" id="state" placeholder='Estado' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="city">Cidade</label>
                                            <input name="city" value="" type="text" class="input-pad" id="city" placeholder='Cidade' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-6">
                                            <label for="country">Country</label>
                                            <input name="country" value="" type="text" class="input-pad" id="country">
                                        </div>
                                    </div>
                                    <div class="form-group text-right">
                                        <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab_bank_data" role="tabpanel">
                                <form method="POST" enctype="multipart/form-data" id='company_bank_update_form'>
                                    @method('PUT')
                                    <h3 class="mb-15 mt-10">Informações Bancárias</h3>
                                    <div class="row">
                                        <div class="form-group col-xl-4">
                                            <label for="bank">Banco</label>
                                            <select id="bank" name="bank" class="form-control select-pad">
                                                <option value="">Selecione</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-3">
                                            <label for="agency">Agência</label>
                                            <input name="agency" value="" type="text" class="input-pad" id="agency" placeholder='Agência' maxlength='20'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="agency_digit">Digito</label>
                                            <input name="agency_digit" value="" type="text" class="input-pad" id="agency_digit" placeholder='Digito' maxlength='20'>
                                        </div>
                                        <div class="form-group col-xl-3">
                                            <label for="account">Conta</label>
                                            <input name="account" value="" type="text" class="input-pad" id="account" placeholder='Conta' maxlength='20'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="account_digit">Digito</label>
                                            <input name="account_digit" value="" type="text" class="input-pad" id="account_digit" placeholder='Digito' maxlength='20'>
                                        </div>
                                    </div>
                                    <div class="form-group text-right">
                                        <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab_documentos" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h5 class="title-pad"> Comprovantes </h5>
                                        <p class="sub-pad"> Para fazer movimentações externas, precisamos de documentos da sua empresa. </p>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="row mt-15">
                                    <div class="col-lg-6">
                                        <div id="dropzone">
                                            <form method="POST" action="{!! route('api.companies.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                                <div class="dz-message needsclick">
                                                    Arraste os arquivos ou clique para selecionar<br/>
                                                </div>
                                                <input id="company_id" name="company_id" value="" type="hidden" class="input-pad">
                                                <input id="document_type" name="document_type" value="" type="hidden" class="input-pad">
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <table class="table table-striped">
                                            <tbody class="custom-t-body">
                                                <tr>
                                                    <td>Extrato Bancário</td>
                                                    <td id="td_bank_status">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td> Comprovante Residência</td>
                                                    <td id="td_address_status">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Contrato Social</td>
                                                    <td id="td_contract_status">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-12  mt-10">
                                        <small class="text-muted" style="line-height: 1.5;">
                                            Conta Bancária: extrato válido do banco.
                                            <br> Residência: luz, água ou outros;
                                            <br> Contrato Social: provando que você é dono ou sócio da empresa;
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/companies/js/edit.js')}}"></script>
    @endpush
@endsection


