@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=04') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet"/>
        <style>
            .select2-selection--single {
                border: 1px solid #dddddd !important;
                border-radius: .215rem !important;
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
        </style>
    @endpush

    <!-- Page -->
    <div class="page">
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
                <div class="fixhalf"></div>
                <form id='filter_form'>
                    <div id="" class="card shadow p-20">
                        <div class="row align-items-baseline">
                            <div class="col-sm-6 col-md">
                                <label for="projeto">Projeto</label>
                                <select name='select_project' id="projeto" class="form-control select-pad">
                                    <option value="">Todos projetos</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md">
                                <label for="plan">Plano</label>
                                <select name='plan' id="plan" class="form-control select-pad" style='width:100%;' data-plugin="select2">
                                    <option value="">Todos planos</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md">
                                <label for="forma">Forma de pagamento</label>
                                <select name='select_payment_method' id="forma" class="form-control select-pad">
                                    <option value="">Boleto e cartão de crédito</option>
                                    <option value="1">Cartão de crédito</option>
                                    <option value="2">Boleto</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md">
                                <label for="status">Status</label>
                                <select name='sale_status' id="status" class="form-control select-pad">
                                    <option value="">Todos status</option>
                                    <option value="1">Aprovado</option>
                                    <option value="24">Em disputa</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md">
                                <label for="comprador">Transação</label>
                                <input name='transaction' id="transaction" class="input-pad" placeholder="transação">
                            </div>
                        </div>
                        <div class="row mt-md-15">
                            <div class="col-sm-8 col-md">
                                <label for="comprador">Nome do cliente</label>
                                <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                            </div>
                            <div class="col-sm-8 col-md">
                                <label for="customer_document">CPF do cliente</label>
                                <input name='customer_document' id="customer_document" class="input-pad" placeholder="CPF" data-mask="000.000.000-00">
                            </div>
                            <div class="col-sm-6 col-md">
                                <label for="date_type">Data</label>
                                <select name='date_type' id="date_type" class="form-control select-pad">
                                    <option value="start_date">Data do pedido</option>
                                    <option value="end_date">Data do pagamento</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md">
                                <div class="form-group form-icons">
                                    <label for="date_range" >Data</label>
                                    <i style="right: 20px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-5 font-size-18"></i>
                                    <input name='date_range' id="date_range" class="select-pad pr-30" placeholder="Clique para editar..." readonly >
                                </div>
                            </div>
                            <div class="col-sm-6 col-md d-flex align-items-center pt-md-20 pt-10">
                                <button id="bt_filtro" class="btn btn-primary col">
                                    <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Resumo -->
                <div class="fixhalf"></div>
                <div class="card shadow p-20" style='display:block;'>
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
                </div>

                <!-- Tabela -->
                <div class="fixhalf"></div>
                <div class="card shadow " style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id="tabela_vendas" class="table-vendas table table-striped unify" style="">
                            <thead>
                            <tr>
                                <td class="table-title display-sm-none display-m-none  display-lg-none">Transação</td>
                                <td class="table-title">Projeto</td>
                                <td class="table-title">Descrição</td>
                                <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                <td class="table-title">Forma</td>
                                <td class="table-title">Status</td>
                                <td class="table-title display-sm-none display-m-none">Data</td>
                                <td class="table-title display-sm-none">Pagamento</td>
                                <td class="table-title">Comissão</td>
                                <td class="table-title">Motivo bloqueio</td>
                            </tr>
                            </thead>
                            <tbody id="dados_tabela">
                            {{-- js carrega... --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <ul id="pagination-sales" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right;margin-bottom:100px;">
                    {{-- js carrega... --}}
                </ul>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>


    @push('scripts')
        <script src='{{asset('modules/reports/js/report-blockedbalance.js?v=' . random_int(100, 10000))}}'></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection

