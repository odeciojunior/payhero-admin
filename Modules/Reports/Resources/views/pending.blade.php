@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
    @endpush

    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row">
                <div class="col-8">
                    <h1 class="page-title">Saldo Pendente</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div id="reports-content" class="page-content container">
                <div class="row justify-content-between mt-20">
                    <div class="col-lg-12">
                        <form id='filter_form'>
                            <div id="" class="card shadow p-20">
                                <div class="row align-items-baseline">
                                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                        <label for="company">Empresa</label>
                                        <select name='select_company' id="company" class="form-control select-pad">
                                            <option value="0">Todas as empresas</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                        <label for="project">Projeto</label>
                                        <select name='select_project' id="project" class="form-control select-pad">
                                            <option value="0">Todas os projetos</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="comprador">Nome do cliente</label>
                                        <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="customer_document">CPF do cliente</label>
                                        <input name='customer_document' id="customer_document" class="input-pad"
                                               placeholder="CPF" data-mask="000.000.000-00">
                                    </div>
                                </div>
                                <div class="collapse pt-20" id="bt_collapse">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-3">
                                            <label for="forma">Forma de pagamento</label>
                                            <select name='select_payment_method' id="forma" class="form-control select-pad">
                                                <option value="">Boleto e cartão de crédito</option>
                                                <option value="1">Cartão de crédito</option>
                                                <option value="2">Boleto</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <label for="sale_code">Transação</label>
                                            <input type="text" id="sale_code" placeholder="transação">
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <label for="date_type">Data</label>
                                            <select name='date_type' id="date_type" class="form-control select-pad">
                                                <option value="start_date">Data do pedido</option>
                                                <option value="end_date">Data do pagamento</option>
                                            </select>
                                        </div>
                                        <!-- <div class="col-sm-6 col-md d-flex align-items-center pt-md-20 pt-10"> -->
                                        <div class="col-sm-6 col-md-3 form-icons">
                                            <label for="date_range">‏‏‎ ‎</label>
                                            <i style="right: 20px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-10 font-size-18"></i>
                                            <input name='date_range' id="date_range" class="select-pad"
                                            placeholder="Clique para editar..." readonly>
                                        </div>
                                        <div class="col-sm-6 col-md-3 pt-20">
                                            <div id="select-statement-div" style="display:none;">
                                                <label for="type_statement">Tipo Extrato</label>
                                                <select name='select_type_statement'
                                                id="type_statement"
                                                class="form-control select-pad"
                                                >
                                                    <option value="manual_liquidation" selected>Extrato Antigo</option>
                                                    <option value="automatic_liquidation">Extrato Novo</option>
                                                </select>
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
                        @if(!auth()->user()->hasRole('attendance'))
                            <div class="card shadow p-20" style='display:block;'>
                                <div class="row justify-content-center">
                                    <div class="col-md-4">
                                        <h6 class="text-center green-gradient">
                                            <i class="material-icons align-middle mr-1 green-gradient"> swap_vert </i>
                                            Quantidade de vendas
                                        </h6>
                                        <h4 id="total_sales" class="number text-center green-gradient"></h4>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-center orange-gradient">
                                            <i class="material-icons align-middle mr-1 orange-gradient">
                                                attach_money </i>
                                            Comissão
                                        </h6>
                                        <h4 id="commission_pending" class="number text-center orange-gradient"></h4>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 class="text-center green-gradient">
                                            <i class="material-icons align-middle green-gradient mr-1"> trending_up </i>
                                            Valor Total </h6>
                                        <h4 id="total" class="number text-center green-gradient">
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tabela -->
                    <div class="col-lg-12">
                        <div class="card shadow" style="min-height: 300px">
                            <div class="page-invoice-table table-responsive">
                                <table class="table-vendas table unify table-striped">
                                    <thead>
                                    <tr>
                                        <th class="table-title">Transação</th>
                                        <th class="table-title">Projeto</th>
                                        <th class="table-title">Cliente</th>
                                        <th class="table-title display-sm-none display-m-none display-lg-none">Forma
                                        </th>
                                        <th class="table-title display-sm-none display-m-none display-lg-none">Data</th>
                                        <th class="table-title">Pagamento</th>
                                        <th class="table-title">Comissão</th>
                                        <th class="table-title"></th>
                                    </tr>
                                    </thead>
                                    <tbody id="body-table-pending">
                                    {{-- js carrega... --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-center justify-content-md-end">
                            <ul id="pagination-pending" class="pl-5 pr-md-15 mb-20"
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
        {{-- Quando não tem projeto cadastrado  --}}
        @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>


@endsection

@push('scripts')
    <script src="{{ asset('modules/reports/js/detail.js?v=s08') }}"></script>
    <script src='{{ asset('modules/reports/js/report-pending.js?v=' . random_int(100, 10000)) }}'></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
@endpush
