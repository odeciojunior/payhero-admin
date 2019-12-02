@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/trackings/css/index.css?v=2') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-lg-4">
                    <h1 class="page-title">Rastreamentos</h1>
                </div>
                <div class="col">
                    <div class="row justify-content-lg-end">
                        <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                            <div class="d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                                </svg>
                                <div class="btn-group" role="group">
                                    <button id="btn-export-xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                    <button id="btn-export-csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                                </div>
                            </div>
                        </div>
                        <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                            <div class="d-flex align-items-center">
                                <svg class="icon-download" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="m4.056244,20.607375c-1.81431,-0.58843 -3.12847,-1.92219 -3.66786,-3.7267c-0.20595,-0.70611 -0.20595,-2.25563 0,-2.96175c0.53939,-1.8045 1.87316,-3.15789 3.66786,-3.71689l0.52958,-0.16672l0.1275,-0.72573c0.2844,-1.69663 0.87283,-2.81464 2.13795,-4.06995c0.76495,-0.76495 1.03955,-0.9709 1.82412,-1.34357c1.22589,-0.58843 1.95161,-0.75515 3.32461,-0.75515c1.37299,0 2.09872,0.16672 3.32461,0.75515c0.78457,0.37267 1.05917,0.57862 1.82412,1.34357c1.26512,1.25531 1.85354,2.37332 2.13795,4.06995l0.12749,0.72573l0.52959,0.16672c1.7947,0.559 3.12846,1.91239 3.66786,3.71689c0.20594,0.70611 0.20594,2.25564 0,2.96175c-0.5492,1.82412 -1.932,3.20692 -3.75613,3.75612c-0.4217,0.12749 -1.00032,0.15692 -2.72637,0.15692l-2.18699,0l0,-0.97091l0,-0.9709l2.03988,-0.03923c2.47139,-0.05884 2.87348,-0.17653 3.75612,-1.04936c0.69631,-0.70611 0.97091,-1.3828 0.97091,-2.41255c0,-1.33377 -0.59824,-2.36351 -1.73586,-2.96175c-0.53939,-0.29421 -1.78489,-0.61784 -2.33409,-0.61784c-0.24518,0 -0.24518,0 -0.24518,-0.86303c0,-2.66753 -1.47107,-4.79568 -3.81496,-5.5116c-0.78457,-0.24517 -2.28506,-0.26479 -3.05982,-0.03922c-2.33409,0.6963 -3.73651,2.63811 -3.88362,5.39391l-0.05884,0.99051l-0.65708,0.06865c-2.23602,0.20595 -3.62863,1.55933 -3.62863,3.54037c0,1.02975 0.2746,1.70644 0.97091,2.41255c0.88264,0.87283 1.28473,0.99052 3.76593,1.04936l2.03007,0.03923l0,0.9709l0,0.97091l-2.22622,-0.00981c-1.86335,0 -2.32428,-0.02942 -2.77541,-0.17653z"/>
                                    <path d="m11.01929,18.407315l0,-2.45178l-1.49068,0l-1.50049,0l1.99084,-1.98103l1.98104,-1.99085l1.98103,1.99085l1.99085,1.98103l-1.50049,0l-1.49068,0l0,2.45178l0,2.45177l-0.98071,0l-0.98071,0l0,-2.45177z"/>
                                </svg>
                                <button id="btn-import-xls" type="button" class="btn btn-round btn-default btn-outline" style="min-width: 118px;">IMPORTAR</button>
                                <input type="file" id="input-import-xls" style="display:none" accept=".csv,.xlsx">
                            </div>
                        </div>
                        <div class="col mt-lg-0 mt-20" style="flex-grow: 0">
                            <div class="d-flex align-items-center">
                                <a class="btn rounded-circle btn-default btn-outline" style="padding: 6px 0px;height: 36px;width: 36px; min-width:36px"
                                   data-toggle="modal" data-target="#modal-detalhes-importar">
                                    <i class="icon wb-info"></i>
                                </a>
                                <span data-toggle="modal" data-target="#modal-detalhes-importar" class="ml-10 pointer" style="min-width: 112px; font-size: 12px">Como importar códigos de rastreio?</span>
                            </div>
                        </div>
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
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                                                <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                                            </svg>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                                <button type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
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
                                <a class="panel-title collapsed" data-parent="#accordion" data-toggle="collapse" href="#collapseThird" aria-controls="collapseThird" aria-expanded="false">
                                    <strong>Terceiro passo</strong>
                                </a>
                            </div>
                            <div class="panel-collapse collapse" id="collapseThird" aria-labelledby="headingThird" role="tabpanel" style="">
                                <div class="panel-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span> Clique em <strong>importar</strong> para fazer o upload</span>
                                        <div class="d-flex align-items-center">
                                            <svg class="icon-download" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="m4.056244,20.607375c-1.81431,-0.58843 -3.12847,-1.92219 -3.66786,-3.7267c-0.20595,-0.70611 -0.20595,-2.25563 0,-2.96175c0.53939,-1.8045 1.87316,-3.15789 3.66786,-3.71689l0.52958,-0.16672l0.1275,-0.72573c0.2844,-1.69663 0.87283,-2.81464 2.13795,-4.06995c0.76495,-0.76495 1.03955,-0.9709 1.82412,-1.34357c1.22589,-0.58843 1.95161,-0.75515 3.32461,-0.75515c1.37299,0 2.09872,0.16672 3.32461,0.75515c0.78457,0.37267 1.05917,0.57862 1.82412,1.34357c1.26512,1.25531 1.85354,2.37332 2.13795,4.06995l0.12749,0.72573l0.52959,0.16672c1.7947,0.559 3.12846,1.91239 3.66786,3.71689c0.20594,0.70611 0.20594,2.25564 0,2.96175c-0.5492,1.82412 -1.932,3.20692 -3.75613,3.75612c-0.4217,0.12749 -1.00032,0.15692 -2.72637,0.15692l-2.18699,0l0,-0.97091l0,-0.9709l2.03988,-0.03923c2.47139,-0.05884 2.87348,-0.17653 3.75612,-1.04936c0.69631,-0.70611 0.97091,-1.3828 0.97091,-2.41255c0,-1.33377 -0.59824,-2.36351 -1.73586,-2.96175c-0.53939,-0.29421 -1.78489,-0.61784 -2.33409,-0.61784c-0.24518,0 -0.24518,0 -0.24518,-0.86303c0,-2.66753 -1.47107,-4.79568 -3.81496,-5.5116c-0.78457,-0.24517 -2.28506,-0.26479 -3.05982,-0.03922c-2.33409,0.6963 -3.73651,2.63811 -3.88362,5.39391l-0.05884,0.99051l-0.65708,0.06865c-2.23602,0.20595 -3.62863,1.55933 -3.62863,3.54037c0,1.02975 0.2746,1.70644 0.97091,2.41255c0.88264,0.87283 1.28473,0.99052 3.76593,1.04936l2.03007,0.03923l0,0.9709l0,0.97091l-2.22622,-0.00981c-1.86335,0 -2.32428,-0.02942 -2.77541,-0.17653z"/>
                                                <path d="m11.01929,18.407315l0,-2.45178l-1.49068,0l-1.50049,0l1.99084,-1.98103l1.98104,-1.99085l1.98103,1.99085l1.99085,1.98103l-1.50049,0l-1.49068,0l0,2.45178l0,2.45177l-0.98071,0l-0.98071,0l0,-2.45177z"/>
                                            </svg>
                                            <button type="button" class="btn btn-round btn-default btn-outline" style="min-width: 118px;">IMPORTAR</button>
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

        <div class="page-content container" style="display:none">
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
                        <label for="date_updated">Data de aprovação venda</label>
                        <input name='date_updated' id="date_updated" class="select-pad" placeholder="Clique para editar..." readonly>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12 offset-xl-6">
                        <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: 30px">
                            <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Resumo -->
            <div class="fixhalf"></div>
            <div class="card shadow p-20" style='display:block;'>
                <div class="row justify-content-center">
                    <div class="col">
                        <h6 class="text-center text-success" style="white-space: nowrap;"><i class="material-icons align-middle mr-1"> trending_up </i> Total</h6>
                        <h4 id="total-trackings" class="number text-center text-success"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-info" style="white-space: nowrap;"><i class="material-icons align-middle mr-1"> markunread_mailbox </i> Postado</h6>
                        <h4 id="percentual-posted" class="number text-center text-info"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-info" style="white-space: nowrap;"><i class="material-icons align-middle mr-1"> local_shipping </i> Em trânsito</h6>
                        <h4 id="percentual-dispatched" class="number text-center text-info"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-info" style="white-space: nowrap;"><i class="material-icons align-middle mr-1"> arrow_right_alt </i> Saiu para entrega</h6>
                        <h4 id="percentual-out" class="number text-center text-info"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-success" style="white-space: nowrap;"><i class="material-icons align-middle mr-1"> check_circle </i> Entregues</h6>
                        <h4 id="percentual-delivered" class="number text-center text-success"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-warning" style="white-space: nowrap;"><i class="material-icons align-middle mr-1" > error </i> Problema na entrega</h6>
                        <h4 id="percentual-exception" class="number text-center text-warning"></h4>
                    </div>
                    <div class="col">
                        <h6 class="text-center text-danger" style="white-space: nowrap;"><i class="material-icons align-middle mr-1" > error </i> Não informado</h6>
                        <h4 id="percentual-unknown" class="number text-center text-danger"></h4>
                    </div>
                </div>
            </div>

            <!-- Tabela -->
            <div class="fixhalf"></div>
            <div class="card shadow " style="min-height: 300px">
                <div class="page-invoice-table table-responsive">
                    <table id="tabela_trackings" class="table-trackings table unify" style="">
                        <thead>
                        <tr>
                            <td class="table-title">Venda</td>
                            <td class="table-title">Data de Aprovação</td>
                            <td class="table-title">Produto</td>
                            <td class="table-title">Status</td>
                            <td class="table-title">Código de Rastreio</td>
                            <td class="table-title" width="80px;"></td>
                        </tr>
                        </thead>
                        <tbody id="dados_tabela">
                        {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
            </div>

            <ul id="pagination-trackings" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>

            <!-- Modal detalhes da venda-->
            @include('sales::details')
            <!-- End Modal -->

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
                                    <div class="col-lg-10 col-9"><p class="table-title"> Produto </p></div>
                                    <div class="col-lg-2 col-3 text-center"><p class="table-title"> Qtde </p></div>
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

                                <!-- js carrega... -->
                                <div class="tracking-timeline">
                                    <div class="tracking-timeline-row">
                                    </div>
                                    <div class="tracking-timeline-row">
                                    </div>
                                    <div class="tracking-timeline-row">
                                    </div>
                                </div>

                                <h4 style="margin-top: 40px"> Destino </h4>
                                <span id="tracking-delivery-address" class="table-title gray"></span>
                                <br>
                                <span id="tracking-delivery-zipcode" class="table-title gray"></span>
                                <br>
                                <span id="tracking-delivery-city" class="table-title gray"></span>
                                <a class='btn p-0 mt-sm-0 mt-10 pointer float-right btn-notify-trackingcode'
                                   title='Enviar e-mail com codigo de rastreio para o cliente'>
                                    <i class='icon wb-envelope' aria-hidden='true'></i>
                                    Enviar e-mail para o cliente
                                </a>

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

{{--            <div class="modal fade modal-3d-flip-vertical" id="modal-tracking" aria-hidden="true" role="dialog" tabindex="-1">--}}
{{--                <div class="modal-dialog modal-dialog-centered modal-simple">--}}
{{--                    <div id='modal-tracking-details' class="modal-content">--}}
{{--                        <div class="modal-header simple-border-bottom mb-10">--}}
{{--                            <h4 class="modal-title" id="modal-title">Detalhes do rastreamento</h4>--}}
{{--                            <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">--}}
{{--                                <i class="material-icons md-16">close</i>--}}
{{--                            </a>--}}
{{--                        </div>--}}
{{--                        <div class="modal-body">--}}
{{--                            <h3 id="tracking-code" class="text-uppercase">LO306849181CN</h3>--}}
{{--                            <div class="p-10">--}}
{{--                                <div class="row">--}}
{{--                                    <div class="col-lg-10 col-9"><p class="table-title"> Produto </p></div>--}}
{{--                                    <div class="col-lg-2 col-3 text-center"><p class="table-title"> Qtde </p></div>--}}
{{--                                </div>--}}
{{--                                <div class="row align-items-center mb-20">--}}
{{--                                    <div class="col-lg-10 col-9">--}}
{{--                                        <div class="row align-items-center pl-10">--}}
{{--                                            <img id="tracking-product-image" src="" width="50px" style="border-radius: 6px;">--}}
{{--                                            <h4 id="tracking-product-name" class="table-title ml-10 ellipsis" style="flex: 1">Produto Teste</h4>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-lg-2 col-3 text-center">--}}
{{--                                        <span id="tracking-product-amount" class="sm-text text-muted">5x</span>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="tracking-timeline">--}}
{{--                                    <div class="tracking-timeline-row">--}}
{{--                                        <div class="date-item">31/10/2019</div>--}}
{{--                                        <div class="date-item">01/11/2019</div>--}}
{{--                                        <div class="date-item">02/11/2019</div>--}}
{{--                                        <div class="date-item">03/11/2019</div>--}}
{{--                                    </div>--}}
{{--                                    <div class="tracking-timeline-row">--}}
{{--                                        <div class="step-item">--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                            <span class="step-dot"></span>--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                        </div>--}}
{{--                                        <div class="step-item">--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                            <span class="step-dot"></span>--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                        </div>--}}
{{--                                        <div class="step-item">--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                            <span class="step-dot"></span>--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                        </div>--}}
{{--                                        <div class="step-item">--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                            <span class="step-dot"></span>--}}
{{--                                            <span class="step-line"></span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="tracking-timeline-row">--}}
{{--                                        <div class="status-item">Postado</div>--}}
{{--                                        <div class="status-item">Em trânsito</div>--}}
{{--                                        <div class="status-item">Saiu para entrega</div>--}}
{{--                                        <div class="status-item">Entregue</div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <h4 style="margin-top: 40px"> Destino </h4>--}}
{{--                                <span id="tracking-delivery-address" class="table-title gray">Endereço: Avenida General Afonseca, 1475</span>--}}
{{--                                <br>--}}
{{--                                <span id="tracking-delivery-zipcode" class="table-title gray">CEP: 27520174</span>--}}
{{--                                <br>--}}
{{--                                <span id="tracking-delivery-city" class="table-title gray">Cidade: Resende/RJ</span>--}}
{{--                                <a class='btn p-1 pointer float-right btn-notify-trackingcode'--}}
{{--                                   title='Enviar e-mail com codigo de rastreio para o cliente'>--}}
{{--                                    <i class='icon wb-envelope' aria-hidden='true'></i>--}}
{{--                                    Enviar e-mail para o cliente--}}
{{--                                </a>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

            <!-- End Nodal -->

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="{{ asset('/modules/trackings/js/index.js?v=2') }}"></script>
    @endpush

@endsection
