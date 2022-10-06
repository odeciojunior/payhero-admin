@extends('layouts.master')

@section('content')
@push('css')
<link rel="stylesheet" href="{{ mix('build/layouts/reports/blockedbalance.min.css') }}">
<style>
    .select2-selection--single {
        border: 1px solid #dddddd !important;
        border-radius: 8px !important;
        height: 43px !important;
    }

    .select2-selection__rendered {
        color: #707070 !important;
        font-size: 16px !important;
        font-family: 'Inter', sans-serif;
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

    .ajust-font {
        font-size: 15px;
    }

    @media only screen and (min-width: 768px) {
        .col-md-4.card {
            margin-right: 10px;
            max-width: calc(33.33% - 10px);
        }
    }

    @media only screen and (min-width: 576px) and (max-width : 767px) {
        .col-sm-6.card {
            margin-right: 10px;
            max-width: calc(50% - 10px);
        }
    }

    strong span {
        color: #57617c;
    }

    td.text-left.font-size-14 {
        padding: 15px !important;
    }
</style>
@endpush

<!-- Page -->
<div class="page mb-0">

    @include('layouts.company-select', ['version' => 'mobile'])

    <div style="display: none" class="page-header container inner-header">
        @can('report_sales')
        <header class="top-system">
            <a href="{!! route('reports.finances') !!}" class="back">
                <svg style="margin-right: 10px;" width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M26 9C26.5523 9 27 8.55228 27 8C27 7.44772 26.5523 7 26 7V9ZM0.292892 7.29289C-0.0976315 7.68342 -0.0976315 8.31658 0.292892 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928932C7.68054 0.538408 7.04738 0.538408 6.65685 0.928932L0.292892 7.29289ZM26 7L1 7V9L26 9V7Z" fill="#636363" />
                </svg>
                Voltar para Financeiro
            </a>
        </header>
        @endcan

        <div class="row align-items-center justify-content-between top-inner-reports">
            <div class="col-8">
                <h1 class="d-flex title-system">
                    <span class="box-title ico-blocked">retido</span>
                    Saldo retido
                </h1>
                <!-- <span type="hidden" class="error-data"></span> -->
            </div>
            <!-- <div class="col-4">
                                <div class="box-projects">
                                    <select id='select_projects' class="sirius-select">
                                        {{-- JS carrega.. --}}
                                    </select>
                                </div>
                            </div> -->
        </div>
    </div>

    <div id="project-not-empty" style="display: none">

        <section class="container box-inner-reports" id="reports-content">
            <div class="row">
                <div class="col-12 box-items-finance pending">
                    <div class="row mb-20 pending-blocked">
                        @if (!auth()->user()->hasRole('attendance'))
                        <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                            <div class="finance-card border pink mb-10">
                                <span class="title">Total retido</span>
                                <div class="d-flex">
                                    <strong class="number" id="commission_blocked">0</strong>
                                </div>
                            </div>
                        </div>

                        <div class="fianance-items col-md-3 col-6 pr-5 pr-md-15">
                            <div class="finance-card border blue mb-10">
                                <span class="title">Quantidade de vendas</span>
                                <div class="d-flex">
                                    <strong class="number" id="total_sales">0</strong>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="page-content container inner-reports-content">
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
                            <label for="payment_method">Forma de pagamento</label>
                            <select name='payment_method' id="payment_method" class="sirius-select">
                                <option value="">Todas formas de pagamento</option>
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
                                <label for="transaction">Transação</label>
                                <input name='transaction' id="transaction" class="input-pad" placeholder="Transação">
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="client">Nome do cliente</label>
                                <input name='client' id="client" class="input-pad" placeholder="Cliente">
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="customer_document">CPF do cliente</label>
                                <input name='customer_document' id="customer_document" class="input-pad" placeholder="CPF" data-mask="000.000.000-00">
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label for="reason">Motivo</label>
                                <select name="reason" id="reason" class="sirius-select">
                                    <option value="">Todos</option>
                                    {{-- loaded via javascript --}}
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <label for="date_type">Data</label>
                                <select name='date_type' id="date_type" class="sirius-select">
                                    <option value="start_date">Data do pedido</option>
                                    <option value="end_date">Data do pagamento</option>
                                </select>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <label style="margin-bottom: 0.19rem;" for="date_range">‏‏‎ ‎</label>
                                <div class="col-12 mb-10 date-report">
                                    <div class="row align-items-center form-icons box-select">
                                        <input id="date-filter" type="text" name="daterange" class="font-size-14" value="" readonly>
                                        <i style="right:16px;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="height: 30px">
                        <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                            <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt_collapse" aria-expanded="false" aria-controls="bt_collapse">
                                <img style="visibility:hidden" id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} " />
                                <span id="text-filtro">Filtros avançados</span>
                                <img style="visibility:hidden" id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} " />
                            </div>
                        </div>
                        <div class="col-6 col-xl-3 mt-20">
                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                <!-- <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/check-all.svg') }} "/> -->
                                Aplicar filtros
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Resumo -->
            <div class="fixhalf"></div>
            <!-- <div class='container col-sm-12 d-lg-block'>
                        <div class='row'>
                            <div class="col-md-4 col-sm-6 col-xs-12 card">
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600">Quantidade de vendas</h5>
                                    <h4 id="total_sales" class="font-size-30 bold number"></h4>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 card">
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600">Saldo retido</h5>
                                    <h4 id="commission_blocked" class='number'></h4>
                                </div>
                                <div class="s-border-right yellow"></div>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12 card">
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600">Valor Total </h5>
                                    <h4 id="total" class='number'></h4>
                                </div>
                                <div class="s-border-right red"></div>
                            </div>
                        </div>
                    </div> -->
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
                                <i class="material-icons align-middle mr-1 orange-gradient"> attach_money </i> Saldo retido
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
                <div class="card shadow">
                    <div class="page-invoice-table table-responsive">
                        <table id="tabela_vendas" class="table-vendas table table-striped unify mb-0" style="">
                            <thead>
                                <tr>
                                    <td class="display-sm-none display-m-none  display-lg-none">Transação</td>
                                    <td class="">Loja</td>
                                    <td class="">Descrição</td>
                                    <td class=" display-sm-none display-m-none display-lg-none">Cliente</td>
                                    <td class="">Forma</td>
                                    <td class=" text-center">Status</td>
                                    <td class=" display-sm-none display-m-none">Data</td>
                                    <td class=" display-sm-none">Pagamento</td>
                                    <td class="">Comissão</td>
                                    <td class="">Motivo</td>
                                </tr>
                            </thead>
                            <tbody id="dados_tabela" img-empty="{!! mix('build/global/img/vendas.svg') !!}">
                                {{-- js carrega... --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row no-gutters justify-content-center justify-content-md-end pb-50">
                    <ul id="pagination-sales" class="pagination-sm margin-chat-pagination mb-20 pagination-style" style="position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>

        </div>
    </div>
    {{-- Quando não tem loja cadastrado --}}
    @include('projects::empty')
    {{-- FIM loja nao existem lojas --}}
</div>

@push('scripts')
<script src='{{ mix('build/layouts/reports/blockedbalance.min.js') }}'></script>
@endpush
@endsection
