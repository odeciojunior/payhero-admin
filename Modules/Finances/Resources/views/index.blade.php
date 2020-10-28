@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=1') }}">
    <style>
        .popover {
            left: -50px !important;
        }
    </style>
@endpush

@section('content')

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-lg-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                </div>
                <div class="col-6 text-right">
                    <div class="justify-content-end align-items-center" id="export-excel" style="display:none;">
                        <div class="p-2 align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20"
                                 viewBox="0 0 24 24">
                                <path
                                        d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                            </svg>
                            <div class="btn-group" role="group">
                                <button id="bt_get_xls" type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-left">.XLS
                                </button>
                                <button id="bt_get_csv" type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-right">.CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container" style="display:none">
            <!-- Aviso de Exportação -->
            <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10"
                 style="display:none;">
                <div class="d-flex">
                    <i class="material-icons mr-10">info</i>
                    <div class="w-full">
                        <strong class="font-size-16">Exportando seu relatório</strong>
                        <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por e-mail para:
                            <strong id="export-email"></strong> e aparecerá nas suas notificações. Pode levar algum
                            tempo, dependendo de quantos registros você estiver exportando.
                        </p>
                    </div>
                    <i class="material-icons pointer" data-dismiss="alert">close</i>
                </div>
            </div>
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
                            <a class="nav-item nav-link" id="nav-statement-tab" data-toggle="tab" href="#nav-statement"
                               role="tab" aria-controls="nav-statement" aria-selected="true" style='display:none;'>Extrato
                                2.0
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
                                    <div class="alert alert-danger alert-dismissible fade show" id='blocked-withdrawal'
                                         role="alert" style='display:none;'>
                                        <strong>Saque bloqueado!</strong> Entre em contato com o suporte para mais
                                        informações.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <h5 class="title-pad"> Nova transferência </h5>
                                    <p class="sub-pad"> Saque o dinheiro para sua conta bancária.
                                    </p>
                                </div>
                                <div class='container'>
                                    <div class='row align-items-center my-20'>
                                        <div class="col-sm-3">
                                            <div id="div-available-money" class="price-holder pointer">
                                                <h6 class="label-price mb-10"> Saldo Disponível </h6>
                                                <h4 class="price saldoDisponivel">
                                                </h4>
                                                <div class="grad-border green"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="input-holder">
                                                <label for="transfers_company_select"> Empresa</label>
                                                <select style='border-radius:10px' class="form-control select-pad"
                                                        name="company"
                                                        id="transfers_company_select"> </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label for="custom-input-addon"> Valor a transferir</label>
                                            <div class="input-group mb-3"
                                                 style='padding:0'>
                                                <div class='input-group-prepend'>
                                                        <span class="input-group-text custom-addon" id="basic-addon1"
                                                              style="border-radius:10px 0 0 10px;background-color: white;height: auto; border: 1px solid #ddd;"><span
                                                                    class="currency">$</span></span>
                                                </div>
                                                <input id="custom-input-addon" type="text"
                                                       class="form-control input-pad withdrawal-value"
                                                       placeholder="Digite o valor" aria-label="Digite o valor"
                                                       aria-describedby="basic-addon1"
                                                       style='border-radius: 0 10px 10px 0'>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 pt-1">
                                            <button id="bt-withdrawal" class="btn btn-success disabled btn-sacar mt-20"
                                                    data-toggle="modal" disabled>
                                                <svg class="mr-2" style="fill: white; vertical-align: middle;"
                                                     xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     viewBox="0 0 24 24">
                                                    <path
                                                            d="M20.285 2l-11.285 11.567-5.286-5.011-3.714 3.716 9 8.728 15-15.285z"></path>
                                                </svg>
                                                Sacar dinheiro
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class='container col-sm-12 mb-40'>
                                    <div class='row'>
                                        <div class="col-sm-3 div-antecipable-balance">
                                            <div class="price-holder" style='cursor: pointer;'>
                                                <h6 class="label-price mb-15"> Saldo Antecipável </h6>
                                                <h4 class="price saldoAntecipavel">
                                                </h4>
                                                <div class="grad-border purple"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 ">
                                            <div class="price-holder">
                                                <h6 class="label-price mb-15"> Saldo Pendente </h6>
                                                <h4 class="price saldoPendente">
                                                </h4>
                                                <div class="grad-border red"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 ">
                                            <div class="price-holder">
                                                <h6 class="label-price mb-15"> Saldo Bloqueado </h6>
                                                <h4 class="price saldoBloqueado">
                                                </h4>
                                                <div class="grad-border red"></div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 ">
                                            <div class="price-holder">
                                                <h6 class="label-price mb-15"> Saldo Total </h6>
                                                <h4 class="price saltoTotal">
                                                </h4>
                                                <div class="grad-border blue"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
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
                                            <th style="display: none" id="col_transferred_value" scope="col">Valor
                                                transferido
                                            </th>
                                            <th scope="col">Status</th>
                                        </tr>
                                        </thead>
                                        <tbody id="withdrawals-table-data" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-withdrawals" class="pagination-sm margin-chat-pagination"
                                        style="margin-top:10px;position:relative;float:right">
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
                                                <h4 id="available-in-period"
                                                    style="font-weight: 700;font-size: 25px;display: inline;">
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
                                            <input type="text" id="reason" class="form-control select-pad"
                                                   placeholder="Digite a razão. Ex.: Saque">
                                        </div>
                                        <div class="col-sm-6 col-md-3 col-lg-3">
                                            <label for="transaction">Transação/Antecipação</label>
                                            <input type="text" id="transaction" class="form-control select-pad"
                                                   placeholder="Digite o código">
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
                                            <input type="text" id="transaction-value"
                                                   class="form-control select-pad withdrawal-value"
                                                   placeholder="Digite o valor">
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
                                            <input name="date_range" id="date_range" class="select-pad mt-30"
                                                   placeholder="Clique para editar..." readonly>
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
                                            <th scope="col" class='headCenter' style='width:33%'>Data da transferência
                                            </th>
                                            <th scope="col" class='headCenter' style='width:34%'>Valor</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-transfers-body" class="custom-t-body">
                                        </tbody>
                                    </table>
                                    <ul id="pagination-transfers" class="pagination-sm margin-chat-pagination"
                                        style="margin-top:10px;position:relative;float:right">
                                        {{--js carrega...--}}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- extrato 2.0 --}}
                        <div class="tab-pane fade" id="nav-statement" role="tabpanel"
                             aria-labelledby="nav-statement-tab">
                            <div class="row justify-content-between">
                                <div class="col-lg-12 mb-15">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-4 col-lg-3">
                                            <div class="input-holder">
                                                <label for="statement_company_select">Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="statement_company_select">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-lg-3">
                                            <div class="form-group">
                                                <label for="date_range_statement">Data da venda</label>
                                                <input name="date_range_statement" type="date"
                                                       id="date_range_statement_unique"
                                                       class="select-pad" placeholder="Clique para editar...">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-lg-3">
                                            <div class="form-group">
                                                <label for="date_range_statement">Transação</label>
                                                <input name="statement_sale" id="statement_sale"
                                                       class="select-pad" placeholder="Transação">
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-4 col-lg-3">
                                            <div class="input-holder">
                                                <label for="statement_status_select">Status</label>
                                                <select class="form-control select-pad" name="status"
                                                        id="statement_status_select">
                                                    <option value="all">Todos</option>
                                                    <option value="1">Aguardando postagem válida</option>
                                                    <option value="2">Aguardando liquidação</option>
                                                    <option value="3">Pago</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="float-right col-sm-4 col-md-2 col-lg-2 mt-10">
                                        <button id="bt_filtro_statement" class="btn btn-primary w-full">
                                            <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 mt-3">
                                    <table id="statementTable" class="table table-condensed unify table-striped">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="headCenter" style="width:33%">Razão</th>
                                            <th scope="col" class="headCenter" style="width:33%">Status</th>
                                            <th scope="col" class="headCenter" style="width:33%">Data prevista
                                                <i class="material-icons gray ml-5 font-size-18" data-toggle="tooltip"
                                                   title=""
                                                   data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos">help</i>
                                            </th>
                                            <th scope="col" class="headCenter" style="width:34%">Valor</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-statement-body" class="custom-t-body">
                                        </tbody>
                                    </table>

                                    <section id="paginate">
                                        <div class="pagination" style="margin-top:10px;position:relative;float:right">
                                            <div class="numbers">
                                                <div style=""></div>
                                            </div>
                                        </div>
                                    </section>

                                    <ul id="pagination-statement" class="pagination-sm margin-chat-pagination"
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
        <script src="{{ asset('modules/finances/js/index.js?v=1') }}"></script>
    @endpush

@endsection
