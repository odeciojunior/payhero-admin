@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('modules/sales/css/index.min.css') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=123') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=123') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
        <style>
            .select2-selection--single {
                border: 1px solid #dddddd !important;
                border-radius: 8px !important;
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
            .ajust-font{
                font-size: 15px;
            }
            @media only screen and (min-width: 768px){
                .col-md-4.card {
                    margin-right: 10px;
                    max-width: calc(33.33% - 10px);
                }
            }
            @media only screen and (min-width: 576px) and (max-width : 767px){
                .col-sm-6.card {
                    margin-right: 10px;
                    max-width: calc(50% - 10px);
                }
            }
            strong span{
                color: #57617c;
            }
            td.text-left.font-size-14 {
                padding: 15px !important;
            }
        </style>
    @endpush

    <!-- Page -->
    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Vendas com saldo bloqueado</h1>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div class="page-content container">
                <!-- Filtro -->
                <form id='filter_form'>
                    <div id="" class="card shadow p-20">
                        <div class="row">

                            <div class="col-sm-6 col-md-3">
                                <label for="projeto">Lojas</label>
                                <select name='select_project' id="project" class="sirius-select">
                                    <option value="">Todas lojas</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="plan">Plano</label>
                                <select name='plan' id="plan" class="form-control input-pad" style='width:100%;' data-plugin="select2">
                                    <option value="">Todos planos</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="forma">Forma de pagamento</label>
                                <select name='select_payment_method' id="forma" class="sirius-select">
                                    <option value="">Boleto e cartão de crédito</option>
                                    <option value="1">Cartão de crédito</option>
                                    <option value="2">Boleto</option>
                                    <option value="4">PIX</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="status">Status</label>
                                <select name='sale_status' id="status" class="sirius-select">
                                    <option value="">Todos status</option>
                                    <option value="1">Aprovado</option>
                                    <option value="24">Em disputa</option>
                                </select>
                            </div>
                        </div>

                        <div class="collapse" id="bt_collapse">
                            <div class="row mt-15">
                                <div class="col-sm-6 col-md-3">
                                    <label for="comprador">Transação</label>
                                    <input name='transaction' id="transaction" class="input-pad" placeholder="transação">
                                </div>

                                <div class="col-sm-6 col-md-3">
                                    <label for="comprador">Nome do cliente</label>
                                    <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                                </div>

                                <div class="col-sm-6 col-md-3">
                                    <label for="customer_document">CPF do cliente</label>
                                    <input name='customer_document' id="customer_document" class="input-pad" placeholder="CPF" data-mask="000.000.000-00">
                                </div>

                                <div class="col-sm-6 col-md-3">
                                    <label for="date_type">Data</label>
                                    <select name='date_type' id="date_type" class="sirius-select">
                                        <option value="start_date">Data do pedido</option>
                                        <option value="end_date">Data do pagamento</option>
                                    </select>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group form-icons">
                                        <label for="date_range">&nbsp;</label>
                                        <i style="right: 27px;top: 42px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-19"></i>
                                        <input name='date_range' id="date_range" class="input-pad pr-30" placeholder="Clique para editar..." readonly >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="height: 30px">
                            <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                                <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                     data-toggle="collapse"
                                     data-target="#bt_collapse"
                                     aria-expanded="false"
                                     aria-controls="bt_collapse">
                                    <img id="icon-filtro" class="hidden-xs-down" src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                    <span id="text-filtro">Filtros avançados</span>
                                </div>
                            </div>
                            <div class="col-6 col-xl-3 mt-20">
                                <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                    <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
                                    Aplicar filtros
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Resumo -->
                <div class="fixhalf"></div>
                <div class='container col-sm-12 d-lg-block'>
                    <div class='row'>
                        <div class="col-md-4 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h5 class="font-size-14 gray-600">Quantidade de vendas</h5>
                                <h4 id="total_sales" class="font-size-30 bold"></h4>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h5 class="font-size-14 gray-600">Saldo bloqueado</h5>
                                <h4 id="commission_blocked"></h4>
                            </div>
                            <div class="s-border-right yellow"></div>
                        </div>
                        <div class="col-md-4 col-sm-6 col-xs-12 card">
                            <div class="card-body">
                                <h5 class="font-size-14 gray-600">Valor Total </h5>
                                <h4 id="total"></h4>
                            </div>
                            <div class="s-border-right red"></div>
                        </div>
                    </div>
                </div>
                {{-- <div class="card shadow p-20" style='display:block;'>
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <h6 class="text-center green-gradient">
                                <i class="material-icons align-middle mr-1 green-gradient"> swap_vert </i> Quantidade de vendas
                            </h6>
                            <h4 id="total_sales" class="number text-center green-gradient"></h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center orange-gradient">
                                <i class="material-icons align-middle mr-1 orange-gradient"> attach_money </i> Saldo bloqueado
                            </h6>
                            <h4 id="commission_blocked" class="number text-center orange-gradient"></h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center green-gradient">
                                <i class="material-icons align-middle green-gradient mr-1"> trending_up </i> Valor Total </h6>
                            <h4 id="total" class="number text-center green-gradient">
                            </h4>
                        </div>
                    </div>
                </div> --}}

                <!-- Tabela -->
                <div class="fixhalf"></div>
                <div class="col-lg-12 p-0 pb-10">
                    <div class="card shadow" style="min-height: 300px">
                        <div class="page-invoice-table table-responsive">
                            <table id="tabela_vendas" class="table-vendas table table-striped unify" style="">
                                <thead>
                                <tr>
                                    <td class="table-title display-sm-none display-m-none  display-lg-none">Transação</td>
                                    <td class="table-title">Loja</td>
                                    <td class="table-title">Descrição</td>
                                    <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                    <td class="table-title">Forma</td>
                                    <td class="table-title text-center">Status</td>
                                    <td class="table-title display-sm-none display-m-none">Data</td>
                                    <td class="table-title display-sm-none">Pagamento</td>
                                    <td class="table-title">Comissão</td>
                                    <td class="table-title">Motivo bloqueio</td>
                                </tr>
                                </thead>
                                <tbody id="dados_tabela"  img-empty="{!! asset('modules/global/img/vendas.svg')!!}">
                                {{-- js carrega... --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center justify-content-md-end pb-60">
                    <ul id="pagination-sales" class="pagination-sm margin-chat-pagination " style="position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
        </div>
        {{-- Quando não tem loja cadastrado  --}}
            @include('projects::empty')
        {{-- FIM loja nao existem lojas--}}
    </div>


    @push('scripts')
        <script src='{{ mix('modules/reports/js/report-blockedbalance.min.js') }}'></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection

