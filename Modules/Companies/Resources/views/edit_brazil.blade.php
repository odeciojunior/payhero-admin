@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.scss')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header">
            <h1 class="page-title">Editar empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container-fluid">
            <div class="panel pt-30 p-30" data-plugin="matchHeight">
                <div class="col-xl-12">
                    <div class="example-wrap">
                        <div class="nav-tabs-horizontal" data-plugin="tabs">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation" id='nav_users'>
                                    <a class="nav-link active" data-toggle="tab" href="#tab_user" aria-controls="tab_user" role="tab">Empresa
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id='nav_bank_data'>
                                    <a class="nav-link" data-toggle="tab" href="#tab_bank_data" aria-controls="tab_bank_data" role="tab">Dados bancários
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="nav_documents">
                                    <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                       aria-controls="tab_documentos" role="tab">Documentos
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content pt-20">
                                <div class="tab-pane active" id="tab_user" role="tabpanel">
                                    <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_update_form'>
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="panel-heading col-10">
                                                <h3 class="panel-title">Informações básicas</h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="business_website">Site</label>
                                                <input name="business_website" value="{!! $company->business_website !!}" type="text" class="form-control" id="business_website">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="support_email">E-mail</label>
                                                <input name="support_email" value="{!! $company->support_email !!}" type="text" class="form-control" id="support_email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="support_telephone">Telefone</label>
                                                <input name="support_telephone" value="{!! $company->support_telephone !!}" type="text" class="form-control" id="support_telephone">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="fantasy_name">Nome fantasia</label>
                                                <input name="fantasy_name" value="{!! $company->fantasy_name !!}" type="text" class="form-control" id="fantasy_name">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="cnpj">CNPJ</label>
                                                <input name="cnpj" value="{!! $company->company_document !!}" type="text" class="form-control" id="cnpj">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="zip_code">CEP</label>
                                                <input name="zip_code" value="{!! $company->zip_code !!}" type="text" class="form-control" id="zip_code">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="state">Estado</label>
                                                <input name="state" value="{!! $company->state !!}" type="text" class="form-control" id="state">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="city">Município</label>
                                                <input name="city" value="{!! $company->city !!}" type="text" class="form-control" id="city">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="neighborhood">Bairro</label>
                                                <input name="neighborhood" value="{!! $company->neighborhood !!}" type="text" class="form-control" id="neighborhood">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="street">Rua</label>
                                                <input name="street" value="{!! $company->street !!}" type="text" class="form-control" id="street">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="number">Número</label>
                                                <input name="number" value="{!! $company->number !!}" type="text" class="form-control" id="number">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="complement">Complemento</label>
                                                <input name="complement" value="{!! $company->complement !!}" type="text" class="form-control" id="complement">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="country">País</label>
                                                <input name="country" value="{!! $company->country !!}" type="text" class="form-control" id="country">
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top: 30px">
                                            <input id="update_profile" type="submit" class="form-control btn btn-success" value="Atualizar" style="width: 30%">
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_bank_data" role="tabpanel">
                                    <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_bank_update_form'>
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="panel-heading col-10">
                                                <h3 class="panel-title">Informações Bancárias</h3>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-12">
                                                <label for="bank">Banco</label>
                                                <select id="bank" name="bank" class="form-control">
                                                    <option value="">Selecione</option>
                                                    @foreach($banks as $bank)
                                                        <option value="{!! $bank['code'] !!}" {!! $company->bank == $bank['code'] ? 'selected' : '' !!}>{!! $bank['code'] . ' - ' .$bank['name'] !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="agency">Agência</label>
                                                <input name="agency" value="{!! $company->agency !!}" type="text" class="form-control" id="agency">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="agency_digit">Digito</label>
                                                <input name="agency_digit" value="{!! $company->agency_digit !!}" type="text" class="form-control" id="agency_digit">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-6">
                                                <label for="account">Conta</label>
                                                <input name="account" value="{!! $company->account !!}" type="text" class="form-control" id="account">
                                            </div>
                                            <div class="form-group col-xl-6">
                                                <label for="account_digit">Digito</label>
                                                <input name="account_digit" value="{!! $company->account_digit !!}" type="text" class="form-control" id="account_digit">
                                            </div>
                                        </div>
                                        <div class="form-group" style="margin-top: 30px">
                                            <input id="update_profile" type="submit" class="form-control btn btn-success" value="Atualizar" style="width: 30%">
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="tab_documentos" role="tabpanel">
                                    Envie um extrato bancário, um comprovante de residência e o contrato social da empresa<br>
                                    <div id="dropzone">
                                        <form method="POST" action="{!! route('companies.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                            @csrf
                                            <div class="dz-message needsclick">
                                                Arraste os arquivos aqui ou click para selecionar.<br/>
                                            </div>
                                            <input id="company_id" name="company_id" value="{{$company->id_code}}" type="hidden" class="form-control">
                                            <input id="document_type" name="document_type" value="" type="hidden" class="form-control">
                                        </form>
                                    </div>
                                    <div class="row">
                                        <div class="panel-heading col-10">
                                            <h3 class="panel-title">Documentos Enviados</h3>
                                        </div>
                                        <table class="table table-hover table-striped table-bordered mt-2">
                                            <tbody>
                                                <tr class="text-center">
                                                    <td>
                                                        Extrato bancário
                                                    </td>
                                                    <td id='td_bank_status'>
                                                        {!! $company->bank_document_translate !!}
                                                    </td>
                                                </tr>
                                                <tr class='text-center'>
                                                    <td>
                                                        Comprovante de residencia
                                                    </td>
                                                    <td id='td_address_status'>
                                                        {!! $company->address_document_translate !!}
                                                    </td>
                                                </tr>
                                                <tr class='text-center'>
                                                    <td>
                                                        Contrato social
                                                    </td>
                                                    <td id='td_contract_status'>
                                                        {!! $company->contract_document_translate !!}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
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


