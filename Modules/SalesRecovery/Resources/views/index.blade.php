@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
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
                            <select name='select_project' id="project" class="form-control select-pad">
                                {{--<option value="">Todos projetos</option>--}}
                                {{--  @foreach($projects as $project)
                                      <option value="{{Hashids::encode($project['id'])}}">{{$project['nome']}}</option>
                                  @endforeach--}}
                            </select>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-3">
                            <label for="type_recovery">Tipo de Recuperação</label>
                            <select name='select_type_recovery' id="type_recovery" class="form-control select-pad">
                                <option value="1" selected>Carrinho Abandonado</option>
                                <option value="2">Boleto Vencido</option>
                                <option value="3">Cartão Recusado</option>
                            </select>
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                            <label for="start_date">Data inicial</label>
                            <input name='start_date' id="start_date" timezone='' class="form-control input-pad" type="date">
                        </div>
                        <div class="col-12 col-sm-6 col-md-6 col-lg-2">
                            <label for="end_date">Data final</label>
                            <input name='end_date' id="end_date" class="form-control input-pad" type="date">
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
                                        <p id='date-as-hours' class="sm-text text-muted">
                                        </p>
                                        <div class="status d-inline">
                                            <span class="badge mr-5" id='status-checkout'></span>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="card shadow pr-20 pl-20 p-10">
                                        <div class="row">
                                            <div class="col-lg-3"><p class="table-title"> Produto </p></div>
                                            <div class="col-lg-9 text-right"><p class="text-muted"> Qtde </p></div>
                                        </div>
                                        {{-- Tabela produtos JS insere dados--}}
                                        <div id='table-product'>
                                        </div>
                                        <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                                            <div class="col-lg-6">
                                                <h4 class="table-title"> Total </h4>
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
                                            <span id='client-name' class="table-title gray"> </span>
                                            <br>
                                            <span id='client-telephone' class='table-title gray'></span>
                                            <a id='client-whatsapp' target='_blank'>
                                                <img src="{!! asset('modules/global/img/whatsapplogo.png') !!}" width="25px">
                                            </a>
                                            <br>
                                            <span id='client-email' class="table-title gray"> </span>
                                            <br>
                                            <span id='client-document' class="table-title gray"></span>
                                            <h4> Entrega </h4>
                                            <span id="client-street" class="table-title gray"> </span>
                                            <br>
                                            <span id='client-zip-code' class="table-title gray"> </span>
                                            <br>
                                            <span id='client-city-state' class="table-title gray"></span>
                                        </div>
                                        <!-- DETALHES  -->
                                        <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                            <h4> Dados Gerais </h4>
                                            <span id='sale-motive' class="table-title gray"> </span>
                                            <br>
                                            <span id='link-sale' class="table-title gray"></span>
                                            <br>
                                            <span id='checkout-ip' class="table-title gray">   </span>
                                            <br>
                                            <span id='checkout-is-mobile' class="table-title gray "> </span>
                                            <br>
                                            <span id='checkout-operational-system' class="table-title gray "> </span>
                                            <br>
                                            <span id='checkout-browser' class="table-title gray "> </span>
                                            <br>
                                            <h4> Conversão </h4>
                                            <span id='checkout-src' class="table-title gray"> </span>
                                            <br>
                                            <span id='checkout-utm-source' class="table-title gray"> </span>
                                            <br>
                                            <span id='checkout-utm-medium' class="table-title gray"> </span>
                                            <br>
                                            <span id='checkout-utm-campaign' class="table-title gray"></span>
                                            <br>
                                            <span id='checkout-utm-term' class="table-title gray"> </span>
                                            <br>
                                            <span id='checkout-utm-content' class="table-title gray"> </span>
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
        <script src="{{ asset('modules/salesrecovery/js/salesrecovery.js') }}"></script>

    @endpush

@endsection

