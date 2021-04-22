@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css?v=05') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css?v=02') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css?v=4545') }}">
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

            .badge {
                color: white;
                padding: 5px 15px !important;
                border-radius: 16px;
                font-weight: 700;
            }

            .badge.badge-success  {
                background-color: #5EE2A1;
            }
        </style>
    @endpush

    <!-- Page -->
        <div class="page">
            <div style="display: none" class="page-header container">
                <div class="row align-items-center justify-content-between" style="min-height:50px">
                    <div class="col-6">
                        <h1 class="page-title">Vendas</h1>
                    </div>
                    @if(auth()->user()->hasRole('account_owner') || auth()->user()->hasRole('admin'))
                        <div class="col-6 text-right">
                            <div class="justify-content-end align-items-center" id="export-excel" style="display:none">
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

            <div id="project-not-empty" style="display:none">
                <div class="page-content container">
                    <!-- Filtro -->
                    <div class="fixhalf"></div>
                    <form id='filter_form'>
                        <div id="" class="card shadow p-20">
                            <div class="row align-items-baseline mb-md-15">
                                <div class="col-sm-12 col-md">
                                    <label for="projeto">Projeto</label>
                                    <select name='select_project' id="projeto" class="form-control select-pad">
                                        <option value="">Todos projetos</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md">
                                    <label for="plan">Plano</label>
                                    <select name='plan' id="plan" class="form-control select-pad" style='width:100%;' data-plugin="select2">
                                        <option value="">Todos planos</option>
                                    </select>
                                </div>
                                <div class="col-sm-12 col-md">
                                    <label for="transaction">Transação</label>
                                    <input name='transaction' id="transaction" class="input-pad" placeholder="Transação">
                                </div>
                                <div class="col-sm-6 col-md">
                                    <label for="date_type">Data</label>
                                    <select name='date_type' id="date_type" class="form-control select-pad">
                                        <option value="start_date">Data do pedido</option>
                                        <option value="end_date">Data do pagamento</option>
                                    </select>
                                </div>
                                <div class="col-sm-6 col-md form-icons">
                                    <label for="date_range">&nbsp;</label>
                                    <i style="right: 20px;" class="form-control-icon form-control-icon-right o-agenda-1 mt-15 font-size-18"></i>
                                    <input name='date_range' id="date_range" class="select-pad pr-30" placeholder="Clique para editar..." readonly>
                                </div>
                            </div>
                            <div class="row collapse" id="bt_collapse">
                                <div class="d-flex flex-wrap">
                                    <div class="col-sm-12 col-md">
                                        <label for="comprador">Nome do cliente</label>
                                        <input name='client' id="comprador" class="input-pad" placeholder="Cliente">
                                    </div>
                                    <div class="col-sm-12 col-md">
                                        <label for="customer_document">CPF do cliente</label>
                                        <input name='customer_document' id="customer_document" class="input-pad" placeholder="CPF" data-mask="000.000.000-00">
                                    </div>
                                    <div class="col-sm-12 col-md">
                                        <label for="status">Status</label>
                                        <select name='sale_status' id="status" class="form-control select-pad">
                                            <option value="">Todos status</option>
                                            <option value="1">Aprovado</option>
                                            <option value="2">Aguardando pagamento</option>
                                            <option value="4">Chargeback</option>
                                            <option value="7">Estornado</option>
                                            {{--                                <option value="6">Em análise</option>--}}
                                            {{--                                <option value="8">Parcialmente estornado</option>--}}
                                            <option value="chargeback_recovered">Chargeback recuperado</option>
                                            <option value="20">Revisão Antifraude</option>
                                            <option value="24">Em disputa</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md">
                                        <label for="forma">Forma de pagamento</label>
                                        <select name='select_payment_method' id="forma" class="form-control select-pad">
                                            <option value="">Boleto e cartão de crédito</option>
                                            <option value="1">Cartão de crédito</option>
                                            <option value="2">Boleto</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-12 col-md d-flex align-items-center flex-wrap mt-15">
                                        <div class='col-sm-6 col-md-12 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                            <label class="switch mr-2">
                                                <input type="checkbox" id='upsell' name="upsell" class='check' value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text"> Upsell </span>
                                        </div>
                                        <div class='col-sm-6 col-md-12 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                            <label class="switch mr-2">
                                                <input type="checkbox" id='order-bump' name="order_bump" class='check' value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text"> Order Bump </span>
                                        </div>
                                        <div class='col-sm-6 col-md-12 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                            <label class="switch mr-2">
                                                <input type="checkbox" id='shopify_error' name="shopify_error" class='check shopify_error' value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text"> Shopify Erros </span>
                                        </div>
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
                    </form>
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
                    <!-- Resumo -->
                    <div class="fixhalf"></div>
                    @if(!auth()->user()->hasRole('attendance'))
                        <div class="row justify-content-center">
                            <div class="col-md-3">
                                <div class="card shadow" style='display:block;'>
                                    <div class="card-body">
                                        <h5 class="gray font-size-16"> Quantidade de vendas </h5>
                                        <h4 id="total-sales" class="number"></h4>
                                    </div>
                                    <div class="s-border-right green"></div>
                                </div>
                            </div>

                                <div class="col-md-3">
                                    <div class="card shadow" style='display:block;'>
                                        <div class="card-body">
                                            <h5 class="gray font-size-16"> Comissão </h5>
                                            <h4 id="commission" class="number"></h4>
                                        </div>
                                        <div class="s-border-right green"></div>
                                    </div>
                                </div>

                            <div class="col-md-3">
                                <div class="card shadow" style='display:block;'>
                                    <div class="card-body">
                                        <h5 class="gray font-size-16"> Total</h5>
                                        <h4 id="total" class="number"></h4>
                                    </div>
                                    <div class="s-border-right green"></div>
                                </div>
                            </div>

                            <div class="col-md-3 d-sm-none d-md-block">
                                <div style='display:block;'>
                                    <div>
                                        <h5 class="gray font-size-16"> Acesso rápido </h5>
                                        <ul class="quick-list">
                                            <li>
                                                 <a href="{{ route('recovery.index') }}">Recuperação</a>
                                            </li>
                                            <li>
                                                 <a href="{{ route('trackings.index') }}">Rastreamento</a>
                                            </li>
                                            <li>
                                                 <a href="{{ route('reports.index') }}">Relatórios</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>
                @endif
                <!-- Tabela -->
                    <div class="fixhalf"></div>
                    <div class="card shadow " style="min-height: 300px">
                        <div class="page-invoice-table table-responsive">
                            <table id="tabela_vendas" class="table-vendas table table-striped unify" style="">
                                <thead>
                                <tr>
                                    <td class="table-title display-sm-none display-m-none  display-lg-none">Transação</td>
                                    <td class="table-title">Descrição</td>
                                    <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                    <td class="table-title">Forma</td>
                                    <td class="table-title">Status</td>
                                    <td class="table-title display-sm-none display-m-none">Iniciada em</td>
                                    <td class="table-title display-sm-none">Pagamento</td>
                                    <td class="table-title">Comissão</td>
                                    <td class="table-title" width="80px;"> &nbsp;</td>
                                </tr>
                                </thead>
                                <tbody id="dados_tabela">
                                {{-- js carrega... --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <ul id="pagination-sales" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right;margin-bottom:100px;">
                        {{-- js carrega... --}}
                    </ul>
                <!-- Modal detalhes da venda-->
                    @include('sales::details')
                <!-- End Modal -->
                </div>
            </div>
        {{-- Quando não tem projeto cadastrado  --}}
            @include('projects::empty')
        {{-- FIM projeto nao existem projetos--}}
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

    <!-- Modal estonar transação-->
    <div id="modal-refund-transaction" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title" id="modal-title">Estornar transação</h4>
                    <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Após confirmada, essa operação não poderá ser desfeita!</p>
                </div>
                <div class="row d-none">
                    <div class="col-3"></div>
                    <div class="col-3">
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="radioTotalRefund" name="radio-stacked" required checked>
                            <label class="custom-control-label" for="radioTotalRefund">Estorno total</label>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" class="custom-control-input" id="radioPartialRefund" name="radio-stacked" disabled>
                            <label class="custom-control-label" for="radioPartialRefund">Estorno Parcial</label>
                        </div>
                    </div>
                    <div class="col-3"></div>
                </div>
                <div class="text-center pt-20 d-none" style="min-height:62px;">
                    <div class="value-partial-refund" style="display: none;">
                        <strong class="font-size-14">Valor a ser estornado: </strong> R$
                        <input type="text" name="refundAmount" id="refundAmount" style="width: 200px;" maxlength="9">
                    </div>
                </div>
                <div class="form-group">
                    <label for="refund_observation">Causa do estorno</label>
                    <textarea class="form-control" id="refund_observation" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                        Fechar
                    </a>
                    <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-confirm-refund-transaction" total="" data-dismiss="modal">
                        Estornar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->


    <!-- Modal estonar boleto-->
    <div id="modal-refund-billet" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title" id="modal-title">Estornar boleto</h4>
                    <a id="modal-button-close" class="close-card pointer close" role="button" data-dismiss="modal" aria-label="Close">
                        <i class="material-icons md-16">close</i>
                    </a>
                </div>
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Você tem certeza? </h3>
                    <p class="gray"> Após confirmada, essa operação não poderá ser desfeita!</p>
                    <small>OBS: Taxa de
                        <label class='billet-refunded-tax-value'></label>
                        pelo estorno
                    </small>
                </div>
                <div class="text-center">
                    <strong class="font-size-14">Valor a ser estornado:
                        <label id="refundBilletAmount"></label>
                    </strong>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                        Fechar
                    </a>
                    <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-confirm-refund-billet" total="" data-dismiss="modal">
                        Estornar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal gerar ordem shopify -->
    <div id="modal-new-order-shopify" class="modal fade example-modal-lg modal-3d-flip-vertical" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Realmente deseja regerar ordem no <b>SHOPIFY</b>? </h3>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close" class="btn btn-primary" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                        Fechar
                    </a>
                    <button type="button" class="btn btn-success btn-confirm-new-order-shopify" data-dismiss="modal">
                        Gerar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

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
        <script src="{{ asset('/modules/sales/js/index.js?v=' . random_int(100, 10000)) }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
    @endpush

@endsection

