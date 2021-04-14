@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
        <link rel="stylesheet" href="{{ asset('modules/trackings/css/index.css?v=02') }}">
    @endpush

    <!-- Page -->
    <div class="page">
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
                                    <button id="btn-export-xls" type="button"
                                            class="btn btn-round btn-default btn-outline btn-pill-left">.XLS
                                    </button>
                                    <button id="btn-export-csv" type="button"
                                            class="btn btn-round btn-default btn-outline btn-pill-right">.CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                            <div class="d-flex align-items-center">
                                <span class="o-download-cloud-1 mr-2"></span>
                                <button id="btn-import-xls" type="button" class="btn btn-round btn-default btn-outline"
                                        style="min-width: 118px;">IMPORTAR
                                </button>
                                <input type="file" id="input-import-xls" style="display:none" accept=".csv,.xlsx">
                            </div>
                        </div>
                        <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                            <div class="d-flex align-items-center">
                                <a class="rounded-info btn mr-3 d-flex justify-content-center align-items-center btn-default btn-outline" data-toggle="modal" data-target="#modal-detalhes-importar"  >
                                    <span class="o-info-1" style="font-size: 24px;"></span>
                                </a>
                                <span data-toggle="modal" data-target="#modal-detalhes-importar" class="ml-10 pointer"
                                      style="min-width: 112px; font-size: 12px">Como importar códigos de rastreio?</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Instruções -->
        <div class="modal fade modal-3d-flip-vertical" id="modal-detalhes-importar" aria-hidden='true' role="dialog"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="panel-group panel-group-continuous m-0" id="accordion" aria-multiselectable="true"
                         role="tablist">
                        <div class="panel">
                            <div class="panel-heading" id="headingFirst" role="tab">
                                <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse"
                                   href="#collapseFirst" aria-controls="collapseFirst" aria-expanded="false">
                                    <strong>Primeiro passo</strong>
                                </a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseFirst" aria-labelledby="headingFirst"
                                 role="tabpanel" style="">
                                <div class="panel-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span> Clique para fazer o download da planilha</span>
                                        <div class="d-flex align-items-center">
                                            <span class="o-download-cloud-1 mr-2"></span>
                                            <div class="btn-group" role="group">
                                                <button type="button"
                                                        class="btn btn-round btn-default btn-outline btn-pill-left">.XLS
                                                </button>
                                                <button type="button"
                                                        class="btn btn-round btn-default btn-outline btn-pill-right">
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
                                <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse"
                                   href="#collapseSecond" aria-controls="collapseSecond" aria-expanded="false">
                                    <strong>Segundo passo</strong>
                                </a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseSecond" aria-labelledby="headingSecond"
                                 role="tabpanel" style="">
                                <div class="panel-body justify-content-center" style="overflow-x: auto;">
                                    <span class="d-block mb-10">Preencha a coluna correspondente aos <strong>Códigos de Rastreio</strong></span>
                                    <table class="table table-striped" style="cursor: default;">
                                        <thead style="font-size: 16px; background: #3e8ef7;">
                                        <tr style="color: #fff;">
                                            <td class="text-nowrap">Código da Venda</td>
                                            <td class="text-nowrap">Código de Rastreio</td>
                                            <td class="text-nowrap">Código do Produto</td>
                                        </tr>
                                        </thead>
                                        <tbody style="font-size: 11px;">
                                        <tr style="color: #000;">
                                            <td>#x4S2ksh3</td>
                                            <td>AA123456789BR</td>
                                            <td>#mWspfhMRLCHLxke</td>
                                        </tr>
                                        <tr style="color: #000;">
                                            <td>#PhkZkf4W</td>
                                            <td>AA987654321BR</td>
                                            <td>#Ra1rm46WlzA09nB</td>
                                        </tr>
                                        <tr style="color: #000;">
                                            <td>#LAc8z7H9</td>
                                            <td style="padding: 0 !important;">
                                                <div class="cell-selected">
                                                    <span class="caret">AA100833276BR</span>
                                                </div>
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
                                <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse"
                                   href="#collapseThird" aria-controls="collapseThird" aria-expanded="false">
                                    <strong>Terceiro passo</strong>
                                </a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseThird" aria-labelledby="headingThird"
                                 role="tabpanel" style="">
                                <div class="panel-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span> Clique em <strong>importar</strong> para fazer o upload</span>
                                        <div class="d-flex align-items-center">
                                            <span class="o-download-cloud-1 mr-2"></span>
                                            <button type="button" class="btn btn-round btn-default btn-outline"
                                                    style="min-width: 118px;">IMPORTAR
                                            </button>
                                            <input type="file" id="input-import-xls" style="display:none"
                                                   accept=".csv,.xlsx">
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
        <div id="project-not-empty" style="display:none">
            <div class="page-content container">
                <!-- Filtro -->
                <div class="fixhalf"></div>
                <div id="" class="card shadow p-20">
                    <div class="row mb-xl-3">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="project-select">Projeto</label>
                            <select name='project' id="project-select" class="form-control select-pad">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="sale">Venda</label>
                            <input name='sale' id="sale" class="input-pad" placeholder="Digite o código da venda">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="tracking_code">Código de rastreio</label>
                            <input name='tracking_code' id="tracking_code" class="input-pad" placeholder="Digite o código">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="status">Status</label>
                            <select name='status' id="status" class="form-control select-pad">
                                <option value="">Todos</option>
                                <option value="posted">Postado</option>
                                <option value="dispatched">Em trânsito</option>
                                <option value="delivered">Entregue</option>
                                <option value="out_for_delivery">Saiu para entrega</option>
                                <option value="exception">Problema na entrega</option>
                                <option value="unknown">Não informado</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="status_commission">Status da comissão</label>
                            <select name='status_commission' id="status_commission" class="form-control select-pad">
                                <option value="">Todos</option>
                                <option value="transfered">Transferido</option>
                                <option value="pending">Pendente</option>
                                <option value="blocked">Não transferido por falta de rastreio</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="date_updated">Data de aprovação venda</label>
                            <input name='date_updated' id="date_updated" class="select-pad"
                                   placeholder="Clique para editar..." readonly>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12 d-flex flex-column justify-content-center">
                            <label for="tracking_problem" class='mb-10 mr-5'>Problemas com o código</label>
                            <label class="switch">
                                <input type="checkbox" id='tracking_problem' name="tracking_problem" class='check'>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: 30px">
                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} ">Aplicar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="fixhalf"></div>
                <!-- Aviso Problemas com os Códigos -->
                <div id="alert-tracking-issues" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10">
                    <div class="d-flex">
                        <i class="material-icons mr-10">contact_support</i>
                        <div class="w-full">
                            <strong class="font-size-16">Problemas com os códigos?</strong>
                            <p class="font-size-14 pr-md-100 mb-5">
                                Caso você tenha algum código de rastreio com divergência de status, entre em contato com o nosso suporte.
                            </p>
                        </div>
                        <i class="material-icons pointer" data-dismiss="alert">close</i>
                    </div>
                </div>
                <!-- Aviso de Saldo Bloqueado -->
                <div id="alert-blockedbalance" class="alert alert-danger alert-dismissible fade show card py-10 pl-20 pr-10"
                     style="display:none;">
                    <div class="d-flex">
                        <span class="o-info-help-1"></span>
                        <div class="w-full">
                            <strong class="font-size-16">Saldo Bloqueado</strong>
                            <p class="font-size-14 pr-md-100 mb-5">
                                Você possui <b>R$
                                    <span id="blocked-balance"></span>
                                    de saldo bloqueado</b> por não informar os códigos de rastreios de <b>
                                    <span id="blocked-balance-sales"></span>
                                    vendas</b>. Informe os códigos de rastreio dessas vendas para que o dinheiro seja
                                transferido.
                            </p>
                        </div>
                        <i class="material-icons pointer" data-dismiss="alert">close</i>
                    </div>
                </div>
                <!-- Aviso de Exportação -->
                <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10"
                     style="display:none;">
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
                <div class="card shadow p-20" style='display:block;'>
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-center text-success" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> trending_up </i> Total</h6>
                            <h4 id="total-trackings" class="number text-center text-success"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-info" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> markunread_mailbox </i> Postado</h6>
                            <h4 id="percentual-posted" class="number text-center text-info"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-info" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> local_shipping </i> Em trânsito</h6>
                            <h4 id="percentual-dispatched" class="number text-center text-info"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-info" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> arrow_right_alt </i> Saiu para entrega</h6>
                            <h4 id="percentual-out" class="number text-center text-info"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-success" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> check_circle </i> Entregues</h6>
                            <h4 id="percentual-delivered" class="number text-center text-success"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-warning" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> error </i> Problema na entrega</h6>
                            <h4 id="percentual-exception" class="number text-center text-warning"></h4>
                        </div>
                        <div>
                            <h6 class="text-center text-danger" style="white-space: nowrap;">
                                <i class="material-icons align-middle mr-1"> error </i> Não informado</h6>
                            <h4 id="percentual-unknown" class="number text-center text-danger"></h4>
                        </div>
                    </div>
                </div>
                <!-- Tabela -->
                <div class="fixhalf"></div>
                <div class="card shadow " style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id="tabela_trackings" class="table-trackings table table-striped unify" style="">
                            <thead>
                            <tr>
                                <th class="table-title">Venda</th>
                                <th class="table-title">Data de Aprovação</th>
                                <th class="table-title">Produto</th>
                                <td class="table-title">Status</td>
                                <th class="table-title">Código de Rastreio</th>
                                <th class="table-title" style="width:90px;"></th>
                            </tr>
                            </thead>
                            <tbody id="dados_tabela">
                            {{-- js carrega... --}}
                            </tbody>
                        </table>
                    </div>
                </div>
                <ul id="pagination-trackings" class="pagination-sm margin-chat-pagination"
                    style="margin-top:10px;position:relative;float:right;margin-bottom:100px;">
                    {{-- js carrega... --}}
                </ul>
                <!-- Modal detalhes da venda-->
                @include('sales::details')
                <!-- End Modal -->
                <!-- Modal detalhes tracking -->
                <div class="modal fade modal-3d-flip-vertical" id="modal-tracking" aria-hidden="true" role="dialog"
                     tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered modal-simple modal-sidebar modal-lg"
                         style="width: 500px;">
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
                                        <div class="col-lg-10 col-9"><p class="table-title"> Produto </p></div>
                                        <div class="col-lg-2 col-3 text-center"><p class="table-title"> Qtde </p></div>
                                    </div>
                                    <div class="row align-items-center mb-20">
                                        <div class="col-lg-10 col-9">
                                            <div class="row align-items-center pl-10">
                                                <img id="tracking-product-image" src="" width="50px"
                                                     style="border-radius: 6px;">
                                                <h4 id="tracking-product-name" class="table-title ml-10 ellipsis"
                                                    style="flex: 1"></h4>
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
                                    <a class='btn mt-10 pl-0 pointer btn-notify-trackingcode'
                                       title='Enviar e-mail com codigo de rastreio para o cliente'>
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

        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    @push('scripts')
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="{{ asset('/modules/trackings/js/index.js?' . random_int(100, 10000)) }}"></script>
    @endpush

@endsection
