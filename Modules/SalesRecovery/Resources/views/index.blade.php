@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
        <link rel="stylesheet" href="{{ asset('/modules/global/css/switch.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Recuperação de vendas</h1>
        </div>
        <div class="page-content container">
            <div id='project-not-empty' style='display:none'>
                <div id="" class="card shadow p-20">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <label for="project">Projeto</label>
                            <select name='select_project' id="project" class="form-control select-pad"> </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <label for="type_recovery">Tipo de Recuperação</label>
                            <select name='select_type_recovery' id="type_recovery" class="form-control select-pad">
                                <option value="1" selected>Carrinho Abandonado</option>
                                <option value="2">Boleto Vencido</option>
                                <option value="3">Cartão Recusado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <label for="start_date">Data inicial</label>
                            <input name='start_date' id="start_date" timezone='' class="form-control input-pad" type="date">
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <label for="end_date">Data final</label>
                            <input name='end_date' id="end_date" class="form-control input-pad" type="date">
                        </div>
                    </div>
                    <div class="row mt-15">
                        <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                            <label for="client-name">Nome do Cliente</label>
                            <input name='cliente-name' id="client-name" value='' class="input-pad" type="text" placeholder="Nome">
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-2 mt-30 text-right float-right">
                            <button id="bt_filtro" class="btn btn-primary col-12">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
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
                        <div class="modal-dialog modal-simple modal-sidebar modal-lg">
                            <div class="modal-content p-20 " style="">
                                <div class="header-modal">
                                    <div class="row justify-content-between align-items-center" style="width: 100%;">
                                        <div class="col-lg-2"> &nbsp;</div>
                                        <div class="col-lg-8 text-center"><h4 id='modal-title'> Detalhes da venda </h4>
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <a role="button" data-dismiss="modal">
                                                <i class="material-icons pointer">close</i></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-body">
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
                                            <a id='client-whatsapp' target='_blank'>
                                                <img src="{!! asset('modules/global/img/whatsapplogo.png') !!}" width="25px">
                                            </a>
                                            <br>
                                            <span id='client-email' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='client-document' class="table-title gray clear-fields"></span>
                                            <h4> Entrega </h4>
                                            <span id="client-street" class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='client-zip-code' class="table-title gray clear-fields"> </span>
                                            <br>
                                            <span id='client-city-state' class="table-title gray clear-fields"></span>
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
                <div class="row">
                    <div class="col-12">
                        <ul id="pagination-salesRecovery" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                            {{-- js carrega... --}}
                        </ul>
                    </div>
                </div>
            </div>
            {{-- Quando não tem projeto cadastrado  --}}
            <div id='project-empty' class="content-error text-center" style='display:none'>
                <link rel="stylesheet" href="modules/global/css/empty.css">
                <img src="modules/global/img/emptyprojetos.svg" width="250px">
                <h1 class="big gray">Você ainda não tem nenhum projeto!</h1>
                <p class="desc gray">Que tal criar um primeiro projeto para começar a vender? </p>
                <a href="/projects/create" class="btn btn-primary gradient">Cadastrar primeiro projeto</a>
            </div>
            {{-- FIM projeto nao existem projetos--}}
        </div>
    </div>

    @push('scripts')

        <script src="{{ asset('modules/salesrecovery/js/salesrecovery.js?v=1') }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>

    @endpush

@endsection

