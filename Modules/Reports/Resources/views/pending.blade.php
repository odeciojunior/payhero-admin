@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! mix('build/layouts/reports/pending.min.css') !!}">
        <style>
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
        </style>
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container inner-header">
            @can('report_sales')
            <header class="top-system">
                <a href="{!! route('reports.finances') !!}" class="back">
                    <svg style="margin-right: 10px;" width="27" height="16" viewBox="0 0 27 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M26 9C26.5523 9 27 8.55228 27 8C27 7.44772 26.5523 7 26 7V9ZM0.292892 7.29289C-0.0976315 7.68342 -0.0976315 8.31658 0.292892 8.70711L6.65685 15.0711C7.04738 15.4616 7.68054 15.4616 8.07107 15.0711C8.46159 14.6805 8.46159 14.0474 8.07107 13.6569L2.41421 8L8.07107 2.34315C8.46159 1.95262 8.46159 1.31946 8.07107 0.928932C7.68054 0.538408 7.04738 0.538408 6.65685 0.928932L0.292892 7.29289ZM26 7L1 7V9L26 9V7Z" fill="#636363"/>
                    </svg>
                    Voltar para Financeiro
                </a>
            </header>
            @endcan

            <div class="row align-items-center justify-content-between top-inner-reports">
                <div class="col-8">
                    <h1 class="d-flex title-system">
                        <span class="box-title ico-pending">Pendente</span>
                        Saldo pendente
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
                        @if(!auth()->user()->hasRole('attendance'))
                            <div class="fianance-items box-inner-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border orange mb-10 block-result">
                                    <span class="title">Total pendente</span>
                                    <div class="d-flex">
                                        <span class="detail"></span>
                                        <strong class="number" id='total-pending'>0</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="fianance-items box-inner-items col-md-3 col-6 pr-5 pr-md-15">
                                <div class="finance-card border blue mb-10 block-result">
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

            <div id="reports-content" class="page-content container inner-reports-content">
                <div class="row justify-content-between">
                    <div class="col-lg-12">
                        <form id='filter_form'>
                            <div id="" class="card shadow p-20">
                                <div class="row align-items-baseline">
                                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                        <label for="company">Empresa</label>
                                        <select name='select_company' id="company" class="sirius-select">
                                            <option value="0">Todas as empresas</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                        <label for="project">Lojas</label>
                                        <select name='select_project' id="project" class="sirius-select">
                                            <option value="0">Todas as lojas</option>
                                        </select>
                                    </div>

                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="client">Nome do cliente</label>
                                        <input name='client' id="client" class="input-pad" placeholder="Digite o nome">
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="customer_document">CPF do cliente</label>
                                        <input
                                            name='customer_document'
                                            id="customer_document"
                                            class="input-pad default-border"
                                            placeholder="Digite o CPF"
                                            data-mask="000.000.000-00"
                                        >
                                    </div>
                                </div>
                                <div class="collapse pt-20" id="bt_collapse">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-3">
                                            <label for="payment_method">Forma de pagamento</label>
                                            <select name='select_payment_method' id="payment_method" class="sirius-select">
                                                <option value="">Todas formas de pagamento</option>
                                                <option value="1">Cartão de crédito</option>
                                                <option value="2">Boleto</option>
                                                <option value="4">PIX</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <label for="sale_code">Transação</label>
                                            <input type="text" id="sale_code" placeholder="Digite ID da transação" class="input-pad">
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <label for="date_type">Data</label>
                                            <select name='date_type' id="date_type" class="sirius-select">
                                                <option value="start_date">Data do pedido</option>
                                                <option value="end_date">Data do pagamento</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-3 form-icons">
                                            <label style="margin-bottom: 0.19rem;" for="date_range">‏‏‎ ‎</label>
                                            <div class="col-12 mb-10 date-report">
                                                <div class="row align-items-center form-icons box-select">
                                                    <input id="date-filter" type="text" name="daterange" class="font-size-14" value="" readonly>
                                                    <i style="right:16px;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm-6 col-md-3 form-icons">
                                            <label for="date_range">‏‏‎ ‎</label>
                                            <i style="right: 25px;top: 37px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-10 font-size-18"></i>
                                            <input name='date_range' id="date_range" class="input-pad"
                                            placeholder="Clique para editar..." readonly>
                                        </div> -->

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6 col-md-3 pt-20" style="display:none">
                                            <div id="select-statement-div" >
                                                <label for="type_statement">Tipo Extrato</label>
                                                <select name='select_type_statement'
                                                        id="type_statement"
                                                        class="form-control select-pad"
                                                >
                                                    <option value="manual_liquidation">Extrato Antigo</option>
                                                    <option value="automatic_liquidation" selected>Extrato Novo</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6 col-xl-3 col-12  pt-20">
                                            <label for="acquirer">Adquirente</label>
                                            <select name='select_acquirer' id="acquirer" class="sirius-select">
                                                <option value="0">Todas os adquirentes</option>
                                            </select>
                                        </div>
                                        <div class='col-md-3 pt-30 d-flex align-items-center'>
                                            <label class="switch mr-2">
                                                <input type="checkbox" id='is-security-reserve' name="cashback" class='check' value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text w-100"> Reserva de Segurança </span>
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
                                            <img
                                                style="visibility:hidden"
                                                id="icon-filtro"
                                                class="hidden-xs-down"
                                                src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} "
                                            />
                                            <span id="text-filtro">Filtros avançados</span>
                                            <img
                                                style="visibility:hidden"
                                                id="icon-filtro"
                                                class="hidden-xs-down"
                                                src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} "
                                            />
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
                        @if(!auth()->user()->hasRole('attendance'))
                            <!-- <div class='container col-sm-12 d-lg-block'>
                                <div class='row'>
                                    <div class="col-md-4 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600">Quantidade de vendas</h6>
                                            <h4 id="total_sales" class="font-size-30 bold"></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600">Comissão</h6>
                                            <h4 id="commission_pending"></h4>
                                        </div>
                                        <div class="s-border-right yellow"></div>
                                    </div>
                                    <div class="col-md-4 col-sm-6 col-xs-12 card">
                                        <div class="card-body">
                                            <h6 class="font-size-14 gray-600">Valor Total</h6>
                                            <h4 id="total">
                                            </h4>
                                        </div>
                                        <div class="s-border-right red"></div>
                                    </div>
                                </div>
                            </div> -->
                        @endif
                    </div>

                    <!-- Tabela -->
                    <div class="col-lg-12">
                        <div class="card shadow" style="min-height: 300px">
                            <div class="page-invoice-table table-responsive">
                                <table class="table-vendas table unify table-striped pending">
                                    <thead>
                                    <tr>
                                        <td class="table-title">Transação</td>
                                        <td class="table-title">Loja</td>
                                        <td class="table-title">Cliente</td>
                                        <td class="table-title display-sm-none display-m-none display-lg-none">Forma
                                        </td>
                                        <td class="table-title display-sm-none display-m-none display-lg-none">Data</td>
                                        <td class="table-title">Pagamento</td>
                                        <td class="table-title">Comissão</td>
                                        <td class="table-title"></td>
                                    </tr>
                                    </thead>
                                    <tbody id="body-table-pending"  img-empty="{!! mix('build/global/img/vendas.svg')!!}">
                                    {{-- js carrega... --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-center justify-content-md-end pb-50">
                            <ul id="pagination-pending" class="pl-5 pr-md-15 mb-20 pagination"
                            style="position:relative;float:right">
                                {{-- js carrega... --}}
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Modal detalhes da venda-->
            @include('sales::details')
            <!-- End Modal -->
            </div>
        </div>
        {{-- Quando não tem loja cadastrado  --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas--}}
    </div>

    <!-- Modal estonar transação-->
    @include('sales::modal_refund_transaction')

@endsection

@push('scripts')
    <script src="{{ mix('build/layouts/reports/pending.min.js') }}"></script>
@endpush
