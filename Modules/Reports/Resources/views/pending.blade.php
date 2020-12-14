@extends("layouts.master")
@section('title', '- Utilização de Cupons')

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/reports/css/reports.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
    @endpush

    <div class="page">
        <div class="page-header container">
            <div class="row">
                <div class="col-8">
                    <h1 class="page-title">Saldo Pendente</h1>
                    <span type="hidden" class="error-data"></span>
                </div>
            </div>
        </div>
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
                                <div class="col-sm-8 col-md">
                                    <label for="comprador">Nome do cliente</label>
                                    <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                                </div>
                                <div class="col-sm-8 col-md">
                                    <label for="customer_document">CPF do cliente</label>
                                    <input name='customer_document' id="customer_document" class="input-pad"
                                           placeholder="CPF" data-mask="000.000.000-00">
                                </div>                                
                            </div>
                            <div class="row mt-md-15">
                                <div class="col-sm-6 col-md">
                                    <label for="forma">Forma de pagamento</label>
                                    <select name='select_payment_method' id="forma" class="form-control select-pad">
                                        <option value="">Boleto e cartão de crédito</option>
                                        <option value="1">Cartão de crédito</option>
                                        <option value="2">Boleto</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                    <label for="sale_code">Transação</label>
                                    <input type="text" id="sale_code" placeholder="transação">
                                </div>
                                <div class="col-sm-6 col-md">
                                    <label for="date_type">Data</label>
                                    <select name='date_type' id="date_type" class="form-control select-pad">
                                        <option value="start_date">Data do pedido</option>
                                        <option value="end_date">Data do pagamento</option>
                                    </select>
                                </div>
                                
                                <!-- <div class="col-sm-6 col-md d-flex align-items-center pt-md-20 pt-10"> -->
                                
                                <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                    <label for="date_range">‏‏‎ ‎</label>
                                    <input name='date_range' id="date_range" class="select-pad"
                                           placeholder="Clique para editar..." readonly>
                                </div>

                                <div class='col-9'></div>

                                <div class="col-sm-6 col-md d-flex align-items-center pt-md-20 pt-10">
                                    <button id="bt_filtro" class="btn btn-primary col">
                                        <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                    </button>
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
                                        <i class="material-icons align-middle mr-1 orange-gradient"> attach_money </i>
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
                    <div class="card shadow">
                        <div class="page-invoice-table table-responsive">
                            <table class="table-vendas table unify table-striped">
                                <thead>
                                <tr>
                                    <th class="table-title">Transação</th>
                                    <th class="table-title">Projeto</th>
                                    <th class="table-title">Cliente</th>
                                    <th class="table-title display-sm-none display-m-none display-lg-none">Forma</th>
                                    <th class="table-title display-sm-none display-m-none display-lg-none">Data</th>
                                    <th class="table-title">Pagamento</th>
                                    <th class="table-title">Comissão</th>
                                </tr>
                                </thead>
                                <tbody id="body-table-pending">
                                {{-- js carrega... --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <ul id="pagination-pending" class="pagination-sm margin-chat-pagination"
                        style="position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
            <!-- Modal detalhes da venda-->
        @include('sales::details')
        <!-- End Modal -->
        </div>
        @include('projects::empty')
    </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('/modules/reports/js/detail.js?v=17') }}"></script>
    <!-- <script src='{{ asset('/modules/reports/js/report-pending.js?v=1')}}'></script> -->
    <script src="{{ asset('/modules/reports/js/report-pending.js?v='.random_int(1, 100))}})}}"></script>
    <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
    <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
@endpush
