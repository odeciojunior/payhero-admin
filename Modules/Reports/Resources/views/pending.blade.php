@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! mix('build/layouts/reports/pending.min.css') !!}"> --}}
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
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height: 50px;">
                <div class="col-8">
                    <h1 class="page-title">Saldo Pendente</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
        <div id="project-not-empty" style="display: none">
            <div id="reports-content" class="page-content container">
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
                                            <option value="0">Todas lojas</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="comprador">Nome do cliente</label>
                                        <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                                    </div>
                                    <div class="col-sm-8 col-md-6 col-xl-3">
                                        <label for="customer_document">CPF do cliente</label>
                                        <input name='customer_document' id="customer_document" class="input-pad default-border" placeholder="CPF" data-mask="000.000.000-00">
                                    </div>
                                </div>
                                <div class="collapse pt-20" id="bt_collapse">
                                    <div class="row">
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
                                            <label for="sale_code">Transação</label>
                                            <input type="text" id="sale_code" placeholder="transação" class="input-pad">
                                        </div>
                                        <div class="col-sm-6 col-md-3">
                                            <label for="date_type">Data</label>
                                            <select name='date_type' id="date_type" class="sirius-select">
                                                <option value="start_date">Data do pedido</option>
                                                <option value="end_date">Data do pagamento</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6 col-md-3 form-icons">
                                            <label for="date_range">‏‏‎ ‎</label>
                                            <i style="right: 25px;top: 37px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-10 font-size-18"></i>
                                            <input name='date_range' id="date_range" class="input-pad"
                                            placeholder="Clique para editar..." readonly>
                                        </div>
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
                                            <img id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} "/>
                                            <span id="text-filtro">Filtros avançados</span>
                                        </div>
                                    </div>
                                    <div class="col-6 col-xl-3 mt-20">
                                        <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                            <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/check-all.svg') }} "/>
                                            Aplicar filtros
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- Resumo -->
                        <div class="fixhalf"></div>
                        @if(!auth()->user()->hasRole('attendance'))
                            <div class='container col-sm-12 d-lg-block'>
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
        {{-- Quando não tem loja cadastrado  --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas--}}
    </div>


@endsection

@push('scripts')
    <script src='{{ mix('build/layouts/reports/pending.min.js') }}'></script>
@endpush
