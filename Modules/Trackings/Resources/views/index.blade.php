@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{!! asset('modules/global/css/empty.css') !!}">
        <link rel="stylesheet" href="{!! asset('modules/global/css/switch.css') !!}">
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
        <div class="page-content container">
            <!-- Filtro -->
            <div class="fixhalf"></div>
            <div id="" class="card shadow p-20">
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                        <label for="product">Produto</label>
                        <select name='product' id="product" class="form-control select-pad">
                            <option value="">Todos</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                        <label for="comprador">Código de rastreio</label>
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
                    <div class="col-sm-6 col-md-6 col-xl-3 col-12">
                        <button id="bt_filtro" class="btn btn-primary col-sm-12" style="margin-top: 30px">
                            <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                        </button>
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
        </div>
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
                    <div class="row" id="div_discount">
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

    @push('scripts')
        <script src="{{ asset('/modules/trackings/index.js?v=1') }}"></script>
    @endpush

@endsection

