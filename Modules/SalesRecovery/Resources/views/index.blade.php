@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=04') }}">
        <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
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
    <div class="page mb-0">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Recuperação de vendas</h1>
                </div>
                @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
                    <div class="col-6 text-right">
                        <div class="justify-content-end align-items-center" id="export-excel">
                            <div class="p-2 d-flex justify-content-end align-items-center">
                                <span class="o-download-cloud-1 mr-2"></span>
                                <div class="btn-group" role="group">
                                    <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                    <button id="bt_get_csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div id='project-not-empty' style='display:none'>
            <div class="page-content container">
                <div id="" class="card shadow p-20">
                    <div class="row align-items-baseline">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="type_recovery">Tipo de Recuperação</label>
                            <select name='select_type_recovery' id="type_recovery" class="form-control select-pad">
                                <option value="1" selected>Carrinho Abandonado</option>
                                <option value="5">Boleto Vencido</option>
                                <option value="3">Cartão Recusado</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="project">Projeto</label>
                            <select name='select_project' id="project" class="form-control select-pad"> </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="plan">Plano</label>
                            <select name='plan' id="plan" class="form-control select-pad" style='width:100%;' data-plugin="select2">
                                <option value="">Todos planos</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for='date-range-sales-recovery'>Filtrar Data</label>
                            <input name='date-range-sales-recovery' id='date-range-sales-recovery' class='select-pad' placeholder='Clique para editar...' readonly>
                        </div>
                    </div>
                    <div class="collapse" id="bt_collapse">
                        <div class="row pt-15">
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="client-name">Nome do Cliente</label>
                                <input name='cliente-name' id="client-name" value='' class="input-pad" type="text" placeholder="Nome">
                            </div>
                            <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                                <label for="client-cpf">CPF do Cliente</label>
                                <input name='client-cpf' id="client-cpf" value='' class="input-pad" type="text" placeholder="CPF" data-mask="000.000.000-00">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="height: 30px">
                        <div class="col-sm-6 col-xl-3 text-right mt-20 offset-xl-6">
                            <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                 data-toggle="collapse"
                                 data-target="#bt_collapse"
                                 aria-expanded="false"
                                 aria-controls="bt_collapse">
                                <img id="icon-filtro" src=" {{ asset('/modules/global/img/svg/filter-2-line.svg') }} "/>
                                <span id="text-filtro">Filtros avançados</span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3 text-right mt-20">
                            <div id="bt_filtro" class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center">
                                <img style="height: 12px; margin-right: 4px" src=" {{ asset('/modules/global/img/svg/check-all.svg') }} "/>
                                Aplicar filtros
                            </div>
                        </div>
                    </div>
                </div>
                <div class="fixhalf"></div>
                <!-- Aviso de Exportação -->
                <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                    <div class="d-flex">
                        <span class="o-info-help-1"></span>
                        <div class="w-full">
                            <strong class="font-size-16">Exportando seu relatório</strong>
                            <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por e-mail para:
                                <strong id="export-email"></strong> e aparecerá nas suas notificações. Pode levar algum tempo, dependendo de quantos registros você estiver exportando.
                            </p>
                        </div>
                        <i class="material-icons pointer" data-dismiss="alert">close</i>
                    </div>
                </div>
                <div class="card shadow" style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id='carrinhoAbandonado' class="table table-striped unify">
                            <thead>
                                <tr>
                                    <td class="table-title display-sm-none display-m-none display-lg-none">Data</td>
                                    <td class="table-title">Projeto</td>
                                    <td class="table-title display-sm-none display-m-none">Cliente</td>
                                    <td class="table-title">Email</td>
                                    <td class="table-title">Sms</td>
                                    <td class="table-title">Status</td>
                                    <td class="table-title">Valor</td>
                                    <td class="table-title display-sm-none"></td>
                                    <td class="table-title display-sm-none">Link</td>
                                    <td class="table-title display-sm-none">Detalhes</td>
                                </tr>
                            </thead>
                            <tbody id="table_data" class='min-row-height'>
                            </tbody>
                        </table>
                    </div>
                    <!-- Modal regerar boleto-->
                    <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_regerar_boleto" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-lg d-flex justify-content-center">
                            <div class="modal-content w-450" id="conteudo_modal_add">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" style="font-weight: 700;">Regerar boleto</h4>
                                </div>
                                <div class="pt-10 pr-20 pl-20 modal_regerar_boleto_body">
                                    <div class="form-group">
                                        <label for="date">Data de vencimeto do boleto:</label>
                                        <input name='date' id="date" class="form-control input-pad" type="date">
                                        <input type='hidden' name='saleId' id='saleId'>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="switch-holder">
                                                <label for="token" class='mb-10'>Aplicar desconto:</label>
                                                <br>
                                                <label class="switch">
                                                    <input type="checkbox" value='1' id="apply_discount" class='check'>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="div_discount" style="display:none">
                                        <div class="col-6">
                                            <label for="discount_type"> Tipo:</label>
                                            <select id="discount_type" class="form-control">
                                                <option value="percentage" selected>Porcentagem</option>
                                                <option value="value">Valor</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label id="label_discount_value" for="discount_value">Valor (ex: 20%)</label>
                                            <input id="discount_value" class="form-control" placeholder="Valor">
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="margin-top: 15px">
                                    <button id="bt_send" type="button" class="btn btn-success">Regerar</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                    <!-- Modal detalhes da venda-->
                    <div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                        <div class="modal-dialog modal-simple modal-sidebar ">
                            <div class="modal-content p-20 " style="width:500px;">
                                <div class="header-modal">
                                    <div class="row justify-content-between align-items-center" style="width: 100%;">
                                        <div class="col-lg-2"> &nbsp;</div>
                                        <div class="col-lg-8 text-center">
                                            <h4 id='modal-title'> Detalhes da venda </h4>
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <a role="button" data-dismiss="modal">
                                                <i class="material-icons pointer">close</i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body-cart-recovery">
                                    <div class="transition-details">
                                        <p id='date-as-hours' class="sm-text text-muted clear-fields">
                                        </p>
                                        <div class="status d-inline">
                                            <span class="clear-fields badge mr-5" id='status-checkout'></span>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="card shadow pr-20 pl-20 p-10">
                                        <div class="row">
                                            <div class="col-lg-3"><p class="table-title"> Produto </p></div>
                                            <div class="col-lg-9 text-right"><p class="text-muted"> Qtde </p></div>
                                        </div>
                                        {{-- Tabela produtos JS insere dados--}}
                                        <div id='table-product' class='clear-fields'>
                                        </div>
                                        <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                                            <div class="col-lg-6">
                                                <h4 class="table-title clear-fields"> Total </h4>
                                            </div>
                                            <div class="col-lg-6 text-right">
                                                <h4 id='total-value' class="table-title"></h4>
                                            </div>
                                        </div>
                                        {{-- Fim tabela produtos--}}
                                    </div>
                                    <div class="nav-tabs-horizontal">
                                        <div class="nav nav-tabs nav-tabs-line text-center" id="nav-tab" role="tablist">
                                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" style="width:50%;">Cliente</a>
                                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" style="width:50%;">Detalhes</a>
                                        </div>
                                    </div>
                                    <div class="tab-content p-10" id="nav-tabContent">
                                        <!-- CLIENTE -->
                                        <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                                            <h4> Dados Pessoais </h4>
                                            <span id='client-name-details' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='client-telephone' class='table-title gray clear-fields'></span>
                                            <a id='client-whatsapp' target='_blank' title='Enviar mensagem pelo whatsapp'>
                                                <span style="font-size: 21px" class="o-whatsapp-1"></span>
                                            </a>
                                            <br>
                                            <span id='client-email' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='client-document' class="table-title gray clear-fields"></span>
                                            <div id='div_delivery' style='display:none;'>
                                                <h4> Entrega </h4>
                                                <span id="client-street" class="table-title gray clear-fields"> </span>
                                                <br>
                                                <span id='client-zip-code' class="table-title gray clear-fields"> </span>
                                                <br>
                                                <span id='client-city-state' class="table-title gray clear-fields"></span>
                                            </div>
                                        </div>
                                        <!-- DETALHES  -->
                                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                            <h4> Dados Gerais </h4>
                                            <span id='sale-motive' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='link-sale' class="table-title gray clear-fields"></span>
                                            <br>
                                            <span id='checkout-ip' class="table-title gray clear-fields">   </span>
                                            <br>
                                            <span id='checkout-is-mobile' class="table-title gray clear-fields "> </span>
                                            <br>
                                            <span id='checkout-operational-system' class="table-title gray clear-fields "> </span>
                                            <br>
                                            <span id='checkout-browser' class="table-title gray  clear-fields"> </span>
                                            <br>
                                            <h4> Conversão </h4>
                                            <span id='checkout-src' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='checkout-utm-source' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='checkout-utm-medium' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='checkout-utm-campaign' class="table-title gray clear-fields"></span>
                                            <br>
                                            <span id='checkout-utm-term' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='checkout-utm-content' class="table-title gray clear-fields"> </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <!-- End Modal -->
                </div>
                <div class="row justify-content-center justify-content-md-end">
                    <ul id="pagination-salesRecovery" class="pl-5 pr-md-15 pb-20" style="margin-top:10px;position:relative;float:right;">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
        </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
    </div>

    <!-- Modal exportar relatorio -->
    <div id="modal-export-sale" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Informe o email para receber o relatório </h3>
                </div>
                <div class="modal-footer">
                    <input type="email" id="email_export">
                    <button type="button" class="btn btn-success btn-confirm-export-sale">
                        Enviar
                    </button>
                    <a id="btn-mobile-modal-close" class="btn btn-primary" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                        Fechar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    @push('scripts')

        <script src="{{ asset('modules/salesrecovery/js/salesrecovery.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{asset('modules/global/js/daterangepicker.min.js')}}'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>

    @endpush

@endsection

