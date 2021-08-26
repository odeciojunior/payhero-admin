@extends("layouts.master")

@push('css')
    <link rel="stylesheet" href="{{ asset('modules/global/css/empty.css?v=03') }}">
    {{-- <link rel="stylesheet" href="{{ asset('modules/global/css/finances.css?v=11') }}"> --}}
    <link rel="stylesheet" href="{{ asset('modules/finances/css/new-finances.css?v=21'.uniqid()) }}">
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
                <div class="col-6 text-right" >
                    <div class="justify-content-end align-items-center d-none" id="export-excel">
                        <div class="p-2 align-items-center">
                            <span class="o-download-cloud-1 mr-2"></span>
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
            {{-- Aviso de Exportação --}}
            <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10"
                 style="display:none;">
                <div class="d-flex">
                    <span class="o-info-help-1"></span>
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
            {{-- TABS --}}
            <nav id="menu-tabs-view" style="display:none;">
                <div class="nav-tabs-horizontal">
                    <div class="nav nav-tabs nav-tabs-line" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active nav-link-finances-hide-export"
                           id="nav-home-tab"
                           data-toggle="tab"
                           href="#nav-transfers"
                           role="tab"
                           aria-controls="nav-home"
                           aria-selected="true"
                           style="display:none"
                        >
                            Transferências
                        </a>
                        <a class="nav-item nav-link nav-link-finances-show-export"
                           id="nav-profile-tab"
                           data-toggle="tab"
                           href="#nav-extract"
                           role="tab"
                           aria-controls="nav-profile"
                           aria-selected="true"
                        >
                            Extrato
                        </a>
                    </div>
                </div>
            </nav>
            <div>
                {{-- TABS --}}
                <div id="tabs-view" style="display:none">
                    <div class="tab-content" id="nav-tabContent">
                        {{-- TRANSFERENCIAS --}}
                        <div class="tab-pane fade show active"
                             id="nav-transfers"
                             role="tabpanel"
                             aria-labelledby="nav-home-tab"
                        >
                            <div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
                                <div class="flex-row justify-content-start align-items-center">
                                    <div class="col-12 mb-3 text-xs-center text-lg-left">
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
                                </div>
                            </div>
                            <div class="col-12 mb-3 mt-3">
                                <h5 class="card-title"> Histórico de saques </h5>
                            </div>
                            <div style="min-height: 300px" class="card">
                                <table id='withdrawalsTable' class="table table-striped table-condensed unify">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="table-title">Conta</th>
                                            <th scope="col" class="table-title">Solicitação</th>
                                            <th scope="col" class="table-title">Liberação</th>
                                            <th scope="col" class="table-title">Valor</th>
                                            <th style="display: none" id="col_transferred_value" scope="col">Valor
                                                transferido
                                            </th>
                                            <th scope="col" class="table-title">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="withdrawals-table-data" class="custom-t-body" img-empty="{!! asset('modules/global/img/extrato.svg')!!}">
                                    </tbody>
                                </table>
                            </div>
                            <div class="row justify-content-center justify-content-md-end pr-md-15">
                                <ul id="pagination-withdrawals"
                                    class="d-inline-flex flex-wrap justify-content-center pl-10 mt-10">
                                    {{--js carrega...--}}
                                </ul>
                            </div>
                        </div>
                        {{--EXTRATO--}}
                        <div class="tab-pane fade"
                             id="nav-extract"
                             role="tabpanel"
                             aria-labelledby="nav-profile-tab">
                            <div class="card shadow card-tabs py-15 px-0 px-md-15 mb-50">
                                <div class="row justify-content-start align-items-center">
                                    <div class="col-md-8 fix-5 px-sm-15">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2" style="flex:1">
                                                <h5 class="title-pad"> Extrato </h5>
                                                <p class="sub-pad"> Pra você controlar tudo que entra e sai da sua conta.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex justify-content-start justify-content-lg-end">
                                        <div class="price-holder px-20 p-md-0" style="position: relative">
                                            <h6 class="label-price bold"> Saldo no período</h6>
                                            <h4 id="available-in-period"
                                                style="font-weight: 700;font-size: 25px;display: inline;">
                                            </h4>
                                            <div style="height: 16px;"
                                                 class="d-none d-md-block s-border-top green mb-15"></div>
                                            <div class="d-md-none s-border-left green mb-15"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-start align-items-center">
                                    <div class="col-12 p-20 pb-0">
                                        <div class="col-lg-12 mb-15">
                                            <div class="row align-items-center">
                                                <div class="col-sm-6 col-md-3 col-lg-3">
                                                    <div class="input-holder form-group">
                                                        <label for="extract_company_select">Empresa</label>
                                                        <select class="form-control select-pad" name="company"
                                                                id="extract_company_select"> </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3 col-lg-3">
                                                    <div class="form-group">
                                                        <label for="reason">Razão</label>
                                                        <input type="text" id="reason" class="form-control select-pad"
                                                            placeholder="Digite a razão. Ex.: Saque">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3 col-lg-3">
                                                    <div class="form-group">
                                                        <label for="transaction">Transação/Antecipação</label>
                                                        <input type="text" id="transaction" class="form-control select-pad"
                                                            placeholder="Digite o código">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6 col-md-3 col-lg-3">
                                                    <div class="input-holder form-group">
                                                        <label for="type">Tipo</label>
                                                        <select class="form-control select-pad" id="type">
                                                            <option value="">Todos</option>
                                                            <option value="in">Entrada</option>
                                                            <option value="out">Saída</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="bt_collapse">
                                                <div class="row">
                                                    <div class="col-sm-6 col-md-3 col-lg-3">
                                                        <div class="form-group">
                                                            <label for="transaction-value">Valor</label>
                                                            <input type="text" id="transaction-value"
                                                                class="form-control select-pad withdrawal-value"
                                                                placeholder="Digite o valor">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-3 col-lg-3">
                                                        <div class="input-holder form-group">
                                                            <label for="date_type">Data</label>
                                                            <select class="form-control select-pad" id="date_type">
                                                                <option value="transfer_date">Data da transferência</option>
                                                                {{--                                                    <option value="transaction_date">Data da transação</option>--}}
                                                                <option value="sale_start_date">Data da venda</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6 col-md-3 col-lg-3">
                                                        <div class="form-group">
                                                            <input name="date_range" id="date_range" class="select-pad mt-30"
                                                            placeholder="Clique para editar..." readonly>
                                                        </div>
                                                    </div>
                                                    {{-- <div class="col-sm-6 col-md-3 col-lg-3 mt-30">
                                                        <button id="bt_filtro" class="btn btn-primary w-full">
                                                            <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar
                                                        </button>
                                                    </div> --}}
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
                                                            <div id="bt_filtro"
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
                            <div style="min-height: 300px" class="card">
                                <table id='transfersTable' class="table table-striped table-condensed unify">
                                    <thead>
                                    <tr>
                                        <th scope="col" class='headCenter' style='width:33%'>Razão</th>
                                        <th scope="col" class='headCenter' style='width:33%'>Data da transferência
                                        </th>
                                        <th scope="col" class='headCenter' style='width:34%'>Valor</th>
                                    </tr>
                                    </thead>
                                    <tbody id="table-transfers-body" class="custom-t-body" img-empty="{!! asset('modules/global/img/geral-1.svg')!!}">
                                    </tbody>
                                </table>
                                <ul id="pagination-transfers" class="pagination-sm margin-chat-pagination"
                                    style="margin-top:10px;position:relative;float:right">
                                    {{--js carrega...--}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('companies::empty')
        </div>

        <!-- Modal exportar relatorio -->
        <div id="modal-export-old-finance-getnet" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-simple">
                <div class="modal-content p-10">
                    <div class='my-20 mx-20 text-center'>
                        <h3 class="black"> Informe o email para receber o relatório </h3>
                    </div>
                    <div class="modal-footer">
                        <input type="email" id="email_finance_export">
                        <button type="button" class="btn btn-success btn-confirm-export-old-finance-getnet">
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
            <div id="modal_add_size" class="modal-dialog modal-dialog-centered modal-simple ">
                <div id="conteudo_modal_add" class="modal-content modal-content-style">
                    <div class="modal-header header-modal simple-border-bottom modal-title-withdrawal" style="height: 60px;">
                        <h2 id="modal-withdrawal-title" class="modal-title" style="color: #FFFFFF;">Confirmar Saque</h2>
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
                            <div class="row">
                                <div class="col-md-8 mt-10">
                                    <p style="color: #5A5A5A;" class="text-uppercase">Valor do saque:</p>
                                </div>
                                <div class="col-md-4 mt-10 text-right">
                                    <span id="modal-withdrawal-value" class='greenGradientText'></span>
                                    <span id="taxValue" class="" style="font-size: 6px">- R$3,80</span>
                                </div>
                            </div>
                            {{-- <h4>Valor do saque:
                                <span id="modal-withdrawal-value" class='greenGradientText'></span>
                                <span id="taxValue" class="" style="font-size: 6px">- R$3,80</span>
                            </h4> --}}
                        </div>
                    </div>
                    <div id="modal-withdraw-footer" class="modal-footer mt-20">
                        <div class="col-md-12 text-center">
                            <button id="bt-cancel-withdrawal" class="btn col-5 s-btn-border" data-dismiss="modal" aria-label="Close" style="font-size:20px; width:200px; border-radius: 12px; color:#818181;">
                                Cancelar
                            </button>

                            <button id="bt-confirm-withdrawal" class="btn btn-success col-5 btn-confirmation s-btn-border" style="background-color: #41DC8F;font-size:20px; width:200px;">
                                <strong>Confirmar</strong>
                            </button>
                        </div>
                    </div>
                    {{-- <div id='modal-withdraw-footer' class="modal-footer">
                        <button id="bt-confirm-withdrawal" class="btn btn-success"
                                style="background-image: linear-gradient(to right, #23E331, #44A44B);font-size:20px; width:100%">
                            <strong>Confirmar</strong></button>
                        <button id="bt-cancel-withdrawal" class="btn btn-success" data-dismiss="modal"
                                aria-label="Close"
                                style="background-image: linear-gradient(to right, #e6774c, #f92278);font-size:20px; width:100%">
                            <strong>Cancelar</strong></button>
                    </div> --}}
                </div>
            </div>
        </div>
        {{-- End Modal --}}

        {{-- Modal Detalhes --}}
        @include('sales::details')
        {{-- End Modal --}}

        @push('scripts')
            <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
            <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
            <script src="{{ asset('modules/finances/js/old-index.js?v='. uniqid()) }}"></script>
        @endpush
    </div>

@endsection
