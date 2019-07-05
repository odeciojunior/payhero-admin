@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <h1 class="page-title">Carrinhos abandonados</h1>
        </div>
        <div class="page-content container">
            <div id="" class="card shadow p-20">
                <div class="row">
                    <div class="col-4">
                        <label for="projeto">Projeto</label>
                        <select name='select_project' id="project" class="form-control select-pad">
                            <option value="">Todos projetos</option>
                            @foreach($projects as $project)
                                <option value="{!! $project['id'] !!}">{!! $project['nome'] !!}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-3">
                        <label for="data_inicial">Data inicial</label>
                        <input name='start_date' id="start_date" class="form-control input-pad" type="date">
                    </div>
                    <div class="col-3">
                        <label for="data_final">Data final</label>
                        <input name='end_date' id="end_date" class="form-control input-pad" type="date">
                    </div>
                    <div class="col-1 mt-30 text-right">
                        <button id="bt_filtro" class="btn btn-primary">
                            <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                        </button>
                    </div>
                </div>
            </div>
            <div class="card shadow" style="min-height: 300px">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td class="table-title">Data</td>
                            <td class="table-title">Projeto</td>
                            <td class="table-title">Cliente</td>
                            <td class="table-title">Email</td>
                            <td class="table-title">Sms</td>
                            <td class="table-title">Status</td>
                            <td class="table-title">Valor</td>
                            <td class="table-title"></td>
                            <td class="table-title">Link</td>
                            <td class="table-title">Detalhes</td>
                        </tr>
                    </thead>
                    <tbody id="table_data">
                    </tbody>
                </table>
                <!-- Modal detalhes da venda-->
                <div class="modal fade example-modal-lg" id="modal_detalhes" aria-hidden="true" aria-labelledby="exampleModalTitle" role="dialog" tabindex="-1">
                    <div class="modal-dialog modal-simple modal-sidebar modal-lg">
                        <div class="modal-content p-20 " style="width: 500px;">
                            <div class="header-modal">
                                <div class="row justify-content-between align-items-center" style="width: 100%;">
                                    <div class="col-lg-2"> &nbsp;</div>
                                    <div class="col-lg-8 text-center"><h4> Detalhes da venda </h4></div>
                                    <div class="col-lg-2 text-right">
                                        <a role="button" data-dismiss="modal">
                                            <i class="material-icons pointer">close</i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="transition-details">
                                    <h3> Transação #1234 </h3>
                                    <p class="sm-text text-muted"> Pagamento via Cartão Visa em 02/07/2019 às 22:32
                                        <br> IP do Cliente: 24.202.302.11 </p>
                                    <div class="status d-inline">
                                        <img style="width: 50px;" src="{!! asset('modules/global/assets/img/cartoes/visa.png') !!}">
                                        <span class="badge badge-success mr-5"> Pago </span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="card shadow pr-20 pl-20 p-10">
                                    <div class="row">
                                        <div class="col-lg-6"><p class="table-title"> Produto </p></div>
                                        <div class="col-lg-2 text-right"><p class="text-muted"> Qtde </p></div>
                                        <div class="col-lg-4 text-right"><p class="text-muted"> Valor </p></div>
                                    </div>
                                    <div class="row align-items-baseline justify-content-between mb-15">
                                        <div class="col-lg-2">
                                            <img src="{!! asset('modules/global/assets/img/produto.png') !!}" width="50px;" style="border-radius:6px;">
                                        </div>
                                        <div class="col-lg-4">
                                            <h4 class="table-title"> Nome Produto </h4>
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <p class="sm-text text-muted"> 1x </p>
                                        </div>
                                        <div class="col-lg-4 text-right">
                                            <p class="sm-text text-muted"> R$360,00 </p>
                                        </div>
                                    </div>
                                    <div class="row" style="border-top: 1px solid #e2e2e2;padding-top: 10px;">
                                        <div class="col-lg-6">
                                            <h4 class="table-title"> Total </h4>
                                        </div>
                                        <div class="col-lg-6 text-right">
                                            <h4 class="table-title"> R$360,00 </h4>
                                        </div>
                                    </div>
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
                                        <span class="table-title gray"> Nome: Amanda Garcia </span>
                                        <br>
                                        <span class="table-title gray "> Telefone: 48 984040924 <img src="{!! asset('modules/global/assets/img/whatsapplogo.png') !!}" width="25px"> </span>
                                        <br>
                                        <span class="table-title gray"> E-mail: amandaga@gmail.com </span>
                                        <br>
                                        <span class="table-title gray"> CPF: 01929229 </span>
                                        <h4> Entrega </h4>
                                        <span class="table-title gray"> Endereço:	Rua Emílio Baldin, 04 </span>
                                        <br>
                                        <span class="table-title gray"> CEP: 9190700 </span>
                                        <br>
                                        <span class="table-title gray"> Cidade: Porto Alegre/RS </span>
                                    </div>
                                    <!-- DETALHES  -->
                                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <h4> Dados Gerais </h4>
                                        <span class="table-title gray"> IP: 20.293.202.92  </span>
                                        <br>
                                        <span class="table-title gray "> Dispositivo: Mobile </span>
                                        <br>
                                        <h4> Conversão </h4>
                                        <span class="table-title gray"> SRC: giovani  </span>
                                        <br>
                                        <span class="table-title gray"> UTM Source: remarketing  </span>
                                        <br>
                                        <span class="table-title gray"> UTM Medium: facebook-click </span>
                                        <br>
                                        <span class="table-title gray"> UTM Campaign: ad091</span>
                                        <br>
                                        <span class="table-title gray"> UTM Term: - </span>
                                        <br>
                                        <span class="table-title gray"> UTM Content: advertorial </span>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <!-- <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                              <h4 id="modal_venda_titulo" class="modal-title" style="width: 100%; text-align:center"></h4>
                            </div>
                            <div id="modal_venda_body" class="modal-body">

                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-danger" data-dismiss="modal">Fechar</button>
                            </div>  -->
                        </div>
                    </div>
                </div>
                <!-- End Modal -->
            </div>
            <div class="row">
                <div class="col-12">
                    <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                        {{-- js carrega... --}}
                    </ul>
                </div>
            </div>
        </div>
        @push('scripts')
            <script src="{!! asset('modules/cartrecovery/js/cartrecovery.js') !!}"></script>
    @endpush

@endsection

