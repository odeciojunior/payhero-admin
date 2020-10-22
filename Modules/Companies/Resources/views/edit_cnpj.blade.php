@extends("layouts.master")
@push('css')
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/basic.css?v=1')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('/modules/profile/css/dropzone.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
@endpush

@section('content')
    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Editar empresa</h1>
            <div class="page-header-actions">
                <a class="btn btn-primary float-right" href="/companies">
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
                                    <a class="nav-link active" data-toggle="tab" href="#tab_user"
                                       aria-controls="tab_user" role="tab">Empresa
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id='nav_bank_data'>
                                    <a class="nav-link" data-toggle="tab" href="#tab_bank_data"
                                       aria-controls="tab_bank_data" role="tab">Dados Bancários</a>
                                </li>
                                <li class="nav-item" role="presentation" id="nav_documents">
                                    <a class="nav-link" data-toggle="tab" href="#tab_documentos"
                                       aria-controls="tab_documentos" role="tab"> Documentos
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation" id="nav_tax_gateways">
                                    <a class="nav-link"
                                       data-toggle="tab"
                                       href="#tab_tax_gateways"
                                       aria-controls="tab_tax_gateways"
                                       role="tab"> Tarifas e Prazos
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content pt-10 pr-30 pl-30">
                            <div class="tab-pane active" id="tab_user" role="tabpanel">
                                <form method="POST" enctype="multipart/form-data" id='company_update_form'
                                      class='form-basic-informations'>
                                    @csrf
                                    @method('PUT')
                                    <h3 class="mb-15 mt-10">Informações básicas</h3>
                                    <div class="row">
                                        <div class="form-group col-xl-6">
                                            {{--carrega no js--}}
                                            <label for="company_document" class='label-document'></label>
                                            <input name="company_document" value="" type="text" class="form-control"
                                                   id="company_document">
                                        </div>
                                        <div class="form-group col-xl-6">
                                            <label for="fantasy_name">Razão social</label>
                                            <input name="fantasy_name" value="" type="text" class="form-control"
                                                   id="fantasy_name" placeholder="Razão social" maxlength='250'>
                                        </div>
                                    </div>
                                    <h3 class="mb-15 mt-15">Informações de endereço</h3>
                                    <div class="row">
                                        <div class="form-group col-xl-2">
                                            <label for="zip_code">CEP</label>
                                            <input name="zip_code" value="" type="text"
                                                   class="form-control info-complemented" id="zip_code"
                                                   placeholder='CEP'>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-5">
                                            <label for="street">Rua/Avenida</label>
                                            <input name="street" value="" type="text"
                                                   class="form-control info-complemented" id="street"
                                                   placeholder='Rua/Avenida' maxlength='40'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="number">Nº</label>
                                            <input name="number" value="" type="text" data-mask="0#########"
                                                   class="form-control info-complemented" id="number" placeholder='Nº'
                                                   maxlength='10'>
                                        </div>
                                        <div class="form-group col-xl-5">
                                            <label for="neighborhood">Bairro</label>
                                            <input name="neighborhood" value="" type="text"
                                                   class="form-control info-complemented" id="neighborhood"
                                                   placeholder='Bairro' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="complement">Complemento</label>
                                            <input name="complement" value="" type="text"
                                                   class="form-control info-complemented" id="complement"
                                                   placeholder='Complemento' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4 div-state" style='display:none;'>
                                            <label for="state">Estado</label>
                                            <input name="state" value="" type="text"
                                                   class="form-control info-complemented" id="state"
                                                   placeholder='Estado' maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="city">Cidade</label>
                                            <input name="city" value="" type="text"
                                                   class="form-control info-complemented" id="city" placeholder='Cidade'
                                                   maxlength='30'>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="country">País</label>
                                            {{--                                            <input name="country" value="" type="text" class="form-control info-complemented" id="country">--}}
                                            <select id="country" name='country' class="form-control select-pad"
                                                    disabled>
                                                <option value="brazil">Brasil</option>
                                                <option value="usa">Estados Unidos</option>
                                                <option value="germany">Alemanha</option>
                                                <option value="spain">Espanha</option>
                                                <option value="france">França</option>
                                                <option value="italy">Itália</option>
                                                <option value="portugal">Portugal</option>
                                                <option value="unitedkingdom">Reino Unido</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class='row'>
                                        <div class='form-group col-xl-4'>
                                            {{--                                            <label for="active_flag">Status da empresa</label>--}}
                                            {{--                                            <select name='active_flag' class='form-control select-pad' id="active_flag">--}}
                                            {{--                                                <option value='1'>Ativo</option>--}}
                                            {{--                                                <option value='0'>Desativado</option>--}}
                                            {{--                                            </select>--}}
                                            <div class="switch-holder">
                                                <label for="active_flag" class='mb-10'>Status da empresa</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" value='1' name="active_flag" id="active_flag"
                                                           class='check' checked>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group text-right col-xl-8">
                                            <input id="update_profile" type="submit" class="btn btn-success"
                                                   value="Atualizar" style="width: auto;">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab_bank_data" role="tabpanel">
                                <h3 class="mb-15 mt-10">Informações bancárias da empresa</h3>
                                <form method="POST" enctype="multipart/form-data" id='company_bank_update_form'>
                                    @method('PUT')
                                    <div class="row">
                                        <div class="col-xl-4">
                                            <div class='form-group div-bank'>
                                                <label for='bank'>Banco</label>
                                                <select id="bank" name="bank" class="form-control" style='width:100%'
                                                        data-plugin="select2">
                                                    <option value="">Selecione</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class='col-xl-4'></div>
                                        {{--                                        <div class='form-group col-xl-2'>--}}
                                        {{--                                            <label for='account_type'>Tipo de conta</label>--}}
                                        {{--                                            <select id="account_type" name="account_type" class="input-pad">--}}
                                        {{--                                                <option value="">Selecione</option>--}}
                                        {{--                                                <option value="1">Conta corrente</option>--}}
                                        {{--                                                <option value="2">Conta poupança</option>--}}
                                        {{--                                            </select>--}}
                                        {{--                                        </div>--}}
                                        <div class="col-xl-8" id="swift-code-info" style="display:none">
                                            <div class="alert alert-secondary">
                                                <h4 class="mt-0">O que é um código SWIFT/BIC?</h4>
                                                <span class="badge badge-secondary">AAA</span>
                                                <span class="badge badge-secondary">BB</span>
                                                <span class="badge badge-secondary">CC</span>
                                                <span class="badge badge-secondary">DDD</span>
                                                <ul class="pl-20 mt-10">
                                                    <li>Os quatro primeiros dígitos representam o
                                                        <strong>código do banco</strong></li>
                                                    <li>O segundo grupo tem 2 dígitos e representa o
                                                        <strong>código do país</strong></li>
                                                    <li>Os terceiro grupo tem 2 dígitos podem ser letras ou números e
                                                        representa o
                                                        <strong>código de localização</strong> da sede do banco
                                                    </li>
                                                    <li>Os três últimos dígitos representam o
                                                        <strong>código do agência</strong>. XXX representa a sede
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-3">
                                            <label for="agency">Agência</label>
                                            <input name="agency" value="" type="text" class="input-pad" id="agency"
                                                   placeholder='Agência'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="agency_digit">Digito</label>
                                            <input name="agency_digit" value="" type="text" class="input-pad"
                                                   id="agency_digit" placeholder='Digito' maxlength='20'>
                                        </div>
                                        <div class="form-group col-xl-3">
                                            <label for="account">Conta</label>
                                            <input name="account" value="" type="text" class="input-pad" id="account"
                                                   placeholder='Conta' maxlength='20'>
                                        </div>
                                        <div class="form-group col-xl-2">
                                            <label for="account_digit">Digito</label>
                                            <input name="account_digit" value="" type="text" class="input-pad"
                                                   id="account_digit" placeholder='Digito' maxlength='20'>
                                        </div>
                                    </div>
                                    <div class="form-group text-right mt-20">
                                        <input id="update_profile" type="submit" class="btn btn-success"
                                               value="Atualizar" style="width: auto;">
                                    </div>
                                </form>
                                <form method="POST" enctype="multipart/form-data" id='company_bank_routing_number_form'
                                      style="display:none;">
                                    @method('PUT')
                                    <div class="row">
                                        <div class="form-group col-xl-4">
                                            <label for="rounting_number">Rounting Number</label>
                                            <input name="bank" value="" type="text" class="input-pad"
                                                   id="rounting_number" placeholder='Routing number' maxlength='9'>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-xl-4">
                                            <label for="bank_routing_number">Banco</label>
                                            <input type="text" class="input-pad disabled" id="bank_routing_number"
                                                   placeholder='Digite um routing number válido...' maxlength="20"
                                                   disabled>
                                        </div>
                                        <div class="form-group col-xl-4">
                                            <label for="account_routing_number">Conta</label>
                                            <input name="account" value="" type="text" class="input-pad"
                                                   id="account_routing_number" placeholder='Conta' maxlength='20'>
                                        </div>
                                    </div>
                                    <div class="form-group text-right">
                                        <input id="update_profile" type="submit" class="btn btn-success"
                                               value="Atualizar" style="width: auto;">
                                    </div>
                                </form>
                            </div>
                            <div class="tab-pane" id="tab_documentos" role="tabpanel">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h5 class="title-pad"> Comprovantes </h5>
                                        <p class="sub-pad"> Para fazer movimentações externas, precisamos de documentos
                                            da sua empresa. </p>
                                        <div class="alert alert-info alert-dismissible fade show text-center"
                                             id='text-alert-documents' role="alert" style='display:none;'>
                                            <strong>Atenção!</strong> Os documentos somente serão analisados após todos
                                            serem enviados.
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th scope="col">Documento</th>
                                            <th scope="col">Status</th>
                                            <th scope="col"></th>
                                        </tr>
                                        </thead>
                                        <tbody class="custom-t-body">
                                        <tr>
                                            <td>
                                                Comprovante de extrato bancário
                                            </td>
                                            <td id="td-bank-status"></td>
                                            <td>
                                                <i id='details-document-person-juridic-bank-document'
                                                   title='Enviar documento'
                                                   class='icon wb-upload gradient details-document-person-juridic'
                                                   data-document='bank_document_status' aria-hidden="true"
                                                   style="cursor:pointer; font-size: 20px; display:none;"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Comprovante de endereço
                                            </td>
                                            <td id="td-address-status"></td>
                                            <td>
                                                <i id='details-document-person-juridic-address' title='Enviar Documento'
                                                   id='details-document-person-juridic-address'
                                                   class='icon wb-upload gradient details-document-person-juridic'
                                                   data-document='address_document_status' aria-hidden="true"
                                                   style="cursor:pointer; font-size: 20px"></i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                Comprovante de contrato social
                                            </td>
                                            <td id="td-contract-status"></td>
                                            <td>
                                                <i id='details-document-person-juridic-contract'
                                                   title='Enviar Documento'
                                                   class='icon wb-upload gradient details-document-person-juridic'
                                                   data-document='contract_document_status' aria-hidden="true"
                                                   style="cursor:pointer; font-size: 20px"></i>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class='tab-pane fade' id='tab_tax_gateways' role='tabpanel'>
                                <div class='row ' style='padding:0 30px 0 30px'>
                                    <div class="cielo-tax" hidden>
                                        <div class='col-lg-12'>
                                            <h6 class='title-pad'>Cartão de crédito:</h6>
                                        </div>
                                        <div class='col'></div>
                                        <div class='row mt-15 col-xl-12'>
                                            <div class='form-group col-xl-5'>
                                                <label for='credit-card-tax-cielo'>Por venda (porcentagem):</label>
                                                <input id='credit-card-tax-cielo' disabled='disabled'
                                                       class="form-control">
                                            </div>
                                            <div class='form-group col-xl-5'>
                                                <div class='form-group'>
                                                    <label for='credit-card-release-cielo'>Dias para liberação:</label>
                                                    <select id="credit-card-release-cielo" disabled='disabled'
                                                            class="form-control">
                                                        <option value="plan-30">30 dias (taxa de 5.9%)</option>
                                                        <option value="plan-15">15 dias (taxa de 6.5%)</option>
                                                        <option value="plan-0">Após postagem com rastreio válido
                                                        </option>
                                                        <option value="plan-tracking-code" disabled>Ao informar o código
                                                            de rastreio (em breve)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='col-lg-12'>
                                            <h6 class='title-pad'>Boleto:</h6>
                                        </div>
                                        <div class='col'></div>
                                        <div class='row mt-15 col-xl-12'>
                                            <div class='form-group col-xl-5'>
                                                <label for='boleto-tax-cielo'>Por venda (porcentagem):</label>
                                                <input id='boleto-tax-cielo' disabled='disabled' class="form-control">
                                            </div>
                                            <div class='form-group col-xl-5'>
                                                <div class='form-group'>
                                                    <label for='boleto-release-cielo'>Dias para liberação:</label>
                                                    <select id="boleto-release-cielo" disabled='disabled'
                                                            class="form-control">
                                                        <option value="plan-30">30 dias (taxa de 5.9%)</option>
                                                        <option value="plan-2">2 dias (taxa de 6.5%)</option>
                                                        <option value="plan-0">Após postagem com rastreio válido
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <p class='info' style='font-size: 10px; margin-top: -10px'>
                                                    <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa de
                                                    parcelamento no cartão de crédito de
                                                    <label id="installment-tax" style="color: gray"></label>
                                                    % ao mês.
                                                </p>
                                                <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                    <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa fixa de
                                                    R$
                                                    <label style="color: gray" id="transaction-tax"></label>
                                                    por transação.
                                                </p>
                                                <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                    <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa de
                                                    transferência para empresas do exterior de
                                                    <label style="color: gray" id="transaction-tax-abroad"></label>
                                                </p>
                                                <p class='info' style='font-size: 10px; margin-top: -13px'>
                                                    <i class='icon wb-info-circle' aria-hidden='true'></i> Em boletos
                                                    com o valor menor de R$ 40,00 a taxa cobrada será de R$ 3,00.
                                                </p>
                                                <p class='info info-antecipation-tax'
                                                   style='font-size: 10px; margin-top: -8px;display:none;'>
                                                    <i class='icon wb-info-circle' aria-hidden='true'></i> Taxa de
                                                    antecipação de
                                                    <label style="color: gray" id="label-antecipation-tax"></label>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="gateway-tax col-md-12 mt-20  " hidden>
                                        <div class="container">
                                            <div class='row mt-15 col-xl-12'>
                                                <div class='col form-group col-md-3'>
                                                    <label for='tax-payment'>Por venda (porcentagem):</label>
                                                    <input id='tax-payment' disabled='disabled'
                                                           class="form-control">
                                                </div>
                                                <div class='col form-group col-md-8'>
                                                    <div class='form-group select-gateway-tax'>
                                                        <label for='gateway-release-payment'>Dias para
                                                            liberação:</label>


                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group text-right mt-10">
                                                <input id="update_payment_tax_cnpj" type="submit" class="btn btn-success"
                                                       value="Atualizar" style="width: auto;">
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
    </div>

    <div class='modal fade example-modal-lg modal-3d-flip-vertical' id='modal-document-person-juridic'
         aria-hidden='true' aria-labelledby='exampleModalTitle' role='dialog' tabindex='-1'>
        <div class='modal-dialog modal-simple'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <button type='button' class='close' data-dismiss='modal' aria-label='Close'
                            id='close-modal-document-person-juridic'>
                        <span aria-hidden='true'>×</span>
                    </button>
                    <div style='width: 100%; text-align: center;'>
                        <h4 id='modal-title-document-person-juridic'></h4>
                    </div>
                </div>
                <div class='modal-body' style='margin: 10px;'>
                    <div class='row'>
                        <div class='col-lg-12'
                             style='min-height: 100px; max-height: 150px; overflow-x: hidden; overflow-y: scroll; margin-bottom: 20px;'>
                            <table class='table table-striped table-hover table-responsive-sm'
                                   id='table-document-person-juridic'>
                                <thead>
                                <tr>
                                    <th class='text-center' scope='col'>Data Envio</th>
                                    <th class='text-center' scope='col'>Status</th>
                                    <th class='text-center' scope='col'></th>
                                    <th class='text-center' scope='col'></th>
                                </tr>
                                </thead>
                                <tbody id='table-body-document-person-juridic'>
                                </tbody>
                            </table>
                        </div>
                        <div class='col-lg-12'>
                            <div id='dropzone' style='display:none;'>
                                <form method='POST' enctype='multipart/form-data' class='dropzone'
                                      id='dropzoneDocumentsJuridicPerson'>
                                    @csrf
                                    <div class="dz-message needsclick text-dropzone dropzone-previews"
                                         id='dropzone-text-document'>
                                        Arraste ou clique para fazer upload.<br/>
                                    </div>
                                    <input type='hidden' id='document-type' name='document_type' value=''>
                                    <input type='hidden' id='company-id' name='company_id' value=''>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn-danger' data-dismiss='modal'>Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2-selection--single {
            /*border: 1px solid #dddddd !important;*/
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

        .select2-selection__arrow {
            height: 43px !important;
            right: 10px !important;
        }

        .select2-selection__arrow b {
            border-color: #8f9ca2 transparent transparent transparent !important;
        }

        .select2-container--open .select2-selection__arrow b {
            border-color: transparent transparent #8f9ca2 transparent !important;
        }
    </style>

    @push('scripts')
        <script src="{{asset('/modules/global/js/dropzone.js')}}"></script>
        <script src="{{asset('/modules/companies/js/edit_cnpj.js?v=14')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection


