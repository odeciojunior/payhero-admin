@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=02') }}">
    <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=s1') }}">
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

                <div class="col-lg-6 text-right d-none" id="finances_export_btns">
                    <div class="justify-content-end align-items-center" id="export-excel" style="">
                        <div class="p-2 d-flex justify-content-end align-items-center">
                                            <span id="bt_get_csv_default"
                                                  class="o-download-cloud-1 icon-export btn mr-2"></span>
                            <div class="btn-group" role="group">
                                <button id="bt_get_sale_xls" type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-left">.XLS
                                </button>
                                <button id="bt_get_sale_csv" type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-right">
                                    .CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="page-content container" style="display:none">
            <div class="card shadow card-show-content-finances" style="display:none">
                {{-- MENU TABS --}}
                <nav class="pt-20" id="menu-tabs-view" style="">
                    <div class="nav-tabs-horizontal">
                        <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active nav-link-finances-hide-export"
                               id="nav-home-tab"
                               data-toggle="tab"
                               href="#nav-transfers"
                               role="tab"
                               aria-controls="nav-home"
                               aria-selected="true"
                            >
                                Transferências
                            </a>
                            <a class="nav-item nav-link nav-link-finances-show-export"
                               id="nav-statement-tab"
                               data-toggle="tab"
                               href="#nav-statement"
                               role="tab"
                               aria-controls="nav-statement"
                               aria-selected="true"
                            >
                                Agenda financeira
                            </a>
                            <a class="nav-item nav-link"
                               id="nav-statement-tab"
                               data-toggle="tab"
                               href="#nav-settings"
                               role="tab"
                               aria-controls="nav-settings"
                               aria-selected="true"
                            >
                                <img height="24" width="24" src="{{ asset('modules/global/img/svg/settings.svg') }}"/>
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
                                <div class="col-12 mb-3">
                                    <div class="alert alert-info alert-dismissible fade show text-center" id=''
                                         role="alert" style="background: #DCECFF 0% 0% no-repeat padding-box;
                                            border: 2px solid #4A89F5;
                                            border-radius: 15px;
                                            opacity: 1;">
                                        <strong>ATENÇÃO!</strong><br> Após a liberação do saque, o prazo para a iniciar
                                        a
                                        liquidação é de 2 dias úteis.
                                        <br>
                                        As informações necessárias podem ser acompanhadas na tela Finanças > Extrato >
                                        Agenda Financeira.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                </div>
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
                                                <span style="-webkit-text-stroke: 1.45px #FFF;"
                                                      class="o-checkmark-1 white font-size-16"></span>
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
                                        <div class="col-sm-3 ">
                                            <div class="price-holder">
                                                <h6 class="label-price mb-15"> Débitos pendentes </h6>
                                                <h4 class="price saldoDebito" id="debit-value">
                                                </h4>
                                                <div class="grad-border red"></div>
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
                                                    <option value="WAITING_WITHDRAWAL">Aguardando saque</option>
                                                    <option value="WAITING_RELEASE">Aguardando liberação</option>
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
                                        <div class="mt-30 col-md-4" style="text-align:right">
                                            <button id="bt_filtro_statement"
                                                    class="btn btn-primary w-full">
                                                <img style="height: 12px; margin-right: 4px"
                                                     src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <!-- Aviso de Exportação -->
                                <div id="alert-finance-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                                    <div class="d-flex">
                                        <span class="o-info-help-1"></span>
                                        <div class="w-full">
                                            <strong class="font-size-16">Exportando seu relatório</strong>
                                            <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por e-mail para:
                                                <strong id="export-finance-email"></strong> e aparecerá nas suas notificações. Pode levar algum tempo, dependendo de quantos registros você estiver exportando.
                                            </p>
                                        </div>
                                        <i class="material-icons pointer" data-dismiss="alert">close</i>
                                    </div>
                                </div>
                                <!-- Resumo -->

                                <div class="col-12 mt-3">
                                    <table id="statementTable" class="table table-condensed unify table-striped">
                                        <thead>
                                        <tr>
                                            <th scope="col" class="headCenter" style="width:30%">Razão</th>
                                            <th scope="col" class="headCenter" style="width:30%">Status</th>
                                            <th scope="col" class="headCenter" style="width:30%">Data prevista
                                                <i style="font-weight: normal"
                                                   class="o-question-help-1 ml-5 font-size-14"
                                                   data-toggle="tooltip"
                                                   title=""
                                                   data-original-title="A comissão será transferida somente após informar códigos de rastreio válidos"></i>
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

                        <div class="tab-pane"
                             id="nav-settings"
                             role="tabpanel"
                             aria-labelledby="nav-statement-tab">
                            <form id="finances-settings-form">
                                <div class="row justify-content-start align-items-center">
                                    <div class="col-12 col-sm-8 text-left">
                                        <h5 class="title-pad">Configurações</h5>
                                        <p class="p-0 m-0">Configure as finanças do seu negócio</p>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="input-holder">
                                            <select style='border-radius:10px' class="form-control select-pad"
                                                    name="company" id="settings_company_select"></select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6">
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
                                                    Crie um saque automático de frequência diária, semanal ou mensal
                                                </p>
                                                <br/>
                                                <p class="mb-0">Frequência</p>
                                                <div
                                                    class="frequency-container py-10 d-flex justify-content-between align-items-center">
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
                                                    class="weekdays-container d-flex align-items-center justify-content-between mt-20">
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="0">SEG
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="1">TER
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="2">QUA
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="3">QUI
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="4">SEX
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="5">SAB
                                                    </button>
                                                    <button type="button" class="btn p-15"
                                                            data-weekday="6">DOM
                                                    </button>
                                                </div>
                                                <div class="day-container d-none flex-column mt-20">
                                                    <p class="mb-0">Dia do <b>mês</b></p>
                                                    <select style='border-radius:10px' class="form-control select-pad"
                                                            name="company" id="settings_company_select">
                                                        @for ($i = 1; $i <= 31; $i++)
                                                            <option value="{{$i}}">{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-5">
                                                        <button type="submit"
                                                                class="btn btn-block btn-success py-10 px-15">
                                                            <img style="height: 12px; margin-right: 4px"
                                                                 src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">
                                                            &nbsp;Salvar&nbsp;
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-sm-6">
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
                                                    Crie um saque automático quando o saldo disponível for superior ao
                                                    valor informado abaixo
                                                </p>
                                                <br/>
                                                <div class="input-group">
                                                    <div class="input-group-append">R$</div>
                                                    <input type="text" class="form-control"/>
                                                </div>
                                                <br/>
                                                <div class="row">
                                                    <div class="col-5">
                                                        <button type="button"
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
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @include('companies::empty')
            @include('companies::not_company_approved_getnet')
        </div>

        <!-- Modal exportar relatorio -->
        <div id="modal-export-finance-getnet" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-10">
                    <div class='my-20 mx-20 text-center'>
                        <h3 class="black"> Informe o email para receber o relatório </h3>
                    </div>
                    <div class="modal-footer">
                        <input type="email" id="email_finance_export">
                        <button type="button" class="btn btn-success btn-confirm-export-finance-getnet">
                            Enviar
                        </button>
                        <a id="btn-mobile-modal-close" class="btn btn-primary" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
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
                        <h3 id="modal-withdrawal-title" class="modal-title" style="color: #FFFFFF;">Confirmar Saque</h3>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-12">
                                <div id="modal-body-withdrawal" class="col-12 mt-30">

                                </div>
                            </div>
                            <div id="debit-pending-informations" class="col-12 mt-20" style="display:none;background:  0 0 no-repeat padding-box;">
                                <div class="col-12">
                                    <h3 class="text-center mt-10" id="text-title-debit-pending"> Débitos pendentes</h3>
                                    <p style="color: #959595;" class="text-center">Você tem alguns valores em aberto, confira:</p>
                                    <div id="debit-itens">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id='modal-withdraw-footer' class="modal-footer">

                        <div class="col-md-8">
                            <button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal"
                                    aria-label="Close"
                                    style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">
                                <strong>Cancelar</strong>
                            </button>

                            <button id="bt-confirm-withdrawal" class="btn btn-success"
                                    style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">
                                <strong>Confirmar</strong>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Modal --}}

        {{-- Modal Detalhes --}}
        @include('sales::details')
        {{-- End Modal --}}
        <link rel="stylesheet" href="{{asset('modules/finances/css/jPages.css?v=2123')}}">

        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
            <script src="{{ asset('modules/finances/js/jPages.min.js') }}"></script>
            <script src="{{ asset('modules/finances/js/index.js?v=222333'. uniqid()) }}"></script>
        @endpush
    </div>

@endsection
