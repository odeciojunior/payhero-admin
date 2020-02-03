@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=1') }}">
@endpush

@section('content')

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-lg-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                </div>
            </div>
        </div>
        <div class="page-content container" style="display:none">
            <div class="card shadow">
                <nav class="pt-20">
                    <div class="nav-tabs-horizontal">
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab"
                               href="#nav-transfers"
                               role="tab" aria-controls="nav-home" aria-selected="false">Transferências
                            </a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-extract"
                               role="tab" aria-controls="nav-profile" aria-selected="true">Extrato
                            </a>
                        </div>
                    </div>
                </nav>
                <div class="p-30 pt-20">
                    <div class="tab-content" id="nav-tabContent">
                        <!-- TRANSFERENCIAS -->
                        <div class="tab-pane fade show active" id="nav-transfers" role="tabpanel"
                             aria-labelledby="nav-home-tab">
                            <div class="row justify-content-start align-items-center">
                                <div class="col-8 mb-3">
                                    <div class="alert alert-danger alert-dismissible fade show" id='blocked-withdrawal' role="alert" style='display:none;'>
                                        <strong>Saque bloqueado!</strong> Entre em contato com o suporte para mais informações.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <h5 class="title-pad"> Nova transferência </h5>
                                    <p class="sub-pad"> Saque o dinheiro para sua conta bancária.
                                    </p>
                                </div>

                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo pendente </h6>
                                                <h4 class="price saldoPendente">
                                                </h4>
                                                <div class="grad-border red"></div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-md-12 col-lg-6 mb-15">
                                            <div id="div-available-money" class="price-holder pointer">
                                                <h6 class="label-price"> Saldo Disponível </h6>
                                                <h4 class="price saldoDisponivel">
                                                </h4>
                                                <div class="grad-border green"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo Total </h6>
                                                <h4 class="price saltoTotal">
                                                </h4>
                                                <div class="grad-border blue"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 col-lg-6" id="quotation_information" style="display: none">
                                            <div class="price-holder">
                                                <h6 class="label-price" id="label_quotation"> </h6>
                                                <h4 class="price" id="current_quotation">
                                                </h4>
                                                <div class="grad-border purple"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6 col-lg-6">
                                    <div class="row flex-column">
                                        <div class="col-12 mb-3">
                                            <div class="input-holder">
                                                <label for="transfers_company_select"> Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="transfers_company_select"> </select>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label for="company"> Valor a transferir</label>
                                            <div class='row' style='display: flex; margin:0px'>
                                                <div class="input-group col-sm-12 col-md-12 col-lg-6"
                                                     style='padding:0px'>
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="basic-addon1 custom-addon"
                                                              style="height: auto; border: 1px solid #ddd;"><span class="currency">$</span></span>
                                                    </div>
                                                    <input id="custom-input-addon" type="text"
                                                           class="form-control input-pad withdrawal-value"
                                                           placeholder="Digite o valor" aria-label="Digite o valor"
                                                           aria-describedby="basic-addon1">
                                                </div>
                                                <button id="bt-withdrawal"
                                                        class="btn btn-success disabled btn-sacar ml-3 col-sm-12 col-md-12 col-lg-5"
                                                        data-toggle="modal" disabled>
                                                    <svg class="mr-2" style="fill: white; vertical-align: middle;"
                                                         xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                         viewBox="0 0 24 24">
                                                        <path d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"></path>
                                                    </svg>
                                                    Sacar dinheiro
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-3 mt-3">
                                    <h5 class="card-title"> Histórico de transferências </h5>
                                </div>
                                <div class="col-12">
                                    <table id='withdrawalsTable' class="table table-striped table-condensed unify">
                                        <thead>
                                            <tr>
                                                <th scope="col">Conta</th>
                                                <th scope="col">Solicitação</th>
                                                <th scope="col">Liberação</th>
                                                <th scope="col">Valor</th>
                                                <th style="display: none" id="col_transferred_value" scope="col">Valor Convertido</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="withdrawals-table-data" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-withdrawals" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                                        {{--js carrega...--}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- EXTRATO -->
                        <div class="tab-pane fade" id="nav-extract" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <div class="row justify-content-between">
                                <div class="col-12 fix-5">
                                    <div class="d-flex align-items-center">
                                        <div class="p-2" style="flex:1">
                                            <h5 class="title-pad"> Extrato </h5>
                                            <p class="sub-pad"> Pra você controlar tudo que entra e sai da sua conta.
                                            </p>
                                        </div>
                                        <div class="p-2">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo no período</h6>
                                                <h4 id="available-in-period" style="font-weight: 700;font-size: 25px;display: inline;">
                                                </h4>
                                                <div class="grad-border green"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-15">
                                    <div class="row align-items-center">
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-holder">
                                                <label for="extract_company_select">Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="extract_company_select"> </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <label for="reason">Razão</label>
                                            <input type="text" id="reason" class="form-control select-pad" placeholder="Digite a razão. Ex.: Saque">
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <label for="transaction">Transação</label>
                                            <input type="text" id="transaction" class="form-control select-pad" placeholder="Digite o código da transação">
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-holder">
                                                <label for="type">Tipo</label>
                                                <select class="form-control select-pad" id="type">
                                                    <option value="">Todos</option>
                                                    <option value="in">Entrada</option>
                                                    <option value="out">Saída</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-20">
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <label for="transaction-value">Valor</label>
                                            <input type="text" id="transaction-value" class="form-control select-pad withdrawal-value" placeholder="Digite o valor">
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <div class="input-holder">
                                                <label for="date_type">Data</label>
                                                <select class="form-control select-pad" id="date_type">
                                                    <option value="transfer_date">Data da transferência</option>
{{--                                                    <option value="transaction_date">Data da transação</option>--}}
                                                    <option value="sale_start_date">Data da venda</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <input name="date_range" id="date_range" class="select-pad mt-30" placeholder="Clique para editar..." readonly>
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3 mt-30">
                                            <button id="bt_filtro" class="btn btn-primary w-full">
                                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <table id='transfersTable' class="table table-striped table-condensed unify">
                                        <thead>
                                            <tr>
                                                <th scope="col" class='headCenter' style='width:33%'>Razão</th>
                                                <th scope="col" class='headCenter' style='width:33%'>Data da transferência</th>
                                                <th scope="col" class='headCenter' style='width:34%'>Valor</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-transfers-body" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-transfers" class="pagination-sm"
                                        style="margin-top:10px;position:relative;float:right">
                                        {{--js carrega...--}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('companies::empty')
    </div>
    <!-- Modal confirmar saque -->
    <div id="modal-withdrawal" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
        <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
            <div id="conteudo_modal_add" class="modal-content p-10">
                <div class="header-modal simple-border-bottom">
                    <h2 id="modal-withdrawal-title" class="modal-title">Confirmar Saque</h2>
                </div>
                <div id="modal_body" class="modal-body simple-border-bottom" style='padding-bottom:1%;padding-top:1%;'>
                    <div>
                        <h5>Verifique os dados da conta:</h5>
                        <h4>Banco:
                            <span id="modal-withdrawal-bank"></span>
                        </h4>
                        <h4>Agência:
                            <span id="modal-withdrawal-agency"></span>
                            <span id="modal-withdrawal-agency-digit"></span>
                        </h4>
                        <h4>Conta:
                            <span id="modal-withdrawal-account"></span>
                            <span id="modal-withdrawal-account-digit"></span>
                        </h4>
                        <h4>Documento:
                            <span id="modal-withdrawal-document"></span>
                        </h4>
                        <hr>
                        <h4>Valor do saque:
                            <span id="modal-withdrawal-value" class='greenGradientText'></span>
                            <span id="taxValue" class="" style="font-size: 6px">- R$3,80</span>
                        </h4>
                    </div>
                </div>
                <div id='modal-withdraw-footer' class="modal-footer">
                    <button id="bt-confirm-withdrawal" class="btn btn-success"
                            style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">
                        <strong>Confirmar</strong></button>
                    <button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal" aria-label="Close"
                            style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">
                        <strong>Cancelar</strong></button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal Detalhes -->
    @include('sales::details')
    <!-- End Modal -->

    @push('scripts')
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="{{ asset('modules/finances/js/index.js?v=8') }}"></script>
    @endpush

@endsection





