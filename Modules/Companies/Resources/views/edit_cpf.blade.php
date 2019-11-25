@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
@endpush

@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Editar empresa (Pessoa física)</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" id="redirect_back_link">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="card shadow" data-plugin="matchHeight">
                <div class="tab-content pt-10 pr-30 pl-30">
                    <div class="tab-pane active" id="tab_user" role="tabpanel">
                        <form method="POST" enctype="multipart/form-data" id='company_update_form' class='form-basic-informations'>
                            <h3 class="mb-15 mt-10">Conta bancária</h3>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class='form-group'>
                                        <label for='bank'>Banco</label>
                                        <select id="bank" name="bank" class="form-control" style='width:100%' data-plugin="select2" >
                                            <option value="">Selecione</option>
                                        </select>
                                    </div>
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
                        </form>
                        <div class="row mt-15" id='row_dropzone_documents'>
                            <div class="col-lg-6">
                                <div id="dropzone">
                                    <form method="POST" action="{!! route('api.companies.uploaddocuments') !!}" enctype="multipart/form-data" class="dropzone" id='dropzoneDocuments'>
                                        <div class="dz-message needsclick text-dropzone">
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
                                            <td>Comprovante de extrato bancário</td>
                                            <td id="td_bank_status">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-lg-12  mt-10 mb-30">
                                <small class="text-muted" style="line-height: 1.5;">
                                    Comprovante de conta bancária: extrato válido do banco.
                                </small>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        {{--  <script src="{{asset('/modules/companies/js/edit.js?v=5')}}"></script>  --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush
@endsection


