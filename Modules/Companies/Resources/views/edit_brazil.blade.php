@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.scss')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
@endpush
@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Editar empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="{{route('companies.index')}}">
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
                                    <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_update_form'>
                                        @csrf
                                        @method('PUT')

                                        <h3 class="mb-15 mt-10">Informações básicas</h3>

                                        <div class="row">
                                                
                                                <div class="form-group col-xl-4">
                                                    <label for="fantasy_name">Nome Fantasia</label>
                                                    <input name="fantasy_name" value="{!! $company->fantasy_name !!}" type="text" class="input-pad" id="fantasy_name" placeholder="Nome Fantasia">
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="company_document">CNPJ</label>
                                                    <input name="company_document" value="{!! $company->company_document !!}" type="text" class="input-pad" id="company_document" placeholder='CNPJ'>
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="business_website">Site</label>
                                                    <input name="business_website" value="{!! $company->business_website !!}" type="text" class="input-pad" id="business_website" placeholder='Site'>
                                                </div>
                                                

                                        </div>

                                        <div class="row">
                                                
                                                <div class="form-group col-xl-4">
                                                    <label for="support_email">E-mail</label>
                                                    <input name="support_email" value="{!! $company->support_email !!}" type="text" class="input-pad" id="support_email" placeholder='E-mail'>
                                                </div>
                                            
                                                <div class="form-group col-xl-4">
                                                    <label for="support_telephone">Telefone</label>
                                                    <input name="support_telephone" value="{!! $company->support_telephone !!}" type="text" class="input-pad" id="support_telephone" placeholder='Telefone'>
                                                </div>

                                        </div>

                                        <h3 class="mb-15">Informações complementares</h3>

                                        <div class="row">

                                                <div class="form-group col-xl-2">
                                                    <label for="zip_code">CEP</label>
                                                    <input name="zip_code" value="{!! $company->zip_code !!}" type="text" class="input-pad" id="zip_code" placeholder='CEP'>
                                                </div>

                                        </div>

                                        <div class="row">
                                                <div class="form-group col-xl-5">
                                                    <label for="street">Rua/Avenida</label>
                                                    <input name="street" value="{!! $company->street !!}" type="text" class="input-pad" id="street" placeholder='Rua/Avenida'>
                                                </div>

                                                
                                                <div class="form-group col-xl-2">
                                                    <label for="number">Nº</label>
                                                    <input name="number" value="{!! $company->number !!}" type="text" class="input-pad" id="number" placeholder='Nº'>
                                                </div>

                                                <div class="form-group col-xl-5">
                                                    <label for="neighborhood">Bairro</label>
                                                    <input name="neighborhood" value="{!! $company->neighborhood !!}" type="text" class="input-pad" id="neighborhood" placeholder='Bairro'>
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="complement">Complemento</label>
                                                    <input name="complement" value="{!! $company->complement !!}" type="text" class="input-pad" id="complement" placeholder='Complemento'>
                                                </div>

                                                <div class="form-group col-xl-4">
                                                    <label for="state">Estado</label>
                                                    <input name="state" value="{!! $company->state !!}" type="text" class="input-pad" id="state" placeholder='Estado'>
                                                </div>
                                                <div class="form-group col-xl-4">
                                                    <label for="city">Cidade</label>
                                                    <input name="city" value="{!! $company->city !!}" type="text" class="input-pad" id="city" placeholder='Cidade'>
                                                </div>
                                                       
                                                {{--<div class="form-group col-xl-6">--}}
                                                    {{--<label for="country">Country</label>--}}
                                                    {{--<input name="country" value="{!! $company->country !!}" type="text" class="input-pad" id="country">--}}
                                                {{--</div>--}}

                                        </div>

                                        <div class="form-group text-right">
                                            <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="tab_bank_data" role="tabpanel">
                                    <form method="POST" action="{!! route('companies.update', ['id' => $company->id_code]) !!}" enctype="multipart/form-data" id='company_bank_update_form'>
                                        @csrf
                                        @method('PUT')

                                        <h3 class="mb-15 mt-10">Informações Bancárias</h3>

                                        <div class="row">
                                            <div class="form-group col-xl-4">
                                                <label for="bank">Banco</label>
                                                <select id="bank" name="bank" class="select-pad">
                                                    <option value="">Selecione</option>
                                                    @foreach($banks as $bank)
                                                        <option value="{!! $bank['code'] !!}" {!! $company->bank == $bank['code'] ? 'selected' : '' !!}>{!! $bank['code'] . ' - ' .$bank['name'] !!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-xl-3">
                                                <label for="agency">Agência</label>
                                                <input name="agency" value="{!! $company->agency !!}" type="text" class="input-pad" id="agency" placeholder='Agência'>
                                            </div>
                                            <div class="form-group col-xl-2">
                                                <label for="agency_digit">Digito</label>
                                                <input name="agency_digit" value="{!! $company->agency_digit !!}" type="text" class="input-pad" id="agency_digit" placeholder='Digito'>
                                            </div>
                                            <div class="form-group col-xl-3">
                                                <label for="account">Conta</label>
                                                <input name="account" value="{!! $company->account !!}" type="text" class="input-pad" id="account" placeholder='Conta'>
                                            </div>
                                            <div class="form-group col-xl-2">
                                                <label for="account_digit">Digito</label>
                                                <input name="account_digit" value="{!! $company->account_digit !!}" type="text" class="input-pad" id="account_digit" placeholder='Digito'>
                                            </div>
                                        </div>

                                        <div class="form-group text-right">
                                            <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane" id="tab_documentos" role="tabpanel">

                                <h3 class="mb-15 mt-10">Comprovantes</h3>


                                <div class="row mt-15">
                                        <div class="col-lg-6">
                                            <p class="mb-10">   Envie um extrato bancário, um comprovante de residência e o contrato social da empresa</p>
                                            <div id="dropzone drop-empresas">
                                                <form method="POST" action="{!! route('companies.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                                    @csrf
                                                    <div class="dz-message needsclick">
                                                        Arraste os arquivos aqui ou click para selecionar.<br/>
                                                    </div>
                                                    <input id="company_id" name="company_id" value="{{$company->id_code}}" type="hidden" class="input-pad">
                                                    <input id="document_type" name="document_type" value="" type="hidden" class="input-pad">
                                                </form>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <table class="table table-striped ">
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
                                                            Comprovante de Residência
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
    </div>
    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/companies/js/edit.js')}}"></script>
    @endpush
@endsection


