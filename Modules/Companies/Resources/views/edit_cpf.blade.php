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
                <a class="btn btn-primary float-right" href="/companies">
                    <i class='icon wb-chevron-left-mini' aria-hidden='true'></i> Voltar
                </a>
            </div>
        </div>
        <div class="page-content container">
            <div class="card shadow" data-plugin="matchHeight">
                <div class="tab-content pt-10 pr-30 pl-30">
                    <div class="tab-pane active" id="tab_user" role="tabpanel">
                        <h3 class="mb-15 mt-10">Conta bancária</h3>
                        <div class="alert alert-info alert-dismissible fade show text-center" role="alert">
                            <strong>Atenção! </strong> O limite de saque mensal é de R$ R$ 1.900,00 para empresas cadastradas como pessoa física!
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form method="POST" enctype="multipart/form-data" id='company_update_bank_form' class='form-basic-informations'>
                            @method('PUT')
                            <div class="alert alert-info alert-dismissible fade show text-center" id='text-alert-documents-cpf' role="alert" style='display:none;'>
                                <strong>Atenção!</strong> Os documentos somente serão analisados após todos serem enviados.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class='form-group'>
                                        <label for='bank'>Banco</label>
                                        <select id="bank" name="bank" class="form-control" style='width:100%' data-plugin="select2">
                                            <option value="">Selecione</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-8" id="swift-code-info" style="display:none">
                                    <div class="alert alert-secondary">
                                        <h4 class="mt-0">O que é um código SWIFT/BIC?</h4>
                                        <span class="badge badge-secondary">AAA</span>
                                        <span class="badge badge-secondary">BB</span>
                                        <span class="badge badge-secondary">CC</span>
                                        <span class="badge badge-secondary">DDD</span>
                                        <ul class="pl-20 mt-10">
                                            <li>Os quatro primeiros dígitos representam o <strong>código do banco</strong></li>
                                            <li>O segundo grupo tem 2 dígitos e representa o <strong>código do país</strong></li>
                                            <li>Os terceiro grupo tem 2 dígitos podem ser letras ou números e representa o <strong>código de localização</strong> da sede do banco</li>
                                            <li>Os três últimos dígitos representam o <strong>código do agência</strong>. XXX representa a sede</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-3">
                                    <label for="agency">Agência</label>
                                    <input name="agency" value="" type="text" class="input-pad" id="agency" placeholder='Agência' maxlength='255'>
                                </div>
                                <div class="form-group col-xl-2">
                                    <label for="agency_digit">Digito</label>
                                    <input name="agency_digit" value="" type="text" class="input-pad" id="agency_digit" placeholder='Digito' maxlength='20'>
                                </div>
                                <div class="form-group col-xl-3">
                                    <label for="account">Conta</label>
                                    <input name="account" value="" type="text" class="input-pad" id="account" placeholder='Conta' maxlength='255'>
                                </div>
                                <div class="form-group col-xl-2">
                                    <label for="account_digit">Digito</label>
                                    <input name="account_digit" value="" type="text" class="input-pad" id="account_digit" placeholder='Digito' maxlength='20'>
                                </div>
                                <div class="form-group col-xl-2">
                                    <label for="update_bank_data"></label>
                                    <input id="update_bank_data" type="button" class="btn btn-success mt-30" value="Atualizar" style="width: auto;">
                                </div>
                            </div>
                        </form>
                        <form method="POST" enctype="multipart/form-data" id='company_bank_routing_number_form' style="display:none;">
                            @method('PUT')
                            <div class="row">
                                <div class="form-group col-xl-4">
                                    <label for="rounting_number">Rounting Number</label>
                                    <input name="bank" value="" type="text" class="input-pad" id="rounting_number" placeholder='Routing number' maxlength='9'>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-xl-4">
                                    <label for="bank_routing_number">Banco</label>
                                    <input type="text" class="input-pad disabled" id="bank_routing_number" placeholder='Digite um routing number válido...' maxlength="20" disabled>
                                </div>
                                <div class="form-group col-xl-4">
                                    <label for="account_routing_number">Conta</label>
                                    <input name="account" value="" type="text" class="input-pad" id="account_routing_number" placeholder='Conta' maxlength='20'>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <input id="update_profile" type="submit" class="btn btn-success" value="Atualizar" style="width: auto;">
                            </div>
                        </form>
                        <div class="col-lg-12 mb-40 mt-15">

                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class='text-center' scope="col">Documento</th>
                                        <th class='text-center' scope="col">Status</th>
                                        <th class='text-center' scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody class="custom-t-body">
                                    <tr>
                                        <td class='text-center'>
                                            Comprovante de extrato bancário
                                        </td>
                                        <td class='text-center' id='td-status-document-person-fisic'>
                                            <span id='status-document-fisic'></span>
                                        </td>
                                        <td class='text-center'>
                                            <i title='Enviar documento' class='icon wb-upload gradient details-document-person-fisic' data-document='person-fisic' aria-hidden="true" style="cursor:pointer; font-size: 20px"></i>
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

    {{-- Modal detalhes --}}
    <div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-document-person-fisic' aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
        <div class='modal-dialog modal-simple'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' id='close-modal-documents-person-fisic'>
                        <span aria-hidden='true'>×</span>
                    </button>
                    <div style='width: 100%; text-align: center;'>
                        <h4 id='modal-title-documents-person-fisic'>Comprovante de extrato bancário</h4>
                    </div>
                </div>
                <div class='modal-body' style='margin: 10px;'>
                    <div class='row'>
                        <div class='col-lg-12'
                             style='min-height: 100px; max-height: 150px; overflow-x: hidden; overflow-y: scroll; margin-bottom: 20px;'>
                            <table class='table table-striped table-hover table-sm' id='table-documents-person-fisic'>
                                <thead>
                                    <tr>
                                        <th class='text-center' scope='col'>Data Envio</th>
                                        <th class='text-center' scope='col'>Status</th>
                                        <th class='text-center' scope='col'></th>
                                        <th class='text-center' scope='col'></th>
                                    </tr>
                                </thead>
                                <tbody id='table-body-documents-person-fisic'>
                                </tbody>
                            </table>
                        </div>
                        <div class='col-lg-12' id='document-person-fisic-refused-motived' style='display: none;'></div>
                        <div class='col-lg-12'>
                            <div id='dropzone'>
                                <form method='POST' enctype='multipart/form-data' class='dropzone' id='dropzoneDocumentsFisicPerson'>
                                    @csrf
                                    <div class="dz-message needsclick text-dropzone dropzone-previews" id='dropzone-text-document'>
                                        Arraste ou clique para fazer upload.<br/>
                                    </div>
                                    <input type='hidden' id='document_type' name='document_type' value='bank_document_status'>
                                    <input type='hidden' id='company_id' name='company_id' value=''>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2-selection--single{
            border: 1px solid #dddddd !important;
            border-radius: .215rem !important;
            height: 43px !important;
        }

        .select2-selection__rendered {
            color: #707070 !important;
            font-size: 16px !important;
            font-family: 'Muli', sans-serif;
            line-height: 43px !important;
            padding-left: 14px !important;
            padding-right: 38px !important;
        }

        .select2-selection__arrow{
            height: 43px !important;
            right: 10px !important;
        }
        .select2-selection__arrow b {
            border-color: #8f9ca2 transparent transparent transparent !important;
        }
        .select2-container--open .select2-selection__arrow b{
            border-color: transparent transparent #8f9ca2 transparent !important;
        }
    </style>

    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/companies/js/edit_cpf.js?v=2')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush
@endsection


