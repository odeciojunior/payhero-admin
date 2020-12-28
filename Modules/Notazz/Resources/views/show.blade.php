@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
        <link rel="stylesheet" href="{{ asset('/modules/notazz/css/index.css') }}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
        <link rel="stylesheet" href="{{ asset('modules/global/css/new-dashboard.css') }}">
        <style>
            .fas {
                color: #9c47fc;
                background: -webkit-linear-gradient(77deg, #e6774c, rgb(249, 34, 120));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                cursor: pointer;
            }
        </style>
    @endpush

    <!-- Page -->
    <div class="page">
        <div style="display: none" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-6">
                    <h1 class="page-title">
                        <a href='/apps/notazz' class='fa fa-arrow-circle-left' style='color:#e7714f'></a>
                        Relatorios Notazz
                    </h1>
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
                            <label for="status">Status</label>
                            <select name='sale_status' id="status" class="form-control select-pad">
                                <option value="">Todos status</option>
                                <option value="1">Pendente</option>
                                <option value="2">Enviado</option>
                                <option value="3">Finalizado</option>
                                <option value="4">Erro</option>
                                <option value="5">Em processamento</option>
                                <option value="6">Maximo de tentativas</option>
                                <option value="7">Cancelado</option>
                                <option value="8">Rejeitado</option>
                            </select>
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="comprador">Nome do cliente</label>
                            <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="comprador">Transação</label>
                            <input name='transaction' id="transaction" class="input-pad" placeholder="transação">
                        </div>
                        <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                            <label for="date_type">Data</label>
                            <input name='date_range' id="date_range" class="select-pad" placeholder="Clique para editar..." readonly>
                        </div>
                    </div>
                    <div class="row mt-15">
                        <div class="offset-sm-6 col-sm-6 offset-md-6 col-md-6 offset-xl-9 col-xl-3 col-12">
                            <button id="bt_filtro" class="btn btn-primary col-sm-12">
                                <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                            </button>
                        </div>
                        <div class="col-2">
                        </div>
                    </div>
                </div>
            </form>
            <!-- Resumo  (PODE SE TORNAR UM RESUMO GERAL DA INTEGRAÇÃO NO FUTURO) -->
        {{--            <div class="fixhalf"></div>
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
                    </div>--}}
            <!-- Tabela -->
            <div class="fixhalf"></div>
            <div class="card shadow " style="min-height: 300px">
                <div class="page-invoice-table table-responsive">
                    <table id="tabela_vendas" class="table-vendas table table-striped unify" style="">
                        <thead>
                            <tr>
                                <td class="table-title display-sm-none display-m-none display-lg-none">Transação</td>
                                <td class="table-title">Descrição</td>
                                <td class="table-title display-sm-none display-m-none display-lg-none">Cliente</td>
                                <td class="table-title">Status</td>
                                <td class="table-title display-sm-none display-m-none">Data</td>
                                <td class="table-title">Valor</td>
                                <td class="table-title" width="80px;"> &nbsp;</td>
                            </tr>
                        </thead>
                        <tbody id="dados_tabela">
                            {{-- js carrega... --}}
                        </tbody>
                    </table>
                </div>
                <!-- Modal detalhes da venda-->
            @include('notazz::details')
            <!-- End Modal -->
            </div>
            <ul id="pagination-invoices" class="pagination-sm margin-chat-pagination" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
    </div>

    <!-- Modal regerar boleto (NAO ULTILIZADO POR ENQUANTO)-->
    {{-- <div class="modal fade example-modal-lg modal-3d-flip-vertical" id="modal_regerar_boleto" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
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
                     <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                 </div>
             </div>
         </div>
     </div>--}}
    <!-- End Modal -->

    @push('scripts')
        <script src="{{ asset('/modules/notazz/js/show.js?v=s1') }}"></script>
        <script src="{{ asset('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ asset('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush

@endsection

