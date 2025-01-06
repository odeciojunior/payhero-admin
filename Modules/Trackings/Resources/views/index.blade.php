@extends('layouts.master')

@section('content')
@push('css')
<link rel="stylesheet" href="{{ mix('build/layouts/trackings/index.min.css') }}">
@endpush

<!-- Page -->
<div class="page pb-0 " style="margin-bottom: 0px !important;">

    @include('layouts.company-select', ['version' => 'mobile'])

    <div style="display: none" class="page-header container">
        <div class="row align-items-center justify-content-between" style="min-height:50px">
            <div class="col-lg-4">
                <h1 class="page-title">Rastreamentos</h1>
            </div>
            <div class="col">
                <div id="export-excel" class="row justify-content-lg-end">
                    <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                        <div class="d-flex align-items-center">
                            <span class="o-download-cloud-1 mr-2"></span>
                            <div class="btn-group" role="group">
                                <!-- <button id="btn-export-xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left border-right-0">.XLS
                                </button> -->
                                <button id="btn-export-csv" type="button" class="btn btn-round btn-default btn-outline btn-pill">.CSV
                                </button>
                            </div>
                        </div>
                    </div>
                    @can('trackings_manage')
                    <div class="col mt-lg-0 mt-20 p-0 mr-25" style="flex-grow: 0">
                        <div class="d-flex align-items-center">
                            <button id="btn-import-xls" type="button" class="btn btn-round btn-default btn-outline font-weight-bold" style="min-width: 118px; font-size: 14px;">Importar
                            </button>
                            <input type="file" id="input-import-xls" style="display:none" accept=".csv,.xlsx">
                        </div>
                    </div>
                    <div class="col mt-lg-0 mt-20 p-0" style="flex-grow: 0">
                        <div class="d-flex align-items-center mr-15">
                            <a class="rounded-info btn mr-3 d-flex justify-content-center align-items-center btn-default btn-outline" data-toggle="modal" data-target="#modal-detalhes-importar" style="min-height:38px;">
                                <span class="o-info-1" style="font-size: 24px;"></span>
                            </a>
                            <span data-toggle="modal" data-target="#modal-detalhes-importar" class="ml-10 pointer" style="min-width: 112px; font-size: 12px">Como importar códigos de rastreio?</span>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Instruções -->
    <div class="modal fade modal-3d-flip-vertical" id="modal-detalhes-importar" aria-hidden='true' role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="panel-group panel-group-continuous m-0" id="accordion" aria-multiselectable="true" role="tablist">
                    <div class="panel">
                        <div class="panel-heading" id="headingFirst" role="tab">
                            <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse" href="#collapseFirst" aria-controls="collapseFirst" aria-expanded="false">
                                <strong>Primeiro passo</strong>
                            </a>
                        </div>
                        <div class="panel-collapse collapse" id="collapseFirst" aria-labelledby="headingFirst" role="tabpanel" style="">
                            <div class="panel-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span> Clique para fazer o download da planilha</span>
                                    <div class="d-flex align-items-center">
                                        <span class="o-download-cloud-1 mr-2"></span>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-round btn-default btn-outline btn-pill-left">
                                                .XLS
                                            </button>
                                            <button type="button" class="btn btn-round btn-default btn-outline btn-pill">
                                                .CSV
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading" id="headingSecond" role="tab">
                            <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse" href="#collapseSecond" aria-controls="collapseSecond" aria-expanded="false">
                                <strong>Segundo passo</strong>
                            </a>
                        </div>
                        <div class="panel-collapse collapse" id="collapseSecond" aria-labelledby="headingSecond" role="tabpanel" style="">
                            <div class="panel-body justify-content-center position-relative" style="overflow-x: auto;">
                                <span class="d-block mb-10">Preencha a coluna correspondente aos <strong>Códigos de
                                        Rastreio</strong></span>
                                <table class="import-example-table">
                                    <thead>
                                        <tr>
                                            <th>Código da Venda</th>
                                            <th>Código de Rastreio</th>
                                            <th>Código do Produto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#x4S2ksh3</td>
                                            <td>AA123456789BR</td>
                                            <td>#mWspfhMRLCHLxke</td>
                                        </tr>
                                        <tr>
                                            <td>#PhkZkf4W</td>
                                            <td>AA987654321BR</td>
                                            <td>#Ra1rm46WlzA09nB</td>
                                        </tr>
                                        <tr>
                                            <td>#LAc8z7H9</td>
                                            <td class="cell-selected">
                                                <span class="caret">AA100833276BR</span>
                                            </td>
                                            <td>#EQUZC43Kq2HLalK</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="cell-fade"></div>
                            </div>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="panel-heading" id="headingThird" role="tab">
                            <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse" href="#collapseThird" aria-controls="collapseThird" aria-expanded="false">
                                <strong>Terceiro passo</strong>
                            </a>
                        </div>
                        <div class="panel-collapse collapse" id="collapseThird" aria-labelledby="headingThird" role="tabpanel" style="">
                            <div class="panel-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span> Clique em <strong>importar</strong> para fazer o upload</span>
                                    <div class="d-flex align-items-center">
                                        <span class="o-download-cloud-1 mr-2"></span>
                                        <button type="button" class="btn btn-round btn-default btn-outline" style="min-width: 118px;">IMPORTAR
                                        </button>
                                        <input type="file" id="input-import-xls" style="display:none" accept=".csv,.xlsx">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Fim - Modal de Instruções -->
    <div id="project-not-empty" style="display:none !important;">
        <div class="page-content container">
            <!-- Filtro -->
            <div id="filters" class="card shadow p-20">
                <div class="row mb-xl-3">
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0">
                        <label for="project-select">Lojas</label>
                        <select name='project' id="project-select" class="form-control select-pad applySelect2">
                            <option value="">Todas lojas</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0">
                        <label for="sale">Venda</label>
                        <input name='sale' id="sale" class="input-pad" placeholder="Digite o código da venda">
                    </div>
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0">
                        <label for="tracking_code">Código de rastreio</label>
                        <input name='tracking_code' id="tracking_code" class="input-pad" placeholder="Digite o código">
                    </div>
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0">
                        <label for="status">Status</label>
                        <select name='status' id="status" class="form-control select-pad applySelect2">
                            <option value="">Todos</option>
                            <option value="posted">Postados</option>
                            <option value="dispatched">Em trânsito</option>
                            <option value="delivered">Entregue</option>
                            <option value="out_for_delivery">Saiu para entrega</option>
                            <option value="exception">Problema na entrega</option>
                            <option value="unknown">Não informado</option>
                        </select>
                    </div>
                </div>

                <div class="collapse" id="bt_collapse">
                    <div class="row pt-15">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0">
                            <label for="status_commission">Status da comissão</label>
                            <select name='status_commission' id="status_commission" class="form-control select-pad applySelect2">
                                <option value="">Todos</option>
                                <option value="transfered">Transferido</option>
                                <option value="paid">Pendente</option>
                                <option value="blocked">Não transferido por falta de rastreio</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12 mb-15 mb-sm-0 form-icons">
                            <label for="date_updated">Data de aprovação venda</label>
                            <i style="right: 20px; margin-top: -13px;" class="form-control-icon form-control-icon-right o-agenda-1 font-size-25"></i>
                            <input name='date_updated' id="date_updated" class="input-pad" placeholder="Clique para editar..." readonly style="margin-bottom: 50px">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12 d-flex flex-column justify-content-center">
                            <label for="tracking_problem" style="margin-top: -10px;" class='mb-20 mr-5'>Problemas com o código</label>
                            <label class="switch" style="margin-bottom: 50px !important;">
                                <input type="checkbox" id='tracking_problem' name="tracking_problem" class='check'>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row mb-10 mb-sm-0">
                    <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                        <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center" data-toggle="collapse" data-target="#bt_collapse" aria-expanded="false" aria-controls="bt_collapse">
                            <img id="icon-filtro" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} " />
                            <div id="text-filtro" style="white-space: normal">Filtros avançados</div>
                        </div>
                    </div>
                    <div class="col-6 col-xl-3 mt-20">
                        <div id="bt_filter" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center" style="white-space: normal">
                            <img style="height: 12px; margin-right: 4px" class="hidden-xs-down" src=" {{ mix('build/global/img/svg/check-all.svg') }} " />
                            Aplicar <br class="d-flex d-sm-none"> filtros
                        </div>
                    </div>
                </div>
            </div>
            <div class="fixhalf"></div>
            <!-- Aviso Problemas com os Códigos -->
            <div id="alert-tracking-issues" class="d-flex alert alert-light alert-dismissible fade show text-primary border border-primary alert-tracking" role="alert" style="border-radius: 12px">
                <img src="{{ mix('build/layouts/trackings/svg/info-tracking.svg') }}">
                <span class="alert-text ml-2">
                    <span class="bold">Problemas com os códigos?</span>
                    Caso você tenha algum código de rastreio com divergência de status, entre em contato com o nosso
                    suporte.
                </span>
                <button type="button" class="close text-primary ml-auto" data-dismiss="alert" aria-label="Close" style="opacity: 1">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Aviso de Saldo retido -->
            <div id="alert-blockedbalance" class="alert alert-danger alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                <div class="d-flex">
                    <span class="o-info-help-1"></span>
                    <div class="w-full">
                        <strong class="font-size-16">Saldo retido</strong>
                        <p class="font-size-14 pr-md-100 mb-5">
                            Você possui <b>R$
                                <span id="blocked-balance"></span>
                                de saldo retido</b> por não informar os códigos de rastreios de <b>
                                <span id="blocked-balance-sales"></span>
                                vendas</b>. Informe os códigos de rastreio dessas vendas para que o dinheiro seja
                            transferido.
                        </p>
                    </div>
                    <i class="material-icons pointer" data-dismiss="alert">close</i>
                </div>
            </div>
            <!-- Aviso de Exportação -->
            <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                <div class="d-flex">
                    <span class="o-info-help-1"></span>
                    <div class="w-full">
                        <strong class="font-size-16">Exportando seu relatório</strong>
                        <p class="font-size-14 pr-md-100 mb-5">Sua exportação será entregue por e-mail para:
                            <strong id="export-email"></strong> e aparecerá nas suas notificações. Pode levar algum
                            tempo, dependendo de quantos registros você estiver exportando.
                        </p>
                    </div>
                    <i class="material-icons pointer" data-dismiss="alert">close</i>
                </div>
            </div>

            <!-- Resumo -->
            <div class="container">
                <div id="viewer-data-general" class="row">

                    <div class="col-12 col-lg-6 adjust">
                        <div id="panel-cards" class="row pr-lg-30 pb-0">

                            <div id="posted" class="col-md-4 col-sm-6 col-xs-12 border-right border-bottom px-0 bg-white card-posted">
                                <div class="card mb-0 rounded-0 card-posted">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0" data-toggle="tooltip" data-container=".page" data-placement="top" title="Postados">Postados </h6>
                                        <h4 id="percentual-posted" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 border-right border-bottom px-0 bg-white">
                                <div class="card mb-0 rounded-0">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0" data-toggle="tooltip" data-container=".page" data-placement="top" title="Em trânsito">Em trânsito</h6>
                                        <h4 id="percentual-dispatched" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 border-bottom pl-0 pr-0 bg-white card-to-delivery">
                                <div class="card mb-0 rounded-0 card-to-delivery">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0 text-truncate" data-toggle="tooltip" data-container=".page" data-placement="top" title="Saiu para entrega">Saiu para entrega</h6>
                                        <h4 id="percentual-out" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 border-right px-0 bg-white card-delivery-problem">
                                <div class="card mb-0 rounded-0 card-delivery-problem">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0 text-truncate" data-toggle="tooltip" data-container=".page" data-placement="top" title="Problema na entrega">Problema na entrega</h6>
                                        <h4 id="percentual-exception" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 border-right px-0 bg-white">
                                <div class="card mb-0 rounded-0">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0 text-truncate" data-toggle="tooltip" data-container=".page" data-placement="top" title="Não informado">Não informado</h6>
                                        <h4 id="percentual-unknown" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 col-sm-6 col-xs-12 pl-0 pr-0 bg-white card-delivered">
                                <div class="card mb-0 rounded-0 card-delivered">
                                    <div class="card-body">
                                        <h6 class="font-size-16 gray-600 m-0" data-toggle="tooltip" data-container=".page" data-placement="top" title="Entregues">Entregues</h6>
                                        <h4 id="percentual-delivered" class="mt-12 mb-0 text-nowrap resume-number">
                                            <span class="resume-number">0</span>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--GRAFICO E LEGENDA -->
                    <div class="col-12 col-lg-6 card-graphic-labels bg-white d-flex align-items-center justify-content-center mt-20 mt-lg-0">
                        <div id="graphic-loading"></div>

                        <div id="panel-graph" class="row">

                            <div class="col-md-5 p-0 m-auto">
                                <div class="card mb-0 rounded-0 card-graphic-labels">
                                    <div id="dataCharts" class="d-flex card-body p-0" style="position: relative;">

                                        <canvas id="myChart"></canvas>

                                        <div id="total-values" class="total-container">
                                            <span class="title font-size-16">Total</span>
                                            <span id="total-products" data-toggle="tooltip" data-html="true" data-placement="top" title="abc"></span>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- LEGENDAS -->
                            <div class="col-md-7 px-0 ">
                                <div class="card m-0 card-graphic-labels" style="height: 100%;">

                                    <div id="data-labels" class="d-flex card-body pl-10 justify-content-start align-items-center">
                                        <div class="labels row">
                                            <h2 class="font-size-14 gray-600 col-6 mt-10 "> <i class="fas fa-circle mr-5 posted"></i> Postados</h2>
                                            <h2 class="font-size-14 gray-600 col-6 mt-10"> <i class="fas fa-circle mr-5 delivered"></i> Entregues</h2>
                                            <h2 class="font-size-14 gray-600 col-6 mt-10"> <i class="fas fa-circle mr-5 inTransit"></i> Em trânsito</h2>
                                            <h2 class="font-size-14 gray-600 col-6 mt-10 "> <i class="fas fa-circle mr-5 withoutInfo"></i> Não informado</h2>
                                            <h2 class="font-size-14 gray-600 col-6 mt-10 "> <i class="fas fa-circle mr-5 onDelivery"></i> Saiu para entrega</h2>
                                            <h2 class="font-size-14 gray-600 col-6 mt-10 "> <i class="fas fa-circle mr-5 withProblem"></i> Problema na entrega</h2>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="fixhalf my-10"></div>
            <div class="card shadow " style="min-height: 300px">
                <div class="page-invoice-table table-responsive no-final-space">
                    <table id="tabela_trackings" class="table-trackings table unify mb-0" style="">
                        <thead>
                            <tr>
                                <td class="table-title">Venda</td>
                                <td class="table-title">Produto</td>
                                <td class="table-title">Aprovação</td>
                                <td class="table-title text-center">Status</td>
                                <td class="table-title text-center"></td>
                                <td class="table-title">Código de Rastreio</td>
                            </tr>
                        </thead>
                        <tbody id="dados_tabela" img-empty="{!! mix('build/global/img/rastreio.svg') !!}">
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="container-pagination-trackings" class="d-none row no-gutters justify-content-center justify-content-md-end mb-80">
                <ul id="pagination-trackings" class="pagination-style pl-5 pr-md-15 p-10" style="margin-top:10px;position:relative;float:right;">
                    {{-- js carrega... --}}
                </ul>
            </div>

            <!-- Modal detalhes da venda-->
            @include('sales::details')

            <!-- Modal estonar transação-->
            @include('sales::modal_refund_transaction')

            <!-- Modal detalhes tracking -->
            <div class="modal fade modal-3d-flip-vertical" id="modal-tracking" aria-hidden="true" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-simple modal-sidebar modal-lg" style="width: 500px;">
                    <div id='modal-tracking-details' class="modal-content">
                        <div class="modal-header simple-border-bottom justify-content-center">
                            <h4> Detalhes do rastreamento </h4>
                        </div>
                        <a data-dismiss="modal" role="button" style="position: absolute;right: 20px;top: 25px;">
                            <i class="material-icons pointer">close</i>
                        </a>
                        <div class="modal-body">
                            <h3 id="tracking-code" class="text-uppercase"></h3>
                            <div class="p-10">
                                <div class="row">
                                    <div class="col-lg-10 col-9">
                                        <p class="table-title"> Produto </p>
                                    </div>
                                    <div class="col-lg-2 col-3 text-center">
                                        <p class="table-title"> Qtde </p>
                                    </div>
                                </div>
                                <div class="row align-items-center mb-20">
                                    <div class="col-lg-10 col-9">
                                        <div class="row align-items-center pl-10">
                                            <img id="tracking-product-image" src="" width="50px" style="border-radius: 6px;">
                                            <h4 id="tracking-product-name" class="table-title ml-10 ellipsis" style="flex: 1"></h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-3 text-center">
                                        <span id="tracking-product-amount" class="sm-text text-muted"></span>
                                    </div>
                                </div>
                                <div>
                                    <h4> Destino </h4>
                                    <span id="tracking-delivery-address" class="table-title gray"></span>
                                    <br>
                                    <span id="tracking-delivery-neighborhood" class="table-title gray"></span>
                                    <br>
                                    <span id="tracking-delivery-zipcode" class="table-title gray"></span>
                                    <br>
                                    <span id="tracking-delivery-city" class="table-title gray"></span>
                                </div>
                                <a class='btn mt-10 pl-0 pointer btn-notify-trackingcode' title='Enviar e-mail com codigo de rastreio para o cliente'>
                                    <i class='icon wb-envelope'></i> Enviar e-mail/sms para o cliente
                                </a>
                                <p class="mt-5" id="link-tracking"><i class="material-icons">link</i>
                                    <a target="_blank" class="pointer text-body">Acessar link de rastreio</a>
                                </p>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Status</th>
                                            <th>Evento</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-checkpoint"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Modal -->
        </div>
    </div>
    {{-- Quando não tem loja cadastrado --}}
    @include('projects::empty')
    {{-- FIM loja nao existem lojas --}}
</div>
</div>

@push('scripts')
<script src="{{ mix('build/layouts/trackings/index.min.js') }}"></script>
@endpush
@endsection
