@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('modules/trackings/css/index.css?v=1') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Rastreamentos</h1>
                </div>
                <div class="col-6 text-right">
                    <div class="p-2 d-flex align-items-center justify-content-end" id="import-excel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                        </svg>
                        <div class="btn-group mr-10" role="group">
                            <button id="btn-export-xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                            <button id="btn-export-csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                        </div>
                        <svg class="icon-download" style="margin-right: 5px; display:none;" width="20" height="15" viewBox="0 0 24 18" xmlns="http://www.w3.org/2000/svg">
                            <path d="m4.056241,17.644411c-1.814315,-0.588427 -3.128468,-1.922193 -3.667859,-3.726701c-0.205949,-0.706112 -0.205949,-2.255635 0,-2.961747c0.539391,-1.804508 1.873158,-3.157889 3.667859,-3.716894l0.529584,-0.166721l0.127492,-0.725726c0.284406,-1.69663 0.872833,-2.81464 2.13795,-4.06995c0.764954,-0.764954 1.039554,-0.970904 1.824122,-1.343574c1.225889,-0.588427 1.951615,-0.755147 3.32461,-0.755147c1.372995,0 2.098721,0.166721 3.32461,0.755147c0.784569,0.37267 1.059168,0.578619 1.824122,1.343574c1.265117,1.25531 1.853544,2.37332 2.13795,4.06995l0.127492,0.725726l0.529584,0.166721c1.794701,0.559005 3.128468,1.912386 3.667859,3.716894c0.205949,0.706112 0.205949,2.255635 0,2.961747c-0.549198,1.824122 -1.932,3.206925 -3.756123,3.756123c-0.421706,0.127492 -1.000325,0.156914 -2.726376,0.156914l-2.186985,0l0,-0.970904l0,-0.970904l2.039879,-0.039228c2.471391,-0.058843 2.873483,-0.176528 3.756123,-1.049361c0.696305,-0.706112 0.970904,-1.382802 0.970904,-2.412549c0,-1.333767 -0.598234,-2.363513 -1.735858,-2.961747c-0.539391,-0.294213 -1.784894,-0.617848 -2.334092,-0.617848c-0.245178,0 -0.245178,0 -0.245178,-0.863026c0,-2.667534 -1.471066,-4.795676 -3.814965,-5.511595c-0.784569,-0.245178 -2.285056,-0.264792 -3.059818,-0.039228c-2.334092,0.696305 -3.736508,2.638112 -3.883615,5.39391l-0.058843,0.990518l-0.657076,0.06865c-2.236021,0.205949 -3.62863,1.55933 -3.62863,3.540366c0,1.029746 0.274599,1.706437 0.970904,2.412549c0.88264,0.872833 1.284731,0.990518 3.76593,1.049361l2.030072,0.039228l0,0.970904l0,0.970904l-2.226214,-0.009807c-1.863351,0 -2.324285,-0.029421 -2.775412,-0.176528z"/>
                            <path d="m11.019288,15.444348l0,-2.451777l-1.490681,0l-1.500488,0l1.990843,-1.981036l1.981036,-1.990843l1.981036,1.990843l1.990843,1.981036l-1.500488,0l-1.490681,0l0,2.451777l0,2.451777l-0.980711,0l-0.980711,0l0,-2.451777z"/>
                        </svg>
                        <button id="btn-import-xls" type="button" class="btn btn-round btn-default btn-outline" style="min-width: 118px; display:none;">IMPORTAR</button>
                        <input type="file" id="input-import-xls" style="display:none" accept=".csv,.xlsx">
                    </div>
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
                                            <img id="tracking-product-image" src="" width="50px" style="border-radius: 6px;">
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
                                        <div class="status-item">Em trânsito</div>
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
                                <a class='btn p-1 pointer float-right btn-notify-trackingcode'
                                   title='Enviar e-mail com codigo de rastreio para o cliente'>
                                    <i class='icon wb-envelope' aria-hidden='true'></i>
                                    Enviar e-mail para o cliente
                                </a>
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
        <script src="{{ asset('/modules/trackings/js/index.js?v=4') }}"></script>
    @endpush

@endsection

