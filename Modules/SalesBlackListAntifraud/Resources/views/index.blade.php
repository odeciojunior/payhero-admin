@extends("layouts.master")

@section('content')
    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales-blacklist-antifraud/css/index.css') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
    @endpush

    <!-- Page -->
    <div class='page'>
        <div class='page-header container'>
            <div class='row align-items-center justify-content-between' style='min-height:50px'>
                <div class='col-12'>
                    <h1 class='page-title'>Antifraude</h1><br>
                    <p id="text-info" style="margin-top: 12px; display: block;">
                        Os dados dos usuários abaixo foram utilizados diversas vezes para tentativas de compras fraudulentas. Para proteger suas vendas, nosso sistema bloqueia automaticamente novas tentativas. Recomendamos não tentar recuperar estas vendas, pois os fraudadores se passam por clientes para enganar o suporte e o sistema.</p>
                </div>
                <div class='col-6 text-right'>
                    <div class='justify-content-end align-items-center' id='export-excel' style='display:none'>
                        <div class='p-2 align-items-center'>
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                                <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                            </svg>
                            <div class="btn-group" role="group">
                                <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                <button id="bt_get_csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='page-content container' style='display:block'>
            <!-- Filtro -->
            <div class='fixhalf'></div>
            <form id='filter_form'>
                <div id="" class="card shadow p-20">
                    <div class="row align-items-baseline">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="projeto">Projeto</label>
                            <select name='select_project' id="projeto" class="form-control select-pad">
                                <option value="">Todos projetos</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="comprador">Transação</label>
                            <input name='transaction' id="transaction" class="input-pad" placeholder="transação">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="date_range">Data</label>
                            <input name='date_range' id="date_range" class="select-pad" placeholder="Clique para editar..." readonly>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label></label>
                            <button id="bt_filtro" class="btn btn-primary col-sm-12">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
            <div class="fixhalf"></div>
            <div class="card shadow " style="min-height: 300px">
                <div class="page-invoice-table table-responsive">
                    <table id="tabela_vendas" class="table-vendas table table-striped unify" style="">
                        <thead>
                            <tr class='text-center'>
                                <td class="table-title display-sm-none display-m-none  display-lg-none">Transação</td>
                                <td class="table-title">Projeto</td>
                                <td class="table-title">Descrição</td>
                                <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                {{--                                <td class="table-title blacklist" style='display:none'>Motivo</td>--}}
                                <td class="table-title display-sm-none display-m-none">Data</td>
                                <td class="table-title" width="80px;"> &nbsp;</td>
                            </tr>
                        </thead>
                        <tbody id="dados_tabela">
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
                <!-- Modal detalhes da venda-->
            @include('salesblacklistantifraud::details')
            <!-- End Modal -->
            </div>
            <ul id="pagination-sales-atifraud-blacklist" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/sales-blacklist-antifraud/js/index.js?v=1') }}"></script>
        <script src="{{ asset('/modules/sales-blacklist-antifraud/js/detail.js?v=1') }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush


@endsection
