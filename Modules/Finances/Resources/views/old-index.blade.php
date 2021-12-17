@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v=09'.uniqid()) }}">
@endpush

@section('content')

    <div class="page mb-0">
        {{-- Buttons Export --}}
        <div style="display: none" class="page-header container">
            <div class="row align-items-center">
                <div class="col-6 col-10">
                    <h1 class="page-title truncate" style="height: 32px;">Finanças</h1>
                </div>

                <div class="col-1 text-right" id="finances_export_btns" style="opacity: 0">
                    <div id="export-excel">
                        <div class="p-2 d-flex justify-content-end align-items-center">
                                            <span id="bt_get_csv_default"
                                                  class="o-download-cloud-1 icon-export btn mr-2 d-none d-md-block"
                                                  style="cursor: default"></span>
                            <div class="btn-group" role="group">
                                <button style="border-radius: 16px 0 0 16px" id="bt_get_sale_xls" type="button"
                                        class="btn btn-round btn-default btn-outline disabled btn-pill-left">.XLS
                                </button>
                                <button style="border-radius: 0 16px 16px 0" id="bt_get_sale_csv" type="button"
                                        class="btn btn-round btn-default btn-outline disabled btn-pill-right">
                                    .CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="page-content container" style="display:none">
            {{-- MENU TABS --}}
            <nav id="menu-tabs-view">
                <div class="nav-tabs-horizontal">
                    <div class="nav nav-tabs nav-tabs-line align-items-center flex-nowrap" id="nav-tab" role="tablist">
                        <a class="truncate nav-item nav-link active nav-link-finances-hide-export"
                           id="nav-home-tab"
                           data-toggle="tab"
                           href="#nav-transfers"
                           role="tab"
                           aria-controls="nav-home"
                           aria-selected="true"
                        >
                            Transferências
                        </a>
                        <a class="truncate nav-item nav-link nav-link-finances-show-export"
                           id="nav-statement-tab"
                           data-toggle="tab"
                           href="#nav-statement"
                           role="tab"
                           aria-controls="nav-statement"
                           aria-selected="true"
                        >
                            Agenda Financeira
                        </a>
                        <a class="nav-item nav-link s-config"
                           id="nav-settings-tab"
                           data-toggle="tab"
                           href="#nav-settings"
                           role="tab"
                           aria-controls="nav-settings"
                           aria-selected="true"
                        >
                            <img height="15" src="{{ asset('modules/global/img/svg/settings.svg') }}"/>
                        </a>
                    </div>
                </div>
            </nav>
            <div class="card-show-content-finances" style="display:none">
                {{-- TABS --}}
                <div id="tabs-view">
                    <div class="tab-content" id="nav-tabContent" style="min-height: 300px">
                        {{-- TRANSFERENCIAS --}}
                        <div class="tab-pane active"
                             id="nav-transfers"
                             role="tabpanel"
                             aria-labelledby="nav-home-tab">
                            <div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
                                <div class="flex-row justify-content-start align-items-center">
                                    <div class="col-12 mb-3 text-xs-center text-lg-left">
                                        <div class="alert alert-danger alert-dismissible fade show"
                                             id='blocked-withdrawal'
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
                                    <div class='container bg-gray sirius-radius'>
                                        <div class='row align-items-center my-20 py-20 d-none d-md-flex'
                                             style="position: relative">
                                            <div class="col-sm-3">
                                                <div id="div-available-money" class="price-holder pointer pl-10">
                                                    <h6 class="label-price mb-10"><b> Saldo Disponível </b></h6>
                                                    <h4 class="number saldoDisponivel"></h4>
                                                </div>
                                                <div class="s-border-left green"></div>
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
                                                <div class="input-group mb-3 align-items-center input-custom-transfer">
                                                    <div class="input-moeda">R$</div>
                                                    <input id="custom-input-addon" type="text"
                                                           class="form-control input-pad withdrawal-value"
                                                           placeholder="Digite o valor" aria-label="Digite o valor"
                                                           aria-describedby="basic-addon1"
                                                           style='border-radius: 0 12px 12px 0; border: none !important; border-left:1px solid #DDD !important;'>
                                                </div>
                                            </div>
                                            <div class="col-sm-3 pt-1">
                                                <button id="bt-withdrawal"
                                                        class="btn btn-success disabled btn-sacar mt-20"
                                                        data-toggle="modal"
                                                        style="border-radius: 8px;" disabled>
                                                    Sacar dinheiro
                                                </button>
                                            </div>
                                        </div>

                                        <div class='row align-items-center justify-content-center my-20 py-20 bg-white d-md-none'
                                             style="position: relative; height: 255px">
                                            <div class="col-md-12">
                                                <div id="div-available-money_m" class="price-holder pointer pl-10">
                                                    <h6 class="label-price mb-10"><b> Saldo Disponível </b></h6>
                                                    <h4 class="price saldoDisponivel"></h4>
                                                </div>
                                                <div class="s-border-left green"></div>
                                            </div>
                                            <div class="px-10 mt-10">
                                                <div class="col-md-12">
                                                    <div class="input-holder">
                                                        <label for="transfers_company_select_mobile"> Empresa</label>
                                                        <select style='border-radius:10px'
                                                                class="form-control select-pad"
                                                                name="company"
                                                                id="transfers_company_select_mobile"> </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 mt-10">
                                                    <label for="custom-input-addon"> Valor a transferir</label>
                                                    <div class="input-group mb-3 align-items-center input-custom-transfer">
                                                        <div class="input-moeda">R$</div>
                                                        <input id="custom-input-addon" type="text"
                                                               class="form-control input-pad withdrawal-value"
                                                               placeholder="Digite o valor" aria-label="Digite o valor"
                                                               aria-describedby="basic-addon1"
                                                               style='border-radius: 0 12px 12px 0; border: none !important; border-left:1px solid #DDD !important;'>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <button id="bt-withdrawal_m"
                                                        class="btn btn-success btn-sacar"
                                                        data-toggle="modal">
                                                    Sacar dinheiro
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class='container col-sm-12 mt-20 d-lg-block'>
                                <div class='row'>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <div class="price-holder">
                                                <h5 class="font-size-14 gray-600"> Saldo Pendente </h5>
                                                <h4 class="number saldoPendente"></h4>
                                            </div>
                                        </div>
                                        <div class="s-border-right yellow"></div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <div class="price-holder">
                                                <h5 class="font-size-14 gray-600"> Saldo Bloqueado </h5>
                                                <h4 class="number saldoBloqueado"></h4>
                                            </div>
                                        </div>
                                        <div class="s-border-right red"></div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <div class="price-holder">
                                                <h5 class="font-size-14 gray-600"> Saldo Total </h5>
                                                <h4 class="number saltoTotal"></h4>
                                            </div>
                                        </div>
                                        <div class="s-border-right blue"></div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <div class="price-holder">
                                                <h5 class="font-size-14 gray-600"> Débitos pendentes </h5>
                                                <h4 class="number saldoDebito" id="debit-value">
                                                    {{--                                                    <span class="currency font-size-30 bold debit-balance" style="color: #E61A1A;">- R$</span>--}}
                                                    {{--                                                    <span style="color:#959595">R$ </span>--}}
                                                    {{--                                                    <a href="javascript:;" id="go-to-pending-debt" class="currency debit-balance font-size-30 bold debit-balanc" style="color: #E61A1A;">0,00</a>--}}
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="s-border-right red"></div>
                                    </div>
                                </div>
                            </div>
                            <h4 class="d-md-none text-center mt-50 mb-30 bold font-size-20">
                                Histórico de transferências
                            </h4>
                            <div style="min-height: 300px" class="card">

                                <!-- Transferências -->
                                <div class="tab-pane active"
                                     id="nav-transfers"
                                     role="tabpanel"
                                     aria-labelledby="nav-home-tab">
                                    <table id='withdrawalsTable'
                                           class="table table-striped table-condensed unify">
                                        <thead>
                                        <tr>
                                            <td class="table-title" scope="col">Código Saque</td>
                                            <td class="table-title" scope="col">Conta</td>
                                            <td class="table-title" scope="col">Solicitação</td>
                                            <td class="table-title" scope="col">Liberação</td>
                                            <td class="table-title text-center" scope="col">Status</td>
                                            <td class="table-title" scope="col">Valor</td>
                                            <td scope="col" class="d-none d-md-block table-title"> &nbsp;</td>
                                        </tr>
                                        </thead>
                                        <tbody id="withdrawals-table-data" class="custom-t-body"
                                               img-empty="{!! asset('modules/global/img/extrato.svg')!!}">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row justify-content-center justify-content-md-end pr-md-15">
                                <ul id="pagination-withdrawals"
                                    class="d-inline-flex flex-wrap justify-content-center pl-10 mt-10">
                                    {{--js carrega...--}}
                                </ul>
                            </div>
                        </div>
                        {{--EXTRATO--}}
                        <div
                                class="tab-pane"
                                id="nav-statement"
                                role="tabpanel"
                                aria-labelledby="nav-statement-tab">
                            <div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
                                <div class="row justify-content-start align-items-center">
                                    <div class="col-md-8 fix-5 px-sm-15">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 text-xs-center text-lg-left" style="flex:1">
                                                <h5 class="title-pad"> Agenda Financeira </h5>
                                                <p class="sub-pad sub-pad-getnet px-2 pl-md-0">
                                                    Para você controlar o fluxo financeiro da sua empresa.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex justify-content-start justify-content-lg-end"
                                         id="statement-money">
                                        <div class="price-holder px-20 p-md-0" style="position: relative">
                                            <h6 class="label-price bold"> Saldo no período</h6>
                                            <h4 id="available-in-period-statement"
                                                style="font-weight: 700;font-size: 25px;display: inline;">
                                            </h4>
                                            <div style="height: 16px;"
                                                 class="d-none d-md-block s-border-top green mb-15"></div>
                                            <div class="d-md-none s-border-left green mb-15"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row justify-content-start align-items-center">
                                    <div class="p-20 pb-0">
                                        <div class="col-lg-12 mb-15">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="input-holder form-group">
                                                        <label for="statement_company_select">Empresa</label>
                                                        <select class="form-control select-pad" name="company"
                                                                id="statement_company_select">
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-3" style="display:none">
                                                    <div class="input-holder form-group">
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

                                                <div class="col-md-3">
                                                    <div class="form-group form-icons">
                                                        <label for="date_range_statement">Período</label>
                                                        <i style="right: 20px;"
                                                           class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                                                        <input name="date_range_statement" id="date_range_statement"
                                                               class="select-pad pr-30"
                                                               placeholder="Clique para editar..." readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="payment_method">Forma de pagamento</label>
                                                        <select name='payment_method' id="payment_method"
                                                                class="form-control select-pad">
                                                            <option value="ALL">Todos</option>
                                                            <option value="CREDIT_CARD">Cartão de crédito</option>
                                                            <option value="BANK_SLIP">Boleto</option>
                                                            <option value="PIX">PIX</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="statement_sale">
                                                            Transação <i style="font-weight: normal"
                                                                         class="o-question-help-1 ml-5 font-size-14"
                                                                         data-toggle="tooltip"
                                                                         title=""
                                                                         data-original-title="Se for passado esse valor, o extrato vai listar as informações dessa transação independente do filtro de data"></i>
                                                        </label>
                                                        <input name="statement_sale" id="statement_sale"
                                                               class="select-pad" placeholder="Transação">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="bt_collapse">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="input-holder form-group">
                                                            <label for="statement_status_select">Status</label>
                                                            <select class="form-control select-pad" name="status"
                                                                    id="statement_status_select">
                                                                <option value="ALL">Todos</option>
                                                                <option value="WAITING_FOR_VALID_POST">
                                                                    Aguardando postagem válida
                                                                </option>
                                                                <option value="WAITING_LIQUIDATION">Aguardando
                                                                    liquidação
                                                                </option>
                                                                <option value="WAITING_WITHDRAWAL">Aguardando saque
                                                                </option>
                                                                <option value="WAITING_RELEASE">Aguardando liberação
                                                                </option>
                                                                <option value="PAID">Liquidado</option>
                                                                <option value="REVERSED">Estornado</option>
                                                                <option value="ADJUSTMENT_CREDIT">Ajuste de crédito
                                                                </option>
                                                                <option value="ADJUSTMENT_DEBIT">Ajuste de débito
                                                                </option>
                                                                <option value="PENDING_DEBIT">Débitos pendentes</option>
                                                            </select>
                                                        </div>
                                                        <input name="withdrawal_id" id="withdrawal_id" type="hidden"
                                                               class="select-pad" placeholder="Id do Saque">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="row" style="height: 0">
                                                        <div class="col-6 col-xl-3 offset-xl-6 pr-0 mt-20">
                                                            <div
                                                                    class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                                                    data-toggle="collapse"
                                                                    data-target="#bt_collapse"
                                                                    aria-expanded="false"
                                                                    aria-controls="bt_collapse">
                                                                <img id="icon-filtro"
                                                                     src="{{ asset('/modules/global/img/svg/filter-2-line.svg') }}"/>
                                                                <span id="text-filtro">Filtros avançados</span>
                                                            </div>
                                                        </div>

                                                        <div class="col-6 col-xl-3 mt-20">
                                                            <div id="bt_filtro_statement"
                                                                 class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                                                <img style="height: 12px; margin-right: 4px"
                                                                     src="{{ asset('/modules/global/img/svg/check-all.svg') }}">
                                                                Aplicar filtros
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Aviso de Exportação -->
                            <div id="alert-finance-export"
                                 class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10"
                                 style="display:none;">
                                <div class="d-flex">
                                    <span class="o-info-help-1"></span>
                                    <div class="w-full ml-10">
                                        <strong class="font-size-16">Exportando seu relatório</strong>
                                        <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por
                                            e-mail para:
                                            <strong id="export-finance-email"></strong> e aparecerá nas suas
                                            notificações. Pode levar algum tempo, dependendo de quantos
                                            registros você estiver exportando.
                                        </p>
                                    </div>
                                    <i class="material-icons pointer" data-dismiss="alert">close</i>
                                </div>
                            </div>

                            <h4 class="d-md-none text-center mt-50 mb-30 bold font-size-20"> Acompanhe a agenda </h4>
                            <div class="card">
                                <table id="statementTable"
                                       class="table table-condensed unify table-striped">
                                    <thead>
                                    <tr>
                                        <td scope="col" class="headCenter table-title">Razão</td>
                                        <td scope="col" class="headCenter table-title">Data prevista
                                            <i style="font-weight: normal"
                                               class="o-question-help-1 ml-5 font-size-14"
                                               data-toggle="tooltip"
                                               title=""
                                               data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos"></i>
                                        </td>
                                        <td scope="col" class="headCenter table-title text-center">Status</td>
                                        <td scope="col" class="headCenter table-title">Valor</td>
                                    </tr>
                                    </thead>
                                    <tbody id="table-statement-body"
                                           img-empty="{!! asset('modules/global/img/geral-1.svg')!!}"
                                           class="custom-t-body table-statement-body-class">
                                    </tbody>
                                </table>
                            </div>
                            <div id="pagination-statement"
                                 class="pagination-sm margin-chat-pagination pagination-statement-class text-xs-center text-md-right"
                                 style="margin-top: 10px; position:relative;">
                            </div>
                        </div>

                        <div class="tab-pane"
                             id="nav-settings"
                             role="tabpanel"
                             aria-labelledby="nav-statement-tab">
                            <form id="finances-settings-form">
                                <div class="card shadow card-tabs p-20">
                                    <div class="row justify-content-start align-items-center">
                                        <div class="col-12 col-sm-8 text-left">
                                            <h5 class="title-pad">Configurações</h5>
                                            <p class="p-0 m-0">Configure as finanças do seu negócio</p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-holder form-group">
                                                <select style='border-radius:10px' class="form-control select-pad"
                                                        name="company" id="settings_company_select"></select>
                                            </div>
                                        </div>

                                        <div class="row d-contents d-md-flex align-items-start p-20">
                                            <div class="col-12 col-md-6 mb-50">
                                                <div class="card bg-light no-shadow mt-30">
                                                    <div class="card-body">
                                                        <h5 class="title-pad">
                                                            Saque automático por período
                                                            <label class="switch" style='float: right; top:3px'>
                                                                <input type="checkbox" id="withdrawal_by_period"
                                                                       name="withdrawal_by_period"
                                                                       class='check'>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </h5>
                                                        <p class="p-0 m-0">
                                                            Crie um saque automático de frequência diária, semanal ou
                                                            mensal.
                                                            <br/>
                                                            O valor será automaticamente solicitado quando superior a R$
                                                            100,00.
                                                        </p>
                                                        <br/>
                                                        <p class="mb-0">Frequência</p>
                                                        <div
                                                                class="frequency-container py-10 d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center">
                                                            <button type="button" data-frequency="daily"
                                                                    class="btn btn-block m-0 mr-5 py-10">
                                                                Diário
                                                            </button>

                                                            <button type="button" data-frequency="weekly"
                                                                    class="btn btn-block m-0 mx-5 py-10">
                                                                Semanal
                                                            </button>

                                                            <button type="button" data-frequency="monthly"
                                                                    class="btn btn-block m-0 ml-5 py-10">
                                                                Mensal
                                                            </button>
                                                        </div>

                                                        <div
                                                                class="weekdays-container d-flex flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                                            <button type="button" class="btn py-15" data-weekday="1">
                                                                SEG
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="2">
                                                                TER
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="3">
                                                                QUA
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="4">
                                                                QUI
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="5">
                                                                SEX
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="6">
                                                                SAB
                                                            </button>
                                                            <button type="button" class="btn py-15" data-weekday="0">
                                                                DOM
                                                            </button>
                                                        </div>
                                                        <div
                                                                class="day-container d-none flex-wrap flex-md-nowrap align-items-center justify-content-between mt-20">
                                                            @foreach (['01', '05', '10', '15', '20', '25', '30'] as $day)
                                                                <button type="button" class="btn py-15"
                                                                        data-day="{{$day}}">
                                                                    {{$day}}
                                                                </button>
                                                            @endforeach
                                                        </div>
                                                        <br/>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <button type="submit"
                                                                        class="btn btn-block btn-success btn-success-1 py-10 px-15">
                                                                    <img style="height: 12px; margin-right: 4px"
                                                                         src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">
                                                                    &nbsp;Salvar&nbsp;
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card bg-lighter no-shadow mt-30">
                                                    <div class="card-body">
                                                        <h5 class="title-pad">
                                                            Saque automático por valor
                                                            <label class="switch" style='float: right; top:3px'>
                                                                <input type="checkbox" id="withdrawal_by_value"
                                                                       name="withdrawal_by_value"
                                                                       class='check'>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </h5>
                                                        <p class="p-0 m-0">
                                                            Crie um saque automático quando o saldo disponível for
                                                            superior ao valor informado abaixo.
                                                            <br/>O valor deve ser superior a R$ 100,00.
                                                        </p>
                                                        <br/>
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">R$</span>
                                                            </div>
                                                            <input id="withdrawal_amount" name="withdrawal_amount"
                                                                   type="text"
                                                                   class="form-control"
                                                                   aria-label="Valor mínimo para saque">
                                                            {{--<div class="input-group-append">--}}
                                                            {{--    <span class="input-group-text">.00</span>--}}
                                                            {{--</div>--}}
                                                        </div>
                                                        <br/>
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <button type="submit"
                                                                        class="btn btn-block btn-default py-10 px-15">
                                                                    <img style="height: 12px; margin-right: 4px"
                                                                         src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">
                                                                    &nbsp;Salvar&nbsp;
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr style="border-top-color: transparent">
        @include('companies::empty')
        @include('companies::not_company_approved_getnet')

    <!-- Modal exportar relatorio -->
        <div id="modal-export-finance-getnet" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-10">
                    <div class='my-20 mx-20 text-center'>
                        <h3 class="black"> Informe o e-mail para receber o relatório </h3>
                    </div>
                    <div class="modal-footer">
                        <input type="email" id="email_finance_export">
                        <button type="button" class="btn btn-success btn-confirm-export-finance-getnet">
                            Enviar
                        </button>
                        <a id="btn-mobile-modal-close" class="btn btn-primary" style='color:white' role="button"
                           data-dismiss="modal" aria-label="Close">
                            Fechar
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->

        {{-- Modal confirmar saque --}}
        <div id="modal-withdrawal" class="modal fade modal-3d-flip-vertical " role="dialog" tabindex="-1">
            <div id="" class="modal-dialog modal-dialog-centered modal-simple">
                <div id="" class="modal-content modal-content-style">
                    <div class="modal-header header-modal simple-border-bottom modal-title-withdrawal"
                         style="height: 60px;">
                        <h3 id="modal-withdrawal-title" class="modal-title" style="color: #FFFFFF;"></h3>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-12">
                                <div id="modal-body-withdrawal" class="col-12 mt-30">

                                </div>
                            </div>
                            <div id="debit-pending-informations" class="col-12 mt-20"
                                 style="display:none;background:  0 0 no-repeat padding-box;">

                            </div>
                        </div>
                    </div>
                    <div id='modal-withdraw-footer' class="modal-footer mt-20">

                    </div>
                </div>
            </div>
        </div>
        {{-- End Modal --}}

    <!-- Modal detalhes da transação-->
        @include('finances::details')
    <!-- End Modal -->

        {{-- Modal Detalhes --}}
        @include('sales::details')
        {{-- End Modal --}}
        <link rel="stylesheet" href="{{asset('modules/finances/css/jPages.css?v=2125')}}">

        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
            <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
            <script src="{{ asset('modules/finances/js/old-index.js?v='. uniqid()) }}"></script>
            <script src="{{ asset('modules/finances/js/settings.js?v='. uniqid()) }}"></script>
        @endpush
    </div>

@endsection
