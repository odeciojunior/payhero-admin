@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ mix('modules/global/css/table.min.css') }}">
        <link rel="stylesheet" href='{{ mix('modules/sales/css/index.min.css') }}'>
        <link rel="stylesheet" href="{{ mix('modules/notazz/css/index.min.css') }}">
        <link rel="stylesheet" href="{!! mix('modules/global/css/empty.min.css') !!}">
        <link rel="stylesheet" href="{!! mix('modules/global/css/switch.min.css') !!}">
        <link rel="stylesheet" href="{{ mix('modules/global/css/new-dashboard.min.css') }}">
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
        <div style="" class="page-header container">
            <div class="row align-items-center justify-content-between" style="min-height:50px">
                <div class="col-sm-8 col-12">
                    <h1 class="page-title">
                        <a href='/apps/notazz' class='o-arrow-right-1'></a>
                        Notas fiscais da loja <span id="title_integration"></span>
                    </h1>
                </div>
                <div class="col-sm-4 col-12 text-right">
                    <div class="justify-content-end align-items-center" id="export-excel" style="display:none">
                        <div class="p-2 align-items-center">
                            <span class="o-download-cloud-1 mr-2"></span>
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
                            <select name='sale_status' id="status" class="sirius-select">
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
                                <img style="height: 12px; margin-right: 4px" src=" {{ mix('modules/global/img/svg/check-all.svg') }} ">Aplicar
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
                             <select id="discount_type" class="sirius-select">
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
        <script src="{{ mix('modules/notazz/js/show.min.js') }}"></script>
        <script src="{{ mix('modules/global/js-extra/moment.min.js') }}"></script>
        <script src='{{ mix('modules/global/js/daterangepicker.min.js') }}'></script>
    @endpush

@endsection

