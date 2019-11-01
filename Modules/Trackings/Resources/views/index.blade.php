@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/trackings/css/index.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Rastreamentos</h1>
                </div>
            </div>
        </div>
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
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                        <label for="date_updated">Data de atualização</label>
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
                    <div class="col-md-3">
                        <h6 class="text-center text-info"><i class="material-icons align-middle mr-1"> check_circle </i> Total</h6>
                        <h4 id="total-trackings" class="number text-center text-info"></h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-center text-success"><i class="material-icons align-middle mr-1"> check_circle </i> Entregues</h6>
                        <h4 id="percentual-delivered" class="number text-center text-success"></h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-center text-info"><i class="material-icons align-middle mr-1"> local_shipping </i> Em trânsito</h6>
                        <h4 id="percentual-dispatched" class="number text-center text-info"></h4>
                    </div>
                    <div class="col-md-3">
                        <h6 class="text-center text-danger"><i class="material-icons align-middle mr-1" > error </i> Problema na entrega</h6>
                        <h4 id="percentual-exception" class="number text-center text-danger"></h4>
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
                            <td class="table-title">Venda</td>
                            <td class="table-title">Produto</td>
                            <td class="table-title">Rastreio</td>
                            <td class="table-title">Status</td>
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
            <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal-tracking" aria-hidden="true" role="dialog" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-simple">
                    <div id='modal-tracking-details' class="modal-content">
                        <div class="modal-header simple-border-bottom mb-10">
                            <h4 class="modal-title" id="modal-title">Detalhes do rastreamento</h4>
                            <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                                <i class="material-icons md-16">close</i>
                            </a>
                        </div>
                        <div class="modal-body">
                            <h3 id="tracking-code" class="text-uppercase">LO306849181CN</h3>
                            <div class="p-10">
                                <div class="row">
                                    <div class="col-lg-10 col-9"><p class="table-title"> Produto </p></div>
                                    <div class="col-lg-2 col-3 text-center"><p class="table-title"> Qtde </p></div>
                                </div>
                                <div class="row align-items-center mb-20">
                                    <div class="col-lg-10 col-9">
                                        <div class="row align-items-center pl-10">
                                            <img id="tracking-product-image" src="https://cdn.entrypoint.directory/assets/46588/produtos/7054/produto_de_teste_2710_1_20180510113746.png" width="50px" style="border-radius: 6px;">
                                            <h4 id="tracking-product-name" class="table-title ml-10 ellipsis" style="flex: 1">Produto Teste</h4>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-3 text-center">
                                        <span id="tracking-product-amount" class="sm-text text-muted">5x</span>
                                    </div>
                                </div>
                                <div class="tracking-timeline">
                                    <div class="tracking-timeline-row">
                                        <div class="date-item">31/10/2019</div>
                                        <div class="date-item">01/11/2019</div>
                                        <div class="date-item">02/11/2019</div>
                                        <div class="date-item">03/11/2019</div>
                                    </div>
                                    <div class="tracking-timeline-row">
                                        <div class="step-item">
                                            <span class="step-line"></span>
                                            <span class="step-dot"></span>
                                            <span class="step-line"></span>
                                        </div>
                                        <div class="step-item">
                                            <span class="step-line"></span>
                                            <span class="step-dot"></span>
                                            <span class="step-line"></span>
                                        </div>
                                        <div class="step-item">
                                            <span class="step-line"></span>
                                            <span class="step-dot"></span>
                                            <span class="step-line"></span>
                                        </div>
                                        <div class="step-item">
                                            <span class="step-line"></span>
                                            <span class="step-dot"></span>
                                            <span class="step-line"></span>
                                        </div>
                                    </div>
                                    <div class="tracking-timeline-row">
                                        <div class="status-item">Postado</div>
                                        <div class="status-item">Em transito</div>
                                        <div class="status-item">Saiu para entrega</div>
                                        <div class="status-item">Entregue</div>
                                    </div>
                                </div>
                                <h4 style="margin-top: 40px"> Destino </h4>
                                <span id="tracking-delivery-address" class="table-title gray">Endereço: Avenida General Afonseca, 1475</span>
                                <br>
                                <span id="tracking-delivery-zipcode" class="table-title gray">CEP: 27520174</span>
                                <br>
                                <span id="tracking-delivery-city" class="table-title gray">Cidade: Resende/RJ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Nodal -->
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="{{ asset('/modules/trackings/js/index.js?v=1') }}"></script>
    @endpush

@endsection

