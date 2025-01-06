@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/salesblacklistantifraud/index.min.css') }}">
    @endpush

    <!-- Page -->
    <div class='page'>
        <div class='page-header container'>

            @include('layouts.company-select',['version'=>'mobile'])

            <div class='row align-items-center justify-content-between' style='min-height:50px'>
                <div class='col-12'>
                    <h1 class='page-title'>Antifraude</h1><br>
                    <p id="text-info"
                       style="margin-top: 12px; display: none;">
                        Os dados dos usuários abaixo foram utilizados diversas vezes para tentativas de compras
                        fraudulentas. Para proteger suas vendas, nosso sistema bloqueia automaticamente novas tentativas.
                        Recomendamos não tentar recuperar estas vendas, pois os fraudadores se passam por clientes para
                        enganar o suporte e o sistema.
                    </p>
                </div>
                <div class='col-6 text-right'>
                    <div class='justify-content-end align-items-center'
                         id='export-excel'
                         style='display:none'>
                        <div class='p-2 align-items-center'>
                            <!-- <span class="o-download-cloud-1 mr-2"></span>
                            <div class="btn-group"
                                 role="group"> -->
                                <!-- <button id="bt_get_xls"
                                        type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button> -->
                                <!-- <button id="bt_get_csv"
                                        type="button"
                                        class="btn btn-round btn-default btn-outline btn-pill">.CSV</button> -->
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="project-not-empty"
             style="display: none">
            <div class='page-content container'
                 style='display:block'>
                <!-- Filtro -->
                <div class='fixhalf'></div>
                <form id='filter_form'>
                    <div id=""
                         class="card shadow p-20">
                        <div class="row align-items-baseline">
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="projeto">Lojas</label>
                                <select name='select_project'
                                        id="projeto"
                                        class="form-control select-pad">
                                    <option value="">Todas lojas</option>
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="comprador">Transação</label>
                                <input name='transaction'
                                       id="transaction"
                                       class="input-pad"
                                       placeholder="transação">
                            </div>
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="date_range">Data</label>
                                <input name='date_range'
                                       id="date_range"
                                       class="select-pad"
                                       placeholder="Clique para editar..."
                                       readonly>
                            </div>
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="customer_name">Cliente</label>
                                <input name='customer_name'
                                       id="customer_name"
                                       class="input-pad"
                                       placeholder="Nome do cliente">
                            </div>
                            <div class='col-md-9'></div>
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label></label>
                                <button id="bt_filtro"
                                        class="btn btn-primary col-sm-12">
                                    <img style="height: 12px; margin-right: 4px"
                                         src=" {{ mix('build/global/img/svg/check-all.svg') }} ">Aplicar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="fixhalf"></div>
                <div class="card shadow "
                     style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id="tabela_vendas"
                               class="table-vendas table table-striped unify"
                               style="">
                            <thead>
                                <tr class='text-center'>
                                    <td class="table-title display-sm-none display-m-none  display-lg-none">Transação</td>
                                    <td class="table-title">Loja</td>
                                    <td class="table-title">Descrição</td>
                                    <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                    {{-- <td class="table-title blacklist" style='display:none'>Motivo</td> --}}
                                    <td class="table-title display-sm-none display-m-none">Data</td>
                                    <td class="table-title"
                                        width="80px;"> &nbsp;</td>
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
                <ul id="pagination-sales-atifraud-blacklist"
                    class="pagination-sm margin-chat-pagination"
                    style="margin-top:10px;position:relative;float:right;margin-bottom:100px;margin-right:100px;">
                    {{-- js carrega... --}}
                </ul>
            </div>
        </div>

        {{-- Quando não tem loja cadastrado --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas --}}
    </div>

    @push('scripts')
        <script src='{{ mix('build/layouts/salesblacklistantifraud/index.min.js') }}'></script>
    @endpush
@endsection
