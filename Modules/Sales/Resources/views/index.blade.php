@extends('layouts.master')

@section('content')
    @push('css')
        <link rel="stylesheet"
              href="{{ mix('build/layouts/sales/index.min.css') }}">
    @endpush

    <!-- Page -->
    <div class="page pb-0"
         style="margin-bottom: 0px !important;">

        @include('layouts.company-select', ['version' => 'mobile'])

        <div style="display: none"
             class="page-header container">
            <div class="row align-items-center justify-content-between"
                 style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Vendas</h1>
                </div>
                <!-- hasanyrole('account_owner|admin|finantial') -->
                @can('sales_manage')
                    <div class="col-6 text-right">
                        <div class="justify-content-end align-items-center"
                             id="export-excel"
                             style="display:none">
                            <div class="p-2 d-flex justify-content-end align-items-center">
                                <!-- <span class="o-download-cloud-1 mr-2"></span>
                                <div class="btn-group"
                                     role="group"> -->
                                    <!-- <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button> -->
                                    <!-- <button id="bt_get_csv"
                                            type="button"
                                            class="btn btn-round btn-default btn-outline btn-pill">.CSV</button> -->
                                <!-- </div> -->
                            </div>
                        </div>
                    </div>
                @endcan
                <!-- endhasanyrole -->
            </div>
        </div>

        <div id="project-not-empty"
             style="display:none">
            <div class="page-content container">
                <!-- Filtro -->
                <form id='filter_form'>
                    <div id=""
                         class="card shadow p-20">

                        <!-- FILTRO DE EXIBICAO PERMANENTE -->
                        <div class="row mb-md-15">

                            <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                <label for="transaction">Transação</label>
                                <input name='transaction'
                                       id="transaction"
                                       class="input-pad"
                                       placeholder="Transação">
                            </div>

                            <div class="col-sm-12 col-md-3 scrol mb-15 mb-sm-0">
                                <label for="status">Status</label>
                                <select name='sale_status'
                                        id="status"
                                        class="form-control applySelect2">
                                    <option value="">Todos status</option>
                                    <option value="1">Aprovado</option>
                                    <option value="2">Aguardando pagamento</option>
                                    <option value="4">Chargeback</option>
                                    <option value="7">Estornado</option>
                                    {{-- <option value="6">Em análise</option> --}}
                                    {{-- <option value="8">Parcialmente estornado</option> --}}
                                    <option value="20">Revisão Antifraude</option>
                                    <option value="21">Cancelado Antifraude</option>
                                    <option value="24">Em disputa</option>
                                </select>
                            </div>

                            <div class="col-sm-12 col-md-3 mb-sm-0">
                                <label for="date_type">Data</label>

                                <select name='date_type'
                                        id="date_type"
                                        class="sirius-select">
                                    <option value="start_date">Data do pedido</option>
                                    <option value="end_date">Data do pagamento</option>
                                </select>

                            </div>

                            <div class="col-sm-12 col-md-3 form-icons mb-15 mb-sm-0">
                                <label for="date_range">&nbsp;</label>
                                <i style="right: 20px; margin-top: 13px;"
                                   class="form-control-icon form-control-icon-right o-agenda-1 font-size-18"></i>
                                <input name='date_range'
                                       id="date_range"
                                       class="input-pad pr-30"
                                       placeholder="Clique para editar..."
                                       readonly>
                            </div>

                        </div>

                        <div id="bt_collapse"
                             class="collapse">

                            <div class="row mb-md-15">

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="forma">Forma de pagamento</label>
                                    <select name='select_payment_method'
                                            id="forma"
                                            class="form-control select-pad applySelect2">
                                        <option value="">Todas formas de pagamento</option>
                                        <option value="1">Cartão de crédito</option>
                                        <option value="2">Boleto</option>
                                        <option value="4">Pix</option>
                                    </select>
                                </div>

                                {{-- <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                        <label for="empresa">Empresa</label>
                                        <input type="text" disabled="" class="company_name">
                                        {{-- <select name="select_company" id="empresa" class="form-control select-pad select-company applySelect2">
                                            <option value="">Todas empresas</option>
                                        </select> - -}}
                        </div> --}}

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="projeto">Lojas</label>
                                    <select name='select_project'
                                            id="projeto"
                                            class="form-control select-pad applySelect2">
                                        <option value="">Todas lojas</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="plan">Plano</label>
                                    <select name='plan'
                                            id="plan"
                                            class="form-control select-pad applySelect2"
                                            style='width:100%;'>
                                        <option value="">Todos planos</option>
                                    </select>
                                </div>

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="cupom">Cupom</label>
                                    <input name="coupon"
                                           id="cupom"
                                           class="input-pad"
                                           placeholder="Código do cupom">
                                </div>

                            </div>

                            <div class="row mb-md-15">

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="valor">Comissão</label>
                                    <input name="value"
                                           id="valor"
                                           class="input-pad"
                                           placeholder="Valor da comissão">
                                </div>

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="comprador">Nome do cliente</label>
                                    <input name='client'
                                           id="comprador"
                                           class="input-pad"
                                           placeholder="Cliente">
                                </div>

                                <div class="col-sm-12 col-md-3 mb-15 mb-sm-0">
                                    <label for="customer_document">CPF do cliente</label>
                                    <input name='customer_document'
                                           id="customer_document"
                                           class="input-pad"
                                           placeholder="CPF"
                                           data-mask="000.000.000-00">
                                </div>

                            </div>

                            <div class="row mb-md-15 d-flex justify-content-between">

                                <div class="col-sm-12 col-md-3">
                                </div>

                                <div class="col-sm-12 col-md-8 mt-20 pr-0 pl-20"
                                     style="flex-grow: 2.134 !important;">

                                    <div class="row pt-15 d-flex justify-content-end mr-0">

                                        <div
                                             class='col-sm-4 col-md-3 mb-10 mb-sm-0 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                            <label class="switch mr-2">
                                                <input type="checkbox"
                                                       id='upsell'
                                                       name="upsell"
                                                       class='check'
                                                       value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text"> Upsell </span>
                                        </div>

                                        <div
                                             class='col col-sm-4 col-md-3 mb-10 mb-sm-0 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                            <label class="switch mr-2">
                                                <input type="checkbox"
                                                       id='order-bump'
                                                       name="order_bump"
                                                       class='check'
                                                       value='0'>
                                                <span class="slider round"></span>
                                            </label>
                                            <span class="switch-text"> Order Bump </span>
                                        </div>

                                        <!-- <div
                                                                 class='col col-sm-4 col-md-3 mb-10 mb-sm-0 d-flex align-items-center justify-content-sm-center justify-content-md-start'>
                                                                <label class="switch mr-2">
                                                                    <input type="checkbox"
                                                                           id='cashback'
                                                                           name="cashback"
                                                                           class='check shopify_error'
                                                                           value='0'>
                                                                    <span class="slider round"></span>
                                                                </label>
                                                                <span class="switch-text"> Cashback </span>
                                                            </div> -->
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="row mb-10 mb-sm-0">
                            <div class="col-6 col-xl-3 mt-20 offset-xl-6 pr-0">
                                <div class="btn btn-light-1 w-p100 bold d-flex justify-content-center align-items-center"
                                     data-toggle="collapse"
                                     data-target="#bt_collapse"
                                     aria-expanded="false"
                                     aria-controls="bt_collapse">
                                    <img id="icon-filtro"
                                         class="hidden-xs-down"
                                         src=" {{ mix('build/global/img/svg/filter-2-line.svg') }} " />
                                    <div id="text-filtro"
                                         style="white-space: normal">Filtros <br class="d-flex d-sm-none"> avançados</div>
                                </div>
                            </div>

                            <div class="col-6 col-xl-3 mt-20">
                                <div id="bt_filtro"
                                     class="btn btn-primary-1 w-p100 bold d-flex justify-content-center align-items-center"
                                     style="white-space: normal">
                                    <img class="hidden-xs-down"
                                         style="height: 12px; margin-right: 4px"
                                         src=" {{ mix('build/global/img/svg/check-all.svg') }} " />
                                    Aplicar <br class="d-flex d-sm-none"> filtros
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
                <!-- Aviso de Exportação -->
                <div id="alert-export"
                     class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10"
                     style="display:none;">
                    <div class="d-flex">
                        <span class="o-info-help-1"></span>
                        <div class="w-full">
                            <strong class="font-size-16">Exportando seu relatório</strong>
                            <p class="font-size-14 pr-md-100 mb-0">Sua exportação será entregue por e-mail para:
                                <strong id="export-email"></strong> e aparecerá nas suas notificações. Pode levar algum
                                tempo, dependendo de quantos registros você estiver exportando.
                            </p>
                        </div>
                        <i class="material-icons pointer"
                           data-dismiss="alert">close</i>
                    </div>
                </div>
                <!-- Resumo -->
                <div class="fixhalf"></div>
                <!-- unlessrole('attendance|finantial')                     -->
                @can('sales_manage')
                    <div class="row justify-content-center">
                        <div class="col-md-3">
                            <div class="card shadow"
                                 style='display:block;'>
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600">Quantidade</h5>
                                    <h4 id="total-sales"
                                        class="number"></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card shadow"
                                 style='display:block;'>
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600"> Comissão </h5>
                                    <h4 id="commission"
                                        class="number"></h4>
                                </div>
                                <div class="s-border-right dark-gray"></div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card shadow"
                                 style='display:block;'>
                                <div class="card-body">
                                    <h5 class="font-size-14 gray-600"> Total</h5>
                                    <h4 id="total"
                                        class="number"></h4>
                                </div>
                                <div class="s-border-right dark-gray"></div>
                            </div>
                        </div>

                        <div class="col-md-3 d-sm-none d-md-block">
                            <div style='display:block;'>
                                <div>
                                    <h5 class="font-size-14 gray-600"> Acesso rápido </h5>
                                    <ul class="quick-list">
                                        <li>
                                            <a href="{{ route('recovery.index') }}">Recuperação</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('trackings.index') }}">Rastreamento</a>
                                        </li>
                                        <li>
                                            <a href="{{ route('reports.resume') }}">Relatórios</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                @endcan
                <!-- Tabela -->
                <div class="fixhalf"></div>

                <div class="col-lg-12 p-0 pb-10">

                    <div class="card shadow">

                        <div class="page-invoice-table table-responsive">

                            <table id="tabela_vendas"
                                   class="table unify mb-0">

                                <thead>

                                    <tr>
                                        <td>Transação</td>
                                        <td>Descrição</td>
                                        <td class="d-none client-collumn">Cliente</td>
                                        <td class="text-center">Forma</td>
                                        <td class="text-center">Status</td>
                                        <td class="text-nowrap">Iniciada em</td>
                                        <td>Pagamento</td>
                                        <td class="text-center">Comissão</td>
                                        <td width="80px;"> &nbsp;</td>
                                    </tr>

                                </thead>

                                <tbody id="dados_tabela"
                                       img-empty="{!! mix('build/global/img/vendas.svg') !!}">
                                    {{-- js carrega... --}}
                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>
                <div id="container-pagination"
                     class="row justify-content-center justify-content-md-end pr-md-15 pb-25">
                    <ul id="pagination-sales"
                        class="pagination-sm pagination-style mb-70"
                        style="position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
                <!-- Modal detalhes da venda-->
                @include('sales::details')
                <!-- End Modal -->
            </div>
        </div>
        {{-- Quando não tem loja cadastrado --}}
        @include('projects::empty')
        {{-- FIM loja nao existem lojas --}}
    </div>
    <!-- Modal regerar boleto-->
    <div class="modal fade example-modal-lg modal-3d-flip-vertical"
         id="modal_regerar_boleto"
         aria-hidden="true"
         aria-labelledby="exampleModalTitle"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-lg d-flex justify-content-center">
            <div class="modal-content w-450"
                 id="conteudo_modal_add">
                <div class="modal-header">
                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title"
                        style="font-weight: 700;">Regerar boleto</h4>
                </div>
                <div class="pt-10 pr-20 pl-20 modal_regerar_boleto_body">
                    <div class="form-group">
                        <label for="date">Data de vencimeto do boleto:</label>
                        <input name='date'
                               id="date"
                               class="form-control input-pad"
                               type="date">
                        <input type='hidden'
                               name='saleId'
                               id='saleId'>
                    </div>
                    <div class="col-6">
                        <div class="switch-holder">
                            <label for="token"
                                   class='mb-10'>Aplicar desconto:</label>
                            <br>
                            <label class="switch">
                                <input type="checkbox"
                                       value='1'
                                       id="apply_discount"
                                       class='check'>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="row"
                         id="div_discount"
                         style="display:none">
                        <div class="col-6">
                            <label for="discount_type"> Tipo:</label>
                            <select id="discount_type"
                                    class="form-control">
                                <option value="percentage"
                                        selected>Porcentagem</option>
                                <option value="value">Valor</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label id="label_discount_value"
                                   for="discount_value">Valor (ex: 20%)</label>
                            <input id="discount_value"
                                   class="form-control"
                                   placeholder="Valor">
                        </div>
                    </div>
                </div>
                <div class="modal-footer"
                     style="margin-top: 15px">
                    <button id="bt_send"
                            type="button"
                            class="btn btn-success">Regerar</button>
                    <button type="button"
                            class="btn btn-danger"
                            data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal estonar transação-->
    @include('sales::modal_refund_transaction')

    <!-- Modal estonar boleto-->
    <div id="modal-refund-billet"
         class="modal fade example-modal-lg modal-3d-flip-vertical"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class="modal-header simple-border-bottom mb-10">
                    <h4 class="modal-title"
                        id="modal-title">Estornar boleto</h4>
                    <a id="modal-button-close"
                       class="pointer close"
                       role="button"
                       data-dismiss="modal"
                       aria-label="Close">
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

                <div class="form-group">
                    <label for="refund-observation-billet">Causa do estorno</label>
                    <textarea class="form-control"
                              id="refund-observation-billet"
                              rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <a id="btn-mobile-modal-close"
                       class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none"
                       style='color:white'
                       role="button"
                       data-dismiss="modal"
                       aria-label="Close">
                        Fechar
                    </a>
                    <button type="button"
                            class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-confirm-refund-billet"
                            total="">
                        Estornar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal gerar ordem shopify -->
    <div id="modal-new-order-shopify"
         class="modal fade example-modal-lg modal-3d-flip-vertical"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Realmente deseja regerar ordem no <b>SHOPIFY</b>? </h3>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close"
                       class="btn btn-primary"
                       style='color:white'
                       role="button"
                       data-dismiss="modal"
                       aria-label="Close">
                        Fechar
                    </a>
                    <button type="button"
                            class="btn btn-success btn-confirm-new-order-shopify"
                            data-dismiss="modal">
                        Gerar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal gerar ordem woocommerce -->
    <div id="modal-new-order-woocommerce"
         class="modal fade example-modal-lg modal-3d-flip-vertical"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Realmente deseja regerar ordem no <b>WOOCOMMERCE</b>? </h3>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close"
                       class="btn btn-primary"
                       style='color:white'
                       role="button"
                       data-dismiss="modal"
                       aria-label="Close">
                        Fechar
                    </a>
                    <button type="button"
                            class="btn btn-success btn-confirm-new-order-woocommerce"
                            data-dismiss="modal">
                        Gerar
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal exportar relatorio -->
    <div id="modal-export-sale"
         class="modal fade example-modal-lg modal-3d-flip-vertical"
         role="dialog"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-simple">
            <div class="modal-content p-10">
                <div class='my-20 mx-20 text-center'>
                    <h3 class="black"> Informe o email para receber o relatório </h3>
                </div>
                <div class="modal-footer">
                    <input type="email"
                           id="email_export">
                    <button type="button"
                            class="btn btn-success btn-confirm-export-sale">
                        Enviar
                    </button>
                    <a id="btn-mobile-modal-close"
                       class="btn btn-primary"
                       style='color:white'
                       role="button"
                       data-dismiss="modal"
                       aria-label="Close">
                        Fechar
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
    @push('scripts')
        <script src="{{ mix('build/layouts/sales/index.min.js') }}"></script>
    @endpush
@endsection
