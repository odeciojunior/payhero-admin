@extends("layouts.master")

@section('content')

    @push('css')
        <link rel="stylesheet" href="{{ asset('/modules/sales/css/index.css') }}">
    @endpush

    <!-- Page -->
    <div class="page">
        <div class="page-header container">
            <div class="row align-items-center justify-content-between">
                <div class="col-6">
                    <h1 class="page-title">Vendas</h1>
                </div>
                <div class="col-6 text-right">
                    <div class="d-flex justify-content-end align-items-center">
                        {{--<div class="p-2 align-items-center">
                            <i class="icon wb-calendar icon-results" aria-hidden="true"></i>
                            <span class="text-result"> RESULTADOS DE 15 A 26 DE MAIO DE 2019 </span>
                        </div>--}}
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
                @if($sales_amount > 0)
                    <!-- <a id="filtros" class="text-filtros"><svg xmlns="http://www.w3.org/2000/svg" class="icon-filtro" width="14" height="14" viewBox="0 0 24 24"><path d="M19.479 2l-7.479 12.543v5.924l-1-.6v-5.324l-7.479-12.543h15.958zm3.521-2h-23l9 15.094v5.906l5 3v-8.906l9-15.094z"/></svg>
                      Filtros
                    </a> -->
                    @endif
                </div>
            </div>
        </div>
        <div class="page-content container">
            {{--  <div class="col-lg-6 text-right">
                <a id="filtros" class="text-filtros"><svg xmlns="http://www.w3.org/2000/svg" class="icon-filtro" width="14" height="14" viewBox="0 0 24 24"><path d="M19.479 2l-7.479 12.543v5.924l-1-.6v-5.324l-7.479-12.543h15.958zm3.521-2h-23l9 15.094v5.906l5 3v-8.906l9-15.094z"/></svg>
                  Filtros
                </a>
            </div>
          </div>  --}}
            <div class="fixhalf"></div>
            @if($sales_amount > 0)
                <form id='filter_form' action='{{route('sales.getcsvsales')}}' method='POST'>
                    @csrf
                    <div id="" class="card shadow p-20">
                        <div class="row align-items-baseline">
                            <div class="col-3">
                                <label for="projeto">Projeto</label>
                                <select name='select_project' id="projeto" class="form-control select-pad">
                                    <option value="">Todos projetos</option>
                                    @foreach($projetos as $projeto)
                                        <option value="{!! $projeto['id'] !!}">{!! $projeto['nome'] !!}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="forma">Forma de pagamento</label>
                                <select name='select_payment_method' id="forma" class="form-control select-pad">
                                    <option value="">Boleto e cartão de crédito</option>
                                    <option value="credit card">Cartão de crédito</option>
                                    <option value="boleto">Boleto</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="status">Status</label>
                                <select name='sale_status' id="status" class="form-control select-pad">
                                    <option value="">Todos status</option>
                                    <option value="1">Aprovado</option>
                                    <option value="2">Aguardando pagamento</option>
                                    <option value="4">Estornada</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <label for="comprador">Nome do cliente</label>
                                <input name='client' id="comprador" class="input-pad" placeholder="cliente">
                            </div>
                        </div>
                        <div class="row mt-15">
                            <div class="col-3">
                                <label for="data_inicial">Data inicial</label>
                                <input name='start_date' id="data_inicial" class="form-control input-pad" type="date">
                            </div>
                            <div class="col-3">
                                <label for="data_final">Data final</label>
                                <input name='end_date' id="data_final" class="form-control input-pad" type="date">
                            </div>
                            <div class="col-4">
                                <button id="bt_filtro" class="btn btn-primary" style="margin-top: 30px">
                                    <i class="icon wb-check" aria-hidden="true"></i>Aplicar
                                </button>
                            </div>
                            <div class="col-2">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="fixhalf"></div>

                <div class="card shadow" style="min-height: 300px">
                    <div class="page-invoice-table table-responsive">
                        <table id="tabela_vendas" class="table-vendas table table-striped" style="width:100%;">
                            <thead>
                                <tr>
                                    <td class="table-title">Transação</td>
                                    <td class="table-title">Projeto</td>
                                    <td class="table-title">Descrição</td>
                                    <td class="table-title">Cliente</td>
                                    <td class="table-title">Forma</td>
                                    <td class="table-title">Status</td>
                                    <td class="table-title">Data</td>
                                    <td class="table-title">Pagamento</td>
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

            @else
                @push('css')
                    <link rel="stylesheet" href="{!! asset('modules/global/assets/css/empty.css') !!}">
                @endpush

                <div class="content-error d-flex text-center">
                    <img src="{!! asset('modules/global/assets/img/emptyvendas.svg') !!}" width="250px">
                    <h1 class="big gray">Poxa! Você ainda não fez nenhuma venda.</h1>
                    <p class="desc gray">Comece agora mesmo a vender os produtos do seu projeto! </p>
                    <a href="/projects" class="btn btn-primary gradient">Meus Projetos</a>
                </div>

            @endif
            <ul id="pagination" class="pagination-sm" style="margin-top:10px;position:relative;float:right">
                {{-- js carrega... --}}
            </ul>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('/modules/sales/js/index.js') }}"></script>
    @endpush

@endsection

