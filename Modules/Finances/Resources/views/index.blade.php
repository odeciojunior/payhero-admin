@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=s0') }}">
    <style>
        .popover {
            left: -50px !important;
        }

        .disableFields {
            background-color: #f3f7f9;
            opacity: 1;
        }
    </style>
@endpush

@section('content')

    <div class="page">
        {{-- Buttons Export --}}
        <div style="display: none" class="page-header container">
            <div class="row">
                <div class="col-lg-6 mb-30">
                    <h1 class="page-title">Finanças</h1>
                </div>

            </div>
        </div>
        <div class="page-content container" style="display:none">
            <div class="card shadow card-show-content-finances" style="display:none">
                {{-- MENU TABS --}}
                <nav class="pt-20" id="menu-tabs-view" style="">
                    <div class="nav-tabs-horizontal">
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active"
                               id="nav-home-tab"
                               data-toggle="tab"
                               href="#nav-transfers"
                               role="tab"
                               aria-controls="nav-home"
                               aria-selected="true"
                            >
                                Transferências
                            </a>
                            <a class="nav-item nav-link"
                               id="nav-statement-tab"
                               data-toggle="tab"
                               href="#nav-statement"
                               role="tab"
                               aria-controls="nav-statement"
                               aria-selected="true"
                            >
                                Agenda financeira
                            </a>
                        </div>
                    </div>
                </nav>
                {{-- TABS --}}
                <div class="p-30 pt-20" id="tabs-view">
                    <div class="tab-content" id="nav-tabContent">
                        {{-- TRANSFERENCIAS --}}
                        <div class="tab-pane active"
                             id="nav-transfers"
                             role="tabpanel"
                             aria-labelledby="nav-home-tab"
                        >
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
<!--                                                <div class='input-group-prepend'>
                                                        <span class="input-group-text custom-addon" id="basic-addon1"
                                                              style="border-radius:10px 0 0 10px;background-color: white;height: auto; border: 1px solid #ddd;"><span
                                                                    class="currency">$</span></span>
                                                </div>-->
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
                        {{--EXTRATO--}}
                        <div
                             class="tab-pane"
                             id="nav-statement"
                             role="tabpanel"
                             aria-labelledby="nav-statement-tab">
                            <div class="row justify-content-start align-items-center">
                                <div class="col-12 fix-5">
                                    <div class="d-flex align-items-center">
                                        <div class="p-2" style="flex:1">
                                            <p class="sub-pad sub-pad-getnet">
                                                Para você controlar o fluxo financeiro da sua empresa.
                                            </p>
                                        </div>
                                        <div class="p-2" id="statement-money">
                                            <div class="price-holder">
                                                <h6 class="label-price"> Saldo no período</h6>
                                                <h4 id="available-in-period-statement"
                                                    style="font-weight: 700;font-size: 25px;display: inline;">
                                                </h4>
                                                <div class="grad-border green"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 mb-15">
                                    <div class="row">
                                        <div class="col-sm-6 col-md">
                                            <div class="input-holder">
                                                <label for="statement_company_select">Empresa</label>
                                                <select class="form-control select-pad" name="company"
                                                        id="statement_company_select">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md" style="display:none">
                                            <div class="input-holder">
                                                <label for="statement_data_type_select">Data</label>
                                                <select class="form-control select-pad"
                                                        name="statement_data_type_select"
                                                        id="statement_data_type_select">

                                                    <option value="schedule_date" selected>
                                                        Data
                                                    </option>
                                                    <option value="transaction_date">
                                                        Data da venda
                                                    </option>
                                                    <option value="liquidation_date">
                                                        Data da liquidação
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md">
                                            {{--<div class="form-group" style="margin-top:30px">
                                                <input name="date_range_statement" type="date"
                                                       id="date_range_statement_unique"
                                                       class="select-pad" placeholder="Clique para editar...">
                                            </div>--}}

                                            <div class="form-group">
                                                <label for="date_range_statement">Período</label>
                                                <input name="date_range_statement" id="date_range_statement"
                                                       class="select-pad" placeholder="Clique para editar..." readonly>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 col-md">
                                            <label for="forma">Forma de pagamento</label>
                                            <select name='payment_method' id="payment_method"
                                                    class="form-control select-pad">
                                                <option value="ALL">Todos</option>
                                                <option value="CREDIT_CARD">Cartão de crédito</option>
                                                <option value="BANK_SLIP">Boleto</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="input-holder">
                                                <label for="statement_status_select">Status</label>
                                                <select class="form-control select-pad" name="status"
                                                        id="statement_status_select">
                                                    <option value="ALL">Todos</option>
                                                    <option value="WAITING_FOR_VALID_POST">
                                                        Aguardando postagem válida
                                                    </option>
                                                    <option value="WAITING_LIQUIDATION">Aguardando liquidação</option>
                                                    <option value="WAITING_WITHDRAWAL">Aguardando saque
                                                    </option>
                                                    <option value="PAID">Liquidado</option>
                                                    <option value="REVERSED">Estornado</option>
                                                    <option value="ADJUSTMENT_CREDIT">Ajuste de crédito</option>
                                                    <option value="ADJUSTMENT_DEBIT">Ajuste de débito</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="statement_sale">
                                                    Transação <i class="material-icons gray ml-5 font-size-18"
                                                                 data-toggle="tooltip"
                                                                 title=""
                                                                 data-original-title="Se for passado esse valor, o extrato vai listar as informações dessa transação independente do filtro de data">help</i>
                                                </label>
                                                <input name="statement_sale" id="statement_sale"
                                                       class="select-pad" placeholder="Transação">
                                            </div>
                                        </div>
                                        <div class="mt-30 col-md-4" style="text-align:right">
                                            <button id="bt_filtro_statement"
                                                    class="btn btn-primary w-full">
                                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-12 mt-3">
                                    <table id="statementTable" class="table table-condensed unify table-striped">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="headCenter" style="width:30%">Razão</th>
                                            <th scope="col" class="headCenter" style="width:30%">Status</th>
                                            <th scope="col" class="headCenter" style="width:30%">Data prevista
                                                <i class="material-icons gray ml-5 font-size-18"
                                                   data-toggle="tooltip"
                                                   title=""
                                                   data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos">help</i>
                                            </th>
                                            <th scope="col" class="headCenter" style="width:10%">Valor</th>
                                        </tr>
                                        </thead>
                                        <tbody id="table-statement-body"
                                               class="custom-t-body table-statement-body-class">
                                        </tbody>
                                    </table>
                                    {{-- <section id="paginate">
                                         <div class="pagination"
                                              style="margin-top:10px;position:relative;float:right">
                                             <div class="numbers">
                                                 <div style=""></div>
                                             </div>
                                         </div>
                                     </section>--}}
                                    <div id="pagination-statement"
                                         class="pagination-sm margin-chat-pagination pagination-statement-class"
                                         style="margin-top:10px;position:relative;float:right">

                                    </div>

                                    {{--<ul id="pagination-statement"
                                        class="pagination-sm margin-chat-pagination pagination-statement-class"
                                        style="margin-top:10px;position:relative;float:right">
                                        --}}{{--js carrega...--}}{{--
                                    </ul>--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('companies::empty')
            @include('companies::not_company_approved_getnet')
        </div>

        {{-- Modal confirmar saque --}}
        <div id="modal-withdrawal" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
            <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                <div id="conteudo_modal_add" class="modal-content p-10">
                    <div class="header-modal simple-border-bottom">
                        <h2 id="modal-withdrawal-title" class="modal-title">Confirmar Saque</h2>
                    </div>
                    <div id="modal_body" class="modal-body simple-border-bottom"
                         style='padding-bottom:1%;padding-top:1%;'>
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
                        <button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal"
                                aria-label="Close"
                                style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">
                            <strong>Cancelar</strong></button>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Modal --}}

        {{-- Modal Detalhes --}}
        @include('sales::details')
        {{-- End Modal --}}
        <link rel="stylesheet" href="{{asset('modules/finances/css/jPages.css')}}">

        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
            <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
            <script src="{{ asset('modules/finances/js/index.js?v='. uniqid()) }}"></script>
        @endpush
    </div>

@endsection
