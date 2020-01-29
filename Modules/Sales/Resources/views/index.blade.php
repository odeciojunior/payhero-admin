@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">Vendas</h1>
                </div>
                <div class="col-6 text-right">
                    <div class="justify-content-end align-items-center" id="export-excel" style="display:none">
                        <div class="p-2 align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon-download" width="20" height="20" viewBox="0 0 24 24">
                                <path d="M8 20h3v-5h2v5h3l-4 4-4-4zm11.479-12.908c-.212-3.951-3.473-7.092-7.479-7.092s-7.267 3.141-7.479 7.092c-2.57.463-4.521 2.706-4.521 5.408 0 3.037 2.463 5.5 5.5 5.5h3.5v-2h-3.5c-1.93 0-3.5-1.57-3.5-3.5 0-2.797 2.479-3.833 4.433-3.72-.167-4.218 2.208-6.78 5.567-6.78 3.453 0 5.891 2.797 5.567 6.78 1.745-.046 4.433.751 4.433 3.72 0 1.93-1.57 3.5-3.5 3.5h-3.5v2h3.5c3.037 0 5.5-2.463 5.5-5.5 0-2.702-1.951-4.945-4.521-5.408z"/>
                            </svg>
                            <div class="btn-group" role="group">
                                <button id="bt_get_xls" type="button" class="btn btn-round btn-default btn-outline btn-pill-left">.XLS</button>
                                <button id="bt_get_csv" type="button" class="btn btn-round btn-default btn-outline btn-pill-right">.CSV</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="page-content container" style="display:none">
            <!-- Filtro -->
            <div class="fixhalf"></div>
            <form id='filter_form'>
                <div id="" class="card shadow p-20">
                    <div class="row align-items-baseline">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="projeto">Projeto</label>
                            <select name='select_project' id="projeto" class="form-control select-pad">
                                <option value="">Todos projetos</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="forma">Forma de pagamento</label>
                            <select name='select_payment_method' id="forma" class="form-control select-pad">
                                <option value="">Boleto e cartão de crédito</option>
                                <option value="1">Cartão de crédito</option>
                                <option value="2">Boleto</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="status">Status</label>
                            <select name='sale_status' id="status" class="form-control select-pad">
                                <option value="">Todos status</option>
                                <option value="1">Aprovado</option>
                                <option value="2">Aguardando pagamento</option>
                                <option value="4">Chargeback</option>
                                <option value="7">Estornado</option>
                                <option value="6">Em análise</option>
                                <option value="20">Análise Antifraude</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="comprador">Nome do cliente</label>
                            <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                        </div>
                    </div>
                    <div class="row mt-15">
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="comprador">Transação</label>
                            <input name='transaction' id="transaction" class="input-pad" placeholder="transação">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="date_type">Data</label>
                            <select name='date_type' id="date_type" class="form-control select-pad">
                                <option value="start_date">Data do pedido</option>
                                <option value="end_date">Data do pagamento</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <input name='date_range' id="date_range" class="select-pad" placeholder="Clique para editar..." readonly style="margin-top:30px">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-1 col-12" style='text-align:center'>
                            <label for="token" class='mb-10'>Shopify Erros</label>
                            <label class="switch m-0">
                                <input type="checkbox" id='shopify_error' name="shopify_error" class='check shopify_error' value='0'>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-2 col-12">
                            <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: 30px">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                </div>
            </form>

            <!-- Aviso de Exportação -->
            <div id="alert-export" class="alert alert-info alert-dismissible fade show card py-10 pl-20 pr-10" style="display:none;">
                <div class="d-flex">
                    <i class="material-icons mr-10">info</i>
                    <div class="w-full">
                        <strong class="font-size-16">Exportando seu relatório</strong>
                        <p class="font-size-14 pr-md-100 mb-0" >Sua exportação será entregue por e-mail para: <strong id="export-email"></strong> e aparecerá nas suas notificações. Pode levar algum tempo, dependendo de quantos registros você estiver exportando.</p>
                    </div>
                    <i class="material-icons pointer" data-dismiss="alert">close</i>
                </div>
            </div>

            <!-- Resumo -->
            <div class="fixhalf"></div>
            @if(!auth()->user()->hasRole('attendance'))
                <div class="card shadow p-20" style='display:block;'>
                    <div class="row justify-content-center">
                        <div class="col-md-4">
                            <h6 class="text-center green-gradient">
                                <i class="material-icons align-middle mr-1 green-gradient"> swap_vert </i> Quantidade de vendas
                            </h6>
                            <h4 id="total-sales" class="number text-center green-gradient"></h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center orange-gradient">
                                <i class="material-icons align-middle mr-1 orange-gradient"> attach_money </i> Comissão
                            </h6>
                            <h4 id="comission" class="number text-center orange-gradient"></h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-center green-gradient">
                                <i class="material-icons align-middle green-gradient mr-1"> trending_up </i> Total </h6>
                            <h4 id="total" class="number text-center green-gradient"></i>
                            </h4>
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
                                <td class="table-title">Projeto</td>
                                <td class="table-title">Descrição</td>
                                <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                <td class="table-title">Forma</td>
                                <td class="table-title">Status</td>
                                <td class="table-title display-sm-none display-m-none">Data</td>
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
                <!-- Modal detalhes da venda-->
                @include('sales::details')
                <!-- End Modal -->
            </div>
            <ul id="pagination-sales" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
        {{--        <div class="content-error text-center" style="display:none">--}}
        {{--            <img src="{!! asset('modules/global/img/emptyvendas.svg') !!}" width="250px">--}}
        {{--            <h1 class="big gray">Poxa! Você ainda não fez nenhuma venda.</h1>--}}
        {{--            <p class="desc gray">Comece agora mesmo a vender produtos de seus projetos! </p>--}}
        {{--            <a href="/projects" class="btn btn-primary gradient">Meus Projetos</a>--}}
        {{--        </div>--}}
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
                    <small>OBS: Taxa de R$ 1,00 por estorno</small>
                </div>
                <div class="modal-footer">
                    <a id="btn-mobile-modal-close" class="col-sm-6 btn btn-primary display-sm-none display-m-none display-lg-none display-xlg-none" style='color:white' role="button" data-dismiss="modal" aria-label="Close">
                        Fechar
                    </a>
                    <button type="button" class="col-sm-6 col-md-3 col-lg-3 btn btn-success btn-confirm-refund-transaction" data-dismiss="modal">
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

    @push('scripts')
        <script src="{{ asset('/modules/sales/js/index.js?v=3') }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush

@endsection

